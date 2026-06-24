<?php

require_once 'logger.php';

require_once 'setting-admin.php';

t_log('begin[input-form.php]');

// Function to detect browser
function getBrowser() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    if (strpos($userAgent, 'Chrome') !== false) {
        return 'Chrome';
    } elseif (strpos($userAgent, 'Edge') !== false) {
        return 'Edge';
    } elseif (strpos($userAgent, 'Firefox') !== false) {
        return 'Firefox';
    } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
        return 'Safari'; // Exclude Chrome since it also includes 'Safari'
    } else {
        return 'Other';
    }
}


function is_mobile_browser() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);

    $keywords = [
        'android', 'iphone', 'ipad', 'ipod',
        'blackberry', 'windows phone', 'opera mini',
        'mobile', 'silk', 'kindle'
    ];

    foreach ($keywords as $k) {
        if (strpos($ua, $k) !== false) {
            return true;
        }
    }

    return false;
}

// Get the browser
$browser = getBrowser();

// Display a message based on the browser
if ($browser === 'Chrome' || $browser === 'Edge' || $browser === 'Firefox' || $browser === 'Safari') {
    // echo "You are using $browser, great choice!";
} else {
    echo "<h1>看來您沒有使用 Chrome、Edge、Firefox 或 Safari。請考慮切換到其中一個瀏覽器以獲得最佳體驗。 <hr> It seems you're not using Chrome, Edge, Firefox, or Safari. Please consider switching to one of these browsers for the best experience.</h1><hr>".$_SERVER['HTTP_USER_AGENT'];
    exit();
}
?>
<?php 

session_start();
$is_management = isset($_SESSION["management"]);

// Prefer explicit GET `type` over session when deciding which reservation UI to show.
// Normalize value to lowercase and trim whitespace.
$reserve_type = '';
if (isset($_GET['type']) && strlen(trim((string)$_GET['type'])) > 0) {
    $reserve_type = strtolower(trim((string)$_GET['type']));
} elseif (isset($_SESSION['type']) && strlen(trim((string)$_SESSION['type'])) > 0) {
    $reserve_type = strtolower(trim((string)$_SESSION['type']));
} else {
    $reserve_type = 'golf';
}

 ?><?php 
require_once 'tesing_stage_verification.php';
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>
    白石高爾夫球練習場 打球位置 預訂表格
    
    White Head Club - Booking Form for Golf Court
</title>

    <style>
/* ============================================================
   MODERN GOLF BOOKING FORM — CLEAN REDESIGN
   ============================================================ */
:root{--green-dark:#1a5632;--green-mid:#2d7a4a;--green-light:#e8f5e9;--gold:#c9a94e;--gold-light:#fdf6e3;--white:#ffffff;--gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;--gray-300:#d1d5db;--gray-400:#9ca3af;--gray-500:#6b7280;--gray-600:#4b5563;--gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;--red:#dc2626;--red-light:#fef2f2;--blue:#2563eb;--blue-light:#eff6ff;--radius-sm:6px;--radius:8px;--radius-lg:12px;--shadow-sm:0 1px 2px rgba(0,0,0,0.04);--shadow:0 1px 3px rgba(0,0,0,0.08),0 1px 2px rgba(0,0,0,0.04);--shadow-md:0 3px 6px rgba(0,0,0,0.06),0 2px 4px rgba(0,0,0,0.04);--shadow-lg:0 6px 12px rgba(0,0,0,0.08),0 3px 6px rgba(0,0,0,0.04);--transition:0.15s ease}
*{box-sizing:border-box;margin:0;padding:0}
html{background:linear-gradient(135deg,#e8f5e9 0%,#f1f8e9 30%,#f9fafb 100%);background-attachment:fixed;min-height:100%}
body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;color:var(--gray-800);line-height:1.6;-webkit-font-smoothing:antialiased;display:flex;justify-content:center;padding:16px}
.input-container{width:100%;max-width:900px;display:flex;justify-content:center}
#frame{width:100%;background:var(--white);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:24px 28px;overflow:hidden}
h1,h2,h3,h4{color:var(--green-dark);font-weight:700;line-height:1.3}
h1{font-size:1.5rem;letter-spacing:-0.02em}
h3{font-size:1.1rem;margin:0}
h4{font-size:1rem}
small{font-size:0.75rem;color:var(--gray-500)}
.form-label{font-size:0.82rem;font-weight:600;color:var(--gray-600);display:inline;margin-bottom:2px}
.section-header{background:linear-gradient(135deg,var(--green-dark),var(--green-mid));color:white;padding:10px 16px;border-radius:var(--radius);margin:16px 0 10px 0;font-weight:700;font-size:0.95rem;display:flex;align-items:center;gap:6px;letter-spacing:0.01em}
.section-header hr{display:none}
input[type="text"],input[type="email"],input[type="tel"],input[type="number"],select,textarea{width:100%;padding:9px 12px;border:1.5px solid var(--gray-200);border-radius:var(--radius);font-size:0.9rem;font-family:inherit;color:var(--gray-800);background:var(--gray-50);transition:all var(--transition);outline:none;-webkit-appearance:none;appearance:none}
input[type="text"]:focus,input[type="email"]:focus,input[type="tel"]:focus,select:focus,textarea:focus{border-color:var(--green-mid);box-shadow:0 0 0 2px rgba(45,122,74,0.15);background:var(--white)}
input[type="text"]:hover,select:hover,textarea:hover{border-color:var(--gray-300)}
select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:28px;cursor:pointer;min-height:auto}
.submit-button,input[type="submit"]{width:100%!important;padding:12px 24px!important;background:linear-gradient(135deg,var(--green-dark),var(--green-mid))!important;color:white!important;border:none!important;border-radius:var(--radius)!important;font-size:1rem!important;font-weight:700!important;cursor:pointer!important;transition:all var(--transition)!important;letter-spacing:0.03em;text-transform:uppercase;box-shadow:var(--shadow-sm)}
.submit-button:hover:not(:disabled),input[type="submit"]:hover:not(:disabled){transform:translateY(-1px);box-shadow:var(--shadow-lg);filter:brightness(1.1)}
.submit-button:disabled,input[type="submit"]:disabled{background:var(--gray-300)!important;color:var(--gray-400)!important;cursor:not-allowed!important;box-shadow:none!important}
.discount_radio{color:var(--gray-800);border:1.5px solid var(--gray-200)!important;border-radius:var(--radius)!important;text-align:center;height:auto!important;min-height:3.5em;width:100%!important;padding:8px 6px!important;font-size:0.85rem!important;font-weight:600;transition:all var(--transition);background:var(--white)!important;cursor:pointer}
.discount_radio:hover{border-color:var(--green-mid)!important;background:var(--green-light)!important}
.container{display:block;position:relative;cursor:pointer;user-select:none;width:100%}
.container input[type="checkbox"],.container input[type="radio"]{position:absolute;opacity:0;cursor:pointer;height:0;width:0}
.checkmark{display:flex;align-items:center;justify-content:center;background-color:var(--gray-100);border:1.5px solid var(--gray-200);border-radius:var(--radius);padding:6px 10px;min-height:2.2em;font-size:0.85rem;font-weight:600;color:var(--gray-700);transition:all var(--transition);text-align:center;width:100%}
.container:hover input~.checkmark{border-color:var(--green-mid);background:var(--green-light)}
.container input:checked~.checkmark{background:var(--green-dark);color:white;border-color:var(--green-dark);box-shadow:var(--shadow-md)}
.checkmark:after{display:none}
.container input:checked~.checkmark:after{display:block}
.position{font-size:1rem!important;font-weight:700;border:none!important;width:auto!important}
.position_checkbox{margin:0!important;padding:0!important}
.expend{cursor:pointer;padding:10px 14px;background:var(--gray-50)!important;border:1px solid var(--gray-200);border-radius:var(--radius);margin:6px 0;transition:all var(--transition);font-weight:600;color:var(--gray-700);font-size:0.9rem}
.expend:hover{background:var(--green-light)!important;border-color:var(--green-mid)}
.expend hr{display:none}
.expend_area{background:var(--white);border:1px solid var(--gray-200);border-radius:var(--radius);margin-bottom:12px;overflow:hidden}
table{width:100%;border-collapse:collapse}
th,td{text-align:left;padding:6px 10px;vertical-align:middle}
.bracket{font-size:1.1rem;font-weight:700;color:var(--gray-500);vertical-align:middle}
.back-link{display:inline-flex;align-items:center;gap:4px;color:var(--green-dark);text-decoration:none;font-weight:600;font-size:0.85rem;padding:6px 12px;border-radius:var(--radius-sm);transition:all var(--transition);background:var(--green-light);margin-bottom:6px}
.back-link:hover{background:var(--green-dark);color:white}
.price-link{display:inline-block;margin-top:8px;color:var(--green-mid);font-weight:600;text-decoration:underline}
.help-text{font-size:0.82rem;color:var(--gray-500);margin-top:4px;line-height:1.5}
.help-text-warn{color:var(--red);font-size:0.82rem;margin-top:4px;line-height:1.5}
.submit-notice{font-size:0.75rem;color:var(--gray-400);margin-top:12px;text-align:center;font-style:italic}
hr{border:none;border-top:1px solid var(--gray-200);margin:12px 0}
#confirmation_code{color:var(--blue);font-weight:600}
#confirmation_code:hover{border-color:var(--gold);background:var(--gold-light)}
#confirmation_button{padding:9px 12px!important;cursor:pointer;background:var(--blue-light)!important;border:1.5px solid var(--blue)!important;color:var(--blue)!important;font-weight:600;font-size:0.85rem!important}
#confirmation_button:hover{background:var(--blue)!important;color:white!important}
.container .checkmark2:after{font-size:1rem;content:"✓ 已選取 Checked"}
.navbar{background-color:var(--green-dark);position:fixed;top:0;width:100%;z-index:100;box-shadow:var(--shadow)}
.navbar a{float:left;display:block;color:white;text-align:center;padding:14px 18px;text-decoration:none;font-weight:500;transition:background var(--transition)}
.navbar a:hover{background:var(--green-mid)}
input[type="checkbox"]{margin:0;padding:0;width:auto;height:auto}
td,th{vertical-align:top}
.span_checkbox{font-size:0.85rem;min-height:2.5em}
.show_for_desktop{display:inline-block}
.hide_for_desktop{display:none}
.hide_for_mobile{display:block}
.widen_checkbox{width:100%}
@media only screen and (max-width:768px){
#frame{padding:16px 12px;border-radius:var(--radius)}
h1{font-size:1.15rem}
h3{font-size:0.95rem}
input[type="text"],input[type="email"],select,textarea{font-size:0.85rem;padding:8px 10px}
.section-header{padding:8px 12px;font-size:0.85rem;margin:14px 0 8px 0}
.discount_radio{font-size:0.8rem!important;min-height:3.2em}
.checkmark{font-size:0.8rem;min-height:2em}
.bracket{font-size:1rem}
.expend{font-size:0.85rem;padding:8px 12px}
.form-label{font-size:0.78rem}
input[type="submit"],.submit-button{font-size:0.9rem!important;padding:10px 18px!important}
.show_for_desktop{display:none}
.hide_for_desktop{display:inline-block}
.span_checkbox{font-size:0.8rem;min-height:2.2em}
}

/* ═══════════════════════════════════════════════════════════
   EXTENDED STYLING — ANIMATIONS, CARDS, STATUS & POLISH
   ═══════════════════════════════════════════════════════════ */

/* ── Keyframes Animations ── */
@keyframes fadeInUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.6} }
@keyframes slideDown { from{opacity:0;max-height:0} to{opacity:1;max-height:600px} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes checkPop { 0%{transform:scale(0.8);opacity:0} 60%{transform:scale(1.08)} 100%{transform:scale(1);opacity:1} }

/* ── Fade-in for selection area ── */
.selection_area { animation: fadeInUp 0.35s ease-out; }

/* ── Expand/Collapse animation ── */
.expend_area:not([style*="display: none"]) { animation: slideDown 0.4s ease-out; }

/* ── Badge ── */
.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.badge-required { background: var(--red); color: white; }
.badge-optional { background: var(--gray-200); color: var(--gray-600); }

/* ── Title / Hero Banner ── */ .page-hero { text-align:center; padding:10px 0 6px 0; margin-bottom:4px; } .page-hero h1 { font-size:1.5rem; font-weight:800; background:linear-gradient(135deg,var(--green-dark),var(--green-mid)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:2px; } .page-hero .subtitle { font-size:0.78rem; color:var(--gray-500); letter-spacing:0.02em; } .page-hero hr { margin:10px auto; max-width:160px; }

/* ── Input Group (label + input) ── */ .input-group { margin-bottom:10px; } .input-group .mandatory-star { color:var(--red); font-weight:700; margin-right:2px; }

/* ── Confirmation Row (email + button side-by-side) ── */ .confirm-row { display:flex; gap:8px; align-items:flex-start; } .confirm-row .confirm-email-col { flex:1; min-width:0; } .confirm-row .confirm-btn-col { flex-shrink:0; min-width:140px; }

/* ── Styled Textarea ── */ textarea { resize:vertical; min-height:100px; line-height:1.5; font-family:inherit; } textarea:focus { border-color:var(--green-mid); box-shadow:0 0 0 2px rgba(45,122,74,0.15); }

/* ── Availability Legend ── */ .legend-table { width:auto !important; margin:8px 0; border:1px solid var(--gray-200); border-radius:var(--radius); overflow:hidden; } .legend-table td { padding:6px 12px; font-size:0.82rem; font-weight:500; }
.legend-available { background: #91FE69; color: var(--green-dark); }
.legend-booked { background: #FE8569; color: #7f1d1d; }

/* ── Pricing Option Cards Section ── */ .pricing-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin:8px 0 10px 0; } .pricing-option-label { text-align:center; cursor:pointer; } .pricing-option-label .discount_radio { min-height:4em; display:flex; align-items:center; justify-content:center; flex-direction:column; }

/* ── Octopus Number Input Row ── */ .octopus-row { display:flex; align-items:center; gap:4px; flex-wrap:wrap; } .octopus-row input[type="text"] { width:auto; flex:1; min-width:80px; } .octopus-row input.octopus-main { flex:2; min-width:120px; } .octopus-row input.octopus-q { flex:1; min-width:60px; }

/* ── Submit Section ── */ .submit-section { margin-top:16px; padding-top:12px; border-top:1.5px solid var(--gray-200); }

/* ── Remarks Label ── */ .remarks-label { font-size:0.95rem; font-weight:700; color:var(--green-dark); margin-bottom:4px; }

/* ── Availability Dot Indicator ── */
.availability-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 6px;
}
.availability-dot.avail { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
.availability-dot.booked { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.2); }

/* ── Court Selection Table ── */
.court-table { width: 100%; }
.court-table tr { transition: background var(--transition); }
.court-table tr:hover { background: var(--green-light); }
.court-table .court-number {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--green-dark);
    min-width: 80px;
    padding: 8px 14px;
}

/* ── "Back" link positioned nicely ── */
.top-nav-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

/* ── Tooltip-style hint ── */
.hint-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--gray-300);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    cursor: help;
    margin-left: 4px;
    vertical-align: middle;
}

