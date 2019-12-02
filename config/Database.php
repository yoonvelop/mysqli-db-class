<?php

class Database
{
    public $mysqli = null;
    private $list_array = array();

    public function __construct()
    {
        $this->getConnection();
    }

    public function getConnection()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        include_once $path . "/config/db_config.php";

        // define : get user and password

        $this->mysqli = new mysqli(HOST, DB_USER, DB_PW, DB_NAME, PORT);

        if ($this->mysqli->connect_errno) {
            echo "Error MySQLi : (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
            exit();
        }

        $this->mysqli->set_charset("utf8mb4");
    }

    public function __destruct()
    {
        $this->CloseDB();
    }

    public function CloseDB()
    {
        $this->mysqli->close();
    }

    public function runQuery($query)
    {
        $result = $this->mysqli->query($query);
//        echo $query;

        if (!$result) { // Mysql 오류
            $errno = mysqli_errno($this->mysqli);
            $error = mysqli_error($this->mysqli);
            $queryResult = array("isExist" => false, "data" => array("errno" => $errno, "error" => $error));

        } else { // 정상 실행

            if (!isset($result->num_rows)) { // Insert, Update, Delete
                $queryResult = array("isExist" => true, "data" => $result);

            } else { // Select
                if ($result->num_rows == 1) { // Data Count == 1
                    $queryResult = array("isExist" => true, "data" => mysqli_fetch_assoc($result));

                } else { // Data Count > 1
                    if (isset($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            array_push($this->list_array, $row);
                        }
                        $queryResult = array("isExist" => true, "data" => $this->list_array);
                    }
                }
            }
        }
        return $queryResult;
    }

    public function select($table, $rows = '*', $where = null, $join = null, $order = null, $limit = null)
    {
        if ($this->tableExists($table)) {
            $query = 'SELECT ' . $rows . ' FROM ' . $table;
            if ($join != null) {
                $query .= ' JOIN ' . $join;
            }
            if ($where != null) {
                $query .= ' WHERE ' . $where;
            }
            if ($order != null) {
                $query .= ' ORDER BY ' . $order;
            }
            if ($limit != null) {
                $query .= ' LIMIT ' . $limit;
            }
            return $this->runQuery($query);
        } else {
            http_response_code(500);
            return json_encode(array("state" => 500, "message" => "SELECT : Table not exists."));
        }
    }

    public function insert($table, $params = array())
    {
        if ($this->tableExists($table)) {
            $query = 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($params)) . ') VALUES ("' . implode('", "', $params) . '")';
            return $this->runQuery($query);
        } else {
            http_response_code(500);
            return json_encode(array("state" => 500, "message" => "INSERT : Table not exists."));
        }
    }

    public function update($table, $params = array(), $where)
    {
        if ($this->tableExists($table)) {
            $values = "";
            for ($i = 0; $i < count($params); $i++) {
                $value = (array_values($params)[$i] != NULL) ? "'" . array_values($params)[$i] . "'" : 'NULL ';
                $values .= ($i == 0 || $i == count($params) ? '' : ',') . array_keys($params)[$i] . " = " . $value;
            }
            $query = 'UPDATE ' . $table . ' SET ' . $values . " WHERE {$where}";
            return $this->runQuery($query);
        } else {
            http_response_code(500);
            return json_encode(array("state" => 500, "message" => "UPDATE : Table not exists."));
        }
    }

    public function tableExists($table)
    {
        $tables = $this->mysqli->query('SHOW TABLES FROM ' . DB_NAME . ' LIKE "' . $table . '"');
        if ($tables) {
            if (mysqli_num_rows($tables) > 0) {
                return true;
            } else {
                http_response_code(500);
                return json_encode(array("state" => 500, "message" => "TableExists : Table not exists."));
            }
        } else {
            http_response_code(500);
            return json_encode(array("state" => 500, "message" => "TableExists : query error."));
        }
    }

    public function lastInsertId()
    {
        return mysqli_insert_id($this->mysqli);
    }
}

?>
