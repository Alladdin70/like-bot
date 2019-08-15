<?php
define('MAIN_MENU_OFFSET', 4000);
define('SCREEN_LIST_LENGTH',7);
define('OFFSET_CONTROL', 990);
require_once 'keyboard.php';
require_once 'vkapi.php';

/*
 * Класс для отбражения экрана пользователю. Получает список кнопок, смещение
 * (номер экрана) для отображения кнопок, смещение для кодировки кнопок и 
 * сообщение для пользователя
 * Надписи и коды кнопок формируются внутренними геттерами. 
 * Функция show создает клавиатуру и вызывает метод ВК, передавая в него id 
 * пользователя, сообщение, клавиатуру и вложения(по умолчанию пусто)
 */

class Screen{
    private $num;
    private $offset;
    private $list;
    private $labels;
    private $numbers;
    private $msg;

    public function __construct($offset, $list, $message, $num=1) {
        $this->num = $num;
        $this->list = $list;
        $this->offset = $offset;
        $this->msg = $message;
    }
    
    
    private function getLabels(){
        $offset = ($this->num - 1)*SCREEN_LIST_LENGTH;
        $this->labels = array_slice($this->list, $offset, SCREEN_LIST_LENGTH);
        switch(count($this->labels)):
            //Если комплект кнопок
            case SCREEN_LIST_LENGTH:
                //Убираем последний элемент
                $last = array_pop($this->labels);
                //Вместо него ставим кнопку навигации "Назад"
                array_push($this->labels, '<PREV');
                //Ставим последний элемент
                array_push($this->labels, $last);
                //Проверяем есть ли еще элементы в первоначальном массиве
                if(isset($this->list[$this->num * SCREEN_LIST_LENGTH])):
                    //Если есть, добавляем кнопку навигации "Вперед"
                    array_push($this->labels, 'NEXT>');
                endif;
                break;
            default:
                //Если элементов меньше семи, то ставим последней кнопку "Назад"
                array_push($this->labels, '<PREV');
                //Убираем кнопку "Назад" у главного меню
                if($this->offset == MAIN_MENU_OFFSET):
                   array_pop($this->labels); 
                endif;
                break;
        endswitch;
    }
    
    //Аналогично getLabels()
    private function getNumbers(){
        $numArray = self::getNumArray();
        $numOffset = ($this->num - 1)*SCREEN_LIST_LENGTH;
        $this->numbers = array_slice($numArray, $numOffset, SCREEN_LIST_LENGTH);
        switch(count($this->numbers)):
            case SCREEN_LIST_LENGTH:
                $last = array_pop($this->numbers);
                array_push($this->numbers, $this->offset + OFFSET_CONTROL + $this->num - 1);
                array_push($this->numbers, $last);
                if(isset($numArray[$this->num * SCREEN_LIST_LENGTH])):
                    array_push($this->numbers, $this->offset + OFFSET_CONTROL + $this->num + 1);
                endif;
                break;
            default:
                array_push($this->numbers, $this->offset + OFFSET_CONTROL + $this->num - 1);
                //Убираем кнопку "Назад" у главного меню
                if($this->offset == MAIN_MENU_OFFSET):
                   array_pop($this->numbers); 
                endif;
            break;
        endswitch;
    }
    
    //Массив с кодами равный по величине массиву с надписями
    //Код получается сложением смещения и итерируемой переменной
    private function getNumArray(){
        $numArray = array();
        for($i = 0; $i < count($this->list); $i ++):
            array_push($numArray, $i + 1 + $this->offset);
        endfor;
        return $numArray;
    }


    public function show($uid, $attachment = ''){
        //Получаем кнопки и коды
        self::getLabels();
        self::getNumbers();
        //Параметры для клавиатуры
        $param = new Param($this->labels, $this->numbers, $this->offset);
        $kb = new Keyboard();
        $kb->setParam($param->getParam());
        $keyboard = $kb->getKeyboard();
        //Отображаем экран в мессенждере ВК
        return messageSend($uid, $this->msg, $keyboard,$attachment);
    }
    
}


/*
 * Класс для формирования массива параметров param, который используется при
 * создании клавиатур.
 * Конструктор класса получает массивы надписей и кодов кнопок.
 * Сеттер для установки параметра one_time
 * Геттер массива param
 */

class Param{
    private $row;
    private $col =[];
    private $labels;
    private $numbers;
    private $one_time;
    private $offset;
    
    public function __construct($labels,$numbers,$offset) {
        $this->labels = $labels;
        $this->numbers = $numbers;
        $this->offset = $offset;
        $this->one_time = false;
    }
    
    public function setOneTime($oneTime){
        $this->one_time = $oneTime;
    }
/*
 * Функция для автоматического расчета колонок по строкам.
 * Количество строк отпределяется округлением в большую сторону
 * длины массива наклеек на три 
 */    
    private function getColAndRow(){
        $i = count($this->labels);
        $this->row = ceil($i / 3);
        while($i > 0):
            if($i >= 3):
                array_push($this->col, 3);
            else:
                array_push($this->col, $i);
            endif;
            $i = $i - 3;
        endwhile;
    }
    
    public function getParam(){
        self::getColAndRow();
        return array(
            'col' => $this->col,
            'row' => $this->row,
            'labels' => $this->labels,
            'numbers' => $this->numbers,
            'colors' => [],
            'offset' => $this->offset,
            'one_time' => $this->one_time
        );
    }
}