/* ── Loading/Shimmer placeholders ── */
.shimmer {
    background: linear-gradient(90deg, var(--gray-100) 25%, var(--gray-200) 50%, var(--gray-100) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

/* ── Mandatory asterisk ── */
.mandatory { color: var(--red); font-weight: 700; }

/* ── Smooth scroll ── */
html { scroll-behavior: smooth; }

/* ── Print styles ── */
@media print {
    html { background: white; }
    #frame { box-shadow: none; border: 1px solid #ccc; }
    .submit-button, .expend, .back-link { display: none; }
}

/* ── Additional responsive tweaks ── */
@media only screen and (max-width: 768px) {
    .pricing-grid { grid-template-columns: 1fr; gap: 6px; }
    .confirm-row { flex-direction: column; }
    .confirm-row .confirm-btn-col { min-width: 100%; }
    .octopus-row { gap: 4px; }
    .page-hero h1 { font-size: 1.2rem; }
}

/* ── Confirmation Code Button & Input ── */
#confirmation_code:hover,
#confirmation_button:hover { background-color: #fef9c3 !important; }
#confirmation_button { padding: 8px; margin: 6px; }

/* ── Half Hour Option Text ── */
.half_hour_option { text-align: left; }

/* ── Discount Notice Span sizing ── */
#discount_notice_span_hourly,
#discount_notice_span_student,
#discount_notice_span_disabled {
    height: 3.2em; font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
}

/* ── Sand Bay & Vehicle option spans ── */
#sand_bay_option_span,
#with_vehicle_span {
    height: 2.4em; font-size: 0.85rem;
    display: flex; align-items: center; justify-content: center;
}
@media only screen and (max-width: 1300px) {
    #sand_bay_option_span,
    #with_vehicle_span { height: 2.8em; font-size: 1rem; }
}

/* ── Widen Checkbox ── */
.widen_checkbox { width: 100%; accent-color: var(--green-mid); }

/* ── Remark Textarea ── */ .remark-textarea { width:100%; min-height:100px; padding:10px 12px; border:1.5px solid var(--gray-200); border-radius:var(--radius); background:var(--gray-50); font-size:0.85rem; line-height:1.5; transition:all var(--transition); resize:vertical; font-family:inherit; } .remark-textarea:focus { border-color:var(--green-mid); background:white; box-shadow:0 0 0 2px rgba(45,122,74,0.12); outline:none; }

/* ── Submit Notice Text ── */ .submit-notice { display:block; margin-top:8px; padding:8px 12px; background:#fffbeb; border-left:3px solid #f59e0b; border-radius:0 var(--radius-sm) var(--radius-sm) 0; color:#92400e; font-size:0.72rem; line-height:1.5; } .submit-notice strong { color:#92400e; }

/* ── Court selection area tweaks ── */ .selection_area table td { padding:2px 6px; }

/* ═══════════════════════════════════════════════════════════
   FRAMEWORK OVERRIDE — BREAK TABLE CONSTRAINTS & UNIFORM SIZING
   ═══════════════════════════════════════════════════════════ */

/* ── Force main form table to behave as a block container ── */
form > table[style*="background-color: white"],
form > table {
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
}

/* Make each direct tr a block-level row */
form > table[style*="background-color: white"] > tbody,
form > table > tbody {
    display: block !important;
    width: 100% !important;
}

form > table[style*="background-color: white"] > tbody > tr,
form > table > tbody > tr {
    display: block !important;
    width: 100% !important;
    margin-bottom: 4px;
}

/* Make td blocks with full width */
form > table[style*="background-color: white"] > tbody > tr > td,
form > table > tbody > tr > td {
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box;
    padding: 8px 0 !important;
}

/* ── UNIFORM INPUT SIZING — override ALL inline widths ── */
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="number"],
select,
textarea {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    box-sizing: border-box;
}

/* ── Override inline styles on octopus inputs ── */
input.octopus-main,
input.octopus-q,
input[name="octopus_no"],
input[name="octopus_no_q"],
input[name="octopus_no_cf"],
input[name="octopus_no_q_cf"] {
    width: 100% !important;
    max-width: 100% !important;
    flex: 1 1 auto !important;
}

/* ── Override nested table inline widths ── */
table[style*="width: 90%"],
table[style*="width: 100%"] {
    width: 100% !important;
    display: block !important;
}

table[style*="width: 90%"] tbody,
table[style*="width: 100%"] tbody,
table[style*="width: 90%"] tr,
table[style*="width: 100%"] tr {
    display: block !important;
    width: 100% !important;
}

table[style*="width: 90%"] td,
table[style*="width: 100%"] td {
    display: block !important;
    width: 100% !important;
    box-sizing: border-box;
}

/* Override inline td widths: 60%, 40%, 50%, 20% */
td[style*="width:"] {
    width: 100% !important;
    display: block !important;
    box-sizing: border-box;
}

/* ── Email confirmation row: kept as inline-flex side-by-side ── */
form > table td table td[style*="width: 60%"],
form > table td table td[style*="width: 40%"] {
    display: inline-block !important;
    width: auto !important;
    vertical-align: top;
}

