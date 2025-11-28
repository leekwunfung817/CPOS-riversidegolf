<?php 

require_once '../logger.php';

$_POST['amount'] = 60;
$_POST['reference'] = '60a21_20250624_17_18_1_83';

m_log("reach pay.php " . json_encode($_POST) );

//交易金额
if(isset($_POST['amount']) && !empty($_POST['amount']) && isset($_POST['reference']) && !empty($_POST['reference']))
{
	if ($_POST['amount'] == 0) {
	echo "cause 1";
		// header("HTTP/1.1 504 Gateway Timeout");
		die();
	}
} else {
	echo "cause 2";
	// header("HTTP/1.1 504 Gateway Timeout");
	die();
}

$auth = $_POST['reference'];

session_start();

setcookie("paying_session", $auth, time()+3600);
$_SESSION["paying_session"] = $auth;




require_once '../account_variable.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM `golf-payment-session` where `payment-datetime` is not null and `auth`='".$auth."'; ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	// header("HTTP/1.1 504 Gateway Timeout");
	echo "cause 3";
	die();
}



	echo "cause 4";





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


	echo "cause 5";

 ?>
<body>
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
<?php

//测试页面
//https://testsecureacceptance.cybersource.com/pay
$post_url = 'https://testsecureacceptance.cybersource.com/oneclick/pay';

//使用前请先设置以下三个参数
$profile_id = '4048C58C-89F6-4FFE-A6EE-90C4DCE5C5F1';	//Profile ID
$access_key = 'cf4b773036d53da9b3cc010ce86858da';	//Profile Access Key
$secret_key = 'ba31989dbabf47f3b5a638666aa5e8d76461fbcbfaaa4e669778296980b5c089e671958734324b829a331956ca6a2e2427e85a831fc743e38c81c64877dcdd295e7cea64b4ff4cd689e0aa4b3d26b4186e82ad138ab54b36b39496b9b7ea03b6b42bbe3f6a6744aa88e41d3db032d15bf47d0f105c834bc2805c5bcc89539173';	//Profile Secret Key


$post_url = 'https://secureacceptance.cybersource.com/pay';

// First Profile
$profile_id = '73B281D2-B93B-4CAA-95B4-8D945E5A4C4F';
$access_key = '1f376db974fe39a38c56c8996af542fe';
$secret_key = 'c3028e36e6d8483bafd567cc73e17161805651d0aa484c629293ab71e63a6a9d89e3e8a417964b47bec9a7c007735e10085187857cea4c93a59d3ed7ba162ca486cafdd18be248ec85f4c69e95bfa3152dcaf7e5ced84919a0001a217a8c45cd9da011f2672c4e5f9572801b0328e1d517125b5ffdef43e9ba1a81f1c0086507';



$pay_amount = 0;
$reference_num = 0;

//交易金额
if(isset($_POST['amount']) && !empty($_POST['amount']))
{
	$pay_amount = $_POST['amount'];
}

//参考编号(相当于商户自己的收据编号)
if(isset($_POST['reference']) && !empty($_POST['reference']))
{
	$reference_num = $_POST['reference'];
}



//从参数中提取需要签名的字段, 把字段和值合成字符串, 用逗号分隔, 之后用于签名
function buildSignDataString($params) 
{
	$signedFieldNames = explode(",", $params["signed_field_names"]);

	foreach ($signedFieldNames as $field) 
	{
		$dataToSign[] = $field ."=" . $params[$field];
	}
	return implode(",", $dataToSign);
}

