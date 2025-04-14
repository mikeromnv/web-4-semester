<?php
require_once 'functions/Query.php';
require_once 'functions/MyFunctions.php';

try {
    // Хешируем пароль
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Добавляем в таблицу users
    $stmt = $databaseConnection->prepare("
        INSERT INTO users 
        (full_name, phone, email, birth_date, gender, bio, contract_accepted) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        'Админ', 
        '+70000000000', 
        'admin@example.com', 
        '2000-01-01', 
        'Male', 
        'Системный администратор', 
        1
    ]);
    
    // Получаем ID добавленного пользователя
    $adminId = $databaseConnection->lastInsertId();
    
    // Добавляем в таблицу login_users
    $stmt = $databaseConnection->prepare("
        INSERT INTO login_users 
        (login, password_hash, role, user_id) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        'admin',
        $hashedPassword,
        'admin',
        $adminId
    ]);
    
    echo "Администратор успешно добавлен! Логин: admin, Пароль: admin123";
    
} catch (PDOException $e) {
    die("Ошибка при добавлении администратора: " . $e->getMessage());
}
?>