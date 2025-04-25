<?php
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

$httpOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
if (in_array($httpOrigin, [
	'https://china.cheonyu.com', 
	'https://english.cheonyu.com',
	'https://cheonyu.com',
	'https://www.cheonyu.com'
]))
//header("Access-Control-Allow-Origin: ${httpOrigin}");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
header("Content-type:text/html;charset=utf-8");

$headers = apache_request_headers();
/*
Array
(
    [Host] => china.cheonyu.com
    [Connection] => keep-alive
    [sec-ch-ua] => " Not;A Brand";v="99", "Google Chrome";v="91", "Chromium";v="91"
    [Accept] => * /*
    [X-Requested-With] => XMLHttpRequest
    [sec-ch-ua-mobile] => ?0
    [User-Agent] => Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36
    [Sec-Fetch-Site] => same-origin
    [Sec-Fetch-Mode] => cors
    [Sec-Fetch-Dest] => empty
    [Referer] => https://china.cheonyu.com/product/view.html?qIDX=55413
    [Accept-Encoding] => gzip, deflate, br
    [Accept-Language] => ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7
    [Cookie] => _ga=GA1.1.1171520800.1613635259; 421f3aa67b14f0aef550c43224e4769c=cjRuYjU1cGlmZHRnMGQxajVtNGNxbWEzZjE%3D; _ga_BR95V94ZZN=GS1.1.1625569915.68.1.1625569978.0; PHPSESSID=qspnpn4e6f66mtdt4slr7dsj11
    [If-Modified-Since] => Thu, 08 Jul 2021 06:13:35 GMT
)
*/

$inIDX = mysqli_real_escape_string($g5['connect_db'], $qIDX);

if(!$inIDX) exit;

$sql = " SELECT Pcontent FROM 2011_productInfo WHERE IDX = '" . $inIDX . "' AND Pshop='" . $shopID . "' AND Pdeleted=0 ";
$rs = sql_fetch($sql);
$dbPcontent = $rs['Pcontent'];

if($dbPcontent) {
	$dbPcontent = str_replace("embed ","embed wmode=\"transparent\" ",$dbPcontent);
	$dbPcontent = str_replace("EMBED ","EMBED wmode=\"transparent\" ",$dbPcontent);
	$dbPcontent = str_replace("src=\"/_DATA","src=\"https://www.cheonyu.com/_DATA",$dbPcontent);
	$dbPcontent = str_replace("1000u.net","cheonyu.com",$dbPcontent);
	$dbPcontent = str_replace('src=', 'loading="lazy" src=', $dbPcontent);
	echo $dbPcontent;
}
?>