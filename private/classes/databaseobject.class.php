<?php

class DatabaseObject {

    static protected $database;
    static protected $table_name = "";
    static protected $columns = [];
    public $errors = [];

    // ---- START OF ACTIVE RECORD CODE ----

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
            $object_array[] = static::instantiate($record);
        }
        
        $result->free();

        return $object_array;
    }

    /**
     * Получить все записи из таблицы bicycles 
     */
    static public function find_all() {
        $sql = "SELECT * FROM ".static::$table_name;
        // self::$table_name означал бы DatabaseObject::$table_name,
        // поэтому нужно воспользоваться поздним статическим связыванием
        // static::$table_name,
        // чтобы получалось Bicycle::$table_name, Admin::$table_name и т.д.

        // Однако всё, что ссылается на объект database, можно оставлять с self:
        // self::$database->query($sql);
        // self::$database->insert_id;

        // к этому return применяем отдельную функцию для запросов sql
        // return self::$database->query($sql);
        return static::find_by_sql($sql); 
    }

    /**
     * Получить одну запись из таблицы bicycles по её id 
     */
    static public function find_by_id($id) {
        $sql = "SELECT * FROM ".static::$table_name." ";
        $sql.= " WHERE id='".self::$database->escape_string($id)."'";
        // Согласно запросу, получим 1 объект в массиве
        $objects_array = static::find_by_sql($sql);
        
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
        $object = new static;
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

    protected function validate() {
        $this->errors = [];

        // Add custom validations
        // Этот метод будет переопределяться в подклассах

        return $this->errors;
    }

    /**
     * Создаёт запись в БД на основе объекта
     * 
     * @return boolean
     */
    protected function create() {
        $this->validate();
        if(!empty($this->errors)) { return false; }

        $attributes = $this->sanitized_attributes();

        $sql = "INSERT INTO ".static::$table_name." (";
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

    /**
     * Обновляет запись в БД на основе объекта
     * 
     * @return boolean
     */
    protected function update() {
        $this->validate();
        if(!empty($this->errors)) { return false; }

        // экранировать и получить как массив атрибуты объекта
        $attributes = $this->sanitized_attributes();

        $attribute_pairs = [];
        foreach($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }

        $sql = "UPDATE ".static::$table_name." SET ";
        $sql.= join(', ', $attribute_pairs);
        $sql.= " WHERE id='".self::$database->escape_string($this->id)."' ";
        $sql.= " LIMIT 1";
        $result = self::$database->query($sql);
        return $result;
    }

    /**
     * Определяет, объект новый или существующий, и вызывает либо метод create(), либо update()  
     */
    public function save() {
        // A new record will not have an ID yet.
        // Если у объекта есть id, то это существующая запись и нужен update().
        // Если у объекта нет id, то это будущая новая запись и нужен create(). 
        if(isset($this->id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /**
     * Получить в объект свойства и их значения из массива
     */
    public function merge_attributes($args=[]) {
        foreach($args as $key => $value) {
            // c if убеждаемся, что такое свойство существует и
            // оно не null
            if(property_exists($this, $key) && !is_null($value)) {
                // динамически подставляем $свойство и 
                // присваиваем значение из аргумента (массива)
                $this->$key = $value;
            }
        }
    }

    // Properties which have database columns, excluding ID
    /**
     * Получить массив свойств, соответствующий колонкам из БД
     * 
     * @return array
     */
    public function attributes() {
        $attributes = [];
        foreach (static::$db_columns as $column) {
            if($column == 'id') { continue; }
            // используется динамический ->$column
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    /**
     * Экранировать и получить как массив атрибуты объекта
     * 
     * @return array
     */
    protected function sanitized_attributes() {
        $sanitized = [];
        foreach($this->attributes() as $key => $value) {
            $sanitized[$key] = self::$database->escape_string($value);
        }
        return $sanitized;
    }

    /**
     * Удаляет запись из БД 
     * 
     * @return boolean 
     */
    public function delete() {
        $sql = "DELETE FROM ".static::$table_name." ";
        $sql.= " WHERE id='".self::$database->escape_string($this->id)."' ";
        $sql.= " LIMIT 1";
        $result = self::$database->query($sql);
        return $result;

        // After deleting, the instance of the object will still
        // exist, even though the database record does not.
        // This can be useful, as in:
        //   echo $user->first_name . " was deleted.";
        // but, for example, we can't call $user->update() after
        // calling $user->delete().
    }

    // ---- END OF ACTIVE RECORD CODE  ----
    
}

?>