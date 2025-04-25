<?php
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST,GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function returnJson($code,$message,$extra = []){
    return array_merge([
        'code'		=> $code,
        'message'	=> $message
    ],$extra);
	exit;
}

$vIDX = htmlspecialchars(trim($vIDX));

$rs = sql_fetch(" SELECT ESNAME FROM nDHL WHERE EIDX = '{$vIDX}' ");
$countryCode = $rs['ESNAME'];

if($countryCode == 'CN') $countryCode = "C2";

$result = sql_query(" SELECT * FROM nEMSPayPal WHERE COUNTRY_CD = '{$countryCode}' ");
while($rs = sql_fetch_array($result)){
	$datas[] = array(
		'COUNTRY_CD'	=> $rs['IDX'],
		'PROVINCE'		=> $rs['PROVINCE'],
		'PROVINCE_CD'	=> $rs['PROVINCE_CD'],
	);
}

if(count($datas) > 0) {
		
	$returnData = [
		'code' => 200,
		'message' => 'Success',
		'datas' => $datas
	];

} else {

	$returnData = [
		'code' => 200,
		'message' => 'No Data',
		'datas' => $datas
	];

}

echo json_encode($returnData, JSON_PRETTY_PRINT);