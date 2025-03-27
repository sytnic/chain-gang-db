<?php

class ParseCSV {

    // можно вручную устанавливать здесь ограничитель,
    // используемый в csv 
    public static $delimiter = ',';

    private $filename;
    private $header;
    private $data=[];

    private $row_count = 0;

    function __construct($filename='') {
        if($filename != '') {
            // старая версия до улучшения
            //$this->filename = $filename;
            
            $this->file($filename);
        }
    }

    // проверяет существование и читаемость файла,
    // присваивает имя файла
    public function file($filename) {
        if(!file_exists($filename)) {
            echo "File does not exists.";
            return false;
        } elseif(!is_readable($filename)) {
            echo "File is not readable.";
            return false;
        } 
        $this->filename = $filename;
        return true;
    }

    public function parse() {
        // если файл не задан, false
        if(!isset($this->filename)) {
            echo "File not set.";
            return false;
        }

        // очистить любые предыдущие сохраненные результаты
        $this->reset();

        // значения по умолчанию для функции парсера,
        // здесь они не нужны:
        // $header = false; 
        // $data = [];

        $file = fopen($this->filename, 'r');
        while(!feof($file)) {
            $row = fgetcsv($file, 0, self::$delimiter);
            if($row == [NULL] || $row === FALSE) { continue; }
            
            // этот ход помогает верхнюю строку в csv использовать как заголовки:
            // если у меня пока не задан header, задай его,
            // затем иди по циклу до конца файла,
            // header уже задан, поэтому переходи к else и создавай строки            
            if(!$this->header) {
                $this->header = $row;
            } else {
                // $header и $row - это массивы,
                // $header используется как ключи,
                // $row используется как значения ключей.
                // https://www.php.net/manual/ru/function.array-combine.php
                // В итоге создаётся массив массивов.
                $this->data[] = array_combine($this->header, $row);

                // подсчитывает общее число получившихся строк
                $this->row_count++;
            }
        }
        fclose($file);

        return $this->data;
    }

    // отдельная возможность
    // получения последних полученных данных
    public function last_results() {
        return $this->data;
    }

    // возвращает общее число подсчитанных строк
    public function row_count() {
        return $this->row_count;
    }

    // стирает сохранённые данные из памяти переменных
    private function reset() {
        $this->header = NULL;
        $this->data = [];
        $this->row_count = 0;
    }

}

?>