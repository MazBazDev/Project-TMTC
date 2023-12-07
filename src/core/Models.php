<?php

namespace app\core;

use app\models\Housing;
use PDO;

class Models
{
    protected $table;

    public $primary_key = "id";
    protected array $fillable = [];
    private array $form_datas = [];
    private $query;


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
            if ($value === null) {
                $setValues[] = "$key = NULL";
            } else {
                if (in_array($key, $this->fillable)) {
                    $setValues[] = "$key = '" . $value . "'";
                    $this->$key = $value;
                }
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
        $instance->query = "SELECT * FROM {$instance->table}";

        $ope = "=";

        if (sizeof($sub) === 2) {
            $key = $sub[0];
            $value = $sub[1];
        } elseif (sizeof($sub) === 3) {
            $key = $sub[0];
            $ope = $sub[1];
            $value = $sub[2];
        }

        $instance->query .= " WHERE $key $ope '$value'";
        return $instance;
    }

    public function first()
    {
        $this->query .= " LIMIT 1";

        $stmt = Application::$app->db->pdo->query($this->query);

        return $stmt->fetchObject(get_called_class());
    }

    public function last($by = "id")
    {
        $this->query .= " ORDER BY {$by} DESC LIMIT 1";
        $stmt = Application::$app->db->pdo->query($this->query);

        return $stmt->fetchObject(get_called_class());
    }

    public function get()
    {
        try {
            $stmt = Application::$app->db->pdo->query($this->query);
            $results = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());

            return $results;
        } catch (\PDOException $e) {
            // Gérer les erreurs PDO ici.
            echo "Erreur lors de la récupération des données : " . $e->getMessage();
            return null;
        }
    }

    public function count()
    {
        $this->query = "SELECT COUNT(*) as count FROM {$this->table}";
        return $this->get()->fetch(PDO::FETCH_ASSOC)['count'];
    }


    public function hasMany(Models $relatedModel, string $foreignKey)
    {
        $relatedInstance = new $relatedModel();
        return $relatedInstance->where([$foreignKey, '=', $this->id])->all();
    }


    public function oneToMany(string $relatedModel, $fk)
    {
        $relatedInstance = new $relatedModel();

        return $relatedInstance->where([$fk, '=', $this->id])->get();
    }

    public function belongsTo(string $relatedModel, string $foreignKey = "id")
    {
        $relatedInstance = new $relatedModel();
        $foreignKeyValue = $this->$foreignKey;
        return $relatedInstance->where(['id', '=', $foreignKeyValue])->get();
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
        if ($tables[0] == $this->table) {
            $query = "SELECT {$tables[1]}.* FROM {$tables[1]} JOIN {$pivotTable} ON {$tables[1]}.id = {$pivotTable}.{$columnB} WHERE {$pivotTable}.{$columnA} = {$this->id};";
        } else {
            $query = "SELECT {$tables[0]}.* FROM {$tables[0]} JOIN {$pivotTable} ON {$tables[0]}.id = {$pivotTable}.{$columnA} WHERE {$pivotTable}.{$columnB} = {$this->id};";
        }

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
        $relatedModel = new $relatedModel();

        $tables = [$this->table, $relatedModel->table];

        sort($tables);

        $pivotTable = implode('_', $tables);

        $columnA = "{$tables[0]}_id";
        $columnB = "{$tables[1]}_id";

        if ($tables[0] === $this->table) {
            $queryCheck = "SELECT COUNT(*) as count FROM {$pivotTable} WHERE  {$columnA} = {$this->id} AND {$columnB} = {$relatedId}";
        } else {
            $queryCheck = "SELECT COUNT(*) as count FROM {$pivotTable} WHERE  {$columnB} = {$this->id} AND {$columnA} = {$relatedId}";
        }

        // Vérifier si la combinaison existe déjà
        try {
            $stmtCheck = Application::$app->db->pdo->query($queryCheck);
            $resultCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if ($resultCheck && $resultCheck['count'] > 0) {
                // La combinaison existe déjà, ne rien faire
                return true;
            }
        } catch (\PDOException $e) {
            echo "Erreur lors de la vérification : " . $e->getMessage();
            return false;
        }

        // La combinaison n'existe pas, exécuter l'insertion
        if ($tables[0] === $this->table) {
            $query = "INSERT INTO {$pivotTable} ({$columnA}, {$columnB}) VALUES ({$this->id}, {$relatedId})";
        } else {
            $query = "INSERT INTO {$pivotTable} ({$columnB}, {$columnA}) VALUES ({$this->id}, {$relatedId})";
        }

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


        if ($tables[0] === $this->table) {
            $query = "DELETE FROM {$pivotTable} WHERE {$columnA} = {$this->id} AND {$columnB} = {$relatedId}";

        } else {
            $query = "DELETE FROM {$pivotTable} WHERE {$columnB} = {$this->id} AND {$columnA} = {$relatedId}";
        }

        try {
            Application::$app->db->pdo->exec($query);
            return true;
        } catch (\PDOException $e) {
            echo "Erreur lors du détachement : " . $e->getMessage();
            return false;
        }
    }
}