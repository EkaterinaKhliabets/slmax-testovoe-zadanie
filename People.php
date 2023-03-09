<?php

/*
Класс должен иметь поля:
Массив с id людей.

Класс должен иметь методы:
1. Конструктор ведет поиск id людей по всем полям БД (поддержка выражений больше, меньше, не равно);
2. Получение массива экземпляров класса 1 из массива с id людей полученного в конструкторе;
3. Удаление людей из БД с помощью экземпляров класса 1 в соответствии с массивом, полученным в конструкторе.

*/

if (class_exists("User")) {
    class People
    {
        private $people = [];

        public function __construct($conn)
        {
            $result = $this->getAllUserId($conn);

            while ($row = $result->fetch()) {
                $this->people[] = $row["id"];
            }

        }

        public function getPeople($conn)
        {
            $sql = "SELECT * FROM users
        WHERE id IN (" . implode(",", $this->people) . ")";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();

            $arrObj = [];

            foreach ($result as $item) {
                $user = new User($item['id'], $item['firstname'], $item['lastname'], $item['date_of_birth'], $item['sex'], $item['city'], $conn);

                $arrObj[] = $user;
            }

            return $arrObj;
        }

        public function getAllUserId($conn)
        {
            $sql = "SELECT * FROM users";
            $result = $conn->query($sql);

            return $result;
        }

        public function delAllUsers($conn)
        {
            $objUsers = $this->getPeople($conn);

            foreach ($objUsers as $objUser) {
                $objUser->delete($objUser->id, $conn);
            }
        }
    }
}
