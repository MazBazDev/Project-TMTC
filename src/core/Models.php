<?php

namespace app\core;

use function MongoDB\BSON\toJSON;

class Models
{
    protected $table;
    protected $fillable = [];

    public static function create(array $data)
    {
        $instance = new static();
        $instance->fill($data);
        $instance->save();

        return $instance;
    }

    protected function fill(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                if (is_bool($value)) $value = $value ? 1: 0;

                $this->$key = $value;
            }
        }
    }

    protected function save()
    {
        $columns = implode(', ', $this->fillable);
        $values = implode(', ', array_map(function ($key) {
            return "'" . $this->$key . "'";
        }, $this->fillable));

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";

        try {
            Application::$app->db->pdo->exec($query);

            $this->id = Application::$app->db->pdo->lastInsertId();

            return $this;
        } catch (\PDOException $e) {

            echo "Erreur d'insertion dans la base de données : " . $e->getMessage();
            return null;
        }
    }

    public static function all()
    {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table}";

        try {
            $stmt = Application::$app->db->pdo->query($query);

            $results = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        } catch (\PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();

            $results = [];
        }

        return $results;
    }

    public static function where($key, $value)
    {
        $instance = new static();

        $query = "SELECT * FROM {$instance->table} WHERE $key = '$value'";

        try {
            $stmt = Application::$app->db->pdo->query($query);

            $result = $stmt->fetchObject(get_called_class());
        } catch (\PDOException $e) {
            // Gérer les erreurs PDO ici.
            echo "Erreur lors de la récupération des données : " . $e->getMessage();
            $result = null;
        }

        return $result;
    }


    public function first()
    {
        $instance = new static();

        $query = "SELECT * FROM {$instance->table} LIMIT 1";

        try {
            $stmt = Application::$app->db->pdo->query($query);

            $result = $stmt->fetchObject(get_called_class());
        } catch (\PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();

            $result = null;
        }

        return $result;
    }

    public function last()
    {
        $instance = new static();

        $query = "SELECT * FROM {$instance->table} ORDER BY id DESC LIMIT 1";

        try {
            $stmt = Application::$app->db->pdo->query($query);

            $result = $stmt->fetchObject(get_called_class());
        } catch (\PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();

            $result = null;
        }

        return $result;
    }
}