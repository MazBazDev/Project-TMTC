<?php

namespace app\core\database;

class Columns
{
    public array $columns = [];
    protected $currentColumn;

    public function id($name = "id"): Columns
    {
        $this->currentColumn = $name;

        $this->columns[$name]['type'] = 'INT';
        $this->columns[$name]['auto_increment'] = true;
        $this->columns[$name]['unique'] = true;

        return $this;
    }

    public function int($name): Columns
    {
        $this->currentColumn = $name;

        $this->columns[$name]['type'] = 'INT';
        return $this;
    }

    public function timestamp($name): Columns
    {
        $this->currentColumn = $name;

        $this->columns[$name]['type'] = 'TIMESTAMP';
        return $this;
    }

    public function boolean($name): Columns
    {
        $this->currentColumn = $name;

        $this->columns[$name]['type'] = 'BOOLEAN';
        return $this;
    }

    public function string($name, $length = 255): Columns
    {
        $this->currentColumn = $name;

        $this->columns[$name] = [
            'type' => "VARCHAR($length)",
        ];
        return $this;
    }

    public function longtext($name): Columns
    {
        $this->currentColumn = $name;

        $this->columns[$name]['type'] = 'LONGTEXT';
        return $this;
    }

    public function float($name, $precision = 10, $scale = 2): Columns
    {
        $this->currentColumn = $name;

        $this->columns[$name]['type'] = "FLOAT($precision, $scale)";
        return $this;
    }

    public function nullable($bool = true): Columns
    {
        $this->columns[$this->currentColumn]['nullable'] = $bool;
        return $this;
    }

    public function min($value): Columns
    {
        $this->columns[$this->currentColumn]['min'] = $value;
        return $this;
    }

    public function max($value): Columns
    {
        $this->columns[$this->currentColumn]['max'] = $value;
        return $this;
    }

    public function default($value): Columns
    {
        $this->columns[$this->currentColumn]['default'] = "'" . $this->formatDefaultValue($value) . "'";

        return $this;
    }

    public function defaultSQL($value): Columns
    {
        $this->columns[$this->currentColumn]['default'] = $this->formatDefaultValue($value);

        return $this;
    }


    public function unique(): Columns
    {
        $this->columns[$this->currentColumn]['unique'] = true;
        return $this;
    }

    public function primary(): Columns
    {
        $this->columns[$this->currentColumn]['primary'] = true;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    private function formatDefaultValue($value)
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        } else {
            return $value;
        }
    }
}
