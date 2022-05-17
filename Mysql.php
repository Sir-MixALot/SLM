<?php

class Mysql
{
    private $USER = 'papaya';
    private $PASSWORD = 'papaya';
    private $HOST = '127.0.0.1';
    private $DB = 'slm';
    private $TABLE = 'people';

    private $conn;
    private $availableColumns = [];

    public $id;
    public $name;
    public $surname;
    public $DOB;
    public $sex;
    public $COB;

    public function __construct($data)
    {
        $this->id = $data[0];
        $this->name = $data[1];
        $this->surname = $data[2];
        $this->DOB = $data[3];
        $this->sex = $data[4];
        $this->COB = $data[5];
        $this->conn = new PDO( 'mysql:host='.$this->HOST.';dbname='.$this->DB.'', $this->USER, $this->PASSWORD );
        $result = $this->readData('Id', $this->id);
        if(empty($result)){
            $this->saveData(['Id' => $this->id, 'Name' => $this->name, 'Surname' => $this->surname, 'Date' => $this->DOB, 'Sex' => $this->sex, 'City' => $this->COB]);
            echo 'Person was successfully added to DB!';
        }else{
            $result = array_intersect_key($result, array_flip($this->availableColumns));
            $result = $this->transformation($result, $result['Date'], $result['Sex']);
            echo implode(',', array_values($result));
        }
    } 

    private function connection()
    {
        $sql = 'DESCRIBE ' .$this->TABLE;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();
        $this->availableColumns = array_column($res, 'Field');      
    }

    public function saveData(array $newData)
    {
        $this->connection();
        $columns = ' (' . implode(',',$this->availableColumns) . ') ';
        $values = implode('","' , array_values($newData));
        $sql = 'INSERT INTO '.$this->TABLE . $columns.'VALUES("'.$values.'")';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }

    public function readData(string $field = "", $value = "")
    {
        $this->connection();
        $where = "";
        if(!empty($field) && !empty($value)){
            $where = " where $field = $value";
        }
        
        $sql = 'SELECT * FROM '.$this->TABLE . $where;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch();
        
        return $res;
    }

    public function transformation($array, $date, $sex)
    {
        if(!empty($array)){
            if(!empty($date)){
                $array['Date'] = static::dateTransformation($date);
                $array = $this->changeKey($array, array('Date' => 'Age'));
                
            }
            if($sex>=0 || $sex<=1){
                $array['Sex'] = static::sexTransformation($sex);
            }
        }
        return $array;
    }

    public static function dateTransformation($date)
    {
        $currentDate = date("Y-m-d");
        $age = date_diff(date_create($currentDate), date_create($date));
        return $age->format("%y");
    }

    public static function sexTransformation($sex)
    {
        return ($sex == 0) ? 'female' : 'male';  
    }

    private function changeKey($arr, $set) {
        if (is_array($arr) && is_array($set)) {
    		$newArr = array();
    		foreach ($arr as $k => $v) {
    		    $key = array_key_exists( $k, $set) ? $set[$k] : $k;
    		    $newArr[$key] = $v;
    		}
    		return $newArr;
    	} 
    }

    public function deletePerson($id)
    {
        
        if(!empty($id)){
            $this->connection();
            $sql = 'DELETE FROM ' . $this->TABLE . ' WHERE Id = ' . $id;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            echo 'Person was deleted';
            
        }

    }
}

?>