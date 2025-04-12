<?php



function getAllLangs($db){
  global $databaseConnection;
    try{
      $all_languages=[];
      $data = $databaseConnection->query("SELECT name FROM programming_languages")->fetchAll();
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

function AdminLogin($login) {
    global $databaseConnection;
    $check = false;
    try{
      $stmt = $databaseConnection->prepare("SELECT login FROM login_users WHERE role='admin'");
      $stmt->execute();
  
      while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        if($login == $row->login){
          $check=true;
        }
      }
    } 
    catch (PDOException $e){
      print('Error : ' . $e->getMessage());
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
      print('Error : ' . $e->getMessage());
      return false;
    }
}

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

function isLogin($login) {
  global $databaseConnection;
  try {
      $stmt = $databaseConnection->prepare("SELECT COUNT(*) FROM login_users WHERE login = ? GROUP BY login");
      $stmt->execute([$login]);
      return (int) $stmt->fetchColumn();
  } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
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
  } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
  }
}

?>