<?php

namespace app\core\database;

class Columns
{
    public array $columns = [];

    public function id(): Columns
    {
        $this->columns["id"]['type'] = 'INT';
        $this->columns["id"]['auto_increment'] = true;
        $this->columns["id"]['unique'] = true;

        return $this;
    }

    public function timestamp($name): Columns
    {
        $this->columns[$name]['type'] = 'TIMESTAMP';
        return $this;
    }


    public function boolean($name): Columns
    {
        $this->columns[$name]['type'] = 'BOOLEAN';
        return $this;
    }
    public function string($name, $length = 255): Columns
    {
        $this->columns[$name] = ['type' => "VARCHAR($length)"];
        return $this;
    }

    public function longtext($name): Columns
    {
        $this->columns[$name] = ['type' => 'LONGTEXT'];
        return $this;
    }

    public function float($name, $precision = 10, $scale = 2): Columns
    {
        $this->columns[$name] = ['type' => "FLOAT($precision, $scale)"];
        return $this;
    }

    public function nullable($bool = true): Columns
    {
        $this->columns[key($this->columns)]['nullable'] = $bool;
        return $this;
    }

    public function min($value): Columns
    {
        $this->columns[key($this->columns)]['min'] = $value;
        return $this;
    }

    public function max($value): Columns
    {
        $this->columns[key($this->columns)]['max'] = $value;
        return $this;
    }

    public function default($value): Columns
    {
        $this->columns[key($this->columns)]['default'] = $value;
        return $this;
    }

    public function unique(): Columns
    {
        $this->columns[key($this->columns)]['unique'] = true;
        return $this;
    }

    public function primary(): Columns
    {
        $this->columns[key($this->columns)]['primary'] = true;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }
}
