<?php

namespace Classes;

/**
 * This is Basixs BaseTable Extension
 * this is like table models ORM
 * @Author: Tyrone Malocon
 */
class BaseTable
{
    protected $pdo;
    protected $table;
    protected $fillable = [];
    protected $guarded = [];
    protected $timestamps = true;
    protected $hidden = [];
    protected $rowcount;
    protected $lastQuery;
    protected $lastBindings;
    protected $totalRecords;
    protected $totalPages;
    protected $currentPage;

    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->pdo = pdo();
        $this->attributes = $attributes;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }


    protected function filterFillable(array $data): array
    {
        if (!empty($this->fillable)) {
            return array_filter(
                $data,
                fn($key) => in_array($key, $this->fillable),
                ARRAY_FILTER_USE_KEY
            );
        }

        if (!empty($this->guarded)) {
            return array_filter(
                $data,
                fn($key) => !in_array($key, $this->guarded),
                ARRAY_FILTER_USE_KEY
            );
        }

        return $data;
    }


    protected function hydrate(array $row): array
    {
        return array_diff_key($row, array_flip($this->hidden));
    }


    public static function filterHidden(array $data, array $hiddenKeys = []): array
    {
        if (empty($hiddenKeys)) {
            return $data;
        }
        return array_diff_key($data, array_flip($hiddenKeys));
    }


    protected static function instance(array $attributes = [])
    {
        return new static($attributes);
    }


    public static function all(array $options = [])
    {
        $self = static::instance();

        $sql = "SELECT * FROM {$self->table}";
        $bindings = [];

        if (isset($options['where']) && is_array($options['where'])) {
            $whereParts = [];
            foreach ($options['where'] as $col => $val) {
                $whereParts[] = "$col = ?";
                $bindings[] = $val;
            }
            if ($whereParts) {
                $sql .= " WHERE " . implode(" AND ", $whereParts);
            }
        }

        if (isset($options['group by'])) {
            $sql .= " GROUP BY " . $options['group by'];
        }

        if (isset($options['order by'])) {
            $sql .= " ORDER BY " . $options['order by'];
        }

        if (isset($options['limit'])) {
            $sql .= " LIMIT " . (int)$options['limit'];
        }
        if (isset($options['offset'])) {
            $sql .= " OFFSET " . (int)$options['offset'];
        }

        $self->lastQuery = $sql;
        $self->lastBindings = $bindings;

        $stmt = $self->pdo->prepare($sql);
        $stmt->execute($bindings);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $self->rowcount = $stmt->rowCount();

        return array_map([$self, 'hydrate'], $rows);
    }



    public static function findOne(array $where)
    {
        $self = static::instance();
        $conditions = $where;
        $data = $self->find($conditions);
        if ($data) {
            if (! $data[0]) {
                return [];
            }
            return $data[0];
        }
        return [];
    }

    public static function get(array $where, int|array $extra = null): array
    {
        $self = static::instance();

        $data = $self->find($where, $extra);
        if (empty($data)) {
            return [];
        }
        return $data;
    }

    public static function getAll(array|null $where = null, int|array $extra = null)
    {
        $extra = is_null($extra) ? [] : $extra;
        if (is_null($where) || empty($where)) {
            $all =  self::select(null, $extra);
            return is_null($all) ? [] : $all;
        }
        return self::get($where, $extra);
    }


    public static function find(array $where, int|array $extra = null)
    {
        $conditions = $where;
        $self = static::instance();

        if (!is_array($conditions)) {
            throw new \InvalidArgumentException("Where conditions must be an associative array.");
        }

        $select = "*";
        if (is_array($extra) && isset($extra['select'])) {
            $select = $extra['select'];
        }

        $whereClause = implode(' AND ', array_map(fn($col) => "`$col` = :$col", array_keys($conditions)));
        $sql = "SELECT {$select} FROM {$self->table} WHERE $whereClause";

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

            if (isset($extra['group by'])) {
                $sql .= " GROUP BY " . $extra['group by'];
            }

            if (isset($extra['having'])) {
                $sql .= " HAVING " . $extra['having'];
            }

            if (isset($extra['order by'])) {
                $sql .= " ORDER BY " . $extra['order by'];
            }
        }

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }

        $self->lastQuery = $sql;
        $self->lastBindings = $conditions;

        $stmt = $self->pdo->prepare($sql);

        foreach ($conditions as $col => $val) {
            $stmt->bindValue(":$col", $val);
        }

        if ($limit !== null) {
            $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rc = $stmt->rowCount();
        $self->rowcount = $rc;

        $countSql = "SELECT COUNT(*) as cnt FROM {$self->table} WHERE $whereClause";
        $countStmt = $self->pdo->prepare($countSql);
        $countStmt->execute($conditions);
        $total = (int)$countStmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $self->totalRecords = $total;
        $self->totalPages = ($limit !== null && $limit > 0) ? (int)ceil($total / $limit) : 1;
        $self->currentPage = $page;

        if ($rc == 0) {
            return [];
        }

        return array_map([$self, 'hydrate'], $rows);
    }


    public function totalRows(): int
    {
        return $this->totalRecords ?? 0;
    }


    public static function select(string|array|null $columns = null, array $extra = [])
    {
        $self = static::instance();

        if ($columns === null || $columns === [] || $columns === '') {
            $cols = '*';
        } elseif (is_array($columns)) {
            $cols = implode(',', array_map(fn($c) => "`" . trim($c, '`') . "`", $columns));
        } else {
            $cols = "`" . trim($columns, '`') . "`";
        }

        $sql = "SELECT $cols FROM {$self->table}";

        if (isset($extra['group by'])) {
            $sql .= " GROUP BY " . $extra['group by'];
        }

        if (isset($extra['having'])) {
            $sql .= " HAVING " . $extra['having'];
        }

        if (isset($extra['order by'])) {
            $sql .= " ORDER BY " . $extra['order by'];
        }

        if (isset($extra['limit'])) {
            $sql .= " LIMIT " . (int)$extra['limit'];
        }

        if (isset($extra['offset'])) {
            $sql .= " OFFSET " . (int)$extra['offset'];
        }

        $self->lastQuery    = $sql;
        $self->lastBindings = [];

        $stmt = $self->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $self->rowcount = $stmt->rowCount();

        if ($self->rowcount === 0) {
            return [];
        }

        return array_map([$self, 'hydrate'], $rows);
    }



    public function totalPages(): int
    {
        return $this->totalPages ?? 1;
    }

    public function currentPage(): int
    {
        return $this->currentPage ?? 1;
    }




    public static function first(array $conditions = [])
    {
        $self = static::instance();

        if (!is_array($conditions)) {
            throw new \InvalidArgumentException("First conditions must be an associative array.");
        }

        if (empty($conditions)) {
            $sql = "SELECT * FROM {$self->table} LIMIT 1";
            $self->lastQuery = $sql;
            $self->lastBindings = [];

            $stmt = $self->pdo->prepare($sql);
            $stmt->execute();
        } else {
            $whereClause = implode(' AND ', array_map(fn($col) => "`$col` = :$col", array_keys($conditions)));
            $sql = "SELECT * FROM {$self->table} WHERE $whereClause LIMIT 1";
            $self->lastQuery = $sql;
            $self->lastBindings = $conditions;

            $stmt = $self->pdo->prepare($sql);
            $stmt->execute($conditions);
        }

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $self->rowcount = $stmt->rowCount();

        return $row ? $self->hydrate($row) : [];
    }


    public static function last(array $conditions = [])
    {
        $self = static::instance();

        if (!is_array($conditions)) {
            throw new \InvalidArgumentException("Last conditions must be an associative array.");
        }

        if (empty($conditions)) {
            $sql = "SELECT * FROM {$self->table} ORDER BY id DESC LIMIT 1";
            $self->lastQuery = $sql;
            $self->lastBindings = [];

            $stmt = $self->pdo->prepare($sql);
            $stmt->execute();
        } else {
            $whereClause = implode(' AND ', array_map(fn($col) => "`$col` = :$col", array_keys($conditions)));
            $sql = "SELECT * FROM {$self->table} WHERE $whereClause ORDER BY id DESC LIMIT 1";
            $self->lastQuery = $sql;
            $self->lastBindings = $conditions;

            $stmt = $self->pdo->prepare($sql);
            $stmt->execute($conditions);
        }

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $self->rowcount = $stmt->rowCount();

        return $row ? $self->hydrate($row) : [];
    }

    public static function insert(array $data)
    {
        return self::create($data);
    }


    public static function create(array $data)
    {
        $self = static::instance();

        $data = $self->filterFillable($data);

        if ($self->timestamps) {
            $now = date('Y-m-d H:i:s');
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
        }

        $columns = array_map(fn($col) => "`$col`", array_keys($data));
        $placeholders = array_map(fn($col) => ":$col", array_keys($data));

        $sql = "INSERT INTO {$self->table} (" . implode(",", $columns) . ") VALUES (" . implode(",", $placeholders) . ")";
        $self->lastQuery = $sql;
        $self->lastBindings = $data;

        $stmt = $self->pdo->prepare($sql);
        $stmt->execute($data);
        $self->rowcount = 1;

        $data['_id'] = $self->pdo->lastInsertId();
        $insertedRow = $data;

        return $insertedRow ? static::instance($insertedRow) : [];
    }

    public static function update(array $where, array $data)
    {
        $self = static::instance();

        $data = $self->filterFillable($data);

        if ($self->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $setClause = implode(', ', array_map(fn($col) => "`$col` = :$col", array_keys($data)));
        $whereClause = implode(' AND ', array_map(fn($col) => "`$col` = :where_$col", array_keys($where)));

        $bindings = array_merge(
            $data,
            array_combine(
                array_map(fn($k) => "where_$k", array_keys($where)),
                array_values($where)
            )
        );

        $sql = "UPDATE {$self->table} SET $setClause WHERE $whereClause";
        $self->lastQuery = $sql;
        $self->lastBindings = $bindings;

        $stmt = $self->pdo->prepare($sql);
        $return = $stmt->execute($bindings);
        $rwCount = $stmt->rowCount();
        $self->rowcount = $rwCount;

        return $rwCount;
    }


    public static function delete(array $where)
    {
        $self = static::instance();

        $whereClause = implode(' AND ', array_map(fn($col) => "`$col` = :$col", array_keys($where)));
        $sql = "DELETE FROM {$self->table} WHERE $whereClause";
        $self->lastQuery = $sql;
        $self->lastBindings = $where;

        $stmt = $self->pdo->prepare($sql);
        $return  = $stmt->execute($where);
        $rwCount = $stmt->rowCount();
        $self->rowcount = $rwCount;

        return $rwCount;
    }

    public static function toFilteredArray(array $data)
    {
        $self = static::instance();
        return self::filterHidden($data, $self->hidden);
    }


    public static function jsonEncode(array $data)
    {
        return json_encode(static::toFilteredArray($data));
    }


    public static function getLastQuery(bool $withBindings = false)
    {
        $self = static::instance();

        if (!$withBindings) {
            return $self->lastQuery;
        }

        $query = $self->lastQuery;
        foreach ($self->lastBindings as $key => $value) {
            $pattern = '/:' . preg_quote($key, '/') . '\b/';
            $replacement = is_numeric($value) ? $value : $self->pdo->quote($value);
            $query = preg_replace($pattern, $replacement, $query);
        }
        return $query;
    }


    public static function rowCount()
    {
        $self = static::instance();
        return $self->rowcount ?? 0;
    }


    public function toArray()
    {
        $data = $this->attributes;
        unset($data['_id']);
        return array_diff_key($data, array_flip($this->hidden));
    }

    public function insertID()
    {
        $data = $this->attributes;
        return $data['_id'] ?? null;
    }

    public function _id()
    {
        return $this->insertID();
    }

    public function data(string|array|null $key = null)
    {
        $attributes = $this->toArray();

        if ($key === null) {
            return $attributes;
        }

        if (is_string($key)) {
            return $attributes[$key] ?? null;
        }

        if (is_array($key)) {
            return array_intersect_key($attributes, array_flip($key));
        }

        return [];
    }

    public function excepts(string|array $key = null)
    {
        $attributes = $this->toArray();

        if ($key === null) {
            return $attributes;
        }

        if (is_string($key)) {
            unset($attributes[$key]);
            return $attributes;
        }

        if (is_array($key)) {
            return array_diff_key($attributes, array_flip($key));
        }

        return $attributes;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
