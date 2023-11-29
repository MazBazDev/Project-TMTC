<?php
namespace app\core\database;

use app\core\Application;

class Schema
{
    private static function generateColumnSql($columnName, $attributes): string
    {
        $sql = "$columnName {$attributes['type']}";

        if (isset($attributes['nullable']) && $attributes['nullable']) {
            $sql .= " NULL";
        } else {
            $sql .= " NOT NULL";
        }

        if (isset($attributes['unique']) && $attributes['unique']) {
            $sql .= " UNIQUE";
        }

        if (isset($attributes['default'])) {
            $sql .= " DEFAULT " . $attributes['default'];
        }

        if (isset($attributes['auto_increment']) && $attributes['auto_increment']) {
            $sql .= " AUTO_INCREMENT";
        }

        if (isset($attributes['primary']) && $attributes['primary']) {
            $sql .= " PRIMARY KEY";
        }

        return $sql;
    }

    private static function getSqlQuery($sql, Columns $columns): string
    {
        foreach ($columns->getColumns() as $columnName => $attributes) {
            $sql .= self::generateColumnSql($columnName, $attributes) . ", ";
        }

        foreach ($columns->getColumns() as $columnName => $attributes) {
            if (isset($attributes['min'])) {
                $sql .= "CHECK ($columnName >= {$attributes['min']}), ";
            }

            if (isset($attributes['max'])) {
                $sql .= "CHECK ($columnName <= {$attributes['max']}), ";
            }
        }

        return rtrim($sql, ", ") . ");";
    }

    public static function create($tableName, callable $callback)
    {
        $columns = new Columns();
        $callback($columns);
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (";
        Application::$app->db->pdo->exec(self::getSqlQuery($sql, $columns));
    }

    public static function edit($tableName, callable $callback)
    {
        $columns = new Columns();
        $callback($columns);
        $sql = "ALTER TABLE $tableName";

        foreach ($columns->getColumns() as $columnName => $attributes) {
            $sql .= " ADD COLUMN " . self::generateColumnSql($columnName, $attributes) . ";";
        }

        Application::$app->db->pdo->exec($sql);
    }
}
