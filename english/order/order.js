//-- 배송지 변경하기
function fnSetAddr(v,v2){	
	 n = addrArray[v]["name"];
	 tel = addrArray[v]["tel"].split("-");
	 hp = addrArray[v]["hp"].split("-");
	 post = addrArray[v]["post"].split("-");
	 addr1 = addrArray[v]["addr1"];
	 addr2 = addrArray[v]["addr2"];
	 
	 jQuery("#inOname").val(n);
	 jQuery("#inOtel1").val(tel[0]);
	 jQuery("#inOtel2").val(tel[1]);
	 jQuery("#inOtel3").val(tel[2]);
	 jQuery("#inOhp1").val(hp[0]);
	 jQuery("#inOhp2").val(hp[1]);
	 jQuery("#inOhp3").val(hp[2]);
	 jQuery("#inOpost1").val(post[0]);
	 jQuery("#inOpost2").val(post[1]);
	 jQuery("#inOaddr1").val(addr1);
	 jQuery("#inOaddr2").val(addr2);

	 if(v2 > 0){
		 jQuery("#inZipMoney").val(v2);
	 } else {
		 jQuery("#inZipMoney").val(0);
	 }
	 fnUsePoint();
 
	 return;	 
}

//===================================================
//-- 재고 맞지않는 제품 모두 팝업 띄우기
//===================================================
function fnChangeStockPop(){
	fnViewPopup("pop_11","isOrder=1");
}	
	
/*========================================================
주문서 작성 모듈 불러서 결제 하기
========================================================*/

var ViewLoading = "0";
var GetInfoComplete = "0";
function fnAllTheGateStart(v){
	
	ViewLoading = v;
	chkNum = $("INPUT[name='inOpayType']:checked").eq(0).val();

	$("#amt").val(totalPrice3);
	
	
	var param="&mode=pre";
	param=param+"&inOorderName=" + $("#inOorderName").val().replaceAll("&","");
	param=param+"&inOorderTel=" + $("#inOorderTel").val();
	param=param+"&inOorderHP=" + $("#inOorderHP").val();
	param=param+"&inEmail=" + $("#inEmail1").val() + "@" + $("#inEmail2").val();
	param=param+"&inObreakdown=" + $("INPUT[name='inObreakdown']:checked").eq(0).val();
	param=param+"&inOname=" + $("#inOname").val().replaceAll("&","");
	param=param+"&inOtel=" + $("#inOtel").val();
	param=param+"&inOhp=" + $("#inOhp").val();
	param=param+"&inOpost=" + $("#inOpost").val();
	param=param+"&inOaddr1=" + $("#inMcomAddr1").val();
	param=param+"&inOaddr2=" + $("#inOaddr1").val().replaceAll("&","") + " " + $("#inOaddr2").val().replaceAll("&","") + " " + $("#inOaddr3").val().replaceAll("&","");
	param=param+"&inOcontent=" + $("#inOcontent").val().replaceAll("&","");
	param=param+"&inOpayType=" + chkNum + "&inZipMoney=" + $("#inZipMoney").val();

	//alert(param);
	$.ajax({
		url:'order_PreAfter.html',
		type:"POST",
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response) {
			v = _response.split("##");
			//alert(_response);
			
			if(v[1] == "ERROR"){
				if(v[2] == "1"){
					alert('Out of stock.');
					return;
				}
			}
					
			$("#ORDER_NO").val(v[2]);
			
			//-- 금액 검사					
			//alert("테스트중입니다.\n\n" + v[3] + " / " + (totalPriceBackup + deliveryPrice + parseFloat(document.getElementById("inZipMoney").value)));
			//return;
			
			/*if(v[3]!=(totalPriceBackup + deliveryPrice + parseFloat(document.getElementById("inZipMoney").value))) {
				//alert("오류가 발생하였습니다.\n\n페이지를 새로고침 후 동일한 메세지가 보이실 경우\n\n고객센터로 문의해주세요.");
				alert("ERROR.");
				return;
			}*/
			
			Pay(frmAGS_pay);
		}
	});
}


