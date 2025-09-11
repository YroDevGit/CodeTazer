<?php

namespace Classes;

class DB
{
    private static $lastQuery;
    private static $lastBindings;
    private static $lastRowCount;
    private static $lastData;
    private static $lastTable;

    private static $pdo;
    private static $rowcount;
    private static $totalRecords;
    private static $totalPages;
    private static $currentPage;

    private static $allowedColumns = null;
    private static $hiddenColumns = null;

    private static $newdb = false;

    public function __construct($database = null)
    {
        if ($database) {
            self::$pdo = pdo($database); // custom connection
            self::$newdb = true;
        }
    }

    private static function conn(): \PDO
    {
        if (self::$newdb && self::$pdo instanceof \PDO) {
            return self::$pdo;
        }
        return pdo(); // fallback to default global PDO
    }

    public static function interface(array $columns)
    {
        self::$allowedColumns = $columns;
        return new static;
    }

    public static function hide(array $columns)
    {
        self::$hiddenColumns = $columns;
        return new static;
    }

    public static function findOne(string $table, array $where)
    {
        $data = self::find($table, $where);
        return $data[0] ?? [];
    }

    public static function get(string $table, array $where, array|int|null $extra = null): array
    {
        $data = self::find($table, $where, $extra);
        return $data ?: [];
    }

    public static function getAll(string $table, array|null $where = null, array|int|null $extra = null)
    {
        $extra = $extra ?? [];
        if (empty($where)) {
            $all = self::select($table, null, $extra);
            return $all ?: [];
        }
        return self::get($table, $where, $extra);
    }

