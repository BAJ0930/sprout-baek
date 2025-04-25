<?php
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

$inMid = mysqli_real_escape_string($g5['connect_db'], $inMid);	

if($mode=="DuplicateID")
{
	
	if($inMid=="admin" || $inMid=="master" || $inMid=="webmaster" || $inMid == "system")
	{
		echo "##used##";
		exit();
	}
			
	$sql = "select IDX from 2011_memberInfo where MID='" . $inMid . "'";
	if($rs=sql_fetch($sql))
	{
		echo "##used##";
		exit();
	}
	else
	{
		echo "##OK##";
		exit();
	}
}

?>