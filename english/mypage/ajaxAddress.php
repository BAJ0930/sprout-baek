<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($mode=="AddOK")
	{
		$sql = "insert into 2011_memberAddr (MID,MAdefault,MAname,MAuser,MAtel,MAhp,MApost,MAaddr1,MAaddr2) values(";
		$sql.= "'" . $MID . "',";
		$sql.= "'1',";
		$sql.= "'" . $inAname . "',";
		$sql.= "'" . $inAuser . "',";
		$sql.= "'" . $inAtel . "',";
		$sql.= "'" . $inAhp . "',";
		$sql.= "'" . $inApost . "',";
		$sql.= "'" . $inAaddr1 . "',";
		$sql.= "'" . $inAaddr2 . "')";		
		sql_query($sql);
		
		echo $mode . "##OK##";
	}
	else if($mode=="EditOK")
	{
		if($qIDX=="Minfo")
		{
			//-- 회원정보 수정			
			$sql = "update 2011_memberInfo set ";
			$sql.="MTEL='" . $inAtel . "',";
			$sql.="MHP='" . $inAhp . "',";
			$sql.="Mpost='" . $inApost . "',";
			$sql.="Maddr1='" . $inAaddr1 . "',";
			$sql.="Maddr2='" . $inAaddr2 . "' ";
			$sql.=" where MID='" . $MID . "'";			
			
		} else {
			//-- 일반 배송지 수정
			$sql = "update 2011_memberAddr set ";
			$sql.= "MAname='" . $inAname . "',";
			$sql.= "MAuser ='" . $inAuser . "',";
			$sql.= "MAtel ='" . $inAtel . "',";
			$sql.= "MAhp ='" . $inAhp . "',";
			$sql.= "MApost ='" . $inApost . "',";
			$sql.= "MAaddr1 ='" . $inAaddr1 . "',";
			$sql.= "MAaddr2 ='" . $inAaddr2 . "' ";
			$sql.= " where IDX='" . $qIDX . "'";
		}		
		sql_query($sql);
		echo $mode . "##OK##" . $sql;
	}
	else if($mode=="DeleteOK")
	{
		
		$sql = "delete from 2011_memberAddr where IDX='" . $qIDX . "'";
		sql_query($sql);		
		echo $mode . "##OK##";
	}	
	else if($mode=="setDefault")
	{
		$sql = "update 2011_memberAddr set MAdefault=1 where MID='" . $MID . "'";
		sql_query($sql);
		
		if($qIDX=="Minfo"){		
		} else {
			$sql = "update 2011_memberAddr set MAdefault=2 where IDX='" . $qIDX . "'";
			sql_query($sql);
		}
		
		echo $mode . "##OK##";
	}	
?>