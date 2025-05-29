<?php

class Bicycle {

    // ---- START OF ACTIVE RECORD CODE ----

    static protected $database;
    static protected $db_columns = ['id', 'brand', 'model', 'year', 'category', 
        'color', 'gender', 'price', 'weight_kg', 'condition_id', 'description'];

    // Класс получит собственное соединение с БД
    static public function set_database($database) {
        self::$database = $database;
    }

    static public function find_by_sql($sql) {
        $result = self::$database->query($sql);
        if(!$result) {
            exit("Database query failed.");
        }

        // results into objects
        // ассоциативный массив, полученный в результате запроса sql,
        // преобразуется в объекты, вкладываемые в массив
        $object_array = [];
        while($record = $result->fetch_assoc()) {
            $object_array[] = self::instantiate($record);
        }
        
        $result->free();

        return $object_array;
    }

    /**
     * Получить все записи из таблицы bicycles 
     */
    static public function find_all() {
        $sql = "SELECT * FROM bicycles";
        // к этому return применяем отдельную функцию для запросов sql
        // return self::$database->query($sql);
        return self::find_by_sql($sql); 
    }

    /**
     * Получить одну запись из таблицы bicycles по её id 
     */
    static public function find_by_id($id) {
        $sql = "SELECT * FROM bicycles";
        $sql.= " WHERE id='".self::$database->escape_string($id)."'";
        // Согласно запросу, получим 1 объект в массиве
        $objects_array = self::find_by_sql($sql);
        
        if(!empty($objects_array)) {
            // возвращаем один объект из массива
            return array_shift($objects_array);
        } else {
            return false;
        }
    }
    

    /**
     * Создаёт объект со значениями свойств из строки БД
     */
    static protected function instantiate($record) {
        $object = new self;
        // Could manually assign values to properties
        // but automatically assignment is easier and re-usable

        // Берём строку из БД и каждый столбец разбиваем на свойства (заголовок столбца) и значения
        foreach($record as $property => $value) {
          // Если свойство (название столбца) из строки БД
          // есть также в качестве свойства в объекте,
          // то присовить значение ячейки из БД этому свойству объекта
          if(property_exists($object, $property)) {
            // динамический $property
            $object->$property = $value;
          }
        }
        // Вернуть получившийся объект с заполненными значениями свойств
        return $object;
    }

    /**
     * Создаёт запись в БД на основе объекта
     * 
     * @return boolean
     */
    public function create() {

        $attributes = $this->attributes();

        $sql = "INSERT INTO bicycles (";
        // $sql.= "brand, model, year, category, color, gender, price, weight_kg, condition_id, description";
        // заменяем эту строку массивом
        // $sql.= join(', ', self::$db_columns);
        // или так
        $sql.= join(', ', array_keys($attributes));
        $sql.= ") VALUES ('";
        $sql.= join("', '", array_values($attributes));
        // этот код заменён на строку с array_values($attributes)
        /*
        $sql.= "'".$this->brand."', ";
        $sql.= "'".$this->model."', ";
        $sql.= "'".$this->year."', ";
        $sql.= "'".$this->category."', ";
        $sql.= "'".$this->color."', ";
        $sql.= "'".$this->gender."', ";
        $sql.= "'".$this->price."', ";
        $sql.= "'".$this->weight_kg."', ";
        $sql.= "'".$this->condition_id."', ";
        $sql.= "'".$this->description."'";
        */        
        $sql.="')";

        // var_dump($this->attributes());
        // var_dump(self::$db_columns);
        // var_dump($attributes);
        // var_dump($sql);

        $result = self::$database->query($sql);
        
        // получить id для объекта
        if($result) {
            $this->id = self::$database->insert_id;
        }

        return $result;
    }

    // Properties which have database columns, excluding ID
    /**
     * Получить массив свойств, соответствующий колонкам из БД
     * 
     * @return array
     */
    public function attributes() {
        $attributes = [];
        foreach (self::$db_columns as $column) {
            if($column == 'id') { continue; }
            // используется динамический ->$column
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    // ---- END OF ACTIVE RECORD CODE  ----

    public $id;
    public $brand;
    public $model;
    public $year;
    public $category;
    public $color;
    public $description;
    public $gender;
    public $price;
    protected $weight_kg;
    protected $condition_id;

    public const CATEGORIES = ['Road', 'Mountain', 'Hybrid', 'Cruiser', 'City', 'BMX'];

    public const GENDERS = ['Mens', 'Womens', 'Unisex'];

    public  const CONDITION_OPTIONS = [
        1 => 'Beat up',
        2 => 'Decent',
        3 => 'Good',
        4 => 'Great',
        5 => 'Like New',
    ];

    public function __construct($args=[]) {
        // $this->brand = isset($args['brand']) ? $args['brand'] : '';
        // вместо тернарного оператора применяется оператор нулевого объединения
        $this->brand = $args['brand'] ?? '';
        $this->model = $args['model'] ?? '';
        $this->year = $args['year'] ?? '';
        $this->category = $args['category'] ?? '';
        $this->color = $args['color'] ?? '';
        $this->description = $args['description'] ?? '';
        $this->gender = $args['gender'] ?? '';
        $this->price = $args['price'] ?? 0;
        $this->weight_kg = $args['weight_kg'] ?? 0.0;
        $this->condition_id = $args['condition_id'] ?? 3;

        /* Caution: allows private/protected properties to be set.
           Иначе говоря, - это другой вариант вместо предыдущих строк, 
           но нежелательный, т.к. так метод __construct 
           сможет получить доступ к возможным защищенным свойствам.
           foreach($args as $k => $v) {
                if (property_exists($this, $k)) {
                    $this->$k = $v;   // динамическая переменная $this->$k
                }
           }
        */

    }

    public function name() {
        return "{$this->brand} {$this->model} {$this->year}";
    }

    public function weight_kg() {
        return number_format($this->weight_kg, 2).' kg';
    }
    
    public function set_weight_kg($value) {
        $this->weight_kg = floatval($value);
    }

    public function weight_lbs() {
        $weight_lbs = floatval($this->weight_kg) * 2.2046226218;
        return number_format($weight_lbs, 2).' lbs';
    }

    public function set_weight_lbs($value) {
        $this->weight_kg = floatval($value) / 2.2046226218;
    }

    public function condition() {
        if($this->condition_id > 0) {
            return self::CONDITION_OPTIONS[$this->condition_id];
        } else {
            return "Unknown";
        }
    }

}

?>