/* The email confirmation table should stay as a flex row */
form > table td table:has(#confirm_email) {
    display: flex !important;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-start;
}
form > table td table:has(#confirm_email) tbody,
form > table td table:has(#confirm_email) tr {
    display: flex !important;
    flex-wrap: wrap;
    gap: 10px;
    width: 100%;
}
form > table td table:has(#confirm_email) td[style*="width: 60%"] {
    flex: 2 1 240px !important;
    min-width: 200px;
    display: block !important;
}
form > table td table:has(#confirm_email) td[style*="width: 40%"] {
    flex: 1 1 180px !important;
    min-width: 160px;
    display: block !important;
}

/* ── Pricing options: keep 3-column grid ── */
table:has(.discount_radio) {
    display: block !important;
    width: 100% !important;
}
table:has(.discount_radio) tbody {
    display: block !important;
    width: 100% !important;
}
table:has(.discount_radio) tr {
    display: flex !important;
    flex-wrap: wrap;
    gap: 12px;
    width: 100% !important;
}
table:has(.discount_radio) td {
    flex: 1 1 180px !important;
    min-width: 140px;
    display: block !important;
    padding: 4px !important;
}

/* ── Vehicle & sand bay row: keep 2-column on desktop ── */
table:has(#with_vehicle) {
    display: block !important;
    width: 100% !important;
}
table:has(#with_vehicle) tbody,
table:has(#with_vehicle) tr {
    display: flex !important;
    flex-wrap: wrap;
    gap: 12px;
    width: 100% !important;
}
table:has(#with_vehicle) td {
    flex: 1 1 200px !important;
    min-width: 160px;
    display: block !important;
}

/* ── Octopus number row: keep input + bracket inline ── */
.octopus_block td {
    display: block !important;
    width: 100% !important;
}

/* ── SELECT elements: consistent sizing ── */ select { width:100% !important; max-width:100% !important; min-height:38px; }

/* ── Input group spacing ── */ .form-group { margin-bottom:12px; }

/* ── Responsive adjustments for small screens ── */
@media only screen and (max-width: 768px) {
    table:has(.discount_radio) td {
        flex: 1 1 100% !important;
    }
    table:has(#with_vehicle) td {
        flex: 1 1 100% !important;
    }
    form > table td table:has(#confirm_email) {
        flex-direction: column;
    }
    form > table td table:has(#confirm_email) td[style*="width: 60%"],
    form > table td table:has(#confirm_email) td[style*="width: 40%"] {
        flex: 1 1 auto !important;
        min-width: auto !important;
    }
}

/* ── Forcibly remove transparent dummy inputs ── */
input[style*="background-color: transparent"][disabled] {
    display: none !important;
}
div[style*="color: transparent"] {
    display: none !important;
}

/* ── Uniform margin for section blocks ── */ .section-header + br { display:none; } form > table[style*="background-color: white"] > tbody > tr { margin-bottom:2px; } form > table[style*="background-color: white"] > tbody > tr > td { padding:4px 0 !important; }
    </style>
</head>




































<body>

<?php 
require_once 'booking-status-json-variable.php';
 ?>

<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//analytics.austreme.com/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '215']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>

    <div class="input-container">
<div id="frame">







<form method="get" action="./email-confirmation.php">
    <input type="hidden" name="type" value="<?php echo htmlspecialchars($reserve_type, ENT_QUOTES, 'UTF-8'); ?>">

<!-- 
    <h1 style="color: red;">
        
網站正在建置中，非開發者請勿嘗試透過本網站進行任何操作。  如果您想立即預訂，請自行前往高爾夫球場預訂。
<br>
The website is under construction. Non-developers should not attempt to perform any operations through this website. If you would like to book immediately, please make your own reservation at the golf course.

    </h1> -->
<table style="background-color: white;">
    <tr>
        <td colspan="2">
            <a href="." class="back-link">← Back 返回</a>
            <div class="page-hero">
            <h1>
            <?php 
if ($reserve_type == 'pickleball') {
?>
🏓 匹克球<br>預訂表格<br> 
Pickleball<br>Reservation Form
<?php 
} else {
?>
⛳ 白石高爾夫球<br>預訂表格<br> 
White Head Club<br>Reservation Form
<?php 
}
            ?>
            </h1>
            <p class="subtitle">請填妥以下資料以完成預訂 &bull; Please fill in the details below</p>
            <hr>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
<?php 
date_default_timezone_set('Asia/Hong_Kong');

$currentDate = new DateTime();
$current_timestamp = $currentDate->format('Y-m-d').'T'.$currentDate->format('H:i:s');
// echo $current_timestamp;


$futureOneHourDate = new DateTime();
$futureOneHourDate->modify('+1 hour');
$futureOneHour_timestamp = $futureOneHourDate->format('Y-m-d').'T'.$futureOneHourDate->format('H:00:00');
// echo "$futureOneHour_timestamp";



 ?>
(*) 必填項 Mandatory field
<div class="section-header">👤 個人資訊 Personal Information</div>

        </td>
    </tr>
    <tr><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>
            <small>
                <span class="form-label">姓名 Full Name</span>
            </small>
            <br>
            *<input 
            class="form-control" 
            type="text" 
            name="name" 
            id="name" 
            placeholder="姓名 Name" 
            required 
            autocomplete="on"
            value="<?php
                $cookie_name = "name"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                    echo $_COOKIE[$cookie_name];
                }
             ?>" 
            ><br></td>
            <script>
                function checkNameEmpty() {
                    const nameValue = document.getElementById("name").value.trim();
                    return (nameValue === "");
                }
            </script>
    </tr>
    <tr><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>
            <small>
                <span class="form-label">電子郵件地址 Email Address</span>
            </small>
            <br>
            <table style="width: 90%;">
                <tr>
                    <td style="width: 60%;">
                        
            *<input 
            class="form-control" 
            type="text" 
            name="email" 
            id="confirm_email" 
            placeholder="電子郵件地址 Email address" 
            required 
            autocomplete="on"
            value="<?php
                $cookie_name = "email"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                    echo $_COOKIE[$cookie_name];
                }
             ?>" 
            >
                    </td>
                    <td style="width: 40%;">

                        <input 
                            type="text" 
                            class="form-control" 
                            onclick="send_confirm_code(false)" 
                            id="confirmation_button" 
                            placeholder="按此發送6位數字驗證碼 \n Click here to send 6-digit confirmation code" 
                            readonly>

                        <input type="hidden" name="open_datetime" id="open_datetime" value="<?php
                            echo $current_timestamp;
                         ?>" readonly>

                    </td>
                </tr>
            </table>
            







        </td>
    </tr>
    <tr>
                    <td>



                    </td>
    </tr>
    <tr style="color: blue;"><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>


<table style="width: 100%;">
                <tr>
                    <td style="width: 100%;" >
                        <small>
                            <span class="form-label">郵件地址 - 驗證碼 Confirmation Code</span>
                        </small>
                        <br>
                        *<input 
            class="form-control" 
            type="text" 
            name="confirmation_code" 
            id="confirmation_code" 
            style="color: blue;" 
            placeholder="驗證碼 Verification code" 
            onkeydown="setTimeout(function() {
                checkConfirmCode(false);
            }, 1);" 
            onblur="setTimeout(function() {
                checkConfirmCode(false);
            }, 1);" 
            onclick="setTimeout(function() {
                checkConfirmCode(false);
            }, 1);" 

            autocomplete="off" 
            required><br>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        
                        <small style="color: red;">
                            我們的電子郵件可能會被網域名稱或郵件信箱過濾。如果您無法收到確認碼，請嘗試其他電子郵件地址。
                            <br>
                            Our emails may be filtered by domain names or mailbox. If you are unable to receive the confirmation code, please try a different email address.
                        </small>
                    </td>
                </tr>
            </table>

    <script type="text/javascript">

        function setCookie(name, value, days) {
            console.log('setCookie:',name, value);
            // Remove the cookie by setting an expired date
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";

            // Set the new cookie
            let expires = "";
            if (days) {
                let date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
        }




        confirmed = false;
        confirming = false;
        function checkCodeConfirmed(already_checked, bypass_alert) {
            // console.log('Correct', html);


            setCookie("name_rivergolf", document.getElementById('name').value, 90);


            confirmed = true;
            if (already_checked) {
                if (!bypass_alert) {
                    alert('不需要確認碼 該電子郵件地址之前已驗證過 ! Confirmation code is not needed, this email address was validated before!');
                }
                document.getElementById('confirmation_code').value = '不需要 Not needed';
            } else {
                alert('驗證碼正確! Confirmation code correct!');
            }
            
            confirmation_code.style.backgroundColor = '#91FE69';

            document.getElementById('name').readOnly = true;
            document.getElementById('confirm_email').readOnly = true;
            document.getElementById('confirmation_code').readOnly = true;
            document.getElementById('confirmation_button').disabled = true;
        }
        function checkConfirmCode(bypass_alert) {
            if (confirmed || confirming) {
                console.log('Already confirmed or confirming');
                return;
            }
            email = document.getElementById('confirm_email').value;
            confirmation_code = document.getElementById('confirmation_code');
            
            // console.log(confirmation_code.value, confirmation_code.value.length)
            if (confirmation_code.value.length == 6) {
                console.log('Checking confirmation code');
                confirming = true;
                confirmation_code = document.getElementById('confirmation_code');
                
                open_datetime = document.getElementById('open_datetime').value;

                // console.log('confirmation_code',confirmation_code,'.');
                fetchHtml('./email-confirmation.php?confirmation_code='+confirmation_code.value+'&email='+email+'&open_datetime='+open_datetime,function (html) {
                    console.log('Received response for confirmation code check');
                    correct = (html_result=='Y'?true:false);

                    // console.log(confirmation_code.value, code_buffer);
                    if (
                        correct
                        // || true // Test Temporary
                        // confirmation_code.value==code_buffer
                        ) {
                        console.log('Correct');
                        checkCodeConfirmed(false, bypass_alert);
                    } else {
                        console.log('Incorrect', html);
                        confirmation_code.style.backgroundColor = '#FE8569';
                        // confirmation_code.value = confirmation_code.value.slice(0, 5);
                        confirmation_code.focus();
                    }
                    confirming = false;
                });
            }
        }
    </script>








    <script type="text/javascript">
            function checkEmailValidity(input) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(input.value);
            }
        </script>
    <script type="text/javascript">
        code_buffer = null;
        html_result = null;
        async function fetchHtml(url, callback) {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const html = await response.text();
                html_result = html;
                // code_buffer = html;
                console.log(html);
                callback(html)
                return html;
            } catch (error) {
                console.error('Error fetching HTML:', error);
                return null; // Or handle the error differently
            }
        }

        function send_confirm_code(bypass_alert) {
            confirm_email_element = document.getElementById('confirm_email');
            email = confirm_email_element.value;

            // fetchHtml('',function (html) {
                
            // });
            <?php
                $cookie_name = "email"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                     ?>
                    if (email == '<?php echo $_COOKIE[$cookie_name]; ?>') {
                        checkCodeConfirmed(true, bypass_alert);
                        return;
                    }
                    <?php
                } else {
                     ?>
                    if (bypass_alert) {
                        checkCodeConfirmed(true, bypass_alert);
                        return;
                    }
                    <?php
                }
             ?>


            document.getElementById('confirmation_code').value = '';
            if (!checkEmailValidity(confirm_email_element)) {
                alert('請輸入電子郵件地址\n Please enter email address');
                return;
            }
            
            confirmation_code = document.getElementById('confirmation_code').value;
            open_datetime = document.getElementById('open_datetime').value;

            if (confirm('您確定要透過'+email+'接收確認碼嗎? \n Are you sure you want to receive the confirmation code by '+email+'?')) {

                fetchHtml('./email-confirmation.php?confirmation_code='+confirmation_code+'&email='+email+'&open_datetime='+open_datetime,function (html) {});

            }
        }
    </script>





        </td>
    </tr>
    <tr><!-- 
        <th class="hide_for_mobile"><span class="form-label">電話號碼<br>Telephone No.</span></th> -->
        <td>

<script type="text/javascript">
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
</script>

            <input 
            class="form-control" 
            type="text" 
            name="telephone" 
            id="telephone" 
            placeholder="電話號碼 Phone number" 
            autocomplete="on"

            value="<?php
                $cookie_name = "telephone"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                    echo $_COOKIE[$cookie_name];
                }
             ?>" 
            onkeypress="
                return isNumberKey(event);
            " 
            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('telephone').value, 
                        90
                    );
                },10);
            "
             required
            ><br></td>
    </tr>






