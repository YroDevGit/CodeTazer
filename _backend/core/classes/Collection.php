<?php

namespace Classes;

class Collection
{
    protected array $items = [];
    protected bool $singleRow = false;

    private function __construct(array $items)
    {
        if (self::isAssoc($items)) {
            $this->singleRow = true;
            $this->items = [$items];
        } else {
            $this->items = $items;
        }
    }

    //Tyrone Malocon
    public static function data(array|null $items): self
    {
        $items = is_null($items) ? [] : $items;
        return new self($items);
    }

    private static function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function get(string|array $keys)
    {
        return $this->pick($keys);
    }

    public function pick(string|array $keys): array
    {
        if ($this->trulyEmpty($this->items)) {
            return [];
        }
        if (is_string($keys)) {
            $keys = [$keys];
        }

        $result = array_map(function ($item) use ($keys) {
            $filtered = [];
            foreach ($keys as $key) {
                if (array_key_exists($key, $item)) {
                    $filtered[$key] = $item[$key];
                }
            }
            return $filtered;
        }, $this->items);

        return $this->singleRow ? $result[0] : $result;
    }

    public function except(string|array $keys): array
    {
        if ($this->trulyEmpty($this->items)) {
            return [];
        }
        if (is_string($keys)) {
            $keys = [$keys];
        }

        $result = array_map(function ($item) use ($keys) {
            return array_diff_key($item, array_flip($keys));
        }, $this->items);

        return $this->singleRow ? $result[0] : $result;
    }


    public function take(array $conditions, string $option = "equal"): self
    {
        if ($this->trulyEmpty($this->items) || empty($conditions)) {
            return $this;
        }

        $this->items = array_filter($this->items, function ($item) use ($conditions, $option) {
            foreach ($conditions as $col => $val) {
                $itemVal = $item[$col] ?? '';

                if ($option === "equal" && (string)$itemVal !== (string)$val) {
                    return false;
                }

                if ($option === "like" && stripos((string)$itemVal, (string)$val) === false) {
                    return false;
                }
            }
            return true;
        });


        $this->items = array_values($this->items);

        $this->singleRow = count($this->items) === 1;

        return $this;
    }



    public function skip(array $conditions, string $option = "equal"): self
    {
        if ($this->trulyEmpty($this->items) || empty($conditions)) {
            return $this;
        }

        $this->items = array_filter($this->items, function ($item) use ($conditions, $option) {
            foreach ($conditions as $col => $val) {
                $itemVal = $item[$col] ?? '';

                if ($option === "equal" && (string)$itemVal === (string)$val) {
                    return false;
                }

                if ($option === "like" && stripos((string)$itemVal, (string)$val) !== false) {
                    return false;
                }
            }
            return true;
        });

        $this->items = array_values($this->items);

        $this->singleRow = count($this->items) === 1;

        return $this;
    }


    public function concat(array $definitions, string $separator = " ", bool $preserveColumns = false): array
    {
        if ($this->trulyEmpty($this->items)) {
            return [];
        }
        $result = array_map(function ($item) use ($definitions, $separator, $preserveColumns) {
            $newItem = $preserveColumns ? $item : $item;

            foreach ($definitions as $alias => $cols) {
                $values = [];
                foreach ($cols as $col) {
                    $values[] = $item[$col] ?? '';
                }
                $newItem[$alias] = trim(
                    implode($separator, array_filter($values, fn($v) => $v !== ''))
                );

                if (!$preserveColumns) {
                    foreach ($cols as $col) {
                        unset($newItem[$col]);
                    }
                }
            }

            return $newItem;
        }, $this->items);

        return $this->singleRow ? $result[0] : $result;
    }


    public function like(...$conditions): array
    {
        if ($this->trulyEmpty($this->items)) {
            return [];
        }
        $result = array_filter($this->items, function ($item) use ($conditions) {
            foreach ($conditions as $cond) {
                if (is_string($cond)) {
                    continue;
                }
                if (self::isAssoc($cond)) {
                    foreach ($cond as $col => $val) {
                        $vals = is_array($val) ? $val : [$val];
                        foreach ($vals as $v) {
                            if (stripos((string)($item[$col] ?? ''), (string)$v) === false) {
                                return false;
                            }
                        }
                    }
                } elseif (is_array($cond)) {
                    $orPass = false;
                    foreach ($cond as $c) {
                        foreach ($c as $col => $val) {
                            if (stripos((string)($item[$col] ?? ''), (string)$val) !== false) {
                                $orPass = true;
                                break;
                            }
                        }
                    }
                    if (!$orPass) return false;
                }
            }
            return true;
        });

        return $this->singleRow ? reset($result) ?: [] : array_values($result);
    }

    public function equal(...$conditions): array
    {
        if ($this->trulyEmpty($this->items)) {
            return [];
        }
        $result = array_filter($this->items, function ($item) use ($conditions) {
            foreach ($conditions as $cond) {
                if (is_string($cond)) {
                    continue;
                }
                if (self::isAssoc($cond)) {
                    foreach ($cond as $col => $val) {
                        $vals = is_array($val) ? $val : [$val];
                        foreach ($vals as $v) {
                            if ((string)($item[$col] ?? '') !== (string)$v) {
                                return false;
                            }
                        }
                    }
                } elseif (is_array($cond)) {
                    $orPass = false;
                    foreach ($cond as $c) {
                        foreach ($c as $col => $val) {
                            if ((string)($item[$col] ?? '') === (string)$val) {
                                $orPass = true;
                                break;
                            }
                        }
                    }
                    if (!$orPass) return false;
                }
            }
            return true;
        });

        return $this->singleRow ? reset($result) ?: [] : array_values($result);
    }

    public function limit(int $size, bool $reverse = false): array
    {
        if ($this->trulyEmpty($this->items)) {
            return [];
        }
        if ($size <= 0) {
            return $this->singleRow ? [] : [];
        }

        $items = $reverse
            ? array_slice($this->items, -$size, $size)
            : array_slice($this->items, 0, $size);

        return $this->singleRow ? ($items[0] ?? []) : $items;
    }


    public function sort(string|array $columns = "firstname"): self
    {
        if ($this->trulyEmpty($this->items)) {
            return $this;
        }

        $sortColumns = [];
        if (is_string($columns)) {
            $sortColumns[$columns] = "asc";
        } elseif (is_array($columns)) {
            foreach ($columns as $col => $dir) {
                if (is_int($col)) {
                    // numeric keys, value is column name, default asc
                    $sortColumns[$dir] = "asc";
                } else {
                    $sortColumns[$col] = strtolower($dir) === "desc" ? "desc" : "asc";
                }
            }
        }

        usort($this->items, function ($a, $b) use ($sortColumns) {
            foreach ($sortColumns as $col => $dir) {
                $valA = $a[$col] ?? null;
                $valB = $b[$col] ?? null;

                if (is_numeric($valA) && is_numeric($valB)) {
                    $cmp = $valA <=> $valB;
                } else {
                    $cmp = strcasecmp((string)$valA, (string)$valB);
                }

                if ($cmp !== 0) {
                    return $dir === "desc" ? -$cmp : $cmp;
                }
            }
            return 0;
        });

        return $this;
    }


    public function all(): array
    {
        if ($this->trulyEmpty($this->items)) {
            return [];
        }
        return $this->singleRow ? $this->items[0] : $this->items;
    }

    private static function trulyEmpty(array $arr): bool
    {
        if (empty($arr)) {
            return true;
        }
        if (count($arr) === 1 && is_array($arr[0]) && empty($arr[0])) {
            return true;
        }
        return false;
    }
}
