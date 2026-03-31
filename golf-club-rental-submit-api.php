<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["management"])) {
    http_response_code(403);
    echo json_encode(array(
        'success' => false,
        'message' => 'forbidden'
    ));
    exit;
}

require_once 'account_variable.php';
require_once './position_list.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'database connection failed'
    ));
    exit;
}

$raw = file_get_contents('php://input');
$input_json = json_decode($raw, true);

$items = array();
if (is_array($input_json) && isset($input_json['items']) && is_array($input_json['items'])) {
    $items = $input_json['items'];
} else if (isset($_POST['items_json'])) {
    $decoded = json_decode($_POST['items_json'], true);
    if (is_array($decoded)) {
        $items = $decoded;
    }
}

if (!is_array($items) || count($items) === 0) {
    echo json_encode(array(
        'success' => false,
        'message' => 'No rental items provided'
    ));
    $conn->close();
    exit;
}

$price_map = array();
$price_sql = "SELECT `name`, `deposit`, `rental-fee` FROM `golf-club-price`";
$price_result = $conn->query($price_sql);
if ($price_result) {
    while ($row = $price_result->fetch_assoc()) {
        $price_map[$row['name']] = array(
            'deposit' => (float) $row['deposit'],
            'rental_fee' => (float) $row['rental-fee']
        );
    }
}

$valid_bay_set = array();
foreach ($position_list as $bay) {
    $valid_bay_set[(string) $bay] = true;
}

$normalized_items = array();
$errors = array();

for ($i = 0; $i < count($items); $i++) {
    $item = $items[$i];

    $name = isset($item['name']) ? trim((string) $item['name']) : '';
    $bay = isset($item['bay']) ? trim((string) $item['bay']) : '';
    $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;

    if ($name === '') {
        $errors[] = 'Row ' . ($i + 1) . ': name is required';
        continue;
    }
    if (!isset($price_map[$name])) {
        $errors[] = 'Row ' . ($i + 1) . ': invalid name ' . $name;
        continue;
    }
    if ($bay === '' || !isset($valid_bay_set[$bay])) {
        $errors[] = 'Row ' . ($i + 1) . ': invalid bay';
        continue;
    }
    if ($quantity < 1 || $quantity > 999) {
        $errors[] = 'Row ' . ($i + 1) . ': quantity should be 1-999';
        continue;
    }

    $normalized_items[] = array(
        'name' => $name,
        'bay' => $bay,
        'quantity' => $quantity,
        'deposit' => $price_map[$name]['deposit'],
        'rental_fee' => $price_map[$name]['rental_fee']
    );
}

if (count($normalized_items) === 0) {
    echo json_encode(array(
        'success' => false,
        'message' => 'All selected rows are invalid',
        'errors' => $errors
    ), JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}

$staff = '';
if (isset($_SESSION['name'])) {
    $staff = (string) $_SESSION['name'];
}

$conn->begin_transaction();

$insert_sql = "
INSERT INTO `golf-club-rental-record`(
    `bay`,
    `name`,
    `deposit`,
    `rental-fee`,
    `src`
)
VALUES (?, ?, ?, ?, ?)
";
$insert_stmt = $conn->prepare($insert_sql);

if ($insert_stmt === false) {
    $conn->rollback();
    echo json_encode(array(
        'success' => false,
        'message' => 'failed to prepare insert statement'
    ));
    $conn->close();
    exit;
}

$total_rows_inserted = 0;
$summary = array();
$last_inserted_seq = 0;
$print_seqs = array();

try {
    foreach ($normalized_items as $item) {
        $bay = $item['bay'];
        $name = $item['name'];
        $deposit = (float) $item['deposit'];
        $rental_fee = (float) $item['rental_fee'];
        $quantity = (int) $item['quantity'];

        for ($k = 0; $k < $quantity; $k++) {
            $insert_stmt->bind_param('ssdds', $bay, $name, $deposit, $rental_fee, $staff);
            if (!$insert_stmt->execute()) {
                throw new Exception('Insert failed');
            }
            $total_rows_inserted++;
            $last_inserted_seq = (int) $conn->insert_id;
        }

        $summary[] = array(
            'name' => $name,
            'bay' => $bay,
            'quantity' => $quantity,
            'deposit_each' => $deposit,
            'rental_fee_each' => $rental_fee,
            'deposit_total' => $deposit * $quantity,
            'rental_fee_total' => $rental_fee * $quantity
        );

        if ($last_inserted_seq > 0) {
            $print_seqs[] = $last_inserted_seq;
        }
    }

    $conn->commit();

    echo json_encode(array(
        'success' => true,
        'message' => 'Rental records submitted',
        'inserted_rows' => $total_rows_inserted,
        'print_seq' => $last_inserted_seq,
        'print_seqs' => $print_seqs,
        'submitted_items' => count($normalized_items),
        'summary' => $summary,
        'validation_errors' => $errors
    ), JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(array(
        'success' => false,
        'message' => 'submit failed',
        'errors' => $errors
    ), JSON_UNESCAPED_UNICODE);
}

$insert_stmt->close();
$conn->close();
