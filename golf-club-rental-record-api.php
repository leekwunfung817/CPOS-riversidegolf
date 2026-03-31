<?php

session_start();

if (!isset($_SESSION["management"])) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'success' => false,
        'message' => 'forbidden'
    ));
    exit;
}

header('Content-Type: application/json; charset=utf-8');

require_once 'account_variable.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'database connection failed'
    ));
    exit;
}

function get_int_param($name, $default, $min, $max)
{
    if (!isset($_GET[$name])) {
        return $default;
    }
    $value = (int) $_GET[$name];
    if ($value < $min) {
        $value = $min;
    }
    if ($value > $max) {
        $value = $max;
    }
    return $value;
}

function get_str_param($name, $default)
{
    if (!isset($_GET[$name])) {
        return $default;
    }
    return trim((string) $_GET[$name]);
}

$page = get_int_param('page', 1, 1, 1000000);
$page_size = get_int_param('page_size', 20, 1, 200);
$status = get_str_param('status', 'returned');
$keyword = get_str_param('keyword', '');
$bay = get_str_param('bay', '');
$club_name = get_str_param('club_name', '');
$date_from = get_str_param('date_from', '');
$date_to = get_str_param('date_to', '');
$sort_by = get_str_param('sort_by', 'start_dt');
$sort_dir = strtolower(get_str_param('sort_dir', 'desc'));

if ($sort_dir !== 'asc' && $sort_dir !== 'desc') {
    $sort_dir = 'desc';
}

$order_column_map = array(
    'seq' => '`golf-club-seq`',
    'start_dt' => '`start-dt`',
    'returned' => '`returned`',
    'bay' => '`bay`',
    'name' => '`name`',
    'deposit' => '`deposit`',
    'rental_fee' => '`rental-fee`'
);

if (!isset($order_column_map[$sort_by])) {
    $sort_by = 'start_dt';
}

$where_parts = array('1=1');
$param_types = '';
$param_values = array();

if ($status === 'returned') {
    $where_parts[] = '`returned` IS NOT NULL';
} else if ($status === 'not_returned') {
    $where_parts[] = '`returned` IS NULL';
} else {
    $status = 'all';
}

if ($keyword !== '') {
    $where_parts[] = '('
        . '`golf-club-seq` LIKE ? '
        . 'OR `bay` LIKE ? '
        . 'OR `name` LIKE ? '
        . 'OR IFNULL(`src`, \'\') LIKE ?'
        . ')';
    $keyword_like = '%' . $keyword . '%';
    $param_types .= 'ssss';
    $param_values[] = $keyword_like;
    $param_values[] = $keyword_like;
    $param_values[] = $keyword_like;
    $param_values[] = $keyword_like;
}

if ($bay !== '') {
    $where_parts[] = '`bay` = ?';
    $param_types .= 's';
    $param_values[] = $bay;
}

if ($club_name !== '') {
    $where_parts[] = '`name` = ?';
    $param_types .= 's';
    $param_values[] = $club_name;
}

if ($date_from !== '') {
    $where_parts[] = 'DATE(`start-dt`) >= ?';
    $param_types .= 's';
    $param_values[] = $date_from;
}

if ($date_to !== '') {
    $where_parts[] = 'DATE(`start-dt`) <= ?';
    $param_types .= 's';
    $param_values[] = $date_to;
}

$where_sql = implode(' AND ', $where_parts);

$count_sql = 'SELECT COUNT(*) AS `total_records` FROM `golf-club-rental-record` WHERE ' . $where_sql;
$count_stmt = $conn->prepare($count_sql);
if ($count_stmt === false) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'failed to prepare count query'
    ));
    $conn->close();
    exit;
}

if ($param_types !== '') {
    $bind_args = array($param_types);
    foreach ($param_values as $k => $v) {
        $bind_args[] = &$param_values[$k];
    }
    call_user_func_array(array($count_stmt, 'bind_param'), $bind_args);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_records = (int) $count_row['total_records'];
$count_stmt->close();

$total_pages = 1;
if ($total_records > 0) {
    $total_pages = (int) ceil($total_records / $page_size);
}
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $page_size;

$data_sql = 'SELECT '
    . '`golf-club-seq` AS `seq`, '
    . 'DATE_FORMAT(`start-dt`, \'%Y-%m-%d %H:%i:%s\') AS `start_dt`, '
    . 'IFNULL(DATE_FORMAT(`returned`, \'%Y-%m-%d %H:%i:%s\'), \'\') AS `returned_dt`, '
    . '`bay`, '
    . '`name`, '
    . '`deposit`, '
    . '`rental-fee` AS `rental_fee`, '
    . 'IFNULL(`src`, \'\') AS `staff` '
    . 'FROM `golf-club-rental-record` '
    . 'WHERE ' . $where_sql . ' '
    . 'ORDER BY ' . $order_column_map[$sort_by] . ' ' . strtoupper($sort_dir) . ', `golf-club-seq` DESC '
    . 'LIMIT ? OFFSET ?';

$data_stmt = $conn->prepare($data_sql);
if ($data_stmt === false) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'failed to prepare data query'
    ));
    $conn->close();
    exit;
}

$data_types = $param_types . 'ii';
$data_values = $param_values;
$data_values[] = $page_size;
$data_values[] = $offset;

$bind_data_args = array($data_types);
foreach ($data_values as $k => $v) {
    $bind_data_args[] = &$data_values[$k];
}
call_user_func_array(array($data_stmt, 'bind_param'), $bind_data_args);

$data_stmt->execute();
$data_result = $data_stmt->get_result();

$records = array();
while ($row = $data_result->fetch_assoc()) {
    $records[] = $row;
}
$data_stmt->close();

$window_size = 9;
$half_window = (int) floor($window_size / 2);
$window_start = max(1, $page - $half_window);
$window_end = min($total_pages, $window_start + $window_size - 1);
$window_start = max(1, $window_end - $window_size + 1);

$page_window = array();
for ($i = $window_start; $i <= $window_end; $i++) {
    $page_window[] = $i;
}

echo json_encode(array(
    'success' => true,
    'meta' => array(
        'page' => $page,
        'page_size' => $page_size,
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page_count' => count($records),
        'has_prev' => $page > 1,
        'has_next' => $page < $total_pages,
        'prev_page' => $page > 1 ? $page - 1 : null,
        'next_page' => $page < $total_pages ? $page + 1 : null,
        'page_window' => array(
            'start' => $window_start,
            'end' => $window_end,
            'pages' => $page_window
        ),
        'filters' => array(
            'status' => $status,
            'keyword' => $keyword,
            'bay' => $bay,
            'club_name' => $club_name,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir
        )
    ),
    'records' => $records
), JSON_UNESCAPED_UNICODE);

$conn->close();
