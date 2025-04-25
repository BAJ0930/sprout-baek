<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	if($qIDX)
	{
		$sql = " SELECT * FROM 2011_orderInfo WHERE IDX='" . $qIDX . "' AND MID = '" . $MID . "' AND Odeleted=0";
		$data = sql_fetch($sql);
		if(!$data['IDX']){
			echo "##ERROR##";
			exit;
		}
		$sql = "update 2011_orderInfo set OcancelType='" . $Ctype ."', OcancelReason='" . $Creason . "',OcancelTime='" . date(time()) . "',Odeleted=1 where IDX='" . $qIDX . "'";
		sql_query($sql);
		
		#========= 상품 재고 다시 업데이트 (발송된 상품 취소시 재고 다시 업데이트)  ============================================
		$sql = " select OIDX,PIDX,ORPcount,ORPoption from 2011_orderProduct where OIDX='" . $qIDX . "' and ORPdeleted=0 ";
		$result = sql_query($sql);
		#-- 처리
		while($rs = sql_fetch_array($result))
		{
				#-- 제품 정보 저장
			$PIDX = $rs['PIDX'];
			$ORPcount = $rs['ORPcount'];
			$opIDX = $rs['ORPoption'];
			
			//-- 재고 복구  1) 제품 자체 재고   2) 옵션 재고
			//sql_query("update 2011_productInfo set PstockCount=PstockCount + " . $ORPcount . " where IDX='" . $PIDX . "'");

			//if($opIDX) sql_query("update 2011_productOption set OPstock=OPstock + " . $ORPcount . " where IDX='" . $opIDX . "'");
		}
		#========= 상품 재고 다시 업데이트 끝 ============================================
		
		#-- 판매 상품 내역 삭제
		$sql = sql_query("update 2011_orderProduct set ORPdeleted=1 where OIDX='" . $qIDX . "'");
		
		//-- 상품권 사용 내역 삭제
		$sql = "update 2011_memberGift set GFdeleted=1 where OIDX='" .  $qIDX . "'";
		sql_query($sql);
		
		//-- Point 사용 내역 삭제
		$sql = "update 2011_memberPoint set POdeleted=1 where OIDX='" . $qIDX . "'";
		sql_query($sql);
		
		//-- 쿠폰 사용 내역 삭제
		$sql = "update 2011_couponList set CPuseDate=0,CPuseMID='',CPuseOIDX='0' where CPuseOIDX='" . $qIDX . "'";
		sql_query($sql);
		
		echo "##OK##" . $qIDX;	
		exit();
	}
	else
	{
		echo "##error##";
		exit();
	}
?>


