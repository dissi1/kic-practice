<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO requests (date, event_name, customer, deadline, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['date'], $_POST['event_name'], $_POST['customer'], $_POST['deadline'], $_POST['status']]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Добавить заявку - КИЦ</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        input, select { margin: 5px 0 15px; padding: 8px; width: 300px; }
        label { font-weight: bold; }
        button { padding: 10px 20px; background: #27ae60; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Новая заявка на полиграфию</h1>
    <form method="post">
        <label>Дата заявки:</label><br>
        <input type="date" name="date" required><br>
        
        <label>Продукция/мероприятие (афиша, баннер, буклет и т.д.):</label><br>
        <input type="text" name="event_name" required><br>
        
        <label>Заказчик:</label><br>
        <input type="text" name="customer" required><br>
        
        <label>Срок выполнения:</label><br>
        <input type="date" name="deadline" required><br>
        
        <label>Статус:</label><br>
        <select name="status">
            <option value="Новая">Новая</option>
            <option value="В работе">В работе</option>
            <option value="Готово">Готово</option>
        </select><br>
        
        <button type="submit">Сохранить</button>
        <a href="index.php">Отмена</a>
    </form>
</body>
</html>