<?php

global $databaseConnection;
$dbUser = 'u68604';
$dbPassword = '5411397';
$databaseConnection = new PDO('mysql:host=localhost;dbname=u68604', $dbUser, $dbPassword,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);


function getADMIN_ID(){
    global $databaseConnection;
    $adminIds= array();
    try{
        $stmt = $databaseConnection->prepare("SELECT user_id FROM login_users WHERE role = 'admin'");
        $stmt->execute();
        $adminIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    }
    catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    return $adminIds;
}

function getAdmin_log() {
    global $databaseConnection;
    try {
        $stmt = $databaseConnection->prepare("SELECT login FROM login_users WHERE role='admin'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
}

function language_stats(){
    global $databaseConnection;
    $languageRows = array();
    try {
        $statement = $databaseConnection->prepare("SELECT pl.name, COUNT(ul.user_id) AS stat 
                                FROM user_languages ul 
                                JOIN programming_languages pl ON ul.language_id = pl.id 
                                JOIN login_users ON ul.user_id = login_users.user_id
                                WHERE role != 'admin'
                                GROUP BY pl.name
                                ");
        $statement->execute();
        while($rowData = $statement->fetch(PDO::FETCH_OBJ)){
            $languageRows[] = "<tr><td>" . htmlspecialchars($rowData->name, ENT_QUOTES, 'UTF-8') . 
                  "</td><td>" . htmlspecialchars($rowData->stat, ENT_QUOTES, 'UTF-8') . "</td></tr>";
        }
    }
    catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    return $languageRows;
}

function users_table(){
    global $databaseConnection;
    $userRows = array();
    try{
        $mainStatement = $databaseConnection->prepare("SELECT login, user_id, role FROM login_users WHERE role='user' ORDER BY user_id DESC");
        $mainStatement->execute();
        $userLogin;
        $userId;
        while($userRow = $mainStatement->fetch(PDO::FETCH_OBJ)){
            $userLogin = $userRow->login;
            $userId = $userRow->user_id;
            $rowContent = "<tr><td>$userId</td><td>$userLogin</td>";

            $userDataStatement = $databaseConnection->prepare("SELECT full_name, phone, email, gender AS gen, bio AS bio, birth_date, contract_accepted FROM users WHERE id = ?");
            $userDataStatement->execute([$userId]);
            $userData = $userDataStatement->fetch(PDO::FETCH_ASSOC);
            foreach($userData as $fieldValue) {
                $rowContent .= "<td>" . htmlspecialchars($fieldValue, ENT_QUOTES, 'UTF-8') . "</td>";
            }

            $langQuery = "SELECT pl.name 
                    FROM programming_languages pl 
                    JOIN user_languages ul ON pl.id = ul.language_id 
                    WHERE ul.user_id = :uid";
            
            $langStatement = $databaseConnection->prepare($langQuery);
            $langStatement->bindValue(':uid', $userId, PDO::PARAM_INT);
            $langStatement->execute();
            $userLangs = $langStatement->fetchAll(PDO::FETCH_COLUMN, 0);
            $langsString = implode(", ", $userLangs);
            $rowContent .= "<td>$langsString</td>";

            $rowContent .= "<td class=\"buttons\">
                            <form action=\"admin.php\" method=\"POST\">
                                <input type=\"hidden\" name=\"del_by_uid\" value=\"" . htmlspecialchars($userId) . "\">
                                <input type=\"hidden\" name=\"csrf_token\" value=\"" . htmlspecialchars($_SESSION['csrf_token']) . "\">
                                <input class=\"delete_button\" type=\"submit\" value=\"УДАЛИТЬ\">
                            </form>";

            $rowContent .= "<br><div class=\"change_button\">
            <a href=\"index.php?uid=$userRow->user_id\">ИЗМЕНИТЬ</a>
            </div></td></tr>";

            $userRows[] = $rowContent;
        }
    } 
    catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    return $userRows;
}

function del_by_uid($userId){
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('HTTP/1.1 403 Forbidden');
        die('Недействительный CSRF-токен');
    }
    global $databaseConnection;
    try{
        $langDelete = $databaseConnection->prepare("DELETE FROM user_languages WHERE user_id=?");
        $userDelete = $databaseConnection->prepare("DELETE FROM users WHERE id=?");
        $loginDelete = $databaseConnection->prepare("DELETE FROM login_users WHERE user_id=?");
        $langDelete->execute([$userId]);
        $loginDelete->execute([$userId]);
        $userDelete->execute([$userId]);
      }
    catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
}

function getUID($userLogin){
    global $databaseConnection;
    $userId;
    try {
        $idStatement = $databaseConnection->prepare("SELECT user_id FROM login_users WHERE login=?");
        $idStatement->execute([$userLogin]);
        $userId = $idStatement->fetchColumn();
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    return $userId;
}

function getlogin($userId){
    global $databaseConnection;
    $userLogin;
    try {
        $loginStatement = $databaseConnection->prepare("SELECT login FROM login_users WHERE user_id=?");
        $loginStatement->execute([$userId]);
        $userLogin = $loginStatement->fetchColumn();
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    return $userLogin;
}

function UPDATE($userId, $fullName, $phoneNumber, $emailAddress, $birthDate, $genderType, $bioText, $contractStatus, $programmingLangs){
    global $databaseConnection;
    try {
        $updateStatement = $databaseConnection->prepare("UPDATE users SET full_name=?, phone=?, email=?, birth_date=?, gender=?, bio=?, contract_accepted=? WHERE id=?");
        $updateStatement->execute([$fullName, $phoneNumber, $emailAddress, $birthDate, $genderType, $bioText, $contractStatus, $userId ]);
    
        $deleteLangs = $databaseConnection->prepare("DELETE FROM user_languages WHERE user_id=?");
        $deleteLangs->execute([$userId]);

        $langSelect = $databaseConnection->prepare("SELECT id FROM programming_languages WHERE name = ?");

        $langInsert = $databaseConnection->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?,?)");
        foreach ($programmingLangs as $langName) {
            $langSelect->execute([$langName]);
            $langId = $langSelect->fetchColumn();
      
            if ($langId) {
                $langInsert->execute([$userId, $langId]);
            }
        }
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
}

function INSERT($newLogin, $hashedPassword){
    global $databaseConnection;
    try{
        $userInsert = $databaseConnection->prepare("INSERT INTO users (full_name, phone, email, birth_date, gender, bio, contract_accepted ) values (?, ?, ?, ?, ?, ?, ? )");
        $userInsert->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['biography'], isset($_POST["contract"]) ? 1 : 0]);
    } catch (PDOException $exception){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    $newId = $databaseConnection->lastInsertId();
    try{
        $langSelect = $databaseConnection->prepare("SELECT id FROM programming_languages WHERE name = ?");
        $langInsert = $databaseConnection->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?, ?)");
        $selectedLangs = $_POST['favorite_languages'] ?? [];
        foreach ($selectedLangs as $langName) {
          $langSelect->execute([$langName]);
          $langId = $langSelect->fetchColumn();
          
          if ($langId) {
            $langInsert->execute([$newId, $langId]);
          }
        }
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    try {
        $loginInsert = $databaseConnection->prepare("INSERT INTO login_users (login, password_hash, role, user_id ) VALUES (?, ?, ?, ?)");
        $loginInsert->execute([$newLogin, $hashedPassword, "user", $newId]);
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
}

function INSERTData($userLogin){
    global $databaseConnection;
    $userId = getUID($userLogin);
    $fieldValues = array();
    try{
        $userData = [];
        $dataStatement = $databaseConnection->prepare("SELECT full_name as fio, phone, email, bio as biography, gender, birth_date as date, contract_accepted as contract FROM users WHERE id = ?");
        $dataStatement->execute([$userId]);
        $userData = $dataStatement->fetch(PDO::FETCH_ASSOC);
        $fields = ['fio', 'phone', 'email', 'biography', 'date', 'gender', 'contract'];
        foreach($fields as $field) {
            $fieldValues[$field] = htmlspecialchars($userData[$field], ENT_QUOTES, 'UTF-8');
        }
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    $langQuery = "SELECT pl.name FROM programming_languages pl JOIN user_languages ul ON pl.id = ul.language_id WHERE ul.user_id = :uid;";
    try{
        $langStatement = $databaseConnection->prepare($langQuery);
        $langStatement->bindValue(':uid', $userId, PDO::PARAM_INT);
        $langStatement->execute();
        $userLangs = $langStatement->fetchAll(PDO::FETCH_COLUMN, 0);
        $langsString = implode(",", $userLangs);
        $fieldValues['favorite_languages'] = $langsString;
    } catch (PDOException $e){
        error_log('Database error in ' . __FUNCTION__ . ': ' . $e->getMessage());
        echo 'Произошла ошибка. Попробуйте позже.';
        exit();
    }
    return $fieldValues;
}
?>