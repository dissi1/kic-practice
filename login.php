<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');

// Получаем IP-адрес пользователя
$ip = $_SERVER['REMOTE_ADDR'];

// Очищаем старые попытки (старше 15 минут)
$pdo->prepare("DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 15 MINUTE)")->execute();

// Проверяем количество неудачных попыток с этого IP
$stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ?");
$stmt->execute([$ip]);
$attempts = $stmt->fetchColumn();

if ($attempts >= 3) {
    $error = 'Слишком много неудачных попыток. Попробуйте через 15 минут.';
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT id, login, password, role FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Успешный вход — очищаем попытки
        $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?")->execute([$ip]);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        // Неудачная попытка — записываем
        $pdo->prepare("INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, NOW())")->execute([$ip]);
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - КИЦ пгт. Октябрьское</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Синяя полоска удалена -->
            <div class="logo-area">
                <div class="logo-wrapper">
                    <img src="9855_logo.png" alt="Логотип КИЦ" class="login-logo">
                </div>
                <h1>МБУК «Культурно-информационный центр»</h1>
            </div>
            
            <div class="form-area">
                <?php if(isset($error)): ?>
                    <div class="alert-error" style="margin-bottom: 20px;">
                        ⚠️ <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label>Логин</label>
                        <input type="text" name="login" placeholder="Введите логин" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" placeholder="Введите пароль" required>
                    </div>
                    
                    <button type="submit" class="btn-submit" style="width: 100%;">Войти в систему</button>
                </form>
                
                <div class="login-hint">
                    🔑 Тестовый доступ: <strong>admin</strong> / <strong>password</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>