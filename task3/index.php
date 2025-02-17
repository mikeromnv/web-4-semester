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
    //header('Location: success.html');
  }
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
    if (strlen($_POST['fio']) > 150) {
      print( "Ошибка: ФИО не должно превышать 150 символов.<br>");
      $errors = TRUE;
    }

  // Проверка на только буквы и пробелы (кириллица и латиница)
    elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $_POST['fio'])) {
        print("Ошибка: ФИО должно содержать только буквы и пробелы.<br>");
        $errors = TRUE;
    } 
}
// ТЕЛЕФОН
if (empty($_POST['phone']) || !preg_match('/^\+7\d{10}$/', $_POST['phone']) ) {
  print('Введите корректный номер телефона.<br/>');
  $errors = TRUE;
}
// EMAIL

if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
   print('Ошибка: Введите корректный email.<br/>');
   $errors = TRUE;
}

// ЯЗЫКИ ПРОГРАММИРОВАНИЯ
$allowed_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala", "Go"];
$fav_languages = $_POST["favorite_languages"] ?? []; // Массив, если multiple select
if (!is_array($fav_languages) || empty($fav_languages)) {
  print('Ошибка: Выберите хотя бы один язык программирования.<br/>');
  $errors = TRUE;
} else {
    foreach ($fav_languages as $lang) {
        if (!in_array($lang, $allowed_languages)) {
            print('Ошибка: Найден недопустимый язык ($lang).<br/>');
            $errors = TRUE;
        }
    }
}

// ДАТА РОЖДЕНИЯ

if (empty($_POST['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['date'])) {
    print("Ошибка: Введите корректную дату рождения в формате ГГГГ-ММ-ДД.<br>");
    $errors = TRUE;
}

// ПОЛ
if (empty($_POST['gender'])){
  print ('Зполните поле ПОЛ.<br/>');
  $errors = TRUE;
}
else{
  $allowed_genders = ["Male", "Female"];
  if (!in_array($_POST['gender'], $allowed_genders)) {
    print('Ошибка: Выбран недопустимый пол.<br/>');
    $errors = TRUE;
  }
}
// БИО
if (empty($_POST['biography'])) {
  print('Заполните биографию.<br/>');
  $errors = TRUE;
}
// С КОНТРАКТОМ ОЗНАКОМЛЕН
if (!isset($_POST["contract"])) {
  print('Ошибка: Вы должны подтвердить ознакомление с контрактом.<br/>');
  $errors = TRUE;
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
  $stmt = $db->prepare("INSERT INTO users (full_name, phone, email, gender, birth_date, bio, contract_accepted) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['gender'], $_POST['date'], $_POST['biography'], isset($_POST["contract"]) ? 1 : 0]);
} catch (PDOException $e) {
  print('Ошибка БД: ' . $e->getMessage());
  exit();
}

$user_id = $db->lastInsertId(); // ID последнего вставленного пользователя
try{
  $stmt = $db->prepare("SELECT id FROM programming_languages WHERE name = ?");
  $insert_stmt = $db->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?, ?)");
  
  foreach ($fav_languages as $language) {
      // Получаем ID языка программирования
      $stmt->execute([$language]);
      $language_id = $stmt->fetchColumn();
      
      if ($language_id) {
          // Связываем пользователя с языком
          $insert_stmt->execute([$user_id, $language_id]);
      }
  }
}
catch (PDOException $e) {
  print('Ошибка БД: ' . $e->getMessage());
  exit();
}

// header('Location: ?save=1');

// Перенаправление на страницу успешного сохранения
header('Location: success.html');