function PayEximbay(){
	var f = document.getElementById("frmAGS_pay");
	
	
	var param = "order_id=" + f.ORDER_NO.value + "&amount=" + f.amt.value + "&name=" + f.inOname.value.split(" ") + "&email=" + f.sender_email.value;
	$.ajax({
		url:'https://english.cheonyu.com/order/_eximbay.php',
		type:"POST",
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(data) {
			var res = $.parseJSON(data);
			if(res.rescode == '0000'){
				f._fgkey.value = res.fgkey;
			} else {
				alert('Error. Please order and payment again.');
				return;
			}
		}
	});
	
	var arrName = f.inOname.value.split(" ");
	if(f.inMcomAddr2_Sel.value) f.inOaddr3.value = f.inMcomAddr2_Sel.value;
	//f.shipTo_country.value = f.inMcomAddr1.value;
	$("#shipTo_country").val($("#inMcomAddr1").find("option:selected").data("country"));
	f.shipTo_city.value = f.inOaddr2.value;
	f.shipTo_state.value = f.inOaddr3.value;
	f.shipTo_street1.value = f.inOaddr1.value;
	f.shipTo_postalCode.value = f.inOpost.value;
	f.shipTo_phoneNumber.value = f.inOhp.value;
	f.shipTo_firstName.value = arrName[0];
	f.shipTo_lastName.value = arrName[1];
	//f.tel.value = f.inOhp.value;
	f.param2.value = f.ORDER_NO.value;
	f.sender_first_name.value = arrName[0];
	f.sender_last_name.value = arrName[1];
	f.sender_country_code.value = f.shipTo_country.value;
	f.sender_phone.value = f.inOhp.value;
	f.returnurl.value = "https://english.cheonyu.com/order/order_end.html?rOrdNo=" + f.ORDER_NO.value + "&uid=" + f.param1.value;
}

/*========================================================
결제 처리
========================================================*/	
//-- 결재 처리
function Pay(form){
	var chkNum = $("INPUT[name='inOpayType']:checked").eq(0).val();
	
	if(chkNum == "5"){ // CreditCard
		PayEximbay();
		form.paymethod.value = "P000";
		form.action='order_action_eximbay.html';
	} else if(chkNum == "6") { // Paypal
		PayEximbay();
		form.paymethod.value = "P001";
		form.action='order_action_eximbay.html';
	/*
	} else if(chkNum == "7") { // Alipay
		PayEximbay();
		form.paymethod.value = "P003";
		form.action='order_action_eximbay.html';
	} else if(chkNum == "8") { // Tenpay
		PayEximbay();
		form.paymethod.value = "P004";
		form.action='order_action_eximbay.html';
	*/
	} else {	//무통장
		form.action='order_action.html';
	}
	//console.log('system checking...');
	form.submit();
	return;
}
/*========================================================
결제방식에 따른 안내페이지 처리
========================================================*/	
function fnSetPayType()
{
	chkNum = $("INPUT[name='inOpayType']:checked").eq(0).val();
	
	for(k=1;k<=3;k++){
		if(k==chkNum)stStr = "block";
		else stStr = "none";
			$("#info" + k).css("display",stStr);
	}
	
	if(chkNum==1)$("#info4").css("display","block");
	else $("#info4").css("display","none");
	
	if(chkNum==1)$("#payTypeTD").html("무통장입금");
	else if(chkNum==2)$("#payTypeTD").html("신용카드");
	else if(chkNum==3)$("#payTypeTD").html("가상계좌");
}
//=== 결제방식에 따른 안내페이지 처리 끝 =====================================


/*========================================================
포인트 사용 처리
========================================================*/	
function fnAllUsePoint(obj) {
	// totalPrice
	// divOtotPrice 	
	if(obj.id=="AllusePoint") {
		if(obj.checked)jQuery("#inOusePoint").val(nPoint);
		else jQuery("#inOusePoint").val(0);
	} else if(obj.id=="AlluseTicket") {
		if(obj.checked)jQuery("#inOuseTicket").val(nTicket);
		else jQuery("#inOuseTicket").val(0);
	}
	fnUsePoint();
}
	
