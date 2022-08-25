<?php

namespace Application\Utilities;

use Application\Database\DB;

class Validator
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
            foreach ($rules as $rule => $ruleValue) {
                $value = trim($source[$item]);
                $item = Functions::escape($item);

                if ($rule === 'required' && empty($value)) {
                    $this->addError("{$rules['name']} is required.");
                } else if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError("{$rules['name']} is not valid.");
                } else {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $ruleValue) {
                                $this->addError("{$rules['name']} must be at least {$ruleValue} characters long.");
                            }
                            break;

                        case 'max':
                            if (strlen($value) > $ruleValue) {
                                $this->addError("{$rules['name']} must be less than {$ruleValue} characters long.");
                            }
                            break;

                        case 'matches':
                            if ($value != $source[$ruleValue]) {
                                $this->addError("{$rules['name']}s don't match.");
                            }
                            break;

                        case 'contains':
                            foreach (str_split($ruleValue) as $char) {
                                if (!str_contains($value, $char)) {
                                    if ($char === ' ') {
                                        $this->addError("{$rules['name']} must contain empty spaces.");
                                    }
                                    $this->addError("{$rules['name']} must contain '{$char}'.");
                                }
                            }
                            break;
                        case '!contains':
                            foreach (str_split($ruleValue) as $char) {
                                if (str_contains($value, $char)) {
                                    $this->addError("{$rules['name']} contains illegal characters.");
                                    break;
                                }
                            }
                            break;

                        case 'unique':
                            $check = $this->db->get($ruleValue, array($rules['dbColumn'], '=', $value));
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

    public function checkFile($source, $items = [])
    {
        $fileName = $source['name'];
        $fileSize = $source['size'];
        $fileError = $source['error'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        if ($fileError === 1) {
            $this->addError('Error uploading your file.');
        }

        foreach ($items as $rule => $ruleValue) {
            switch ($rule) {
                case 'allowedTypes':
                    if (!in_array($fileActualExt, $ruleValue)) {
                        $this->addError("You cannot upload files of this type.");
                    }
                    break;

                case 'maxSize':
                    if ($fileSize > $ruleValue) {
                        $this->addError("File too big.");
                    }
                    break;

                case 'illegalSymbols':
                    foreach ($ruleValue as $value) {
                        if (str_contains($fileName, $value)) {
                            $this->addError("File name contains illegal symbols.");
                        }
                    }
                    break;
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
