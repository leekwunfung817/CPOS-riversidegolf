<?php
// Enable verbose error reporting for debugging and render fatal errors on-page
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Convert warnings/notices to exceptions so they are visible during development
set_error_handler(function($severity, $message, $file, $line) {
  throw new ErrorException($message, 0, $severity, $file, $line);
});

// Render fatal errors instead of letting the webserver return a generic 500 page
register_shutdown_function(function() {
  $err = error_get_last();
  if ($err !== null) {
    http_response_code(500);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Fatal error</title></head><body style="font-family:monospace;background:#fff;padding:12px;">';
    echo '<h1>Fatal error</h1>';
    echo '<pre style="background:#fee;border:1px solid #f00;padding:12px;">';
    echo htmlspecialchars("{$err['message']} in {$err['file']} on line {$err['line']}", ENT_QUOTES, 'UTF-8');
    echo '</pre>';
    @file_put_contents(__DIR__ . '/php_error.log', date('c') . " - " . print_r($err, true) . PHP_EOL, FILE_APPEND);
    echo '</body></html>';
  }
});

session_start();
if (isset($_GET['type'])) {
  $_SESSION['type'] = $_GET['type'];
}

$is_logged_in = isset($_SESSION['management']) && !empty($_SESSION['management']);
$page_mode = 'view';
if (isset($_GET['mode']) && $_GET['mode'] === 'edit' && $is_logged_in) {
  $page_mode = 'edit';
}
$is_edit_mode = $page_mode === 'edit';





require_once 'setting-admin.php';

$page_text = [
  'title' => '條款及細則',
  'mode_prefix' => 'Mode: ',
  'mode_edit' => 'Edit',
  'mode_view' => 'View Only',
  'switch_to_view' => 'Switch to View Only',
  'switch_to_edit' => 'Switch to Edit',
  'agree_zh' => '同意並繼續',
  'agree_en' => 'Agree and continue',
  'copyright' => '版權所有 © 2024 白石高球練習場有限公司',
  'booking_href' => './input-form.php',
];

// UI labels for bilingual editing interface (Traditional Chinese / English)
$ui_labels = [
  'show_section' => ['zh' => '顯示章節', 'en' => 'Show Section'],
  'section_title' => ['zh' => '章節標題', 'en' => 'Section Title'],
  'intro' => ['zh' => '導言', 'en' => 'Intro'],
  'paragraphs' => ['zh' => '段落', 'en' => 'Paragraphs'],
  'add_paragraph' => ['zh' => '新增段落', 'en' => 'Add Paragraph'],
  'delete_paragraph' => ['zh' => '刪除段落', 'en' => 'Delete Paragraph'],
  'items' => ['zh' => '項目', 'en' => 'Items'],
  'add_item' => ['zh' => '新增項目', 'en' => 'Add Item'],
  'delete_item' => ['zh' => '刪除項目', 'en' => 'Delete Item'],
  'delete_section' => ['zh' => '刪除章節', 'en' => 'Delete Section'],
  'add_section' => ['zh' => '新增章節', 'en' => 'Add Section'],
  'save_changes' => ['zh' => '儲存更改', 'en' => 'Save Changes'],
];

function ui_label($key)
{
  global $ui_labels;
  if (!isset($ui_labels[$key])) return $key;
  $item = $ui_labels[$key];
  return ($item['zh'] ?? '') . ' / ' . ($item['en'] ?? '');
}

$terms_dictionary = [];
$current_type = $_SESSION['type'] ?? '';
// dictionary defaults were moved to terms_defaults.php; loaded later only when DB has no records

if (!isset($_SESSION['terms_dictionary_override']) || !is_array($_SESSION['terms_dictionary_override'])) {
  $_SESSION['terms_dictionary_override'] = [];
}
if (!isset($_SESSION['terms_visibility_override']) || !is_array($_SESSION['terms_visibility_override'])) {
  $_SESSION['terms_visibility_override'] = [];
}

$visibility_map = [];

function normalize_section(array $section)
{
  $title = isset($section['title']) ? trim((string) $section['title']) : '';
  $intro = isset($section['intro']) ? trim((string) $section['intro']) : '';

  $items = [];
  if (isset($section['items']) && is_array($section['items'])) {
    foreach ($section['items'] as $item) {
      $item_text = trim((string) $item);
      if ($item_text !== '') {
        $items[] = $item_text;
      }
    }
  }

  $paragraphs = [];
  if (isset($section['paragraphs']) && is_array($section['paragraphs'])) {
    foreach ($section['paragraphs'] as $paragraph) {
      $paragraph_text = trim((string) $paragraph);
      if ($paragraph_text !== '') {
        $paragraphs[] = $paragraph_text;
      }
    }
  }

  return [
    'title' => $title,
    'intro' => $intro,
    'items' => $items,
    'paragraphs' => $paragraphs,
  ];
}

function normalize_terms_dictionary($raw_terms)
{
  $normalized = [];
  if (!is_array($raw_terms)) {
    return $normalized;
  }

  foreach ($raw_terms as $language => $sections) {
    if (!is_array($sections)) {
      continue;
    }

    $normalized[$language] = [];
    foreach ($sections as $section) {
      if (!is_array($section)) {
        continue;
      }
      $normalized[$language][] = normalize_section($section);
    }
  }

  return $normalized;
}

function apply_editor_action(array &$terms_dictionary, $action)
{
  $parts = explode('|', (string) $action);
  $name = $parts[0] ?? 'save';

  if ($name === 'save') {
    return;
  }

  $language = $parts[1] ?? '';
  if ($language === '' || !isset($terms_dictionary[$language]) || !is_array($terms_dictionary[$language])) {
    return;
  }

  if ($name === 'add_section') {
    $terms_dictionary[$language][] = [
      'title' => '',
      'intro' => '',
      'items' => [''],
      'paragraphs' => [''],
    ];
    return;
  }

  $section_index = isset($parts[2]) ? (int) $parts[2] : -1;
  if ($section_index < 0 || !isset($terms_dictionary[$language][$section_index])) {
    return;
  }

  if ($name === 'delete_section') {
    array_splice($terms_dictionary[$language], $section_index, 1);
    return;
  }

  if (!isset($terms_dictionary[$language][$section_index]['items']) || !is_array($terms_dictionary[$language][$section_index]['items'])) {
    $terms_dictionary[$language][$section_index]['items'] = [];
  }

  if (!isset($terms_dictionary[$language][$section_index]['paragraphs']) || !is_array($terms_dictionary[$language][$section_index]['paragraphs'])) {
    $terms_dictionary[$language][$section_index]['paragraphs'] = [];
  }

  if ($name === 'add_item') {
    $terms_dictionary[$language][$section_index]['items'][] = '';
    return;
  }

  if ($name === 'add_paragraph') {
    $terms_dictionary[$language][$section_index]['paragraphs'][] = '';
    return;
  }

  $entry_index = isset($parts[3]) ? (int) $parts[3] : -1;
  if ($entry_index < 0) {
    return;
  }

  if ($name === 'delete_item' && isset($terms_dictionary[$language][$section_index]['items'][$entry_index])) {
    array_splice($terms_dictionary[$language][$section_index]['items'], $entry_index, 1);
    return;
  }

  if ($name === 'delete_paragraph' && isset($terms_dictionary[$language][$section_index]['paragraphs'][$entry_index])) {
    array_splice($terms_dictionary[$language][$section_index]['paragraphs'], $entry_index, 1);
  }
}

