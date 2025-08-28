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
        if (is_string($keys)) {
            $keys = [$keys];
        }

        $result = array_map(function ($item) use ($keys) {
            return array_diff_key($item, array_flip($keys));
        }, $this->items);

        return $this->singleRow ? $result[0] : $result;
    }

    public function concat(array $definitions, string $separator = " ", bool $preserveColumns = false): array
    {
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
        if ($size <= 0) {
            return $this->singleRow ? [] : [];
        }

        $items = $reverse
            ? array_slice($this->items, -$size, $size)
            : array_slice($this->items, 0, $size);

        return $this->singleRow ? ($items[0] ?? []) : $items;
    }

    public function all(): array
    {
        return $this->singleRow ? $this->items[0] : $this->items;
    }
}
