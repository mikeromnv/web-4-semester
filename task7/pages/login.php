<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"  href="./styles/loginstyle.css">
    <title> LAB6 </title>
  </head>

  <body>

    <div id="form-menu" class="form-menu">
        <form action="" method="POST">
            <!-- <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"> -->
            <p>Логин</p>
            <input name="login" type="text" maxlength="50" pattern="[a-zA-Z0-9]+" title="Только буквы и цифры"/>
            <p>Пароль</p>
            <input name="pass" type="password" maxlength="50"/>
            <input id="button" type="submit" value="Войти" />
        </form>
    </div>

    <a class="admin_ref" href="admin.php">Войти как администратор</a>

  </body>
</html>
