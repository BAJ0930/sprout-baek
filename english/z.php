<?php
exit;


function getEmsAmount($ems_option, $country, $weight, $length, $width, $height){
	/*
		$ems_option : ems / emspremium
		보안키 771865f1cc0217f4
		인증키 771865f1cc0217f491680673121058
		고객번호  0005099258
		getEmsAmount('ems', 'US', 1000, 10, 10, 10);
	*/
	$premiumcd = ($ems_option == "ems") ? "31" : "32";
	$data = array(
		'regkey' => '771865f1cc0217f491680673121058',	//인증키
		'premiumcd' => $premiumcd,	// 국제우편물 구분코드
		'em_ee' => 'em',			// 국제우편물 종류코드
		'boyn' => 'N',				// 보험가입 여부
		'boprc' => '0',				// 보험가입금액 KRW
		'apprno' => '60051C0062',	// 계약승인번호
		'countrycd' => $country,	// 도착 국가코드
		'totweight' => $weight * 1000,		// 우편물 총중량 g
		'boxlength' => $length,		// 포장상자 세로길이 cm
		'boxwidth' => $width,		// 포장상자 가로길이 cm
		'boxheight' => $height		// 포장상자 높이 cm
	);

	$url = "https://eship.epost.go.kr/api.EmsTotProcCmd.ems" . "?" . http_build_query($data, '', );
	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	curl_close($ch);
	
	$xml = simplexml_load_string($response);
	$price = trim($xml->EmsTotProcCmd->emsTotProc);
	return ($price) ? $price : 0;
}

		$price = getEmsAmount('ems', 'AU', 33.96, 2, 27, 2);
		$price_pre = getEmsAmount('emspremium', 'AU', 33.96, 2, 27, 2);
		
		
		print_r($price);
		echo "<br>";
		print_r($price_pre);
?>