<?php 
if ($reserve_type != 'pickleball') {
?>
    <tr>
        <td colspan="2">

<div class="section-header">💰 價錢及優惠 Pricing &amp; Discount</div>
<div style="margin: -8px 0 12px 0;">
<small>
<a class="price-link" target="_blank" href="./price_display.php">📋 請按此處參考價錢表 Please click here for price table</a>
</small>
</div>




        </td>
    </tr>
<?php 
}
 ?>

    <tr
    
<?php 
if ($reserve_type == 'pickleball') {
?>
hidden
<?php 
}
?>
    >
        <td colspan="2" style="text-align: center;">
            <hr>
            <h4>價錢選項 Pricing option</h4> 
            <hr>
            <br>
            <table>
                <tr>
                    
                    <td>
                        
                        <label class="container" style="text-align: center;">
                            <input type="radio" name="discount" id="hourly" value="H">
                            <span id="discount_notice_span_hourly" class="checkmark discount_radio span_checkbox discount_checkbox higher" >

                            </span>
                        </label>
                    </td>
                    <td>
                        <label class="container" style="text-align: center;">
                            <input type="radio" class="discount_radio" name="discount" id="student" value="S">
                            <span id="discount_notice_span_student" class="checkmark discount_radio span_checkbox discount_checkbox higher" >

                            </span>
                        </label>
                    </td>
<!--                 </tr>
                <tr> -->
                    <td>
                        <label class="container" style="text-align: center;">
                            <input type="radio" name="discount" id="disabled" value="D">
                            <span id="discount_notice_span_disabled" class="checkmark discount_radio span_checkbox discount_checkbox higher" >

                            </span>
                        </label>
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">

                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                    </td>
<!--                 </tr>
                <tr> -->
                </tr>


            </table>
            <table>

                <tr style="white-space: nowrap;">
                    <td>
                        <input type="text" style="
                                background-color: transparent;
                                border-color: transparent;
                                color: transparent;
                                box-shadow: none;
" disabled><br>
                        <div style="color: transparent;">
                            <br>
                            No need to input anything<br>
                        <div style="color: transparent;">_____________________________________</div>
                        </div>
                    </td>
                    <td style="color: red;text-align: center;">
                        <input 
                        type="text" 
                        id="school_name" 
                        name="school_name" 
                        placeholder="學校名稱 School Name"

                        value="<?php
                            $cookie_name = "school_name"."_rivergolf";
                            if(isset($_COOKIE[$cookie_name])) {
                                echo $_COOKIE[$cookie_name];
                            }
                         ?>" 

            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('school_name').value, 
                        90
                    );
                },10);
            "

                        ><br>
                        <small>
                        如果您是學生，請輸入學校名稱。<br>
                        If you are student, please enter school name.<br>
                        </small>
                        <div style="color: transparent;">_____________________________________</div>

                    </td>
                    <td style="color: purple;text-align: center;">
                        <input 
                        type="text" 
                        id="disabled_id" 
                        name="disabled_id" 
                        placeholder="傷健人士號碼 Disabilities Identification"

                        value="<?php
                            $cookie_name = "disabled_id"."_rivergolf";
                            if(isset($_COOKIE[$cookie_name])) {
                                echo $_COOKIE[$cookie_name];
                            }
                         ?>" 


            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('disabled_id').value, 
                        90
                    );
                },10);
            "


                        ><br>
                        <small>
                        如果您是傷健人士，請輸入傷健人士號碼。<br>
                        If you are disabled, please enter your disabilities identification.<br>
                        </small>
                        <div style="color: transparent;">_____________________________________</div>
                    </td>
                </tr>
            </table>

            <script type="text/javascript">
                
                function checked_discount() {

                    var span_id_root = 'discount_notice_span_';
                    var id;

                    id = 'hourly';
                    span_id = span_id_root+id;
                    if (document.getElementById(id).checked) {
                        document.getElementById(span_id).innerHTML 
= '<div style="color: yellow;">已選正價<br>Regular Price<br>Selected</div>';
                    } else {
document.getElementById(span_id).innerHTML = '正價<br>Regular Price';
                    }

                    id = 'student';
                    span_id = span_id_root+id;
                    if (document.getElementById(id).checked) {
                        document.getElementById(span_id).innerHTML 
= '<div style="color: yellow;">已選 學生優惠<br>Student Price<br>Selected</div>';
                    } else {
document.getElementById(span_id).innerHTML = '學生優惠<br>Student Price';
                    }


                    id = 'disabled';
                    span_id = span_id_root+id;
                    if (document.getElementById(id).checked) {
                        document.getElementById(span_id).innerHTML 
= '<div style="color: yellow;">已選 傷健人士優惠<br>Disabled Price<br>Selected</div>';
                    } else {
document.getElementById(span_id).innerHTML = '傷健人士優惠<br>Disabled Price';
                    }

                }
                document.getElementById('hourly').checked = true;

            </script>

        </td>
    </tr>
    <tr>
        <td  id="with_vehicle_span_space" style="text-align: center;" colspan="2">
<!--             <hr>
            <h4>其他選項 Other option</h4>
            <hr> -->
            <br>

            <table style="width: 100%;">
                <tr>
                    <td>

<label class="container" style="text-align: center;">
    <input type="checkbox" name="vehicle[]" id="with_vehicle" value="Y">
    <span id="with_vehicle_span" class="span_checkbox checkmark widen_checkbox higher" 
    style="
    color: black;
    border-style: solid;
    border-radius:  20px;
    text-align: center;
    " onchange="checked_with_vehicle()" onclick="checked_with_vehicle()">
    </span>
</label>

    <br class="hide_for_mobile">
    <br class="hide_for_mobile">

    <br class="hide_for_desktop">
    <br class="hide_for_desktop">


                    </td>
                    <td>


<script type="text/javascript">
    
    function check_sand_bay() {
        console.log('check_sand_bay');
        var checkboxes = document.getElementsByClassName("position_checkbox");
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false; // Set to true if you want to check them all
        }
    }

</script>
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">

<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">


                    </td>
                </tr>
            </table>

<style type="text/css">

#discount_notice_span_hourly, #discount_notice_span_student, #discount_notice_span_disabled {
    height: 4em;
    font-size: 1.5em;
}


#sand_bay_option_span, #with_vehicle_span {
    height: 5em;
    font-size: 1.5em;
}

@media only screen and (max-width: 1300px) {
    #sand_bay_option_span, #with_vehicle_span {
        height: 5em;
        font-size: 2.3em;
        font-style: bold;
    }
    #discount_notice_span_hourly, #discount_notice_span_student, #discount_notice_span_disabled {
        height: 4em;
        font-size: 2.3em;
        font-style: bold;
    }
}
</style>





        </td>
<!--     </tr>
    <tr> -->
        <td style="text-align: center;" colspan="1">
        </td>
        <td></td>
    </tr>
    <tr class="octopus_block">
        <td colspan="2">
            
<div class="section-header">🅿️ 停車場優惠 Parking Offers</div>


        </td>
    </tr>
    <tr class="octopus_block"><!-- 
        <th class="hide_for_mobile">

<script type="text/javascript">
</script>
        </th> -->