    public static function find(string $table, array $where, array|int|null $extra = null): array
    {
        if (!is_array($where)) {
            throw new \InvalidArgumentException("Where conditions must be an associative array.");
        }

        $select = "*";
        if (is_array($extra) && isset($extra['select'])) {
            $select = $extra['select'];
        }

        $whereClause = implode(' AND ', array_map(fn($col) => "`$col` = :$col", array_keys($where)));
        $sql = "SELECT {$select} FROM `{$table}` WHERE $whereClause";

        $limit = null;
        $offset = null;
        $page = 1;

        if (is_numeric($extra)) {
            $limit = (int)$extra;
        } elseif (is_array($extra)) {
            if (isset($extra['limit'])) {
                $limit = (int)$extra['limit'];
                if (isset($extra['page'])) {
                    $page = max(1, (int)$extra['page']);
                    $offset = ($page - 1) * $limit;
                }
            }
            if (isset($extra['group by'])) $sql .= " GROUP BY " . $extra['group by'];
            if (isset($extra['having'])) $sql .= " HAVING " . $extra['having'];
            if (isset($extra['order by'])) $sql .= " ORDER BY " . $extra['order by'];
        }

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) $sql .= " OFFSET :offset";
        }

        self::$lastQuery = $sql;
        self::$lastBindings = $where;

        $stmt = self::conn()->prepare($sql);

        foreach ($where as $col => $val) {
            $stmt->bindValue(":$col", $val);
        }
        if ($limit !== null) {
            $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
            if ($offset !== null) $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rc = $stmt->rowCount();
        self::$rowcount = $rc;
        $stmt->closeCursor();

        $countStmt = self::conn()->prepare("SELECT COUNT(*) as cnt FROM `{$table}` WHERE $whereClause");
        $countStmt->execute($where);
        $total = (int)$countStmt->fetch(\PDO::FETCH_ASSOC)['cnt'];
        $countStmt->closeCursor();

        self::$totalRecords = $total;
        self::$totalPages = ($limit !== null && $limit > 0) ? (int)ceil($total / $limit) : 1;
        self::$currentPage = $page;

        return $rc > 0 ? $rows : [];
    }

    private static function resetColumnFilters()
    {
        self::$allowedColumns = null;
        self::$hiddenColumns = null;
    }

    private static function filterInsertData(array $data)
    {
        if (self::$allowedColumns !== null) {
            $data = array_intersect_key($data, array_flip(self::$allowedColumns));
        }
        return $data;
    }

    private static function filterResultArray(array $results)
    {
        if (self::$hiddenColumns !== null) {
            foreach ($results as &$row) {
                foreach (self::$hiddenColumns as $col) {
                    unset($row[$col]);
                }
            }
        }
        return $results;
    }

    public static function insert(string $table, array $data)
    {
        $data = self::filterInsertData($data);
        $columns = implode(", ", array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO `{$table}` ($columns) VALUES ($placeholders)";

        $pdo = self::conn();
        $stmt = $pdo->prepare($sql);

        self::$lastQuery = $sql;
        self::$lastBindings = array_values($data);
        self::$lastRowCount = 1;
        self::$lastData = $data;
        self::$lastTable = $table;

        $stmt->execute(self::$lastBindings);
        $id = $pdo->lastInsertId();
        $stmt->closeCursor();

        self::resetColumnFilters();
        return $id ?: null;
    }

    public static function delete(string $table, array $where)
    {
        $whereClause = implode(" AND ", array_map(fn($col) => "`$col` = ?", array_keys($where)));
        $sql = "DELETE FROM `{$table}` WHERE $whereClause";

        $pdo = self::conn();
        $stmt = $pdo->prepare($sql);

        self::$lastQuery = $sql;
        self::$lastBindings = array_values($where);
        self::$lastData = $where;
        self::$lastTable = $table;

        $stmt->execute(self::$lastBindings);
        $rowCount = $stmt->rowCount() ?? null;
        self::$lastRowCount = $rowCount;
        $stmt->closeCursor();

        self::resetColumnFilters();
        return $rowCount;
    }

    public static function update(string $table, array $data, array $where)
    {
        $data = self::filterInsertData($data);
        $setClause = implode(", ", array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($col) => "`$col` = ?", array_keys($where)));
        $sql = "UPDATE `{$table}` SET $setClause WHERE $whereClause";
        $params = array_merge(array_values($data), array_values($where));

        $pdo = self::conn();
        $stmt = $pdo->prepare($sql);

        self::$lastQuery = $sql;
        self::$lastBindings = $params;
        self::$lastData = ["data" => $data, "where" => $where];
        self::$lastTable = $table;

        $stmt->execute($params);
        $rowCount = $stmt->rowCount();
        self::$lastRowCount = $rowCount;
        $stmt->closeCursor();

        self::resetColumnFilters();
        return $rowCount;
    }

    public static function query(string $sql, array $params = [])
    {
        $pdo  = self::conn();
        $stmt = $pdo->prepare($sql);
        self::$lastQuery = $sql;
        self::$lastBindings = $params;
        self::$lastData = null;
        self::$lastTable = null;

        foreach ($params as $key => $value) {
            if (is_array($value)) throw new \InvalidArgumentException("Parameter cannot be an array");
            $placeholder = is_int($key) ? $key + 1 : $key;
            $stmt->bindValue($placeholder, $value);
        }

        $stmt->execute();
        $verb = strtoupper(strtok(ltrim($sql), " \n\t("));
        $rett = null;

        switch ($verb) {
            case 'SELECT':
            case 'SHOW':
            case 'DESCRIBE':
            case 'PRAGMA':
                self::$lastRowCount = $stmt->rowCount();
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $rett = self::filterResultArray($results);
                break;
            case 'INSERT':
                self::$lastRowCount = 1;
                $rett = $pdo->lastInsertId();
                break;
            case 'UPDATE':
            case 'DELETE':
            default:
                $rett = $stmt->rowCount();
                self::$lastRowCount = $rett;
        }

        $stmt->closeCursor();
        self::resetColumnFilters();
        return $rett;
    }

    public static function select(string $table, string|array|null $columns = null, array $extra = []): array
    {
        if ($columns === null || $columns === [] || $columns === '') $cols = '*';
        elseif (is_array($columns)) $cols = implode(',', array_map(fn($c) => "`" . trim($c, '`') . "`", $columns));
        else $cols = "`" . trim($columns, '`') . "`";

        $sql = "SELECT $cols FROM `{$table}`";
        if (isset($extra['group by'])) $sql .= " GROUP BY " . $extra['group by'];
        if (isset($extra['having'])) $sql .= " HAVING " . $extra['having'];
        if (isset($extra['order by'])) $sql .= " ORDER BY " . $extra['order by'];
        if (isset($extra['limit'])) $sql .= " LIMIT " . (int)$extra['limit'];
        if (isset($extra['offset'])) $sql .= " OFFSET " . (int)$extra['offset'];

        self::$lastQuery = $sql;
        self::$lastBindings = [];

        $stmt = self::conn()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        self::$rowcount = $stmt->rowCount();
        $stmt->closeCursor();

        return self::$rowcount > 0 ? $rows : [];
    }

    public static function getLastQuery($withBindings = true)
    {
        if (!self::$lastQuery) return null;
        if (!$withBindings) return self::$lastQuery;

        $query = self::$lastQuery;
        $bindings = self::$lastBindings;

        foreach ($bindings as $key => $value) {
            $quoted = is_numeric($value) ? $value : self::conn()->quote($value);
            if (is_int($key)) $query = preg_replace('/\?/', $quoted, $query, 1);
            else $query = str_replace(":$key", $quoted, $query);
        }
        return $query;
    }

    public static function first(string $table, array $where, array $columns = ["*"])
    {
        $results = self::select($table, $where, $columns);
        return $results[0] ?? null;
    }

    public static function rowCount(): int
    {
        return self::$lastRowCount ?? 0;
    }

    public static function lastTable()
    {
        return self::$lastTable ?? null;
    }

    public static function lastData()
    {
        return self::$lastData ?? null;
    }
}
