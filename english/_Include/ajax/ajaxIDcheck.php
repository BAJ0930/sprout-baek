<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	$idCheck = strtolower($inKeyword);
	if($idCheck=="admin" || $idCheck=="master")
	{
		echo "##used##";
		exit();
	}
	
	//=== 문자 체크 =========================
	if(strlen($inKeyword) < 2 || strlen($inKeyword)>12)
	{
		echo "##lengthError##";
		exit();
	}
	
	// 첫글자 영문체크
	if(!preg_match("/[a-zA-Z]/",$inKeyword[0]))
	{
		echo "##firstCharError##";
		exit();
	}
	
	// 특문 포함 체크
	if(preg_match("/[!#$&%^&*\'\"()?+=\/]/",$inKeyword))
	{
		echo "##charError##";
		exit();
	}
	
	#-- 일반 사용자쪽 검토
	$sql = "select * from 2011_memberInfo where MID='" . $inKeyword . "'";
	$result = sql_query($sql);
	if($rs=mysqli_num_rows($result))
	{
		echo "##used##";
	}
	else
	{
		#-- 판매자쪽 검토	
		$sql = "select * from 2011_makerSeller where MSID='" . $inKeyword . "'";
		$result = sql_query($sql);
		if($rs=mysqli_num_rows($result))
		{
			echo "##used##";
		}
		else
		{
			echo "##OK##";
		}
	}
	
?>
