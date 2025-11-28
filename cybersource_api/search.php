<?php
$import_filepath__ = 'logger.php';
if (file_exists("./$import_filepath__")) {
    $import_filepath__ = "./$import_filepath__";
} else if (file_exists("../$import_filepath__")) {
    $import_filepath__ = "../$import_filepath__";
}
require_once $import_filepath__;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalParameter.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'ObjectSerializer.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'SdkTracker.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'CreateSearchRequest.php';


$g_SearchResourcePath = "/tss/v2/searches";         //查询交易清单API的路径
$g_DetailResourcePath = "/tss/v2/transactions/{id}";//单笔交易详情API的路径

$g_authType = GlobalParameter::HTTP_SIGNATURE;//使用HTTP授权方式, http_signature/jwt

$g_ClientId = "web";//客户端ID, 可以为空

//请在此处设置交易平台建立的KEY参数
$g_MerchantID = ""; //商户ID, 
$g_AppKeyId = '';   //Key 的ID
$g_SecretKey = '';  //Secret Key

if(1)
{

    $g_runEnv = "apitest.cybersource.com";              //API访问的地址, apitest.cybersource.com 为测试环境地址
    
    //cybersource 提供的测试环境参数一
    //cybersource test key testrest
    $g_SecretKey = 'yBJxy6LjM2TmcPGu+GaJrHtkke25fPpUX+UY6/L/1tE=';
    $g_AppKeyId = "08c94330-f618-42a3-b09d-e1e43be5efda";
    $g_MerchantID = "testrest";        

    //参数二, 也可用
    //cybersource test key testrest_cpctv
    $g_SecretKey = 'zXKpCqMQPmOR/JRldSlkQUtvvIzOewUVqsUP0sBHpxQ=';
    $g_AppKeyId = "964f2ecc-96f0-4432-a742-db0b44e6a73a";
    $g_MerchantID = "testrest_cpctv";


    // 73B281D2-B93B-4CAA-95B4-8D945E5A4C4F
    $g_runEnv = "api.cybersource.com";
    // $g_SecretKey = 'c3028e36e6d8483bafd567cc73e17161805651d0aa484c629293ab71e63a6a9d89e3e8a417964b47bec9a7c007735e10085187857cea4c93a59d3ed7ba162ca486cafdd18be248ec85f4c69e95bfa3152dcaf7e5ced84919a0001a217a8c45cd9da011f2672c4e5f9572801b0328e1d517125b5ffdef43e9ba1a81f1c0086507';
    // $g_SecretKey = 'c3028e36e6d8483bafd567cc73e17161805651d0aa484c629293ab71e63a6a9d89e3e8a417964b47bec9a7c007735e10085187857cea4c93a59d3ed7ba162ca486cafdd18be248ec85f4c69e95bfa3152dcaf7e5ced84919a0001a217a8c45cd9da011f2672c4e5f9572801b0328e1d517125b5ffdef43e9ba1a81f1c0086507';

    // $g_AppKeyId = "73B281D2-B93B-4CAA-95B4-8D945E5A4C4F";
    // $g_AppKeyId = "1f376db974fe39a38c56c8996af542fe";
    // $g_AppKeyId = "WhiteheadGolfSecureKey";
    $g_SecretKey = '88GIzXAc1y6sqNcvUB+murokhDMYmTMgh4rVMHCtVT0=';
    $g_AppKeyId = "b5d7a134-ec29-48b3-8d71-eb65499ee1c0";

    $g_MerchantID = "rwg180185019"; // Must correct
    

}     


    function generateDigest($payLoad)
    {
        $utf8EncodedString = mb_convert_encoding($payLoad, 'UTF-8', mb_detect_encoding($payLoad));
        $digestEncode = hash("sha256", $utf8EncodedString, true);
 
        return base64_encode($digestEncode);
    }
    
    //Purpose: using for access and return the signature token
    function accessTokenHeader($signatureString, $headerString, $SecretKey, $ApiKeyID)
    {
        $signatureByteString = mb_convert_encoding($signatureString, 'UTF-8', mb_detect_encoding($signatureString));
        $decodeKey = base64_decode($SecretKey);
        $signature = base64_encode(hash_hmac(GlobalParameter::SHA256, $signatureByteString, $decodeKey, true));
        $signatureHeader = array(
            'keyid="'.$ApiKeyID.'"',
            'algorithm="'.GlobalParameter::HMACSHA256.'"',
            'headers="'.$headerString.'"',
            'signature="'.$signature.'"'
        );
        return GlobalParameter::SIGNATURE.implode(", ",$signatureHeader);
    }

    //Signature Creation function
    function generateToken($resourcePath, $payloadData, $method, $host, $MerchantID, $SecretKey, $ApiKeyID) //add
    {
        $date = date("D, d M Y G:i:s ").GlobalParameter::GMT;
        //$date = 'Sat, 19 Oct 2024 04:39:17 GMT';
        
        $methodHeader = strtolower($method);
        $signatureString = "";
        if($method == GlobalParameter::GET || $method == GlobalParameter::DELETE)
        {
            //signature creation for GET/DELETE
            $signatureString = "host: ".$host."\ndate: ".$date."\nrequest-target: ".$methodHeader." ".$resourcePath."\nv-c-merchant-id: ".$MerchantID;
            
            $headerString = GlobalParameter::GETALGOHEADER;
        }
        else if($method == GlobalParameter::POST || $method == GlobalParameter::PUT || $method == GlobalParameter::PATCH)
        {
            //signature creation for POST/PUT
            if(empty($payloadData))
            {
                //没有数据,错误
                return;
            }
            //Get digest data
           
            $digest = generateDigest($payloadData);
            
            $signatureString = "host: ".$host."\ndate: ".$date."\nrequest-target: ".$methodHeader." ".$resourcePath."\ndigest: ".GlobalParameter::SHA256DIGEST.$digest."\nv-c-merchant-id: ".$MerchantID;
            
            $headerString = GlobalParameter::POSTALGOHEADER;
        }
        else
        {
            return;
            //不支持的参数
        }
        
        return accessTokenHeader($signatureString, $headerString, $SecretKey, $ApiKeyID);
    }
    


    function callAuthenticationHeader($AuthenticationType, $host, $method, $postData, $resourcePath, $ClientId, $MerchantID, $SecretKey, $ApiKeyID)
    {
         
        $getToken = generateToken($resourcePath, $postData, $method, $host, $MerchantID, $SecretKey, $ApiKeyID); 
        if($AuthenticationType == GlobalParameter::HTTP_SIGNATURE)
        {
            $vcMerchant = "v-c-merchant-id:".$MerchantID;
            $date = date("D, d M Y G:i:s ").GlobalParameter::GMT;
            //$date = 'Sat, 19 Oct 2024 04:39:17 GMT';
            $headers = array(
                $vcMerchant,
                $getToken,
                "Host:".$host,
                'Date:'.$date
            ); 
        }
        else
        {
            //不支持的授权方式
        }

        array_push($headers, "v-c-client-id:" . $ClientId);
        
        if($method == GlobalParameter::POST || $method == GlobalParameter::PUT || $method == GlobalParameter::PATCH)
        {
            $digest = generateDigest($postData);
            $digestArray = array(GlobalParameter::POSTHTTPDIGEST.$digest);
            $headers = array_merge($headers, $digestArray);
        }
        return $headers;
    }
    
    function getHeaderIfExistInRequestHeaderByCaseInsensitive($headerName, $requestHeaders){
        foreach ($requestHeaders as $header => $val) {
            if(strcasecmp($header,$headerName) == 0){
                return $header;
            }
        }
        return -1;
    }
    
    function httpParseHeaders($raw_headers)
    {
        // ref/credit: http://php.net/manual/en/function.http-parse-headers.php#112986
        $headers = [];
        $key = '';

        foreach (explode("\n", $raw_headers) as $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1] ?? '');
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1] ?? '')]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1] ?? '')]);
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) === "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0] ?? '');
                } elseif (!$key) {
                    $headers[0] = trim($h[0] ?? '');
                }
                trim($h[0] ?? '');
            }
        }

        return $headers;
    }
    
    
    function callApi($authType, $ClientId, $MerchantID, $AppKeyId, $SecretKey, $runEnv, $resourcePath, $method, $queryParams, $postData, $headerParams)
    {
       // self::$logger->info("CALLING API \"$resourcePath\" STARTED");
        $headers = [];

        // MetaKey configuration [Start]
        $useMetaKey = false;
        // MetaKey configuration [End]

        $url = "";
        
        if (!empty($queryParams)) {
            $resourcePath = ($resourcePath . '?' . http_build_query($queryParams));
            $queryParams = null;
        }
        
        foreach ($headerParams as $key => $val) {
            $headers[] = "$key: $val";
        }
        
        // form data
        if ($postData and in_array('Content-Type: application/x-www-form-urlencoded', $headers, true)) {
            $postData = http_build_query($postData);
        } elseif ((is_object($postData) or is_array($postData)) and !in_array('Content-Type: multipart/form-data', $headers, true)) { // json model
            $postData = json_encode(\CyberSource\ObjectSerializer::sanitizeForSerialization($postData));
        }
        
        if ($resourcePath != null) {
            $resourcePath = mb_convert_encoding($resourcePath, 'UTF-8', mb_detect_encoding($resourcePath));
        }

        $authHeader = callAuthenticationHeader($authType, $runEnv, $method, $postData, $resourcePath, $ClientId, $MerchantID, $SecretKey, $AppKeyId);
        
        $requestHeaders = [];
        foreach ($headers as $value) {
            $splitArr = explode(":", $value, 2);
            $requestHeaders[$splitArr[0]] = $splitArr[1];
        }
        
        foreach ($authHeader as $value) {
            
            if(empty($value))continue;
            $splitArr= explode(":", $value, 2);

            if(strcasecmp($splitArr[0],"Signature")==0){
                $headerName= getHeaderIfExistInRequestHeaderByCaseInsensitive($splitArr[0],$requestHeaders);
                if($headerName != -1){
                    unset($requestHeaders[$headerName]);
                }
            }
            if(strcasecmp($splitArr[0],"Authorization")==0){
                $headerName= getHeaderIfExistInRequestHeaderByCaseInsensitive($splitArr[0],$requestHeaders);
                if($headerName != -1){
                    unset($requestHeaders[$headerName]);
                }
            }
            $headerName= getHeaderIfExistInRequestHeaderByCaseInsensitive($splitArr[0],$requestHeaders);
            if($headerName == -1){
                $requestHeaders[$splitArr[0]] = $splitArr[1];
            }
        }

        $reqHeaders=[];
        foreach ( $requestHeaders as $key => $val) {
            $reqHeaders[] = "$key: $val";
        }
        
        //print_r($authHeader);
        //print_r($headers);
        //print_r($queryParams);
        
        //API地址
        $url = GlobalParameter::HTTPS_PREFIX.$runEnv . $resourcePath;
        
        //设置查询响应的时间,如果网速慢可以延长
        $CurlTimeout = 10;  //秒
        $CurlConnectTimeout = 10;
        $SSLVerification = false;
        $CurlProxyHost= "";
        $CurlProxyPort = 0;
        
        $curl = curl_init();
        // set timeout, if needed
        if ($CurlTimeout !== 0) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $CurlTimeout);
        }
        // set connect timeout, if needed
        if ($CurlConnectTimeout != 0) {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $CurlConnectTimeout);
        }

        // return the result on success, rather than just true
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $reqHeaders);

        // disable SSL verification, if needed
        if ($SSLVerification === false) 
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }else{
            curl_setopt($curl, CURLOPT_CAINFO, __DIR__. DIRECTORY_SEPARATOR . 'ssl/cacert.pem');
        }

        //GET请求可能带参数
        if (!empty($queryParams)) {
            $url = ($url . '?' . http_build_query($queryParams));
        }

        if ($method === GlobalParameter::POST) 
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);  //设置要POST的内容
            
        } elseif ($method === GlobalParameter::HEAD) {
            curl_setopt($curl, CURLOPT_NOBODY, true);
        } elseif ($method === GlobalParameter::OPTIONS) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "OPTIONS");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method === GlobalParameter::PATCH) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method === GlobalParameter::PUT) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method === GlobalParameter::DELETE) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method !== GlobalParameter::GET) {
           // self::$logger->error("ApiException : Method . $method . is not recognized.");

        }
        curl_setopt($curl, CURLOPT_URL, $url);
        
        // Set user agent
        
        $userAgent = 'Swagger-Codegen/1.0.0/php';
        
        //用户浏览器的agent, 可以不设置
        if(function_exists('getallheaders')) 
        {
            $header = getallheaders();
            foreach($header as $key => $val)    //从头信息中取出userkey
            {
                if(strcasecmp($key, 'User-Agent') == 0)
                {
                    $userAgent = $val;
                    break;
                }
            }
        }
        else
        {
            foreach($_SERVER as $key => $value) 
            {
                if(strcasecmp($key, 'HTTP_USER_AGENT') == 0)
                {
                    $userAgent = $value;
                    break;
                }       
            }
        }

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);

        curl_setopt($curl, CURLOPT_VERBOSE, 0);

        // obtain the HTTP response headers
            
        curl_setopt($curl, CURLOPT_HEADER, 1);
        
        // Adding Client Cert if Required
        if(false)//证书, 不使用这种方式
        {
            $clientCertPath = $ClientCertFilePathName;
            curl_setopt($curl, CURLOPT_SSLCERT, $clientCertPath);
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'P12');
            curl_setopt($curl, CURLOPT_SSLCERTPASSWD, $this->merchantConfig->getClientCertPassword());
        }
        
        //print_r($reqHeaders);
        
        // echo "callApi( curl_exec ";
        // Make the request
        $response = curl_exec($curl);
        
        $http_header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $http_header = httpParseHeaders(substr($response, 0, $http_header_size));
        $http_body = substr($response, $http_header_size);
        $response_info = curl_getinfo($curl);
        curl_close($curl);
            
        //print_r($response);
        //print_r($response_info);
            
            // Handle the response
            if ($response_info['http_code'] === 0) 
            {
                $curl_error_message = curl_error($curl);

                // curl_exec can sometimes fail but still return a blank message from curl_error().
                if (!empty($curl_error_message)) {
                    $error_message = "API call to $url failed: $curl_error_message";
                } else {
                    $error_message = "API call to $url failed, but for an unknown reason. " .
                        "This could happen if you are disconnected from the network.";
                }
            }
            elseif ($response_info['http_code'] >= 200 && $response_info['http_code'] <= 299) 
            {
                $stream_headers['http_code'] = $response_info['http_code'];

                if(0)//不处理
                {   
                    //检测数据是否为json
                    $data_res = json_decode($http_body);
                    if (json_last_error() > 0) { // if response is a string
                        $data_res = $http_body;
                    }
                    if(!is_object($data_res)) // 数据错误, 转换不了
                    { 
                        
                    }
                    else
                    {
                        //print_r($data_res);
                    }
                }
                
                $data_res = $http_body;//收到的数据直接原样返回
                return [$data_res, $stream_headers['http_code'], $stream_headers];

            }
            else 
            {
               
                
            }
        // echo "callApi( complete http_code:".$response_info['http_code'];
    }

    function selectHeaderAccept($accept)
    {
        if (count($accept) === 0 or (count($accept) === 1 and $accept[0] === '')) {
            return null;
        } elseif (preg_grep("/application\/json/i", $accept)) {
            return 'application/json';
        } else {
            return implode(',', $accept);
        }
    }

    function selectHeaderContentType($content_type)
    {
        if (count($content_type) === 0 or (count($content_type) === 1 and $content_type[0] === '')) {
            return 'application/json';
        } elseif (preg_grep("/application\/json/i", $content_type)) {
            return 'application/json';
        } else {
            return implode(',', $content_type);
        }
    }
    
    



    function createSearch($searchPara)
    {
        // echo "createSearch 1";
        global $g_authType;
        global $g_ClientId;
        global $g_MerchantID;
        global $g_AppKeyId;
        global $g_SecretKey;
        global $g_runEnv;
        global $g_SearchResourcePath;

        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = selectHeaderAccept(['*/*']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = selectHeaderContentType(['application/json;charset=utf-8']);

        $createSearchRequest = new CreateSearchRequest($searchPara);

        // body params
        $_tempBody = null;
        if (isset($createSearchRequest)) {
            $_tempBody = $createSearchRequest;
        }
        
        $sdkTracker = new SdkTracker();
        $modelClassLocation = explode('\\', '\CyberSource\Model\CreateSearchRequest');
        
        $_tempBody = $sdkTracker->insertDeveloperIdTracker($_tempBody, end($modelClassLocation), $g_runEnv);

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }

        // echo "createSearch 2";
        list($response, $statusCode, $httpHeader) = callApi(
                $g_authType,
                $g_ClientId, 
                $g_MerchantID,
                $g_AppKeyId,
                $g_SecretKey,
                $g_runEnv,
                $g_SearchResourcePath,
                GlobalParameter::POST,
                $queryParams,
                $httpBody,
                $headerParams
            );
        // echo "createSearch 3";
            
        // echo "$statusCode<br>";  
        // print_r($response);
        // print_r($httpHeader);

        // echo "createSearch 4";
        return $response;
    }
    
    
    
    function TransactionDetails($id)
    {
        // verify the required parameter 'id' is set
        if ($id === null) 
        {
            return;
        }
        
        global $g_authType;
        global $g_ClientId;
        global $g_MerchantID;
        global $g_AppKeyId;
        global $g_SecretKey;
        global $g_runEnv;
        global $g_DetailResourcePath;
        
        // parse inputs

        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = selectHeaderAccept(['application/hal+json;charset=utf-8']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = selectHeaderContentType(['application/json;charset=utf-8']);
        $serializer = new CyberSource\ObjectSerializer();
        // path params
        if ($id !== null) {
            $g_DetailResourcePath = str_replace(
                "{" . "id" . "}",
                $serializer->toPathValue($id),
                $g_DetailResourcePath
            );
        }
        if ('GET' == 'POST') {
            $_tempBody = '{}';
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        

        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = callApi(
                $g_authType,
                $g_ClientId, 
                $g_MerchantID,
                $g_AppKeyId,
                $g_SecretKey,
                $g_runEnv,
                $g_DetailResourcePath,
                GlobalParameter::GET,
                $queryParams,
                $httpBody,
                $headerParams
            );
            
        //echo "$statusCode<br>";   
        
        print_r($response);
        //print_r($httpHeader);
       // return [$response, $statusCode, $httpHeader];
            
        }
        catch (ApiException $e) 
        {
            switch ($e->getCode()) {
                case 200:


                    break;
            }

            throw $e;
        }
    }
    
$date_from = '';
$date_to = '';
$offset = '0';
$limit = '1';
$sort = "id:asc,submitTimeUtc:asc";

$query = '';

$trans_id = '';


if(isset($_POST['date_from']) && !empty($_POST['date_from']))
{
    $date_from = $_POST['date_from'];
}
if(isset($_POST['date_to']) && !empty($_POST['date_to']))
{
    $date_to = $_POST['date_to'];
}
if(isset($_POST['query']) && !empty($_POST['query']))
{
    $query = $_POST['query'];
}
if(isset($_POST['offset']) && !empty($_POST['offset']))
{
    $offset = $_POST['offset'];
}
if(isset($_POST['limit']) && !empty($_POST['limit']))
{
    $limit = $_POST['limit'];
}
if(isset($_POST['sort']) && !empty($_POST['sort']))
{
    $sort = $_POST['sort'];
}


if(isset($_POST['trans_id']) && !empty($_POST['trans_id']))
{
    $trans_id = $_POST['trans_id'];
}


if(isset($_GET['reference_no']) && !empty($_GET['reference_no']))
{
    $query = 'clientReferenceInformation.code:'.$_GET['reference_no'];
}

function cybersource_api_query_1($query, $offset)
{
        $query_str =  $query;
        
         $requestObjArr = [
                "save" => false,
                "name" => "",
                "timezone" => "Asia/Hong_Kong",
                "query" => $query_str,
                "offset" => '0',
                "limit" => '100',
                "sort" => "id:asc,submitTimeUtc:asc"
        ];
        // print_r($requestObjArr);
        return createSearch($requestObjArr);
        // ['_embedded']
}

function cybersource_api_query($query)
{
    return cybersource_api_query_1($query, '0');
}

function cybersource_api_date_range($begin,$end,$offset)
{
    $begin_date = DateTime::createFromFormat('Y-m-d H:i:s',$begin);
    // $begin_date->modify('-8 hours');

    $end_date = DateTime::createFromFormat('Y-m-d H:i:s',$end);
    // $end_date->modify('-8 hours');

    echo "From ".$begin_date->format('Y-m-d H:i:s').' to '.$end_date->format('Y-m-d H:i:s').'<br>';

    $query = 'submitTimeUtc:['
        .($begin_date->getTimestamp()*1000).' TO '
        .($end_date->getTimestamp()*1000).']';

    echo "$query<br>";
    return cybersource_api_query($query,$offset);
}


function cybersource_api_reference_no($reference_no)
{
    return cybersource_api_query('clientReferenceInformation.code:'.$reference_no);
}

if(!empty($trans_id))
{
    //  $transid = '7245925089726368604005';//测试ID
    TransactionDetails($trans_id);

}
else
{

    //$the_date = strtotime("2010-01-19 00:00:00");
    //echo ($the_date);
    //strtotime 把时间字符转成utc
    //gmdate 把UTC按格式转成字符 gmdate("Y-m-d H:i:s", strtotime($date_from));
    //echo(date_default_timezone_get() . "<br />");//查询当前时区
    //$date = date_parse(date('Y-m-d H:i:s'));
    
    //date_default_timezone_set("Asia/Hong_Kong");
    
    date_default_timezone_set("UTC");//转换前把时区切换到UTC
    
    $utc_from = strtotime($date_from);  
    $utc_from *= 1000;  
    
    $utc_to = strtotime($date_to);  
    $utc_to *= 1000;    

    //submitTimeUtc:[NOW/DAY-7DAYS TO NOW/DAY+1DAY}
    $query_str =  "submitTimeUtc:[$utc_from TO $utc_to]";
    if(!empty($query))
    {
        // $query_str =  $query; // . " AND " . $query_str;
        cybersource_api_query($query);
    }

}



$import_filepath = "lib_complete_payment.php";
if (file_exists("./$import_filepath")) {
    $import_filepath = "./$import_filepath";
} else if (file_exists("../$import_filepath")) {
    $import_filepath = "../$import_filepath";
}
require_once $import_filepath;



function top_up_cybersource($conn,$reference_no)
{
    m_log("RSTUCS $reference_no");
    $json_raw = cybersource_api_reference_no($reference_no);
    try {
        $trade_record = json_decode($json_raw);
    } catch (Exception $e) {
        echo "Exception to decode JSON $json_raw";
    }

    if ($trade_record==null || !property_exists($trade_record, '_embedded')) {
        return 0;
    }



    foreach ($trade_record->_embedded->transactionSummaries as $record_index => $record_object) {
        

        // Assuming $data is the given object
        // $data = json_decode(json_encode($data), true);
        $data = $record_object;

        // Extract data from the object
        if (!property_exists($data->orderInformation->amountDetails, 'totalAmount')) {
            continue;
        }

        if (!property_exists($data->processorInformation, 'approvalCode')) {
            continue;
        }

        $id = $data->id;
        $submitTimeUtc = $data->submitTimeUtc;
        $merchantId = $data->merchantId;
        $reasonCode = $data->applicationInformation->reasonCode;
        $rCode = $data->applicationInformation->rCode;
        $rFlag = $data->applicationInformation->rFlag;
        $code = $data->clientReferenceInformation->code;
        $applicationName = $data->clientReferenceInformation->applicationName;
        $transactionId = $data->consumerAuthenticationInformation->transactionId;
        $ipAddress = $data->deviceInformation->ipAddress;
        $resellerId = $data->merchantInformation->resellerId;
        $address1 = $data->orderInformation->billTo->address1;
        $state = $data->orderInformation->billTo->state;
        $city = $data->orderInformation->billTo->city;
        $country = $data->orderInformation->billTo->country;
        $postalCode = $data->orderInformation->billTo->postalCode;
        $email = $data->orderInformation->billTo->email;
        $firstName = $data->orderInformation->billTo->firstName;
        $lastName = $data->orderInformation->billTo->lastName;
        $totalAmount = $data->orderInformation->amountDetails->totalAmount;
        $currency = $data->orderInformation->amountDetails->currency;
        $paymentType = $data->paymentInformation->paymentType->type;
        $method = $data->paymentInformation->paymentType->method;
        $suffix = $data->paymentInformation->card->suffix;
        $prefix = $data->paymentInformation->card->prefix;
        $type = $data->paymentInformation->card->type;
        $commerceIndicator = $data->processingInformation->commerceIndicator;
        $commerceIndicatorLabel = $data->processingInformation->commerceIndicatorLabel;
        $processorName = $data->processorInformation->processor->name;
        $approvalCode = $data->processorInformation->approvalCode;
        // $eventStatus = $data->processorInformation->eventStatus;
        $retrievalReferenceNumber = $data->processorInformation->retrievalReferenceNumber;
        $transactionDetailHref = $data->_links->transactionDetail->href;
        $transactionDetailMethod = $data->_links->transactionDetail->method;


        if ($reasonCode==100) {
            // echo "$record_index:<br>";
            // var_dump($record_object);
            // echo "<br>";
            $sql = "select count(*) c from golf_cybersource where transaction_id='$id';";
            $c=-1;
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $c=$row['c'];
                }
            } else {
                echo "For $sql <br>";
                echo "No record <br>";
            }
            // echo "Record count: $c";
            if ($c>0) {
                $sql = "
update golf_cybersource set signed_date_time='$submitTimeUtc'
where transaction_id='$id' and signed_date_time is null
;";

                // echo "$sql";
                   if ($conn->query($sql) === TRUE) {
                   } else {
                   }
                $sql = "
update `golf-payment-session` set `payment-datetime`=addtime('$submitTimeUtc','08:00:00')
where `golf-payment-session`.auth='$code'
;";

                // echo "$sql";
                   if ($conn->query($sql) === TRUE) {
                   } else {
                   }
            }
            if ($c>0||strlen($id)==0 || $totalAmount==0) {

                continue;
            }
            if (true) {
                // echo "Insert transaction";
                $insert_data = array();
                $insert_data['decision']     = 'ACCEPT';
                $insert_data['transaction_id'] = "$id";

                $insert_data['req_transaction_type'] = "sale";
                $insert_data['req_reference_number'] = "$code";
                $insert_data['req_amount'] = "$totalAmount";
                $insert_data['req_currency'] = "$currency";
                $insert_data['req_locale'] = "en-us";
                $insert_data['req_payment_method'] = "card";
                $insert_data['req_bill_to_forename'] = "$firstName";
                $insert_data['req_bill_to_surname'] = "$lastName";

                $insert_data['req_card_number'] = "$suffix";
                $insert_data['req_card_type'] = "$type";
                $insert_data['req_card_type_selection_indicator'] = "$rCode";
                $insert_data['card_type_name'] = "$method";
                $insert_data['reason_code'] = "$reasonCode";
                $insert_data['auth_amount'] = "$totalAmount";
                $insert_data['auth_code'] = "$approvalCode";
                $insert_data['auth_trans_ref_no'] = "$id";
                $insert_data['auth_reconciliation_reference_number'] = "$retrievalReferenceNumber";
                $insert_data['signed_date_time'] = "$submitTimeUtc";

                m_log("reach search.php INSERT Cybersource $reference_no");
                if (insert_payment_record($conn, $insert_data)) {
                    m_log("reach search.php MAIL $reference_no");
                    mail_payment_record_by_auth($insert_data['req_reference_number'], "Cybersource API processing");
                    return 1;
                } else {
                    return 0;
                }
            } else {
                echo "Transaction was recorded";
            }


        }


    }
    return 0;
}