/*========================================================
포인트 후 총액 재계산
========================================================*/	
function fnUsePoint() {	
	var v1 = parseFloat(jQuery("#inOusePoint").val());
	var v2 = parseFloat(jQuery("#inOuseTicket").val());
	var lastPrice1 = parseFloat(totalPriceBackup) + parseFloat(jQuery("#inZipMoney").val());
	var lastPrice2 = lastPrice1;	//-- 쿠폰 사용 후 금액
	var lastPrice3 = lastPrice1;	//-- 포인트 사용 후 금액
	var useCPprice=0;
		
	//======= 쿠폰사용 처리(배송비를 제외한 계산) ==================================
	/*if(useCPtype && useCPvalue) {
		//== 상품권 사용은 0로 만듦.			
		jQuery("#inOuseTicket").val(0);
		v2=0;		

		if(useCPtype==1) {
			useCPprice = useCPvalue;
		} else if(useCPtype==2) {
			useCPprice = Math.round((lastPrice1-couponNoUse2)/100*useCPvalue);
		} else {
			//-- 오류
			fnDelCoupon();
			return;
		}
			
		//if( lastPrice1-couponNoUse2 < useCPprice )
		if( (useCPtype==1&&lastPrice1-couponNoUse2 < useCPprice) || (useCPtype==2&&lastPrice1 < useCPprice) ) {
			//-- 오류
			alert("구매하신 금액이 쿠폰 할인액 보다 작습니다.");
			fnDelCoupon();
			return;
		}	
		lastPrice2 = lastPrice1 - useCPprice;
		//jQuery("#couponTD").html('쿠폰적용 <font class="style2">' + commify(useCPprice) + '</font> 원 할인 <img src="/2011/image_3/op_04.gif" width="19" height="19" style="cursor:pointer;" onclick="fnDelCoupon()" align=absmiddle />');
		jQuery("#inUseCPprice").val(useCPprice);
		disablePopup();
	}*/
		
	//======= 소지금액 과 사용한 금액 제어 ===================
	if(v1>parseFloat(nPoint)) {
		alert("현재 사용가능하신 포인트는 [ " + commify(nPoint) + " P ] 입니다.");
		jQuery("#inOusePoint").val(nPoint);
		v1=nPoint;
	}	
	if(v2>parseFloat(nTicket)) {
		alert("현재 사용가능하신 상품권은 [ " + commify(nTicket) + " P ] 입니다.");
		jQuery("#inOuseTicket").val(nTicket);
		v2=nTicket;
	}
	//-- 나머지 포인트/상품권은 배송비 포함 계산가능
	lastPrice2 = lastPrice2 + deliveryPrice;
	
	//========= 사용한 포인트 / 상품권 / 쿠폰 처리 + 상품권 사용불가 + 쿠폰 사용불가 계산 ====================
	if(v1>=lastPrice2)	{
		v1=lastPrice2;
		jQuery("#inOusePoint").val(v1);
	}
	lastPrice3 = lastPrice2-v1;
	
	if(v2>=lastPrice3) {
		v2 = lastPrice3;
		jQuery("#inOuseTicket").val(v2);
	}
		
	if(v2>=(lastPrice3-couponNoUse1) && lastPrice3>=couponNoUse1) {
		v2 = lastPrice3-couponNoUse1;
		jQuery("#inOuseTicket").val(v2);
	}
	
	//--couponNoUse2 나중에 쿠폰 계산할때 씁시다.
	totalPrice = lastPrice1-v1-v2-useCPprice + deliveryPrice;
		
	//-- 총 할인금액
	totalDiscount = v1+v2+useCPprice;
		
	if(totalPrice==0) {
		//-- 0원 카드/에스크로 안됨
		$("INPUT[id='inOpayType2']").attr("disabled","true");
		$("INPUT[id='inOpayType3']").attr("disabled","true");
		$("INPUT[name='inOpayType']").eq(0).attr("checked","true");
		$("INPUT[name='inOpayType']").eq(0).click();
	} else {
		//-- 나머지는 카드결제 가능
		$("INPUT[id='inOpayType2']").removeAttr("disabled");
		$("INPUT[id='inOpayType3']").removeAttr("disabled");
	}
	
	//jQuery('#divOtotPrice').html(commify(totalPrice));
	//-- 총 금액 화면 셋팅 함수 호출
	fnSetTotalPrice(0);
}
	
	
/*=============================================================================
	쿠폰 적용하기
=============================================================================*/
useCPtype='';
useCPvalue='';
useCPlimit='';
function fnSetCoupon(t,v,limit,CPcode) {
	useCPtype=t;
	useCPvalue=v;
	useCPlimit=limit;
	
	if(totalPriceBackup-couponNoUse2<limit || totalPriceBackup-couponNoUse2==0) {
		str = "";
		if(limit) {
			str+= "본 쿠폰은 [" + commify(limit) + " 원 이상] 구매 시 이용하실 수 있습니다. \n\n";
		}
		str+= "빅세일 및 일부 특가제품들은 쿠폰으로 할인혜택을 받으실 수 없습니다.";
		alert(str);
		return;
	}
	
	jQuery("#inUseCPIDX").val(CPcode);
	
	//-- 가격 계산
	fnUsePoint();
	return;
		
	if(t==1) {
		//-- 정가 할인 alert(limit + " 원 이상 구매시 " +  v + " 원 할인");
	} else if(t==2) {
		//-- 퍼센트(%) 할인alert(limit + " 원 이상 구매시 " + v + " % 할인");
	}
}
	
	
/*=============================================================================
	쿠폰사용하기
=============================================================================*/
function fnUseCoupon() {
	var Code = jQuery("#inCPcode").val();
	if(!Code) {
		alert("쿠폰번호를 입력해 주세요."); return;
	}
	
	var param="&mode=use&CPcode=" + Code;
	
	$.ajax({
		url:'ajaxCoupon.php',
		type:"POST",
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response) {
			//alert(_response);
			v = _response.split("##");
				  
			CPtype = parseFloat(v[2]);
 			CPval = parseFloat(v[3]);
 			CPlimit = parseFloat(v[4]);
 			CPcode = v[5];
 				
			if(v[1]=="none") {
				//-- 쿠폰코드 잘못됨
				alert("등록되지 않은 쿠폰번호입니다.\n\n※ 지금샵 쿠폰은 숫자 '0' 과 대문자 'O' / 숫자 '1' 과 대문자 'I' 를 잘 확인하시고 입력해 주세요. ※");
				return;
			} else if(v[1]=="used") {
				//-- 이미 사용된 쿠폰코드
				alert("이미사용된 쿠폰번호입니다.");
				return;
			} else if(v[1]=="OK") {
				//alert("쿠폰을 사용합니다.");
				fnSetCoupon(CPtype,CPval,CPlimit,CPcode);
			}
		}
	});
}
	
