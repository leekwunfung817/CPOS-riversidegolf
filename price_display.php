<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start session and check authentication
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION["management"]) && !empty($_SESSION["management"]);

require_once './account_variable.php';

// Create connection to get effective dates only
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
    <title>Price Display - AJAX Version</title>
    <style type="text/css">
        html {
            padding: 30px;
            background-color: CornflowerBlue;
        }
        
        body {
            background-color: white;
            border-style: outset;
        }
        
        table {
            font-size: 2em;
            width: 100%;
        }
        
        th, td {
            border-style: inset;
            text-align: center;
            padding: 8px;
        }
        
        .login-notice {
            background-color: <?php echo $isLoggedIn ? '#d4edda' : '#f8d7da'; ?>;
            border: 1px solid <?php echo $isLoggedIn ? '#c3e6cb' : '#f5c6cb'; ?>;
            color: <?php echo $isLoggedIn ? '#155724' : '#721c24'; ?>;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-size: 1.1em;
        }
        
        .price-cell {
            background-color: #e3f2fd;
            user-select: none;
        }
        
        .price-cell:hover {
            background-color: #ffffcc;
        }
        
        .effective-date {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 5px;
            transition: all 0.2s ease;
        }
        
        .effective-date-editable {
            background-color: #e7f3ff;
            border: 2px solid #0066cc;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }
        
        .effective-date-editable:hover {
            background-color: #fff3cd;
            border-color: #856404;
            color: #856404;
            transform: scale(1.05);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .effective-date-editable:active {
            transform: scale(0.98);
        }
        
        .effective-date-editing {
            background-color: #d1ecf1;
            padding: 5px;
        }
        
        #debug-panel {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: #0f0;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-width: 400px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 9999;
        }
        
        #debug-panel h4 {
            margin: 0 0 5px 0;
            color: #ff0;
        }
    </style>
</head>
<body>

<div id="debug-panel" style="display: none;">
    <h4>🐛 Debug Info</h4>
    <div id="debug-content"></div>
</div>

    <?php if ($isLoggedIn): ?>
<div class="login-notice">
        <strong>✓ Logged In</strong><br>
        User: <strong><?php echo htmlspecialchars($_SESSION['name2'] ?? 'User'); ?></strong> 
        (<?php echo htmlspecialchars($_SESSION['identity'] ?? 'unknown'); ?>)<br>
        <em>Click on prices or effective dates to edit them</em>
</div>
    <?php else: ?>
<div class="login-notice" style="display: none;">
        <strong>⚠ Not Logged In</strong><br>
        You must be logged in to edit prices and effective dates. Data is displayed in read-only mode.
</div>
    <?php endif; ?>