<!-- 號碼  number -->
        <td style="vertical-align: text-top;">
            <small>
                <span class="form-label">八達通號碼 Octopus No.</span>
                <?php
                        $cookie_name = "octopus_no"."_rivergolf";
                        if(isset($_COOKIE[$cookie_name])) {
                            echo $_COOKIE[$cookie_name];
                        }
                     ?>
            </small>
            
            <br>
                    *<input type="text" 
                    name="octopus_no" 
                    id="octopus_no" 
                    placeholder="八達通 Octopus"
                    onblur="
                    console.log('confirm_octopus:',confirm_octopus());
                    " 
                    class="octopus-main"
                    onkeypress="return isNumberKey(event)" 
                    required 
                    autocomplete="on"

                    value="<?php
                        $cookie_name = "octopus_no"."_rivergolf";
                        if(isset($_COOKIE[$cookie_name])) {
                            echo $_COOKIE[$cookie_name];
                        }
                     ?>" 
                    onchange="

                        setTimeout(function () {
                            setCookie(
                                '<?php echo $cookie_name; ?>', 
                                document.getElementById('octopus_no').value, 
                                90
                            );
                        },10);
                            console.log('confirm_octopus:',confirm_octopus());
                    " 
                    >

                    <b class="bracket">(*</b>
                    <input 
                    type="text" 
                    name="octopus_no_q" 
                    id="octopus_no_q"
                    onblur="console.log('confirm_octopus:',confirm_octopus())" 
                    class="octopus-q"
                    placeholder="括號內數字 Bracket number" 
                    onkeypress="return isNumberKey(event)" 
                    required 
                    autocomplete="on"

                    value="<?php
                        $cookie_name = "octopus_no_q"."_rivergolf";
                        if(isset($_COOKIE[$cookie_name])) {
                            echo $_COOKIE[$cookie_name];
                        }
                     ?>" 
                    onchange="

                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('octopus_no_q').value, 
                        90
                    );
                },10);

                    console.log('confirm_octopus:',confirm_octopus());

                    "  

                    ><b class="bracket">)</b>

        </td>
    </tr>
    <tr class="octopus_block" id="octopus_block"><!-- 
        <th class="hide_for_mobile">
        </th> -->
        <td id="octopus_no_cf2">
            <small>
                <span class="form-label">八達通號碼 重複確認 Octopus No. repeat confirmation</span>
            </small>
            <br>
            <!-- 重複確認  number repeat confirmation -->
            <b style="vertical-align: text-top;">
                *<input type="text" 
                name="octopus_no_cf" id="octopus_no_cf" 
                placeholder="八達通 Octopus"
                style="width:50%;" 
                onblur="console.log('confirm_octopus:',confirm_octopus())" 
                onchange="console.log('confirm_octopus:',confirm_octopus())" 
                onkeypress="return isNumberKey(event)" 
                required 
                autocomplete="on"

                value="<?php
                    $cookie_name = "octopus_no_cf"."_rivergolf";
                    if(isset($_COOKIE[$cookie_name])) {
                        echo $_COOKIE[$cookie_name];
                    }
                 ?>" 
            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('octopus_no_cf').value, 
                        90
                    );
                },10);
            "
                >

                <b class="bracket">(*</b>

                <input type="text" name="octopus_no_q_cf" id="octopus_no_q_cf" 
                onblur="console.log('confirm_octopus:',confirm_octopus())" 
                onchange="console.log('confirm_octopus:',confirm_octopus())" 
                onkeypress="return isNumberKey(event)" 
                style="width:20%" 
                placeholder="括號內數字 Bracket number" 
                required 
                autocomplete="on"

                value="<?php
                    $cookie_name = "octopus_no_q_cf"."_rivergolf";
                    if(isset($_COOKIE[$cookie_name])) {
                        echo $_COOKIE[$cookie_name];
                    }
                 ?>" 
            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('octopus_no_q_cf').value, 
                        90
                    );
                },10);
            "
                ><b class="bracket">)</b>

            </b>


        <script type="text/javascript">
            function confirm_octopus() {

                const checkbox = document.getElementById('octopus_block');
                // console.log('checkbox.checked:',checkbox.style.display,(checkbox.style.display=='none'));
                if ((checkbox.style.display=='none')) {
                    return true;
                }

                // Get the password inputs
                var octopus_no = document.getElementById('octopus_no');
                var octopus_no_cf = document.getElementById('octopus_no_cf');


                var octopus_no_q = document.getElementById('octopus_no_q');
                var octopus_no_cf_q = document.getElementById('octopus_no_q_cf');

                octopus_no_cf.style.backgroundColor = "white";

                if (octopus_no.value.trim() != "" && octopus_no.value === octopus_no_cf.value) {
                    octopus_no_cf.style.backgroundColor = "rgb(200,255,200)";
                } else {
                    octopus_no_cf.style.backgroundColor = "rgb(255,200,200)";
                }

                if (octopus_no_q.value.trim() != "" && octopus_no_q.value === octopus_no_cf_q.value) {
                    octopus_no_cf_q.style.backgroundColor = "rgb(200,255,200)";
                } else {
                    octopus_no_cf_q.style.backgroundColor = "rgb(255,200,200)";
                }

                if (octopus_no.value.trim() === "") {
                    return false;
                }
                if (octopus_no_q.value.trim() === "") {
                    return false;
                }


                if (octopus_no_cf.value.trim() === "") {
                    return false;
                }
                if (octopus_no_cf_q.value.trim() === "") {
                    return false;
                }

                // Check if passwords match
                if (octopus_no.value === octopus_no_cf.value && octopus_no_q.value === octopus_no_cf_q.value) {
                    // console.log('octopus_no match');
                    return true;
                } else {
                    // console.log('octopus_no do not match');
                    return false;
                }
            }











    </script>
    <script type="text/javascript">
                
    function with_vehecle() {
                
        var octopus_no = document.getElementById('octopus_no');
        var octopus_no_cf = document.getElementById('octopus_no_cf');


        var octopus_no_q = document.getElementById('octopus_no_q');
        var octopus_no_cf_q = document.getElementById('octopus_no_q_cf');

        const elementsToHide = document.getElementsByClassName('octopus_block');


        const checkbox = document.getElementById('with_vehicle');
        var has_octopus_discount = checkbox.checked;
        has_octopus_discount = <?php echo (!$has_octopus_discount?'false':'has_octopus_discount'); ?>;
        
        if (has_octopus_discount) {
            console.log('access with car');
            octopus_no.required = true;
            octopus_no_cf.required = true;
            octopus_no_q.required = true;
            octopus_no_cf_q.required = true;
            for (let i = 0; i < elementsToHide.length; i++) {
                elementsToHide[i].style.display = '';
            }
        } else {
            console.log('access without car');
            octopus_no.required = false;
            octopus_no_cf.required = false;
            octopus_no_q.required = false;
            octopus_no_cf_q.required = false;
            for (let i = 0; i < elementsToHide.length; i++) {
                elementsToHide[i].style.display = 'none';
            }
            octopus_no.value = '';
            octopus_no_cf.value = '';
            octopus_no_q.value = '';
            octopus_no_cf_q.value = '';
        }

    }

    function checked_with_vehicle() {
        var has_octopus_discount = document.getElementById('with_vehicle').checked;
        has_octopus_discount = <?php echo (!$has_octopus_discount?'false':'has_octopus_discount'); ?>;
        // todo
        // has_octopus_discount = false;
        if (has_octopus_discount) {
            document.getElementById('with_vehicle_span').innerHTML = '<div style="color: yellow;">已選 停車優惠<br>Parking offer selected</div>';
            document.getElementById('with_vehicle_span').style.backgroundColor = '#2196F3';
        } else {
            <?php if ($has_octopus_discount) { ?>
                document.getElementById('with_vehicle_span').innerHTML = '<div>停車優惠<br>Parking offer</div>';
            <?php } else { ?>
                // document.getElementById('with_vehicle_span').innerHTML = '<div>不提供停車優惠<br>No parking discount</div>';
                document.getElementById('with_vehicle_span').innerHTML = '<div style="color: red;">網站暫不提供泊車優惠，請到接待處換領 <br> Web-order CANNOT provide parking discount. <br> Please redeem at reception.</div>';
                document.getElementById('with_vehicle_span').style.display = 'none';
                document.getElementById('with_vehicle_span_space').style.display = 'none';
            <?php } ?>
                document.getElementById('with_vehicle_span').style.backgroundColor = 'white';
        }
        with_vehecle();
    }
    document.getElementById('with_vehicle').checked = true;
    checked_with_vehicle();

</script>




        </td>
    </tr>
    <tr>
        <td colspan="2">
            

<div class="section-header">📅 預訂日期和時間 Booking Date &amp; Time</div>


        </td>
    </tr>
















<?php

// Function to generate date string for the next 6 days (including today)
function getNextWeekDates() {
  $dates = [];
    // $dates[] = date('Y-m-d', strtotime("-1 days"));
  for ($i = 0; $i < 8; $i++) {
    $cursor = date('Y-m-d', strtotime("+$i days"));
    if ($cursor >= '2024-08-26') {
        $dates[] = $cursor;
    }
    
  }
  return $dates;
}

// Get next week dates
$dates = getNextWeekDates();

?>
    <tr><!-- 
        <th class="hide_for_mobile">
        </th> -->
        <td>
            <small> 預訂日期 Reservation date</small>
            <br>
*
<select name="booking_date" id="booking_date" onchange="setTimeout(function () {
check_datetime();show_and_hide_hours_2();show_and_hide_hours();
},10);" required>
     <optgroup>
    <?php foreach ($dates as $date): ?>
        <option value="<?php echo $date; ?>"><?php 
        $dateString = $date; // Example date string in "YYYY-MM-dd" format
        $dateObject = new DateTime($dateString);
        $dayOfWeekName = $dateObject->format('l'); // Returns the full textual representation of the day (e.g., "Tuesday")
        // echo "Day of the week (name): $dayOfWeekName"; // Output: Tuesday

        echo "$date ($dayOfWeekName)";
         ?></option>
    <?php endforeach; ?>
</optgroup>
</select>

        </td>
    </tr>

<?php 

$iternation = 1;
if ($half_hour_cluster) {
    $iternation = 0.5;
}
    
 ?>
    <tr>
<!--         <th class="hide_for_mobile">
</th> -->
<!-- 
Use javascript to create listener of the select-option "begin_hour" and set the "end_hour" have more than one than "begin_hour" when "begin_hour" have any change 
 -->

        <td>
<small>
開始時間 (小時) Starting time (Hour)    
</small>

<br>
            *
<style type="text/css">
    .half_hour_option {
        text-align: left;
    }
</style>

<?php 
function generate_hour_option($is_begin_hour,$half_hour, $is_monday, $must_half_hour)
{
    $last_hour_option = 21;
    if (!$is_begin_hour) {
        $last_hour_option = 22;
    }

    $prefix = 'b';
    if ($is_begin_hour) {
        $prefix = 'e';
    }
     ?>
    <?php for ($hour = 8; $hour <= $last_hour_option; $hour++): ?>
        <?php 
        $not_monday = false;
        $monday = false;
        if ($hour >= 8 && $hour <= 22) {
            $not_monday = true;
        }
        if ($hour >= 13 && $hour <= 22) {
            $monday = true;
        }
         ?>

        <?php if (
            ($is_begin_hour||(!$is_begin_hour && $hour != 8)) 
            && (($is_monday && $monday)||($not_monday && !$is_monday))
        ) { ?>
            <option 
                class="<?php echo ($not_monday?'not_monday ':''); echo ($monday?'monday ':''); ?>hour_opt <?php echo $prefix; ?>_hour_<?php echo $hour; ?>" 
                value="<?php echo $hour; ?>"
                ><?php echo $hour; ?>:00</option>
        <?php } ?>

        <?php if (
            ($must_half_hour && $hour != 22) || (
                $half_hour 
                && (($is_monday && $monday)||($not_monday && !$is_monday)) 
                && ($is_begin_hour||(!$is_begin_hour && $hour != 22))
            )
        ) { ?>
            <option 
                class="<?php echo ($not_monday?'not_monday ':''); echo ($monday?'monday ':''); ?>hour_opt <?php echo $prefix; ?>_hour_<?php echo $hour; ?> <?php echo $prefix; ?>_half_hour_<?php echo $hour; ?> half_hour_option" 
                value="<?php echo ($hour+0.5) ?>"
            ><?php echo $hour; ?>:30</option>
        <?php } ?>

  <?php endfor; ?>
<?php   
}
 ?>
<?php 

$begin_hour_placeholder = '<option value="" disabled selected>開始時間 Begin time</option>';
$and_hour_placeholder = '<option value="" disabled selected>完結時間 End time</option>';
// 請選擇 Please select a s
// $begin_hour_placeholder = "";
// $and_hour_placeholder = "";


 ?>

<div style="display: none;" id="BeginWholeHourMon">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, false , true, $must_half_hour); ?>
</div>

<div style="display: none;" id="BeginHalfHourMon">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, true, true, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndWholeHourMon">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, false, true, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndHalfHourMon">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, true, true, $must_half_hour); ?>
</div>


<div style="display: none;" id="BeginWholeHour">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, false, false, $must_half_hour); ?>
</div>

<div style="display: none;" id="BeginHalfHour">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, true, false, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndWholeHour">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, false, false, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndHalfHour">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, true, false, $must_half_hour); ?>
</div>





<select name="begin_hour" id="begin_hour" onchange="check_datetime()" required> 
<optgroup id="begin_hour_group">
    <?php // generate_hour_option(true, false); ?>
</optgroup>
</select>


        </td>
    </tr>




    <tr><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>
            <small>結束時間 (小時) End time (Hours)</small>

<br>
            *
<select name="end_hour" id="end_hour" onchange="check_datetime()" required> <optgroup id="end_hour_group">
    <?php // generate_hour_option(false, false); ?>
    </optgroup>
</select>


<script type="text/javascript">

function show_class(class_name) {
    // console.log('show_class: '+class_name);
    const elements = document.getElementsByClassName(class_name);
    for (const element of elements) {
        element.style.display = "";
    }
}
function hide_class(class_name) {
    // console.log('hide_class: '+class_name);
    const elements = document.getElementsByClassName(class_name);
    for (const element of elements) {
        element.style.display = "none";
    }
}

// hide_class('selection_area');
function hidePastHalfHourBlocksForEach(dateStr, elements, hourStr, minuteStr, now) {
    elements.forEach(el => {
        if (!dateStr) return;

        const elementTime = new Date(`${dateStr}T${hourStr}:${minuteStr}:00`);

        if (elementTime < now) {
            el.style.display = "none";
            el.disabled = true;
            el.hidden = true;
            console.log('Hiding past time block: ', el);
        }
    });
}
// hide_class('selection_area');
function showHourBlocksForEach(elements) {
    elements.forEach(el => {
        el.style.display = "";
        el.disabled = false;
        el.hidden = false;
    });
}

