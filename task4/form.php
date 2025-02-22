<html>
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

<?php
if (!empty($messages)) {
  print('<div id="messages">');
  // Выводим все сообщения.
  foreach ($messages as $message) {
    print($message);
  }
  print('</div>');
}

// Далее выводим форму отмечая элементы с ошибками классом error
// и задавая начальные значения элементов ранее сохраненными.
?>

<div id="form-menu" class="form-menu">
        <form id="UserForm" class="user-form" action="index.php" method="POST">
            <label>ФИО<br/>
              <input type="text" name="fio" value="<?= $_COOKIE['fio_value'] ?? '' ?>" class="<?= isset($errors['fio']) ? 'if_error' : '' ?>">
              <span class="error"><?php echo isset($errors['fio']) ? $messages['fio'] : ''; ?>
              </span>
            </label><br/>
            <label>Телефон: <br>
                <input name="phone" type="tel" value="<?= $_COOKIE['phone_value'] ?? '' ?>" class="<?= isset($errors['phone']) ? 'if_error' : '' ?>">
                <p class="notice">**используйте телефонный код +7</p>
                <span class="error"><?php echo isset($errors['phone']) ? $messages['phone'] : ''; ?>
            </label> <br>
            <label>Email:<br/>
                <input name="email" type="email" value="<?= $_COOKIE['email_value'] ?? '' ?>" class="<?= isset($errors['email']) ? 'if_error' : '' ?>">
                <span class="error"><?php echo isset($errors['email']) ? $messages['email'] : ''; ?>
            </label><br />

            <label>Дата рождения:<br />
                    <input name="date"
                            value="<?= $_COOKIE['date_value'] ?? date('Y-m-d') ?>" class="<?= isset($errors['date']) ? 'if_error' : '' ?>"
                            type="date"/>
                    <span class="error"><?php echo isset($errors['date']) ? $messages['date'] : ''; ?>
            </label><br />


            <label>Пол:<br />
                <label><input type="radio"
                name="gender" value="Male" <?= (isset($_COOKIE['gender_value']) && $_COOKIE['gender_value'] === 'Male') ? 'checked' : ''; ?> />
                    Муж</label>
                <label><input type="radio"
                name="gender" value="Female" <?= (isset($_COOKIE['gender_value']) && $_COOKIE['gender_value'] === 'Female') ? 'checked' : ''; ?> />
                    Жен</label><br />
                <span class="error"><?php echo isset($errors['gender']) ? $messages['gender'] : ''; ?>
            </label>
            
            <label>Ваш любимый язык программирования:<br />
                <select name="favorite_languages[]" class="<?= isset($errors['favorite_languages']) ? 'if_error' : '' ?>"
                multiple="multiple">
                    <?php
                    $languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala"];
                    $selected_languages = isset($_COOKIE['favorite_languages_value']) ? explode(',', $_COOKIE['favorite_languages_value']) : [];
                    
                    foreach ($languages as $lang) {
                        $selected = in_array($lang, $selected_languages) ? 'selected' : '';
                        echo "<option value=\"$lang\" $selected>$lang</option>";
                    }
                    ?>
                </select>
                <span class="error"><?php echo isset($errors['favorite_languages']) ? $messages['favorite_languages'] : ''; ?>
            </label><br />

            <label><p><b>Ваша биография:</b></p>
                <p> <textarea name="biography"  class="<?= isset($errors['biography']) ? 'if_error' : '' ?>"><?= $_COOKIE['biography_value'] ?? ''; ?></textarea> </p>
                <span class="error"><?php echo isset($errors['biography']) ? $messages['biography'] : ''; ?>  
            </label>
            
            
          <label><input type="checkbox"  
            name="contract" <?= !empty($_COOKIE['contract_value']) ? 'checked' : ''; ?> class="<?= isset($errors['contract']) ? 'if_error' : '' ?>" />
            С контрактом ознакомлен(a)
            <span class="error"><?php echo isset($errors['contract']) ? $messages['contract'] : ''; ?>  <br />
          </label> 
            
            <input name="submit_button" type="submit" value="Сохранить">
        </form>
    </div>
  </body>
</html>
