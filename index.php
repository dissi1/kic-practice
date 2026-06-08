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
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список заявок - КИЦ пгт. Октябрьское</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Arial', sans-serif;
            background: linear-gradient(135deg, #eef2f7 0%, #d4dee8 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        /* Главный контейнер */
        .main-container {
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Карточка контента */
        .content-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        /* Шапка с логотипом */
        .header {
            background: #1e3a5f;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-placeholder {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .header h1 {
            color: white;
            font-size: 1.3rem;
            font-weight: 500;
        }

        .header h2 {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            font-weight: 400;
        }

        .user-info {
            background: rgba(255,255,255,0.1);
            padding: 8px 18px;
            border-radius: 40px;
            color: white;
            font-size: 0.85rem;
        }

        .user-info a {
            color: #ffd966;
            text-decoration: none;
            margin-left: 10px;
        }

        .user-info a:hover {
            text-decoration: underline;
        }

        /* Навигационная полоска */
        .nav-bar {
            background: #f8f9fc;
            padding: 12px 30px;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-bar a {
            color: #1e3a5f;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-add {
            background: #1e3a5f;
            color: white !important;
            padding: 8px 20px;
            border-radius: 40px;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background: #0f2c48;
        }

        /* Основной контент */
        .content {
            padding: 30px;
        }

        .content h2 {
            color: #1e3a5f;
            font-size: 1.3rem;
            margin-bottom: 20px;
            border-left: 4px solid #c9a03d;
            padding-left: 15px;
        }

        /* Таблица */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #eef2f7;
            color: #1e3a5f;
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            border-bottom: 2px solid #dce3ec;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        tr:hover {
            background: #fafbfd;
        }

        /* Кнопки действий */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-edit {
            background: #eef2f7;
            color: #1e3a5f;
            padding: 5px 14px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-edit:hover {
            background: #e2e8f0;
        }

        .btn-delete {
            background: #fdecea;
            color: #b33;
            padding: 5px 14px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-delete:hover {
            background: #f8d7da;
            color: #a00;
        }

        /* Статусы */
        .status-new {
            background: #eef2f7;
            color: #1e3a5f;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-work {
            background: #fff3e0;
            color: #c9a03d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-done {
            background: #e6f7e6;
            color: #2e7d32;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
        }

        /* Пустое состояние */
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #8a9bb0;
        }

        /* Адаптивность */
        @media (max-width: 800px) {
            body {
                padding: 15px;
            }
            .content {
                padding: 20px;
            }
            .header {
                flex-direction: column;
                text-align: center;
            }
            th, td {
                font-size: 0.8rem;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="content-card">
            <!-- Шапка -->
            <div class="header">
                <div class="logo-area">
                    <div class="logo-placeholder">🎭</div>
                    <div>
                        <h1>МБУК «Культурно-информационный центр»</h1>
                        <h2>пгт. Октябрьское</h2>
                    </div>
                </div>
                <div class="user-info">
                    👤 <?= htmlspecialchars($_SESSION['login']) ?>
                    <a href="logout.php">Выйти</a>
                </div>
            </div>

            <!-- Навигация -->
            <div class="nav-bar">
                <a href="index.php">📋 Все заявки</a>
                <a href="add.php" class="btn-add">➕ Новая заявка</a>
            </div>

            <!-- Контент -->
            <div class="content">
                <h2>Список заявок на полиграфическую продукцию</h2>
                
                <?php if (count($requests) > 0): ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Дата</th>
                                    <th>Продукция / мероприятие</th>
                                    <th>Заказчик</th>
                                    <th>Срок</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $row): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= date('d.m.Y', strtotime($row['date'])) ?></td>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><?= htmlspecialchars($row['customer']) ?></td>
                                        <td><?= date('d.m.Y', strtotime($row['deadline'])) ?></td>
                                        <td>
                                            <?php
                                            $status_class = '';
                                            $status_text = '';
                                            switch($row['status']) {
                                                case 'Новая':
                                                    $status_class = 'status-new';
                                                    $status_text = 'Новая';
                                                    break;
                                                case 'В работе':
                                                    $status_class = 'status-work';
                                                    $status_text = 'В работе';
                                                    break;
                                                case 'Готово':
                                                    $status_class = 'status-done';
                                                    $status_text = 'Готово';
                                                    break;
                                                default:
                                                    $status_class = 'status-new';
                                                    $status_text = $row['status'];
                                            }
                                            ?>
                                            <span class="<?= $status_class ?>"><?= $status_text ?></span>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">✏️ Ред.</a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Удалить заявку?')">🗑️ Удл.</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        🎨 Пока нет ни одной заявки<br>
                        <a href="add.php" style="color: #1e3a5f; margin-top: 10px; display: inline-block;">➕ Создать первую заявку</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>