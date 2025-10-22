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
    $this->set_hashed_password();
    return parent::update();
  }

}

?>
