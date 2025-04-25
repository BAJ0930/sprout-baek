<?
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

$uid = htmlspecialchars(trim($uid));
$qIDX = htmlspecialchars(trim($qIDX));
$inFAtype = htmlspecialchars(trim($inFAtype));
$mode = htmlspecialchars(trim($qMode));

$inFAtype = 1;

if(!$uid){
	echo "##NOLOGIN##";
	exit;
}

if($mode=="delete"){
	$sql = "DELETE FROM 2011_favoriteInfo WHERE FAIDX = '" . $qIDX . "' AND MID = '" . $uid . "'";
	sql_query($sql);
	echo "##DELETED##";
}
if($mode=="add"){
	//-- 중복 검사
	$sql = "select * from 2011_favoriteInfo where MID='" . $uid . "' and FAtype='" . $inFAtype . "' and FAIDX='" . $qIDX . "'";
	if(sql_fetch($sql)) {
		//-- 이미 담겨져 있음
		echo "##overlap##";
		exit();
	} else {
		//-- 신규 등록
		$sql = "insert into 2011_favoriteInfo (MID,FAtype,FAIDX,Fregdate) values(";
		$sql.= "'" . $uid . "',";
		$sql.= "'" . $inFAtype . "',";
		$sql.= "'" . $qIDX . "',";
		$sql.= "'" . date(time()) . "')";
		sql_query($sql);
		if($inFAtype==1)echo "##productOK##";
		else if($inFAtype==2)echo "##brandOK##";
		exit();
	}
}	
?>
