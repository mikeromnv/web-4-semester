<html>
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

<div id="form-menu" class="form-menu">
        <form id="UserForm" class="user-form" action="index.php" method="POST">
            <label>ФИО <span class="error"><?php echo isset($errors['fio']) ? nl2br(htmlspecialchars($messages['fio'])) : ''; ?></span> <br/>
              <input type="text" 
              name="fio" 
              value="<?= $_COOKIE['fio_value'] ?? '' ?>" 
              class="<?= (!empty($errors['fio'])) ? 'if_error' : 'no_errors' ?>">
            </label><br/>
            

            <label>Телефон <span class="error"><?php echo isset($errors['phone']) ? nl2br(htmlspecialchars($messages['phone'])) : ''; ?></span><br>
                <input name="phone" 
                type="tel" 
                value="<?= $_COOKIE['phone_value'] ?? '' ?>" 
                class="<?= (!empty($errors['phone'])) ? 'if_error' : 'no_errors' ?>">
                <p class="notice">*используйте телефонный код +7</p>
                
            </label> <br>
            
            <label>Email <span class="error"><?php echo isset($errors['email']) ? nl2br(htmlspecialchars($messages['email'])) : ''; ?></span><br/>
                <input name="email" 
                type="email" 
                value="<?= $_COOKIE['email_value'] ?? '' ?>" 
                class="<?= (!empty($errors['email'])) ? 'if_error' : 'no_errors' ?>">
                
            </label><br />
            
            <label>Дата рождения <span class="error"><?php echo isset($errors['date']) ? nl2br(htmlspecialchars($messages['date'])) : ''; ?></span><br />
                    <input name="date"
                            value="<?= $_COOKIE['date_value'] ?? date('Y-m-d') ?>" 
                            class="<?= (!empty($errors['date'])) ? 'if_error' : 'no_errors' ?>"
                            type="date"/>
                    
            </label><br />


            <label>Пол <span class="error"><?php echo isset($errors['gender']) ? nl2br(htmlspecialchars($messages['gender'])) : ''; ?></span><br />
                <label><input 
                type="radio"
                name="gender" 
                value="Male" <?= (isset($_COOKIE['gender_value']) && $_COOKIE['gender_value'] === 'Male') ? 'checked' : ''; ?> />
                    Муж</label>
                <label><input 
                type="radio"
                name="gender" 
                value="Female" <?= (isset($_COOKIE['gender_value']) && $_COOKIE['gender_value'] === 'Female') ? 'checked' : ''; ?> />
                    Жен</label><br />
                
            </label>
            
            <label>Ваш любимый язык программирования <span class="error"><?php echo isset($errors['favorite_languages']) ? nl2br(htmlspecialchars($messages['favorite_languages'])) : ''; ?></span><br />
                <select 
                name="favorite_languages[]" 
                class="<?= (!empty($errors['favorite_languages'])) ? 'if_error' : 'no_errors' ?>"
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
                
            </label><br />

            <label><p><b>Ваша биография<span class="error"><?php echo isset($errors['biography']) ? nl2br(htmlspecialchars($messages['biography'])) : ''; ?>  </span></b></p>
                <p> <textarea 
                name="biography"  
                class="<?= (!empty($errors['biography'])) ? 'if_error' : 'no_errors' ?>">
                <?= $_COOKIE['biography_value'] ?? ''; ?></textarea> </p>
                
            </label>
            
            
          <label><input 
            type="checkbox"  
            name="contract" <?= !empty($_COOKIE['contract_value']) ? 'checked' : ''; ?> 
            class="<?= (!empty($errors['contract'])) ? 'if_error' : 'no_errors' ?>">
            С контрактом ознакомлен(a)
            
          </label> 
            <span class="error"><?php echo isset($errors['contract']) ? nl2br(htmlspecialchars($messages['contract'])) : ''; ?>  <br /></span>
            <input name="submit_button" type="submit" value="Сохранить">
        </form>
    </div>
  </body>
</html>
