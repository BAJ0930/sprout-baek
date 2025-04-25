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
		$sql .= " MKboxCount = '" . $inMKboxCount . "' , ";
		$sql .= " MKboxCount2 = '" . $inMKboxCount2 . "' , ";
		$sql .= " MKdiscount = '" . $_POST["dis0"] . "' , ";
		for($k=10;$k<=120;$k=$k+10)
		{
			$sql.=" MKdiscount" . $k . "='" . $_POST["dis" . $k] . "'";
			if($k<120)$sql.=", ";
		}
		$sql.=" where IDX='" . $qIDX . "'";
		sql_query($sql);
		
		#== 상품 마진 변경
		
		$sql = "update 2011_productInfo set ";
		$sql .= " PboxCount = '" . $inMKboxCount . "' , ";
		$sql .= " PboxCount2 = '" . $inMKboxCount2 . "' , ";
		$sql .= " Pdiscount = '" . $_POST["dis0"] . "' , ";		
		for($k=10;$k<=120;$k=$k+10)
		{
			$sql.=" Pdiscount" . $k . "='" . $_POST["dis" . $k] . "'";
			if($k<120)$sql.=", ";
		}
		//$sql.=" where BRIDX in (select IDX from 2011_brandInfo where MKIDX='" . $qIDX . "')  and Pshop='" . $masterSeller . "'";
		$sql.=" where MKIDX = '" . $qIDX . "' and Pshop='" . $masterSeller . "' and Pdeleted = 0";		
		
		sql_query($sql);
		
		echo "##OK##";
	}
?>