function get_db_connection()
{
  $cfgPath = __DIR__ . '/account_variable.php';
  if (!file_exists($cfgPath)) {
    return null;
  }
  require_once $cfgPath;

  $conn = @new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    return null;
  }
  $conn->set_charset('utf8mb4');
  return $conn;
}

function ensure_terms_table($conn)
{
  $sql = "CREATE TABLE IF NOT EXISTS `terms_store` (
    `type` VARCHAR(100) NOT NULL PRIMARY KEY,
    `data` LONGTEXT NOT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  @$conn->query($sql);
}

function load_terms_from_db($conn, $type)
{
  $stmt = $conn->prepare('SELECT `data` FROM `terms_store` WHERE `type`=? LIMIT 1');
  if (!$stmt) return null;
  $stmt->bind_param('s', $type);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res && $row = $res->fetch_assoc()) {
    $data = $row['data'] ?? '';
    $decoded = json_decode($data, true);
    return is_array($decoded) ? $decoded : null;
  }
  return null;
}

function save_terms_to_db($conn, $type, $terms_dictionary)
{
  $json = json_encode($terms_dictionary, JSON_UNESCAPED_UNICODE);
  $stmt = $conn->prepare("INSERT INTO `terms_store` (`type`,`data`) VALUES (?,?) ON DUPLICATE KEY UPDATE `data`=VALUES(`data`), `updated_at`=CURRENT_TIMESTAMP");
  if (!$stmt) return false;
  $stmt->bind_param('ss', $type, $json);
  $ok = $stmt->execute();
  return (bool)$ok;
}

function get_any_terms_type($conn)
{
  $stmt = $conn->prepare('SELECT `type` FROM `terms_store` ORDER BY `updated_at` DESC LIMIT 1');
  if (!$stmt) return null;
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res && $row = $res->fetch_assoc()) {
    return $row['type'];
  }
  return null;
}

function ensure_visibility_table($conn)
{
  $sql = "CREATE TABLE IF NOT EXISTS `terms_element_visibility` (
    `type` VARCHAR(100) NOT NULL,
    `language` VARCHAR(10) NOT NULL,
    `section_id` INT NOT NULL,
    `element_type` VARCHAR(32) NOT NULL,
    `element_index` INT NOT NULL DEFAULT 0,
    `enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`type`, `language`, `section_id`, `element_type`, `element_index`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  @$conn->query($sql);
}

function load_visibility_from_db($conn, $type)
{
  $map = [];
  $stmt = $conn->prepare('SELECT `language`,`section_id`,`element_type`,`element_index`,`enabled` FROM `terms_element_visibility` WHERE `type`=?');
  if (!$stmt) return $map;
  $stmt->bind_param('s', $type);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res) {
    while ($row = $res->fetch_assoc()) {
      $lang = $row['language'];
      $sid = (int)$row['section_id'];
      $etype = $row['element_type'];
      $eidx = (int)$row['element_index'];
      $enabled = (int)$row['enabled'] ? 1 : 0;
      if (!isset($map[$lang])) $map[$lang] = [];
      if (!isset($map[$lang][$sid])) $map[$lang][$sid] = [];
      if (!isset($map[$lang][$sid][$etype])) $map[$lang][$sid][$etype] = [];
      $map[$lang][$sid][$etype][$eidx] = $enabled;
    }
  }
  return $map;
}

function save_visibility_to_db($conn, $type, $visibility_map)
{
  if (!$conn) return false;
  $sql = "INSERT INTO `terms_element_visibility` (`type`,`language`,`section_id`,`element_type`,`element_index`,`enabled`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `enabled`=VALUES(`enabled`), `updated_at`=CURRENT_TIMESTAMP";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    file_put_contents(__DIR__ . '/visibility_debug.log', "prepare failed: " . ($conn->error ?? 'unknown') . "\n", FILE_APPEND);
    return false;
  }

  $overall_ok = true;
  foreach ($visibility_map as $lang => $sections) {
    foreach ($sections as $sid => $etypes) {
      foreach ($etypes as $etype => $indices) {
        foreach ($indices as $eidx => $enabled) {
          $s = (int)$sid;
          $ei = (int)$eidx;
          $en = (int)$enabled ? 1 : 0;
          // types: type(s), language(s), section_id(i), element_type(s), element_index(i), enabled(i)
          $bound = $stmt->bind_param('ssisii', $type, $lang, $s, $etype, $ei, $en);
          if ($bound === false) {
            file_put_contents(__DIR__ . '/visibility_debug.log', "bind_param failed: " . $stmt->error . "\n", FILE_APPEND);
            $overall_ok = false;
            continue;
          }
          $execOk = $stmt->execute();
          if ($execOk === false) {
            file_put_contents(__DIR__ . '/visibility_debug.log', "execute failed for type={$type} lang={$lang} sid={$s} etype={$etype} eidx={$ei} enabled={$en}: " . $stmt->error . "\n", FILE_APPEND);
            $overall_ok = false;
          }
        }
      }
    }
  }
  return $overall_ok;
}

function compute_element_hash($element_type, $text)
{
  $text = is_scalar($text) ? (string)$text : json_encode($text, JSON_UNESCAPED_UNICODE);
  return sha1($element_type . '|' . $text);
}

function ensure_visibility_hash_table($conn)
{
  $sql = "CREATE TABLE IF NOT EXISTS `terms_element_visibility_hash` (
    `type` VARCHAR(100) NOT NULL,
    `language` VARCHAR(10) NOT NULL,
    `section_id` INT NOT NULL,
    `element_type` VARCHAR(32) NOT NULL,
    `element_hash` VARCHAR(128) NOT NULL,
    `enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`type`,`language`,`section_id`,`element_type`,`element_hash`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  @$conn->query($sql);
}

