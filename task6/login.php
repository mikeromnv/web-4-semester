
<?php
header('Content-Type: text/html; charset=UTF-8');

require_once 'functions/Query.php';
require_once 'functions/MyFunctions.php';

$session_started = false;

if (isset($_COOKIE[session_name()]) && session_start()) {
  $session_started = true;
  if (!empty($_SESSION['login'])) {

    if(isset($_POST['logout'])){
      session_unset();
      session_destroy();
      header('Location: login.php');
      exit();
    }

    header('Location: ./');
    exit();
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  include('pages/login.php');
}
else {
  $login_messages='';
  $login = $_POST['login'];
  $password=$_POST['password'];

  if (!$session_started) {
    session_start();
  }

  if (isValid($login) && password_check($login, $password)){
    $_SESSION['login'] = $_POST['login'];
    $_SESSION['uid'] = getUID([$_SESSION['login']]);

      header('Location: ./');
  }
  else {
    echo "<div class=\"login_error_message\"> Неверный логин или пароль </div>"; 
  }

}