//$params：表单中的所有参数数组
//$skey Profile Secret Key
function sign($params, $skey) 
{
	$str_data = buildSignDataString($params);
	
	$signature = base64_encode(hash_hmac('sha256', $str_data, $skey, true));
	return $signature;
}

	
function getCheckoutPara($access_key, $secret_key, $profile_id, $pay_amount, $reference_number)
{
	//需要签名的字段, 用','隔开便于之后解析
	$signed_field_names = 'access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,';
	$signed_field_names .='signed_date_time,locale,transaction_type,reference_number,amount,payment_method,';
	$signed_field_names .='currency,bill_to_forename,bill_to_surname,'.//bill_to_address_line2,'.
	$signed_field_names .='bill_to_address_postal_code,';
	$signed_field_names .='bill_to_phone,';//



	$signed_field_names .='bill_to_address_line1,bill_to_address_city,bill_to_address_country,bill_to_email,';
	$signed_field_names .='bill_to_address_state,';







	//ship_to_forename,ship_to_surname,ship_to_address_line1,ship_to_address_line2,'
	//$signed_field_names .='ship_to_address_city,ship_to_address_country,ship_to_address_state,ship_to_address_postal_code,ship_to_phone,';
	$signed_field_names .= 'override_custom_cancel_page,override_custom_receipt_page';

	$locale = 'en-us';//支付页面的语言en-us，zh-hk，zh-cn

	$signed_date_time = gmdate("Y-m-d\TH:i:s\Z");//签名时间, UTC时间







	// 1. write the http protocol
	$full_url = "https://";

	// 2. check if your server use HTTPS
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
	    $full_url = "https://";
	}

	// 3. append domain name
	$full_url .= $_SERVER["SERVER_NAME"];

	// 4. append request URI
	// $full_url .= $_SERVER["REQUEST_URI"];

	// print $full_url; // example output: https://example.com/post/1










	// 参数数组

	$params = [
		'access_key' => $access_key, //Profile Access Key
		'profile_id' => $profile_id, //Profile ID
		'transaction_uuid' => uniqid(), //唯一标识符用 uniqid() 生成, 回调时可以保存返回值(交易ID)

		//由服务器签名的以逗号分隔的响应数据列表。此列表中的所有字段都应该用于生成一个签名，然后可以将其与要进行ve的响应签名进行比较 释放响应。
		'signed_field_names' => $signed_field_names,
		'payment_method'=> 'card',//["VISA","MASTERCARD","AMEX"],
		'unsigned_field_names' => '',
		'signed_date_time' => $signed_date_time,
		'locale' => $locale,//语言

		//事务处理的类型。可能值：
		//authorization
		//authorization,create_payment_token
		//authorization,update_payment_token
		//sale
		//    sale,create_payment_token
		//    sale,update_payment_token
		//    create_payment_token
		//    update_payment_token
		
		// 'transaction_type' => 'authorization',
		// 'transaction_type' => 'sale,create_payment_token',
		'transaction_type' => 'sale',
		'reference_number' => $reference_number,//商家自己的订单编号
		'amount' => strval($pay_amount),//付款总金额
		'currency' => 'HKD',//货币类型
		'bill_to_forename' => '',//客户的名字
		'bill_to_surname' => '',//客户姓氏

		// 'bill_to_address_line1' => '',//账单地址的第一行
		// 'bill_to_address_city' => '',////账单地址中的城市
		// 'bill_to_address_country' => '',//账单地址的国家地区代码, 中国CN, 香港HK
		// 'bill_to_email' => '',//客户电邮地址，包括完整的域名
		// 'bill_to_address_state' =>'', ////账单地址中的州或省,这个字段将返回给美国和加拿大
		// 'bill_to_address_state_us_ca' => '',
		// 'bill_to_address_postal_code' => '', //邮政编码默认可以用000000

		'bill_to_address_line1' => '1295 Charleston Rd',//账单地址的第一行
		'bill_to_address_city' => 'Mountain View',////账单地址中的城市
		'bill_to_address_country' => 'US',//账单地址的国家地区代码, 中国CN, 香港HK
		'bill_to_email' => 'null@cybersource.com',//客户电邮地址，包括完整的域名
		'bill_to_address_state' =>'CA', ////账单地址中的州或省,这个字段将返回给美国和加拿大
		// 'bill_to_address_state_us_ca' => 'CA',
		'bill_to_address_postal_code' => '94043', //邮政编码默认可以用000000

// field-bill_to_address_state_us_ca 
		// bill_to_address_state_us_ca
		// bill_to_address_state_us_ca

		// 'billTo_street1' => '1295 Charleston Rd',
		// 'billTo_city' => 'Mountain View',
		// 'billTo_postalCode' => '94043',
		// 'billTo_country' => 'US',
		// 'bill_country' => 'us',
		// 'billTo_state' => 'CA',
		// 'bill_state' => 'ca',

		//代码查询: https://developer.cybersource.com/docs/cybs/en-us/country-codes/reference/all/na/country-codes/country-codes.html

		// 'bill_to_address_line2' => '',
		'bill_to_phone' => '',//客户电话号码,如果订单来自美国以外的地区，则包括国家代码, 此字段对于信用卡支付是可选的
		//'bill_to_company_name' => '',//客户的公司名称

		// 'ship_to_forename' => '',//客户的名字
		// 'ship_to_surname' => '',//客户姓氏
		//'ship_to_company_name' => '',//接收该产品的公司的名称
		// 'ship_to_address_line1' => '',          //第一行的航运地址默认可以用'default'
		// 'ship_to_address_line2' => '',          //第二行航运地址默认可以用'default'
		// 'ship_to_address_city' => '',//城市的运输地址
		// 'ship_to_address_country' => '',//运输地址的国家代码
		// 'ship_to_address_state' =>'default',//对于美国和加拿大，请使用标准的州、省和地区代码,如果运输地址是美国或加拿大，则需要此字段
		// 'ship_to_address_postal_code' =>'default',   //运输地址的邮政编码,默认可以用000000如果账单地址国家为美国或加拿大，则需要此字段
		// 'ship_to_phone' => '',//送货地址的电话号码
		//'ship_to_type' => '',      //商业、住宅、商店, 留空/不传/给个默认值

		//  sameday:快递或当日服务
		//  oneday:次日或通宵服务
		//  twoday:为期两天的服务
		//  threeday:三天服务
		//  lowcost:最低成本服务
		//  pickup:商店皮卡
		//  other:其他运输方式
		//  none:无运输方式
		//'shipping_method' => '',   //运输方法,留空/不传/给个默认值

		'override_custom_cancel_page' => '',//取消url(用户点取消交易后回调的页面)

		//支付成功后的收据页面回调url(可在此输出"支付成功,保存支付结果,打印支付后的QRCODE等资料)
		//注意此页面为同步回调,复杂操作在后台处理,例如发送成功支付的邮件等
		'override_custom_receipt_page' => $full_url.'/GolfBooking/payment-page/payment-confirm.php'

		//预授权(钱没有真正到商家中，退款时，会灵活一些)：0: Preauthorization

		//最终授权：1: Final authorization
		// 'auth_indicator' => '1',
		// AUTOCAPTURE：自动捕获，是不是不用再请求 rest api 的 /v1/payments/{id}/captures接口？？？？？？
		// STANDARDCAPTURE：标准捕获
		// verbal：强制捕获
		// 'auth_type' => 'AUTOCAPTURE'

	];

	//用需要签名的参数和密钥来计算签名
	$params['signature'] = sign($params, $secret_key);//参数签名

	return $params;

}



