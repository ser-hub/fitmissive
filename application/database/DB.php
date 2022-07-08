<?php

namespace Application\Database;

use application\utilities\Config;

use \PDO;
use \PDOException;

//database wrapper class
class DB
{
    private static $instance = null;
    private $pdo,
        $query,
        $error,
        $results,
        $count = 0;

    private function __construct()
    {
        try {
            $this->pdo = new PDO(
                'mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'),
                Config::get('mysql/username'),
                Config::get('mysql/password')
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        $this->error = false;
        if ($this->query = $this->pdo->prepare($sql)) {
            if (count($params)) {
                $x = 1;
                foreach ($params as $param) {
                    $this->query->bindValue($x, $param);
                    $x++;
                }
            }

            if ($this->query->execute()) {
                $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
                $this->count = $this->query->rowCount();
            } else {
                $this->error = true;
            }
        }

        return $this;
    }

    public function action($action, $table, $where = [])
    {
        if (count($where) === 3) {
            $operators = array('=', '>', '<', '>=', '<=');

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if (in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if (!$this->query($sql, array($value))->error()) {
                    return $this;
                }
            }
        }
        return false;
    }

    public function get($table, $where)
    {
        return $this->action('SELECT *', $table, $where);
    }

    public function delete($table, $where)
    {
        return $this->action('DELETE', $table, $where);
    }

    public function insert($table, $fields = [])
    {
        $keys = array_keys($fields);
        $values = null;
        $x = 1;

        foreach ($fields as $field) {
            $values .= '?';
            if ($x < count($fields)) {
                $values .= ', ';
            }
            $x++;
        }

        $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

        if (!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function update($table, $id = array(), $fields = [])
    {
        $set = '';
        $x = 1;

        foreach ($fields as $name => $value) {
            $set .= "{$name} = ?";
            if ($x < count($fields)) {
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE {$id['field']} = {$id['value']}";

        if (!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function error()
    {
        return $this->error;
    }

    public function count()
    {
        return $this->count;
    }

    public function results()
    {
        if ($this->results) return $this->results;
        else return null;
    }

    public function first()
    {
        if ($this->results()) return $this->results()[0];
        else return null;
    }
}
