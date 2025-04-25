<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
?>
<div style="float:left;width:738px; height:70px; background-color:#f7f7f7; margin-bottom:10px; text-align:center; line-height:5px; padding-top:10px;">
<p style="font-size:16px; color:#d50c0c;">DHL Weight / Regional Fee</p>
<p style="font-size:11px;">Area <b><?=$code;?></b> Shipping charge per weight</p>
</div>
<table>
<colgroup>
	<col width="25%" /> <col width="25%" /> <col width="25%" /> <col width="*" />
</colgroup>
<thead>
<tr>
	<th scope="row">Weight (Kg)</th>
	<th scope="row">Estimated Shipping Fee</th>
	<th scope="row">Weight (Kg)</th>
	<th scope="row">Estimated Shipping Fee</th>
</tr>
</thead>
<tbody>
<?
	$query = sql_query(" SELECT EPCODE, EPWEIGHT, EPPRICE FROM nDHLPay WHERE EPCODE = '" . $code ."' ORDER BY EPIDX ASC ");
	while($data = sql_fetch_array($query)){
		$ArrWeight[] = $data['EPWEIGHT'];
		$ArrPay[] = $data['EPPRICE'] * 1.1;
	}

	for($i = 0; $i < 30; $i ++){
		$k = $i + 30;
		$Weight1 = $ArrWeight[$i];
		$Weight2 = $ArrWeight[$k];

		$Pay1 = $ArrPay[$i];
		$Pay2 = $ArrPay[$k];
		
		$Pay1 = number_format($Pay1 / $shopConfig['CFperDollar'],2,'.','');
		$Pay2 = number_format($Pay2 / $shopConfig['CFperDollar'],2,'.','');

		if($Weight2 == "35") $Weight2 = "30 over";
?>
<tr>
	<td><?=$Weight1;?></td>
	<td>$ <?=$Pay1;?></td>
	<td><?=$Weight2;?></td>
	<td>
		<? 
			if($k == 61) echo "Contact us";
			else echo "$ ".$Pay2;
		?>
	</td>
</tr>
<? } ?>
</tbody>
</table>
