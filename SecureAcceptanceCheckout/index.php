<?php 

require_once '../logger.php';

m_log("reach SecureAcceptanceCheckout ".$_POST);

//交易金额
if(isset($_POST['amount']) && !empty($_POST['amount']) && isset($_POST['reference_number']) && !empty($_POST['reference_number']))
{
	$pay_amount = $_POST['amount'];
	$auth = $_POST['reference_number'];
	if ($pay_amount == 0) {
		header("HTTP/1.1 504 Gateway Timeout");
		die();
	}
} else {
	header("HTTP/1.1 504 Gateway Timeout");
	die();
}


require_once '../account_variable.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM `golf-payment-session` where `payment-datetime` is not null and `auth`='".$auth."'; ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	header("HTTP/1.1 504 Gateway Timeout");
	die();
}


$sql = "
	update `golf_fairway_booking` 
	set `timestamp`=CURRENT_TIMESTAMP
	where `auth`='".$auth."';
";

try {
   if ($conn->query($sql) === TRUE) {
   } else {
   }
} catch (Exception $e) {
}





 ?>

<!DOCTYPE html>
<html>

<head>
<TITLE>Payment</TITLE>
</head>

<body style="display: none;">
	
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

	<form id="reference_amount_form" method="post" action="pay.php"> 

      <table  align="center">
	  
	  		<tr><td>
			Reference No:<input type="text" name="reference" id="reference" value="<?php echo $auth; ?><?php // $digits = 13; echo rand(pow(10, $digits-1), pow(10, $digits)-1); ?>" readonly="readonly">
			</td></tr>
			<tr><td >
			Amount:<input type="text" name="amount" id="amount" value="<?php echo $pay_amount; ?>">
			</td></tr>
			<tr> <td align="center">
			<!-- <input type="submit" name="submit" value="Pay Now">  -->
			<button type="submit">Submit</button>
			</td></tr>
      </table>
	</form>
	
<script>

	// var d = new Date();
	// var n = d.getTime();
	// document.getElementById("reference").value = parseInt(n/1000);

</script>

<script> document.getElementById('reference_amount_form').submit();</script>
</body>
</html>
