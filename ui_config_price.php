<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'setting-admin.php';
require 'account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$sql = "
SELECT 
    combined_tables_2.*
FROM (
    select * from golf_price
    union 
    select * from golf_price_2
) AS combined_tables_2
where 1=1
and `effective-date`=(
    SELECT 
        combined_tables.`effective-date`
    FROM (
        select * from golf_price
        union 
        select * from golf_price_2
    ) AS combined_tables
    GROUP BY combined_tables.`effective-date`
    order by combined_tables.`effective-date` desc
    limit 1
)
order by `effective-date` desc
; ";
$raw_data = array();
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $raw_data[] = $row;
    }
}
// 1. Raw Data Input (Truncated for brevity, but this represents your dataset)
// $raw_data = [
//     ['VIP', 'workday', 'student', 250, '2025-09-01'],
//     ['1', 'workday', 'student', 100, '2025-09-01'],
//     ['VIP', 'holiday', 'student', 360, '2025-09-01'],
//     ['VIP', 'workday', 'disabled', 250, '2025-09-01'],
//     ['VIP', 'workday', 'hourly', 250, '2025-09-01'],
//     // ... add all your records here
// ];

// 2. Grouping Logic
$grouped = [];
foreach ($raw_data as $row) {
    $name = $row['price-name'];
    $period = $row['period'];
    $identity = $row['identity'];
    $price = $row['price'];
    $date = $row['effective-date'];
    $grouped[$period][$identity][] = ['name' => $name, 'price' => $price];
}

// 3. Handle Form Submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_effective_date = $_POST['effective_date'];
    $prices = $_POST['prices']; // Array of prices organized by hierarchy
    
    // Here you would typically run your SQL UPDATE/INSERT queries
    $message = "Success: Prices updated for " . htmlspecialchars($new_effective_date);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Golf Price Management</title>
</head>
<body class="bg-gray-50 p-8">

    <div class="max-w-6xl mx-auto">
        <form action="" method="POST" class="bg-white shadow-md rounded-lg overflow-hidden">
            
            <div class="p-6 border-b border-gray-200 bg-gray-100 flex justify-between items-center">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Effective Date</label>
                    <input type="date" name="effective_date" value="2025-09-01" 
                           class="mt-1 block w-48 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                </div>
                <?php if($message): ?>
                    <div class="bg-green-100 text-green-700 px-4 py-2 rounded"><?php echo $message; ?></div>
                <?php endif; ?>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                    Update All Records
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Identity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Price Name & Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($grouped as $period => $identities): ?>
                            <?php 
                                $period_rowspan = 0;
                                foreach($identities as $id) { $period_rowspan += 1; } 
                            ?>
                            <?php $first_id = true; ?>
                            <?php foreach ($identities as $identity => $price_list): ?>
                                <tr>
                                    <?php if ($first_id): ?>
                                        <td rowspan="<?php echo $period_rowspan; ?>" class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 bg-gray-50 border-r">
                                            <span class="uppercase"><?php echo str_replace('_', ' ', $period); ?></span>
                                        </td>
                                        <?php $first_id = false; ?>
                                    <?php endif; ?>

                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-blue-600 border-r">
                                        <?php echo ucfirst($identity); ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                                            <?php foreach ($price_list as $item): ?>
                                                <div class="flex flex-col border border-gray-200 rounded p-1 bg-white hover:bg-blue-50">
                                                    <span class="text-[10px] text-gray-500 font-bold truncate"><?php echo $item['name']; ?></span>
                                                    <input type="number" 
                                                           name="prices[<?php echo $period; ?>][<?php echo $identity; ?>][<?php echo $item['name']; ?>]" 
                                                           value="<?php echo $item['price']; ?>"
                                                           class="text-sm font-semibold focus:outline-none bg-transparent">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

</body>
</html>