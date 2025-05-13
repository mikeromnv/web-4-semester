<?php

header('Content-Type: text/html; charset=UTF-8');
require_once 'functions/Query.php';
require_once 'functions/MyFunctions.php';

// $user = 'u68604'; // Заменить на ваш логин uXXXXX
// $pass = '5411397'; // Заменить на пароль
// $db = new PDO('mysql:host=localhost;dbname=u68604', $user, $pass,
//   [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$all_languages=getAllLangs();

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
        $messages_log[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
          и паролем <strong>%s</strong> для изменения данных.',
          strip_tags($_COOKIE['login']),
          strip_tags($_COOKIE['password']));
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
      else if ($_COOKIE['fio_error']==3){
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
  
  //insert values
  if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) &&
  AdminLogin($_SERVER['PHP_AUTH_USER']) && AdminPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
    if(!empty($_GET['uid']))
    {
      $update_id = $_GET['uid'];
      $log=getlogin($update_id);
      $values=INSERTData($log);
      $values['uid']=$update_id;
    }
  }
  
  if (isset($_COOKIE[session_name()]) && session_start() &&!empty($_SESSION['login'])) { 
    $_SESSION['uid']=getUID($_SESSION['login']);

    $values=INSERTData($_SESSION['login']);

    $login_message='Успешно!'; //. $_SESSION['login'] . ", uid: ". $_SESSION['uid'];
    $messages[] = $login_message;
  }
  

  include('pages/formPage.php');
}
else {
  if (!validateCsrfToken()) {
    http_response_code(403); 
   
  }
    

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


    // Проверяем меняются ли ранее сохраненные данные или отправляются новые.

  if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) &&
  AdminLogin($_SERVER['PHP_AUTH_USER']) && AdminPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
  
    if(!empty($_POST['uid'])) {
      $user_id = $_POST['uid'];
      $languages = $_POST['favorite_languages'] ?? [];
      UPDATE($user_id, $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['biography'], isset($_POST["contract"]) ? 1 : 0, $languages);
      header('Location: admin.php');
      
      exit();
    } else{
      print('Пользователь для изменения не выбран');
    }
  
  } else {

    if (isset($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
      try {
        $user_id=getUID($_SESSION['login']);
        $languages = $_POST['favorite_languages'] ?? [];
        UPDATE($user_id, $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['biography'], isset($_POST["contract"]) ? 1 : 0, $languages);
      }
      catch(PDOException $e){
        error_log('Database error: ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
        // print('Error : ' . $e->getMessage());
        // exit();
      }
    } else {
      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

      $login = substr(md5(time()), 0, 16);
      while(isLogin($login, $db)){
        $login = substr(md5(time()), 0, 16);
      }
      $pass = substr(md5(uniqid()), 0, 8);
      $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
      
      setcookie('login', $login);
      setcookie('pass', $pass);
      
      try {
        INSERT($login, $pass_hash);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
    }

  }

  setcookie('save', '1');
  header('Location: ./');
}
