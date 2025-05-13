<?php
declare(strict_types=1);
function getAllLangs(){
  global $databaseConnection;
    try{
      $all_languages=[];
      $data = $databaseConnection->query("SELECT name FROM programming_languages")->fetchAll();
      foreach ($data as $lang) {
        $lang_name = $lang['name'];
        $all_languages[$lang_name] = $lang_name;
      }
      return $all_languages;
    } catch (PDOException $e){
      error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
      echo 'Произошла ошибка. Попробуйте позже.';
      exit();
  }
}

function AdminLogin($login) {
    global $databaseConnection;
    $check = false;
    try{
      $stmt = $databaseConnection->prepare("SELECT login FROM login_users WHERE role = :role");
      $stmt->execute([':role' => 'admin']);
  
      while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        if($login == $row->login){
          $check=true;
        }
      }
    } 
    catch (PDOException $e){
      error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
      echo 'Произошла ошибка. Попробуйте позже.';
      exit();
  }
    return $check;
}

function makeCsrfToken() {
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  $token = null;

  if (empty($_SESSION['csrf_token']) || time() - $_SESSION['csrf_token_time'] > 1800) { // 30 мин
    if (function_exists('random_bytes')) {
      $token = bin2hex(random_bytes(32));
    } else {
      $token = md5(uniqid(rand(), true));
    }
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
  }

  return isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; // Проверка существования
}

/**
 * Валидирует CSRF-токен
 * 
 * @return bool Возвращает true если токен валиден, false если есть ошибка
 */
function validateCsrfToken() {
  $submittedToken = $_POST['csrf_token'] ?? null;
  if (empty($submittedToken)) {
      error_log('CSRF Error: Missing token in POST data');
      return false;
  }
  $storedToken = $_SESSION['csrf_token'] ?? null;
  $tokenCreationTime = $_SESSION['csrf_token_time'] ?? 0;
  
  if (empty($storedToken)) {
      error_log('CSRF Error: Missing token in session');
      return false;
  }
  if (!hash_equals($storedToken, $submittedToken)) {
      error_log('CSRF Error: Token mismatch');
      return false;
  }
  $tokenAge = time() - $tokenCreationTime;
  $maxTokenAge = 3600; // 1 час в секундах
  
  if ($tokenAge > $maxTokenAge) {
      error_log('CSRF Error: Token expired (age: ' . $tokenAge . 's)');
      return false;
  }
  unset($_SESSION['csrf_token']);
  unset($_SESSION['csrf_token_time']);

  return true;
}


function AdminPassword($login, $password) {
    global $databaseConnection;
    $passw;
    try{
      $stmt = $databaseConnection->prepare("SELECT password_hash FROM login_users WHERE login = ? AND role='admin' ");
      $stmt->execute([$login]);
      $passw = $stmt->fetchColumn();
      if($passw===false){
        return false;
      }
      return password_verify($password, $passw);
    } 
    catch (PDOException $e){
      error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
      echo 'Произошла ошибка. Попробуйте позже.';
      exit();
  }
}

function emailExists($email) {
  global $databaseConnection;
  $sql = "SELECT COUNT(*) FROM users WHERE email = :email"; 
  $stmt = $databaseConnection->prepare($sql);
  $stmt->bindValue(':email', $email, PDO::PARAM_STR);
  if ($stmt === false) {
    error_log("Ошибка подготовки запроса: " . $databaseConnection->errorInfo()[2]);
    return true; 
  }
  $stmt->bindValue(':email', $email, PDO::PARAM_STR);
  if (!$stmt->execute()) {
    error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
    return true; 
  }
  $count = $stmt->fetchColumn(); 
  $stmt->closeCursor();
  return $count > 0;
}

function isLogin($login) {
  global $databaseConnection;
  try {
      $stmt = $databaseConnection->prepare("SELECT COUNT(*) FROM login_users WHERE login = ? GROUP BY login");
      $stmt->execute([$login]);
      return (int) $stmt->fetchColumn();
  } catch (PDOException $e){
    error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
    echo 'Произошла ошибка. Попробуйте позже.';
    exit();
}
}

function isRightPassword($login, $password) {
  global $databaseConnection;
  try {
      $stmt = $databaseConnection->prepare("SELECT password_hash FROM login_users WHERE login = ? GROUP BY login");
      $stmt->execute([$login]);
      $hash_passw = $stmt->fetchColumn();

      return $hash_passw && password_verify($password, $hash_passw); // Проверяем хеш
  } catch (PDOException $e){
    error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
    echo 'Произошла ошибка. Попробуйте позже.';
    exit();
}
}

?>