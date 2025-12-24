<?php

class Session {

  private $admin_id;

  // при вызове класса Сессии (будет вызываться в initialize),
  // начать сессию,
  // проверить, сохранён ли уже вход в сессии
  public function __construct() {
    session_start();
    $this->check_stored_login();
  }

  // вход в сессию
  public function login($admin) {
    // Если админ есть и задан
    if($admin) {
      // предотвращаем атаку фиксации сессии
      // prevent session fixation attacks
      session_regenerate_id();
      // помещаем id в сессию
      $_SESSION['admin_id'] = $admin->id;
      // помещаем id в свойство класса
      $this->admin_id = $admin->id;
    }
    return true;
  }

  // проверка вошедшего пользователя
  public function is_logged_in() {
    // если задан admin_id, вернётся true
    // иначе - false
    return isset($this->admin_id);
  }

  // выход из сессии
  public function logout() {
    // при выходе пользователя
    // стираем переменные сессии и класса
    unset($_SESSION['admin_id']);
    unset($this->admin_id);
    return true;
  }

  // проверка хранящегося логина 
  private function check_stored_login() {
    // если пользователь в сессии,
    // то присвоить это значение и свойству класса
    if(isset($_SESSION['admin_id'])) {
      $this->admin_id = $_SESSION['admin_id'];
    }
  }

}


?>