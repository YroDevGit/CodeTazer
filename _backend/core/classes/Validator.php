<?php

namespace Classes;

class Validator
{
    /**
     * Tyrone Validator
     * inspired to Express validator
     */
    private static $errors = [];
    private static $failed = false;
    private static $ers = [];

    private string $field;
    private string $label = '';
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

    public function validate(): mixed
    {
        $rulesString = implode('|', $this->rules);
        $label = $this->label ?: $this->field;
        return self::check($this->field, $label, $rulesString);
    }

    public function x()
    {
        return $this->validate();
    }

    public static function check($postname, $label, $rules)
    {
        $postdata = postdata();
        if (!isset($postdata[$postname])) {
            $postdata[$postname] = null;
        }

        $value = trim($postdata[$postname] ?? '');
        $rulesArray = explode('|', $rules);
        $hasRequired = in_array('required', $rulesArray);
        $rulesArray = array_reverse($rulesArray);

        foreach ($rulesArray as $rule) {
            $ruleParts = explode(':', $rule, 2);
            $ruleName = $ruleParts[0];
            $ruleParam = $ruleParts[1] ?? null;

            if ($value === '' && $ruleName !== 'required' && !$hasRequired) {
                continue;
            }

            // --- Your existing checks (exactly from your code) ---
            if ($ruleName === 'required' && $value === '') {
                self::addError($postname, "$label is required.");
                self::addErrs($postname, "Required");
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

            if ($ruleName === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                self::addError($postname, "$label must be a valid email address.");
                self::addErrs($postname, "must be a valid email address.");
            }

            if (($ruleName === 'string' || $ruleName === 'text') && !is_string($value)) {
                self::addError($postname, "$label must be a string.");
                self::addErrs($postname, "must be a string.");
            }

            if (($ruleName === 'numeric' || $ruleName === 'number') && !is_numeric($value)) {
                self::addError($postname, "$label must be a number.");
                self::addErrs($postname, "must be a number.");
            }

            // ... keep all your other checks (alpha, alphanumeric, regex, in, not_in, date, url, ip, boolean, length, starts_with, ends_with, etc.)
        }

        return $value;
    }

    // Reset state
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

    protected static function addErrs(string $post, string $message)
    {
        self::$ers[$post] = $message;
        self::$failed = true;
    }
}
