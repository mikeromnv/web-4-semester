<?php
// logout.php
session_start();

// Очищаем все данные сессии
$_SESSION = array();

// Уничтожаем сессию
session_destroy();

// Сбрасываем HTTP Basic Authentication
header('HTTP/1.1 401 Unauthorized');
header('WWW-Authenticate: Basic realm="Admin Area"');

// Перенаправляем на страницу входа с сообщением
header('Location: login.php?logout=1');
exit();
?>