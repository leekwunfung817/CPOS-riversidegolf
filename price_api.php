<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: application/json');

// Start session and check authentication
session_start();

// Initialize response array
$response = array('success' => false, 'error' => '', 'data' => null);

// Note: Authentication is checked only for write operations (update)
// Read operations (get price) are allowed for all users

// Load database configuration
require_once './account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response['error'] = "Database connection failed: " . $conn->connect_error;
    http_response_code(500);
    echo json_encode($response);
    exit;
}

// Set charset
if (!$conn->set_charset("utf8")) {
    $response['error'] = "Error loading character set utf8: " . $conn->error;
    http_response_code(500);
    echo json_encode($response);
    exit;
}

// Determine request action
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

if ($action === 'get') {
    // Handle GET price request
    handleGetPrice($conn, $response);
} elseif ($action === 'update') {
    // Handle UPDATE price request
    handleUpdatePrice($conn, $response);
} elseif ($action === 'get_prices_list') {
    // Handle GET all prices for a table
    handleGetPricesList($conn, $response);
} elseif ($action === 'update_effective_date') {
    // Handle UPDATE effective date request
    handleUpdateEffectiveDate($conn, $response);
} else {
    $response['error'] = 'Invalid action parameter. Use: get, update, get_prices_list, or update_effective_date';
    http_response_code(400);
}

$conn->close();
echo json_encode($response);
exit;

/**
 * Get a single price from the database
 */
function handleGetPrice(&$conn, &$response) {
    // Required parameters
    $table = isset($_GET['table']) ? sanitizeInput($_GET['table']) : '';
    $period = isset($_GET['period']) ? sanitizeInput($_GET['period']) : '';
    $identity = isset($_GET['identity']) ? sanitizeInput($_GET['identity']) : '';
    $price_category = isset($_GET['price_category']) ? sanitizeInput($_GET['price_category']) : '';

    // Validate required parameters
    if (empty($table) || empty($period) || empty($price_category)) {
        $response['error'] = 'Missing required parameters: table, period, price_category';
        http_response_code(400);
        return;
    }

    // Validate table name against allowed tables
    $allowed_tables = array('golf_price', 'golf_price_2');
    if (!in_array($table, $allowed_tables)) {
        $response['error'] = 'Invalid table name';
        http_response_code(400);
        return;
    }

    // Build WHERE clause based on price category
    $sql_condition = buildPriceCondition($price_category);
    if ($sql_condition === false) {
        $response['error'] = 'Invalid price_category: use general_bay, sand_bay, pickle_ball, or vip';
        http_response_code(400);
        return;
    }

    // Build SQL query
    $sql = "SELECT `price` FROM `$table` WHERE `period`='$period' AND $sql_condition";
    
    // Add identity condition if specified and not for sand_bay/vip
    if (!empty($identity) && $price_category !== 'sand_bay' && $price_category !== 'vip') {
        $sql .= " AND `identity`='$identity'";
    }
    
    $sql .= " GROUP BY `price`";

    // Execute query
    $result = $conn->query($sql);
    
    if (!$result) {
        $response['error'] = 'Database query failed: ' . $conn->error;
        http_response_code(500);
        return;
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['data'] = array(
            'price' => $row['price'],
            'table' => $table,
            'period' => $period,
            'identity' => $identity,
            'price_category' => $price_category
        );
    } else {
        $response['success'] = true;
        $response['data'] = array(
            'price' => '-',
            'table' => $table,
            'period' => $period,
            'identity' => $identity,
            'price_category' => $price_category
        );
    }

    http_response_code(200);
}

/**
 * Update a price in the database
 */
function handleUpdatePrice(&$conn, &$response) {
    // Check authentication - only logged-in users can update prices
    if (!isset($_SESSION["management"]) || empty($_SESSION["management"])) {
        $response['error'] = 'Authentication required. Only logged-in users can update prices.';
        http_response_code(401);
        return;
    }
    
    // Get POST data
    $table = isset($_POST['table']) ? sanitizeInput($_POST['table']) : '';
    $period = isset($_POST['period']) ? sanitizeInput($_POST['period']) : '';
    $identity = isset($_POST['identity']) ? sanitizeInput($_POST['identity']) : '';
    $price_category = isset($_POST['price_category']) ? sanitizeInput($_POST['price_category']) : '';
    $new_price = isset($_POST['new_price']) ? floatval($_POST['new_price']) : 0;

    // Basic validation
    if (empty($table) || empty($period) || empty($price_category)) {
        $response['error'] = 'Missing required parameters: table, period, price_category';
        http_response_code(400);
        return;
    }

    if ($new_price <= 0) {
        $response['error'] = 'Invalid price value. Must be greater than 0.';
        http_response_code(400);
        return;
    }

    // Validate table name
    $allowed_tables = array('golf_price', 'golf_price_2');
    if (!in_array($table, $allowed_tables)) {
        $response['error'] = 'Invalid table name';
        http_response_code(400);
        return;
    }

    // Build WHERE clause based on price category
    $sql_condition = buildPriceCondition($price_category);
    if ($sql_condition === false) {
        $response['error'] = 'Invalid price_category';
        http_response_code(400);
        return;
    }

    // Build UPDATE query
    $sql = "UPDATE `$table` SET `price`=$new_price WHERE `period`='$period' AND $sql_condition";
    
    // Add identity condition if specified
    if (!empty($identity) && $price_category !== 'sand_bay' && $price_category !== 'vip') {
        $sql .= " AND `identity`='$identity'";
    }

    // Execute update
    // echo "Executing SQL: $sql"; // Debugging output
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['data'] = array(
            'message' => 'Price updated successfully',
            'affected_rows' => $conn->affected_rows,
            'sql' => $sql
        );
        http_response_code(200);
    } else {
        $response['error'] = 'Failed to update price: ' . $conn->error;
        http_response_code(500);
    }
}