function hidePastHalfHourBlocks(dateStr) {
    console.log('BEGIN hidePastHalfHourBlocks: ',dateStr);
    var now = new Date("<?= date('Y-m-d H:i:s') ?>");

    console.log('NOW: ', now);

    // Loop 48 half-hour slots
    for (let i = 0; i < 48; i++) {
        const h = Math.floor(i / 2);
        const m = (i % 2 === 0) ? "00" : "30";

        const hourStr = String(h).padStart(2, "0");
        const minuteStr = m;

        // Map to your existing class names
        const className = (minuteStr === "00")
            ? `e_hour_${hourStr}`
            : `e_half_hour_${hourStr}`;
        // console.log('Checking class: ', className);
        const elements = document.querySelectorAll(`.${className}`);
        showHourBlocksForEach(elements);
        hidePastHalfHourBlocksForEach(dateStr, elements, hourStr, minuteStr, now);
    }
    console.log('END hidePastHalfHourBlocks: ',dateStr);
}


function show_and_hide_hours_2() {
    var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;
    var booking_date = document.getElementById('booking_date');
    var begin_hour = document.getElementById('begin_hour');
    var end_hour = document.getElementById('end_hour');
    // console.log('Show: begin_hour ',begin_hour.value);
    // console.log('Show: end_hour ',end_hour.value);

    const dateString = booking_date.value; // Example date string in "YYYY-MM-dd" format
    const dateParts = dateString.split("-"); // Split the string into year, month, and day parts
    const year = parseInt(dateParts[0]);
    const month = parseInt(dateParts[1]) - 1; // Months are zero-based (0 = January, 1 = February, etc.)
    const day = parseInt(dateParts[2]);

    const dateObject = new Date(year, month, day);
    const dayOfWeek = dateObject.getDay(); // Returns a number (0 for Sunday, 1 for Monday, etc.)
    const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var weekdayName = weekdays[dayOfWeek];


    var recent_holiday_list = <?php 
        echo json_encode($recent_holiday_list);
     ?>;
    if (recent_holiday_list.indexOf(booking_date.value) !== -1) {
        console.log('You\'re choosing holiday');
        weekdayName = 'Holiday';
    }
    var is_monday = (weekdayName=='Monday');
    // console.log('is_monday:',is_monday,'sand_bay_option_checked:',sand_bay_option_checked);


    begin_hour_group = document.getElementById('begin_hour_group');
    end_hour_group = document.getElementById('end_hour_group');

    console.log('sand_bay_option_checked',sand_bay_option_checked);
    // console.log('is_monday',is_monday);

    var begin_hour_name = 'Begin'
        +(sand_bay_option_checked?'Half':'Whole')
        +'Hour'
        +(is_monday?'Mon':'');
    var end_hour_name = 'End'
        +(sand_bay_option_checked?'Half':'Whole')
        +'Hour'
        +(is_monday?'Mon':'');
    // console.log('Show: Begin hour ',begin_hour_name);
    // console.log('Show: End hour ',end_hour_name);

    begin_hour_group.innerHTML =
    document.getElementById(begin_hour_name).innerHTML;
    begin_hour.value = '';

    end_hour_group.innerHTML =
    document.getElementById(end_hour_name).innerHTML;
    end_hour.value = '';

}


function setDisplayById(ele_id ,display) {
    var element = document.getElementById(ele_id);
    if (!element) {
        return;
    }
    if (display) {
        element.style.display = '';
    } else {
        element.style.display = 'none';
    }
}


function show_and_hide_hours() {
    var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;

    var booking_date = document.getElementById('booking_date');
    var begin_hour = document.getElementById('begin_hour');
    var end_hour = document.getElementById('end_hour');

    const dateString = booking_date.value; // Example date string in "YYYY-MM-dd" format
    const dateParts = dateString.split("-"); // Split the string into year, month, and day parts
    const year = parseInt(dateParts[0]);
    const month = parseInt(dateParts[1]) - 1; // Months are zero-based (0 = January, 1 = February, etc.)
    const day = parseInt(dateParts[2]);

    const dateObject = new Date(year, month, day);
    const dayOfWeek = dateObject.getDay(); // Returns a number (0 for Sunday, 1 for Monday, etc.)
    const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var weekdayName = weekdays[dayOfWeek];




    const formattedDate = dateObject.toISOString().split('T')[0];
    // console.log('formattedDate:',formattedDate);
    // console.log('booking_date.value:',booking_date.value);
    

    var recent_holiday_list = <?php 
        echo json_encode($recent_holiday_list);
     ?>;
    if (recent_holiday_list.indexOf(booking_date.value) !== -1) {
        console.log('You\'re choosing holiday');
        weekdayName = 'Holiday';
    }

    var is_monday = (weekdayName=='Monday');
    // console.log('is_monday:',is_monday,'sand_bay_option_checked:',sand_bay_option_checked);
    var is_half_hour = ( sand_bay_option_checked || <?php echo ($must_half_hour?"true":"false"); ?> );

    if (is_monday) {
        if (is_half_hour) {
            show_class('half_hour_option');
        }
        hide_class('not_monday');
        show_class('monday');
        // console.log('Show Monday');
        if (!is_half_hour) {
            hide_class('half_hour_option');
        }
    } else {
        if (is_half_hour) {
            show_class('half_hour_option');
        }
        hide_class('monday');
        show_class('not_monday');
        // console.log('Show other than monday');
        if (!is_half_hour) {
            hide_class('half_hour_option');
        }
    }









    if (sand_bay_option_checked) {
        document.getElementById('sand_bay_option_span').innerHTML = '<div style="color: yellow;">已選 沙地球道 <br> 半小時預訂<br> Sand Bay <br> Half Hour Booking Selected</div>';
        document.getElementById('sand_bay_option_span').style.backgroundColor = '#2196F3';


        setDisplayById('selection_VIP', false);
        setDisplayById('selection_sand', true);
        setDisplayById('selection_iron', false);

        setDisplayById('selection_short_wood', false);
        setDisplayById('selection_wood', false);
        
    } else {
        document.getElementById('sand_bay_option_span').innerHTML = '<div>沙地球道 <br> 半小時預訂<br> Sand Bay (Half Hour)'
        +'<br>'
        // +'<small style="color: red;">請雙擊 Please double click</small>'
        +'</div>';
        document.getElementById('sand_bay_option_span').style.backgroundColor = 'white';

        setDisplayById('selection_VIP', true);
        setDisplayById('selection_sand', false);
        setDisplayById('selection_iron', true);
        setDisplayById('selection_short_wood', true);
        setDisplayById('selection_wood', true);
    }

    selection_area();

}

setTimeout(show_and_hide_hours,100);

function getSelectedDateTime_begin() {
    var booking_date = document.getElementById('booking_date');
    var begin_hour = document.getElementById('begin_hour');

    if (begin_hour.options[begin_hour.selectedIndex] == undefined) {
        return undefined;
    }

    var begin_hour_root = Math.floor(begin_hour.value);
    var begin_hour_val = parseFloat(begin_hour.value);
    var is_begin_half = (begin_hour_val > begin_hour_root);
    var begin_hour_time_part = (begin_hour_root+'').padStart(2, '0') + (is_begin_half?":30":":00");
    var beginTime_str = booking_date.value+'T'+begin_hour_time_part;
    const beginTime = new Date(beginTime_str);

    if (begin_hour.value == '' || isNaN(beginTime)) {
        // console.log('beginTime_str',beginTime_str);
        return undefined;
    }

    return beginTime;
}

function getSelectedDateTime_end() {
    var booking_date = document.getElementById('booking_date');
    var end_hour = document.getElementById('end_hour');

    if (end_hour.options[begin_hour.selectedIndex] == undefined) {
        return undefined;
    }

    var end_hour_root = Math.floor(end_hour.value);
    var end_hour_val = parseFloat(end_hour.value);
    var is_end_half = (end_hour_val > end_hour_root);
    var end_hour_time_part = (end_hour_root+'').padStart(2, '0') + (is_end_half?":30":":00");
    var endTime_str = booking_date.value+'T'+end_hour_time_part;
    const endTime = new Date(endTime_str);

    if (end_hour.value == '' || isNaN(endTime)) {
        // console.log('endTime_str',endTime_str);
        return undefined;
    }

    return endTime;
}

function clearHours() {
    var begin_hour = document.getElementById('begin_hour');
    var end_hour = document.getElementById('end_hour');
    begin_hour.value = '';
    end_hour.value = '';
}

hide_class('selection_area');
function checkFutureDateTime() {
    checkFutureDateTime(false);

}

function isLessThanOneHourApart(dateA, dateB) {
  const diffMs = Math.abs(dateA - dateB); // Difference in milliseconds
  const oneHourMs = 60 * 60 * 1000;       // One hour in milliseconds
  return diffMs < oneHourMs;
}

function getHKDatetime() {
    const hongKongTime = new Intl.DateTimeFormat('en-GB', {
    timeZone: 'Asia/Hong_Kong',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    }).format(new Date());
    console.log('hongKongTime:',hongKongTime); // e.g., "28/08/2025, 17:11:00"
    // Split date and time
    const [datePart, timePart] = hongKongTime.split(', ');
    const [day, month, year] = datePart.split('/');
    const [hour, minute, second] = timePart.split(':');
    // Create Date object in Hong Kong time (UTC+8)
    const hongKongDate = new Date(Date.UTC(year, month - 1, day, hour - 8, minute, second));
    console.log(hongKongDate); // Native Date object in your local time zone
    return hongKongDate;
}
</script>

<script>
// Example usage:
const now = new Date();
const fortyFiveMinutesLater = new Date(now.getTime() + 45 * 60 * 1000);

console.log(isLessThanOneHourApart(now, fortyFiveMinutesLater)); // true

function checkFutureDateTime(justCheck) {
    var is_management = <?php echo ($is_management?'true':'false'); ?>;
    
    var current_timestamp = '<?php echo $current_timestamp; ?>';
    var futureOneHour_timestamp = '<?php echo $futureOneHour_timestamp; ?>';
    // console.log('current_timestamp:', current_timestamp);
    // console.log('futureOneHour_timestamp:', futureOneHour_timestamp);
    var currentDate = new Date(current_timestamp);
    var futureOneHourDate = new Date(futureOneHour_timestamp);

    var now;
    if (is_management || <?php echo ($must_half_hour?'true':'false')  ?> ) {
        // console.log('currentDate:', currentDate);
        now = currentDate;
    } else {
        // console.log('futureOneHourDate:', futureOneHourDate);
        now = futureOneHourDate;
    }

    const beginTime = getSelectedDateTime_begin();
    const endTime = getSelectedDateTime_end();


    if ( beginTime==undefined || endTime==undefined ) {
        if (justCheck) {
            return false;
        }
        return;
    }
    
    var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;
    isLessThanOneHour = isLessThanOneHourApart(beginTime, endTime);
    if ( 
        beginTime >= now && endTime > now <?php 
        if ($must_half_hour) {
            // half hour system cannot play less than an hour 
            echo "&& (!isLessThanOneHour || sand_bay_option_checked)"; 
        }
         ?>
    ) {
        if (justCheck) {
            return true;
        }
    } else {
        if (justCheck) {
            return false;
        } else {
            if (isLessThanOneHour) {
                console.log('is less than an hour:');
                console.log('beginTime:'+beginTime);
                console.log('endTime:'+endTime);
                alert('請選擇一小時或更長。\n Please select one hour or more.');
            } else {
                alert('您選擇了不當的時間\nYou selected an inappropriate time.');
            }
            console.log('( = '+beginTime+' - '+endTime+' ) ');
            console.log('now:',now);
            console.log('beginTime:',beginTime);
            console.log('endTime:',endTime);
            clearHours();
        }
    }

}

