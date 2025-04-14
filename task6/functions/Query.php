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
    catch (PDOException $exception){
        print('ERROR : ' . $exception->getMessage());
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
    } catch (PDOException $e) {
        error_log('Error in getAdmin_log: ' . $e->getMessage());
        return [];
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
            $languageRows[] = "<tr><td>$rowData->name</td><td>$rowData->stat</td></tr>";
        }
    }
    catch (PDOException $exception){
        print('ERROR : ' . $exception->getMessage());
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
                $rowContent .= "<td>$fieldValue</td>";
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
            <input type=\"hidden\" name=\"del_by_uid\" value=\"$userId\">
            <input class=\"delete_button\" type=\"submit\" value=\"УДАЛИТЬ\">
            </form>";

            $rowContent .= "<br><div class=\"change_button\">
            <a href=\"index.php?uid=$userRow->user_id\">ИЗМЕНИТЬ</a>
            </div></td></tr>";

            $userRows[] = $rowContent;
        }
    } 
    catch (PDOException $exception){
        print('ERROR : ' . $exception->getMessage());
        exit();
    }
    return $userRows;
}

function del_by_uid($userId){
    global $databaseConnection;
    try{
        $langDelete = $databaseConnection->prepare("DELETE FROM user_languages WHERE user_id=?");
        $userDelete = $databaseConnection->prepare("DELETE FROM users WHERE id=?");
        $loginDelete = $databaseConnection->prepare("DELETE FROM login_users WHERE user_id=?");
        $langDelete->execute([$userId]);
        $loginDelete->execute([$userId]);
        $userDelete->execute([$userId]);
      }
    catch(PDOException $exception){
        print('Error : ' . $exception->getMessage());
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
    } catch (PDOException $exception){
        print('Error : ' . $exception->getMessage());
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
    } catch (PDOException $exception){
        print('Error : ' . $exception->getMessage());
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
    } catch (PDOException $exception){
        print('update Error : ' . $exception->getMessage());
        exit();
    }
}

function INSERT($newLogin, $hashedPassword){
    global $databaseConnection;
    try{
        $userInsert = $databaseConnection->prepare("INSERT INTO users (full_name, phone, email, birth_date, gender, bio, contract_accepted ) values (?, ?, ?, ?, ?, ?, ? )");
        $userInsert->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['biography'], isset($_POST["contract"]) ? 1 : 0]);
    } catch (PDOException $exception){
        print('Error : ' . $exception->getMessage());
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
    } catch (PDOException $exception) {
        print('Error : ' . $exception->getMessage());
        exit();
    }
    try {
        $loginInsert = $databaseConnection->prepare("INSERT INTO login_users (login, password_hash, role, user_id ) VALUES (?, ?, ?, ?)");
        $loginInsert->execute([$newLogin, $hashedPassword, "user", $newId]);
    } catch (PDOException $exception){
        print('Error : ' . $exception->getMessage());
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
            $fieldValues[$field] = strip_tags($userData[$field]);
        }
    } catch (PDOException $exception){
        print('ERROR : ' . $exception->getMessage());
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
    } catch(PDOException $exception){
        print('Error : ' . $exception->getMessage());
        exit();
    }
    return $fieldValues;
}
?>