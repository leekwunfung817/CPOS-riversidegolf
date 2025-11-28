<?php

$notice = null;

require_once 'account_variable.php';
$removed_chars = array("'", '"', "`");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function sanitizeForMariaDB(string $input): string {
    // Step 1: Remove control characters (ASCII 0–31 except newline/tab)
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $input);

    // Step 2: Strip dangerous SQL meta-characters
    $input = str_replace(
        "\n",
        '\\n',
        $input
    );
    $input = str_replace(
        ["'", '"', '`', ';', '--', '#', '/*', '*/'],
        '',
        $input
    );

    // Step 3: Normalize whitespace
    $input = preg_replace('/\s+/u', ' ', $input);

    // Step 4: Trim leading/trailing whitespace
    return trim($input);
}

$sql = "
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chinese_title VARCHAR(255) NOT NULL,
    chinese_paragraph TEXT NOT NULL,
    english_title VARCHAR(255) NOT NULL,
    english_paragraph TEXT NOT NULL,
    display_prompt TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";
try {
    $result = $conn->query($sql);
} catch (Exception $e) {
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_notice'])) {
    try {

        $chinese_title = sanitizeForMariaDB($_POST['chinese_title']);
        $chinese_paragraph = sanitizeForMariaDB($_POST['chinese_paragraph']);
        $english_title = sanitizeForMariaDB($_POST['english_title']);
        $english_paragraph = sanitizeForMariaDB($_POST['english_paragraph']);
        $display_prompt = sanitizeForMariaDB(isset($_POST['display_prompt']) ? 1 : 0);

        $sql = "DELETE FROM `notices` WHERE 1;";
        try {
            $result = $conn->query($sql);
        } catch (Exception $e) {
        }

        $sql = "
        INSERT INTO notices (
            chinese_title, chinese_paragraph, english_title, english_paragraph, display_prompt
        ) VALUES (
            '$chinese_title', '$chinese_paragraph', '$english_title', '$english_paragraph', '$display_prompt'
        )
        ";
        try {
            $result = $conn->query($sql);
        } catch (Exception $e) {
        }

        $success_message = "Notice saved successfully";
    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}



$notice_row = array();
try {

    $notice = false;
    $sql = "SELECT * FROM notices ORDER BY id DESC LIMIT 1;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $notice = true;
            $notice_row = $row;
        }
    }
    if (!$notice) {
        $error_message = "No notice set to display.";
    }
} catch(PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Prompt System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        .display-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            font-size: 16px;
        }

        .display-button:hover {
            background-color: #0056b3;
        }

        .notice-display {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>通知提示配置 Notice Prompt Configuration</h1>

        <?php if ($success_message): ?>
            <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form id="noticeForm" method="POST" action="">
            <label for="chinese_title">Chinese Title:</label>
            <input type="text" id="chinese_title" name="chinese_title" value="<?php echo $notice_row['chinese_title']; ?>" required>

            <label for="chinese_paragraph">Chinese Paragraph:</label>
            <textarea 
                id="chinese_paragraph" 
                name="chinese_paragraph" 
                style="height: 150px;" 
                required><?php echo $notice_row['chinese_paragraph']; ?></textarea>

            <label for="english_title">English Title:</label>
            <input type="text" id="english_title" name="english_title" value="<?php echo $notice_row['english_title']; ?>" required>

            <label for="english_paragraph">English Paragraph:</label>
            <textarea 
                id="english_paragraph" 
                name="english_paragraph"  
                style="height: 150px;" 
                required><?php echo $notice_row['english_paragraph']; ?></textarea>

            <label for="display_prompt">
                <input type="checkbox" id="display_prompt" name="display_prompt" <?php echo (((int) $notice_row['display_prompt'])>0?'checked':''); ?> >
                Display this prompt
            </label>

            <button type="submit" name="submit_notice">Submit</button>
        </form>

        <?php if ($notice): ?>
            <div class="notice-display">
                <h2><?php echo htmlspecialchars($notice['chinese_title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($notice['chinese_paragraph'])); ?></p>
                <h2><?php echo htmlspecialchars($notice['english_title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($notice['english_paragraph'])); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.getElementById('noticeForm').addEventListener('submit', function(event) {
            const chineseTitle = document.getElementById('chinese_title').value;
            const chineseParagraph = document.getElementById('chinese_paragraph').value;
            const englishTitle = document.getElementById('english_title').value;
            const englishParagraph = document.getElementById('english_paragraph').value;

            if (!chineseTitle || !chineseParagraph || !englishTitle || !englishParagraph) {
                event.preventDefault();
                alert('Please fill out all fields.');
            }
        });
    </script>
</body>
</html>