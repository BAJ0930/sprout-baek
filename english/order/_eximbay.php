<?php
/*
[테스트 환경]
호출 URL: https://api-test.eximbay.com
mid: 1849705C64
API Key: test_1849705C642C217E0B2D
 
[운영 환경]
호출 URL: https://api.eximbay.com
mid: 24C2E64DE0
API Key: live_24C2E64DE033A9259741
*/

$order_id	= htmlspecialchars(trim(strip_tags($_POST['order_id'])));
$amount		= htmlspecialchars(trim(strip_tags($_POST['amount']))); 
$name		= htmlspecialchars(trim(strip_tags($_POST['name'])));
$email		= htmlspecialchars(trim(strip_tags($_POST['email'])));

$apiKey = base64_encode('live_24C2E64DE033A9259741:');
$FIELDS = array(
	'payment'	=> [
					'transaction_type' => "AUTHORIZE",
					'order_id' => "{$order_id}",
					'currency' => "USD",
					'amount' => "{$amount}",
					'lang' => "EN"
				],
	'merchant'	=> [
					'mid' => '24C2E64DE0'
				],
	'buyer'		=> [
					'name' => '{$name}',
					'email' => '{$email}',
				],
	'url'		=> [
					'return_url' => 'https://english.cheonyu.com/order/eximReturn.php',
					'status_url' => 'https://english.cheonyu.com/order/eximStatus.php'
				]
);
			
$post_data = json_encode($FIELDS);
$opts = array(
	CURLOPT_URL => 'https://api.eximbay.com/v1/payments/ready',
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $post_data,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => array("Content-Type: application/json", "Authorization: Basic {$apiKey}"),
);

$CURL = curl_init();
curl_setopt_array($CURL, $opts);
$response = curl_exec($CURL);
curl_close($CURL);

echo $response;
?>