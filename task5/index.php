<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

$user = 'u68604'; // Заменить на ваш логин uXXXXX
$pass = '5411397'; // Заменить на пароль
$db = new PDO('mysql:host=localhost;dbname=u68604', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX
  

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
  $count = $stmt->fetchColumn(); 
  $stmt->closeCursor();
  return $count > 0;
}
function isLogin($login, $db) {
  try {
      $stmt = $db->prepare("SELECT COUNT(*) FROM login_users WHERE login = ? GROUP BY login");
      $stmt->execute([$login]);
      return (int) $stmt->fetchColumn();
  } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
  }
}

function getAllLangs($db){
    try{
      $all_languages=[];
      $data = $db->query("SELECT name FROM programming_languages")->fetchAll();
      foreach ($data as $lang) {
        $lang_name = $lang['name'];
        $all_languages[$lang_name] = $lang_name;
      }
      return $all_languages;
    } catch(PDOException $e){
      print('Error: ' . $e->getMessage());
      exit();
    }
}
$all_languages=getAllLangs($db);

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages_log = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Выводим сообщение пользователю.
    $messages_log[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
        $messages_log[] = sprintf('Вы можете <a href="login.php"> войти </a> с логином: <strong>%s</strong>
        и паролем: <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
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
     $messages['fio'] = 'ФИО может содержать только только буквы и пробелы';
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
      if ($_COOKIE['email_error'] == 2){
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
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : strip_tags($_COOKIE['phone_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['date'] = empty($_COOKIE['date_value']) ? '' : strip_tags($_COOKIE['date_value']);
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
  $values['favorite_languages'] = empty($_COOKIE['favorite_languages_value']) ? '' : strip_tags($_COOKIE['favorite_languages_value']);
  $values['biography'] = empty($_COOKIE['biography_value']) ? '' : strip_tags($_COOKIE['biography_value']);
  $values['contract'] = empty($_COOKIE['contract_value']) ? '' : strip_tags($_COOKIE['contract_value']);


  

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if ( !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
        try {
            $stmt_select = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt_select->execute([$_SESSION['uid']]); // Берем ID пользователя из сессии
            $log_user = $stmt_select->fetch(PDO::FETCH_OBJ);
        
            if ($log_user) {
                $values = [
                    'fio' => strip_tags($log_user->full_name),
                    'phone' => strip_tags($log_user->phone),
                    'email' => strip_tags($log_user->email),
                    'date' => strip_tags($log_user->birth_date),
                    'gender' => strip_tags($log_user->gender),
                    'biography' => strip_tags($log_user->bio),
                    'contract' => $log_user->contract_accepted
                ];
            }
            // Запрос на получение языков программирования
            $stmt_lang = $db->prepare("
                SELECT pl.name 
                FROM programming_languages pl
                JOIN user_languages ul ON pl.id = ul.language_id
                WHERE ul.user_id = ?
            ");
            $stmt_lang->execute([$_SESSION['uid']]);
            $favorite_languages = $stmt_lang->fetchAll(PDO::FETCH_COLUMN); // Получаем список языков в виде массива строк
        
            $values['favorite_languages'] = implode(',', $favorite_languages);
          } 
          catch (PDOException $e) {
              print('Ошибка БД: ' . $e->getMessage());
              exit();
          }
        
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    // Для загрузки данных из БД делаем запрос SELECT и вызываем метод PDO fetchArray(), fetchObject() или fetchAll() 
    // См. https://www.php.net/manual/en/pdostatement.fetchall.php

    //printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
    $msg = 'Вход с логином: '. $_SESSION['login'] . ", uid: ". $_SESSION['uid'];
    $messages_log[] = $msg;
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в базе данных.
else {
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
  if (emailExists($email, $db) && session_start())  { 
    $current_id;
    try{
          $dp=$db->prepare("SELECT id from users where email=?");
          $dp->execute([$email]);
          $current_id = $dp->fetchColumn();
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }

    if($current_id!==$_SESSION['uid']) {
      setcookie('email_error', '2', time() + 24 * 60 * 60);
      $errors_validate = TRUE;
    }     
  }
  elseif (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors_validate = TRUE;
  }
  setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);

  // ЯЗЫКИ ПРОГРАММИРОВАНИЯ
 
  $fav_languages = $_POST["favorite_languages"] ?? []; // Получаем массив из формы

  if (!is_array($fav_languages) || empty($fav_languages)) {
      setcookie('favorite_languages_error', '1', time() + 24 * 60 * 60);
      $errors_validate = TRUE;
  } 
  else {
      foreach ($fav_languages as $lang) {
          if (!in_array($lang, $all_languages)) {
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
  }

    // Проверяем, авторизован ли пользователь
    if (!empty($_COOKIE[session_name()]) &&
    session_start() && !empty($_SESSION['login'])) {

          try {
              // Обновляем данные
              $stmt = $db->prepare("UPDATE users 
                  SET full_name=?, phone=?, email=?, birth_date=?, gender=?, bio=?, contract_accepted=?
                  WHERE id = (SELECT user_id FROM login_users WHERE login=?)");
              
              $stmt->execute([
                  $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['date'], $_POST['gender'],
                  $_POST['biography'], isset($_POST["contract"]) ? 1 : 0, $_SESSION['login']
              ]);

              // Удаляем старые языки
              $stmt_delete = $db->prepare("DELETE FROM user_languages 
                  WHERE user_id = (SELECT user_id FROM login_users WHERE login=?)");
              $stmt_delete->execute([$_SESSION['login']]);

              // новые языки
              if (!empty($_POST['favorite_languages']) && is_array($_POST['favorite_languages'])) {
                  $insert_stmt = $db->prepare("INSERT INTO user_languages (user_id, language_id) 
                      VALUES ((SELECT user_id FROM login_users WHERE login=?), ?)");
                  
                  $stmt = $db->prepare("SELECT id FROM programming_languages WHERE name = ?");
                
                  
                  foreach ($fav_languages as $language) {
                      // Получаем ID языка программирования
                      $stmt->execute([$language]);
                      $language_id = $stmt->fetchColumn();
                      
                      if ($language_id) {
                          // Связываем пользователя с языком
                          $insert_stmt->execute([$_SESSION['login'], $language_id]);
                      }

              }
            }

          } catch (PDOException $e) {
              print('Ошибка БД: ' . $e->getMessage());
              exit();
          }

    }

  else {
    // Генерируем уникальный логин и пароль
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $login = substr(md5(time()), 0, 16);
    while(isLogin($login, $db)){
      $login = substr(md5(time()), 0, 16);
    }
    $pass = substr(md5(uniqid()), 0, 8);
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);

    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);

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
    try{
      $stmt_insert = $db->prepare("INSERT INTO login_users (user_id, login, password_hash, role ) VALUES (?, ?, ?, ?)");
      $stmt_insert->execute([$user_id, $login, $pass_hash, "user" ]);
    } 
    catch (PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
  }
  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
