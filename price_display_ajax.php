<?php 
/**
 * PRICE DISPLAY - AJAX VERSION
 * 
 * This file demonstrates how to integrate the new price_api.php with AJAX
 * All prices are loaded dynamically via AJAX calls
 * Only authenticated users can edit prices
 * 
 * Integration Instructions:
 * 1. Include price_manager.js before closing </body>
 * 2. Replace price display cells with: <td class="price-cell" data-action="loadPrice" ...></td>
 * 3. Use JavaScript to load prices: priceManager.loadPriceIntoCell(element, table, period, category, identity)
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start session and check authentication
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION["management"]) && !empty($_SESSION["management"]);

require_once './account_variable.php';

// Create connection for effective-date display only (not for price fetching)
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$conn->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $conn->error);
}

// Define price table names
$price_tables = array('golf_price', 'golf_price_2');

// Get effective dates for display
function getEffectiveDates($conn, $price_table_name) {
    $result = $conn->query("
        SELECT `effective-date` 
        FROM `$price_table_name`
        GROUP BY `effective-date`
        ORDER BY `effective-date` DESC
    ");
    
    $dates = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dates[] = $row['effective-date'];
        }
    }
    return $dates;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Price Display - AJAX Version</title>
    <style type="text/css">
        html {
            padding: 30px;
            background-color: CornflowerBlue;
        }
        
        body {
            background-color: white;
            border-style: outset;
            font-family: Arial, sans-serif;
        }
        
        table {
            font-size: 1.2em;
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 10px;
            min-height: 40px;
        }
        
        th {
            background-color: #4CAF50;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        tr:hover {
            background-color: #ddd;
        }
        
        h1 {
            text-align: center;
            color: #333;
        }
        
        h2 {
            color: #666;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        
        .login-notice {
            background-color: <?php echo $isLoggedIn ? '#d4edda' : '#f8d7da'; ?>;
            border: 1px solid <?php echo $isLoggedIn ? '#c3e6cb' : '#f5c6cb'; ?>;
            color: <?php echo $isLoggedIn ? '#155724' : '#721c24'; ?>;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        
        .price-cell {
            background-color: #e3f2fd;
            user-select: none;
        }
        
        .price-cell:hover {
            background-color: #fff9c4;
        }
    </style>
</head>
<body>

<div class="login-notice">
    <?php if ($isLoggedIn): ?>
        <strong>✓ Logged In</strong><br>
        User: <strong><?php echo htmlspecialchars($_SESSION['name2'] ?? 'User'); ?></strong> 
        (<?php echo htmlspecialchars($_SESSION['identity'] ?? 'unknown'); ?>)<br>
        <em>Click on prices to edit them</em>
    <?php else: ?>
        <strong>⚠ Not Logged In</strong><br>
        You must be logged in to edit prices. Prices are displayed in read-only mode.
    <?php endif; ?>
</div>

<?php
foreach ($price_tables as $price_table_name) {
    $dates = getEffectiveDates($conn, $price_table_name);
?>

<h2>
    &darr; Effective Date: 
    <?php 
    foreach ($dates as $date) {
        echo " [ " . htmlspecialchars($date) . " ] ";
    }
    ?>
    &darr;
</h2>

<!-- GOLF COURSE PRICING TABLE -->
<table>
    <tr>
        <td colspan="4"><h1>白石高球練習場 - 價格表 
            <br>WhiteHead Golf Club - Price Table</h1></td>
    </tr>
    <tr>
        <td></td>
        <th>正價<br>Regular Price</th>
        <th>學生優惠<br>Student Price</th>
        <th>傷健人士優惠<br>Disabled Price</th>
    </tr>
    
    <!-- Monday 13:00-22:00 / Tue-Fri 08:00-22:00 -->
    <tr>
        <th>星期一 (13:00-22:00)<hr>星期二至星期五 (08:00-22:00)</th>
        <td class="price-cell" id="workday-hourly-golf"></td>
        <td class="price-cell" id="workday-student-golf"></td>
        <td class="price-cell" id="workday-disabled-golf"></td>
    </tr>
    
    <!-- Saturday/Sunday/Holiday 08:00-18:59 -->
    <tr>
        <th>星期六日及公眾假期<br>(08:00-18:59)</th>
        <td class="price-cell" id="holiday-hourly-golf"></td>
        <td class="price-cell" id="holiday-student-golf"></td>
        <td class="price-cell" id="holiday-disabled-golf"></td>
    </tr>
    
    <!-- Saturday/Sunday/Holiday 19:00-22:00 -->
    <tr>
        <th>星期六日及公眾假期<br>(19:00-22:00)</th>
        <td class="price-cell" id="holiday19-hourly-golf"></td>
        <td class="price-cell" id="holiday19-student-golf"></td>
        <td class="price-cell" id="holiday19-disabled-golf"></td>
    </tr>
    
    <!-- Sand Bay Workday -->
    <tr>
        <th>沙地球道<br>星期一至星期五</th>
        <td colspan="3" class="price-cell" id="workday-sandbay"></td>
    </tr>
    
    <!-- Sand Bay Weekend/Holiday -->
    <tr>
        <th>沙地球道<br>星期六日及公眾假期</th>
        <td colspan="3" class="price-cell" id="holiday-sandbay"></td>
    </tr>
    
    <!-- VIP Room Workday -->
    <tr>
        <th>VIP房<br>星期一至星期五</th>
        <td colspan="3" class="price-cell" id="workday-vip"></td>
    </tr>
    
    <!-- VIP Room Weekend/Holiday 08:00-19:00 -->
    <tr>
        <th>VIP房<br>星期六日及公眾假期<br>(08:00-19:00)</th>
        <td colspan="3" class="price-cell" id="holiday-vip"></td>
    </tr>
    
    <!-- VIP Room Weekend/Holiday 19:00-22:00 -->
    <tr>
        <th>VIP房<br>星期六日及公眾假期<br>(19:00-22:00)</th>
        <td colspan="3" class="price-cell" id="holiday19-vip"></td>
    </tr>
</table>

<!-- PICKLEBALL PRICING TABLE -->
<table>
    <tr>
        <td colspan="4"><h1>匹克球 練習場 - 價格表 
            <br>Pickleball Court - Price Table</h1></td>
    </tr>
    <tr>
        <td></td>
        <th>正價<br>Regular Price</th>
        <th>學生優惠<br>Student Price</th>
        <th>傷健人士優惠<br>Disabled Price</th>
    </tr>
    
    <!-- Monday 13:00-22:00 / Tue-Fri 08:00-22:00 -->
    <tr>
        <th>星期一 (13:00-22:00)<hr>星期二至星期五 (08:00-22:00)</th>
        <td class="price-cell" id="workday-hourly-pickle"></td>
        <td class="price-cell" id="workday-student-pickle"></td>
        <td class="price-cell" id="workday-disabled-pickle"></td>
    </tr>
    
    <!-- Saturday/Sunday/Holiday 08:00-18:59 -->
    <tr>
        <th>星期六日及公眾假期<br>(08:00-18:59)</th>
        <td class="price-cell" id="holiday-hourly-pickle"></td>
        <td class="price-cell" id="holiday-student-pickle"></td>
        <td class="price-cell" id="holiday-disabled-pickle"></td>
    </tr>
    
    <!-- Saturday/Sunday/Holiday 19:00-22:00 -->
    <tr>
        <th>星期六日及公眾假期<br>(19:00-22:00)</th>
        <td class="price-cell" id="holiday19-hourly-pickle"></td>
        <td class="price-cell" id="holiday19-student-pickle"></td>
        <td class="price-cell" id="holiday19-disabled-pickle"></td>
    </tr>
</table>

<?php 
} // End foreach price_tables

$conn->close();
?>

<!-- Load Price Manager Script -->
<script src="./price_manager.js"></script>

<script type="text/javascript">
// Configuration for each price cell
const priceConfig = [
    // Golf Course - Workday
    { id: 'workday-hourly-golf', table: 'golf_price', period: 'workday', category: 'general_bay', identity: 'hourly' },
    { id: 'workday-student-golf', table: 'golf_price', period: 'workday', category: 'general_bay', identity: 'student' },
    { id: 'workday-disabled-golf', table: 'golf_price', period: 'workday', category: 'general_bay', identity: 'disabled' },
    
    // Golf Course - Holiday Day
    { id: 'holiday-hourly-golf', table: 'golf_price', period: 'holiday', category: 'general_bay', identity: 'hourly' },
    { id: 'holiday-student-golf', table: 'golf_price', period: 'holiday', category: 'general_bay', identity: 'student' },
    { id: 'holiday-disabled-golf', table: 'golf_price', period: 'holiday', category: 'general_bay', identity: 'disabled' },
    
    // Golf Course - Holiday Evening
    { id: 'holiday19-hourly-golf', table: 'golf_price', period: 'holiday_19To22', category: 'general_bay', identity: 'hourly' },
    { id: 'holiday19-student-golf', table: 'golf_price', period: 'holiday_19To22', category: 'general_bay', identity: 'student' },
    { id: 'holiday19-disabled-golf', table: 'golf_price', period: 'holiday_19To22', category: 'general_bay', identity: 'disabled' },
    
    // Sand Bay
    { id: 'workday-sandbay', table: 'golf_price', period: 'workday', category: 'sand_bay', identity: '' },
    { id: 'holiday-sandbay', table: 'golf_price', period: 'holiday', category: 'sand_bay', identity: '' },
    
    // VIP Room
    { id: 'workday-vip', table: 'golf_price', period: 'workday', category: 'vip', identity: '' },
    { id: 'holiday-vip', table: 'golf_price', period: 'holiday', category: 'vip', identity: '' },
    { id: 'holiday19-vip', table: 'golf_price', period: 'holiday_19To22', category: 'vip', identity: '' },
    
    // Pickleball - Workday
    { id: 'workday-hourly-pickle', table: 'golf_price', period: 'workday', category: 'pickle_ball', identity: 'hourly' },
    { id: 'workday-student-pickle', table: 'golf_price', period: 'workday', category: 'pickle_ball', identity: 'student' },
    { id: 'workday-disabled-pickle', table: 'golf_price', period: 'workday', category: 'pickle_ball', identity: 'disabled' },
    
    // Pickleball - Holiday Day
    { id: 'holiday-hourly-pickle', table: 'golf_price', period: 'holiday', category: 'pickle_ball', identity: 'hourly' },
    { id: 'holiday-student-pickle', table: 'golf_price', period: 'holiday', category: 'pickle_ball', identity: 'student' },
    { id: 'holiday-disabled-pickle', table: 'golf_price', period: 'holiday', category: 'pickle_ball', identity: 'disabled' },
    
    // Pickleball - Holiday Evening
    { id: 'holiday19-hourly-pickle', table: 'golf_price', period: 'holiday_19To22', category: 'pickle_ball', identity: 'hourly' },
    { id: 'holiday19-student-pickle', table: 'golf_price', period: 'holiday_19To22', category: 'pickle_ball', identity: 'student' },
    { id: 'holiday19-disabled-pickle', table: 'golf_price', period: 'holiday_19To22', category: 'pickle_ball', identity: 'disabled' },
];

// Load all prices when page loads
window.addEventListener('DOMContentLoaded', function() {
    priceConfig.forEach(config => {
        const element = document.getElementById(config.id);
        if (element) {
            priceManager.loadPriceIntoCell(
                element,
                config.table,
                config.period,
                config.category,
                config.identity
            );
        }
    });
});
</script>

</body>
</html>
