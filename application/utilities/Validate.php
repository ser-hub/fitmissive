<?php

namespace Application\Utilities;

use Application\Database\DB;

class Validate
{
    private $passed = false,
        $errors = [],
        $db = null;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function check($source, $items = [])
    {
        foreach ($items as $item => $rules) {
            foreach ($rules as $rule => $rule_value) {
                $value = trim($source[$item]);
                $item = Functions::escape($item);

                if ($rule === 'required' && empty($value)) {
                    $this->addError("{$rules['name']} is required.");
                } else if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError("{$rules['name']} is not valid.");
                } else {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError("{$rules['name']} must be at least {$rule_value} characters long.");
                            }
                            break;

                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->addError("{$rules['name']} must be less than {$rule_value} characters long.");
                            }
                            break;

                        case 'matches':
                            if ($value != $source[$rule_value]) {
                                $this->addError("{$rules['name']}s don't match.");
                            }
                            break;

                        case 'contains':
                            foreach (str_split($rule_value) as $char) {
                                if (!str_contains($value, $char)) {
                                    $this->addError("{$rules['name']} must contain '{$char}'.");
                                }
                            }
                            break;

                        case 'unique':
                            $check = $this->db->get($rule_value, array($rules['dbColumn'], '=', $value));
                            if ($check->count()) {
                                $this->addError("{$rules['name']} already exists.");
                            }
                            break;
                    }
                }
            }
        }

        if (empty($this->errors)) {
            $this->passed = true;
        }

        return $this;
    }

    private function addError($error)
    {
        $this->errors[] = $error;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function passed()
    {
        return $this->passed;
    }
}
