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
			//-- �ѹ��� ���� �˻�
			$n = $smsNumberArray[$k];
			if($n)
			{			
				$notSMSnumber=0;
				//-- Check 1. �� ����ó ���� 12 �ڸ� Ȥ�� 13�ڸ���߸� �Ѵ�.				
				if(!(strlen($n)==12 || strlen($n)==13))$notSMSnumber=1;
				
				//-- Check 2. "-" ���ڸ� �������� 3��� �Ǿ�� �Ѵ�.
				$tmp = explode("-",$n);
				if(sizeof($tmp)!=3)$notSMSnumber=1;
				
				//-- Check 3. ���ڿ��� ���� �ִ��� üũ
				if(!is_numeric($tmp[0]) || !is_numeric($tmp[1]) || !is_numeric($tmp[2]))$notSMSnumber=1;
				
				if($notSMSnumber==0)$smsNumberResult.= $n . ",";
				
			}//-- end if
			
		}//-- end for
		
		if(substr($smsNumberResult,strlen($smsNumberResult)-1)==",")$smsNumberResult=substr($smsNumberResult,0,strlen($smsNumberResult)-1);
		
		//echo $smsNumberResult;
		
		//-- ���� �߼�
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
		SMS �߼� ��� ������ ���� �����
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
		
		//-- ����Ʈ ����
		$cutCnt=500;
		$nCnt=0;
		while($rs = sql_fetch_array($result)){
			$nCnt++;
			//-- �ߵ����ִٴ� �����Ͽ� �߰� �˻�� ���� �ʰ���. ������ ������ �߸��� ��ȣ�� ȣ���� �ý��ۿ��� �߼۵��� ����.
			$smsNumberResult.=$rs["MHP"] . ",";
			
			if($nCnt%$cutCnt==0)
			{
				//-- $cutCnt ������ �ѹ��� �߶� ���� ������  (ȣ���� �ý��� �� 1ȸ�� ���� 1000���̻� �Ұ��� ������ ������)				
				
				//-- ������ ��ǥ ����
				if(substr($smsNumberResult,strlen($smsNumberResult)-1)==",")$smsNumberResult=substr($smsNumberResult,0,strlen($smsNumberResult)-1);
				
				//-- ���� �߼�
				#@smsSend($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
				@smsSendNew($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
				
				//-- �ѹ� �߼��� ��ȣ�� ����ֱ�
				$smsNumberResult = "";
			}			
		}
		
		if($smsNumberResult)
		{
			//-- ������ ��ǥ ����
			if(substr($smsNumberResult,strlen($smsNumberResult)-1)==",")$smsNumberResult=substr($smsNumberResult,0,strlen($smsNumberResult)-1);
			//-- ���� �߼�
			//$result = smsSend($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
			$result = smsSendNew($smsNumberResult,$smsMsg,$sphone,$smsGroup,"ADMINSend");
		}
		

		
		
	}//-- smsType if End	
	
	echo $result;
	
	//echo "success";
?>