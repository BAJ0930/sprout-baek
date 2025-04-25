<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($smsNum=="010-7592-1114")
	{
		echo "XXXXXXXXXX error XXXXXXXXXX";
		exit();
	}

	if(!$smsMsg)
	{
		echo "error";
		exit();
	}
	
	
	if($smsType==1)
	{	
		$smsNumberArray = explode("|",$smsNum);
		
		$smsNumberResult="";
		
		for($k=0;$k<sizeof($smsNumberArray);$k++)
		{
			//-- 한번더 형식 검사
			$n = $smsNumberArray[$k];
			if($n)
			{			
				$notSMSnumber=0;
				//-- Check 1. 총 연락처 수가 12 자리 혹은 13자리어야만 한다.				
				if(!(strlen($n)==12 || strlen($n)==13))$notSMSnumber=1;
				
				//-- Check 2. "-" 문자를 기준으로 3등분 되어야 한다.
				$tmp = explode("-",$n);
				if(sizeof($tmp)!=3)$notSMSnumber=1;
				
				//-- Check 3. 숫자외의 값이 있는지 체크
				if(!is_numeric($tmp[0]) || !is_numeric($tmp[1]) || !is_numeric($tmp[2]))$notSMSnumber=1;
				
				if($notSMSnumber==0)$smsNumberResult.= $n . ",";
				
			}//-- end if
			
		}//-- end for
		
		if(substr($smsNumberResult,strlen($smsNumberResult)-1)==",")$smsNumberResult=substr($smsNumberResult,0,strlen($smsNumberResult)-1);
		
		//echo $smsNumberResult;
		
		//-- 문자 발송
		//$result = smsSend($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
		$result = smsSendNew($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
		
	}
	else
	{
		/*=============================================================
				
		===============================================================*/
		
		
		if(strpos($smsGroup,"smsGroup11")!==false)$smsGroup="All";
		else if(strpos($smsGroup,"smsGroup10")!==false)$smsGroup="Pserson";
		else
		{
			$smsGroupArray = explode("|",$smsGroup);		
		}
		
		
		/*======================================================================
		SMS 발송 목록 가져올 쿼리 만들기
		======================================================================*/
		$where = " (left(`MHP`,3)='010' or left(`MHP`,3)='016' or left(`MHP`,3)='017' or left(`MHP`,3)='018' or left(`MHP`,3)='019' or left(`MHP`,3)='011')";
		
		if($smsGroup=="All")
		{
			$sql = "Select MHP from 2011_memberInfo where " . $where;
		}
		else if($smsGroup=="Pserson")
		{
			$sql = "Select MHP from 2011_memberInfo where Mlevel=1 and " . $where;
		}
		else
		{
			$addWhere="";
			for($k=0;$k<sizeof($smsGroupArray);$k++)
			{
				if($addWhere && $k<sizeof($smsGroupArray)-1)$addWhere.=" or ";
				if($smsGroupArray[$k])$addWhere.= " Mlevel='" . (round(str_replace("smsGroup","",$smsGroupArray[$k]))*10) . "'";
			}
			
			$sql = "Select MHP from 2011_memberInfo where (" . $addWhere . ") and " . $where;			
		}
		
		$result = sql_query($sql);
		
		$smsNumberResult = "";
		
		//-- 리스트 생성
		$cutCnt=500;
		$nCnt=0;
		while($rs = sql_fetch_array($result)){
			$nCnt++;
			//-- 잘들어와있다는 가정하에 추가 검사는 하지 않겠음. 어차피 형식이 잘못된 번호는 호스팅 시스템에서 발송되지 않음.
			$smsNumberResult.=$rs["MHP"] . ",";
			
			if($nCnt%$cutCnt==0)
			{
				//-- $cutCnt 단위로 한번씩 잘라서 문자 보내기  (호스팅 시스템 상 1회에 동시 1000건이상 불가일 것으로 보여짐)				
				
				//-- 마지막 쉼표 삭제
				if(substr($smsNumberResult,strlen($smsNumberResult)-1)==",")$smsNumberResult=substr($smsNumberResult,0,strlen($smsNumberResult)-1);
				
				//-- 문자 발송
				#@smsSend($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
				@smsSendNew($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
				
				//-- 한번 발송한 번호는 비워주기
				$smsNumberResult = "";
			}			
		}
		
		if($smsNumberResult)
		{
			//-- 마지막 쉼표 삭제
			if(substr($smsNumberResult,strlen($smsNumberResult)-1)==",")$smsNumberResult=substr($smsNumberResult,0,strlen($smsNumberResult)-1);
			//-- 문자 발송
			//$result = smsSend($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
			$result = smsSendNew($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
		}
		

		
		
	}//-- smsType if End	
	
	echo $result;
	
	//echo "success";
?>