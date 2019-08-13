<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('DB_CONNECTION_STRING',"mysql:host=127.0.0.1;dbname=vkassist;charset=utf8");
define('DB_PASSWORD','4603917q'); //O7vINQpyW07ctxcd
define ('DB_USER','admin'); //db;

class DB{
    public $sql; //Строка - запрос
    public $data = array(); //Массив - плейсхолдеры
    
    //Получение одного значения(объекта)
    public function getOne(){
        $pdo = new PDO(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
        try{
            $stm = $pdo->prepare($this->sql);
            $stm->execute($this->data);
            return $stm->fetch(PDO::FETCH_OBJ);
        }
        catch(PDOException $e){
            file_put_contents('PDOErrors.txt', $e->getMessage(),FILE_APPEND);
            return;
        }
    }
    
    //Получение массива значений(объектов)
    public function getArray(){
        $response = array();
        $pdo = new PDO(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
        try{
            $stm = $pdo->prepare($this->sql);
            $stm->execute($this->data);
            while($row = $stm->fetch(PDO::FETCH_OBJ)):
                array_push($response,$row);
            endwhile;
            return $response;
        }
        catch(PDOException $e){
            file_put_contents('PDOErrors.txt', $e->getMessage(),FILE_APPEND);
            return;
        }
    }
    
    //Выполнение команды типа(INSERT INTO, CREATE TABLE и.т.п.)
    public function execRequest(){
        $pdo = new PDO(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
        try{
            $stm = $pdo->prepare($this->sql);
            $stm->execute($this->data);
        }
        catch(PDOException $e){
            file_put_contents('PDOErrors.txt', $e->getMessage(),FILE_APPEND);
        }  
        return;
    }
}


//Запросы SQL каждая функция возвращает строку запроса.
class Requests{
    //Список стран
    public function getCountries(){
        return 'SELECT country_rus FROM countries WHERE id IN(SELECT country_id'
        . ' FROM excursions) ORDER BY country_rus;';
    }
    
    //Список городов выбранной страны
    public function getCities(){
        return 'SELECT city_rus FROM cities WHERE country_id IN(SELECT id FROM'
        . ' countries WHERE country_rus=?)AND id IN(SELECT city_id FROM'
        . ' excursions) ORDER BY city_rus;';
    }
    
    //Заголовки экскурсий
    public function getExcursions(){
        return 'SELECT title,description,url,netto_price FROM excursions WHERE'
        . ' city_id IN (SELECT id FROM cities WHERE city_rus=?);';
    }
    
    //Список городов общий
    public function getCitiesAll(){
        return 'SELECT city_rus FROM cities;';
    }
    
    //Список городов -отправлений
    public function getDeptList(){
        return 'SELECT dept FROM hotoffers;';
    }
    
    //Список стран-направлений
    public function getCountriesList(){
        return 'SELECT country FROM hotoffers WHERE dept=?;';
    }
    
    //Список предложений
    public function getOffers(){
        return 'SELECT * FROM hotoffers WHERE dept=? AND country=?;';
    }
}

