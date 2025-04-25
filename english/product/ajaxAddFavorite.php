<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	if($mode=="delete"){
		$qIDX = explode(",",$qIDX);
		for($k=0;$k<sizeof($qIDX);$k++){	
			$sql = "delete from 2011_favoriteInfo where IDX='" . $qIDX[$k] . "'";
			sql_query($sql);
		}
		echo $sql . "##OK" . $inFAtype . "##";	
		exit();
	} else {	
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