<?php
$tableIndex = 0;
foreach ($price_tables as $price_table_name) {
    $tableIndex++;
    $tablePrefix = ($tableIndex === 1) ? '' : 'alt-';
    $dates = getEffectiveDates($conn, $price_table_name);
?>

<h2>
    &darr; 生效日期 Effective Date: 
    <?php 
    foreach ($dates as $dateIndex => $date) {
        $dateId = $tablePrefix . "effective-date-" . $dateIndex;
        $editableClass = $isLoggedIn ? 'effective-date-editable' : '';
        $editableTitle = $isLoggedIn ? 'Click to edit this date' : 'Login to edit dates';
        echo "<span id='{$dateId}' class='effective-date {$editableClass}' title='{$editableTitle}' data-table='{$price_table_name}' data-date='" . htmlspecialchars($date) . "'>" . htmlspecialchars($date) . "</span> ";
    }
    ?>
    &darr;
</h2>

<!-- GOLF COURSE PRICING TABLE -->
<table>
    <tr>
        <td colspan="4"><h1>白石高球練習場 - 價格表 
            <!-- <hr> WhiteHead Golf Club - Price table -->
        </h1></td>
    </tr>
    <tr>
        <td></td>
        <th>正價
            <!-- <br>Regular Price -->
        </th>
        <th>學生優惠
            <!-- <br>Student Price -->
        </th>
        <th>傷健人士優惠
            <!-- <br>Disabled Price -->
        </th>
    </tr>
    <tr>
        <th>星期一 
            <!-- Monday -->
            <br>(13:00-22:00)<hr>星期二至星期五
            <!-- <br>Tuesday to Friday -->
            <br>(08:00-22:00)</th>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-workday-hourly"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-workday-student"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-workday-disabled"></td>
    </tr>

    <tr>
        <th>星期六日及公眾假期
            <!-- <br>Saturday, Sunday, and public holiday  -->
            <br>(08:00-18:59)</th>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday-hourly"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday-student"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday-disabled"></td>
    </tr>

    <tr>
        <th>星期六日及公眾假期
            <!-- <br>Saturday, Sunday, and public holiday  -->

            <br>(19:00-22:00)</th>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday19-hourly"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday19-student"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday19-disabled"></td>
    </tr>

    <tr>
        <th>沙地球道<br>星期一至星期五
            <!-- <br>Sand Bay<br>Monday to Friday -->
        </th>
        <td colspan="3" class="price-cell" id="<?php echo $tablePrefix; ?>golf-workday-sandbay"></td>
    </tr>


    <tr>
        <th>沙地球道<br>星期六日及公眾假期
            <!-- <br>Sand Bay<br>Saturday, Sunday, and public holiday -->
        </th>
        <td colspan="3" class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday-sandbay"></td>
    </tr>


    <tr>
        <th>VIP房<br>星期一至星期五
            <!-- <br>VIP room<br>Monday to Friday -->
        </th>
        <td colspan="3" class="price-cell" id="<?php echo $tablePrefix; ?>golf-workday-vip"></td>
    </tr>


    <tr>
        <th>VIP房<br>星期六日及公眾假期<br>(08:00-19:00)
            <!-- <br>VIP room<br>Saturday, Sunday, and public holiday -->
        </th>
        <td colspan="3" class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday-vip"></td>
    </tr>


    <tr>
        <th>VIP房<br>星期六日及公眾假期<br>(19:00-22:00)
            <!-- <br>VIP room<br>Saturday, Sunday, and public holiday -->
        </th>
        <td colspan="3" class="price-cell" id="<?php echo $tablePrefix; ?>golf-holiday19-vip"></td>
    </tr>
</table>




<table>
    <tr>
        <td colspan="4"><h1>匹克球 練習場 - 價格表 
            <!-- <hr> WhiteHead Golf Club - Price table -->
        </h1></td>
    </tr>

    <tr>
        <td></td>
        <th>正價
            <!-- <br>Regular Price -->
        </th>
        <th>學生優惠
            <!-- <br>Student Price -->
        </th>
        <th>傷健人士優惠
            <!-- <br>Disabled Price -->
        </th>
    </tr>


    <tr>
        <th>星期一 
            <!-- Monday -->
            <br>(13:00-22:00)<hr>星期二至星期五
            <!-- <br>Tuesday to Friday -->
            <br>(08:00-22:00)</th>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-workday-hourly"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-workday-student"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-workday-disabled"></td>
    </tr>

    <tr>
        <th>星期六日及公眾假期
            <!-- <br>Saturday, Sunday, and public holiday  -->
            <br>(08:00-18:59)</th>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-holiday-hourly"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-holiday-student"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-holiday-disabled"></td>
    </tr>

    <tr>
        <th>星期六日及公眾假期
            <!-- <br>Saturday, Sunday, and public holiday  -->

            <br>(19:00-22:00)</th>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-holiday19-hourly"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-holiday19-student"></td>
        <td class="price-cell" id="<?php echo $tablePrefix; ?>pickle-holiday19-disabled"></td>
    </tr>

</table>

<?php 
} // End foreach price_tables

$conn->close();
?>

<br>
<!-- 
<script src="./price_manager.js"></script> -->

<script>
/**
 * Price Display AJAX Manager
 * Handles AJAX calls for fetching and updating golf prices
 * Only allows updates from logged-in users
 */

class PriceManager {
    constructor() {
        this.apiUrl = './price_api.php';
        this.editingCell = null;
        this.originalPrice = null;
    }

