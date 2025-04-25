<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	if(!$stepCode)
	{
		/*== 제품목록 저장 ========================*/
		$idxArray = explode(",",$inIDX);
		$l1 = sizeof($idxArray);
		
		if($inCount)
		{
			$countArray = explode(",",$inCount);
			$l2 = sizeof($countArray);
		}		
		
		//-- IDX 와 제품 수량도 함께들어왔으나~ 두개의 갯수가 매치가 되지않으면 에러
		if($l2)
		{
			if($l1!=$l2)
			{
				echo "##error##";
				exit();
			}
		}				
		
		//-- 코드 생성
		$cartCode = date(time());
		for($k=0;$k<sizeof($idxArray);$k++)
		{
			if($l2) $count = $countArray[$k];
			else $count=1;

			$sql = " DELETE FROM 2011_cartInfo WHERE MID = '$uid' AND PIDX = '$idxArray[$k]' AND CAstate = '0' AND CAcheck = '0' AND CAorder = '0' ";
			sql_query($sql);
			
			//-- 일단 밀어넣기
			$sql = "insert into 2011_cartInfo (MID,PIDX,OPIDX,CAcount,CAaddPrice,CAstate,CAregdate) values(";
			$sql.= "'" . $uid . "',";
			$sql.= "'" . $idxArray[$k] . "',";
			$sql.= "'0',";
			$sql.= "'" . $count . "',";	//-- 장바구니 담은 상품 수량
			$sql.= "'0',";
			$sql.= "'0',";	//-- stateCode   0 : Ready   /   1 : Complete
			$sql.= "'" . date(time()) . "')";
			sql_query($sql);
		}
		echo "##step2##" . $cartCode . "##";
	}
	else if($stepCode==2)
	{
		if($_Minus == 1) {
			echo "##step4##" . $cartCode . "##";
		} else {
			//-- 각 제품들 재고 확인
			$sql = "select * from 2011_cartInfo as a left join 2011_productInfo as b on a.PIDX=b.IDX where MID='" . $uid . "' and a.CAstate=0 and b.PstockCount<1 and b.PorderMinus != '1' and CAregdate='" . $cartCode . "'";
			$result = sql_query($sql);
			
			if(mysqli_num_rows($result))
			{		
				//-- 재고 부족한 제품 발견시 3단계로 진행
				echo "##step3##" . $cartCode . "##";
			}
			else
			{
				//-- 재고 부족한 제품이 없다면 바로 옵션 확인 단계로 진행
				echo "##step4##" . $cartCode . "##";
			}
		}
	}
	else if($stepCode==3)
	{
		//-- 재고파악은 cartStock.html 페이지에서 처리합니다.
	}
	else if($stepCode==4)
	{
		//--- 옵션 처리 확인 쿼리
		$sql = "select * from 2011_cartInfo as a left join 2011_productInfo as b on a.PIDX=b.IDX where MID='" . $uid . "' and a.OPIDX=0 and a.CAregdate='" . $cartCode . "'";
		/*	and b.PoptionUse=2	*/
		$result = sql_query($sql);
		if(mysqli_num_rows($result))
		{
			//-- 옵션 처리할 상품이 있다면 5단계 처리 필요
			echo "##step5##" . $cartCode . "##";
		}
		else
		{
			//-- (종료)옵션 처리할 상품이 없다면
			echo "##step6##" . $cartCode . "##";
		}
	}
	else if($stepCode==5)
	{
		//-- 5단계 옵션 선택은 cartOption.html 페이지에서 처리됩니다.
	}
	else if($stepCode==6)
	{		
		//-- 설정한 옵션 저장 처리
		
		if($addStr)
		{
			//-- 넘어온 자료 1차 분할
			$pInfo = explode("##",$addStr);
			for($k=0;$k<sizeof($pInfo);$k++)
			{
				//-- 자료 2차 분할
				$tmp = explode("@@",$pInfo[$k]);
				
				$CAidx = $tmp[0];
				$OPinfo = explode("|",$tmp[1]);
				//-- 제품 정보 구하기
				$sql = "select * from 2011_cartInfo where IDX='" . $CAidx . "'";
				$caResult=sql_query($sql);
				
				if($caRs = sql_fetch_array($caResult))
				{
					//-- 선택한 옵션의 횟수만큰 돌지만, 처음에는 기존 장바구니를 업데이트, 나머지는 새로 insert 해준다.	
					for($j=0;$j<sizeof($OPinfo);$j++)
					{
						$OPtemp = explode(",",$OPinfo[$j]);
						
						$OPidx = $OPtemp[0];
						$OPcount = $OPtemp[1];
						
						//-- 업데이트 실행
						if($j==0)
						{
							$sql = "update 2011_cartInfo set OPIDX='" . $OPidx . "',CAcount='" . $OPcount . "' where IDX='" . $caRs["IDX"] . "'";
						}
						else
						{
							$sql = "insert into 2011_cartInfo (MID,PIDX,OPIDX ,CAcount ,CAaddPrice,CAstate,CAregdate) values(";
							$sql.="'" . $caRs["MID"] . "',";
							$sql.="'" . $caRs["PIDX"] . "',";
							$sql.="'" . $OPidx . "',";
							$sql.="'" . $OPcount . "',";
							$sql.="'0',";
							$sql.="'" . $caRs["CAstate"] . "',";
							$sql.="'" . $caRs["CAregdate"] . "')";
						}//-- end if
						@sql_query($sql);
					}//-- end for
				}//-- end if
				
				
			}//-- end for
		}//-- end if
		
		
		/*===============================================================================================
		장바구니 대기상품들 중복여부 확인 후 다르게 처리 (서브쿼리가 된다면 더 간단해질텐데;;)
		================================================================================================*/
		$sql = "SELECT a.*,b.IDX as CAIDX2  FROM `2011_cartInfo` as a left join 2011_cartInfo as b on a.PIDX=b.PIDX and a.OPIDX=b.OPIDX and a.MID=b.MID and a.CAstate=0 and b.CAstate=1 where a.MID='" . $uid . "' and a.CAstate=0 and a.CAregdate='" . $cartCode . "'";
		echo $sql;
		$result = sql_query($sql);
		while($rs = sql_fetch_array($result))
		{
			//-- 변수화
			$CAIDX = $rs['IDX'];
			$CAIDX2 = $rs['CAIDX2'];
			$AddCount = $rs['CAcount'];
			
			if($rs["CAIDX2"])
			{
				#-- 중복 상품이 있다면 카운트 증가
				$sql = "update 2011_cartInfo set CAcount=CAcount + " . $AddCount . " where IDX='" . $CAIDX2 . "'";
				sql_query($sql);
				
				#-- 준비중이던 장바구니제품 삭제
				$sql = "delete from 2011_cartInfo where IDX='" . $CAIDX . "'";
				sql_query($sql);
			}
			else
			{
				#-- 중복 상품이 없다면 상태변경
				$sql = "update 2011_cartInfo set CAstate='1' where IDX='" . $CAIDX . "'";
				sql_query($sql);				
			}			
		}
	
		//-- 종료
		echo "##OK##";
	}	
?>
