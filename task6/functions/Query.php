<?php

global $db;
$user = 'u68604'; // Заменить на ваш логин uXXXXX
$pass = '5411397'; // Заменить на пароль
$db = new PDO('mysql:host=localhost;dbname=u68604', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

function language_stats(){
    global $db;
    $rows = array();
    try {
        $stmt = $db->prepare("SELECT pl.name, COUNT(ul.user_id) AS stat 
                                FROM user_languages ul 
                                JOIN programming_languages pl ON ul.language_id = pl.id 
                                GROUP BY pl.name
                                ");
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
            $rows[] = "<tr><td>$row->name</td><td>$row->stat</td></tr>";
        }
    }
    catch (PDOException $e){
        print('ERROR : ' . $e->getMessage());
        exit();
    }
    return $rows;
}

function users_table(){
    global $db;
    $rows = array();
    try{
        $stmt = $db->prepare("SELECT login, user_id, role FROM login_users WHERE role='user' ORDER BY user_id DESC");
        $stmt->execute();
        $log;
        $uid;
        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
            $log=$row->login;
            $uid=$row->user_id;
            $r = "<tr><td>$uid</td><td>$log</td>";

            $form_data = $db->prepare("SELECT full_name, phone, email, gender AS gen, bio AS bio, birth_date, contract_accepted FROM users WHERE id = ?");
            $form_data->execute([$uid]);
            $mas = $form_data->fetch(PDO::FETCH_ASSOC);
            foreach($mas as $field) {
                $r.="<td>$field</td>";
            }

            $sql = "SELECT pl.name 
                    FROM programming_languages pl 
                    JOIN user_languages ul ON pl.id = ul.language_id 
                    WHERE ul.user_id = :uid";
            
            $stmt_lang = $db->prepare($sql);
            $stmt_lang->bindValue(':uid', $uid, PDO::PARAM_INT);
            $stmt_lang->execute();
            $lang = $stmt_lang->fetchAll(PDO::FETCH_COLUMN, 0);
            $langs_value1 =(implode(", ", $lang));
            $r.="<td>$langs_value1</td>";

            $r.="<td class=\"buttons\">
            <form action=\"admin.php\" method=\"POST\">
            <input type=\"hidden\" name=\"del_by_uid\" value=\"$uid\">
            <input class=\"delete_button\" type=\"submit\" value=\"удалить\">
            </form>";

            $r.="<br><div class=\"change_button\">
            <a href=\"index.php?uid=$row->user_id\">Изменить</a>
            </div></td></tr>";

            $rows[]=$r;
        }
    } 
    catch (PDOException $e){
        print('ERROR : ' . $e->getMessage());
        exit();
    }
    return $rows;
}

function del_by_uid($uid){
    global $db;
    try{
        $stmt_delete_lang = $db->prepare("DELETE FROM user_languages WHERE user_id=?");
        $stmt_delete_application = $db->prepare("DELETE FROM users WHERE id=?");
        $stmt_delete_user = $db->prepare("DELETE FROM login_users WHERE user_id=?");
        $stmt_delete_lang->execute([$uid]);
        $stmt_delete_user->execute([$uid]);
        $stmt_delete_application->execute([$uid]);
      }
    catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
}

function getUID($login){
    global $db;
    $uid;
    try {
        $stmt_select = $db->prepare("SELECT user_id FROM login_users WHERE login=?");
        $stmt_select->execute([$login]);
        $uid = $stmt_select->fetchColumn();
    } catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $uid;
}
function getlogin($uid){
    global $db;
    $login;
    try {
        $stmt_select = $db->prepare("SELECT login FROM login_users WHERE user_id=?");
        $stmt_select->execute([$uid]);
        $login = $stmt_select->fetchColumn();
    } catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $login;
}

function UPDATE($id, $full_name, $phone, $email, $birth_date, $gender, $bio, $contract_accepted, $languages){
    global $db;
    try {
        $stmt_update = $db->prepare("UPDATE users SET full_name=?, phone=?, email=?, birth_date=?, gender=?, bio=?, contract_accepted=? WHERE id=?");
        $stmt_update->execute([$full_name, $phone, $email, $birth_date, $gender, $bio, $contract_accepted, $id ]);
    
        $stmt_delete = $db->prepare("DELETE FROM user_languages WHERE user_id=?");
        $stmt_delete -> execute([$id]);

        $stmt_select = $db->prepare("SELECT id FROM programming_languages WHERE name = ?");

        $stmt_lang_update = $db->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?,?)");
        foreach ($languages as $language) {
            $stmt_select ->execute([$language]);
            $id_lang = $stmt_select->fetchColumn();
      
            if ($id_lang) {
                $stmt_lang_update->execute([$user_id, $id_lang]);
            }
        }
    } catch (PDOException $e){
        print('update Error : ' . $e->getMessage());
        exit();
    }
}

function INSERT($login, $hash_password){
    global $db;
    try{
        $stmt = $db->prepare("INSERT INTO users (full_name, phone, email, birth_date, gender, bio, contract_accepted ) values (?, ?, ?, ?, ?, ?, ? )");
        $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['biography'], isset($_POST["contract"]) ? 1 : 0]);
    } catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
    $id=$db->lastInsertId();
    try{
        $stmt_select = $db->prepare("SELECT id FROM programming_languages WHERE name = ?");
        $stmt_insert = $db->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?, ?)");
        $languages = $_POST['languages'] ?? [];
        foreach ($languages as $language) {
          $stmt_select ->execute([$language]);
          $id_lang = $stmt_select->fetchColumn();
          
          if ($id_lang) {
            $stmt_insert->execute([$id, $id_lang]);
          }
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    try {
        $stmt_insert = $db->prepare("INSERT INTO login_users (login, password_hash, role, user_id ) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute([ $login, $hash_password, "user", $id]);
    } catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
}

function INSERTData($login){
    global $db;
    $uid=getUID($login);
    $values = array();
    try{
        $mas=[];
        $stmt = $db->prepare("SELECT full_name, phone, email, bio, gender, birth_date, contract_accepted FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $mas = $stmt->fetch(PDO::FETCH_ASSOC);
        $fields = ['full_name', 'phone', 'email', 'bio', 'birth_date', 'gender', 'contract_accepted'];
        foreach($fields as $field) {
            $values[$field] = strip_tags($mas[$field]);
        }
    } catch (PDOException $e){
        print('ERROR : ' . $e->getMessage());
        exit();
    }
    $sql = "SELECT pl.name FROM programming_languages pl JOIN user_languages ul ON pl.id = ul.language_id WHERE ul.user_id = :uid;";
    try{
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        $lang = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $langs_value1 =(implode(",", $lang));
        $values['lang']=$langs_value1;
    } catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $values;
}
?>