    /**
     * Fetch a single price from the API
     * @param {string} table - Database table name (golf_price, golf_price_2)
     * @param {string} period - Period (workday, holiday, holiday_19To22)
     * @param {string} priceCategory - Category (general_bay, sand_bay, pickle_ball, vip)
     * @param {string} identity - User type (hourly, student, disabled)
     * @param {object} cellElement - The TD element to update
     */
    fetchPrice(table, period, priceCategory, identity = '') {
        const params = new URLSearchParams({
            action: 'get',
            table: table,
            period: period,
            price_category: priceCategory
        });

        if (identity) {
            params.append('identity', identity);
        }

        return fetch(`${this.apiUrl}?${params}`)
            .then(response => {
                if (response.status === 401) {
                    console.error('Unauthorized: User not logged in');
                    throw new Error('User not logged in');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                // console.log('Raw API Response:', text);
                return JSON.parse(text);
            })
            .then(data => {
                if (!data.success) {
                    console.error('API Error:', data.error);
                    throw new Error(data.error);
                }
                return data.data;
            })
            .catch(error => {
                console.error('Error fetching price:', error);
                return null;
            });
    }

    /**
     * Load price into a TD element using AJAX
     * @param {HTMLElement} cellElement - The TD element to populate
     * @param {string} table - Database table name
     * @param {string} period - Time period
     * @param {string} priceCategory - Price category
     * @param {string} identity - User type
     */
    loadPriceIntoCell(cellElement, table, period, priceCategory, identity = '') {
        // Show loading indicator
        cellElement.innerHTML = '<span class="price-loading">⏳</span>';
        cellElement.dataset.table = table;
        cellElement.dataset.period = period;
        cellElement.dataset.priceCategory = priceCategory;
        cellElement.dataset.identity = identity;

        this.fetchPrice(table, period, priceCategory, identity)
            .then(data => {
                if (data) {
                    const displayPrice = data.price === '-' ? '-' : `$${data.price}`;
                    cellElement.innerHTML = displayPrice;
                    cellElement.classList.add('price-cell-editable');
                    cellElement.style.cursor = 'pointer';
                    
                    // Add click event to allow editing
                    cellElement.addEventListener('click', (e) => {
                        this.enablePriceEdit(cellElement);
                    });
                } else {
                    cellElement.innerHTML = '-';
                }
            });
    }

    /**
     * Enable inline editing of price
     * @param {HTMLElement} cellElement - The TD element being edited
     */
    enablePriceEdit(cellElement) {
        // If already editing, return
        if (this.editingCell === cellElement) {
            return;
        }

        // Save previous edit if exists
        if (this.editingCell) {
            this.cancelEdit();
        }

        this.editingCell = cellElement;
        this.originalPrice = cellElement.textContent.replace('$', '').trim();

        // Create input and buttons
        const currentPrice = this.originalPrice === '-' ? '' : this.originalPrice;
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'price-edit-group';
        inputGroup.style.cssText = 'display: flex; gap: 5px; align-items: center;';

        const input = document.createElement('input');
        input.type = 'number';
        input.className = 'price-input';
        input.value = currentPrice;
        input.placeholder = 'Enter price';
        input.style.cssText = 'width: 70px; padding: 4px;';
        input.step = '0.01';
        input.min = '0';

        const saveBtn = document.createElement('button');
        saveBtn.textContent = '✓';
        saveBtn.className = 'price-save-btn';
        saveBtn.style.cssText = 'padding: 4px 8px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;';
        saveBtn.onclick = (e) => {
            e.stopPropagation();
            this.savePrice(cellElement, input.value);
        };

        const cancelBtn = document.createElement('button');
        cancelBtn.textContent = '✕';
        cancelBtn.className = 'price-cancel-btn';
        cancelBtn.style.cssText = 'padding: 4px 8px; background-color: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;';
        cancelBtn.onclick = (e) => {
            e.stopPropagation();
            this.cancelEdit();
        };

        inputGroup.appendChild(input);
        inputGroup.appendChild(saveBtn);
        inputGroup.appendChild(cancelBtn);

        cellElement.innerHTML = '';
        cellElement.appendChild(inputGroup);
        input.focus();
        input.select();

        // Allow Enter to save and Escape to cancel
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                this.savePrice(cellElement, input.value);
            } else if (e.key === 'Escape') {
                this.cancelEdit();
            }
        });
    }

    /**
     * Save the updated price via API
     * @param {HTMLElement} cellElement - The TD element being edited
     * @param {string} newPrice - The new price value
     */
    savePrice(cellElement, newPrice) {
        if (!newPrice || parseFloat(newPrice) <= 0) {
            alert('Please enter a valid price greater than 0');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('table', cellElement.dataset.table);
        formData.append('period', cellElement.dataset.period);
        formData.append('price_category', cellElement.dataset.priceCategory);
        formData.append('identity', cellElement.dataset.identity);
        formData.append('new_price', newPrice);

        fetch(this.apiUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.status === 401) {
                    throw new Error('User not logged in');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                // console.log('Raw API Response:', text);
                return JSON.parse(text);
            })
            .then(data => {
                if (data.success) {
                    // Reload the price display
                    cellElement.innerHTML = `$${newPrice}`;
                    cellElement.classList.add('price-updated');
                    this.editingCell = null;
                    
                    // Remove highlight after 2 seconds
                    setTimeout(() => {
                        cellElement.classList.remove('price-updated');
                    }, 2000);

                    console.log('Price updated successfully');
                } else {
                    throw new Error(data.error);
                }
            })
            .catch(error => {
                console.error('Error saving price:', error);
                alert('Error saving price: ' + error.message);
                this.cancelEdit();
            });
    }

    /**
     * Cancel price editing
     */
    cancelEdit() {
        if (this.editingCell) {
            const displayPrice = this.originalPrice === '-' ? '-' : `$${this.originalPrice}`;
            this.editingCell.innerHTML = displayPrice;
            this.editingCell = null;
            this.originalPrice = null;
        }
    }

    /**
     * Load all prices for a table via AJAX
     * @param {string} table - Database table name
     * @param {string} period - Optional time period to filter
     */
    loadAllPrices(table, period = '') {
        const params = new URLSearchParams({
            action: 'get_prices_list',
            table: table
        });

        if (period) {
            params.append('period', period);
        }

        return fetch(`${this.apiUrl}?${params}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error);
                }
                return data.data;
            })
            .catch(error => {
                console.error('Error loading all prices:', error);
                return null;
            });
    }
}

// Create global instance
const priceManager = new PriceManager();

// CSS for styling
const style = document.createElement('style');
style.textContent = `
    .price-cell-editable {
        user-select: none;
        transition: background-color 0.2s ease;
    }

    .price-cell-editable:hover {
        background-color: #ffffcc;
    }

    .price-loading {
        animation: spin 1s linear infinite;
        display: inline-block;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .price-updated {
        background-color: #d4edda !important;
        animation: highlight 0.5s ease;
    }

    @keyframes highlight {
        0% { background-color: #90ee90; }
        100% { background-color: #d4edda; }
    }

    .price-input {
        border: 2px solid #007bff;
        border-radius: 3px;
        font-size: 14px;
    }

    .price-input:focus {
        outline: none;
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
    }

    .price-edit-group {
        white-space: nowrap;
    }
`;
document.head.appendChild(style);


</script>
<script type="text/javascript">
// Check if user is logged in (from PHP)
const isUserLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

// Custom price loader that includes format/unit text
function loadPriceWithFormat(cellElement, table, period, category, identity, format) {
    // Show loading indicator
    cellElement.innerHTML = '<span class="price-loading">⏳</span>';
    cellElement.dataset.table = table;
    cellElement.dataset.period = period;
    cellElement.dataset.priceCategory = category;
    cellElement.dataset.identity = identity;
    cellElement.dataset.format = format;

    priceManager.fetchPrice(table, period, category, identity)
        .then(data => {
            if (data) {
                let displayPrice = data.price;
                // Divide by 2 for half-hour pricing
                if (format.includes('半小時') && displayPrice !== '-') {
                    displayPrice = displayPrice / 2;
                }
                const priceText = displayPrice === '-' ? '-' : `$${displayPrice}${format}`;
                cellElement.innerHTML = priceText;
                
                // Only allow editing if user is logged in
                if (isUserLoggedIn) {
                    cellElement.classList.add('price-cell-editable');
                    cellElement.style.cursor = 'pointer';
                    
                    // Add click event to allow editing
                    cellElement.addEventListener('click', (e) => {
                        priceManager.enablePriceEdit(cellElement);
                    });
                } else {
                    // Remove hover effect for non-logged-in users
                    cellElement.style.cursor = 'default';
                }
            } else {
                cellElement.innerHTML = '-';
            }
        });
}

// Configuration for golf_price table
const priceConfigGolf1 = [
    // Golf Course - Workday
    { id: 'golf-workday-hourly', table: 'golf_price', period: 'workday', category: 'general_bay', identity: 'hourly', format: '元/小時' },
    { id: 'golf-workday-student', table: 'golf_price', period: 'workday', category: 'general_bay', identity: 'student', format: '元/小時' },
    { id: 'golf-workday-disabled', table: 'golf_price', period: 'workday', category: 'general_bay', identity: 'disabled', format: '元/小時' },
    
    // Golf Course - Holiday Day
    { id: 'golf-holiday-hourly', table: 'golf_price', period: 'holiday', category: 'general_bay', identity: 'hourly', format: '元/小時' },
    { id: 'golf-holiday-student', table: 'golf_price', period: 'holiday', category: 'general_bay', identity: 'student', format: '元/小時' },
    { id: 'golf-holiday-disabled', table: 'golf_price', period: 'holiday', category: 'general_bay', identity: 'disabled', format: '元/小時' },
    
    // Golf Course - Holiday Evening
    { id: 'golf-holiday19-hourly', table: 'golf_price', period: 'holiday_19To22', category: 'general_bay', identity: 'hourly', format: '元/小時' },
    { id: 'golf-holiday19-student', table: 'golf_price', period: 'holiday_19To22', category: 'general_bay', identity: 'student', format: '元/小時' },
    { id: 'golf-holiday19-disabled', table: 'golf_price', period: 'holiday_19To22', category: 'general_bay', identity: 'disabled', format: '元/小時' },
    
    // Sand Bay
    { id: 'golf-workday-sandbay', table: 'golf_price', period: 'workday', category: 'sand_bay', identity: '', format: '元/半小時' },
    { id: 'golf-holiday-sandbay', table: 'golf_price', period: 'holiday', category: 'sand_bay', identity: '', format: '元/半小時' },
    
    // VIP Room
    { id: 'golf-workday-vip', table: 'golf_price', period: 'workday', category: 'vip', identity: '', format: '元/小時' },
    { id: 'golf-holiday-vip', table: 'golf_price', period: 'holiday', category: 'vip', identity: '', format: '元/小時' },
    { id: 'golf-holiday19-vip', table: 'golf_price', period: 'holiday_19To22', category: 'vip', identity: '', format: '元/小時' },
    
    // Pickleball - Workday
    { id: 'pickle-workday-hourly', table: 'golf_price', period: 'workday', category: 'pickle_ball', identity: 'hourly', format: '元/小時' },
    { id: 'pickle-workday-student', table: 'golf_price', period: 'workday', category: 'pickle_ball', identity: 'student', format: '元/小時' },
    { id: 'pickle-workday-disabled', table: 'golf_price', period: 'workday', category: 'pickle_ball', identity: 'disabled', format: '元/小時' },
    
    // Pickleball - Holiday Day
    { id: 'pickle-holiday-hourly', table: 'golf_price', period: 'holiday', category: 'pickle_ball', identity: 'hourly', format: '元/小時' },
    { id: 'pickle-holiday-student', table: 'golf_price', period: 'holiday', category: 'pickle_ball', identity: 'student', format: '元/小時' },
    { id: 'pickle-holiday-disabled', table: 'golf_price', period: 'holiday', category: 'pickle_ball', identity: 'disabled', format: '元/小時' },
    
    // Pickleball - Holiday Evening
    { id: 'pickle-holiday19-hourly', table: 'golf_price', period: 'holiday_19To22', category: 'pickle_ball', identity: 'hourly', format: '元/小時' },
    { id: 'pickle-holiday19-student', table: 'golf_price', period: 'holiday_19To22', category: 'pickle_ball', identity: 'student', format: '元/小時' },
    { id: 'pickle-holiday19-disabled', table: 'golf_price', period: 'holiday_19To22', category: 'pickle_ball', identity: 'disabled', format: '元/小時' },
];

// Configuration for golf_price_2 table (alternative/second entry)
const priceConfigGolf2 = [
    // Golf Course - Workday
    { id: 'alt-golf-workday-hourly', table: 'golf_price_2', period: 'workday', category: 'general_bay', identity: 'hourly', format: '元/小時' },
    { id: 'alt-golf-workday-student', table: 'golf_price_2', period: 'workday', category: 'general_bay', identity: 'student', format: '元/小時' },
    { id: 'alt-golf-workday-disabled', table: 'golf_price_2', period: 'workday', category: 'general_bay', identity: 'disabled', format: '元/小時' },
    
    // Golf Course - Holiday Day
    { id: 'alt-golf-holiday-hourly', table: 'golf_price_2', period: 'holiday', category: 'general_bay', identity: 'hourly', format: '元/小時' },
    { id: 'alt-golf-holiday-student', table: 'golf_price_2', period: 'holiday', category: 'general_bay', identity: 'student', format: '元/小時' },
    { id: 'alt-golf-holiday-disabled', table: 'golf_price_2', period: 'holiday', category: 'general_bay', identity: 'disabled', format: '元/小時' },
    
    // Golf Course - Holiday Evening
    { id: 'alt-golf-holiday19-hourly', table: 'golf_price_2', period: 'holiday_19To22', category: 'general_bay', identity: 'hourly', format: '元/小時' },
    { id: 'alt-golf-holiday19-student', table: 'golf_price_2', period: 'holiday_19To22', category: 'general_bay', identity: 'student', format: '元/小時' },
    { id: 'alt-golf-holiday19-disabled', table: 'golf_price_2', period: 'holiday_19To22', category: 'general_bay', identity: 'disabled', format: '元/小時' },
    
    // Sand Bay
    { id: 'alt-golf-workday-sandbay', table: 'golf_price_2', period: 'workday', category: 'sand_bay', identity: '', format: '元/半小時' },
    { id: 'alt-golf-holiday-sandbay', table: 'golf_price_2', period: 'holiday', category: 'sand_bay', identity: '', format: '元/半小時' },
    
    // VIP Room
    { id: 'alt-golf-workday-vip', table: 'golf_price_2', period: 'workday', category: 'vip', identity: '', format: '元/小時' },
    { id: 'alt-golf-holiday-vip', table: 'golf_price_2', period: 'holiday', category: 'vip', identity: '', format: '元/小時' },
    { id: 'alt-golf-holiday19-vip', table: 'golf_price_2', period: 'holiday_19To22', category: 'vip', identity: '', format: '元/小時' },
    
    // Pickleball - Workday
    { id: 'alt-pickle-workday-hourly', table: 'golf_price_2', period: 'workday', category: 'pickle_ball', identity: 'hourly', format: '元/小時' },
    { id: 'alt-pickle-workday-student', table: 'golf_price_2', period: 'workday', category: 'pickle_ball', identity: 'student', format: '元/小時' },
    { id: 'alt-pickle-workday-disabled', table: 'golf_price_2', period: 'workday', category: 'pickle_ball', identity: 'disabled', format: '元/小時' },
    
    // Pickleball - Holiday Day
    { id: 'alt-pickle-holiday-hourly', table: 'golf_price_2', period: 'holiday', category: 'pickle_ball', identity: 'hourly', format: '元/小時' },
    { id: 'alt-pickle-holiday-student', table: 'golf_price_2', period: 'holiday', category: 'pickle_ball', identity: 'student', format: '元/小時' },
    { id: 'alt-pickle-holiday-disabled', table: 'golf_price_2', period: 'holiday', category: 'pickle_ball', identity: 'disabled', format: '元/小時' },
    
    // Pickleball - Holiday Evening
    { id: 'alt-pickle-holiday19-hourly', table: 'golf_price_2', period: 'holiday_19To22', category: 'pickle_ball', identity: 'hourly', format: '元/小時' },
    { id: 'alt-pickle-holiday19-student', table: 'golf_price_2', period: 'holiday_19To22', category: 'pickle_ball', identity: 'student', format: '元/小時' },
    { id: 'alt-pickle-holiday19-disabled', table: 'golf_price_2', period: 'holiday_19To22', category: 'pickle_ball', identity: 'disabled', format: '元/小時' },
];

// Load all prices when page loads
window.addEventListener('DOMContentLoaded', function() {
    // Load prices from first table
    priceConfigGolf1.forEach(config => {
        const element = document.getElementById(config.id);
        if (element) {
            loadPriceWithFormat(
                element,
                config.table,
                config.period,
                config.category,
                config.identity,
                config.format
            );
        }
    });
    
    // Load prices from second table
    priceConfigGolf2.forEach(config => {
        const element = document.getElementById(config.id);
        if (element) {
            loadPriceWithFormat(
                element,
                config.table,
                config.period,
                config.category,
                config.identity,
                config.format
            );
        }
    });
    
    // Initialize effective date editors (only for logged-in users)
    if (isUserLoggedIn) {
        // Use setTimeout to ensure all DOM elements are fully loaded and rendered
        setTimeout(function() {
            console.log('Attempting to initialize effective date editors...');
            initializeEffectiveDateEditors();
        }, 100);
    } else {
        console.log('User not logged in - effective date editing disabled');
    }
});

/**
 * Debug helper - adds messages to both console and debug panel
 */
function debugLog(message) {
    is_debug = false
    if (!is_debug) return;

    console.log(message);
    const debugPanel = document.getElementById('debug-panel');
    const debugContent = document.getElementById('debug-content');
    if (debugPanel && debugContent) {
        debugPanel.style.display = 'block';
        debugContent.innerHTML += '<div>' + message + '</div>';
        // Auto-scroll to bottom
        debugContent.scrollTop = debugContent.scrollHeight;
    }
}

/**
 * Initialize effective date editing functionality
 */
function initializeEffectiveDateEditors() {
    debugLog('=== Starting effective date editor initialization ===');
    debugLog('User logged in: ' + isUserLoggedIn);
    
    // First, let's check all elements with 'effective-date' class
    const allDateElements = document.querySelectorAll('.effective-date');
    debugLog('Total elements with .effective-date class: ' + allDateElements.length);
    allDateElements.forEach((el, i) => {
        debugLog(`  Date ${i}: ${el.dataset.date}, classes: ${el.className}, id: ${el.id}`);
    });
    
    // Now find editable ones
    const dateElements = document.querySelectorAll('.effective-date-editable');
    debugLog('Editable date elements found: ' + dateElements.length);
    
    if (dateElements.length === 0) {
        debugLog('❌ ERROR: No .effective-date-editable elements found!');
        return;
    }
    
    // Method 1: Direct event listeners
    dateElements.forEach((element, index) => {
        debugLog(`Adding click handler to date ${index}: ${element.dataset.date} (ID: ${element.id})`);
        
        element.addEventListener('click', function(e) {
            debugLog('🎯 CLICKED! Effective date: ' + this.dataset.date);
            e.preventDefault();
            e.stopPropagation();
            enableEffectiveDateEdit(this);
        });
        
        // Visual feedback
        element.style.userSelect = 'none';
        element.style.webkitUserSelect = 'none';
    });
    
    // Method 2: Event delegation as backup (in case elements are added dynamically)
    document.body.addEventListener('click', function(e) {
        const target = e.target;
        if (target && target.classList.contains('effective-date-editable')) {
            debugLog('🎯 CLICKED via delegation! Effective date: ' + target.dataset.date);
            e.preventDefault();
            e.stopPropagation();
            enableEffectiveDateEdit(target);
        }
    });
    
    debugLog('=== ✅ Effective date editor initialization complete ===');
}

/**
 * Enable inline editing of effective date
 */
let currentEditingDate = null;

function enableEffectiveDateEdit(dateElement) {
    debugLog('✏️ enableEffectiveDateEdit called for: ' + dateElement.dataset.date);
    
    // If already editing this element, return
    if (currentEditingDate === dateElement) {
        debugLog('Already editing this date');
        return;
    }
    
    // Cancel previous edit if exists
    if (currentEditingDate) {
        cancelDateEdit();
    }
    
    currentEditingDate = dateElement;
    const currentDate = dateElement.dataset.date;
    const table = dateElement.dataset.table;
    
    // Create date input and buttons
    const editGroup = document.createElement('span');
    editGroup.className = 'effective-date-editing';
    editGroup.style.cssText = 'display: inline-flex; gap: 5px; align-items: center;';
    
    const input = document.createElement('input');
    input.type = 'date';
    input.className = 'date-input';
    input.value = currentDate;
    input.style.cssText = 'padding: 4px; font-size: 0.9em;';
    
    const saveBtn = document.createElement('button');
    saveBtn.textContent = '✓';
    saveBtn.className = 'date-save-btn';
    saveBtn.style.cssText = 'padding: 4px 8px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.9em;';
    saveBtn.onclick = (e) => {
        e.stopPropagation();
        saveEffectiveDate(dateElement, table, currentDate, input.value);
    };
    
    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = '✕';
    cancelBtn.className = 'date-cancel-btn';
    cancelBtn.style.cssText = 'padding: 4px 8px; background-color: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.9em;';
    cancelBtn.onclick = (e) => {
        e.stopPropagation();
        cancelDateEdit();
    };
    
    editGroup.appendChild(input);
    editGroup.appendChild(saveBtn);
    editGroup.appendChild(cancelBtn);
    
    // Replace date display with edit controls
    const originalContent = dateElement.innerHTML;
    dateElement.setAttribute('data-original-content', originalContent);
    dateElement.innerHTML = '';
    dateElement.appendChild(editGroup);
    input.focus();
}

/**
 * Save updated effective date via API
 */
function saveEffectiveDate(dateElement, table, oldDate, newDate) {
    if (!newDate) {
        alert('Please select a valid date');
        return;
    }
    
    if (oldDate === newDate) {
        alert('New date is the same as the old date');
        cancelDateEdit();
        return;
    }
    
    // Confirm with user
    if (!confirm(`Update all records in ${table} from ${oldDate} to ${newDate}?\n\nThis will affect all price records with the effective date ${oldDate}.`)) {
        cancelDateEdit();
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update_effective_date');
    formData.append('table', table);
    formData.append('old_date', oldDate);
    formData.append('new_date', newDate);
    
    // Show loading indicator
    dateElement.innerHTML = '⏳ Updating...';
    
    fetch('./price_api.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (response.status === 401) {
                throw new Error('User not logged in');
            }
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            console.log('Raw API Response (Update Effective Date):', text);
            return JSON.parse(text);
        })
        .then(data => {
            if (data.success) {
                // Update the display
                dateElement.innerHTML = newDate;
                dateElement.dataset.date = newDate;
                dateElement.style.backgroundColor = '#d4edda';
                
                // Show success message
                alert(`✓ Effective date updated successfully!\n\n${data.message}`);
                
                // Remove highlight after 2 seconds
                setTimeout(() => {
                    dateElement.style.backgroundColor = '';
                }, 2000);
                
                currentEditingDate = null;
                console.log('Effective date updated:', data);
            } else {
                throw new Error(data.error);
            }
        })
        .catch(error => {
            console.error('Error saving effective date:', error);
            alert('Error updating effective date: ' + error.message);
            cancelDateEdit();
        });
}

/**
 * Cancel effective date editing
 */
function cancelDateEdit() {
    if (currentEditingDate) {
        const originalContent = currentEditingDate.getAttribute('data-original-content');
        if (originalContent) {
            currentEditingDate.innerHTML = originalContent;
        }
        currentEditingDate.removeAttribute('data-original-content');
        currentEditingDate = null;
    }
}
</script>

</body>
</html>
