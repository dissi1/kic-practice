<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "SELECT * FROM requests WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND customer LIKE ?";
    $params[] = "%$search%";
}
if ($status_filter !== '') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}
$sql .= " ORDER BY date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
$filename = 'zayavki';
if ($search !== '') $filename .= '_poisk_' . $search;
if ($status_filter !== '') $filename .= '_' . $status_filter;
$filename .= '_' . date('Y-m-d') . '.xls';
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo '<table border="1">';
echo '<tr><th>ID</th><th>Дата заявки</th><th>Продукция/мероприятие</th><th>Заказчик</th><th>Срок выполнения</th><th>Статус</th></tr>';

if (count($requests) > 0) {
    foreach ($requests as $row) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . date('d.m.Y', strtotime($row['date'])) . '</td>';
        echo '<td>' . htmlspecialchars($row['event_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['customer']) . '</td>';
        echo '<td>' . date('d.m.Y', strtotime($row['deadline'])) . '</td>';
        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="6" style="text-align:center;">📭 Нет заявок для экспорта</td></tr>';
}
echo '</table>';
?>