//$post_url 要提交的页面链接
//$params 所有要提交的数据, 根据数据自动生成要提交的表单
//返回整个页面

function buildOnlinePaymentSubmitForm($post_url, $params) 
{
	$html = '<!DOCTYPE html>';
	$html .= '<html>';
	$html .= '<body>';

	$html .= '<form id="cybersource-payment-form" action="'. $post_url . '" method="post" style="display:none;">';

	foreach ($params as $key => $value) 
	{
		$html .= '<div>';
		$html .= '<label>' . $key . '</label>';
		$html .= '<input type="text" name="' . $key . '" value="' . $value . '" />';
		$html .= '</div>';
	}

	$html .= '<button type="submit">Submit</button></form>';

	//自动提交
	$html .= "<script> document.getElementById('cybersource-payment-form').submit();</script>";
	$html .= '</body>';
	$html .= '</html>';

	return $html;
}



	echo "cause 6";

m_log("pay.php getCheckoutPara $access_key, $secret_key, $profile_id, $pay_amount, $reference_num");


//生成要提交的数据, 包括计算签名
$para = getCheckoutPara($access_key, $secret_key, $profile_id, $pay_amount, $reference_num);

//echo "<pre>";
//print_r($para);
m_log("pay.php buildOnlinePaymentSubmitForm $post_url, ",json_encode($para));

echo buildOnlinePaymentSubmitForm($post_url, $para);//把数据组合成要提前的表单并自动提交


?>