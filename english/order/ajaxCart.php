<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($mode=="edit")
	{
		//============= 갯수 수정 일괄 처리기능 추가 =========================================
		$nCount = explode(",",$nCount);
		$qIDX = explode(",",$qIDX);

		$s = sizeof($nCount);

		for($k=0;$k<$s;$k++)
		{
			$sql = "update 2011_cartInfo set CAcount='" . $nCount[$k] . "' where IDX='" . addslashes($qIDX[$k]) . "'";
			sql_query($sql);
		}
		if($s==1)echo "##OK##" . $nNum . "##" . $nCount[0];		//-- 카트중 1가지 상품 재고 수정
		else if($s>1)echo "##OK##";		//-- 카트 상품중 여러가지 상품 재고 수정
	}
	else if($mode=="delete")
	{
		$qIDX = explode(",",$qIDX);
		$len = sizeof($qIDX);
		
		for($k=0;$k<$len;$k++)
		{
			//============= 장바구니 삭제 =========================================
			$sql = "delete from 2011_cartInfo where IDX='" . addslashes(trim($qIDX[$k])) . "'";
			sql_query($sql);
		}

		echo "##OK##" . $nNum . "##" . $nCount;
	}
	else if($mode=="totalPrice")
	{
		//============= 장바구니에 있는 제품의 총액 / 총 갯수 / 배송비 리턴 =========================================
		#-- 업체별 배송비 담을 변수
		$DVinfo = array();		//-- 업체별 배송비
		//-- 첫째자리는 천유 관리자 -- 업로더는 다를 수 있으니 master 로 구분한다.
		$DVinfo[0]["com"] = $masterSeller;	//-- 판매자 아이디
		$DVinfo[0]["total"] = 0;			//-- 구매 총액
		
		$DVinfo[0]["taxTotal"] = 0;			//-- 구매 총액(과세)
		$DVinfo[0]["freeTotal"] = 0;			//-- 구매 총액(면세)
		
		$DVinfo[0]["noFreeDelivery"] = 0;			//-- 구매 총액(무료배송조건제외)
		
		$DVinfo[0]["weight"] = 0;			//-- 구매 총 무게
		$DVinfo[0]["dlimit1"] = 200;		//-- 일반 배송비 조건
		$DVinfo[0]["dprice1"] = 3;		//-- 일반 배송비
		$DVinfo[0]["dlimit2"] = 200;		//-- 도매 배송비 조건 A
		$DVinfo[0]["dprice2"] = 3;		//-- 도매 배송비 A
		$DVinfo[0]["dlimit3"] = 200;		//-- 도매 배송비 조건 B
		$DVinfo[0]["dprice3"] = 3;		//-- 도매 배송비 B
		$DVinfo[0]["dlimit4"] = 200;		//-- 도매 배송비 조건 C
		$DVinfo[0]["dprice4"] = 3;		//-- 도매 배송비 C

		if($Ocode) $stateCode = 5;
		else if($tempCode) $stateCode = $tempCode;
		else $stateCode = 1;
		
		if($cartCode=="")
		{
			#-- 전체 장바구니 제품 (특별한 조건문 없음 / 카트 업데이트)
			$sql = "update 2011_cartInfo set CAorder=0, CAcheck=1 WHERE CAstate=1 and MID = '" . $uid . "'";
			sql_query($sql);
		}
		else if($cartCode=="orderCancel")
		{
			#-- 주문서에서 빠져나감 표시
			$sql = "update 2011_cartInfo set CAorder=0 where CAstate=1 and CAcheck=1 and MID = '" . $uid . "'";
			sql_query($sql);
			echo "OK";
			exit();			
		}
		else if($cartCode=="order")
		{
			/*if($Ocode) $stateCode = 5;
			else if($tempCode) $stateCode = $tempCode;
			else $stateCode = 1;
			
			#-- 전체 CAorder는 0으로 만들기 ???
			$sql = "update 2011_cartInfo set CAorder=0 where MID = '" . $uid . "' and CAorder=1 and CAstate='" . $stateCode . "'";
			sql_query($sql);
			
			#-- 전체 장바구니 제품 (특별한 조건문 없음 / 카트 업데이트)
			$sql = "update 2011_cartInfo set CAorder=1 where MID = '" . $uid . "' and CAcheck=1 and CAstate='" . $stateCode . "'";
			sql_query($sql);*/
			
			#-- 실제 주문할 제품
			$cartWhere = " CAorder = 1 ";
		}
		else if($cartCode)
		{
			#-- 해당 코드 제품만 계산
			$cartCodeArray = explode(",",$cartCode);
			$cartLen = sizeof($cartCodeArray);
			
			for($k=0;$k<$cartLen;$k++)
			{
				if($cartWhere)$cartWhere.=" or ";
				$cartWhere.= " a.IDX='" . $cartCodeArray[$k] . "' ";
			}
			if($cartWhere)$cartWhere = "(" . $cartWhere . ")";
			
			sql_query(" UPDATE 2011_cartInfo SET CAorder = '0' WHERE MID = '" . $uid . "' AND CAstate = '1' ");
			sql_query(" UPDATE 2011_cartInfo AS a SET a.CAorder = '1', a.CAcheck = '1' WHERE " . $cartWhere );
		}

		#-- 등록한 사람을 기준으로 정렬
		$sql = " SELECT a.PIDX,b.PtaxType,b.Pprice2,b.Pprice3, a.CAcount, b.PdeliveryPrice, b.PdeliveryType, b.Puploader,Pweight,b.PcouponUse1,b.PcouponUse2,PdeliveryPriceType,";
		$sql.= " b.Pdiscount,b.Pdiscount10,b.Pdiscount20,b.Pdiscount30,b.Pdiscount40,b.Pdiscount50,b.Pdiscount60,b.Pdiscount70,b.Pdiscount80,b.Pdiscount90,Pdiscount100,Pdiscount110,Pdiscount120,";
		$sql.= " EVIDX,EVdiscount,b.Pboxin,b.PboxCount,b.PboxCount2,";
		$sql.= " c.OPbox, d.MSdeliveryLimit, d.MSdeliveryPay, d.MScomDeliveryLimit, d.MScomDeliveryPay,ifnull(d.MSID,b.Pshop) ";
		$sql.= " FROM 2011_cartInfo AS a ";
		$sql.= " LEFT JOIN 2011_productInfo AS b ON a.PIDX = b.IDX ";
		$sql.= " LEFT JOIN 2011_productOption AS c ON a.OPIDX = c.IDX ";
		$sql.= " LEFT JOIN 2011_brandInfo AS e ON b.BRIDX = e.IDX ";
		$sql.= " LEFT JOIN 2011_makerSeller AS d ON e.MKIDX = d.MKIDX ";
		$sql.= " WHERE MID = '" . $uid . "' ";		
		if($cartWhere) $sql.= " AND " . $cartWhere;		
		$sql.= " AND a.CAstate ='" . $stateCode . "' order by b.Puploader";
		$result = sql_query($sql);
		
		$_TOT_PRICE=0;
		$_TOT_TAX_PRICE=0;
		$_TOT_FREE_PRICE=0;
		$_TOT_COUNT=0;
		$_ADD_DELIVERY_PAY=0;
		$_TOT_WEIGHT=0;
		
		#-- 2011.05.17 상품권 사용불가 / 쿠폰 사용불가 체크
		$_DISABLE_GIFT = 0;
		$_DISABLE_COUPON = 0;
		
		#-- 2011.06.02 무료배송 조건 미포함 제품 체크
		$_NOFREE_DIRYVERY = 0;

		while($rs = sql_fetch_array($result))
		{
			# DB 레코드 결과값 모두 변수로 전환
			foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}

			#-- 할인가 정보가 있을경우 강제 할인가 적용
			$priceInfo = fnCalPrice($dbPprice3,$rs,1);
			$_TOT_PRICE+= $priceInfo["dcDollar_txt"] * $dbCAcount;
			
			if($dbPtaxType==1)$_TOT_TAX_PRICE+=$priceInfo["dcDollar_txt"] * $dbCAcount;
			else $_TOT_FREE_PRICE+=$priceInfo["dcDollar_txt"] * $dbCAcount;
			
			$_TOT_COUNT+=$dbCAcount;
						
			if($dbPcouponUse1)$_DISABLE_GIFT+= $priceInfo["dcDollar_txt"] * $dbCAcount;
			if($dbPcouponUse2)$_DISABLE_COUPON+= $priceInfo["dcDollar_txt"] * $dbCAcount;
			
			#==== 배송 방법에 따른 총액 저장
			if($dbPdeliveryType==1)
			{
				#-- 천유 배송 ------------------------------------
				$DVinfo[0]["total"]+=$priceInfo["dcDollar_txt"] * $dbCAcount;
				
				if($dbPdeliveryPriceType==4)$DVinfo[0]["noFreeDelivery"] = $priceInfo["dcDollar_txt"] * $dbCAcount;			//-- 구매 총액(무료배송조건제외)
				
			}
			else
			{
				#-- 업체 배송 ------------------------------------

				#-- 자기 자리 찾기
				$size = sizeof($DVinfo);
				$comPoint=-1;
				for($k=0;$k<$size;$k++)
				{
					if($DVinfo[$k]["com"]==$dbMSID)
					{
						$comPoint = $k;
						break;
					}
				}

				if($comPoint>=0)
				{
					#-- 자리 찾았으면 해당 자리에 누적
					$DVinfo[$comPoint]["total"]+=$priceInfo["dcDollar_txt"] * $dbCAcount;
					
					if($dbPdeliveryPriceType==4)$DVinfo[$comPoint]["noFreeDelivery"] = $priceInfo["dcDollar_txt"] * $dbCAcount;			//-- 구매 총액(무료배송조건제외)
				}
				else
				{
					#-- 새로운 배열 자리에 밀어넣기
					$DVinfo[$size]["com"]=$dbMSID;
					$DVinfo[$size]["total"]=$priceInfo["dcDollar_txt"] * $dbCAcount;
					$DVinfo[$size]["dlimit1"] = $dbMSdeliveryLimit;		//-- 일반 배송비 조건
					$DVinfo[$size]["dprice1"] = $dbMSdeliveryPay;		//-- 일반 배송비
					$DVinfo[$size]["dlimit2"] = $dbMScomDeliveryLimit;		//-- A
					$DVinfo[$size]["dlimit3"] = $dbMScomDeliveryLimit;		//-- B
					$DVinfo[$size]["dlimit4"] = $dbMScomDeliveryLimit;		//-- C
					$DVinfo[$size]["dprice2"] = $dbMScomDeliveryPay;		//-- A
					$DVinfo[$size]["dprice3"] = $dbMScomDeliveryPay;		//-- B
					$DVinfo[$size]["dprice4"] = $dbMScomDeliveryPay;		//-- C
					$DVinfo[$size]["weight"] = $dbPweight * $dbCAcount;			//-- 구매 총 무게
					
					if($dbPdeliveryPriceType==4)$DVinfo[$size]["noFreeDelivery"] = $priceInfo["dcDollar_txt"] * $dbCAcount;			//-- 구매 총액(무료배송조건제외)
					
				}
			}

			#-- 제품별 추가 배송비 계산
			if($dbPdeliveryPriceType==3)$_ADD_DELIVERY_PAY+=$dbPdeliveryPrice;
			
			#-- 총 무게 계산
			$_TOT_WEIGHT+=$dbPweight * $dbCAcount;
		}
	
		
		#================= 기본 배송비 설정 ============================================

		//-- 추가주문은 배송비 0 => 기존 배송 정보에서 해당 업체 주문이 있는지 체크

		$_DELIVERY_PRICE=0;
		$size = sizeof($DVinfo);

		for($k=0;$k<$size;$k++)
		{
			$Dprice=0;
			
			if($DVinfo[$k]["total"])
			{
				//echo $DVinfo[$k]["noFreeDelivery"] . "//" . $DVinfo[$k]["total"] . "// " . $DVinfo[$k]["dprice2"] .  "<br>";
				if($MLV == "A" && $DVinfo[$k]["total"] - $DVinfo[$k]["noFreeDelivery"]<$DVinfo[$k]["dlimit2"]) {
					$Dprice = $DVinfo[$k]["dprice2"]; 
				} else if($MLV == "B" && $DVinfo[$k]["total"] - $DVinfo[$k]["noFreeDelivery"]<$DVinfo[$k]["dlimit3"]) {
					$Dprice = $DVinfo[$k]["dprice3"];
				} else if($MLV == "C" && $DVinfo[$k]["total"] - $DVinfo[$k]["noFreeDelivery"]<$DVinfo[$k]["dlimit4"]) {
					$Dprice = $DVinfo[$k]["dprice4"];
				} else if($MLV == "D" && $DVinfo[$k]["total"] - $DVinfo[$k]["noFreeDelivery"]<$DVinfo[$k]["dlimit2"]) {
					$Dprice = $DVinfo[$k]["dprice2"];
				} else {
					if($DVinfo[$k]["total"] - $DVinfo[$k]["noFreeDelivery"] < $DVinfo[$k]["dlimit1"]){
						$Dprice = $DVinfo[$k]["dprice1"];
					}
				}
	
				if($Ocode)
				{
					#-- 추가 주문일 경우 기존 같은 업체의 배송 정보가 있는지 체크(있다면 무료, 없다면 똑같이 정상 적용)
					$sql = "select * from 2011_orderDeliveryInfo where ODuser='" . $DVinfo[$k]["com"] . "'";
					if($Drs = sql_fetch($sql)) $Dprice=0;
				}
				$_DELIVERY_PRICE+=$Dprice;
			}
		}

		#================= 기본 배송비 설정 끝 ============================================

		#-- 재고부족 카트 정보 체크
		$sql = "SELECT a.*, b.Pname, b.PstockCount, b.Pstate, b.PsaveFile1, c.OPname, c.OPvalue, c.OPstock ";
		$sql.= " FROM 2011_cartInfo AS a ";
		$sql.= " LEFT JOIN 2011_productInfo AS b ON a.PIDX = b.IDX ";
		$sql.= " LEFT JOIN 2011_productOption AS c ON a.OPIDX = c.IDX ";
		$sql.= " WHERE MID = '" . $uid . "'";
		$sql.= " AND a.CAstate = 1 ";
		$sql.= " AND b.PorderMinus != 1 ";
		if($cartWhere) $sql.= " AND " . $cartWhere;
		$result = sql_query($sql);		
		$isOver = 0;
		while($data = sql_fetch_array($result)){
			if($data['Pstate'] > 9) $isOver = 1;
			$dbPready = getProductReady($data['PIDX'],$data['OPIDX']);
			
			if($data['OPIDX']) $maxStock = $data['OPstock'];
			else $maxStock = $data['PstockCount'];

			$dbPstockCount = $maxStock - $dbPready;
			if($dbPstockCount < $data['CAcount']) $isOver = 1;
		}
		if($_Minus == 1) $isOver = 0;
		
		#================ 최종 정보 리턴 ====================================================
		echo "##OK##" . $_TOT_PRICE . "##" . $_TOT_COUNT . "##" . $_TOT_WEIGHT;
		echo "##" . $_DELIVERY_PRICE . "##" . $_ADD_DELIVERY_PAY . "##" . $isOver;
		echo "##" . $_DISABLE_GIFT . "##" . $_DISABLE_COUPON;
		echo "##" . $_TOT_TAX_PRICE . "##" . $_TOT_FREE_PRICE;
		echo "##" . $pay;
	}
	else
	{
		echo "##error##";
	}

?>