function isValidDateTime() {
    return checkFutureDateTime(true);
}

function getTimeInTimezone() {
    const now = new Date();
    // now.setHours(now.getHours() + 1);
    return now;
}

function selection_area() {

    setTimeout(function () {
        var begin_hour = document.getElementById('begin_hour');
        var end_hour = document.getElementById('end_hour');
        // console.log('begin_hour.value: '+begin_hour.value);
        // console.log('end_hour.value: '+end_hour.value);
        if ( (begin_hour.value).length>0 && (end_hour.value).length>0 ) {
            show_class('selection_area');
            checkBookingRecord();
        } else {
            hide_class('selection_area');
        }
    }, 100);
    
}
selection_area();
function check_datetime() {
    selection_area();
    // show_and_hide_hours();
    checkFutureDateTime();
}

clearHours();





















            function updateInputState() {
                // console.log('updateInputState ');
                // show_and_hide_hours();

                const emailInput = document.getElementById('confirm_email'); // Replace with your input ID
                const confirmInput = document.getElementById('confirmation_code'); // Replace with your input ID
                if (checkEmailValidity(emailInput)) {
                    // confirmInput.readOnly = false;
                    confirmInput.disabled = false;
                    // confirmInput.style.backgroundColor = 'white';
                } else {
                    // confirmInput.readOnly = true;   
                    confirmInput.disabled = true;
                    confirmInput.style.backgroundColor = 'orange';
                }

                // check_sand_bay();
            }
            const checkInterval = setInterval(() => updateInputState(), 300);




</script>
    


        </td>
    </tr>













<tr
<?php 
if ($reserve_type == 'pickleball') {
?>
hidden
<?php 
}
 ?>
>
    <td>

<label class="container" style="text-align: center;">
    <input type="checkbox" name="sand_bay_option" id="sand_bay_option" onclick="
    check_sand_bay();
    show_and_hide_hours_2();
    show_and_hide_hours();
">
    <span id="sand_bay_option_span" class="span_checkbox checkmark widen_checkbox higher" 
    style="
        color: black;
        border-style: solid;
        border-radius:  20px;
        text-align: center;
    " onchange="setTimeout(function () {
        check_sand_bay();show_and_hide_hours();show_and_hide_hours_2();
    },10);">
    </span>

    
</label>

                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">

<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">

    </td>
</tr>
<script>
show_and_hide_hours_2();
</script>






















    <tr class="selection_area">
        <td colspan="2">


<?php 
if ($reserve_type == 'pickleball') {
?>
<div class="section-header">🏓 請選擇匹克球 預訂位置<br><small style="color:rgba(255,255,255,0.85)">Please select the courts you would like to reserve</small></div>
<?php 
} else {
    
?>
<div class="section-header">⛳ 請選擇高爾夫打球 預訂位置<br><small style="color:rgba(255,255,255,0.85)">Please select the courts you would like to reserve</small></div>
<?php 
}
 ?>

        </td>
    </tr>
    <tr>
        <td colspan="2">
            


























<table class="legend-table" style="width: auto;">
    <tr><td colspan="2">
        

請先選擇您的<?php echo ($reserve_type == 'pickleball'?"球場":"球道"); ?>，然後按「提交」按鈕。<br>
Please select your court(s) and then click the Submit button. <br>
未選擇<?php echo ($reserve_type == 'pickleball'?"球場":"球道"); ?>時，提交按鈕將無法使用。<br>
When no court is selected, the submit button will not be visible.<br>
<hr>
    </td></tr>
    <tr><td class="legend-available">🟢 綠色 — 可預約<br>Green — Available</td></tr>
    <tr><td class="legend-booked">🔴 紅色 — 已預訂<br>Red — Reserved</td></tr>
</table>

<hr>
<!-- 
<p style="color: red;">
            請不要選擇超過40<?php echo ($reserve_type == 'pickleball'?"個球場":"條球道"); ?>，否則將無法預訂<br>
            Please do not select more than 40 fairways or your reservation will not be available
</p> -->

<script>



complexArray = null;
function update_booking_record() {
                // console.log('update_booking_record ');
    fetch('./booking-status-json-variable.php?api&api_1') // Replace with your API endpoint
    .then(response => response.json()) // Parse the data as JSON
    .then(data => {
        if (complexArray == null) {
           // console.log(data); 
        }
        complexArray = data;
        // console.log(data); // Log the data
        // 
    })
    .catch(error => {
        console.error('Error:', error); // Log any errors
    });

}
update_booking_record();
const intervalId1 = setInterval(update_booking_record, 5*60*1000);













    document.getElementById("begin_hour").addEventListener("change", function() {
        const selectedBeginHour = parseFloat(this.value);

        var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;
        document.getElementById("end_hour").value = selectedBeginHour + (sand_bay_option_checked?0.5:1);
    });












</script>













<!-- 

Use php to generate html, css to create a complex group of check box in a selection table, the first row shows 1 to 60 position numbers; the second row is a series of date from today to the following six dates (Total 7 days, show the date with format YYYY-MM-DD in the table cell); the third row is a series of hours from 09:00 am to 10:00 pm (Totally 13 hours - one table cell for each hour) with two hours text (the 24-hour formatted time, and the 12-hour formatted time), each hour is assigned an check box for html form.

 -->
    <style type="text/css">
        .c {
            text-align: center;
            border: 1px solid #ddd;
        }
    </style>
    <div class="booking-form">


<style type="text/css">
.navbar {
  background-color: #333; /* Black background color */
  position: fixed; /* Make it stick/fixed */
  top: 0; /* Stay on top */
  width: 100%; /* Full width */
  transition-duration: 1s;
}

/* Style the navbar links */
.navbar a {
  float: left;
  display: block;
  color: white;
  text-align: center;
  padding: 15px;
  text-decoration: none;
}

.navbar a:hover {
  background-color: #ddd;
  color: black;
}
</style>

<style type="text/css">
input[type=checkbox]
{
  margin:  30 px;
  
  padding: 20px;
  width: 100%;
}
td, th {
    vertical-align: top;
}
</style>
<?php 

require_once './position_list.php';

// $position_list_ = array(
//     //Sand
//     array(
//         1,2
//         // ,3
//     ),
//     // VIP
//     array(
//         "VIP"
//     ),
//     // Iron
//     array(
        
//         5,6,7,8,9,10,11,12,13,
//         15,16,
//         17,18,19,20,21,22,23,
//         25,26,
//         27,28,29,30,31,32,33,
//         35,
//         36,37,38,39,
//     ),
//     // Wood
//     array(
//         50,51,52,53,

//         55,56,57,
//         59,60,61,62,63,
//         65,66,67,68,69,70,71,72,73,

//         75,76,77,78,79,80,81,82,83
//         // ,84
//         ,85
//     ),
// );

?>



<style>
/* Customize the label (the container) */
.container {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    width: 90%;
}

/* Hide the browser's default checkbox */
.container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

/* Create a custom checkbox */
.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    background-color: #eee;
/*    color: white;*/
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
    background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
    background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
    display: block;
}

.container .checkmark2:after {
    vertical-align: text-top;
    font-size: 30px;
}

.container .checkmark2:after {
    content: "已選取 Checked";
}
/* Style the checkmark/indicator */
.container .checkmark:after {
    vertical-align: text-top;
    font-size: 30px;
/*    left: 9px;
    top: 5px;
    width: 5px;
    border: solid white;
    border-width: 0 3px 3px 0;*/
/*    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);*/
}
</style>



<style type="text/css">
    .widen_checkbox {
        width: 100%;
    }
</style>
<?php

