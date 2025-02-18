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
                <input name="fio" />
            </label><br/>
            <label>Телефон: <br>
                <input name="phone" type="tel">
                <p class="notice">*вместе с кодом страны (10 цифр)</p>
            </label> <br>
            <label>Email:<br/>
                <input name="email" type="email">
            </label><br />

            <label>Дата рождения:<br />
                <input  name="date"
                        value="2025-10-02"
                        type="date"/>
            </label><br />

            <label>Пол:<br />
                <label><input type="radio"
                    name="gender" value="Male" />
                    Муж</label>
                <label><input type="radio"
                    name="gender" value="Female" />
                    Жен</label><br />
            </label>
                
            <label>Ваш любимый язык программирования:<br />
                <select name="favorite_languages[]"
                multiple="multiple" style="width: 250px; height: 150px">
                <option value="Pascal">Pascal</option>
                <option value="C" selected="selected">C</option>
                <option value="C++" selected="selected">C++</option>
                <option value="JavaScript">JavaScript</option>
                <option value="PHP">PHP</option>
                <option value="Python">Python</option>
                <option value="Java">Java</option>
                <option value="Haskell">Haskell</option>
                <option value="Clojure">Clojure</option>
                <option value="Prolog">Prolog</option>
                <option value="Scala">Scala</option>
                </select>
            </label><br />
            <label><p><b>Ваша биография:</b></p>
            <p><textarea name="biography"></textarea></p> </label>
            
            
          <label><input type="checkbox" 
            name="contract" />
            С контрактом ознакомлен(a)</label><br />

            <input name="submit_button" type="submit" value="Сохранить">
        </form>
    </div>
  </body>
</html>
