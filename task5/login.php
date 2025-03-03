<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

function isLogin($login, $db) {
  try {
      $stmt = $db->prepare("SELECT COUNT(*) FROM login_users WHERE login = ? GROUP BY login");
      $stmt->execute([$login]);
      return (int) $stmt->fetchColumn(); // Приводим к int
  } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
  }
}

function isRightPassword($login, $password, $db) {
  try {
      $stmt = $db->prepare("SELECT password_hash FROM login_users WHERE login = ? GROUP BY login");
      $stmt->execute([$login]);
      $hash_passw = $stmt->fetchColumn();

      return $hash_passw && password_verify($password, $hash_passw); // Проверяем хеш
  } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
  }
}


// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
$session_started = false;
if (isset($_COOKIE[session_name()]) && session_start()) {
  $session_started = true;
  if (!empty($_SESSION['login'])) {
    // Если есть логин в сессии, то пользователь уже авторизован.
    // TODO: Сделать выход (окончание сессии вызовом session_destroy()
    //при нажатии на кнопку Выход).
    // Делаем перенаправление на форму.
    header('Location: ./');
    exit();
  }
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>

<form action="" method="post">
  <input name="login" />
  <input name="pass" />
  <input type="submit" value="Войти" />
</form>

<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
  $login = $_POST['login'];
  $password = empty($_POST['password']) ? '' $_POST['password'];
  $user = 'u68604'; // Заменить на ваш логин uXXXXX
  $pass = '5411397'; // Заменить на пароль
  $db = new PDO('mysql:host=localhost;dbname=u68604', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX
  
  if (!$session_started) {
    session_start();
  }
  // Если все ок, то авторизуем пользователя.
  if (isLogin($login, $db) || isRightPassword($login, $password, $db)){
    $_SESSION['login'] = $_POST['login'];
    // Записываем ID пользователя.
    $_SESSION['uid'];
    try {
        $stmt_login = $db->prepare("SELECT id FROM login_users WHERE login=?");
        $stmt_login->execute([$_SESSION['login']]);
        $_SESSION['uid']  = $stmt_login->fetchColumn();
    } catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
     // Делаем перенаправление.
      header('Location: ./');
  }
  else{
    print('Неверный логин или пароль'); 
  }
}
