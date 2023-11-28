<?php
namespace app\core\database;

use app\core\Application;

class Schema
{
    public static function create($tableName, callable $callback)
    {
        $columns = new Columns();
        $callback($columns);

        $sql = "CREATE TABLE IF NOT EXISTS $tableName (";

        foreach ($columns->getColumns() as $columnName => $attributes) {
            $sql .= "$columnName {$attributes['type']}";

            if (isset($attributes['nullable']) && $attributes['nullable']) {
                $sql .= " NULL";
            } else if (!isset($attributes['unique'])) {
                $sql .= " NOT NULL";
            }

            if (isset($attributes['default'])) {
                $sql .= " DEFAULT '" . $attributes['default'] . "'";
            }

            if (isset($attributes['auto_increment'])) {
                $sql .= " AUTO_INCREMENT";
            }
            if (isset($attributes['unique'])) {
                $sql .= " UNIQUE";
            }

            if (isset($attributes['primary']) && $attributes['primary']) {
                $sql .= " PRIMARY KEY";
            }

            $sql .= ", ";
        }

        foreach ($columns->getColumns() as $columnName => $attributes) {
            if (isset($attributes['min'])) {
                $sql .= "CHECK ($columnName >= {$attributes['min']}), ";
            }

            if (isset($attributes['max'])) {
                $sql .= "CHECK ($columnName <= {$attributes['max']}), ";
            }
        }

        $sql = rtrim($sql, ", ") . ")";
        echo $sql;
        Application::$app->db->pdo->exec($sql);
    }
}
