<?php
/**
 * Реализовать проверку заполнения обязательных полей формы в предыдущей
 * с использованием Cookies, а также заполнение формы по умолчанию ранее
 * введенными значениями.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');
function emailExists($email, $pdo) {

  $sql = "SELECT COUNT(*) FROM users WHERE email = :email"; 
  $stmt = $pdo->prepare($sql);
  if ($stmt === false) {
    error_log("Ошибка подготовки запроса: " . $pdo->errorInfo()[2]);
    return true; 
  }
  $stmt->bindValue(':email', $email, PDO::PARAM_STR);
  if (!$stmt->execute()) {
    error_log("Ошибка выполнения запроса: " . $stmt->errorInfo()[2]); 
    return true; 
  }
  // 4. Получение результата запроса.
  $count = $stmt->fetchColumn(); // Получаем сразу значение COUNT(*)
  $stmt->closeCursor();
  // 6. Возврат true, если email найден в базе, иначе false.
  return $count > 0;
}
// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
   $messages = array();

   // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
   // Выдаем сообщение об успешном сохранении.
   if (!empty($_COOKIE['save'])) {
       // Удаляем куку, указывая время устаревания в прошлом.
       setcookie('save', '', time() - 3600);
       // Если есть параметр save, то выводим сообщение пользователю.
       $messages['success'] = 'Спасибо, результаты сохранены.';
   }

   // Складываем признак ошибок в массив.
   $errors = array();
   $errors['fio'] = !empty($_COOKIE['fio_error']);
   $errors['phone'] = !empty($_COOKIE['phone_error']);
   $errors['email'] = !empty($_COOKIE['email_error']);
   $errors['date'] = !empty($_COOKIE['date_error']);
   $errors['gender'] = !empty($_COOKIE['gender_error']);
   $errors['favorite_languages'] = !empty($_COOKIE['favorite_languages_error']);
   $errors['biography'] = !empty($_COOKIE['biography_error']);
   $errors['contract'] = !empty($_COOKIE['contract_error']);

   // Массив для хранения текстов ошибок, которые будут выводиться рядом с полями.
   $messages = array(
      'fio' => '',
      'phone' => '',
      'email' => '',
      'date' => '',
      'gender' => '',
      'favorite_languages' => '',
      'biography' => '',
      'contract' => ''
  );

   if ($errors['fio']) {
       setcookie('fio_error', '', time() - 3600);
       setcookie('fio_value', '', time() - 3600);
       if ($_COOKIE['fio_error']==2){
        $messages['fio'] = 'ФИО должно быть короче 150 символов';
       }
       if ($_COOKIE['fio_error']==3){
        $messages['fio'] = 'ФИО может содержать только буквы и пробелы';
       }
       else{
        $messages['fio'] = 'Заполните имя.';
       }
       
   }

   if ($errors['phone']) {
       setcookie('phone_error', '', time() - 3600);
       setcookie('phone_value', '', time() - 3600);
       $messages['phone'] = 'Введите корректный номер телефона.';
   }

   if ($errors['email']) {
       setcookie('email_error', '', time() - 3600);
       setcookie('email_value', '', time() - 3600);
       if ($_COOKIE['email_error']==2){
        $messages['email'] = 'Такой email уже зарегистрирован.';
       }
       else{
        $messages['email'] = 'Введите корректный email.';
       }
   }

   if ($errors['date']) {
       setcookie('date_error', '', time() - 3600);
       setcookie('date_value', '', time() - 3600);
       $messages['date'] = 'Выберите дату рождения.';
   }

   if ($errors['gender']) {
       setcookie('gender_error', '', time() - 3600);
       setcookie('gender_value', '', time() - 3600);
       $messages['gender'] = 'Выберите пол.';
   }

   if ($errors['favorite_languages']) {
       setcookie('favorite_languages_error', '', time() - 3600);
       setcookie('favorite_languages_value', '', time() - 3600);
       $messages['favorite_languages'] = 'Выберите хотя бы один язык программирования.';
   }

   if ($errors['biography']) {
       setcookie('biography_error', '', time() - 3600);
       setcookie('biography_value', '', time() - 3600);
       
       if ($_COOKIE['biography_error']==2){
        $messages['biography'] = 'Недопустимые символы в биографии.';
       }
       else{
       
        $messages['biography'] = 'Заполните биографию.';
       }
   }

   if ($errors['contract']) {
       setcookie('contract_error', '', time() - 3600);
       setcookie('contract_value', '', time() - 3600);
       $messages['contract'] = 'Вы должны согласиться с условиями.';
   }

  // TODO: тут выдать сообщения об ошибках в других полях.

  // Складываем предыдущие значения полей в массив, если есть.
  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : trim($_COOKIE['fio_value']);
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : trim($_COOKIE['phone_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : trim($_COOKIE['email_value']);
  $values['date'] = empty($_COOKIE['date_value']) ? '' : trim($_COOKIE['date_value']);
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : trim($_COOKIE['gender_value']);
  $values['favorite_languages'] = empty($_COOKIE['favorite_languages_value']) ? '' : trim($_COOKIE['favorite_languages_value']);
  $values['biography'] = empty($_COOKIE['biography_value']) ? '' : trim($_COOKIE['biography_value']);
  $values['contract'] = empty($_COOKIE['contract_value']) ? '' : trim($_COOKIE['contract_value']);


  

  // TODO: аналогично все поля.

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  $user = 'u68604'; // Заменить на ваш логин uXXXXX
  $pass = '5411397'; // Заменить на пароль
  $db = new PDO('mysql:host=localhost;dbname=u68604', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX
  // Проверяем ошибки.
  $errors_validate = FALSE;
  if (empty($_POST['fio'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }
  else{
    // Проверка длины
      if (strlen($_POST['fio']) > 150) {
        setcookie('fio_error', '2', time() + 24 * 60 * 60);
        $errors_validate = TRUE;
      }
  
    // Проверка на только буквы и пробелы (кириллица и латиница)
      elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $_POST['fio'])) {
          setcookie('fio_error', '3', time() + 24 * 60 * 60);
          $errors_validate = TRUE;
      } 
  }
  // Сохраняем ранее введенное в форму значение на месяц.
  setcookie('fio_value', $_POST['fio'], time() + 365 * 24 * 60 * 60);
  // ТЕЛЕФОН
  if (empty($_POST['phone']) || !preg_match('/^\+7\d{10}$/', $_POST['phone']) ) {
    setcookie('phone_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }
  setcookie('phone_value', $_POST['phone'], time() + 365 * 24 * 60 * 60);
  // EMAIL
  $email=trim($_POST['email']);
  if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }
  setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);

  // ЯЗЫКИ ПРОГРАММИРОВАНИЯ
  $allowed_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala", "Go"];
  $fav_languages = $_POST["favorite_languages"] ?? []; // Получаем массив из формы

  if (!is_array($fav_languages) || empty($fav_languages)) {
      setcookie('favorite_languages_error', '1', time() + 24 * 60 * 60);
      $errors_validate = TRUE;
  } 
  else {
      foreach ($fav_languages as $lang) {
          if (!in_array($lang, $allowed_languages)) {
              setcookie('favorite_languages_error', '1', time() + 24 * 60 * 60);
              $errors_validate = TRUE;
          }
      }
  }
  setcookie('favorite_languages_value', implode(',', $fav_languages), time() + 365 * 24 * 60 * 60);

  // ДАТА РОЖДЕНИЯ
  if (empty($_POST['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['date'])) {
    setcookie('date_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }
  setcookie('date_value', $_POST['date'], time() + 365 * 24 * 60 * 60);
  // ПОЛ
  if (empty($_POST['gender'])){
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }
  else{
    $allowed_genders = ["Male", "Female"];
    if (!in_array($_POST['gender'], $allowed_genders)) {
      setcookie('gender_error', '1', time() + 24 * 60 * 60);
      $errors_validate = TRUE;
    }
  }
  setcookie('gender_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);
  // БИО
  if (empty($_POST['biography'])) {
    setcookie('biography_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }elseif (preg_match('/[<>{}\[\]]|<script|<\?php/i', $_POST['biography'])) {
    setcookie('biography_error', '2', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
}
  setcookie('biography_value', $_POST['biography'], time() + 365 * 24 * 60 * 60);
  // С КОНТРАКТОМ ОЗНАКОМЛЕН
  if (!isset($_POST["contract"])) {
    setcookie('contract_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }
  setcookie('contract_value', $_POST['contract'], time() + 365 * 24 * 60 * 60);
// *************
// TODO: тут необходимо проверить правильность заполнения всех остальных полей.
// Сохранить в Cookie признаки ошибок и значения полей.
// *************

  if ($errors_validate) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('fio_error', '', 100000);
    setcookie('phone_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('date_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('favorite_languages_error', '', 100000);
    setcookie('biography_error', '', 100000);
    setcookie('contract_error', '', 100000);
    // TODO: тут необходимо удалить остальные Cookies.
  }

  // Сохранение в БД.
  // ...
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
  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');
  // Делаем перенаправление.
  header('Location: index.php');
}