function cybersource_api_booking_id($conn,$booking_id)
{
    $sql = "SELECT `auth`
    FROM `golf_fairway_booking` 
    where `id`='$booking_id';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $auth=$row['auth'];
            return cybersource_api_reference_no($auth);
        }
    }
    $sql = "SELECT `auth`
    FROM `golf_fairway_booking_history` 
    where `id`='$booking_id';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $auth=$row['auth'];
            return cybersource_api_reference_no($auth);
        }
    }
    
}

function top_up_cybersource_by_id($conn,$booking_id)
{
    $sql = "SELECT `auth`
    FROM `golf_fairway_booking` 
    where `id`='$booking_id';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $auth=$row['auth'];
            return top_up_cybersource($conn,$auth);
        }
    }
    $sql = "SELECT `auth`
    FROM `golf_fairway_booking_history` 
    where `id`='$booking_id';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $auth=$row['auth'];
            return top_up_cybersource($conn,$auth);
        }
    }
}

function report_cybersource_by_id($conn,$booking_id)
{
    top_up_cybersource_by_id($conn,$booking_id);

    echo "########################################### BEGIN $booking_id<br>";
    $trade_record = json_decode(cybersource_api_booking_id($conn,$booking_id));

    // var_dump($trade_record);
    if (!property_exists($trade_record, '_embedded')) {
        return;
    }

    echo "<hr>";
    // var_dump($trade_record->_embedded->transactionSummaries);
    echo "<hr>";
    echo "BEGIN";
    echo "<hr>";

    foreach ($trade_record->_embedded->transactionSummaries as $record_index => $record_object) {

        $data = $record_object;
        if (!property_exists($data->orderInformation->amountDetails, 'totalAmount')) {
            continue;
        }
        // echo "<hr>";
        // echo "Record found";
        // var_dump($data->orderInformation->amountDetails);
        // var_dump($data);
        // if (!property_exists($data->processorInformation, 'approvalCode')) {
        //     continue;
        // }
        $id = $data->id;
        $totalAmount = $data->orderInformation->amountDetails->totalAmount;
        if (strlen($id)==0 || $totalAmount==0) {
            continue;
        }

        echo "<hr>";
        echo "Received Object <br>";
        var_dump($record_object);

        echo "<hr>";
        echo "Processor Information <br>";
        var_dump($record_object->processorInformation);

        echo "<hr>";
        echo "Order Information <br>";
        var_dump($data->orderInformation->amountDetails);




    }
}

?>