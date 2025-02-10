<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_GET['save'])) {
    // Если есть параметр save, то выводим сообщение пользователю.
    print('Спасибо, результаты сохранены.');
  }
  // Включаем содержимое файла form.php.
  //include('form.php');

  include('index.html');

  // Завершаем работу скрипта.
  exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в БД.

// Проверяем ошибки.

$errors = FALSE;

// ФИО
if (empty($_POST['fio'])) {
  print('Заполните имя.<br/>');
  $errors = TRUE;
}
else{
  // Проверка длины
    if (mb_strlen($fio) > 150) {
      echo "Ошибка: ФИО не должно превышать 150 символов.<br>";
      $errors = TRUE;
    }

  // Проверка на только буквы и пробелы (кириллица и латиница)
    elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $fio)) {
        echo "Ошибка: ФИО должно содержать только буквы и пробелы.<br>";
        $errors = TRUE;
    } 
}
// ТЕЛЕФОН
if (empty($_POST['phone']) || !preg_match('/^\+7\d{10}$/', $_POST['phone']) ) {
  print('Введите корректный номер телефона.<br/>');
  $errors = TRUE;
}
// EMAIL

if (!empty($_POST["email"]) !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
  $errors[] = "Ошибка: Введите корректный email.";
}

// ЯЗЫКИ ПРОГРАММИРОВАНИЯ
$allowed_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala", "Go"];
$fav_languages = $_POST["favorite_languages"] ?? []; // Массив, если multiple select
if (!is_array($fav_languages) || empty($fav_languages)) {
    $errors[] = "Ошибка: Выберите хотя бы один язык программирования.";
} else {
    foreach ($fav_languages as $lang) {
        if (!in_array($lang, $allowed_languages)) {
            $errors[] = "Ошибка: Найден недопустимый язык ($lang).";
        }
    }
}

// ДАТА РОЖДЕНИЯ
if (empty($_POST['year']) || !is_numeric($_POST['year']) || !preg_match('/^\d+$/', $_POST['year'])) {
  print('Заполните год.<br/>');
  $errors = TRUE;
}

// ПОЛ
if (empty($_POST['gender'])){
  print ('Зполните поле ПОЛ.<br/>');
  $errors = TRUE;
}
elseif{
  $allowed_genders = ["Муж", "Жен"];
  if (!in_array($_POST['gender'], $allowed_genders)) {
    print('Ошибка: Выбран недопустимый пол.<br/>');
  }
}
// БИО ???

// С КОНТРАКТОМ ОЗНАКОМЛЕН
if (!isset($_POST["contract"])) {
  print('Ошибка: Вы должны подтвердить ознакомление с контрактом.<br/>');
}

if ($errors) {
  // При наличии ошибок завершаем работу скрипта.
  exit();
}

// Сохранение в базу данных.

$user = 'u68604'; // Заменить на ваш логин uXXXXX
$pass = '5411397'; // Заменить на пароль
$db = new PDO('mysql:host=localhost;dbname=u68604', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX

// Подготовленный запрос. Не именованные метки.

try {
  $stmt = $db->prepare("INSERT INTO users (name, phone, email, gender, year, biography, contract) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$fio, $phone, $email, $gender, $year, $biography, $_POST["contract"]]);
} catch (PDOException $e) {
  print('Ошибка БД: ' . $e->getMessage());
  exit();
}



// try {
//   $stmt = $db->prepare("INSERT INTO users SET name = ?");
//   $stmt->execute([$_POST['fio']]);
// }
// catch(PDOException $e){
//   print('Error : ' . $e->getMessage());
//   exit();
// }

//  stmt - это "дескриптор состояния".
 
//  Именованные метки.
//$stmt = $db->prepare("INSERT INTO test (label,color) VALUES (:label,:color)");
//$stmt -> execute(['label'=>'perfect', 'color'=>'green']);
 
//Еще вариант
/*$stmt = $db->prepare("INSERT INTO users (firstname, lastname, email) VALUES (:firstname, :lastname, :email)");
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':email', $email);
$firstname = "John";
$lastname = "Smith";
$email = "john@test.com";
$stmt->execute();
*/

// Делаем перенаправление.
// Если запись не сохраняется, но ошибок не видно, то можно закомментировать эту строку чтобы увидеть ошибку.
// Если ошибок при этом не видно, то необходимо настроить параметр display_errors для PHP.
header('Location: ?save=1');
