<?
	session_start();

	#-- 파일명 헤더 지정
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=Order_" . date("YmdHis") . ".xls"); 
	header("Content-Description: PHP4 Generated Data"); 
	header("Content-charset=euc-kr"); 

	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";

	ob_clean();

	$delCheck = $_POST[delCheck];
	$or = "";
	for($i = 0; $i < count($delCheck); $i ++){
		if($i == 0) $or = "";
		else $or = " OR ";
		$wh .= $or . " a.IDX = '$delCheck[$i]' ";
	}
	
?>
  <Table border="1">
    <tr>
    <td>주문번호</td>
    <td>수령자</td>
    <td>운송장번호</td>
    <td>주소</td>
    <td>휴대폰</td>
    <td>전화번호</td>
    </tr> 
<?
	$query = "SELECT a.*, d.* FROM 2011_orderInfo as a left join 2011_orderDeliveryInfo as d ON a.IDX = d.OIDX WHERE MID='".$_SESSION[NOWMID]."' and a.Odeleted=0 and ( " . $wh . " ) ORDER BY a.IDX DESC";
	$result = sql_query($query);
	while($data = sql_fetch_array($result)){		
?>
    <tr>
    <td><?=$data[Ocode]?></td>
    <td><?=$data[Oname]?></td>
    <td style='mso-number-format:"\@";' ><?=$data[ODcode]?></td>
    <td>(<?=$data[Opost]?>) <?=$data[Oaddr1]?> <?=$data[Oaddr2]?></td>
    <td><?=$data[Ohp]?></td>
    <td><?=$data[Otel]?></td>
    </tr>
<?
	}
?>
</Table>