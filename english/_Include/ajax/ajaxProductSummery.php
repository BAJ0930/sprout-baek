<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

if($mode=="info")
{
	//-- 최근 30일
	$day1 =date(time()) - (60*60*24*30);
	$day2 =date(time());

	if($PIDX)
	{
			#-- 제품 기본정보 (일단 사진 + 기본 재고만 가져오기)		
			$sql = "select * from 2011_productInfo where IDX='" . $PIDX . "'";
			$rs = sql_fetch($sql);
			$PSTOCK = $rs["PstockCount"];
			$PIMAGE = $rs["PsaveFile1"];
		
		if($OPIDX)
		{
			#-- 옵션은 재고 다시 구함
			$sql = "select * from 2011_productOption where IDX='" . $OPIDX . "'";
			$rs = sql_fetch($sql);
			$PSTOCK = $rs["OPstock"];
			
			#-- 최근 30일 판매
			$sql ="	select PIDX,ORPoption,sum(ORPcount) as ORPcount from 2011_orderProduct as a left join 2011_orderInfo as b on a.OIDX = b.IDX ";
			$sql.=" where ORPdeleted=0 and ORPcountCheck<>1 and PIDX='" . $PIDX . "' and ORPoption='" . $OPIDX . "'";
			$sql.=" and Oregdate>=" . $day1 . " ";
			$sql.=" and Oregdate<" . $day2 . " ";
			$sql.= " group by PIDX,ORPoption ";
			$rs = sql_fetch($sql);
			$ORPcount1 = $rs["ORPcount"];
			
			#-- 전체 누적 판매
			$sql ="	select PIDX,ORPoption,sum(ORPcount) as ORPcount from 2011_orderProduct as a left join 2011_orderInfo as b on a.OIDX = b.IDX ";
			$sql.=" where ORPdeleted=0 and ORPcountCheck<>1 and PIDX='" . $PIDX . "' and ORPoption='" . $OPIDX . "'";
			$sql.= " group by PIDX,ORPoption ";
			$rs = sql_fetch($sql);
			$ORPcount2 = $rs["ORPcount"];
			
			#-- 발송대기
			$sql ="	select PIDX,ORPoption,sum(ORPcount) as ORPcount from 2011_orderProduct as a left join 2011_orderInfo as b on a.OIDX = b.IDX ";
			$sql.=" where ORPdeleted=0 and ORPcountCheck=1 and PIDX='" . $PIDX . "' and ORPoption='" . $OPIDX . "'";
			$sql.= " group by PIDX,ORPoption ";
			$rs = sql_fetch($sql);
			$ORPready = $rs["ORPcount"];
		}
		else
		{
			#-- 최근 30일 판매
			$sql ="	select PIDX,ORPoption,sum(ORPcount) as ORPcount from 2011_orderProduct as a left join 2011_orderInfo as b on a.OIDX = b.IDX ";
			$sql.=" where ORPdeleted=0 and ORPcountCheck<>1 and PIDX='" . $PIDX . "' ";
			$sql.=" and Oregdate>=" . $day1 . " ";
			$sql.=" and Oregdate<" . $day2 . " ";
			$sql.= " group by PIDX ";
			$rs = sql_fetch($sql);
			$ORPcount1 = $rs["ORPcount"];
			
			#-- 전체 누적 판매
			$sql ="	select PIDX,ORPoption,sum(ORPcount) as ORPcount from 2011_orderProduct as a left join 2011_orderInfo as b on a.OIDX = b.IDX ";
			$sql.=" where ORPdeleted=0 and ORPcountCheck<>1 and PIDX='" . $PIDX . "' ";
			$sql.= " group by PIDX ";
			$rs = sql_fetch($sql);
			$ORPcount2 = $rs["ORPcount"];
			
			#-- 발송대기
			$sql ="	select PIDX,ORPoption,sum(ORPcount) as ORPcount from 2011_orderProduct as a left join 2011_orderInfo as b on a.OIDX = b.IDX ";
			$sql.=" where ORPdeleted=0 and ORPcountCheck=1 and PIDX='" . $PIDX . "' ";
			$sql.= " group by PIDX,ORPoption ";
			$rs = sql_fetch($sql);
			$ORPready = $rs["ORPcount"];
			
		}
		if(!$ORPcount1)$ORPcount1=0;
		if(!$ORPcount2)$ORPcount2=0;
		if(!$ORPready)$ORPready=0;
		
		echo $sql."##OK##" . $PIMAGE . "##" . $PSTOCK . "##" . $ORPready . "##" . $ORPcount1 . "##" . $ORPcount2;
	}
	else
	{
		echo "#error#";
	}
}
else if($mode=="check")
{
	$sql = "update 2011_adminCart set ACAmemCheck" . $checkNum . "=1 where ACAMKIDX='" . $qMKIDX . "' and ACAgroup='" . $qGRP . "'";
	sql_query($sql);
	echo "##OK##";
}
?>