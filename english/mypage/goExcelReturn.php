<?
	session_start();

	#-- ���ϸ� ��� ����
	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=OrderReturn_" . date("YmdHis") . ".xls"); 
	header("Content-Description: PHP4 Generated Data"); 
	header("Content-charset=euc-kr"); 

	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";

	ob_clean();

	$query = urldecode($_POST[pQuery]);
	$result = sql_query($query);
?>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr' />

  <Table border="1">
    <tr>
    <td>��ǰ ������</td>
    <td>�ֹ���ȣ</td>
    <td>������</td>
    <td>��ǰ��</td>
    <td>��ǰ����</td>
    <td>�ҷ���ǰ����</td>
	<td>�ѻ�ǰ�ݾ�</td>
	<td>��ۺ�</td>
	<td>�����ݾ�</td>
	<td>����Ʈ���꿩��</td>
    </tr> 
<?
	$MID = $_SESSION[USER_ID];
	while($rs=sql_fetch_array($result)){
				
		# DB ���ڵ� ����� ��� ������ ��ȯ
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
	
		$query2 = " SELECT a.*,b.Ocode,b.Oname FROM 2011_orderReturn a LEFT JOIN 2011_orderInfo b ON a.OIDX = b.IDX WHERE b.MID = '$MID' AND GROUPCODE = '$dbGROUPCODE'";
		$result2 = sql_query($query2);
		$cnt = mysqli_num_rows($result2);
		$k = 0;
		while($data = sql_fetch_array($result2)){
			$k ++;


			$query3 = " SELECT * FROM `2011_orderProduct` WHERE OIDX = '$data[OIDX]' AND PIDX = '$data[PIDX]' AND ORPoption = '$data[OPIDX]' ";
			$data3 = sql_fetch($query3);
			
			if($k == 1)	{
				$de = sql_fetch_array(" SELECT * FROM 2011_orderReturnDelivery WHERE DRIDX = '$dbGROUPCODE' ");
				if($de[AMOUNT] == "") $de[AMOUNT] = 0;
				$tAmount = $dbTPRICE + $de[AMOUNT];
				
				if($de[RDSTATUS]==1)
				{
					$css="color:red;";
					$GFconstate="X";
				}
				else if($de[RDSTATUS]==2)
				{
					$css="color:blue;";
					$GFconstate="O";
				}
				else if($de[RDSTATUS]==3)
				{
					$css="color:green;";
					$GFconstate="C";
				} else if($de[RDSTATUS] == 4){
					$css="color:green;";
					$GFconstate="��������";
				}
			}
?>
	 <tr>
		<? if($k == 1){ ?><td rowspan="<?=$cnt?>"><?=substr($dbRDATE,2,8)?></td><? } ?>
		<td><?=$data[Ocode]?></td>
		<td><?=iconv("UTF-8","EUC-KR", $data["Oname"]);?></td>
		<td><?=iconv("UTF-8","EUC-KR", $data3["ORPname"]);?> <? if($data3["ORPoptionValue"]){ ?> (<?=iconv("UTF-8","EUC-KR", $data3["ORPoptionName"]);?>-<?=iconv("UTF-8","EUC-KR", $data3["ORPoptionValue"]);?>)<? } ?></td>
		<td><? if($data[KIND] == 1) { echo $data[RCOUNT]; } ?></td>
		<td><? if($data[KIND] == 2) { echo $data[RCOUNT]; } ?></td>
		<td><?=number_format($data[PRICE] * $data[RCOUNT]);?></td>
		<? if($k == 1){ ?><td rowspan="<?=$cnt?>"><?=number_format($de[AMOUNT])?></td><? } ?>
		<? if($k == 1){ ?><td rowspan="<?=$cnt?>"><?=number_format($tAmount)?></td><? } ?>
		<? if($k == 1){ ?><td rowspan="<?=$cnt?>"><?=$GFconstate?></td><? } ?>
	  </tr>
<? 
		}
	}
?>

</Table>