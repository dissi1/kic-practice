<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Вход в систему</title></head>
<body style="font-family:Arial;margin:50px;text-align:center">
    <h2>КИЦ пгт. Октябрьское</h2>
    <h3>Учет заявок на полиграфию</h3>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <p><input type="text" name="login" placeholder="Логин" required></p>
        <p><input type="password" name="password" placeholder="Пароль" required></p>
        <p><button type="submit">Войти</button></p>
    </form>
    <p><small>Логин: admin | Пароль: password</small></p>
</body>
</html>