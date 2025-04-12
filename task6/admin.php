<?php

require_once 'functions/Query.php';
require_once 'functions/MyFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $user_log=$_SERVER['PHP_AUTH_USER'];
  $user_pass=$_SERVER['PHP_AUTH_PW'];
  
  if (empty($_SERVER['PHP_AUTH_USER']) ||
      empty($_SERVER['PHP_AUTH_PW']) ||
      !AdminLogin($user_log) ||
      !AdminPassword($user_log, $user_pass)) {

    header('HTTP/1.1 401 Unanthorized');
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

  // Обработка выхода
  if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit();
  }

  if(!empty($_POST['del_by_uid']) && !empty($_SERVER['PHP_AUTH_USER'])){
    del_by_uid($_POST['del_by_uid']);
  } 

  header('Location: admin.php');
}
