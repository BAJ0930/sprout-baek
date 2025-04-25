<?php
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

if($mode == "shipping"){

	$sql = " SELECT * FROM `nEMS` WHERE EIDX = '" . $countryVal ."' ";
	$data = sql_fetch($sql);
	$esName = $data['ESName'];

	if($countryWeight < 1.1){

		$price = getEmsAmount('ems', $esName, $countryWeight, 2, 27, 2);
		$price_pre = getEmsAmount('emspremium', $esName, $countryWeight, 2, 27, 2);
		$boxes = 1;
		
		/* 2024-12-09 요금 수정 */
		if($esName == 'US'){
			$price = "45000";
			$price_pre = "45000";
		}

	} else {

		if($cBoxVol_1 > $countryWeight){

			$price = getEmsAmount('ems', $esName, $countryWeight, 40, 31, 22);
			$price_pre = getEmsAmount('emspremium', $esName, $countryWeight, 40, 31, 22);
			$boxes = floor($countryWeight / $cBoxVol_1);
			
		} else {
		
			if($cBoxVol_3 > $countryWeight){
				$price = getEmsAmount('ems', $esName, $countryWeight, 48, 35, 34);
				$price_pre = getEmsAmount('emspremium', $esName, $countryWeight, 48, 35, 34);
				$boxes = floor($countryWeight / $cBoxVol_3);
			} else {
				$price = getEmsAmount('ems', $esName, $countryWeight, 47, 63, 35);
				$price_pre = getEmsAmount('emspremium', $esName, $countryWeight, 47, 63, 35);
				$boxes = floor($countryWeight / $cBoxVol_7);
			}
			
			
		}

	}

	if($boxes == 0) $boxes = 1;
	
	$kr = 0;
	if($price && $price_pre) {

		$feeDollar = ($price < $price_pre) ? number_format((($price * 0.9) / $shopConfig['CFperDollar']) * $boxes,2,'.','') : number_format((($price_pre * 0.9) / $shopConfig['CFperDollar']) * $boxes,2,'.','');

	} else if($price && !$price_pre){

		$feeDollar = number_format((($price * 0.9) / $shopConfig['CFperDollar']) * $boxes,2,'.','');

	} else if(!$price && $price_pre){

		$feeDollar = number_format((($price_pre * 0.9) / $shopConfig['CFperDollar']) * $boxes,2,'.','');

	} else {

		$kr = 1;

	}

	echo ($kr == 1) ? "##KOREA##" : "##OK##".$esName."##".$countryWeight."##".$feeDollar."##".$boxes."##".$price."##".$price_pre."##".$cBoxVol_1."##".$cBoxVol_3;
	
} else {
	echo "##ERROR##";
}
?>