<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	if($inCAstate==6)
	{
		#-- 이전 바로구매 임시저장값 삭제
		$sql = "delete from 2011_cartInfo where MID='" . $uid . "' and CAstate=" . $inCAstate;
		sql_query($sql);
	}
	else
	{
		
		#-- 일반 담기일시 장바구니 함께 담기 불가한 제품이 존재하는지 체크
		$sql = "select a.IDX from 2011_cartInfo as a left join 2011_productInfo as b on a.PIDX=b.IDX where b.PcartWith=2 and a.MID='" . $uid . "' and a.PIDX<>'" . $qIDX . "' and a.CAstate=" . $inCAstate;
		if($result = sql_fetch($sql))
		{
			echo "##noWith##";
			exit();
		}
		else
		{
			#-- 해당 제품이 함께 담을 수 없는 제품인지 검사
			$sql = "select PcartWith from 2011_productInfo where IDX='" . $qIDX . "'";
			$result = sql_query($sql);
			if($rs=sql_fetch_array($result))
			{
				if($rs['PcartWith']==2)
				{
					$sql = "select count(IDX) AS cnt from 2011_cartInfo where MID='" . $uid . "' and CAstate=" . $inCAstate;
					$rs = sql_fetch($sql);
					if($rs['cnt'])
					{
						echo "##noWithThis##";
						exit();
					}
				}
			}
			
		}
	}
	
	for($k=0;$k<sizeof($opIDX);$k++)
	{
		
		#=== 텍스트 옵션 추가. 옵션명을 추가하면 좋겠지만 옵션생성이 귀찮아 지므로 패스하겠음. 텍스트 입력이 있을때에는 상품명으로만 중복 체크
		//-- 중복상품 검사
		if($opTEXT)$sql = "select * from 2011_cartInfo where MID='" . $uid . "' and PIDX='" . $qIDX . "' and CAstate=" . $inCAstate;
		else $sql = "select * from 2011_cartInfo where MID='" . $uid . "' and OPIDX='" . $opIDX[$k] . "' and PIDX='" . $qIDX . "' and CAstate=" . $inCAstate;
	
		if($rs=sql_fetch($sql))
		{	
			//-- 중복 확인 (수량 추가)
			if($opTEXT)$sql = "update 2011_cartInfo set CAcount=" . $opCount[$k] . ",CAtext='" . $opTEXT . "' where IDX='" . $rs["IDX"]  . "'";
			else $sql = "update 2011_cartInfo set CAcount=CAcount+" . $opCount[$k] . " where IDX='" . $rs["IDX"]  . "'";			
			sql_query($sql);
		}
		else
		{
			//-- 신규 추가
			$sql = "insert into 2011_cartInfo (MID,PIDX,OPIDX,CAcount,CAtext,CAaddPrice,CAstate,";
			if($inCAstate==6)$sql.="CAcheck,CAorder,";
			$sql.="CAregdate) values(";
			$sql.= "'" . $uid . "',";
			$sql.= "'" . $qIDX . "',";
			$sql.= "'" . $opIDX[$k] . "',";
			$sql.= "'" . $opCount[$k] . "',";
			$sql.= "'" . $opTEXT . "',";
			$sql.= "'" . $opAddprice[$k] . "',";
			$sql.= "'" . $inCAstate . "',";
			if($inCAstate==6)$sql.="'1','1',";
			$sql.= "'" . date(time()) . "')";
			sql_query($sql);
		}		
	}
	
	if($inCAstate==6)
	{
		#-- 바로구매
		echo "##ORDERGO##";
	}
	else
	{
		#-- 일반 카트 저장
		echo "##OK##";
	}
?>