/**
 * Get all prices for a given table and period
 */
function handleGetPricesList(&$conn, &$response) {
    $table = isset($_GET['table']) ? sanitizeInput($_GET['table']) : '';
    $period = isset($_GET['period']) ? sanitizeInput($_GET['period']) : '';

    if (empty($table)) {
        $response['error'] = 'Missing required parameter: table';
        http_response_code(400);
        return;
    }

    // Validate table name
    $allowed_tables = array('golf_price', 'golf_price_2');
    if (!in_array($table, $allowed_tables)) {
        $response['error'] = 'Invalid table name';
        http_response_code(400);
        return;
    }

    // Build query
    $sql = "SELECT `period`, `identity`, `price-name`, `price` FROM `$table`";
    
    if (!empty($period)) {
        $sql .= " WHERE `period`='$period'";
    }
    
    $sql .= " ORDER BY `period`, `price-name`, `identity`";

    $result = $conn->query($sql);
    
    if (!$result) {
        $response['error'] = 'Database query failed: ' . $conn->error;
        http_response_code(500);
        return;
    }

    $prices = array();
    while ($row = $result->fetch_assoc()) {
        $prices[] = $row;
    }

    $response['success'] = true;
    $response['data'] = $prices;
    http_response_code(200);
}

/**
 * Update effective date for all records in a table
 */
function handleUpdateEffectiveDate(&$conn, &$response) {
    // Check authentication - only logged-in users can update effective dates
    if (!isset($_SESSION["management"]) || empty($_SESSION["management"])) {
        $response['error'] = 'Authentication required. Only logged-in users can update effective dates.';
        http_response_code(401);
        return;
    }
    
    // Get POST data
    $table = isset($_POST['table']) ? sanitizeInput($_POST['table']) : '';
    $old_date = isset($_POST['old_date']) ? sanitizeInput($_POST['old_date']) : '';
    $new_date = isset($_POST['new_date']) ? sanitizeInput($_POST['new_date']) : '';

    // Validate required parameters
    if (empty($table) || empty($old_date) || empty($new_date)) {
        $response['error'] = 'Missing required parameters: table, old_date, new_date';
        http_response_code(400);
        return;
    }

    // Validate table name
    $allowed_tables = array('golf_price', 'golf_price_2');
    if (!in_array($table, $allowed_tables)) {
        $response['error'] = 'Invalid table name';
        http_response_code(400);
        return;
    }

    // Validate date format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $new_date)) {
        $response['error'] = 'Invalid date format. Use YYYY-MM-DD';
        http_response_code(400);
        return;
    }

    // Update all records with the old effective date to the new date
    $sql = "UPDATE `$table` SET `effective-date` = '$new_date' WHERE `effective-date` = '$old_date'";
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        $affected_rows = $conn->affected_rows;
        $response['success'] = true;
        $response['message'] = "Effective date updated successfully. $affected_rows rows affected.";
        $response['data'] = array(
            'table' => $table,
            'old_date' => $old_date,
            'new_date' => $new_date,
            'affected_rows' => $affected_rows,
            'sql' => $sql
        );
        http_response_code(200);
    } else {
        $response['error'] = 'Database update failed: ' . $conn->error;
        http_response_code(500);
    }
}

/**
 * Build SQL condition based on price category
 */
function buildPriceCondition($price_category) {
    switch ($price_category) {
        case 'general_bay':
            return "`price-name` >= 5 AND `price-name` <= 85";
        case 'sand_bay':
            return "`price-name` >= 1 AND `price-name` <= 2";
        case 'pickle_ball':
            return "`price-name` >= 100 AND `price-name` < 200";
        case 'vip':
            return "`price-name` = 'VIP'";
        default:
            return false;
    }
}

/**
 * Sanitize input to prevent SQL injection
 */
function sanitizeInput($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

?>
