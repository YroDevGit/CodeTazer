<?php

namespace Classes;

class Validator
{
    /**
     * Tyrone Validator
     * inspired by Express validator
     */
    private static $errors = [];
    private static $failed = false;
    private static $ers = [];

    private string|null $field;
    private string|null $label = '';
    private array $rules = [];

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public static function input(string $field): self
    {
        return new self($field);
    }

    public static function body(string $field): self
    {
        return new self($field);
    }

    public static function post(string $field): self
    {
        return new self($field);
    }

    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function required(): self
    {
        $this->rules[] = 'required';
        return $this;
    }

    public function email(): self
    {
        $this->rules[] = 'email';
        return $this;
    }

    public function number(): self
    {
        $this->rules[] = 'number';
        return $this;
    }

    public function string(): self
    {
        $this->rules[] = 'string';
        return $this;
    }

    public function equal(string $val): self
    {
        $this->rules[] = "equal:$val";
        return $this;
    }

    public function max(int|float $val): self
    {
        $this->rules[] = "max:$val";
        return $this;
    }

    public function min(int|float $val): self
    {
        $this->rules[] = "min:$val";
        return $this;
    }

    public function minChars(int $val): self
    {
        $this->rules[] = "min_chars:$val";
        return $this;
    }

    public function maxChars(int $val): self
    {
        $this->rules[] = "max_chars:$val";
        return $this;
    }

    public function contain(mixed $val): self
    {
        $this->rules[] = "contain:$val";
        return $this;
    }

    public function exclude(mixed $val): self
    {
        $this->rules[] = "exclude:$val";
        return $this;
    }

    public function regex(string $pattern): self
    {
        $this->rules[] = "regex:$pattern";
        return $this;
    }

    public function in(array $options): self
    {
        $this->rules[] = "in:" . implode(',', $options);
        return $this;
    }

    public function notIn(array $options): self
    {
        $this->rules[] = "not_in:" . implode(',', $options);
        return $this;
    }

    public function length(int $len): self
    {
        $this->rules[] = "length:$len";
        return $this;
    }

    public function startsWith(string $val): self
    {
        $this->rules[] = "starts_with:$val";
        return $this;
    }

    public function endsWith(string $val): self
    {
        $this->rules[] = "ends_with:$val";
        return $this;
    }

    public function trim(): self
    {
        $this->rules[] = "trim";
        return $this;
    }

    public function alpha(): self
    {
        $this->rules[] = "alpha";
        return $this;
    }

    public function alphanumeric(): self
    {
        $this->rules[] = "alphanumeric";
        return $this;
    }

    public function date(): self
    {
        $this->rules[] = "date";
        return $this;
    }

    public function url(): self
    {
        $this->rules[] = "url";
        return $this;
    }

    public function ip(): self
    {
        $this->rules[] = "ip";
        return $this;
    }

    public function boolean(): self
    {
        $this->rules[] = "boolean";
        return $this;
    }

    public function validate(): mixed
    {
        $rulesString = implode('|', $this->rules);
        $label = $this->label ?: $this->field;
        $ret = self::check($this->field, $label, $rulesString);
        $this->label = null;
        $this->field = null;
        $this->rules = [];
        return $ret;
    }

    public function X()
    {
        return $this->validate();
    }

    public function exec()
    {
        return $this->validate();
    }

