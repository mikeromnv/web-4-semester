<!-- <?php
if(empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || !AdminLogin($user_log, $databaseConnection) ||
!AdminPassword($user_log, $user_pass, $databaseConnection)){
    header('Location: admin.php');
}
?> -->

<?php
if(empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || !AdminLogin($_SERVER['PHP_AUTH_USER']) ||
!AdminPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])){
    header('Location: admin.php');
}
?>

<!DOCTYPE html>
<html lang="ru">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"  href="./styles/table_style.css"> 
    <meta http-equiv="refresh"/>
    <title> ADMIN </title>
  </head>

    <form action="admin.php?logout=1" method="POST" >
        <input class="logout_button" type="submit" value="ВЫЙТИ">
    </form>

    
  <body>
    <table>
        <thead> 
            <tr>
                <td>ID</td><td>LOGIN</td><td>FIO</td><td>PHONE</td><td>EMAIL</td><td>GENDER</td><td>BIO</td><td>DATA</td><td>CONTRACT</td><td>LANGUAGES</td><td>ACTION</td>
            </tr>
        </thead> 

        <tbody>
            <?php
                foreach($user_table as $row){
                    echo $row;
                }
            ?>
        </tbody>
    </table>

    <table>
        <thead> 
            <tr><td>LANGUAGE</td><td>SUM</td></tr>
        </thead> 
        <tbody>
            <?php
                foreach($language_table as $row){
                    echo $row;
                }
            ?>
        </tbody>
    </table>
  </body>
</html>
