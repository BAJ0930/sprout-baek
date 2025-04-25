<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($mode=="productCheck")
	{
		//$sql = "select count(a.IDX) from 2011_productInfo as a left join 2011_brandInfo as b on a.BRIDX=b.IDX  where b.MKIDX='" . $qIDX . "' and  Pshop='" . $masterSeller . "' ";
		$sql = "select count(a.IDX) as cnt from 2011_productInfo as a where a.MKIDX='" . $qIDX . "' and  Pshop='" . $masterSeller . "' ";
		$rs = sql_fetch($sql);
		echo "##OK##" . $rs['cnt'];
	}
	else if($mode=="setMargin")
	{
		
		#== 제조사 마진도 변경
		$sql = "update 2011_makerInfo set MKbuyMargin = '$inMKbuyMargin'";
		$sql.=" where IDX='" . $qIDX . "'";
		sql_query($sql);
		
		#== 상품 마진 변경
		
		$sql = "select * from 2011_productInfo as a where a.MKIDX='" . $qIDX . "' and  Pshop='" . $masterSeller . "' ";
		$result = sql_query($sql);
		while($rs = sql_fetch_array($result)){
			
			$v3 = ceil($rs["Pprice2"] / 100 * $inMKbuyMargin);

			$sql2 = " update 2011_productInfo set Pprice1 = '".$v3."' ";
			$sql2.=" where IDX = '" . $rs['IDX'] . "' ";					

			sql_query($sql2);

		}
	
		echo "##OK##";
	}
?>