function load_visibility_hash_from_db($conn, $type)
{
  $map = [];
  $stmt = $conn->prepare('SELECT `language`,`section_id`,`element_type`,`element_hash`,`enabled` FROM `terms_element_visibility_hash` WHERE `type`=?');
  if (!$stmt) return $map;
  $stmt->bind_param('s', $type);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res) {
    while ($row = $res->fetch_assoc()) {
      $lang = $row['language'];
      $sid = (int)$row['section_id'];
      $etype = $row['element_type'];
      $hash = $row['element_hash'];
      $enabled = (int)$row['enabled'] ? 1 : 0;
      if (!isset($map[$lang])) $map[$lang] = [];
      if (!isset($map[$lang][$sid])) $map[$lang][$sid] = [];
      if (!isset($map[$lang][$sid][$etype])) $map[$lang][$sid][$etype] = [];
      $map[$lang][$sid][$etype][$hash] = $enabled;
    }
  }
  return $map;
}

function save_visibility_hash_to_db($conn, $type, $terms_dictionary, $visibility_map)
{
  if (!$conn) return false;
  $sql = "INSERT INTO `terms_element_visibility_hash` (`type`,`language`,`section_id`,`element_type`,`element_hash`,`enabled`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `enabled`=VALUES(`enabled`), `updated_at`=CURRENT_TIMESTAMP";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    file_put_contents(__DIR__ . '/visibility_debug.log', "prepare_hash failed: " . ($conn->error ?? 'unknown') . "\n", FILE_APPEND);
    return false;
  }

  $overall_ok = true;
  foreach ($terms_dictionary as $lang => $sections) {
    if (!is_array($sections)) continue;
    foreach ($sections as $sid => $section) {
      $sid = (int)$sid;
      // title
      $title_text = $section['title'] ?? '';
      $hash = compute_element_hash('title', $title_text);
      $enabled = isset($visibility_map[$lang][$sid]['title'][0]) ? (int)$visibility_map[$lang][$sid]['title'][0] : 1;
      $etype = 'title';
      $bound = $stmt->bind_param('ssissi', $type, $lang, $sid, $etype, $hash, $enabled);
      if ($bound === false) {
        file_put_contents(__DIR__ . '/visibility_debug.log', "bind_param_hash failed (title): " . $stmt->error . "\n", FILE_APPEND);
        $overall_ok = false;
      } else {
        $execOk = $stmt->execute();
        if ($execOk === false) {
          file_put_contents(__DIR__ . '/visibility_debug.log', "execute_hash failed (title): " . $stmt->error . "\n", FILE_APPEND);
          $overall_ok = false;
        }
      }

      // intro
      $intro_text = $section['intro'] ?? '';
      $hash = compute_element_hash('intro', $intro_text);
      $enabled = isset($visibility_map[$lang][$sid]['intro'][0]) ? (int)$visibility_map[$lang][$sid]['intro'][0] : 1;
      $etype = 'intro';
      $bound = $stmt->bind_param('ssissi', $type, $lang, $sid, $etype, $hash, $enabled);
      if ($bound === false) {
        file_put_contents(__DIR__ . '/visibility_debug.log', "bind_param_hash failed (intro): " . $stmt->error . "\n", FILE_APPEND);
        $overall_ok = false;
      } else {
        $execOk = $stmt->execute();
        if ($execOk === false) {
          file_put_contents(__DIR__ . '/visibility_debug.log', "execute_hash failed (intro): " . $stmt->error . "\n", FILE_APPEND);
          $overall_ok = false;
        }
      }

      // paragraphs
      $paragraphs = isset($section['paragraphs']) && is_array($section['paragraphs']) ? $section['paragraphs'] : [];
      foreach ($paragraphs as $pidx => $paragraph) {
        $hash = compute_element_hash('paragraph', $paragraph);
        $enabled = isset($visibility_map[$lang][$sid]['paragraph'][$pidx]) ? (int)$visibility_map[$lang][$sid]['paragraph'][$pidx] : 1;
        $etype = 'paragraph';
        $bound = $stmt->bind_param('ssissi', $type, $lang, $sid, $etype, $hash, $enabled);
        if ($bound === false) {
          file_put_contents(__DIR__ . '/visibility_debug.log', "bind_param_hash failed (paragraph): " . $stmt->error . "\n", FILE_APPEND);
          $overall_ok = false;
        } else {
          $execOk = $stmt->execute();
          if ($execOk === false) {
            file_put_contents(__DIR__ . '/visibility_debug.log', "execute_hash failed (paragraph): " . $stmt->error . "\n", FILE_APPEND);
            $overall_ok = false;
          }
        }
      }

      // items
      $items = isset($section['items']) && is_array($section['items']) ? $section['items'] : [];
      foreach ($items as $iidx => $item) {
        $hash = compute_element_hash('item', $item);
        $enabled = isset($visibility_map[$lang][$sid]['item'][$iidx]) ? (int)$visibility_map[$lang][$sid]['item'][$iidx] : 1;
        $etype = 'item';
        $bound = $stmt->bind_param('ssissi', $type, $lang, $sid, $etype, $hash, $enabled);
        if ($bound === false) {
          file_put_contents(__DIR__ . '/visibility_debug.log', "bind_param_hash failed (item): " . $stmt->error . "\n", FILE_APPEND);
          $overall_ok = false;
        } else {
          $execOk = $stmt->execute();
          if ($execOk === false) {
            file_put_contents(__DIR__ . '/visibility_debug.log', "execute_hash failed (item): " . $stmt->error . "\n", FILE_APPEND);
            $overall_ok = false;
          }
        }
      }
    }
  }
  return $overall_ok;
}

