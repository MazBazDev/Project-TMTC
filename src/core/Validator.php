<?php

namespace app\core;

abstract class Validator
{
    private array $messages = [];

    public function validate($rules, $messages = []): array
    {
        $errors = [];
        $this->messages = $messages;

        foreach ($rules as $input => $rule) {
            $value = $this->input($input);

            if ($value === null) {
                return $errors;
            }

            // Split the rule into individual validation methods
            $validations = explode(';', $rule);

            // Check for 'bail' validation and return errors immediately
            $errors = $this->bail($validations, $input, $value, $errors);
        }

        if (sizeof($errors) !== 0) {
            return Application::$app->response->redirect()
                ->back()
                ->with("inputs_errors", $errors)
                ->with("inputs_old", $this->getBody());
        }

        return $errors;
    }

    private function bail($validations, $input, $value, $errors): array
    {
        if (!empty($errors[$input])) {
            return $errors;
        }

        foreach ($validations as $validation) {
            list($type, $params) = array_pad(explode(':', $validation, 2), 2, null);

            if ($type !== 'bail') {
                $isValid = $this->{$type}($value, $params);

                if (!$isValid) {
                    $errors[$input][] = $this->getErrorMessage($type, $input, $params);

                    if ($this->hasBail($validations)) {
                        return $errors;
                    }
                }
            }
        }

        return $errors;
    }
    private function getErrorMessage($type, $input, $params = null): string
    {
        if (!empty($this->messages) && key_exists($input, $this->messages)) {
            $messages = $this->messages[$input];

            if (key_exists($type, $messages)) {
                return $messages[$type];
            }
        }

        switch ($type) {
            case 'required':
                return "The field '{$input}' is required.";
            case 'min':
                return "The field '{$input}' must be at least {$params} characters long.";
            case 'max':
                return "The field '{$input}' must be at most {$params} characters long.";
            case 'unique':
                return "The {$input} already exist.";
            case 'exist':
                return "The {$input} does not exist in our data.";
            case 'equal':
                return "The {$input} don't match with {$params}";
            default:
                return "Validation error for the field '{$input}'.";
        }
    }
    private function hasBail($validations): bool
    {
        return in_array('bail', $validations);
    }

    private function required($value): bool
    {
        return !empty($value) || $value !== "";
    }

    private function min($value, $minLength): bool
    {
        return strlen($value) >= $minLength;
    }

    private function max($value, $maxLength): bool
    {
        return strlen($value) <= $maxLength;
    }

    private function after_date($value, $date): bool
    {
        $inputTimestamp = strtotime($value);
        $dateTimestamp = strtotime($date);

        return $inputTimestamp > $dateTimestamp;
    }

    private function before_date($value, $date): bool
    {
        $inputTimestamp = strtotime($value);
        $dateTimestamp = strtotime($date);

        return $inputTimestamp < $dateTimestamp;
    }
    private function after($value, $inputName): bool
    {
        $inputDate = $this->input($inputName);

        if ($inputDate === null) {
            return false;
        }

        $inputTimestamp = strtotime($value);
        $inputDateTimestamp = strtotime($inputDate);

        return $inputTimestamp > $inputDateTimestamp;
    }

    private function before($value, $inputName): bool
    {
        $inputDate = $this->input($inputName);

        if ($inputDate === null) {
            return false;
        }

        $inputTimestamp = strtotime($value);
        $inputDateTimestamp = strtotime($inputDate);

        return $inputTimestamp < $inputDateTimestamp;
    }

    private function unique($value, $inputName) : bool {
        $class = explode(",", $inputName)[0];
        $col = explode(",", $inputName)[1];

        $class = new $class();

        $exist = $class->where([$col, $value])->first() !== false;

        return !$exist;
    }

    private function exist($value, $inputName) : bool {
        $class = explode(",", $inputName)[0];
        $col = explode(",", $inputName)[1];

        $class = new $class();

        $exist = $class->where([$col, $value])->first() !== false;

        return $exist;
    }

    private function equal($value, $inputName): bool
    {
        $inputValue = $this->input($inputName);

        return $value === $inputValue;
    }
}