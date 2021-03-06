<?php
class Database{
    private $di;
    
    private $pdo;
    private $stmt;

    private $debug;
    private $host;
    private $username;
    private $password;
    private $db;
    
    private $table;
    public function __construct(DependencyInjector $di)
    {
        $this->di = $di;
        $config = $this->di->get('config');

        $this->debug = $config->get('debug');
        $this->host = $config->get('host');
        $this->username = $config->get('username');
        $this->password = $config->get('password');
        $this->db = $config->get('db');
        $this->connectDB();
    }

    private function connectDB(){
        try
        {
            
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->db}",$this->username,$this->password);
            if($this->debug)
            {
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            }
        }
        catch(PDOException $e)
        {
            die($this->debug ? $e->getMessage() : "Error While connecting to databaase");
        }
    }
    public function query($sql)
    {
        return $this->pdo->query($sql);
    }
    public function raw($sql,$mode = PDO::FETCH_OBJ)
    {
        /**
         * select query hai toh raw mai
         */
       return $this->query($sql)->fetchAll($mode);
    }
    public function insert(string $table,$data)
    {
        $keys = array_keys($data);

        $fields = "`". implode("`, `", $keys). "`";
        $placeholder = ":". implode(", :",$keys);
        $sql = "INSERT INTO `{$table}` ({$fields}) VALUES ({$placeholder})";
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($data);
        return $this->pdo->lastInsertId();
    }
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId(); 
    }

    public function delete(string $table, $condition)
    {
        
        $sql = "UPDATE {$table} SET deleted = 1 WHERE {$condition}";
        $this->stmt = $this->pdo->prepare($sql);
        // Util::dd($sql);
        return $this->stmt->execute();
    }

    public function update(string $table, $data, $condition = "1")
    {
        $columnKeyValue = "";
        $i=0;
        foreach($data as $key => $value)
        {
            $columnKeyValue .= "$key = :$key";
            $i++;
            if($i < count($data))
            {
                $columnKeyValue.= ", ";
            } 
        }
        $sql = "UPDATE {$table} SET {$columnKeyValue} WHERE {$condition}";
        
        $this->stmt = $this->pdo->prepare($sql);
        return $this->stmt->execute($data);
    }

    public function readData($table,$fields = [],$condition= "1", $readMode = PDO::FETCH_OBJ)
    {
        if(count($fields) == 0)
        {
            $columnNameString = "*";
        }
        else
        {
            $columnNameString = implode(", ", $fields);
        }
        $sql = "SELECT {$columnNameString} FROM {$table} WHERE {$condition}";
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute();
        return $this->stmt->fetchAll($readMode);
    }
    public function exists($table,$data)
    {
        //$data['name'=>'HT'];
        $field = array_keys($data)[0];    
        $result = $this->readData($table,[],"{$field} = '{$data[$field]}' and deleted=0",PDO::FETCH_ASSOC);
        if(count($result) > 0)
        {
            return true;
        }
        else{
            return false;
        }
    }
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    
    public function commit()
    {
        return $this->pdo->commit();
    }
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    //JUST KEPT TO ENSURE THAT WHEN WE ADD AUTH IT WILL BE COMPATIBLE
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }
    public function where($field, $operator, $value)
    {
        $this->stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$field} {$operator} :value");
        $this->stmt->execute(["value" => $value]);
        return $this;
    }

    public function get()
    {
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function first()
    {

        $result = $this->get();
        return !empty($result) ? $result[0] :null;
    }

    public function count()
    {
        return $this->stmt->rowCount();
    }
    
}
?>