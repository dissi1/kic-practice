<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');

// Получаем параметры — здесь ВСЕГДА определёны переменные
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC' : 'DESC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Разрешённые поля для сортировки
$allowed_sorts = ['id', 'date', 'event_name', 'customer', 'deadline', 'status'];
if (!in_array($sort, $allowed_sorts)) {
    $sort = 'date';
}

// Базовый запрос
$sql = "SELECT * FROM requests WHERE 1=1";
$count_sql = "SELECT COUNT(*) FROM requests WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND customer LIKE ?";
    $count_sql .= " AND customer LIKE ?";
    $params[] = "%$search%";
}

if ($status_filter !== '') {
    $sql .= " AND status = ?";
    $count_sql .= " AND status = ?";
    $params[] = $status_filter;
}

// Получаем общее количество записей
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// Основной запрос с сортировкой и пагинацией
$sql .= " ORDER BY $sort $order LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список заявок - КИЦ пгт. Октябрьское</title>
    <link rel="stylesheet" href="style.css">
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
                <a href="export.php<?= (!empty($search) || !empty($status_filter)) ? '?' . http_build_query(['search' => $search, 'status' => $status_filter]) : '' ?>" class="btn-export"> Экспорт в Excel</a>
            </div>

            <div class="filter-area">
                <form method="get" action="index.php" style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%;">
                    <input type="text" name="search" class="search-input" placeholder="🔍 Поиск по заказчику..." value="<?= htmlspecialchars($search) ?>">
                    <select name="status" class="status-select">
                        <option value=""> Все статусы</option>
                        <option value="Новая" <?= $status_filter == 'Новая' ? 'selected' : '' ?>>Новая</option>
                        <option value="В работе" <?= $status_filter == 'В работе' ? 'selected' : '' ?>>В работе</option>
                        <option value="Готово" <?= $status_filter == 'Готово' ? 'selected' : '' ?>>Готово</option>
                    </select>
                    <button type="submit" class="btn-search"> Применить</button>
                    <?php if ($search !== '' || $status_filter !== ''): ?>
                        <a href="index.php" class="btn-clear">Сбросить всё</a>
                    <?php endif; ?>
                </form>
                
                <?php if ($search !== '' || $status_filter !== ''): ?>
                    <div class="filter-info">
                         Фильтр: 
                        <?php if ($search !== ''): ?>поиск «<?= htmlspecialchars($search) ?>» <?php endif; ?>
                        <?php if ($status_filter !== ''): ?>статус «<?= htmlspecialchars($status_filter) ?>»<?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="content">
                <h2>Список заявок на полиграфическую продукцию</h2>
                
                <?php if (count($requests) > 0): ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th><a href="?sort=id&order=<?= $sort=='id' && $order=='DESC' ? 'asc' : 'desc' ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&page=<?= $page ?>">ID <?= $sort=='id' ? ($order=='DESC' ? '▼' : '▲') : '' ?></a></th>
                                    <th><a href="?sort=date&order=<?= $sort=='date' && $order=='DESC' ? 'asc' : 'desc' ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&page=<?= $page ?>">Дата <?= $sort=='date' ? ($order=='DESC' ? '▼' : '▲') : '' ?></a></th>
                                    <th><a href="?sort=event_name&order=<?= $sort=='event_name' && $order=='DESC' ? 'asc' : 'desc' ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&page=<?= $page ?>">Продукция <?= $sort=='event_name' ? ($order=='DESC' ? '▼' : '▲') : '' ?></a></th>
                                    <th><a href="?sort=customer&order=<?= $sort=='customer' && $order=='DESC' ? 'asc' : 'desc' ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&page=<?= $page ?>">Заказчик <?= $sort=='customer' ? ($order=='DESC' ? '▼' : '▲') : '' ?></a></th>
                                    <th><a href="?sort=deadline&order=<?= $sort=='deadline' && $order=='DESC' ? 'asc' : 'desc' ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&page=<?= $page ?>">Срок <?= $sort=='deadline' ? ($order=='DESC' ? '▼' : '▲') : '' ?></a></th>
                                    <th><a href="?sort=status&order=<?= $sort=='status' && $order=='DESC' ? 'asc' : 'desc' ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&page=<?= $page ?>">Статус <?= $sort=='status' ? ($order=='DESC' ? '▼' : '▲') : '' ?></a></th>
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
                    
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page-1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">← Предыдущая</a>
                        <?php endif; ?>
                        <span class="active">Страница <?= $page ?> из <?= $total_pages ?></span>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page+1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">Следующая →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <?php if ($search !== '' || $status_filter !== ''): ?>
                             🔍 По заданным критериям ничего не найдено.<br>
                            <a href="index.php" style="color: #1e3a5f;">Показать все заявки</a>
                        <?php else: ?>
                              Пока нет ни одной заявки
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>