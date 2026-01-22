<?php

class Session {

  private $admin_id;
  public $username;
  private $last_login;

  // максимальный возраст хранения входа в систему
  public const MAX_LOGIN_AGE = 60*60*24; // 1 day

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

      // храним имя и время входа в систему,
      // и одновременно присваиваем значения в сессию
      $this->username = $_SESSION['username'] = $admin->username;
      $this->last_login = $_SESSION['last_login'] = time();
    }
    return true;
  }

  // проверка вошедшего пользователя
  public function is_logged_in() {
    // если задан admin_id, вернётся true,
    // иначе - false
    // return isset($this->admin_id);

    // другой вариант,
    // если задан admin_id и последний вход является недавним,
    // возвращаем true
    return isset($this->admin_id) && $this->last_login_is_recent();
  }

  // выход из сессии
  public function logout() {
    // при выходе пользователя
    // стираем переменные сессии и класса
    unset($_SESSION['admin_id']);
    unset($_SESSION['username']);
    unset($_SESSION['last_login']);
    unset($this->admin_id);
    unset($this->username);
    unset($this->last_login);
    return true;
  }

  // проверка хранящегося логина 
  private function check_stored_login() {
    // если пользователь в сессии,
    // то присвоить значения сессии свойствам класса
    if(isset($_SESSION['admin_id'])) {
      $this->admin_id = $_SESSION['admin_id'];
      $this->username = $_SESSION['username'];
      $this->last_login = $_SESSION['last_login'];
    }
  }

  /**
   * Является ли вход недавним
   * 
   * @return boolean
   */
  private function last_login_is_recent() {
    // если последний вход не объявлен - ложь
    if(!isset($this->last_login)) {
      return false;
      // если последний вход плюс максимальное время
      // отстало от текущего времени - ложь
    } elseif(($this->last_login + self::MAX_LOGIN_AGE) < time()) {
      return false;
      // иначе - истина
    } else {
      return true;
    }
  }

  /**
   * Задаёт сообщение в сессию, если оно передано в аргументе (set), и
   * Получает сообщение из сессии, если аргумент пуст (get).
   * 
   */
  public function message($msg="") {
    if(!empty($msg)) {
      // Then this is a "set" message
      $_SESSION['message'] = $msg;
      return true;
    } else {
      // Then this is a "get" message
      return $_SESSION['message'] ?? '';
    }
  }

/**
 * Стирает сообщение в сессии
 * 
 */
  public function clear_message() {
    unset($_SESSION['message']);
  }

}


?>