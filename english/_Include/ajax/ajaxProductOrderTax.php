<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	$MNAME = fnGetAdminName($MID);
	
	if($stepCode=="ACCOUNT")	//정산상태
	{
		if($ACABDATE){
			$addACABDATE = " , ACABDATE = '$ACABDATE' ";
		} else {
			$addACABDATE = "";
		}

		$query = " UPDATE 2011_adminCart SET ACAaccount = 1, ACAaccountMID = '$MID' $addACABDATE WHERE ACAMKIDX = '$MKIDX' AND ACAgroup = '$ACAgroup' ";
		$result = sql_query($query);

		
		echo "##ACCOUNTOK##".$MNAME;
		//echo "##StateSaveOK##" . $PIDX . "##" . $newState . "##";
	} 
	else if($stepCode=="TAX")	//세금계산서
	{
		$query = " UPDATE 2011_adminCart SET ACAtax = 1, ACAtaxMID = '$MID' WHERE ACAMKIDX = '$MKIDX' AND ACAgroup = '$ACAgroup' ";
		$result = sql_query($query);
		
		echo "##TAXOK##".$MNAME;
		//echo "##StateSaveOK##" . $PIDX . "##" . $newState . "##";
	} 
	else if($stepCode=="BUY")	//거래구분
	{
		$query = " UPDATE 2011_adminCart SET ACAbuying = '$OPT' WHERE ACAMKIDX = '$MKIDX' AND ACAgroup = '$ACAgroup' ";
		$result = sql_query($query);

		if($OPT == 0) $txt = "사입";
		else $txt = "위탁";
		
		echo "##BUYOK##".$txt;
		//echo "##StateSaveOK##" . $PIDX . "##" . $newState . "##";
	} 
	else if($stepCode=="DELIVER")	//발송
	{
		$query = " UPDATE 2011_adminCart SET ACAdeliver = '$OPT' WHERE ACAMKIDX = '$MKIDX' AND ACAgroup = '$ACAgroup' ";
		$result = sql_query($query);
		
		if($OPT == 0) $txt = "천유";
		else if($OPT == 1) $txt = "직배송";
		else $txt = "업체배송";

		echo "##DELIVEROK##".$txt;
		//echo "##StateSaveOK##" . $PIDX . "##" . $newState . "##";
	}

?>
