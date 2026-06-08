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
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка - КИЦ пгт. Октябрьское</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Arial', sans-serif;
            background: linear-gradient(135deg, #eef2f7 0%, #d4dee8 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .main-container {
            max-width: 700px;
            margin: 0 auto;
        }

        .content-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: #1e3a5f;
            padding: 20px 30px;
        }

        .header h1 {
            color: white;
            font-size: 1.3rem;
            font-weight: 500;
        }

        .header p {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .form-area {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e3a5f;
            margin-bottom: 8px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d0d7de;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #c9a03d;
            box-shadow: 0 0 0 3px rgba(201, 160, 61, 0.2);
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-submit {
            background: #1e3a5f;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #0f2c48;
        }

        .btn-cancel {
            background: #eef2f7;
            color: #1e3a5f;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="content-card">
            <div class="header">
                <h1>📝 Новая заявка на полиграфию</h1>
                <p>МБУК «Культурно-информационный центр» | пгт. Октябрьское</p>
            </div>
            <div class="form-area">
                <form method="post">
                    <div class="form-group">
                        <label>📅 Дата заявки</label>
                        <input type="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label>🎨 Продукция / мероприятие</label>
                        <input type="text" name="event_name" placeholder="Например: Афиша «День посёлка», Баннер для сцены..." required>
                    </div>
                    <div class="form-group">
                        <label>👤 Заказчик</label>
                        <input type="text" name="customer" placeholder="ФИО или организация" required>
                    </div>
                    <div class="form-group">
                        <label>⏰ Срок выполнения</label>
                        <input type="date" name="deadline" required>
                    </div>
                    <div class="form-group">
                        <label>📌 Статус</label>
                        <select name="status">
                            <option value="Новая">Новая</option>
                            <option value="В работе">В работе</option>
                            <option value="Готово">Готово</option>
                        </select>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn-submit">✅ Сохранить заявку</button>
                        <a href="index.php" class="btn-cancel">❌ Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>