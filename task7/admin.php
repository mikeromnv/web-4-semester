<?php

require_once 'functions/Query.php';
require_once 'functions/MyFunctions.php';

if (isset($_GET['logout'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('WWW-Authenticate: Basic realm="Admin Area"');
  echo '<h1>Вы вышли из панели администратора. Нажмите отмена в окне авторизации или закройте вкладку.</h1>';
  exit();
}

// Инициализация сессии
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Генерация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  
  $user_log=$_SERVER['PHP_AUTH_USER'];
  $user_pass=$_SERVER['PHP_AUTH_PW'];
  
  if (empty($_SERVER['PHP_AUTH_USER']) ||
      empty($_SERVER['PHP_AUTH_PW']) ||
      !AdminLogin($user_log) ||
      !AdminPassword($user_log, $user_pass)) {

    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация. Доступ запрещен.</h1>');
    exit();
  }

  print('<div class="success_admin">Вы успешно авторизовались и видите защищенные паролем данные.</div>');

  $language_table = language_stats();
  $user_table = users_table();

  include('pages/table.php');
}
else {

  // Проверка CSRF-токена
  if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Недействительный CSRF-токен');
  }

  if(!empty($_POST['del_by_uid']) && !empty($_SERVER['PHP_AUTH_USER'])){
    del_by_uid($_POST['del_by_uid']);
  } 
  elseif(!empty($_POST['uid']) && !empty($_SERVER['PHP_AUTH_USER'])){
    $user_id = $_POST['uid'];
    $languages = $_POST['favorite_languages'] ?? [];
    UPDATE(
        $user_id, 
        $_POST['fio'], 
        $_POST['phone'], 
        $_POST['email'], 
        $_POST['date'], 
        $_POST['gender'], 
        $_POST['biography'], 
        isset($_POST["contract"]) ? 1 : 0, 
        $languages
    );
}



  header('Location: admin.php');
}
