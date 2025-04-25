<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($qIDX)
	{

		$query = " SELECT * FROM 2011_orderReturnDelivery WHERE DRIDX = '$qIDX' AND RDSTATUS != '1' ";
		$result = sql_query($query);
		if(mysqli_num_rows($result)){
			echo "##ERR1##";
			exit;
		}
		
		$query3 = " SELECT * FROM 2011_orderReturn WHERE GROUPCODE = '$qIDX' ";
		$result3 = sql_query($query3);
		while($data3 = sql_fetch_array($result3)){

			$OPIDX = $data3[OPIDX];
			$PIDX = $data3[PIDX];
			$inReturn1 = $data3[RCOUNT];
			$inReturn2 = $data3[RCOUNT];
			$OIDX = $data3[OIDX];
			
			/*	반품 */
			if($data3[KIND] == 1) {
				
				if($OPIDX > 0)
				{
					#-- 기존 재고 확인
					$sql = "select OPstock from 2011_productOption where IDX='" . $OPIDX . "'";
					$rs = sql_fetch($sql);
					$nStock = $rs['OPstock'];	
					
					$sql = "update 2011_productOption set OPstock=OPstock-" . $inReturn1 . " where IDX='" . $OPIDX . "'";
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
				$sql = "update 2011_productInfo set PstockCount=PstockCount-" . $inReturn1 . " where IDX='" . $PIDX ."'";
				sql_query($sql);	

				#-- 판매수량 차감하기
				$sql = "update 2011_productInfo set PsellCount=PsellCount+" . $inReturn1 . " where IDX='" . $PIDX . "'";
				sql_query($sql);

				if($OPIDX)
				{
					$sql = "update 2011_productOption set OPsellCount=OPsellCount+" . $inReturn1 . " where IDX='" . $OPIDX . "'";
					sql_query($sql);
				}
				sql_query ( " UPDATE 2011_orderProduct SET ORPRGROUPCODE = '' WHERE IDX = '$OIDX' " );				
				sql_query ( " DELETE FROM 2011_orderReturn WHERE RIDX = '$data3[RIDX]' ");
				sql_query ( " DELETE FROM 2011_LogStock WHERE LSreason = '반품' AND LSOIDX = '$data3[OIDX]' AND LScount  = '$data3[RCOUNT]' ");


			/* 불량반품 */
			} else if($data3[KIND] == 2){

				if($OPIDX)
				{				
					#-- 기존 재고 확인
					$sql = "select OPstock from 2011_productOption where IDX='" . $OPIDX . "'";
					$rs = sql_fetch($sql);
					$nStock = $rs['OPstock'];	
				}
				else
				{				
					#-- 기존 재고 확인
					$sql = "select PstockCount from 2011_productInfo where IDX='" . $PIDX . "'";
					$rs = sql_fetch($sql);
					$nStock = $rs['PstockCount'];	
				}
						
				#-- 판매수량 차감하기
				$sql = "update 2011_productInfo set PsellCount=PsellCount+" . $inReturn2 . " where IDX='" . $PIDX . "'";
				sql_query($sql);			
				
				if($OPIDX)
				{
					$sql = "update 2011_productOption set OPsellCount=OPsellCount+" . $inReturn2 . " where IDX='" . $OPIDX . "'";
					sql_query($sql);
				}
				sql_query( " UPDATE 2011_orderProduct SET ORPRGROUPCODE = '' WHERE IDX = '$OIDX' " );
				sql_query ( " DELETE FROM 2011_orderReturn WHERE RIDX = '$data3[RIDX]' ");
				sql_query ( " DELETE FROM 2011_LogStock WHERE LSreason = '불량반품' AND LSOIDX = '$data3[OIDX]' AND LScount  = '$data3[RCOUNT]' ");

			}

		}

		echo "##OK##";
		exit;
	}
	else
	{
		echo "##error##";
		exit;
	}


?>
