<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	$CPcode = str_replace("-","",$CPcode);
	
	if($mode=="use"){
		$sql = "select a.IDX as CPLSITIDX,a.CPuseMID,b.* from 2011_couponList as a left join 2011_couponGroup as b on a.CPIDX = b.IDX ";
		$sql.= " where replace(CPcode,'-','')='" . $CPcode . "' and CPdate1<='" . date(time()) . "' and CPdate2>='" . date(time()) . "'";
		$result = sql_query($sql);
		
		#-- 쿠폰이 여러장일 경우 사용가능 쿠폰이 있는지 검사
		$none=1;
		$used=0;
		$OK=0;
		while($rs = sql_fetch_array($result)) {
			#-- 일단 존재하는 쿠폰으로 표시
			$none=0;
			if($rs["CPuseMID"]) {
				#-- 사용된 쿠폰이라면 표시 후 다음 쿠폰 검색
				$used=1;
				$OK=0;
			} else {
				#-- 사용할 수 있는 쿠폰 발경
				$used=0;			
				$OK=1;
				break;
			}		
		}	
		
		#-- 결과 표시
		if($none) echo "##none##";
		else if($used) echo "##used##";
		else if($OK) {
			echo "##OK##";
			echo $rs["CPtype"] . "##" . $rs["CPprice"] . "##" . $rs["CPlimit"] . "##"  . $rs["CPLSITIDX"] . "##";
		}
		exit();
	}	
?>