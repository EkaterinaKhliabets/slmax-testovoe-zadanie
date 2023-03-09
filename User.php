<?php

/*
БД содержит поля:
id, имя(только буквы), фамилия(только буквы), дата рождения, пол(0,1), город рождения.

Класс должен иметь поля:
id, имя, фамилия, дата рождения, пол(0,1), город рождения.

Класс должен иметь методы:
1. Сохранение полей экземпляра класса в БД;
2. Удаление человека из БД в соответствии с id объекта;
3. static преобразование даты рождения в возраст (полных лет);
4. static преобразование пола из двоичной системы в текстовую (муж, жен);
5. Конструктор класса либо создает человека в БД с заданной информацией, либо берет информацию из БД по id
(предусмотреть валидацию данных);
6. Форматирование человека с преобразованием возраста и (или) пола (п.3 и п.4) в зависимости от параметров
(возвращает новый экземпляр stdClass со всеми полями изначального класса).
*/

class User
{
    public $id;
    private $firstname;
    private $lastname;
    private  $dateOfBirth;
    private  $sex;
    private $cityOfBirth;

    public function __construct($id, $firstname, $lastname, $dateOfBirth, $sex, $cityOfBirth, $conn)
    {
        $result = $this->getId($id, $conn);

        if ($result !== null) {

            $this->id = $result['id'];
            $this->firstname = $result['firstname'];
            $this->lastname = $result['lastname'];
            $this->dateOfBirth = $result['date_of_birth'];
            $this->sex = $result['sex'];
            $this->cityOfBirth = $result['city'];

        } else {
            $error = [];

            if (!preg_match('/^[A-Za-zА-яа-я]+$/u', $firstname)) {
                $error[] = 'Поле ИМЯ должно содержать только буквы';
            }

            if (!preg_match('/^[A-Za-zА-яа-я]+$/u', $lastname)) {
                $error[] = 'Поле ФАМИЛИЯ должно содержать только буквы';
            }

            if ($sex != 1 && $sex != 0) {
                $error[] = 'Поле ПОЛ должно содержать или 1 или 0';
            }

            if (!preg_match('/^(19|20)[0-99]{2}-([0][0-9]|[1][0-2])-([0-2][0-9]|[3][0-1])+$/u', $dateOfBirth)) {
                $error[] = 'Поле дата рождения должна быть в формате ГГГГ-ММ-ДД';
            }

            if (count($error) === 0) {
                $this->save($id, $firstname, $lastname, $dateOfBirth, $sex, $cityOfBirth, $conn);

                $this->firstname = $firstname;
                $this->lastname = $lastname;
                $this->dateOfBirth = $dateOfBirth;
                $this->sex = $sex;
                $this->cityOfBirth = $cityOfBirth;

            } else {
                echo 'User не создан!' . '<br>' . implode("<br>\r\n", $error);
            }
        }
    }

    public function getId($id, $conn)
    {
        $sql = "SELECT * FROM users WHERE id = :userid";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":userid", $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        } else
            return null;
    }

    public function save($id, $firstname, $lastname, $dateOfBirth, $sex, $cityOfBirth, $conn)
    {
        $result = $conn->prepare("INSERT users (firstname, lastname, date_of_birth,  sex, city) 
                            VALUES (:firstname, :lastname, :date_of_birth, :sex, :city)");

        $result->bindParam(":firstname", $firstname, PDO::PARAM_STR, 30);
        $result->bindParam(":lastname", $lastname, PDO::PARAM_STR, 30);
        $result->bindParam(":date_of_birth", $dateOfBirth, PDO::PARAM_STR, 30);
        $result->bindParam(":sex", $sex, PDO::PARAM_BOOL);
        $result->bindParam(":city", $cityOfBirth, PDO::PARAM_STR, 150);

        $result->execute();

    }

    public function delete($id, $conn)
    {
        $sql = "DELETE FROM users WHERE id = :userid";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":userid", $id);
        $stmt->execute();
    }

    public static function age($dateOfBirth)
    {
        $diff = date('Ymd') - date('Ymd', strtotime($dateOfBirth));

        return substr($diff, 0, -4);
    }

    public static function sexTransformation($sex_id)
    {
        if ($sex_id == 0)
            return 'муж';
        elseif ($sex_id == 1)
            return 'жен';
        else
            return 'Ошибка ввода данных!';
    }

    public function userFormatting($dateOfBirth = false, $sex = false)
    {
        $user =  new StdClass();
        $user->id = $this->id;
        $user->firstname = $this->firstname;
        $user->lastname = $this->lastname;

        if ($dateOfBirth)
            $user->age = self::age($this->dateOfBirth);
        else
            $user->age = $this->dateOfBirth;
        if ($sex)
            $user->sex = self::sexTransformation($this->sex);
        else
            $user->sex = $this->sex;
        $user->cityOfBirth = $this->cityOfBirth;

        return $user;
    }

    public function getFirstname(){
        return $this->firstname;
    }

    public function getLastname(){
        return $this->lastname;
    }
}