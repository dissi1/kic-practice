<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');
$requests = $pdo->query("SELECT * FROM requests ORDER BY date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Список заявок - КИЦ</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; background: #3498db; color: white; border-radius: 3px; }
        .btn-del { background: #e74c3c; }
        .btn-add { background: #27ae60; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Культурно-информационный центр</h1>
    <h2>Учёт заявок на полиграфическую продукцию</h2>
    <p>Пользователь: <?= htmlspecialchars($_SESSION['login']) ?> | <a href="logout.php">Выйти</a></p>
    
    <a href="add.php" class="btn btn-add">+ Новая заявка</a>
    
    <table>
        <tr>
            <th>ID</th><th>Дата</th><th>Продукция/мероприятие</th><th>Заказчик</th><th>Срок</th><th>Статус</th><th>Действия</th>
        </tr>
        <?php foreach ($requests as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= htmlspecialchars($row['event_name']) ?></td>
            <td><?= htmlspecialchars($row['customer']) ?></td>
            <td><?= $row['deadline'] ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn">Ред.</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Удалить заявку?')">Удл.</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>