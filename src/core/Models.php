<?php

namespace app\core;

use PDO;

class Models
{
    protected $table;
    protected array $fillable = [];
    private array $form_datas = [];

    public static function create(array $data)
    {
        $instance = new static();
        $instance->fill($data);
        $instance->save();

        return $instance;
    }

    public function update(array $data)
    {
        $setValues = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $setValues[] = "$key = '" . $value . "'";
                $this->$key = $value;
            }
        }

        $setValues = implode(', ', $setValues);

        $query = "UPDATE {$this->table} SET {$setValues} WHERE id = {$this->id}";

        try {
            Application::$app->db->pdo->exec($query);
            return $this;
        } catch (\PDOException $e) {
            echo "Erreur de mise à jour dans la base de données : " . $e->getMessage();
            return null;
        }
    }

    public function delete()
    {
        if (!$this->id) {
            // Impossible de supprimer sans identifiant
            return false;
        }

        $query = "DELETE FROM {$this->table} WHERE id = {$this->id}";

        try {
            Application::$app->db->pdo->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erreur de suppression dans la base de données : " . $e->getMessage();
            return false;
        }
    }

    protected function fill(array $data)
    {
        foreach (array_keys($data) as $array_key) {
            if (in_array($array_key, $this->fillable)) {
                $this->form_datas[] = $array_key;
            }
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                if (is_bool($value)) $value = $value ? 1: 0;

                $this->$key = $value ?? "";
            }
        }
    }

    protected function save()
    {
        $columns = implode(', ', $this->form_datas);
        $values = implode(', ', array_map(function ($value) {
            if ($this->$value === null) {
                $this->$value = "NULL";
            } else {
                return "'" . $this->$value . "'";
            }
        }, $this->form_datas));

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

    public static function where($sub = [])
    {
        $instance = new static();
        $ope = "=";

        if (sizeof($sub) === 2) {
            $key = $sub[0];
            $value = $sub[1];
        } elseif (sizeof($sub) === 3) {
            $key = $sub[0];
            $ope = $sub[1];
            $value = $sub[2];
        }

        $query = "SELECT * FROM {$instance->table} WHERE $key $ope '$value'";

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

    public function count()
    {
        $instance = new static();

        $query = "SELECT COUNT(*) as count FROM {$instance->table}";

        try {
            $stmt = Application::$app->db->pdo->query($query);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result !== false && isset($result['count'])) {
                $count = intval($result['count']);

                return $count;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();

            return null;
        }
    }

    public function hasMany(Models $relatedModel, string $foreignKey)
    {
        $relatedInstance = new $relatedModel();
        return $relatedInstance->where([$foreignKey, '=', $this->id])->all();
    }


    public function belongsTo(Models $relatedModel, string $foreignKey = "id")
    {
        $relatedInstance = new $relatedModel();
        $foreignKeyValue = $this->$foreignKey;
        return $relatedInstance->where(['id', '=', $foreignKeyValue])->first();
    }

    public function belongsToMany(string $relatedModel)
    {
        // Déterminer le nom de la table pivot en ordre alphabétique
        $tables = [$this->table, (new $relatedModel())->table];
        sort($tables);
        $pivotTable = implode('_', $tables);

        // Déterminer les noms des colonnes dans la table pivot
        $columnA = "{$tables[0]}_id";
        $columnB = "{$tables[1]}_id";

        // Retourner les résultats liés
        $query = "SELECT {$relatedModel}.* FROM {$relatedModel}
                  INNER JOIN {$pivotTable} ON {$relatedModel}.id = {$pivotTable}.{$columnB}
                  WHERE {$pivotTable}.{$columnA} = {$this->id}";

        try {
            $stmt = Application::$app->db->pdo->query($query);
            $results = $stmt->fetchAll(\PDO::FETCH_CLASS, $relatedModel);
        } catch (\PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();
            $results = [];
        }

        return $results;
    }

    public function attach(string $relatedModel, int $relatedId)
    {
        $tables = [$this->table, (new $relatedModel())->table];
        sort($tables);
        $pivotTable = implode('_', $tables);

        $columnA = "{$tables[0]}_id";
        $columnB = "{$tables[1]}_id";

        $query = "INSERT INTO {$pivotTable} ({$columnA}, {$columnB}) VALUES ({$this->id}, {$relatedId})";

        try {
            Application::$app->db->pdo->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erreur lors de l'attachement : " . $e->getMessage();
            return false;
        }
    }

    public function detach(string $relatedModel, int $relatedId)
    {
        $tables = [$this->table, (new $relatedModel())->table];
        sort($tables);
        $pivotTable = implode('_', $tables);

        $columnA = "{$tables[0]}_id";
        $columnB = "{$tables[1]}_id";

        $query = "DELETE FROM {$pivotTable} WHERE {$columnA} = {$this->id} AND {$columnB} = {$relatedId}";

        try {
            Application::$app->db->pdo->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erreur lors du détachement : " . $e->getMessage();
            return false;
        }
    }
}