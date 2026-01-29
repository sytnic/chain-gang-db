<?php

class Pagination {

    public $current_page;
    public $per_page;
    public $total_count;

    // установка свойств при инициализации объекта класса
    public function __construct($page=1, $per_page=20, $total_count=0) {
        $this->current_page = (int) $page;
        $this->per_page = (int) $per_page;
        $this->total_count = (int) $total_count;
    }

    /**
     * @return int Большое число, речь о записях в БД, а не о страницах в пагинации 
     */
    public function offset() {
        return $this->per_page * ($this->current_page - 1);
    }
    
    /**
     * @return int Количество страниц в пагинации
     */
    public function total_pages() {
        return ceil($this->total_count / $this->per_page);
    }
    
    /**
     * @return int|boolean Страница в пагинации или false
     */
    public function previous_page() {
        $prev = $this->current_page - 1;
        return ($prev > 0) ? $prev : false;
    }
    
    /**
     * @return int|boolean Страница в пагинации или false
     */
    public function next_page() {
        $next = $this->current_page + 1;
        return ($next <= $this->total_pages()) ? $next : false;
    }    

}

?>