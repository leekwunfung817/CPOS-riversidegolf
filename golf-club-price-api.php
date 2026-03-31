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

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'database connection failed'
    ));
    exit;
}

$action = '';
if (isset($_REQUEST['action'])) {
    $action = trim((string) $_REQUEST['action']);
}

function response_json($payload)
{
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
}

function get_param($name, $default = '')
{
    if (!isset($_REQUEST[$name])) {
        return $default;
    }
    return trim((string) $_REQUEST[$name]);
}

if ($action === 'list') {
    $rows = array();
    $sql = "SELECT `name`, `deposit`, `rental-fee` FROM `golf-club-price` ORDER BY `name` DESC";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = array(
                'name' => $row['name'],
                'deposit' => $row['deposit'],
                'rental_fee' => $row['rental-fee']
            );
        }
    }

    response_json(array(
        'success' => true,
        'records' => $rows
    ));
    $conn->close();
    exit;
}

if ($action === 'add') {
    $name = get_param('name');
    $deposit = get_param('deposit');
    $rental_fee = get_param('rental_fee');

    if ($name === '' || $deposit === '' || $rental_fee === '') {
        response_json(array(
            'success' => false,
            'message' => 'name, deposit and rental_fee are required'
        ));
        $conn->close();
        exit;
    }

    $dup_sql = "SELECT COUNT(*) AS cnt FROM `golf-club-price` WHERE `name` = ?";
    $dup_stmt = $conn->prepare($dup_sql);
    $dup_stmt->bind_param('s', $name);
    $dup_stmt->execute();
    $dup_result = $dup_stmt->get_result();
    $dup_row = $dup_result->fetch_assoc();
    $dup_stmt->close();

    if ((int) $dup_row['cnt'] > 0) {
        response_json(array(
            'success' => false,
            'message' => 'name already exists'
        ));
        $conn->close();
        exit;
    }

    $sql = "INSERT INTO `golf-club-price` (`name`, `deposit`, `rental-fee`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdd', $name, $deposit, $rental_fee);
    $ok = $stmt->execute();
    $stmt->close();

    response_json(array(
        'success' => $ok,
        'message' => $ok ? 'added' : 'add failed'
    ));
    $conn->close();
    exit;
}

if ($action === 'edit') {
    $original_name = get_param('original_name');
    $name = get_param('name');
    $deposit = get_param('deposit');
    $rental_fee = get_param('rental_fee');

    if ($original_name === '' || $name === '' || $deposit === '' || $rental_fee === '') {
        response_json(array(
            'success' => false,
            'message' => 'original_name, name, deposit and rental_fee are required'
        ));
        $conn->close();
        exit;
    }

    if ($original_name !== $name) {
        $dup_sql = "SELECT COUNT(*) AS cnt FROM `golf-club-price` WHERE `name` = ?";
        $dup_stmt = $conn->prepare($dup_sql);
        $dup_stmt->bind_param('s', $name);
        $dup_stmt->execute();
        $dup_result = $dup_stmt->get_result();
        $dup_row = $dup_result->fetch_assoc();
        $dup_stmt->close();

        if ((int) $dup_row['cnt'] > 0) {
            response_json(array(
                'success' => false,
                'message' => 'new name already exists'
            ));
            $conn->close();
            exit;
        }
    }

    $sql = "UPDATE `golf-club-price` SET `name` = ?, `deposit` = ?, `rental-fee` = ? WHERE `name` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdds', $name, $deposit, $rental_fee, $original_name);
    $ok = $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    response_json(array(
        'success' => $ok,
        'message' => $ok ? 'updated' : 'update failed',
        'affected' => $affected
    ));
    $conn->close();
    exit;
}

if ($action === 'delete') {
    $name = get_param('name');

    if ($name === '') {
        response_json(array(
            'success' => false,
            'message' => 'name is required'
        ));
        $conn->close();
        exit;
    }

    $sql = "DELETE FROM `golf-club-price` WHERE `name` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $name);
    $ok = $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    response_json(array(
        'success' => $ok,
        'message' => $ok ? 'deleted' : 'delete failed',
        'affected' => $affected
    ));
    $conn->close();
    exit;
}

response_json(array(
    'success' => false,
    'message' => 'invalid action'
));

$conn->close();
