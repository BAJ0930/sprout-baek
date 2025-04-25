<?
	if(@$_POST["isMode"]=="totalAdmin")$isAdminMode=1;
	else $isManagerMode=1;
	
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	$MID = $_SESSION[ADMIN_ID];
	
	if($mode=="edit")
	{
		$query = " SELECT * FROM 2011_productInfo WHERE IDX = '$IDX' AND Pdeleted = 0 ";
		if(sql_fetch($query)){

			$query1 = " UPDATE 2011_productInfo SET ";
			$query1 .= " Pdiscount = '". $inPdiscount ."', ";
			$query1 .= " Pdiscount10 = '". $inPdiscount10 ."', ";
			$query1 .= " Pdiscount20 = '". $inPdiscount20 ."', ";
			$query1 .= " Pdiscount30 = '". $inPdiscount30 ."', ";
			$query1 .= " Pdiscount40 = '". $inPdiscount40 ."', ";
			$query1 .= " Pdiscount50 = '". $inPdiscount50 ."', ";
			$query1 .= " Pdiscount60 = '". $inPdiscount60 ."', ";
			$query1 .= " Pdiscount70 = '". $inPdiscount70 ."', ";
			$query1 .= " Pdiscount80 = '". $inPdiscount80 ."', ";
			$query1 .= " Pdiscount90 = '". $inPdiscount90 ."', ";
			$query1 .= " Pdiscount100 = '". $inPdiscount100 ."', ";
			$query1 .= " Pdiscount110 = '". $inPdiscount110 ."', ";
			$query1 .= " Pdiscount120 = '". $inPdiscount120 ."', ";
			$query1 .= " PboxCount = '". $inPboxCount ."', ";
			$query1 .= " PboxCount2 = '". $inPboxCount2 ."', ";
			$query1 .= " Pprice1 = '". $inPprice1 ."', ";
			$query1 .= " Pprice2 = '". $inPprice2 ."', ";
			$query1 .= " Pprice3 = '". $inPprice3 ."' ";
			$query1 .= " WHERE IDX = '$IDX'  AND Pdeleted = 0 ";
			sql_query($query1);

			echo "##OK##";
		} else {
			echo "##error##";
		}
		
	}
	else
	{
		echo "##error##";
	}
?>
