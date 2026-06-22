<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Проверка прав: только admin может редактировать
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    die("Заявка не найдена");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['event_name'])) $errors['event_name'] = 'Укажите наименование продукции';
    if (empty($_POST['customer'])) $errors['customer'] = 'Укажите заказчика';
    if (empty($_POST['deadline'])) $errors['deadline'] = 'Укажите срок выполнения';
    
    if (strlen($_POST['event_name']) > 100) $errors['event_name'] = 'Название продукции не должно превышать 100 символов';
    if (strlen($_POST['customer']) > 100) $errors['customer'] = 'Заказчик не должен превышать 100 символов';
    
    if (!empty($_POST['date']) && !empty($_POST['deadline'])) {
        if ($_POST['date'] > $_POST['deadline']) {
            $errors['deadline'] = 'Срок выполнения не может быть раньше даты заявки';
        }
    }
    
    if (count($errors) == 0) {
        $stmt = $pdo->prepare("UPDATE requests SET date=?, event_name=?, customer=?, deadline=?, status=? WHERE id=?");
        $stmt->execute([$_POST['date'], $_POST['event_name'], $_POST['customer'], $_POST['deadline'], $_POST['status'], $id]);
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
    <title>Редактирование заявки - КИЦ пгт. Октябрьское</title>
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
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="add.php" class="btn-add"> Новая заявка</a>
            <?php endif; ?>
            <a href="export.php" class="btn-export"> Экспорт в Excel</a>
        </div>

        <div class="content">
            <div class="breadcrumbs">
                <a href="index.php"> Главная</a> / <a href="index.php">Список заявок</a> / <span>Редактирование №<?= $id ?></span>
            </div>
            
            <div class="form-area">
                <h2 class="page-title">Редактирование заявки №<?= $id ?></h2>
                
                <?php if (count($errors) > 0): ?>
                    <div class="alert-error">
                        ⚠️ Пожалуйста, исправьте ошибки в форме.
                    </div>
                <?php endif; ?>
                
                <form method="post" id="editForm">
                    <div class="form-group">
                        <label>Дата заявки</label>
                        <input type="date" name="date" value="<?= $row['date'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Продукция / мероприятие <span style="color:#e74c3c">*</span></label>
                        <input type="text" name="event_name" 
                            class="<?= isset($errors['event_name']) ? 'error' : '' ?>" 
                            value="<?= htmlspecialchars($row['event_name']) ?>"
                            maxlength="100">
                        <?php if (isset($errors['event_name'])): ?>
                            <span class="error-message">⚠️ <?= $errors['event_name'] ?></span>
                        <?php else: ?>
                            <span class="hint">Максимум 100 символов</span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Заказчик <span style="color:#e74c3c">*</span></label>
                        <input type="text" name="customer" 
                            class="<?= isset($errors['customer']) ? 'error' : '' ?>" 
                            value="<?= htmlspecialchars($row['customer']) ?>"
                            maxlength="100">
                        <?php if (isset($errors['customer'])): ?>
                            <span class="error-message">⚠️ <?= $errors['customer'] ?></span>
                        <?php else: ?>
                            <span class="hint">Максимум 100 символов</span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Срок выполнения <span style="color:#e74c3c">*</span></label>
                        <input type="date" name="deadline" class="<?= isset($errors['deadline']) ? 'error' : '' ?>" value="<?= $row['deadline'] ?>">
                        <?php if (isset($errors['deadline'])): ?>
                            <span class="error-message">⚠️ <?= $errors['deadline'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Статус</label>
                        <select name="status">
                            <option value="Новая" <?= $row['status'] == 'Новая' ? 'selected' : '' ?>>Новая</option>
                            <option value="В работе" <?= $row['status'] == 'В работе' ? 'selected' : '' ?>>В работе</option>
                            <option value="Готово" <?= $row['status'] == 'Готово' ? 'selected' : '' ?>>Готово</option>
                        </select>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn-submit"> Сохранить изменения</button>
                        <a href="index.php" class="btn-cancel"> Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.textContent = ' Сохранение...';
        }
    });
</script>
</body>
</html>