function apply_db_hash_visibility(&$visibility_map, $terms_dictionary, $db_hash_map)
{
  if (!is_array($db_hash_map) || !is_array($terms_dictionary)) return;
  foreach ($terms_dictionary as $lang => $sections) {
    if (!is_array($sections)) continue;
    foreach ($sections as $sid => $section) {
      $sid = (int)$sid;
      // title
      $hash = compute_element_hash('title', $section['title'] ?? '');
      if (isset($db_hash_map[$lang][$sid]['title'][$hash]) && !isset($visibility_map[$lang][$sid]['title'][0])) {
        $visibility_map[$lang][$sid]['title'][0] = $db_hash_map[$lang][$sid]['title'][$hash];
      }
      // intro
      $hash = compute_element_hash('intro', $section['intro'] ?? '');
      if (isset($db_hash_map[$lang][$sid]['intro'][$hash]) && !isset($visibility_map[$lang][$sid]['intro'][0])) {
        $visibility_map[$lang][$sid]['intro'][0] = $db_hash_map[$lang][$sid]['intro'][$hash];
      }
      // paragraphs
      $paragraphs = isset($section['paragraphs']) && is_array($section['paragraphs']) ? $section['paragraphs'] : [];
      foreach ($paragraphs as $pidx => $p) {
        $hash = compute_element_hash('paragraph', $p);
        if (isset($db_hash_map[$lang][$sid]['paragraph'][$hash]) && !isset($visibility_map[$lang][$sid]['paragraph'][$pidx])) {
          $visibility_map[$lang][$sid]['paragraph'][$pidx] = $db_hash_map[$lang][$sid]['paragraph'][$hash];
        }
      }
      // items
      $items = isset($section['items']) && is_array($section['items']) ? $section['items'] : [];
      foreach ($items as $iidx => $it) {
        $hash = compute_element_hash('item', $it);
        if (isset($db_hash_map[$lang][$sid]['item'][$hash]) && !isset($visibility_map[$lang][$sid]['item'][$iidx])) {
          $visibility_map[$lang][$sid]['item'][$iidx] = $db_hash_map[$lang][$sid]['item'][$hash];
        }
      }
    }
  }
}

function normalize_visibility_post($raw)
{
  $out = [];
  if (!is_array($raw)) return $out;
  foreach ($raw as $lang => $sections) {
    if (!is_array($sections)) continue;
    foreach ($sections as $sid => $etypes) {
      foreach ($etypes as $etype => $indices) {
        foreach ($indices as $eidx => $val) {
          $out[$lang][(int)$sid][$etype][(int)$eidx] = (int)$val ? 1 : 0;
        }
      }
    }
  }
  return $out;
}

function is_element_visible($visibility_map, $language, $section_id, $element_type, $element_index = 0)
{
  if (!is_array($visibility_map)) return true;
  if (isset($visibility_map[$language]) && isset($visibility_map[$language][$section_id]) && isset($visibility_map[$language][$section_id][$element_type]) && array_key_exists($element_index, $visibility_map[$language][$section_id][$element_type])) {
    return (bool)$visibility_map[$language][$section_id][$element_type][$element_index];
  }
  return true; // default to visible when not specified
}

$dbConn = get_db_connection();
if ($dbConn) {
  ensure_terms_table($dbConn);
  // Try to load terms for the current type (from GET/session). If none found,
  // fall back to the most-recently-updated stored type so public users see saved changes.
  $db_terms = load_terms_from_db($dbConn, $current_type);
  if (is_array($db_terms) && !empty($db_terms)) {
    $terms_dictionary = $db_terms;
  } else {
    $fallback_type = get_any_terms_type($dbConn);
    if ($fallback_type) {
      $current_type = $fallback_type;
      $_SESSION['type'] = $current_type;
      $db_terms2 = load_terms_from_db($dbConn, $current_type);
      if (is_array($db_terms2) && !empty($db_terms2)) {
        $terms_dictionary = $db_terms2;
      }
    }
  }

  // ensure visibility table and load visibility settings for the resolved type
  ensure_visibility_table($dbConn);
  $db_visibility = load_visibility_from_db($dbConn, $current_type);
  if (is_array($db_visibility) && !empty($db_visibility)) {
    $visibility_map = $db_visibility;
  }

  // ensure and load hash-based visibility table
  ensure_visibility_hash_table($dbConn);
  $db_visibility_hash = load_visibility_hash_from_db($dbConn, $current_type);
  if (!is_array($db_visibility_hash)) $db_visibility_hash = [];
}

// Apply session preview overrides only when in edit mode (admin preview).
// This prevents admin-only session overrides from affecting public view.
if ($is_edit_mode && $is_logged_in) {
  if (isset($_SESSION['terms_dictionary_override'][$current_type]) && is_array($_SESSION['terms_dictionary_override'][$current_type])) {
    $terms_dictionary = $_SESSION['terms_dictionary_override'][$current_type];
  }
  if (isset($_SESSION['terms_visibility_override'][$current_type]) && is_array($_SESSION['terms_visibility_override'][$current_type])) {
    $visibility_map = $_SESSION['terms_visibility_override'][$current_type];
  }
}

// Apply hash-based DB visibility mappings to the index-based visibility map
// without overriding any session overrides that may already be present.
if (!empty($dbConn) && !empty($db_visibility_hash) && is_array($terms_dictionary)) {
  apply_db_hash_visibility($visibility_map, $terms_dictionary, $db_visibility_hash);
}

