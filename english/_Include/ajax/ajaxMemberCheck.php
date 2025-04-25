<?
	
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	echo $mode;
	
	if($mode=="Joined")
	{
		//$sql = "select * from 2011_memberInfo where Mname='" . $inMname . "' and Mjumin='" . $inMjumin . "'";
		#-- 탈퇴해도 재가입 가능
		$sql = "select * from 2011_memberInfo where Mjumin='" . $inMjumin . "' and Moutdate=0 and Mdeleted=0";
		if($rs=sql_fetch($sql))
		{
			//-- 중복
			echo "##used##";
			exit();
		}
		else
		{
			//-- 가능
			echo "##OK##";
			exit();
		}

	}
	else if($mode=="DuplicateID")
	{
		
		if($inMid=="admin" || $inMid=="master" || $inMid=="webmaster")
		{
			echo "##used##";
			exit();
		}
		
		
		$sql = "select * from 2011_memberInfo where MID='" . $inMid . "'";
		if($rs=sql_fetch($sql))
		{
			//-- 중복
			echo "##used##";
			exit();
		}
		else
		{
			//-- 가능
			echo "##OK##";
			exit();
		}
	}

?>