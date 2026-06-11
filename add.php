<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');

$errors = [];
$form_data = ['date' => date('Y-m-d'), 'event_name' => '', 'customer' => '', 'deadline' => '', 'status' => 'Новая'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data = $_POST;
    
    if (empty($_POST['event_name'])) $errors['event_name'] = 'Укажите наименование продукции';
    if (empty($_POST['customer'])) $errors['customer'] = 'Укажите заказчика';
    if (empty($_POST['deadline'])) $errors['deadline'] = 'Укажите срок выполнения';
    
    if (count($errors) == 0) {
        $stmt = $pdo->prepare("INSERT INTO requests (date, event_name, customer, deadline, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['date'], $_POST['event_name'], $_POST['customer'], $_POST['deadline'], $_POST['status']]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка - КИЦ пгт. Октябрьское</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="main-container">
        <div class="content-card">
            <div class="header">
                <div class="logo-area">
                    <div><img src="9855_logo.png" alt="Логотип КИЦ"></div>
                    <div>
                        <h1>МБУК «Культурно-информационный центр»</h1>
                        <h2>пгт. Октябрьское</h2>
                    </div>
                </div>
                <div class="user-info">
                    <?= htmlspecialchars($_SESSION['login']) ?> | <a href="logout.php">Выйти</a>
                </div>
            </div>

            <div class="nav-bar">
                <a href="index.php"> Все заявки</a>
                <a href="add.php" class="btn-add"> Новая заявка</a>
                <a href="export.php" class="btn-export"> Экспорт в Excel</a>
            </div>

            <div class="form-area">
                <h2 class="page-title">Новая заявка на полиграфию</h2>
                
                <?php if (count($errors) > 0): ?>
                    <div class="alert-error">
                        ⚠️ Пожалуйста, исправьте ошибки в форме.
                    </div>
                <?php endif; ?>
                
                <form method="post" id="mainForm">
                    <div class="form-group">
                        <label>Дата заявки</label>
                        <input type="date" name="date" value="<?= $form_data['date'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Продукция / мероприятие <span style="color:#e74c3c">*</span></label>
                        <input type="text" name="event_name" class="<?= isset($errors['event_name']) ? 'error' : '' ?>" value="<?= htmlspecialchars($form_data['event_name']) ?>" placeholder="Например: Афиша «День посёлка», Баннер для сцены...">
                        <?php if (isset($errors['event_name'])): ?>
                            <span class="error-message">⚠️ <?= $errors['event_name'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Заказчик <span style="color:#e74c3c">*</span></label>
                        <input type="text" name="customer" class="<?= isset($errors['customer']) ? 'error' : '' ?>" value="<?= htmlspecialchars($form_data['customer']) ?>" placeholder="ФИО или организация">
                        <?php if (isset($errors['customer'])): ?>
                            <span class="error-message">⚠️ <?= $errors['customer'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Срок выполнения <span style="color:#e74c3c">*</span></label>
                        <input type="date" name="deadline" class="<?= isset($errors['deadline']) ? 'error' : '' ?>" value="<?= $form_data['deadline'] ?>">
                        <?php if (isset($errors['deadline'])): ?>
                            <span class="error-message">⚠️ <?= $errors['deadline'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Статус</label>
                        <select name="status">
                            <option value="Новая" <?= $form_data['status'] == 'Новая' ? 'selected' : '' ?>>Новая</option>
                            <option value="В работе" <?= $form_data['status'] == 'В работе' ? 'selected' : '' ?>>В работе</option>
                            <option value="Готово" <?= $form_data['status'] == 'Готово' ? 'selected' : '' ?>>Готово</option>
                        </select>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn-submit">Сохранить заявку</button>
                        <a href="index.php" class="btn-cancel">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>