function fnDelCoupon() {
	//-- 쿠폰 메세지 초기화
	jQuery("#couponTD").html('-사용된 쿠폰이 없습니다.-');
	jQuery("#inUseCPIDX").val('');
	jQuery("#inUseCPprice").val('0');
		
	//-- 쿠폰 초기화
	useCPtype='';
	useCPvalue='';
	useCPlimit='';
	
	//-- 가격 계산
	fnUsePoint();
}
	
function fnOrderComplete(Ocode) {
	document.location.replace("order2.html?oNum=" + Ocode);
}
	
function fnOnDirect(obj) {
	if(obj.checked) {
		jQuery("#inOname").val("");
		jQuery("#inOtel1").val("");
		jQuery("#inOtel2").val("");
		jQuery("#inOtel3").val("");
		jQuery("#inOhp1").val("");
		jQuery("#inOhp2").val("");
		jQuery("#inOhp3").val("");
		jQuery("#inOpost1").val("");
		jQuery("#inOpost2").val("");
		jQuery("#inOaddr1").val("");
		jQuery("#inOaddr2").val("");
	
		jQuery("#inZipMoney").val(0);
		fnUsePoint();
	} else {
		//jQuery("#inZipMoney").val(jQuery("#inZipMoney2").val());
		fnUsePoint();
	}
}

function fnSelectCountry2(){
	var v = $("#inMcomAddr1").find(':selected').data('id');
	fnSelectCountry(v,);
}

function fnSelectCountry(v, s){
	console.log(`country: ${v}`);

	if(v){
			
		var param = "&vIDX=" + v;
		$.ajax({
			url: "../ajax_paypal_state.php",
			type: "POST",
			cache: false,
			data : param,
			dataType: "text",
			error: fnErrorAjax,
			success: function(obj) {
				let data = JSON.parse(obj);
				let selectTerm = ""; 
				$("#inMcomAddr2_Sel option").remove(); 
				$("#inOaddr3").val("");

				//console.log(data);
				
				if(data.message == 'Success') {
					
					for (let i = 0; i < data.datas.length; i++) {
						selectTerm += "<option value=" + data.datas[i].PROVINCE_CD + ">" + data.datas[i].PROVINCE + "</option>";
					}
	                $("#inMcomAddr2_Sel").append(selectTerm);

					$("#htmlStateText").hide();
					$("#htmlStateSelect").show();

					$("#inMcomAddr2_Sel").val(s).attr("selected", true);
				
				} else {	// state 코드 없음 No Data 포함
					$("#htmlStateSelect").hide();
					$("#htmlStateText").show();
				}
	
			}
		});
				
	} else {
		$("#inMcomAddr2_Sel option").remove(); 
		$("#inOaddr3").val("");
		$("#htmlStateSelect").hide();
		$("#htmlStateText").show();
	}
}


