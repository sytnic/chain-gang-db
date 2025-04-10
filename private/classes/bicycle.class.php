<?php

class Bicycle {

    // ---- START OF ACTIVE RECORD CODE ----
    static protected $database;

    // Класс получит собственное соединение с БД
    static public function set_database($database) {
        self::$database = $database;
    }

    static public function find_by_sql($sql) {
        $result = self::$database->query($sql);
        if(!$result) {
            exit("Database query failed.");
        }
        return $result;
    }

    static public function find_all() {
        $sql = "SELECT * FROM bicycles";
        // к этому return применяем отдельную функцию для запросов sql
        // return self::$database->query($sql);
        return self::find_by_sql($sql); 
    }
    // ---- END OF ACTIVE RECORD CODE  ----

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

    protected const CONDITION_OPTIONS = [
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