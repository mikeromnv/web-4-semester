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