$spotTableVertical = function ($arr,$title="") use ($reserve_type)
{

    $ele_id = substr(md5($title), 0, 4);
?>




<script>
function toggleDiv<?php echo $ele_id; ?>() {
    var x = document.getElementById("myDiv<?php echo $ele_id; ?>");
    console.log(x.style.display);
    if (x.style.display === "none") {
        var elements = document.getElementsByClassName("expend_area");
        for(var i = 0; i < elements.length; i++){
            elements[i].style.display = "none";
        }
        x.style.display = "";
        x.focus();
    } else {
        var elements = document.getElementsByClassName("expend_area");
        for(var i = 0; i < elements.length; i++){
            elements[i].style.display = "none";
        }
        x.style.display = "none";
    }
}
</script>









<div onclick="toggleDiv<?php echo $ele_id; ?>()" class="expend" style="background-color: white;">
<hr>
    <h3><b><?php 
        echo $title; 
    
    ?></b></h3><br>
    <small>展開/隱藏 Expand/Hide</small><br>
<hr>
</div>

<table style="width: 100%;display: none;"  class="expend_area" id="myDiv<?php echo $ele_id; ?>">
    <tbody>
        <?php 
        // $min = 50;
        // $max = 69;
        // $max = 59;
        for ($i=0; $i < sizeof($arr); $i++) { 
            $p=$arr[$i];
            if (filter_var($p, FILTER_VALIDATE_INT) !== false) {
                $integer = (int) $p;
                // Check if the integer is within the specified range
                // if ($integer >= $min && $integer <= $max) {
                //     continue;
                // }
            }
         ?>
        <tr class="position position_<?php echo "$p"; ?>">
            <td class="c higher position position_<?php echo "$p"; ?>" ><?php 
            // echo $p;
    if ($reserve_type == 'pickleball') {
        if (filter_var($p, FILTER_VALIDATE_INT) !== false) {
            $num = (int)$p;
            $result = $num - 100 + 1;
            ?>
            <small>
                球場編號 Court No. <?php echo $result; ?>
            </small>
            <?php
        }
    } else {
        echo $p; 
    }
            ?></td>
            <th>



<label class="container">
    <input type="checkbox" class="position_checkbox position_<?php echo "$p"; ?>" id="position_<?php echo "$p"; ?>" name="p_selections[]" value="position_<?php echo "$p"; ?>" onclick="checkBookingRecord()">
    <span class="checkmark checkmark2 widen_checkbox higher"></span>
</label>






</th>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php
};


function spotTableHorizon($arr)
{
 ?>
<table>
    <tbody>
        <tr>
            <?php 
            for ($i=0; $i < sizeof($arr); $i++) { 
                $p=$arr[$i];
             ?>
            <th class="c"><?php echo $p; ?></th>
            <?php } ?>
        </tr>
        <tr>
            <?php 
            for ($i=0; $i < sizeof($arr); $i++) { 
                $p=$arr[$i];
             ?>
            <td class="c">
            <input type="checkbox" id="position_<?php echo "$p"; ?>" class="position" name="p_selections[]" value="position_<?php echo "$p"; ?>">
            </td>
            <?php } ?>
        </tr>
    </tbody>
</table>
<?php
}

 ?>

<table class="selection_area">
    <tbody>
        <?php
$bays_tr = function($id, $position_submenu,  $title) use ($reserve_type, $spotTableVertical) {
    ?>
        <tr id="<?php echo $id; ?>">
            <td>
<?php 
$spotTableVertical($position_submenu, title: $title);
 ?>
            </td>
        </tr>
    <?php
};
        ?>
<?php 
if (!$position_list_) {
    die();
}
$golf_bays_trs = function() use ($bays_tr, $position_list_) {
$bays_tr("selection_VIP", $position_list_[1], '貴賓室球道 VIP Room Bays');
$bays_tr("selection_sand", $position_list_[0], '沙地 球道 Sand court');
$bays_tr("selection_iron", $position_list_[2], '鐵桿球道 <br> Irons Only Bays');
$bays_tr("selection_short_wood", $position_list_[3], '鐵桿及球道木桿球道 <br> Irons to Fairway Woods Bays');
$bays_tr("selection_wood", $position_list_[4], '所有球桿球道 <br> All Clubs Bays');
};
?>












<?php 
if ($reserve_type == 'pickleball') {
    $bays_tr("selection_pickle_ball", $position_list_[5], '匹克球 <br> Pickleball');
} else {
    $golf_bays_trs();
}
 ?>

<script>
const expanded = document.querySelectorAll('.expend');
if (expanded.length === 1) {
    expanded[0].click();
}
</script>














    </tbody>
</table>








<hr>
<script type="text/javascript">
    
show_and_hide_hours();
</script>
</div>





























        </td>
    </tr>
                <tr>
                    <td colspan="2">
                        <br>
                        <b style="font-size: 1.5em;">備註 Remark</b>
                        <br>

<!-- 
                        如果您選擇正價，不需要備註。<br>
                        If you choose regular price, remark may not needed.<br>
                        <br>

                        <div style="color: red;">
                            如果您是學生，請輸入學校名稱。<br>
                            If you are student, please enter school name.<br>
                        <br>
                        </div>

                        <div style="color: purple;">
                        如果您是傷健人士，請輸入殘疾人士號碼。<br>
                        If you are disabled, please enter your disabilities identification.<br>
                        </div>
                        <br>

                        如果您無法提供任何相關信息，工作人員可能會在您抵達時詢問您。<br>
                        If you cannot provide any related information, the staff may ask you while you arrived.<br>
                        <br>
                        <br>
 -->
                        <br>
                        <div class="remarks-label">✏️ 備註 Remark</div>
                        <textarea name="remark" class="remark-textarea" placeholder="如有特殊要求請註明&#10;Please note any special requests here..."></textarea>
                        <br>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td class="submit-section">
                        

<div
    onmouseover="notice_submitbutton()" 
    onclick="notice_submitbutton()" 
>
    
<input 
    type="submit" 
    class="submit-button" 
    value="✅ 提交 Submit" 
    onmousedown="update_booking_record();checkBookingRecord();"
    disabled
    >

</div>
<script type="text/javascript">
    function notice_submitbutton() {
        setTimeout(function() {
            send_confirm_code(true);
            setTimeout(function() {
                confirmationCodeValid = validateConfirmationCode();
                checkboxesValids = validateCheckboxes();
                dateTimeIsValid = isValidDateTime();
                octopusConfirm = confirm_octopus();
                
                if (checkNameEmpty()) {
                    alert("請輸入您的姓名。 \n Please enter your name.");
                } else if (!checkboxesValids) {
                    alert('請在提交前選擇球道 \n Please select a fairway before submitting ');
                } else if (!dateTimeIsValid) {
                    alert('請輸入目前時間之前的有效日期 \n Please enter a valid date before the current time ');
                } else if (!octopusConfirm) {
                    alert('請在輸入欄和確認輸入欄輸入有效的八達通卡號碼 \n Please enter a valid Octopus card number in the input field and confirmation input field ');
                } else if (!confirmationCodeValid) {
                    alert('請輸入正確的電子郵件確認碼 \n Please enter correct email confirmation code ');
                }
                console.log('checkboxesValids,dateTimeIsValid,octopusConfirm: ',checkboxesValids,dateTimeIsValid,octopusConfirm);
            }, 1);
        }, 1);
    }
</script>
<br>

<div class="submit-notice">
<strong>⚠️ 提交前請注意 / Before submitting:</strong><br>
❶ 確認電子郵件並輸入正確的確認碼 &bull; Confirm email with correct confirmation code<br>
❷ 如駕車入場請輸入八達通號碼並重複確認 &bull; Enter Octopus number with confirmation if driving<br>
❸ 選擇球道 &bull; Select the fairway
</div>

<hr>

</form>






</div>
    </div>

<script type="text/javascript">


    function validateConfirmationCode() {
        return confirmed;
        // const confirmation_code = document.getElementById('confirmation_code'); // Replace with your input ID
        // const confirmation_code_match = (confirmation_code.value.length == 6 && confirmed);
        // // console.log('confirmation_code && confirmation_code_match',confirmation_code, confirmation_code_match)


        // return confirmation_code_match;

    }
    function validateCheckboxes() {

        checked_discount();
        // checked_with_vehicle();

        const checkboxes = document.getElementsByClassName('position_checkbox');
        const atLeastOneChecked = Array.from(checkboxes).some((checkbox) => checkbox.checked);

        if (!atLeastOneChecked) {
            // Handle the case where no checkbox is checked (e.g., show an error message)
            // console.log("Please select at least one checkbox.");
        }






        return atLeastOneChecked;
    }

    function check_red(booking_date, hour_number, position_name) {
        if (complexArray == null) {
            // console.log('booking data not received yet');
            return;
        }
        // console.log('hour_number: ',hour_number);
        // console.log('position_name: ',position_name);
        i = hour_number;
        ii = position_name;
        const element_id = "position_"+ii;
        if (!document.getElementById(element_id)) {
            return;
        }
        // var is_problematic_position = (element_id == 'position_1' || element_id == 'position_2');

        var state_number = complexArray[booking_date][i+':00']['booking'][ii];
        // if (is_problematic_position) {
        //     console.log('Checking '+element_id+' '+state_number);
        // }

        if (state_number > 0) {
            // console.log('Set red to ',i,ii);
            document.getElementById(element_id).disabled = true;
            // document.getElementById(element_id).display = 'none';

            collection = document.getElementsByClassName(element_id);
            for (let i = 0; i < collection.length; i++) {
                collection[i].style.backgroundColor = "rgb(255,200,200)";
                collection[i].checked = false;
            }

            // console.log("disabled - "+element_id+' '+booking_date+' '+i+':00'+' '+'booking'+' '+ii);
        } else {

        }
    }

    function filterNonNumericCharactersById(id) {
        var ele = document.getElementById(id);
        var str = ele.value;
        ele.value = str.replace(/\D/g, '');
    }

    function checkBookingRecord() {
                // console.log('update_booking_record ');
        setTimeout(function() {
            checkConfirmCode(false);
        }, 1);


        // Get the password inputs
        filterNonNumericCharactersById('octopus_no');
        filterNonNumericCharactersById('octopus_no_cf');

        filterNonNumericCharactersById('octopus_no_q');
        filterNonNumericCharactersById('octopus_no_q_cf');
        filterNonNumericCharactersById('octopus_no_q_cf');
        filterNonNumericCharactersById('telephone');
        
        const booking_date = document.getElementById("booking_date").value;
        const begin_hour = parseInt(document.getElementById("begin_hour").value);
        const end_hour = parseInt(document.getElementById("end_hour").value);
        // console.log("check for - "+booking_date+' '+begin_hour+' '+end_hour);
        max_num = 200
        for (var ii = 1; ii <= max_num; ii++) {
            const element_id = "position_"+ii;
            if (!document.getElementById(element_id)) {
                continue;
            }
            document.getElementById(element_id).disabled = false;
            document.getElementById(element_id).display = 'block';
        }

        positionClass = document.getElementsByClassName('position');
        for (let i = 0; i < positionClass.length; i++) {
            positionClass[i].style.backgroundColor = "#A4FD51";
        }

                    // collection = document.getElementsByClassName(element_id);
                    // for (let i = 0; i < collection.length; i++) {
                    //     collection[i].style.backgroundColor = "white";
                    // }
        for (var i = begin_hour; i < end_hour; i++) {
            // console.log('level 1 '+i);
            for (var ii = 1; ii <= max_num; ii++) {
                check_red(booking_date, i, ii);
            }
            check_red(booking_date, i, 'VIP');
        }



        begin_hour_ = parseFloat(document.getElementById('begin_hour').value);
        end_hour_ = parseFloat(document.getElementById('end_hour').value);
        if (end_hour_ < begin_hour_) {
            alert('您不能選擇早於開始時間的結束時間。 \n You cannot choose the ending hour earlier than beginning hour.');
            document.getElementById('begin_hour').value = '';
            document.getElementById('end_hour').value = '';
        }

        checkFutureDateTime();
        confirmationCodeValid = validateConfirmationCode();
        checkboxesValids = validateCheckboxes();
        dateTimeIsValid = isValidDateTime();
        octopusConfirm = confirm_octopus();
        nameValid = !checkNameEmpty();

        // console.log('checkboxesValids,dateTimeIsValid,octopusConfirm: ',checkboxesValids,dateTimeIsValid,octopusConfirm);

        collection = document.getElementsByClassName('submit-button');
        for (let i = 0; i < collection.length; i++) {
            if (checkboxesValids&&dateTimeIsValid&&octopusConfirm&&confirmationCodeValid&&nameValid) {
                collection[i].style.color = 'black';
                collection[i].disabled = false;
            } else {
                collection[i].style.color = 'grey';
                collection[i].disabled = true;
            }
        }











    }

    // Create an interval that calls sayHello() every 2 seconds (2000 milliseconds)
    const intervalId = setInterval(checkBookingRecord, 1000);












// Cleaning previous page data
// Select all input elements and checkboxes
var inputs = document.querySelectorAll('input');

// Loop through the selected elements
for (var i = 0; i < inputs.length; i++) {
    // If it's a text input, clear the value
    // if (inputs[i].type == 'text') {
    //     inputs[i].value = '';
    // }

    // If it's a checkbox, uncheck it
    if (inputs[i].type == 'checkbox') {
        inputs[i].checked = false;
    }
}
document.getElementById('confirmation_code').value = '';

document.getElementById('confirmation_button').value = ' 確認電郵 Verify Email ';






</script>


<script type="text/javascript">
    

// document.write("Window width : " + window.innerWidth);
// document.write("Window height : " + window.innerHeight);


setTimeout(function () {
    alert('頁面逾時 \n Page timeout ');
    window.location.href = "./";
}, 15*60*1000);



</script>



                    </td>
                </tr>
            </table>

</body>
</html>
<?php
t_log('end[input-form.php]');
?>