    public static function check($postname, $label, $rules)
    {
        $postdata = postdata();
        if (!isset($postdata[$postname])) {
            $postdata[$postname] = null;
        }

        $rulesArray = explode('|', $rules);
        $hasRequired = in_array('required', $rulesArray);
        $rulesArray = array_reverse($rulesArray);
        $value = $postdata[$postname] ?? "";
        $org = $postdata[$postname] ?? null;

        if (in_array("trim", $rulesArray)) {
            $value = trim($org ?? "");
        }

        foreach ($rulesArray as $rule) {
            $ruleParts = explode(':', $rule, 2);
            $ruleName = $ruleParts[0];
            $ruleParam = $ruleParts[1] ?? null;

            if ($value === '' && $ruleName !== 'required' && !$hasRequired) {
                continue;
            }

            if ($ruleName === 'required' && $value === '') {
                self::addError($postname, "$label is required.");
                self::addErrs($postname, "required.");
            }

            if ($ruleName === 'min') {
                if (is_numeric($value)) {
                    if ($value < (float)$ruleParam) {
                        self::addError($postname, "$label must be at least $ruleParam.");
                        self::addErrs($postname, "must be at least $ruleParam.");
                    }
                } else {
                    if (strlen($value) < (int)$ruleParam) {
                        self::addError($postname, "$label must be at least $ruleParam characters.");
                        self::addErrs($postname, "must be at least $ruleParam characters.");
                    }
                }
            }

            if ($ruleName === 'max') {
                if (is_numeric($value)) {
                    if ($value > (float)$ruleParam) {
                        self::addError($postname, "$label must not exceed $ruleParam.");
                        self::addErrs($postname, "must not exceed $ruleParam.");
                    }
                } else {
                    if (strlen($value) > (int)$ruleParam) {
                        self::addError($postname, "$label must not exceed $ruleParam characters.");
                        self::addErrs($postname, "must not exceed $ruleParam characters.");
                    }
                }
            }

            if ($ruleName === 'min_chars') {
                if (strlen((string)$value) < (int)$ruleParam) {
                    self::addError($postname, "$label must be at least $ruleParam characters.");
                    self::addErrs($postname, "must be at least $ruleParam characters.");
                }
            }

            if ($ruleName === 'max_chars') {
                if (strlen((string)$value) > (int)$ruleParam) {
                    self::addError($postname, "$label must not exceed $ruleParam characters.");
                    self::addErrs($postname, "must not exceed $ruleParam characters.");
                }
            }

            if ($ruleName === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                self::addError($postname, "$label must be a valid email address.");
                self::addErrs($postname, "invalid email address.");
            }

            if (($ruleName === 'string' || $ruleName === 'text') && !is_string($value)) {
                self::addError($postname, "$label must be a string.");
                self::addErrs($postname, "must be a string.");
            }

            if (($ruleName === 'numeric' || $ruleName === 'number') && !is_numeric($value)) {
                self::addError($postname, "$label must be a number.");
                self::addErrs($postname, "must be a number.");
            }

            if ($ruleName === 'alpha' && !ctype_alpha($value)) {
                self::addError($postname, "$label must contain only letters.");
                self::addErrs($postname, "must contain only letters.");
            }

            if ($ruleName == "contain" || $ruleName == "having") {
                if (! str_contains((string)$value, (string)$ruleParam)) {
                    self::addError($postname, "$label has invalid value.");
                    self::addErrs($postname, "invalid value.");
                }
            }

            if ($ruleName == "exclude") {
                if (str_contains((string)$value, (string)$ruleParam)) {
                    self::addError($postname, "$label value is not allowed.");
                    self::addErrs($postname, "value is not allowed.");
                }
            }

            if ($ruleName === 'alphanumeric' && !ctype_alnum($value)) {
                self::addError($postname, "$label must contain only letters and numbers.");
                self::addErrs($postname, "must contain only letters and numbers.");
            }

            if ($ruleName === 'regex' && $ruleParam) {
                if (!preg_match($ruleParam, $value)) {
                    self::addError($postname, "$label format is invalid.");
                    self::addErrs($postname, "format is invalid.");
                }
            }

            if ($ruleName === 'in' && $ruleParam) {
                $options = explode(',', $ruleParam);
                if (!in_array($value, $options)) {
                    self::addError($postname, "$label has invalid value.");
                    self::addErrs($postname, "invalid value.");
                }
            }

            if ($ruleName === 'not_in' && $ruleParam) {
                $options = explode(',', $ruleParam);
                if (in_array($value, $options)) {
                    self::addError($postname, "$label value is not allowed.");
                    self::addErrs($postname, "value is not allowed.");
                }
            }

            if ($ruleName === 'date' && strtotime($value) === false) {
                self::addError($postname, "$label must be a valid date.");
                self::addErrs($postname, "invalid date.");
            }

            if ($ruleName === 'url' && !filter_var($value, FILTER_VALIDATE_URL)) {
                self::addError($postname, "$label must be a valid URL.");
                self::addErrs($postname, "invalid URL.");
            }

            if ($ruleName === 'ip' && !filter_var($value, FILTER_VALIDATE_IP)) {
                self::addError($postname, "$label must be a valid IP address.");
                self::addErrs($postname, "must be a valid IP address.");
            }

            if ($ruleName === 'boolean' && !in_array($value, [true, false, 0, 1, '0', '1'], true)) {
                self::addError($postname, "$label must be true or false.");
                self::addErrs($postname, "must be true or false.");
            }

            if ($ruleName === 'length' && strlen($value) !== (int)$ruleParam) {
                self::addError($postname, "$label must be exactly $ruleParam characters.");
                self::addErrs($postname, "must be exactly $ruleParam characters.");
            }

            if ($ruleName === 'starts_with' && !str_starts_with($value, $ruleParam)) {
                self::addError($postname, "$label must start with $ruleParam.");
                self::addErrs($postname, "must start with $ruleParam.");
            }

            if ($ruleName === 'ends_with' && !str_ends_with($value, $ruleParam)) {
                self::addError($postname, "$label must end with $ruleParam.");
                self::addErrs($postname, "must end with $ruleParam.");
            }

            if ($ruleName === 'equal' && $ruleParam !== null) {
                if ($value !== $ruleParam) {
                    self::addError($postname, "$label has invalid value.");
                    self::addErrs($postname, "invalid value.");
                }
            }
        }

        return $org;
    }

    public static function reset()
    {
        self::$errors = [];
        self::$failed = false;
    }

    public static function failed()
    {
        return self::$failed;
    }

    public static function errors($complete = true)
    {
        return $complete ? self::$errors : self::$ers;
    }

    protected static function addError(string $post, string $message)
    {
        self::$errors[$post] = $message;
        self::$failed = true;
    }

    public static function add_error(string $post, string $message)
    {
        self::$errors[$post] = $message;
        self::$failed = true;
        self::addErrs($post, $message);
    }

    protected static function addErrs(string $post, string $message)
    {
        self::$ers[$post] = $message;
        self::$failed = true;
    }

    public static function field_error(string|null|bool $field)
    {
        if (is_null($field) || is_bool($field)) {
            return null;
        }

        $errors = self::$errors;

        if (!$errors) {
            return null;
        }

        $err = isset($errors[$field]) ? $errors[$field] : null;
        return $err;
    }
}
