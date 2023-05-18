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
                    $this->addError("{$rules['name']} е задължително.");
                } else if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError("{$rules['name']} не е валиден.");
                } else {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $ruleValue) {
                                $this->addError("{$rules['name']} трябва да е поне {$ruleValue} символа.");
                            }
                            break;

                        case 'max':
                            if (strlen($value) > $ruleValue) {
                                $this->addError("{$rules['name']} трябва да е по-малко от {$ruleValue} символа.");
                            }
                            break;

                        case 'matches':
                            if ($value != $source[$ruleValue]) {
                                $this->addError("{$rules['name']} не съвпада.");
                            }
                            break;

                        case 'contains':
                            foreach (str_split($ruleValue) as $char) {
                                if (!str_contains($value, $char)) {
                                    if ($char === ' ') {
                                        $this->addError("{$rules['name']} трябва да съдържа празен символ.");
                                    }
                                    $this->addError("{$rules['name']} трябва да съдържа '{$char}'.");
                                }
                            }
                            break;
                        case '!contains':
                            foreach (str_split($ruleValue) as $char) {
                                if (str_contains($value, $char)) {
                                    $this->addError("{$rules['name']} съдържа забранени символи.");
                                    break;
                                }
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
            $this->addError('Грешка при качването на файла.');
        }

        foreach ($items as $rule => $ruleValue) {
            switch ($rule) {
                case 'allowedTypes':
                    if (!in_array($fileActualExt, $ruleValue)) {
                        $this->addError("Не може да качвате файлове от този тип.");
                    }
                    break;

                case 'maxSize':
                    if ($fileSize > $ruleValue) {
                        $this->addError("Файлът е твърде голям.");
                    }
                    break;

                case 'illegalSymbols':
                    foreach ($ruleValue as $value) {
                        if (str_contains($fileName, $value)) {
                            $this->addError("Името на файла съдържа забранени символи.");
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
