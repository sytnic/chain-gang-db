<?php

class Admin extends DatabaseObject {

  static protected $table_name = "admins";
  static protected $db_columns = ['id', 'first_name', 'last_name', 'email', 'username', 'hashed_password'];

  public $id;
  public $first_name;
  public $last_name;
  public $email;
  public $username;
  protected $hashed_password;
  public $password;
  public $confirm_password;

  // требование пароля,
  // по умолчанию пароль требуется всегда
  // (при создании админа, при обновлении админа)
  protected $password_required = true;

  public function __construct($args=[]) {
    $this->first_name = $args['first_name'] ?? '';
    $this->last_name = $args['last_name'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->username = $args['username'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->confirm_password = $args['confirm_password'] ?? '';
  }

  public function full_name() {
    return $this->first_name . " " . $this->last_name;
  }

  protected function set_hashed_password() {
    $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  /* переопределяем родительский метод */
  protected function create() {
    $this->set_hashed_password();
    // вызываем работу родительского метода parent::create(),
    // а return нужен, п.что родительский метод возвращает true/false
    return parent::create();
  }

  /* переопределяем родительский метод */
  protected function update() {

    // Если инпут с паролем не пустой,
    // то проверять его на валидацию
    // и задать хэш пароля
    if($this->password != '') {
      $this->set_hashed_password();
      // validate password
    } else {  
      // Иначе - пароль не требуется,
      // пароль не обновлять,
      // skip hashing and validation
      $this->password_required = false;
    }

    return parent::update();
  }

  // validate method for Admin class
  protected function validate() {
    $this->errors = [];

    if(is_blank($this->first_name)) {
      $this->errors[] = "First name cannot be blank.";
    } elseif (!has_length($this->first_name, array('min' => 2, 'max' => 255))) {
      $this->errors[] = "First name must be between 2 and 255 characters.";
    }

    if(is_blank($this->last_name)) {
      $this->errors[] = "Last name cannot be blank.";
    } elseif (!has_length($this->last_name, array('min' => 2, 'max' => 255))) {
      $this->errors[] = "Last name must be between 2 and 255 characters.";
    }

    if(is_blank($this->email)) {
      $this->errors[] = "Email cannot be blank.";
    } elseif (!has_length($this->email, array('max' => 255))) {
      $this->errors[] = "Last name must be less than 255 characters.";
    } elseif (!has_valid_email_format($this->email)) {
      $this->errors[] = "Email must be a valid format.";
    }

    if(is_blank($this->username)) {
      $this->errors[] = "Username cannot be blank.";
    } elseif (!has_length($this->username, array('min' => 8, 'max' => 255))) {
      $this->errors[] = "Username must be between 8 and 255 characters.";
    }

    // Если требование пароля истинно,
    // то проверять пароль и его повтор по правилам валидации
    if($this->password_required) {
      if(is_blank($this->password)) {
        $this->errors[] = "Password cannot be blank.";
      } elseif (!has_length($this->password, array('min' => 12))) {
        $this->errors[] = "Password must contain 12 or more characters";
      } elseif (!preg_match('/[A-Z]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 uppercase letter";
      } elseif (!preg_match('/[a-z]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 lowercase letter";
      } elseif (!preg_match('/[0-9]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 number";
      } elseif (!preg_match('/[^A-Za-z0-9\s]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 symbol";
      }
      // здесь по очереди используется требование любого, хотя бы одного, знака в строке;
      // в последнем случае - хотя бы один не из этого набора.
      // https://regex101.com/
      

      if(is_blank($this->confirm_password)) {
        $this->errors[] = "Confirm password cannot be blank.";
      } elseif ($this->password !== $this->confirm_password) {
        $this->errors[] = "Password and confirm password must match.";
      }
    }

    return $this->errors;
  }

}

?>
