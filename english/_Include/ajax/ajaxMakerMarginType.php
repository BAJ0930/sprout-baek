<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($mode=="productCheck")
	{
		//$sql = "select count(a.IDX) from 2011_productInfo as a left join 2011_brandInfo as b on a.BRIDX=b.IDX  where b.MKIDX='" . $qIDX . "' and  Pshop='" . $masterSeller . "' ";
		$sql = "select count(a.IDX) as cnt from 2011_productInfo as a where a.MKIDX='" . $qIDX . "' and  Pshop='" . $masterSeller . "' and Pdeleted = 0";
		$rs = sql_fetch($sql);
		echo "##OK##" . $rs['cnt'];
	}
	else if($mode=="setMargin")
	{
		
		#== 제조사 마진도 변경
		$sql = "update 2011_makerInfo set ";

		if($_POST["kind"] == "SU") {
			$sql .= " MKboxCount = '" . $inMKboxCount . "' , ";
			$sql .= " MKboxCount2 = '" . $inMKboxCount2 . "'  ";
		}

		if($_POST["kind"] == "Z") {
			$sql .= " MKdiscount = '" . $_POST["dis0"] . "'  ";
		}

		if($_POST["kind"] == "A"){
			$sql .= " MKdiscount10='" . $_POST["dis10"] . "' , ";
			$sql .= " MKdiscount20='" . $_POST["dis20"] . "' , ";
			$sql .= " MKdiscount30='" . $_POST["dis30"] . "'  ";
		}
		
		if($_POST["kind"] == "B"){
			$sql .= " MKdiscount70='" . $_POST["dis70"] . "' , ";
			$sql .= " MKdiscount80='" . $_POST["dis80"] . "' , ";
			$sql .= " MKdiscount90='" . $_POST["dis90"] . "'  ";
		}
		
		if($_POST["kind"] == "C"){
			$sql .= " MKdiscount40='" . $_POST["dis40"] . "' , ";
			$sql .= " MKdiscount50='" . $_POST["dis50"] . "' , ";
			$sql .= " MKdiscount60='" . $_POST["dis60"] . "'  ";
		}
		
		if($_POST["kind"] == "D"){
			$sql .= " MKdiscount100='" . $_POST["dis100"] . "' , ";
			$sql .= " MKdiscount110='" . $_POST["dis110"] . "' , ";
			$sql .= " MKdiscount120='" . $_POST["dis120"] . "'  ";
		}
		$sql .= " where IDX='" . $qIDX . "'";
		sql_query($sql);
		
		#== 상품 마진 변경
		
		$sql = "update 2011_productInfo set ";
		
		if($_POST["kind"] == "SU") {
			$sql .= " PboxCount = '" . $inMKboxCount . "' , ";
			$sql .= " PboxCount2 = '" . $inMKboxCount2 . "'  ";
		}

		if($_POST["kind"] == "Z") {
			$sql .= " Pdiscount = '" . $_POST["dis0"] . "' ";
		}		

		if($_POST["kind"] == "A"){
			$sql .= " Pdiscount10='" . $_POST["dis10"] . "' , ";
			$sql .= " Pdiscount20='" . $_POST["dis20"] . "' , ";
			$sql .= " Pdiscount30='" . $_POST["dis30"] . "'  ";
		}
		
		if($_POST["kind"] == "B"){
			$sql .= " Pdiscount70='" . $_POST["dis70"] . "' , ";
			$sql .= " Pdiscount80='" . $_POST["dis80"] . "' , ";
			$sql .= " Pdiscount90='" . $_POST["dis90"] . "'  ";
		}
		
		if($_POST["kind"] == "C"){
			$sql .= " Pdiscount40='" . $_POST["dis40"] . "' , ";
			$sql .= " Pdiscount50='" . $_POST["dis50"] . "' , ";
			$sql .= " Pdiscount60='" . $_POST["dis60"] . "'  ";
		}
		
		if($_POST["kind"] == "D"){
			$sql .= " Pdiscount100='" . $_POST["dis100"] . "' , ";
			$sql .= " Pdiscount110='" . $_POST["dis110"] . "' , ";
			$sql .= " Pdiscount120='" . $_POST["dis120"] . "'  ";
		}
		$sql .= " where MKIDX = '" . $qIDX . "' and Pshop='" . $masterSeller . "'";		
		sql_query($sql);
		
		echo "##OK##";
	}
?>