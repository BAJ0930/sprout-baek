<?php
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

$secretKey = "749BE048E00275174EEE0029211CC2E0";//가맹점 secretkey
$mid = "24C2E64DE0";//가맹점 아이디

//기본 응답 파라미터
$ver = $_POST['ver'];//연동 버전
$txntype = $_POST['txntype'];//거래 타입
$ref = $_POST['ref'];//가맹점 지정에서 지정한 거래 아이디 
$cur = $_POST['cur'];//통화 
$amt = $_POST['amt'];//결제 금액
$shop = $_POST['shop'];//가맹점명
$buyer = $_POST['buyer'];//결제자명
$tel = $_POST['tel'];//결제자 전화번호
$email = $_POST['email'];//결제자 이메일
$lang = $_POST['lang'];//결제정보 언어 타입

$transid = $_POST['transid'];//Eximbay 내부 거래 아이디
$rescode = $_POST['rescode'];//0000 : 정상 
$resmsg = $_POST['resmsg'];//결제 결과 메세지
$authcode = $_POST['authcode'];//승인번호, PayPal, Alipay, Tenpay등 일부 결제수단은 승인번호가 없습니다.
$cardco = $_POST['cardco'];//카드 타입
$resdt = $_POST['resdt'];//결제 시간 정보 YYYYMMDDHHSS
$paymethod = $_POST['paymethod'];//결제수단 코드 (연동문서 참고)

$accesscountry = $_POST['accesscountry'];//결제자 접속 국가
$allowedpvoid = $_POST['allowedpvoid'];//Y: 부분취소 가능. N: 부분취소 불가
$fgkey = $_POST['fgkey'];//검증키, rescode=0000인 경우에만 값 세팅 됨
$payto = $_POST['payto'];//청구 가맹점명

//주문 상품 파라미터
$item_0_product = $_POST['item_0_product'];
$item_0_quantity = $_POST['item_0_quantity'];
$item_0_unitPrice = $_POST['item_0_unitPrice'];

//추가 항목 파라미터
$surcharge_0_name = $_POST['surcharge_0_name'];
$surcharge_0_quantity = $_POST['surcharge_0_quantity'];
$surcharge_0_unitPrice = $_POST['surcharge_0_unitPrice'];

//가맹점 지정 파라미터
$param1 = $_POST['param1'];
$param2 = $_POST['param2'];
$param3 = $_POST['param3'];

//카드 결제 정보 파라미터
$cardholder = $_POST['cardholder'];//결제자가 입력한 카드 명의자 영문명
$cardno1 = $_POST['cardno1'];
$cardno4 = $_POST['cardno4'];

//DCC 파라미터
$foreigncur = $_POST['foreigncur'];//고객 선택 통화
$foreignamt = $_POST['foreignamt'];//고객 선택 통화 금액
$convrate = $_POST['convrate'];//적용 환율
$rateid = $_POST['rateid'];//적용 환율 아이디

//배송지 파라미터 
$shipTo_city = $_POST['shipTo_city'];
$shipTo_country = $_POST['shipTo_country'];
$shipTo_firstName = $_POST['shipTo_firstName'];
$shipTo_lastName = $_POST['shipTo_lastName'];
$shipTo_phoneNumber = $_POST['shipTo_phoneNumber'];
$shipTo_postalCode = $_POST['shipTo_postalCode'];
$shipTo_state = $_POST['shipTo_state'];
$shipTo_street1 = $_POST['shipTo_street1'];

//CyberSource의 DM을 사용 하는 경우 받는 파라미터
$dm_decision = $_POST['dm_decision'];
$dm_reject = $_POST['dm_reject'];
$dm_review = $_POST['dm_review'];

//PayPal 거래 아이디
$pp_transid = $_POST['pp_transid'];

//일본 결제 파라미터
$status = $_POST['status'];//(일본결제)Registered or Sale :: Sale은 입금완료 시, statusurl로만 전송됨 일본 편의점/온라인뱅킹 후불결제 이용 시, 결제정보 등록에 대한 통지가 설정된 경우 발송됩니다.
$paymentURL = $_POST['paymentURL'];//일본결제의 편의점/온라인뱅킹 후불 결제 이용시 고객에게 결제 방법을 안내하는 URL


