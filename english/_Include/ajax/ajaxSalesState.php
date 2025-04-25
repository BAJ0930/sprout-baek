<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	if($qIDX){

		$query = " SELECT * FROM 2011_memberInfo WHERE IDX = '$qIDX' AND Mdeleted = 0 ";
		$result = sql_query($query);
		if(mysqli_num_rows($result)){
			$query = " UPDATE 2011_memberInfo SET MDI = '$qSTR' WHERE IDX = '$qIDX' AND Mdeleted = 0 ";
			$result = sql_query($query);
			echo "##OK##";
			exit;
		} else {
			echo "##ERROR##";
			exit;
		}

	} else {
		echo "##ERROR##";
		exit;
	}
	
?>
