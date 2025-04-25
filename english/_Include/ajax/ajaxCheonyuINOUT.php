<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	$wdate = date("Y-m-d");
	$mktime = time();
	
	$query = " SELECT * FROM memberINOUT WHERE CMID = '$MID' AND CDATE = '$wdate' ";
	$data = sql_fetch($query);

	if($data['NO']){
		
		if($qMODE == "IN" && $data[TIMEIN]){
			echo "##OK##ALREADYIN";
			exit;
		} else if($qMODE == "IN" && $data[TIMEOUT] == ""){
			$query = " UPDATE memberINOUT SET TIMEIN = '$mktime' WHERE CMID = '$MID' AND CDATE = '$wdate' ";
			$result = sql_query($query);
			echo "##OK##IN##";
			exit;
		}
		if($qMODE == "OUT" && $data[TIMEOUT]){
			echo "##OK##ALREADYOUT";
			exit;
		} else if($qMODE == "OUT" && $data[TIMEOUT] == ""){
			$query = " UPDATE memberINOUT SET TIMEOUT = '$mktime' WHERE CMID = '$MID' AND CDATE = '$wdate' ";
			$result = sql_query($query);
			echo "##OK##OUT##";
			exit;
		}

	} else {

		if($qMODE == "IN"){
			$query = " INSERT INTO memberINOUT VALUES ('', '$MID', '$wdate', '$mktime', '', '', '', now()) ";
			$result = sql_query($query);
			echo "##OK##IN##";
			exit;
		} else if($qMODE == "OUT"){
			$query = " INSERT INTO memberINOUT VALUES ('', '$MID', '$wdate', '', '$mktime', '', '', now()) ";
			$result = sql_query($query);
			echo "##OK##OUT##";
			exit;
		}

	}
?>