// If DB and session do not provide terms, load the defaults file
if (empty($terms_dictionary)) {
  $defaults_path = __DIR__ . '/terms_defaults.php';
  if (file_exists($defaults_path)) {
    include $defaults_path; // defines $terms_defaults
    if (isset($terms_defaults[$current_type]) && is_array($terms_defaults[$current_type])) {
      $terms_dictionary = $terms_defaults[$current_type];
    } elseif (isset($terms_defaults['default']) && is_array($terms_defaults['default'])) {
      $terms_dictionary = $terms_defaults['default'];
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in && $is_edit_mode) {
  $posted_terms = $_POST['terms'] ?? [];
  $posted_visibility = $_POST['visibility'] ?? [];
  $terms_dictionary = normalize_terms_dictionary($posted_terms);
  $visibility_map = normalize_visibility_post($posted_visibility);
  $editor_action = $_POST['editor_action'] ?? 'save';
  apply_editor_action($terms_dictionary, $editor_action);
  // Save to session override first
  $_SESSION['terms_dictionary_override'][$current_type] = $terms_dictionary;
  $_SESSION['terms_visibility_override'][$current_type] = $visibility_map;

  // Try to persist to DB as JSON and visibility map
  if ($dbConn) {
    save_terms_to_db($dbConn, $current_type, $terms_dictionary);
    save_visibility_to_db($dbConn, $current_type, $visibility_map);
    // also persist hash-based visibility for stable matching
    save_visibility_hash_to_db($dbConn, $current_type, $terms_dictionary, $visibility_map);
  }
  // After saving, follow PRG pattern for save action to reflect changes in view mode
  if ($editor_action === 'save') {
    $loc = '?type=' . urlencode($current_type) . '&mode=view';
    header('Location: ' . $loc);
    exit;
  }
}

function render_terms_dictionary(array $sections, $language, $visibility_map = [], $type = null)
{
  foreach ($sections as $section_index => $section) {
    // if the whole section is hidden, skip
    if (!is_element_visible($visibility_map, $language, (int)$section_index, 'section', 0)) {
      continue;
    }
    echo '<section>';
    // Title
    if (is_element_visible($visibility_map, $language, (int)$section_index, 'title', 0) && !empty($section['title'])) {
      echo '<h2>' . htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') . '</h2>';
    }

    // Intro
    if (is_element_visible($visibility_map, $language, (int)$section_index, 'intro', 0) && !empty($section['intro'])) {
      echo '<p>' . htmlspecialchars($section['intro'], ENT_QUOTES, 'UTF-8') . '</p>';
    }

    // Paragraphs
    if (!empty($section['paragraphs'])) {
      foreach ($section['paragraphs'] as $pidx => $paragraph) {
        if (is_element_visible($visibility_map, $language, (int)$section_index, 'paragraph', (int)$pidx)) {
          echo '<p>' . htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') . '</p>';
        }
      }
    }

    // Items
    if (!empty($section['items'])) {
      echo '<ol>';
      foreach ($section['items'] as $iidx => $item) {
        if (is_element_visible($visibility_map, $language, (int)$section_index, 'item', (int)$iidx)) {
          echo '<li>' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</li>';
        }
      }
      echo '</ol>';
    }

    echo '</section>';
  }
}

function render_terms_editor(array $terms_dictionary, $current_type, $visibility_map = [])
{
  echo '<form method="post" class="terms-editor-form">';
  echo '<input type="hidden" name="type" value="' . htmlspecialchars($current_type, ENT_QUOTES, 'UTF-8') . '">';

  foreach ($terms_dictionary as $language => $sections) {
    echo '<section class="editor-language-block" data-language="' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '">';
    echo '<h2>' . htmlspecialchars(strtoupper($language), ENT_QUOTES, 'UTF-8') . '</h2>';

    foreach ($sections as $section_index => $section) {
      $title = $section['title'] ?? '';
      $intro = $section['intro'] ?? '';
      $paragraphs = isset($section['paragraphs']) && is_array($section['paragraphs']) ? $section['paragraphs'] : [];
      $items = isset($section['items']) && is_array($section['items']) ? $section['items'] : [];

      echo '<div class="editor-section-card" data-language="' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '" data-section-index="' . (int)$section_index . '">';
      // Section visibility toggle
      $sec_visible = is_element_visible($visibility_map, $language, (int)$section_index, 'section', 0);
      echo '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
      echo '<label>' . htmlspecialchars(ui_label('show_section'), ENT_QUOTES, 'UTF-8') . '</label>';
      echo '<input type="hidden" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][section][0]" value="0">';
      echo '<input type="checkbox" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][section][0]" value="1"' . ($sec_visible ? ' checked' : '') . '>';
      echo '</div>';

      echo '<label>' . htmlspecialchars(ui_label('section_title'), ENT_QUOTES, 'UTF-8') . '</label>';
      $title_visible = is_element_visible($visibility_map, $language, (int)$section_index, 'title', 0);
      echo '<input type="hidden" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][title][0]" value="0">';
      echo '<input type="checkbox" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][title][0]" value="1"' . ($title_visible ? ' checked' : '') . '>';
      echo '<input type="text" class="editor-input" name="terms[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int) $section_index . '][title]" value="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">';

      $intro_visible = is_element_visible($visibility_map, $language, (int)$section_index, 'intro', 0);
      echo '<label>' . htmlspecialchars(ui_label('intro'), ENT_QUOTES, 'UTF-8') . '</label>';
      echo '<input type="hidden" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][intro][0]" value="0">';
      echo '<input type="checkbox" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][intro][0]" value="1"' . ($intro_visible ? ' checked' : '') . '>';
      echo '<textarea class="editor-textarea" rows="2" name="terms[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int) $section_index . '][intro]">' . htmlspecialchars($intro, ENT_QUOTES, 'UTF-8') . '</textarea>';

      echo '<label>' . htmlspecialchars(ui_label('paragraphs'), ENT_QUOTES, 'UTF-8') . '</label>';
      foreach ($paragraphs as $paragraph_index => $paragraph) {
        $p_visible = is_element_visible($visibility_map, $language, (int)$section_index, 'paragraph', (int)$paragraph_index);
        echo '<div class="editor-row">';
        echo '<div class="editor-row-main">';
        echo '<input type="hidden" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][paragraph][' . (int)$paragraph_index . ']" value="0">';
        echo '<input type="checkbox" style="margin-right:8px;" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][paragraph][' . (int)$paragraph_index . ']" value="1"' . ($p_visible ? ' checked' : '') . '>';
        echo '<textarea class="editor-textarea" rows="2" name="terms[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int) $section_index . '][paragraphs][' . (int) $paragraph_index . ']">' . htmlspecialchars((string) $paragraph, ENT_QUOTES, 'UTF-8') . '</textarea>';
        echo '</div>';
        echo '<div class="editor-row-actions">';
        echo '<button type="submit" class="delete-btn btn-small" name="editor_action" value="delete_paragraph|' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '|' . (int)$section_index . '|' . (int)$paragraph_index . '">' . htmlspecialchars(ui_label('delete_paragraph'), ENT_QUOTES, 'UTF-8') . '</button>';
        echo '</div>';
        echo '</div>';
      }
      echo '<div style="margin-top:6px;"><button type="submit" class="sub-action-btn btn-primary js-add-paragraph" data-language="' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '" data-section-index="' . (int)$section_index . '" name="editor_action" value="add_paragraph|' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '|' . (int) $section_index . '">' . htmlspecialchars(ui_label('add_paragraph'), ENT_QUOTES, 'UTF-8') . '</button></div>';

      echo '<div class="editor-item-block">';
      echo '<label>' . htmlspecialchars(ui_label('items'), ENT_QUOTES, 'UTF-8') . '</label>';
      foreach ($items as $item_index => $item) {
        $it_visible = is_element_visible($visibility_map, $language, (int)$section_index, 'item', (int)$item_index);
        echo '<div class="editor-row editor-item-row">';
        echo '<div class="editor-row-main" style="display:flex;align-items:center;gap:8px;">';
        echo '<span class="item-index">' . ((int) $item_index + 1) . '.</span>';
        echo '<input type="hidden" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][item][' . (int)$item_index . ']" value="0">';
        echo '<input type="checkbox" name="visibility[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int)$section_index . '][item][' . (int)$item_index . ']" value="1"' . ($it_visible ? ' checked' : '') . '>';
        echo '<input type="text" class="editor-item-input" name="terms[' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '][' . (int) $section_index . '][items][' . (int) $item_index . ']" value="' . htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') . '">';
        echo '</div>';
        echo '<div class="editor-row-actions">';
        echo '<button type="submit" class="delete-btn btn-small" name="editor_action" value="delete_item|' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '|' . (int)$section_index . '|' . (int)$item_index . '">' . htmlspecialchars(ui_label('delete_item'), ENT_QUOTES, 'UTF-8') . '</button>';
        echo '</div>';
        echo '</div>';
      }
      echo '<div style="margin-top:6px;"><button type="submit" class="sub-action-btn btn-primary js-add-item" data-language="' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '" data-section-index="' . (int)$section_index . '" name="editor_action" value="add_item|' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '|' . (int) $section_index . '">' . htmlspecialchars(ui_label('add_item'), ENT_QUOTES, 'UTF-8') . '</button></div>';
      echo '</div>';

      echo '<div class="editor-section-actions">';
      echo '<button type="submit" class="delete-btn btn-small" name="editor_action" value="delete_section|' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '|' . (int) $section_index . '">' . htmlspecialchars(ui_label('delete_section'), ENT_QUOTES, 'UTF-8') . '</button>';
      echo '</div>';
      echo '</div>';
    }

    echo '<button type="submit" class="sub-action-btn btn-primary js-add-section" data-language="' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '" name="editor_action" value="add_section|' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(ui_label('add_section'), ENT_QUOTES, 'UTF-8') . '</button>';
    echo '</section>';
  }

  echo '<div class="editor-save-wrap">';
  echo '<button type="submit" class="toggle-btn btn btn-primary" name="editor_action" value="save">' . htmlspecialchars(ui_label('save_changes'), ENT_QUOTES, 'UTF-8') . '</button>';
  echo '</div>';
  // Debug visibility map for admin when editing (optional)
  if (!empty($visibility_map) && isset($_GET['debug_visibility'])) {
    echo '<pre style="background:#fff;padding:8px;border:1px solid #ccc;margin-top:12px;">' . htmlspecialchars(json_encode($visibility_map, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . '</pre>';
  }
  echo '</form>';
}

session_write_close();

function render_agree_cta($label, $href)
{
  echo '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">';
  echo '<header class="agree-header">';
  echo '<h1>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</h1>';
  echo '</header>';
  echo '</a>';
}

?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_text['title'], ENT_QUOTES, 'UTF-8'); ?></title>
<style>
  body { font-family: Arial, sans-serif; line-height: 1.6; }
  .container { width: 80%; margin: auto; overflow: hidden; }
  header { background: #50b3a2; color: white; padding: 20px; text-align: center; }
  section { margin: 20px 0; }
  h2 { color: #333; }
  ol { padding-left: 22px; }
  li { margin-bottom: 8px; }
  .agree-header {
    cursor: pointer;
    background: #50b3a2;
    color: #fff;
    padding: 20px;
    text-align: center;
  }

  /* Page mode toolbar */
  .page-mode-toolbar {
    width: 80%;
    margin: 15px auto;
    padding: 10px 14px;
    border: 1px solid #c9d4d2;
    background: #f5faf9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .page-mode-badge { font-weight: bold; color: #1e4d46; }

  /* Unified button styles */
  .btn, .sub-action-btn, .delete-btn, .toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 0;
    cursor: pointer;
    border-radius: 6px;
    padding: 6px 10px;
    font: inherit;
    text-decoration: none;
    vertical-align: middle;
  }
  .btn-small { padding: 4px 8px; font-size: 0.95rem; }
  .btn-primary, .sub-action-btn { background: #2f6fbd; color: #fff; }
  .btn-danger, .delete-btn { background: #b84545; color: #fff; }
  .btn-ghost { background: transparent; border: 1px solid #cbd9d6; color: #1e4d46; }
  .btn:focus { outline: 2px solid rgba(47,111,189,0.18); }
  .btn-group { display: inline-flex; gap: 8px; align-items: center; }

  .terms-editor-form { margin-top: 20px; }
  .editor-language-block {
    border: 1px solid #d8e3e1;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 12px;
    background: #fcfefe;
  }
  .editor-section-card {
    border: 1px solid #e4eceb;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 10px;
    background: #fff;
  }
  .editor-input, .editor-textarea {
    width: 100%; box-sizing: border-box; margin: 4px 0 8px; padding: 6px 8px; border: 1px solid #cbd9d6; border-radius: 6px; font: inherit;
  }

  /* Editor rows and actions */
  .editor-row { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 8px; }
  .editor-row-main { flex: 1; min-width: 0; }
  .editor-row-actions { flex: 0 0 auto; display: flex; gap: 6px; align-items: flex-start; }

  .editor-item-block { border: 1px solid #dce6e4; border-radius: 6px; background: #f9fcfb; padding: 8px; margin: 8px 0; }
  .editor-item-row { display: flex; align-items: center; gap: 6px; }
  .item-index { width: 28px; text-align: right; color: #1e4d46; font-weight: bold; flex: 0 0 auto; margin-right: 8px; }
  .editor-item-input { flex: 1; min-width: 0; box-sizing: border-box; padding: 6px 8px; border: 1px solid #cbd9d6; border-radius: 6px; font: inherit; }

  .editor-section-actions { margin-top: 8px; display: flex; justify-content: flex-end; }
  .editor-save-wrap { margin-top: 20px; }

  /* helper for screen-reader only labels (keeps markup light) */
  .sr-only { position: absolute; left: -10000px; top: auto; width: 1px; height: 1px; overflow: hidden; }
</style>
</head>
<body>
<header>
  <h1><?php echo htmlspecialchars($page_text['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
</header>

<?php if ($is_logged_in) { ?>
<div class="page-mode-toolbar">
  <span class="page-mode-badge">
    <?php echo htmlspecialchars($page_text['mode_prefix'], ENT_QUOTES, 'UTF-8'); ?><?php echo $is_edit_mode ? htmlspecialchars($page_text['mode_edit'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($page_text['mode_view'], ENT_QUOTES, 'UTF-8'); ?>
  </span>
  <?php if ($is_edit_mode) { ?>
    <a href="?type=<?php echo urlencode($current_type); ?>&mode=view"><button class="toggle-btn" type="button"><?php echo htmlspecialchars($page_text['switch_to_view'], ENT_QUOTES, 'UTF-8'); ?></button></a>
  <?php } else { ?>
    <a href="?type=<?php echo urlencode($current_type); ?>&mode=edit"><button class="toggle-btn" type="button"><?php echo htmlspecialchars($page_text['switch_to_edit'], ENT_QUOTES, 'UTF-8'); ?></button></a>
  <?php } ?>
</div>
<?php } ?>

<hr>
<div class="container">
<?php
if ($is_edit_mode && $is_logged_in) {
  render_terms_editor($terms_dictionary, $current_type, $visibility_map);
} else {
  foreach ($terms_dictionary as $language => $dictionary_sections) {
    render_terms_dictionary($dictionary_sections, $language, $visibility_map, $current_type);
    $agree_label = ($language === 'zh') ? $page_text['agree_zh'] : $page_text['agree_en'];
    render_agree_cta($agree_label, $page_text['booking_href']);
  }
}
?>

<?php if ($is_edit_mode && $is_logged_in) { ?>
<script>
// Client-side editor enhancements: intercept add actions and modify DOM
(function(){
  var L = {
    addParagraph: <?php echo json_encode(ui_label('add_paragraph')); ?>,
    deleteParagraph: <?php echo json_encode(ui_label('delete_paragraph')); ?>,
    addItem: <?php echo json_encode(ui_label('add_item')); ?>,
    deleteItem: <?php echo json_encode(ui_label('delete_item')); ?>,
    addSection: <?php echo json_encode(ui_label('add_section')); ?>,
    deleteSection: <?php echo json_encode(ui_label('delete_section')); ?>,
    showSection: <?php echo json_encode(ui_label('show_section')); ?>,
    sectionTitle: <?php echo json_encode(ui_label('section_title')); ?>,
    intro: <?php echo json_encode(ui_label('intro')); ?>,
    paragraphs: <?php echo json_encode(ui_label('paragraphs')); ?>,
    items: <?php echo json_encode(ui_label('items')); ?>,
    saveChanges: <?php echo json_encode(ui_label('save_changes')); ?>
  };

  function updateItemIndices(sectionCard){
    var itemIndexEls = sectionCard.querySelectorAll('.editor-item-row .item-index');
    itemIndexEls.forEach(function(el, idx){ el.textContent = (idx+1) + '.'; });
  }

  function makeParagraphRow(lang, sectionIndex, nextIndex){
    var wrapper = document.createElement('div');
    wrapper.className = 'editor-row';
    wrapper.innerHTML = '<div class="editor-row-main">'
      + '<input type="hidden" name="visibility['+lang+']['+sectionIndex+'][paragraph]['+nextIndex+']" value="0">'
      + '<input type="checkbox" style="margin-right:8px;" name="visibility['+lang+']['+sectionIndex+'][paragraph]['+nextIndex+']" value="1" checked>'
      + '<textarea class="editor-textarea" rows="2" name="terms['+lang+']['+sectionIndex+'][paragraphs]['+nextIndex+']"></textarea>'
      + '</div>'
      + '<div class="editor-row-actions">'
      + '<button type="button" class="delete-btn btn-small js-local-delete">'+L.deleteParagraph+'</button>'
      + '</div>';
    return wrapper;
  }

  function makeItemRow(lang, sectionIndex, nextIndex){
    var wrapper = document.createElement('div');
    wrapper.className = 'editor-row editor-item-row';
    wrapper.innerHTML = '<div class="editor-row-main" style="display:flex;align-items:center;gap:8px;">'
      + '<span class="item-index">'+(nextIndex+1)+'.</span>'
      + '<input type="hidden" name="visibility['+lang+']['+sectionIndex+'][item]['+nextIndex+']" value="0">'
      + '<input type="checkbox" name="visibility['+lang+']['+sectionIndex+'][item]['+nextIndex+']" value="1" checked>'
      + '<input type="text" class="editor-item-input" name="terms['+lang+']['+sectionIndex+'][items]['+nextIndex+']" value="">'
      + '</div>'
      + '<div class="editor-row-actions">'
      + '<button type="button" class="delete-btn btn-small js-local-delete">'+L.deleteItem+'</button>'
      + '</div>';
    return wrapper;
  }

  document.addEventListener('click', function(e){
    var btn;
    // Add paragraph
    btn = e.target.closest('.js-add-paragraph');
    if (btn){
      e.preventDefault();
      var lang = btn.getAttribute('data-language');
      var sectionIndex = btn.getAttribute('data-section-index');
      var sectionCard = btn.closest('.editor-section-card') || document.querySelector('.editor-section-card[data-language="'+lang+'"][data-section-index="'+sectionIndex+'"]');
      if (!sectionCard) return;
      var textareas = sectionCard.querySelectorAll('textarea[name*="[paragraphs]"]');
      var nextIndex = textareas.length;
      var insertBefore = sectionCard.querySelector('.editor-item-block') || sectionCard.querySelector('.editor-section-actions') || null;
      var newRow = makeParagraphRow(lang, sectionIndex, nextIndex);
      sectionCard.insertBefore(newRow, insertBefore);
      return;
    }

    // Add item
    btn = e.target.closest('.js-add-item');
    if (btn){
      e.preventDefault();
      var lang = btn.getAttribute('data-language');
      var sectionIndex = btn.getAttribute('data-section-index');
      var sectionCard = btn.closest('.editor-section-card') || document.querySelector('.editor-section-card[data-language="'+lang+'"][data-section-index="'+sectionIndex+'"]');
      if (!sectionCard) return;
      var itemBlock = sectionCard.querySelector('.editor-item-block');
      if (!itemBlock) return;
      var inputs = itemBlock.querySelectorAll('input[name*="[items]"]');
      var nextIndex = inputs.length;
      var newRow = makeItemRow(lang, sectionIndex, nextIndex);
      // insert before the add button inside itemBlock
      var addBtn = itemBlock.querySelector('.js-add-item');
      itemBlock.insertBefore(newRow, addBtn ? addBtn.parentNode : null);
      updateItemIndices(sectionCard);
      return;
    }

    // Add section
    btn = e.target.closest('.js-add-section');
    if (btn){
      e.preventDefault();
      var lang = btn.getAttribute('data-language');
      var langBlock = document.querySelector('.editor-language-block[data-language="'+lang+'"]');
      if (!langBlock) return;
      var sections = Array.from(langBlock.querySelectorAll('.editor-section-card'));
      var maxIndex = -1;
      sections.forEach(function(s){ var idx = parseInt(s.getAttribute('data-section-index'),10); if (!isNaN(idx) && idx>maxIndex) maxIndex = idx; });
      var newIndex = maxIndex + 1;
      var template = sections[sections.length-1] || null;
      var newSection;
      if (template){
        newSection = template.cloneNode(true);
        var oldIndex = template.getAttribute('data-section-index');
        newSection.setAttribute('data-section-index', newIndex);
        // update names and values
        var elems = newSection.querySelectorAll('[name],button');
        elems.forEach(function(el){
          if (el.hasAttribute('name')){
            el.name = el.name.replace(new RegExp('\\['+oldIndex+'\\]','g'),'['+newIndex+']');
          }
          if (el.tagName.toLowerCase() === 'button' && el.getAttribute('name') === 'editor_action'){
            var v = el.value || el.getAttribute('value') || '';
            v = v.replace('|' + oldIndex + '|','|' + newIndex + '|').replace('|' + oldIndex, '|' + newIndex);
            el.value = v;
          }
          // reset inputs
          if (el.tagName.toLowerCase() === 'input'){
            if (el.type === 'text') el.value = '';
            if (el.type === 'checkbox') el.checked = true;
            if (el.type === 'hidden' && el.name.indexOf('visibility')!==-1) el.value = '0';
          }
          if (el.tagName.toLowerCase() === 'textarea') el.value = '';
        });
        // make delete buttons local for new section
        var delBtns = newSection.querySelectorAll('.delete-btn');
        delBtns.forEach(function(db){ db.setAttribute('type','button'); db.classList.add('js-local-delete-section'); db.textContent = L.deleteSection; });
        // ensure add buttons target the new section index
        var addPar = newSection.querySelector('.js-add-paragraph'); if (addPar) addPar.setAttribute('data-section-index', newIndex);
        var addIt = newSection.querySelector('.js-add-item'); if (addIt) addIt.setAttribute('data-section-index', newIndex);
      } else {
        // build minimal template
        newSection = document.createElement('div');
        newSection.className = 'editor-section-card';
        newSection.setAttribute('data-language', lang);
        newSection.setAttribute('data-section-index', newIndex);
        newSection.innerHTML = '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">'
          + '<label>'+L.showSection+'</label>'
          + '<input type="hidden" name="visibility['+lang+']['+newIndex+'][section][0]" value="0">'
          + '<input type="checkbox" name="visibility['+lang+']['+newIndex+'][section][0]" value="1" checked>'
          + '</div>'
          + '<label>'+L.sectionTitle+'</label>'
          + '<input type="hidden" name="visibility['+lang+']['+newIndex+'][title][0]" value="0">'
          + '<input type="checkbox" name="visibility['+lang+']['+newIndex+'][title][0]" value="1" checked>'
          + '<input type="text" class="editor-input" name="terms['+lang+']['+newIndex+'][title]" value="">'
          + '<label>'+L.intro+'</label>'
          + '<input type="hidden" name="visibility['+lang+']['+newIndex+'][intro][0]" value="0">'
          + '<input type="checkbox" name="visibility['+lang+']['+newIndex+'][intro][0]" value="1" checked>'
          + '<textarea class="editor-textarea" rows="2" name="terms['+lang+']['+newIndex+'][intro]"></textarea>'
          + '<label>'+L.paragraphs+'</label>'
          + '<div class="editor-row">'
            + '<div class="editor-row-main">'
              + '<input type="hidden" name="visibility['+lang+']['+newIndex+'][paragraph][0]" value="0">'
              + '<input type="checkbox" style="margin-right:8px;" name="visibility['+lang+']['+newIndex+'][paragraph][0]" value="1" checked>'
              + '<textarea class="editor-textarea" rows="2" name="terms['+lang+']['+newIndex+'][paragraphs][0]"></textarea>'
            + '</div>'
            + '<div class="editor-row-actions"><button type="button" class="delete-btn btn-small js-local-delete">'+L.deleteParagraph+'</button></div>'
          + '</div>'
          + '<div style="margin-top:6px;"><button type="submit" class="sub-action-btn btn-primary js-add-paragraph" data-language="'+lang+'" data-section-index="'+newIndex+'">'+L.addParagraph+'</button></div>'
          + '<div class="editor-item-block">'
            + '<label>'+L.items+'</label>'
            + '<div class="editor-row editor-item-row">'
              + '<div class="editor-row-main" style="display:flex;align-items:center;gap:8px;">'
                + '<span class="item-index">1.</span>'
                + '<input type="hidden" name="visibility['+lang+']['+newIndex+'][item][0]" value="0">'
                + '<input type="checkbox" name="visibility['+lang+']['+newIndex+'][item][0]" value="1" checked>'
                + '<input type="text" class="editor-item-input" name="terms['+lang+']['+newIndex+'][items][0]" value="">'
              + '</div>'
              + '<div class="editor-row-actions"><button type="button" class="delete-btn btn-small js-local-delete">'+L.deleteItem+'</button></div>'
            + '</div>'
            + '<div style="margin-top:6px;"><button type="submit" class="sub-action-btn btn-primary js-add-item" data-language="'+lang+'" data-section-index="'+newIndex+'">'+L.addItem+'</button></div>'
          + '</div>'
          + '<div class="editor-section-actions"><button type="button" class="delete-btn btn-small js-local-delete-section">'+L.deleteSection+'</button></div>';
      }
      var addSectionBtn = langBlock.querySelector('.js-add-section');
      langBlock.insertBefore(newSection, addSectionBtn);
      return;
    }

    // local delete for newly added rows
    var localDel = e.target.closest('.js-local-delete');
    if (localDel){
      e.preventDefault();
      var row = localDel.closest('.editor-row'); if (row) row.parentNode.removeChild(row);
      // update indices for items if needed
      var sectionCard = localDel.closest('.editor-section-card'); if (sectionCard) updateItemIndices(sectionCard);
      return;
    }

    var localDelSec = e.target.closest('.js-local-delete-section');
    if (localDelSec){
      e.preventDefault();
      var sec = localDelSec.closest('.editor-section-card'); if (sec) sec.parentNode.removeChild(sec);
      return;
    }
  });
})();
</script>
<?php } ?>

<br>
<br>
<br>
<br>
<br>
<hr>

<footer>
  <p><?php echo htmlspecialchars($page_text['copyright'], ENT_QUOTES, 'UTF-8'); ?></p>
</footer>

</body>
</html>
