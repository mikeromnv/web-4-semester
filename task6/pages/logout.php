<?php

session_start();
session_unset();
session_destroy();

header('HTTP/1.1 401 Unauthorized');
header('WWW-Authenticate: Basic realm="Admin Area"');

header('Location: login.php?logout=1');
exit();
?>