//rescode=0000 일때 fgkey 확인
if($rescode == "0000"){
	//fgkey 검증키 생성
	$linkBuf = $secretKey. "?mid=" . $mid ."&ref=" . $ref ."&cur=" .$cur ."&amt=" .$amt ."&rescode=" .$rescode ."&transid=" .$transid;
	$newFgkey = hash("sha256", $linkBuf);
	
	//fgkey 검증 실패 시 에러 처리
	/* 건당 수작업 확인
	if(strtolower($fgkey) != $newFgkey){
		$rescode = "ERROR";
		$resmsg = "Invalid transaction";
		echo "<script> alert('Invalid transaction. Please order and payment again.'); location.href = 'https://english.cheonyu.com'; </script>";
		exit;
	}
	*/
}

if($rescode == "0000"){
	//가맹점 측 DB 처리하는 부분
	//해당 페이지는 Back-End로 처리되기 때문에 스크립트, 세션, 쿠키 사용이 불가능 합니다.

	if($paymethod == "P000" or $paymethod == "P101" or $paymethod == "P102" or $paymethod == "P103" or $paymethod == "P104" or $paymethod == "P105") {
		$inOpayType = "5";
		$REPLYMSG = "CREDITCARD";
	} else if($paymethod == "P001") {
		$inOpayType = "6";
		$REPLYMSG = "PAYPAL";
	} else if($paymethod == "P003") {
		$inOpayType = "7";
		$REPLYMSG = "ALIPAY";
	} else if($paymethod == "P002") {
		$inOpayType = "8";
		$REPLYMSG = "UNIONPAY";
	}

	//-- 신규주문 카트 코드 / 추가주문 카트코드
	if($isAddOrder)$CAcode = "5";
	else if($tempCode)$CAcode=$tempCode;
	else $CAcode = "1";

	$inOdepositCheck = 2;
	$ORDER_NO = addslashes(trim($param2));
	$uid = addslashes(trim($param1));

	//-- $Oidx 구하기
	$rs = sql_fetch("select IDX from 2011_orderInfo where Ocode='" . $ORDER_NO . "' and MID='" . $uid . "'");
	$Oidx = $rs['IDX'];

	if($Oidx){

		//-- 주문서 상태 수정 (일단 미완료 처리. 하단 코드에서 다시 체크 후 업데이트됨);
		$sql = "update 2011_orderInfo set OpayState=1 where IDX='" . $Oidx . "' and MID='" . $uid . "'";
		sql_query($sql);

		//-- 주문 상품 상태 수정 (일단 미완료 처리. 하단 코드에서 다시 체크 후 업데이트됨);
		$sql = "update 2011_orderProduct set ORPcountCheck=1 where OIDX='" . $Oidx . "'";
		sql_query($sql);

		#--- 테스트용으로 쥐고있자 ㅠㅠ
		$sql = "update 2011_cartInfo set CAstate=111, CAorder=111  where MID='" . $uid . "' and CAstate=" . $CAcode;
		if($CAcode==1)$sql.=" and CAorder=1";
		sql_query($sql);


		/*=====================================================================================
		결제정보 저장
		=====================================================================================*/
		$PAY_TYPE = $inOpayType;
		$inOdepositCheck = 2;
		$sql = "update 2011_orderInfo set OpayState='2' where IDX='" . $Oidx . "' and MID='" . $uid . "'";
		sql_query($sql);

		$inOdeposit = "";
		$ACCOUNT_NO = 'english';
		
		/*=====================================================================================
		결재정보 기록
		=====================================================================================*/
		$sql = "insert into 2011_orderPayment (OIDX,OpayType,OusePoint,OuseGift,OuseCoupon,Obill,Odeposit,OdepositCheck,REPLYCD,REPLYMSG,ORDER_NO,AMT,PAY_TYPE,APPROVAL_YMDHMS,SEQ_NO,ESCROW_YN,APPROVAL_NO,CARD_ID,";
		$sql.= "CARD_NM,SELL_MM,ZEROFEE_YN,CERT_YN,CONTRACT_YN,SAVE_AMT,BANK_ID,BANK_NM,CASH_BILL_NO,ACCOUNT_NO,INCOME_ACC_NM,ACCOUNT_NM,INCOME_LIMIT_YMD,";
		$sql.= "INCOME_EXPECT_YMD,CASH_YN,HP_ID,TICKET_ID,TICKET_NAME,TICKET_PAY_TYPE) values(";

		$sql.= "'" . $Oidx . "',";

		$sql.= "'" . $inOpayType . "',";	//-- 결재방식
		$sql.= "'" . $inOusePoint . "',";	//-- 사용 포인트
		$sql.= "'" . $inOuseTicket . "',";	//-- 사용 상품권
		$sql.= "'" . $inUseCPprice . "',";	//-- 사용 쿠폰
		$sql.= "'" . $inObill . "',";	//-- 계산서 첨부
		$sql.= "'" . $inOdeposit . "',"; //-- 입금자명
		$sql.= "'" . $inOdepositCheck . "',"; //-- 입금상태

		$sql.= "'" . $rescode . "',";		//-- 결과코드
		$sql.= "'" . $REPLYMSG . "',";	//-- 결과메세지
		$sql.= "'" . $ORDER_NO . "',";	//-- 주문코드
		$sql.= "'" . $amt . "',";				//-- 결재총액
		$sql.= "'" . $resmsg . "',";		//-- 리턴메세지
		$sql.= "'" . $resdt . "',";	//-- 승인일시
		$sql.= "'" . $SEQ_NO . "',";
		$sql.= "'" . $ESCROW_YN . "',";
		$sql.= "'" . $authcode . "',";	//-- 카드거래 번호
		$sql.= "'" . $cardco . "',";		//-- 카드사 코드
		$sql.= "'" . $cardholder . "',";		//-- 카드명
		$sql.= "'" . $SELL_MM . "',";		//-- 할부개월수
		$sql.= "'" . $ref . "',";
		$sql.= "'" . $CERT_YN . "',";
		$sql.= "'" . $CONTRACT_YN . "',";
		$sql.= "'" . $SAVE_AMT . "',"; 
		$sql.= "'" . $BANK_ID . "',";					//-- 가상계좌 은행 코드
		$sql.= "'" . $pp_transid . "',";					//-- 가상계좌 은행 명, 페이팔 아이디
		$sql.= "'" . $CASH_BILL_NO . "',";
		$sql.= "'" . $ACCOUNT_NO . "',";			//-- 가상계좌번호
		$sql.= "'" . $param3 . "',";		//-- 계좌주명 (??)
		$sql.= "'" . $ACCOUNT_NM . "',";			//-- 입금자명 (입력안함)
		$sql.= "'" . $INCOME_LIMIT_YMD . "',";	//-- 입금 기한
		$sql.= "'" . $shopConfig['CFperDollar'] . "',";	//-- 입금 예정 (에스크로는 그냥 기한과 동일값)
		$sql.= "'" . $CASH_YN . "',";
		$sql.= "'" . $transid . "',";
		$sql.= "'" . $accesscountry . "',";
		$sql.= "'" . $fgkey . "',";
		$sql.= "'" . $payto . "')";					
		sql_query($sql);

		$chkPay = false;
		$rs1 = sql_fetch("select IDX from 2011_orderInfo where IDX='" . $Oidx . "'");
		$rs2 = sql_fetch("select IDX from 2011_orderProduct where OIDX='" . $Oidx . "'");
		$rs3 = sql_fetch("select IDX from 2011_orderDeliveryInfo where OIDX='" . $Oidx . "'");
		$rs4 = sql_fetch("select IDX from 2011_orderPayment where OIDX='" . $Oidx . "'");

		if($rs1['IDX'] && $rs2['IDX'] && $rs3['IDX'] && $rs4['IDX']) {
		} else {		
			$memo = "해외결제실패 - " . $param2 . " .. " . $buyer . " .. " . $param1;
			sql_query(" INSERT INTO admin_memo VALUES ('', 'system', 'system', '$memo', '', '1', now(), '', '', 1) ");
		}

	} else {
		//오류처리
		$memo = "해외결제실패 - " . $param2 . " .. " . $buyer . " .. " . $param1;
		sql_query(" INSERT INTO admin_memo VALUES ('', 'system', 'system', '$memo', '', '1', now(), '', '', 1) ");
		echo "<script> alert('Invalid transaction. If the payment has been made, we will cancel it ASAP.'); location.href = 'https://english.cheonyu.com'; </script>";
		exit;
	}

} else {
		
		$Oidx = "000000";
		/*=====================================================================================
		결재정보 기록
		=====================================================================================*/
		$sql = "insert into 2011_orderPayment (OIDX,OpayType,OusePoint,OuseGift,OuseCoupon,Obill,Odeposit,OdepositCheck,REPLYCD,REPLYMSG,ORDER_NO,AMT,PAY_TYPE,APPROVAL_YMDHMS,SEQ_NO,ESCROW_YN,APPROVAL_NO,CARD_ID,";
		$sql.= "CARD_NM,SELL_MM,ZEROFEE_YN,CERT_YN,CONTRACT_YN,SAVE_AMT,BANK_ID,BANK_NM,CASH_BILL_NO,ACCOUNT_NO,INCOME_ACC_NM,ACCOUNT_NM,INCOME_LIMIT_YMD,";
		$sql.= "INCOME_EXPECT_YMD,CASH_YN,HP_ID,TICKET_ID,TICKET_NAME,TICKET_PAY_TYPE) values(";

		$sql.= "'" . $Oidx . "',";

		$sql.= "'" . $inOpayType . "',";	//-- 결재방식
		$sql.= "'" . $inOusePoint . "',";	//-- 사용 포인트
		$sql.= "'" . $inOuseTicket . "',";	//-- 사용 상품권
		$sql.= "'" . $inUseCPprice . "',";	//-- 사용 쿠폰
		$sql.= "'" . $inObill . "',";	//-- 계산서 첨부
		$sql.= "'" . $inOdeposit . "',"; //-- 입금자명
		$sql.= "'" . $inOdepositCheck . "',"; //-- 입금상태

		$sql.= "'" . $rescode . "',";		//-- 결과코드
		$sql.= "'" . $REPLYMSG . "',";	//-- 결과메세지
		$sql.= "'" . $ORDER_NO . "',";	//-- 주문코드
		$sql.= "'" . $amt . "',";				//-- 결재총액
		$sql.= "'" . $resmsg . "',";		//-- 리턴메세지
		$sql.= "'" . $resdt . "',";	//-- 승인일시
		$sql.= "'" . $SEQ_NO . "',";
		$sql.= "'" . $ESCROW_YN . "',";
		$sql.= "'" . $authcode . "',";	//-- 카드거래 번호
		$sql.= "'" . $cardco . "',";		//-- 카드사 코드
		$sql.= "'" . $cardholder . "',";		//-- 카드명
		$sql.= "'" . $SELL_MM . "',";		//-- 할부개월수
		$sql.= "'" . $ref . "',";
		$sql.= "'" . $CERT_YN . "',";
		$sql.= "'" . $CONTRACT_YN . "',";
		$sql.= "'" . $SAVE_AMT . "',"; 
		$sql.= "'" . $BANK_ID . "',";					//-- 가상계좌 은행 코드
		$sql.= "'" . $pp_transid . "',";					//-- 가상계좌 은행 명, 페이팔 아이디
		$sql.= "'" . $CASH_BILL_NO . "',";
		$sql.= "'" . $ACCOUNT_NO . "',";			//-- 가상계좌번호
		$sql.= "'" . $param3 . "',";		//-- 계좌주명 (??)
		$sql.= "'" . $ACCOUNT_NM . "',";			//-- 입금자명 (입력안함)
		$sql.= "'" . $INCOME_LIMIT_YMD . "',";	//-- 입금 기한
		$sql.= "'" . $shopConfig['CFperDollar'] . "',";	//-- 입금 예정 (에스크로는 그냥 기한과 동일값)
		$sql.= "'" . $CASH_YN . "',";
		$sql.= "'" . $transid . "',";
		$sql.= "'" . $accesscountry . "',";
		$sql.= "'" . $fgkey . "',";
		$sql.= "'" . $payto . "')";					
		sql_query($sql);

		//오류처리
		$memo = "해외결제실패(내역 오류) - " . $param2 . " .. " . $buyer . " .. " . $param1 . " .. rescode:" . $rescode;
		sql_query(" INSERT INTO admin_memo VALUES ('', 'system', 'system', '$memo', '', '1', now(), '', '', 1) ");
	echo "<script> alert('Invalid transaction. If the payment has been made, we will cancel it ASAP.'); location.href = 'https://english.cheonyu.com'; </script>";
	exit;
}
?>
