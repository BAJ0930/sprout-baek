<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($mode=="get")
	{
		#-- 사용하지 않습니다. 해당 항목이 checkDate 로 변경되었습니다.
		$sql = "update 2011_adminCart set ACAend='" . date(time()) . "',ACAendUser='" . $MID . "',ACAendUserName='" . $Mname . "' where ACAMKIDX='" . $MKIDX . "' and ACAGROUP='" . $GROUPCODE . "'";
		sql_query($sql);
		$returnDate = date("Y-m-d");
	}
	else if($mode=="check")
	{
		$sql = "update 2011_adminCart set ACAend2='" . date(time()) . "',ACAendUser2='" . $MID . "',ACAendUserName2='" . $Mname . "' where ACAMKIDX='" . $MKIDX . "' and ACAGROUP='" . $GROUPCODE . "'";
		sql_query($sql);
		$returnDate = date("Y-m-d");
	}
	else if($mode=="input")
	{
		$sql = "update 2011_adminCart set ACAend3='" . date(time()) . "',ACAendUser3='" . $MID . "',ACAendUserName3='" . $Mname . "' where ACAMKIDX='" . $MKIDX . "' and ACAGROUP='" . $GROUPCODE . "'";
		sql_query($sql);
		$returnDate = date("Y-m-d");
	}
	else if($mode=="checkDate")
	{
		$sql = "update 2011_adminCart set ACAend='" . date(time()) . "',ACAendUser='" . $MID . "',ACAendUserName='" . $Mname . "',ACAcheckSendDate='" . $checkDate . "' where ACAMKIDX='" . $MKIDX . "' and ACAGROUP='" . $GROUPCODE . "'";
		sql_query($sql);
		$returnDate = $checkDate;
	
		list($year, $month, $day) = explode('-', $checkDate);
		$wdate =  mktime(0, 0, 0, $month, $day, $year);

		$query = " SELECT * FROM 2011_adminCart WHERE ACAGROUP='" . $GROUPCODE . "'";
		$result = sql_query($query);
		while($data = sql_fetch_array($result)){
			sql_query( " UPDATE 2011_productInfo SET PstockDate = '$wdate' WHERE IDX = '$data[ACAPIDX]' AND Pdeleted = 0 " );
		}
	}	

	echo "##OK##" . $mode . "##" . $MKIDX . "##" . $GROUPCODE . "##" . $Mname . "<br>" . $returnDate;

?>