<?php
require_once 'User.php';
require_once 'People.php';

try {
    // БД
    $conn = new PDO("mysql:host=localhost", "root", "");

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE DATABASE IF NOT EXISTS slmax;
            USE slmax;
           CREATE TABLE IF NOT EXISTS users (id integer auto_increment primary key, firstname varchar(30), 
            lastname varchar(30), date_of_birth date, sex bool, city varchar(150));";

    $conn->query($sql);

    $Ivan = new User(1, 'Ivan', "Semenov", '1990-05-25', 0, "Minsk", $conn);
    $Anna = new User(2, 'Anna', "Petrova", '1995-08-03', 1, "Brest", $conn);
    $Petr = new User(3, 'Petr', "Sverdlov", '1988-09-11', 0, "Grodno", $conn);
    $Kate = new User(4, 'Kate', "Ivanova", '1991-09-11', 1, "Minsk", $conn);

    // удаление данных по id
    //$Kate->delete(4, $conn);

    // перевод даты в возраст
    $age = $Ivan::age('1990-05-25');
    echo '<br>' . 'Дата рождения: ' . '1990-05-25' . '. Возраст: ' . $age;

    // перевод пола из двоичной в текстовую
    $sex = 0;
    echo '<br>' . 'Пол цифрой: ' . $sex . '. Пол текстом: ' . ($Ivan::sexTransformation($sex));

    $Ivan->userFormatting();
    var_dump($Anna->userFormatting(true, true));


    $objPeople = new People($conn);

    // выведу всех юзеров
    echo '<br>' . "Вывод списка всех пользоватей для задания 2" . '<br>';
    $arrIdUsers = $objPeople->getPeople($conn);
    foreach ($arrIdUsers as $user) {
        echo $user->id . ' ' . $user->getFirstname() . ' ' . $user->getLastname() . '<br>';
    }

    // удаление всех юзеров
    //$objPeople->delAllUsers($conn);


} catch (PDOException $exception) {
    echo "Database error: " . $exception->getMessage();
}