function fnOnDirectGet() {
	var v = $("INPUT[name='inOdirectGet']:checked").eq(0).val();
	
	if(v==2 || v==4) {
		$("INPUT[id='inOdirect']").attr("disabled","ture");
		//-- 직배송 체크 풀기
		$("INPUT[id='inOdirect']").attr("checked",false);		
		deliveryPrice = 0;
		jQuery("#inZipMoney").val(0);
	} else {
		$("INPUT[id='inOdirect']").removeAttr("disabled");
		deliveryPrice = deliveryPriceBackup;
		//jQuery("#inZipMoney").val(jQuery("#inZipMoney2").val());
	}

	//-- 포인트 계산 꼬인다 다시 초기화
	jQuery("#inOusePoint").val(0);
	jQuery("#inOuseTicket").val(0);
	
	fnUsePoint();
}

//-- 전체 결제금액 계산식
function fnSetTotalPrice(v) {
	var countryWeight = $("#fnWeight").val();
	var countryVal = $("#inMcomAddr1").find(':selected').attr("data-id");
	if(countryVal){
		var param='mode=shipping&countryVal=' + countryVal;
		param+="&countryWeight=" + countryWeight;
		$.ajax({
			url:'ajaxShipping.php',
			type:"POST",
			data : param,
			dataType:"text",
			error:fnErrorAjax,
			success:function(_response){
				console.log(_response);
				v = _response.split("##");
				if(v[1] == "OK"){
					var ship = parseFloat(v[4]);
					$("#shippingFee").html("USD " + ship);
					$("#surcharge_0_unitPrice").val(ship);
					$("#inZipMoney").val(ship);
				} else {
					$("#shippingFee").html("USD 0");
					$("#inZipMoney").val("0");
					$("#surcharge_0_unitPrice").val("0");
				}
				
				var v2 = parseFloat(document.getElementById("inZipMoney").value);
				if(!totalDiscount) totalDiscount = 0;
				
				if(v2 > 0) var deliveryPrice2 = parseFloat(document.getElementById("inZipMoney").value);
				else var deliveryPrice2 = parseFloat(deliveryPrice) + parseFloat(document.getElementById("inZipMoney").value);

				if(v[1] == "KOREA"){
					v2 = deliveryPrice2;
					$("#inZipMoney").val(v2);
				}
				
			//deliveryPrice2 = 0;
			//v2 = 0;

				var totalPrice2 = parseFloat(totalPrice) + parseFloat(v2);
				totalPrice3 = totalPrice2.toFixed(2);

				if(!totalDiscount) totalDiscount = 0;

				//-- 1 총 금액 계산
				$("#priceTD1").html(totalPriceBackup);
				$("#priceTD2").html(deliveryPrice2);
					 
				//$("#priceTD3").html(totalDiscount);
				$("#priceTD4").html(totalPrice3);
				//-- 할인 정보 칸에 값 입력	
			}
		});
	} else {
		$("#shippingFee").html("USD 0");
		$("#inZipMoney").val("0");
		$("#surcharge_0_unitPrice").val("0");
		var v2 = parseFloat(document.getElementById("inZipMoney").value);
		if(!totalDiscount) totalDiscount = 0;
		
		if(v2 > 0) var deliveryPrice2 = parseFloat(document.getElementById("inZipMoney").value);
		else var deliveryPrice2 = parseFloat(deliveryPrice) + parseFloat(document.getElementById("inZipMoney").value);
		
	//deliveryPrice2 = 0;
	//v2 = 0;

		var totalPrice2 = parseFloat(totalPrice) + parseFloat(v2);
		totalPrice3 = totalPrice2.toFixed(2);

		if(!totalDiscount) totalDiscount = 0;

		//-- 1 총 금액 계산
		$("#priceTD1").html(totalPriceBackup);
		$("#priceTD2").html(deliveryPrice2);
			 
		//$("#priceTD3").html(totalDiscount);
		$("#priceTD4").html(totalPrice3);
		//-- 할인 정보 칸에 값 입력	
	}

}