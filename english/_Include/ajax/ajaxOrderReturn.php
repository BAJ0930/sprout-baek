<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	$nStock=0;

//=== 입고 로그 기록 =====================
	if($inReturn1) {
		
		if($OPIDX)
		{

			#-- 기존 재고 확인
			$sql = "select OPstock from 2011_productOption where IDX='" . $OPIDX . "'";
			$rs = sql_fetch($sql);
			$nStock = $rs[OPstock];	
			
			$sql = "update 2011_productOption set OPstock=OPstock+" . $inReturn1 . " where IDX='" . $OPIDX . "'";
			sql_query($sql);			
			
			$OBJtype = "OPSTOCK";
			$OBJIDX = $OPIDX;
					
		}
		else
		{
			
			#-- 기존 재고 확인
			$sql = "select PstockCount from 2011_productInfo where IDX='" . $PIDX . "'";
			$rs = sql_fetch($sql);
			$nStock = $rs[PstockCount];	
			
			$OBJtype = "PSTOCK";
			$OBJIDX = $PIDX;

		}
		

		$sql = "update 2011_productInfo set PstockCount=PstockCount+" . $inReturn1 . " where IDX='" . $PIDX ."'";
		sql_query($sql);	
		

		#-- 판매수량 차감하기
		$sql = "update 2011_productInfo set PsellCount=PsellCount-" . $inReturn1 . " where IDX='" . $PIDX . "'";
		sql_query($sql);

		if($OPIDX)
		{
			$sql = "update 2011_productOption set OPsellCount=OPsellCount-" . $inReturn1 . " where IDX='" . $OPIDX . "'";
			sql_query($sql);
		}
		$kind1 = $inReturn1."^".date("Y-m-d H:i:s");
		sql_query( " UPDATE 2011_orderProduct SET ORPRcount1 = '$kind1', ORPRID = '$MID' , ORPRDATE = now(), ORPRPAY = '$RETURNPAY' WHERE IDX = '$IDX' " );


		$LSreason = "반품";
		$newStock = $nStock + $inReturn1;

		$sql = "insert into 2011_LogStock (PIDX,OPIDX,MID,LSOIDX,LScount,LSstock1,LSstock2,LSreason,LSregdate) values(";
		$sql.= "'" . $PIDX . "',";
		$sql.= "'" . $OPIDX . "',";
		$sql.= "'" . $MID . "',";
		$sql.= "'" . $OIDX . "',";
		$sql.= "'" . $inReturn1 . "',";
		$sql.= "'" . $nStock . "',";
		$sql.= "'" . $newStock . "',";
		$sql.= "'" . $LSreason . "',";
		$sql.= "'" . date(time()) . "')";
		
		sql_query($sql);
	}



	if($inReturn2){

		if($OPIDX)
		{
			
			#-- 기존 재고 확인
			$sql = "select OPstock from 2011_productOption where IDX='" . $OPIDX . "'";
			$rs = sql_fetch($sql);
			$nStock = $rs[OPstock];	
			
			$OBJtype = "OPSTOCK";
			$OBJIDX = $OPIDX;

		}
		else
		{
			
			#-- 기존 재고 확인
			$sql = "select PstockCount from 2011_productInfo where IDX='" . $PIDX . "'";
			$rs = sql_fetch($sql);
			$nStock = $rs[PstockCount];	
			
			$OBJtype = "PSTOCK";
			$OBJIDX = $PIDX;

		}
				
		#-- 판매수량 차감하기
		$sql = "update 2011_productInfo set PsellCount=PsellCount-" . $inReturn2 . " where IDX='" . $PIDX . "'";
		sql_query($sql);
		
		
		if($OPIDX)
		{
			$sql = "update 2011_productOption set OPsellCount=OPsellCount-" . $inReturn2 . " where IDX='" . $OPIDX . "'";
			sql_query($sql);
		}
		$kind2 = "";
		$kind2 = $inReturn2."^".date("Y-m-d H:i:s");
		sql_query( " UPDATE 2011_orderProduct SET ORPRcount2 =  '$kind2' ,  ORPRID = '$MID' , ORPRDATE = now(), ORPRPAY = '$RETURNPAY'  WHERE IDX = '$IDX' " );

		$LSreason = "불량반품";
		$newStock = $nStock;

		$sql = "insert into 2011_LogStock (PIDX,OPIDX,MID,LSOIDX,LScount,LSstock1,LSstock2,LSreason,LSregdate) values(";
		$sql.= "'" . $PIDX . "',";
		$sql.= "'" . $OPIDX . "',";
		$sql.= "'" . $MID . "',";
		$sql.= "'" . $OIDX . "',";
		$sql.= "'" . $inReturn2 . "',";
		$sql.= "'" . $nStock . "',";
		$sql.= "'" . $newStock . "',";
		$sql.= "'" . $LSreason . "',";
		$sql.= "'" . date(time()) . "')";
		
		sql_query($sql);
	}

	
	#-- 상품 재고 업데이트 결과 출력
	//echo "##StockSaveOK##" . $OBJtype . "##" . $OBJIDX . "##" . $OBJvalue . "##";
	echo "##OK##" . $OBJtype . "##" . $OBJIDX . "##" . $inStock . "##" . $inStockType;
	exit();
?>
