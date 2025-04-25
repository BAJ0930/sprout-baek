<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($qIDX)
	{
			
		$query = " SELECT * FROM `2011_orderReturn` WHERE `GROUPCODE` = '$qIDX' ";
		$result = sql_query($query);

		$z = 0;
		while($data = sql_fetch_array($result)){
			
			$z ++;

			$query2 = " SELECT Oname FROM 2011_orderInfo WHERE IDX = '$data[OIDX]' ";
			$data2 = sql_fetch($query2);

			if($z == 1) $zum = "";
			else $zum = ",";

			$txt .= $zum.$data2[Oname];

		}
	
		if($kind == 2){
			
			sql_query( " UPDATE 2011_orderReturnDelivery SET RDSTATUS = '4', RDMID = '$_SESSION[__ADMIN_NAME___]', RDWDATE = now() WHERE  DRIDX = '$qIDX' ");
			echo "##OK##2##";

		} else {

			$inContent = date("Y-m-d",$qIDX)." 반품건 (".$txt.")";
			$inPoint = $money;
			$stateCode = 1;
			#-- 포인트 내역 추가
			$sql = "insert into 2011_memberPoint (MID,PIDX,OIDX,POpoint,POcount,POcontent,POtype,POstate,POregdate,POregnm) values(";
			$sql.= "'" . $dbMID  . "',";
			$sql.= "'',";
			$sql.= "'',";
			$sql.= "'" . $inPoint  . "',";
			$sql.= "'1',";
			$sql.= "'" . $inContent  . "',";
			$sql.= "'" . $stateCode . "',";
			$sql.= "'2',";
			$sql.= "'" . date(time()) ."',";
			$sql.= "'" . $_SESSION[__ADMIN_NAME___] . "')";
			sql_query($sql);

			sql_query( " UPDATE 2011_orderReturnDelivery SET RDSTATUS = '2', RDMID = '$_SESSION[__ADMIN_NAME___]', RDWDATE = now() WHERE  DRIDX = '$qIDX' ");
			echo "##OK##1##";
		
		}
	}
	else
	{
		echo "##error##";
	}


?>
