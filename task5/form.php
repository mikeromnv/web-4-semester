<html>
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

  <?php 
      if(isset($_COOKIE[session_name()]) && !empty($_SESSION['login'])){
        print('<form class="logout_button" action="login.php" method="POST">
        <input type="submit" name="logout" value="Выйти"/> 
        </form>');
      }
  ?>

  <div <?php if (empty($messages_log)) {print 'class="no_msgs"';} else {print 'class="error_messages" ';} ?>>
      <?php
      if (!empty($messages_log)) {
        print('<div id="is_login">');
        foreach ($messages_log as $message) {
          print($message);
        }
        print('</div>');
      }
      ?>

  </div>

<div id="form-menu" class="form-menu">
        <form id="UserForm" class="user-form" action="index.php" method="POST">
          <h1><?php
                    echo (empty($all_langs));?></h1>

            <label>ФИО <br /><span class="error"><?php echo isset($errors['fio']) ? $messages['fio'] : ''; ?></span> <br/>
              <input type="text" 
              name="fio" 
              value="<?= $values['fio'] ?? '' ?>" 
              class="<?= (!empty($errors['fio'])) ? 'if_error' : 'no_errors' ?>">
            </label><br/>
            

            <label>Телефон <br /><span class="error"><?php echo isset($errors['phone']) ? $messages['phone'] : ''; ?></span><br>
                <input name="phone" 
                type="tel" 
                value="<?= $values['phone'] ?? '' ?>" 
                class="<?= (!empty($errors['phone'])) ? 'if_error' : 'no_errors' ?>">
                <p class="notice">*используйте телефонный код +7</p>
                
            </label> <br>
            
            <label>Email <br /><span class="error"><?php echo isset($errors['email']) ? $messages['email'] : ''; ?></span><br/>
                <input name="email" 
                type="email" 
                value="<?= $values['email'] ?? '' ?>" 
                class="<?= (!empty($errors['email'])) ? 'if_error' : 'no_errors' ?>">
                
            </label><br />
            
            <label>Дата рождения <br /><span class="error"><?php echo isset($errors['date']) ? $messages['date'] : ''; ?></span><br />
                    <input name="date"
                            value="<?= $values['date'] ?? date('Y-m-d') ?>" 
                            class="<?= (!empty($errors['date'])) ? 'if_error' : 'no_errors' ?>"
                            type="date"/>
                    
            </label><br />


            <label>Пол <br/><span class="error"><?php echo isset($errors['gender']) ? $messages['gender'] : ''; ?></span><br />
                <label><input 
                type="radio"
                name="gender" 
                value="Male" <?= (isset($values['gender']) && $values['gender'] === 'Male') ? 'checked' : ''; ?> />
                    Муж</label>
                <label><input 
                type="radio"
                name="gender" 
                value="Female" <?= (isset($values['gender']) && $values['gender'] === 'Female') ? 'checked' : ''; ?> />
                    Жен</label><br />
                
            </label>
            
            <label>Ваш любимый язык программирования <br />
              <span class="error"><?php echo isset($errors['favorite_languages']) ? $messages['favorite_languages'] : ''; ?></span><br />
                <select 
                name="favorite_languages[]" 
                class="<?= (!empty($errors['favorite_languages'])) ? 'if_error' : 'no_errors' ?>"
                multiple="multiple">
                    <?php
                    

                    $selected_languages = isset($values['favorite_languages']) ? explode(',', $values['favorite_languages']) : [];
                    
                    foreach ($all_langs as $lang => $value) {
                      printf('<option value="%s" ', $lang);
                      if(in_array($lang, $selected_languages)) {
                        print 'selected="selected"';
                      }
                      printf('>%s</option>', $value);
                    }

                  
                    ?>
                </select>
                
            </label><br />

            <label><p><b>Ваша биография <br /><span class="error">
              <?php echo isset($errors['biography']) ? $messages['biography'] : ''; ?></span></b></p>
                <p> <textarea name="biography" class="<?= (!empty($errors['biography'])) ? 'if_error' : 'no_errors' ?>"><?php print $values['biography'];?></textarea>
                </p>
            </label>
            
          <label><input 
            type="checkbox"  
            name="contract" <?= !empty($values['contract']) ? 'checked' : ''; ?> 
            class="<?= (!empty($errors['contract'])) ? 'if_error' : 'no_errors' ?>">
            С контрактом ознакомлен(a)
            
          </label> 
            <br /><span class="error"><?php echo isset($errors['contract']) ? $messages['contract'] : ''; ?>  <br /></span>
            <input name="submit_button" type="submit" value="Сохранить">
        </form>
    </div>
  </body>
</html>
