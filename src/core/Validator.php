<?php

namespace app\core;

abstract class Validator
{
    public function validate($rules): array
    {
        $errors = [];

        foreach ($rules as $input => $rule) {
            $value = $this->input($input);

            // Split the rule into individual validation methods
            $validations = explode(';', $rule);

            foreach ($validations as $validation) {
                // Extract validation type and parameters (if any)
                list($type, $params) = array_pad(explode(':', $validation, 2), 2, null);

                if ($type === 'bail') {
                    // If 'bail' is present, return errors immediately
                    if (!empty($errors[$input])) {
                        return $errors;
                    }
                } else {
                    // Call the corresponding validation method
                    $isValid = $this->{$type}($value, $params);

                    if (!$isValid) {
                        $errors[$input][] = $this->getErrorMessage($type, $input, $params);

                        // If 'bail' is present, return errors immediately
                        if ($this->hasBail($validations)) {
                            return $errors;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    private function getErrorMessage($type, $input, $params = null): string
    {
        switch ($type) {
            case 'required':
                return "The field '{$input}' is required.";
            case 'min':
                return "The field '{$input}' must be at least {$params} characters long.";
            case 'max':
                return "The field '{$input}' must be at most {$params} characters long.";
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
        return !empty($value);
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
            // If the specified input doesn't exist, consider validation failed
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
            // If the specified input doesn't exist, consider validation failed
            return false;
        }

        $inputTimestamp = strtotime($value);
        $inputDateTimestamp = strtotime($inputDate);

        return $inputTimestamp < $inputDateTimestamp;
    }

}