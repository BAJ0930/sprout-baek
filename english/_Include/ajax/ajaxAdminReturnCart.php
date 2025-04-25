<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($stepCode=="cartSet")
	{
		/*
			$dbIDX ## $dbPIDX ## $dbORPoption ## $dbMID ## $dbOIDX
			pIDX + "|" + pPIDX + "|" + pOPTION + "|" + pMID + "|" + pOIDX + "|" + pCount2;
			IDX=' + idx + '&mode=edit&inReturn1=' + inReturn1 + '&inReturn2=' + inReturn2  + '&PIDX=' + pidx + '&OPIDX=' + opidx + '&inMID=' + inmid + '&OIDX=' + oidx + '&RETURNPAY=' + inReturnPay;
		*/
		$groupCode = time();

		$dataArray1 = explode("##",$dataStr1);	
		$pLen1 = count($dataArray1);
		
		$dataArray2 = explode("##",$dataStr2);
		$pLen2 = count($dataArray2);
			
		for($k=0;$k<$pLen1;$k++)
		{
			//반품 시작

			$Arr1 = explode("|",$dataArray1[$k]);
			if(!$Arr1[0]) continue;
			$IDX = $Arr1[0];
			$PIDX = $Arr1[1];
			$OPIDX = $Arr1[2];
			$inMID = $Arr1[3];
			$OIDX = $Arr1[4];
			$inReturn1 = $Arr1[5];
			$PRICE = $Arr1[6];

				
			if($OPIDX)
			{
				#-- 기존 재고 확인
				$sql = "select OPstock from 2011_productOption where IDX='" . $OPIDX . "'";
				$rs = sql_fetch($sql);
				$nStock = $rs['OPstock'];	
				
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
				$nStock = $rs['PstockCount'];	
				
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
			//$kind1 = $inReturn1."^".date("Y-m-d H:i:s");
			sql_query( " UPDATE 2011_orderProduct SET ORPRGROUPCODE = '$groupCode' WHERE IDX = '$IDX' " );
			sql_query ( " INSERT INTO 2011_orderReturn VALUES ('', '$OIDX', '$PIDX', '$OPIDX', '1', '$inReturn1', '$PRICE', '$MID', '$groupCode', now(), '1', '') " );


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

			//반품 끝
		}
		
		for($k=0;$k<$pLen2;$k++)
		{
			$Arr2 = explode("|",$dataArray2[$k]);
			if(!$Arr2[0]) continue;
			$IDX = $Arr2[0];
			$PIDX = $Arr2[1];
			$OPIDX = $Arr2[2];
			$inMID = $Arr2[3];
			$OIDX = $Arr2[4];
			$inReturn2 = $Arr2[5];
			$PRICE = $Arr2[6];

				
			if($OPIDX)
			{				
				#-- 기존 재고 확인
				$sql = "select OPstock from 2011_productOption where IDX='" . $OPIDX . "'";
				$rs = sql_fetch($sql);
				$nStock = $rs['OPstock'];	
				
				$OBJtype = "OPSTOCK";
				$OBJIDX = $OPIDX;
			}
			else
			{				
				#-- 기존 재고 확인
				$sql = "select PstockCount from 2011_productInfo where IDX='" . $PIDX . "'";
				$rs = sql_fetch($sql);
				$nStock = $rs['PstockCount'];	
				
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
			sql_query( " UPDATE 2011_orderProduct SET ORPRGROUPCODE = '$groupCode' WHERE IDX = '$IDX' " );
			sql_query ( " INSERT INTO 2011_orderReturn VALUES ('', '$OIDX', '$PIDX', '$OPIDX', '2', '$inReturn2', '$PRICE', '$MID', '$groupCode', now(), '1', '') " );

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

		sql_query(" INSERT INTO 2011_orderReturnDelivery VALUES ('', '$groupCode', '$dPay', '1', '', '') ");
		
		echo "##SaveOK##";
	}
?>
