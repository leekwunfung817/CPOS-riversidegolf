<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: application/json; charset=utf-8');

require_once './account_variable.php';

function fail($message, $httpCode = 400)
{
    http_response_code($httpCode);
    echo json_encode(array(
        'ok' => false,
        'error' => $message
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function defaultTestInput()
{
    return array(
        'table_names' => array('golf_price'),
        'price_names' => array(
            'VIP',
            '1', '2', '3', '5', '6', '7', '8', '9',
            '10', '11', '12', '13', '15', '16', '17', '18', '19',
            '20', '21', '22', '23', '25', '26', '27', '28', '29',
            '30', '31', '32', '33', '35', '36', '37', '38', '39',
            '50', '51', '52', '53', '55', '56', '57', '59',
            '60', '61', '62', '63', '65', '66', '67', '68', '69',
            '70', '71', '72', '73', '75', '76', '77', '78', '79',
            '80', '81', '82', '83', '84', '85',
            '100', '101', '102', '103'
        ),
        'effective_date' => '2026-03-01',
        'mode' => 'preview',
        'price_map' => array(
            'workday' => array('hourly' => 100, 'student' => 100, 'disabled' => 100),
            'holiday' => array('hourly' => 150, 'student' => 150, 'disabled' => 150),
            'holiday_19To22' => array('hourly' => 150, 'student' => 150, 'disabled' => 150)
        )
    );
}

function readInput()
{
    $raw = file_get_contents('php://input');
    if ($raw !== false && trim($raw) !== '') {
        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            fail('Invalid JSON payload: ' . json_last_error_msg());
        }
        return $decoded;
    }

    if (!empty($_POST)) {
        return $_POST;
    }

    return defaultTestInput();
}

function normalizeArray($value)
{
    if (is_array($value)) {
        return $value;
    }
    if (is_string($value)) {
        $value = trim($value);
        if ($value === '') {
            return array();
        }
        return array_values(array_filter(array_map('trim', explode(',', $value)), 'strlen'));
    }
    return array();
}

function normalizePriceMap($priceMap)
{
    if (is_array($priceMap)) {
        return $priceMap;
    }
    if (is_string($priceMap) && trim($priceMap) !== '') {
        $decoded = json_decode($priceMap, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            fail('price_map must be a JSON object when passed as string.');
        }
        return $decoded;
    }
    return array();
}

$input = readInput();

$tableNames = normalizeArray(isset($input['table_names']) ? $input['table_names'] : array('golf_price'));
$priceNames = normalizeArray(isset($input['price_names']) ? $input['price_names'] : null);
$effectiveDate = isset($input['effective_date']) ? trim((string)$input['effective_date']) : '';
$priceMap = normalizePriceMap(isset($input['price_map']) ? $input['price_map'] : null);
$mode = isset($input['mode']) ? strtolower(trim((string)$input['mode'])) : 'preview';

if (count($tableNames) === 0) {
    fail('table_names is required.');
}

if (count($priceNames) === 0) {
    fail('price_names is required.');
}

if ($effectiveDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $effectiveDate)) {
    fail('effective_date is required and must be YYYY-MM-DD.');
}

if ($mode !== 'preview' && $mode !== 'execute') {
    fail('mode must be either preview or execute.');
}

$requiredPeriods = array('workday', 'holiday', 'holiday_19To22');
$requiredIdentities = array('hourly', 'student', 'disabled');

foreach ($requiredPeriods as $period) {
    if (!isset($priceMap[$period]) || !is_array($priceMap[$period])) {
        fail('price_map missing period: ' . $period);
    }
    foreach ($requiredIdentities as $identity) {
        if (!isset($priceMap[$period][$identity]) || !is_numeric($priceMap[$period][$identity])) {
            fail('price_map missing numeric price for ' . $period . '.' . $identity);
        }
    }
}

foreach ($tableNames as $tableName) {
    if (!preg_match('/^[A-Za-z0-9_]+$/', $tableName)) {
        fail('Invalid table name: ' . $tableName);
    }
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    fail('Connection failed: ' . $conn->connect_error, 500);
}

$conn->set_charset('utf8mb4');

$resultSummary = array();
$allStatements = array();

foreach ($tableNames as $tableName) {
    $sql = "INSERT INTO `{$tableName}` (`price-name`, `period`, `identity`, `price`, `effective-date`) VALUES (?, ?, ?, ?, ?);" 
    // ."ON DUPLICATE KEY UPDATE `price` = VALUES(`price`), `effective-date` = VALUES(`effective-date`)"
    ;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $conn->close();
        fail('Failed to prepare statement for table ' . $tableName . ': ' . $conn->error, 500);
    }

    $rowCount = 0;
    $tableEscaped = '`' . str_replace('`', '``', $tableName) . '`';
    foreach ($priceNames as $priceName) {
        $priceName = trim((string)$priceName);
        if ($priceName === '') {
            continue;
        }

        foreach ($requiredPeriods as $period) {
            foreach ($requiredIdentities as $identity) {
                $price = (float)$priceMap[$period][$identity];
                $sqlPreview = "INSERT INTO {$tableEscaped} (`price-name`, `period`, `identity`, `price`, `effective-date`) VALUES ('" .
                    $conn->real_escape_string($priceName) . "', '" .
                    $conn->real_escape_string($period) . "', '" .
                    $conn->real_escape_string($identity) . "', " .
                    $price . ", '" .
                    $conn->real_escape_string($effectiveDate) . "');"
                    // ." ON DUPLICATE KEY UPDATE `price` = VALUES(`price`), `effective-date` = VALUES(`effective-date`);"
                    ;

                $allStatements[] = $sqlPreview;

                // if ($mode === 'execute') {
                //     $stmt->bind_param('sssds', $priceName, $period, $identity, $price, $effectiveDate);
                //     if (!$stmt->execute()) {
                //         $stmt->close();
                //         $conn->close();
                //         fail('Insert failed for table ' . $tableName . ', price-name ' . $priceName . ', ' . $period . ', ' . $identity . ': ' . $stmt->error, 500);
                //     }
                // }
                $rowCount++;
            }
        }
    }

    $stmt->close();

    $resultSummary[] = array(
        'table' => $tableName,
        'rows_processed' => $rowCount,
        'price_names_count' => count($priceNames)
    );
}

$conn->close();

echo json_encode(array(
    'ok' => true,
    'mode' => $mode,
    'executed' => $mode === 'execute',
    'effective_date' => $effectiveDate,
    'periods' => $requiredPeriods,
    'identities' => $requiredIdentities,
    'total_statements' => count($allStatements),
    'statements' => $allStatements,
    'tables' => $resultSummary,
    'example_payload' => $input
), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
