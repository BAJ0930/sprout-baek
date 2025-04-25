var popBox1 = "<div style='background-color:#fff;padding:15px;border:1px solid #ccc;'>";
var popBox2 = "</div>";
var HOME_PATH = "";

$(document).ready(function () {

	/* 상단 검색 부분 */
	$(".inputDesign").each(function(i){
		var action = $(this).attr('action');
		var parent = $(this);
		$(this).bind({
			mouseenter : function(){
				action = true;
			},
			mouseleave : function() {
				action = false;
			},
			click : function(){
				$(parent).find('ul').show();
				$(".inputDesign").css('zIndex','19');
				$(this).css('zIndex','20');
				if ($(parent).find('li').length > 5) $(parent).find('ul').css('height',$(parent).find('li').height()*5);
				else $(parent).find('ul').css('height','auto');
			}
		});
		$(this).find('li').find('a').click(function(){
			$(parent).find('p').find('a').text($(this).text());
			$(parent).find('ul').hide();
			action = false;
		});
		$('body').click(function() {
			if (!action) {
				$(parent).find('ul').hide();
				action = false;
			}
		});
	});

});

/*=======================================================================
	main 상단 메뉴
=======================================================================*/
function fnTopMenuOn(id,img){
	$("." + id).fadeIn(0);
	document.getElementById(img).src = "/image/main/" + img + "_on.png";
}
function fnTopMenuOut(id,img){
	$("." + id).fadeOut(0);
	document.getElementById(img).src = "/image/main/" + img + ".png";
}
function fnTopMenuOn_Brand(img){
	document.getElementById(img).src = "/img/" + img + "_on.png";
}
function fnTopMenuOut_Brand(img){
	document.getElementById(img).src = "/img/" + img + ".png";
}


/*=======================================================================
	ajax 공통 오류 처리 함수
=======================================================================*/
function fnErrorAjax()
{
	alert("ERROR");
	return;
}

//검색
function fnProductSearchEng(skey)
{
	str = "/product/list.html?s1=";
	if(skey)keyword=encodeURIComponent(skey);
	else keyword = encodeURIComponent(document.getElementById("search_s1").value);

	if (!keyword){
		alert('Enter the keyword searching.');
		return;
	}
	if(document.getElementById("searchDropdownBox").value == "Brand"){
		document.location = "/brand/brand.html?s3=" + encodeURIComponent(document.getElementById("search_s1").value);
		return;
	} else {
		document.location=str + keyword + "&searchKind=" + document.getElementById("searchDropdownBox").value;
		return;
	}
	document.getElementById("searchForm").submit();
}

/*===============================================================================================
	InputBox Validation Check
===============================================================================================*/
function VCheck(obj,obj_name,must_word,deny_word)
{
	if(!obj.value)
	{
		alert("[" + obj_name + "] 을(를) 입력해 주세요.");
		try{obj.focus();}catch(E){}
		return false;
	}

	if(must_word && obj.value.indexOf(must_word) < 0)
	{
		alert("[" + obj_name + "] 항목에 [" +  must_word +"] 단어를 반드시 포함하여야 합니다.");
		try{obj.focus();}catch(E){}
		return false;
	}

	if(deny_word && obj.value.indexOf(deny_word))
	{
		alert("[" + obj_name + "] 항목에 [" +  deny_word +"] 단어는 입력하실 수 없습니다.");
		try{obj.focus();}catch(E){}
		return false;
	}

	return true;
}

/*===============================================================================================
	InputBox Validation Check jQuery 
===============================================================================================*/
function VCheckjQuery(obj,obj_name,must_word,deny_word)
{
	if(!obj.val())
	{
		alert("Please enter " + obj_name + ".");
		obj.focus();
		return false;
	}

	if(must_word && obj.value.indexOf(must_word) < 0)
	{
		alert("[" + obj_name + "] 항목에 [" +  must_word +"] 단어를 반드시 포함하여야 합니다.");
		obj.focus();
		return false;
	}

	if(deny_word && obj.value.indexOf(deny_word))
	{
		alert("[" + obj_name + "] 항목에 [" +  deny_word +"] 단어는 입력하실 수 없습니다.");
		obj.focus();
		return false;
	}

	return true;
}


/*=======================================================================
숫자 천단위 콤마 찍기
=======================================================================*/
function commify(n) {
  n = parseFloat(n).toFixed(2); // Convert to a float and fix to two decimal places
  var parts = n.split('.');     // Split integer and decimal parts
  var reg = /(^[+-]?\d+)(\d{3})/; // Regular expression for thousands separator

  var intPart = parts[0];
  while (reg.test(intPart)) {
    intPart = intPart.replace(reg, '$1' + ',' + '$2');
  }

  return intPart + (parts[1] ? '.' + parts[1] : ''); // Recombine with the decimal part
}

/*----------------------------------------------------------------
숫자만 입력 가능
----------------------------------------------------------------*/ 
function onlyNum()
{
	var inChar = String.fromCharCode(event.keyCode);

	code = event.keyCode;	
	//- 우측 키패드 numpad keycode값 96 ~ 105    Tab : 9   BackSpace : 8  Delete : 46    왼쪽 : 37   오른쪽 : 39	  컨트롤 : 17   .  : 190 or 110
	if((isNaN(inChar) || code==32 || code==13) && !(code==8 || code==9 || code==46 || code==37 || code==39 || code==17 || code==190 || code==110 || (code>=96 && code<=105)) && code!=229)
	{
		event.returnValue = false;
		//alert(code);
	}
	else
	{
		event.returnValue = true;
	}

	return;
}
/*----------------------------------------------------------------
한글 제한
----------------------------------------------------------------*/
function hanCheck(obj)
{
	var check = "[ㄱ-ㅎ가-힣]";
	var CHK_STRING = new RegExp(check);
	if(CHK_STRING.test(obj.value))
	{
		return false;
	}
	return true;
}
/*----------------------------------------------------------------
숫자만 입력 가능 + 마이너스 사용가능(인자값으로 처리하려 하였으나 추가로 처리되는 사항들이 좀 있어서 따로 분리
----------------------------------------------------------------*/
function onlyNum2(val)
{
	var inChar = String.fromCharCode(event.keyCode);
	
	code = event.keyCode;
	if((code==109 || code==189) && (val.length==0 || (val==0 && val.length==1)))
	{
		//-- 마이너스 요소는 처음에만 입력할 수 있습니다.
		event.returnValue = true;
		return;
	}
	
	//- 우측 키패드 numpad keycode값 96 ~ 105    Tab : 9   BackSpace : 8  Delete : 46    왼쪽 : 37   오른쪽 : 39	  컨트롤 : 17
	if((isNaN(inChar) || code==32 || code==13) && !(code==8 || code==9 || code==46 || code==37 || code==39 || code==17 || (code>=96 && code<=105)) && code!=229)
	{		
		event.returnValue = false;		
	}
	else
	{
		event.returnValue = true;
	}

	return;
}

/*=================================================
	장바구니 공통 제어 ( + 관리자모드 주문된 상품 제어 ) Script
=================================================*/
	
//=== 옵션 사용시 수량 + - =====================================
function fnPcountPlus(obj,v)
{
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	nNum = $("img[id='" + myID + "']").index(obj);
	inputObj = $("input[id='inPcount']").eq(nNum);
	inputObj.val(parseFloat(inputObj.val())+v);
	if(parseFloat(inputObj.val())<1)inputObj.val(1);
	$("DIV[id='msgDiv']").eq(nNum).html("<font color=red>Please be sure to click 'change' button after change quantity</font>");
	fnPcountCheck(obj);
}

//=== 수량 제한 체크 =====================================
function fnPcountCheck(obj)
{
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	
	myTag = obj.tagName;
	nNum = $(myTag + "[id='" + myID + "']").index(obj);
	
	obj = $("input[id='inPcount']").eq(nNum);
	var maxCount = $("input[id='inMaxStock']").eq(nNum).val();
	var PorderMinus = $("input[id='inPorderMinus']").eq(nNum).val();

	if(obj.val()>parseFloat(maxCount))
	{
		if(PorderMinus != "1"){

			if($("div[id='msgDiv']").attr("tagName"))
			{
				$("div[id='msgDiv']").eq(nNum).html(maxCount + " pcs ramainning now.");
				setTimeout("$(\"div[id='msgDiv']\").eq(nNum).html(\"<font color=red>Please be sure to click 'change' button after change quantity</font>\")",2000);
			}
			else
			{
				alert(maxCount + " pcs ramainning now.");
			}
		
			obj.val(maxCount);

		} 
	}
	else
	{
		$("div[id='msgDiv']").eq(nNum).html("<font color=red>Please be sure to click 'change' button after change quantity.</font>");
	}
	
	if($("input[id='inPcount']").attr("tagName"))
	{
		var count = parseFloat($("input[id='inPcount']").eq(nNum).val());

		var boxCount =  parseFloat($("input[id='dcbox']").eq(nNum).val());
		var boxCount2 =  parseFloat($("input[id='dcbox2']").eq(nNum).val());
		var jsMLV = $("input[id='jsMLV']").val();
		var Pprice2Box = $("input[id='inBoxPrice']").eq(nNum).val();
		var Pprice2Box2 = $("input[id='inBoxPrice2']").eq(nNum).val();	

		if(!boxCount) boxCount = 0;
		if(!boxCount2) boxCount2 = 0;
		
		if(count >= parseFloat(boxCount2) && Pprice2Box2 > 0 && boxCount2 > 0 && jsMLV){		//박스 주문
			var dc8 = parseFloat($("input[id='dc8']").eq(nNum).val());
			price = parseFloat($("input[id='inBoxPrice2']").eq(nNum).val());	
			$("[id='htmlDCPrice']").eq(nNum).html("<strong>USD "+price+"</strong>");
			$("[id='htmlDCPer']").eq(nNum).html("("+dc8+"% OFF)");			
		} else if(count >= parseFloat(boxCount) && Pprice2Box > 0 && boxCount > 0 && jsMLV){		//박스 주문
			var dc9 = parseFloat($("input[id='dc9']").eq(nNum).val());
			price = parseFloat($("input[id='inBoxPrice']").eq(nNum).val());	
			$("[id='htmlDCPrice']").eq(nNum).html("<strong>USD "+price+"</strong>");
			$("[id='htmlDCPer']").eq(nNum).html("("+dc9+"% OFF)");
		} else {
			var dc0 = parseFloat($("input[id='dc0']").eq(nNum).val());
			price = parseFloat($("input[id='inOPrice']").eq(nNum).val());			
			$("[id='htmlDCPrice']").eq(nNum).html("<strong>USD "+price+"</strong>");			
			$("[id='htmlDCPer']").eq(nNum).html("("+dc0+"% OFF)");
		}

		weight = parseFloat($("input[id='inOneWeight']").eq(nNum).val());
		
		itPrice = (count*price);

		$("TD[id='inListPrice']").eq(nNum).html("USD " + itPrice.toFixed(2));
		$("span[id='inListWeight']").eq(nNum).html((weight * count) + " g");
	}
}

//=== 옵션 적용 =====================================
function fnPcountSet(obj)
{
	try{if(!ajaxUrl){}}
	catch(E){alert("ERROR.");return;}
	
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	myTag = obj.tagName;
	nNum = $(myTag + "[id='" + myID + "']").index(obj);

	var IDX = $("INPUT[id='inPIDX']").eq(nNum).val();
	var nCount = parseFloat($("INPUT[id='inPcount']").eq(nNum).val());
	var maxCount = parseFloat($("INPUT[id='inMaxStock']").eq(nNum).val());

	var boxPrice = parseFloat($("INPUT[id='inBoxPrice']").eq(nNum).val());
	var boxPrice2 = parseFloat($("INPUT[id='inBoxPrice2']").eq(nNum).val());
	var boxCount = parseFloat($("INPUT[id='dcbox']").eq(nNum).val());
	var boxCount2 = parseFloat($("INPUT[id='dcbox2']").eq(nNum).val());
	var dc0 = parseFloat($("INPUT[id='dc0']").eq(nNum).val());
	var dc8 = parseFloat($("INPUT[id='dc8']").eq(nNum).val());
	var dc9 = parseFloat($("INPUT[id='dc9']").eq(nNum).val());

	var PorderMinus = parseFloat($("INPUT[id='inPorderMinus']").eq(nNum).val()); 

	if(nCount<=0)
	{
		nCount=1;
		$("INPUT[id='inPcount']").eq(nNum).val(1);
	}
	
	if((maxCount<nCount) && (PorderMinus != '1'))
	{
		alert(maxCount +" pcs ramainning now.");
		$("INPUT[id='inPcount']").eq(nNum).val(maxCount);
		nCount = maxCount;
	}

	param='qIDX=' + IDX + '&mode=edit&nCount=' + nCount + "&nNum=" + nNum;

	$.ajax({
	url:ajaxUrl,
	type:"POST",
	cache:false,
	data : param,
	dataType:"text",
	error:fnErrorAjax,
	success:function(_response)
	{
		//alert(_response);
		v = _response.split("##");
		if(v[1]=="OK")
		{
			if(v[2])
			{
				$("DIV[id='msgDiv']").eq(v[2]).html("<span>Has been modified " + v[3] + " pieces.</span>");
				//$("[id='htmlDCPrice']").eq(v[2]).html("<strong>1111</strong>");
				setTimeout("msgDivClear(" + v[2] + ")",2000);
			}
			else
			{
				alert("<span>Has been modified " + v[3] + " pieces.</span>");
			}
			fnGetTotalInfo();
		}
		else
		{
			if(v[2])
			{
				$("DIV[id='msgDiv']").eq(v[2]).html("<font color=red>ERROR.</font>");
				setTimeout("msgDivClear(" + v[2] + ")",2000);
			}
			else
			{
				alert(_response);
				alert("ERROR.");
			}
		}
	}
});
}

function fnProductDelete(obj)
{
	try{if(!ajaxUrl){}}
	catch(E){alert("ERROR.");return;}
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	myTag = obj.tagName;
	nNum = $(myTag + "[id='" + myID + "']").index(obj);
	IDX = $("INPUT[id='inPIDX']").eq(nNum).val();
	param='qIDX=' + IDX + '&mode=delete&nNum='+ nNum;
	$.ajax({
	url:ajaxUrl,
	type:"POST",
	cache:false,
	data : param,
	dataType:"text",
	error:fnErrorAjax,
	success:function(_response)
	{
		v = _response.split("##");
		if(v[1]=="OK")
		{
			$("TR[id='cartList']").eq(v[2]).remove();
			
			try{
				fnGetTotalInfo();
			}catch(E){}
		}
		else
		{
			alert(_response);
			alert("오류가 발생하였습니다.");
		}
	}
	});
}


/*=======================================================================
상품목록에서 1개의 제품만 클릭해서 장바구니 담기.
일괄담기와 같은 프로세스로 진행이 되나, 함수 호출이 번거러울 것 같아
별도 함수 호출
=======================================================================*/
function fnCartIn(PIDX,obj)
{
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	nNum = $("img[id='" + myID + "']").index(obj);
	nCount = $("input[id='inPcount']").eq(nNum).val();		
	if(nCount<1)return;
	param="inIDX=" + PIDX + "&inCount=" + nCount;
	
	fnCheckCartIn('','','',param);
}
/*삭제 */
function fnCartIn2(PIDX,obj)
{
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	nNum = $("img[id='" + myID + "']").index(obj);
	nCount = $("input[id='inPcount']").eq(nNum).val();		
	if(nCount<1)return;
	param="inIDX=" + PIDX + "&inCount=" + nCount;
	
	fnCheckCartIn2('','','',param);
}
var isDrag = 0;
function fnCheckCartIn2(addId,stepCode,cartCode,qStr)
{
	param="";
	if(stepCode)
	{
		param="stepCode=" + stepCode + "&cartCode=" + cartCode;
		if(qStr)param+=qStr	;		
	}
	else if(qStr)
	{
		param=qStr;
	}
	else
	{
		obj = $("INPUT[id='inPcheck" + addId + "']");
		//countObj = $("INPUT[id='inPcount" + addId + "']");
		countObj = $("INPUT[id='inPcount']");
		len = obj.length;
		var inIDX ="";
		var inCount="";
		for(k=0;k<len;k++)
		{
			if(obj.eq(k).is(":checked"))
			{
				if(inIDX)inIDX+=",";
				inIDX+=obj.eq(k).val();
				
				if(countObj.eq(k).val())
				{
					if(inCount)inCount+=",";
					inCount+=countObj.eq(k).val();
				}
			}
		}

		if(!inIDX.length)
		{
			alert("Please select item");
			return;
		}
		param="inIDX=" + inIDX + "&inCount=" + inCount;
	}
		
	$.ajax({
		url:'/order/ajaxCartAddCheck.php',
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			v = _response.split("##");
			popupLoding=0;

			if(v[1]=="error")
			{
				fnErrorAjax();
				return;
			}
			else if(v[1]=="step2")
			{
				//-- 2 단계 Go(재고파악)
				fnCheckCartIn2('',2,v[2]);
				return;
			}
			else if(v[1]=="step3")
			{
				//-- 3 단계 Go(재고없는 제품 확인 팝업)
				//-- 재고 없는 제품 출력
				fnCartCheck2("stock",v[2]);
				return;
			}
			else if(v[1]=="step4")
			{
				//-- 4 단계 Go(옵션파악)
				fnCheckCartIn2('',4,v[2]);
				return;
			}
			else if(v[1]=="step5")
			{
				//-- 5 단계 Go(옵션팝업띄우기)
				fnCartCheck2("option",v[2]);
				return;
			}
			else if(v[1]=="step6")
			{
				fnCheckCartIn2('',6,v[2]);
				return;
			}
			else if(v[1]=="OK")
			{
				//-- 종료(장바구니에 담김)
				if(isDrag==0)
				{
					fnViewPopup("pop_04");
				}
				else
				{
					disablePopup();
					isDrag=0;
				}
				
				$("INPUT[id='inPcount']").val(1);
				return;
			}
			else
			{
				//-- 기타 메세지
				fnErrorAjax();
				isDrag=0;
				return;
			}
		}
	});
}
/*=======================================================================
체크한 상품 재고/옵션 체크
=======================================================================*/
function fnCartCheck2(t,cartCode)
{
	if(t=="stock") urlStr = "cartStockCheck_test.html"
	else if(t=="option") urlStr = "cartOptionCheck_test.html"

	param = "cartCode=" + cartCode;
	$.ajax({
		url:'/_Include/pop/' + urlStr,
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			loadPopup();
			jQuery("#popContent").html(popBox1 + _response + popBox2);
			fnPopupResize();
			centerPopup();
		}
	});
}//-- end function

/* 삭제 */


/*=======================================================================
체크한 상품 일괄 장바구니 담기
addId : 체크버튼 객체가 다를경우 이용
stepCode : 진행 단계
cartCode : 단계별로 들고다니는 장바구니 코드번호
PIDX : 제품 1게 장바구니에 담을때 고유번호
=======================================================================*/
var isDrag = 0;
function fnCheckCartIn(addId,stepCode,cartCode,qStr)
{
	param="";
	if(stepCode)
	{
		param="stepCode=" + stepCode + "&cartCode=" + cartCode;
		if(qStr)param+=qStr	;		
	}
	else if(qStr)
	{
		param=qStr;
	}
	else
	{
		obj = $("INPUT[id='inPcheck" + addId + "']");
		//countObj = $("INPUT[id='inPcount" + addId + "']");
		countObj = $("INPUT[id='inPcount']");
		len = obj.length;
		var inIDX ="";
		var inCount="";
		for(k=0;k<len;k++)
		{
			if(obj.eq(k).is(":checked"))
			{
				if(inIDX)inIDX+=",";
				inIDX+=obj.eq(k).val();
				
				if(countObj.eq(k).val())
				{
					if(inCount)inCount+=",";
					inCount+=countObj.eq(k).val();
				}
			}
		}

		if(!inIDX.length)
		{
			alert("Please select item");
			return;
		}
		param="inIDX=" + inIDX + "&inCount=" + inCount;
	}
		
	$.ajax({
		url:'/order/ajaxCartAddCheck.php',
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			v = _response.split("##");
			popupLoding=0;

			if(v[1]=="error")
			{
				fnErrorAjax();
				return;
			}
			else if(v[1]=="step2")
			{
				//-- 2 단계 Go(재고파악)
				fnCheckCartIn('',2,v[2]);
				return;
			}
			else if(v[1]=="step3")
			{
				//-- 3 단계 Go(재고없는 제품 확인 팝업)
				//-- 재고 없는 제품 출력
				fnCartCheck("stock",v[2]);
				return;
			}
			else if(v[1]=="step4")
			{
				//-- 4 단계 Go(옵션파악)
				fnCheckCartIn('',4,v[2]);
				return;
			}
			else if(v[1]=="step5")
			{
				//-- 5 단계 Go(옵션팝업띄우기)
				fnCartCheck("option",v[2]);
				return;
			}
			else if(v[1]=="step6")
			{
				fnCheckCartIn('',6,v[2]);
				return;
			}
			else if(v[1]=="OK")
			{
				//-- 종료(장바구니에 담김)
				if(isDrag==0)
				{
					fnViewPopup("pop_04");
				}
				else
				{
					disablePopup();
					isDrag=0;
				}
				
				$("INPUT[id='inPcount']").val(1);
				return;
			}
			else
			{
				//-- 기타 메세지
				fnErrorAjax();
				isDrag=0;
				return;
			}
		}
	});
}

function fnCheckCartInBest(addId,stepCode,cartCode,qStr)
{
	param="";
	if(stepCode)
	{
		param="stepCode=" + stepCode + "&cartCode=" + cartCode;
		if(qStr)param+=qStr	;
	}
	else if(qStr)
	{
		param=qStr;
	}
	else
	{
		obj = $("INPUT[id='inPcheck" + addId + "']");
		//countObj = $("INPUT[id='inPcount" + addId + "']");
		countObj = $("INPUT[id='inPcount']");
		len = obj.length;
		var inIDX ="";
		var inCount="";
		var j = 0;
		for(k=0;k<len;k++)
		{
			j = k + 10;
			if(obj.eq(k).is(":checked"))
			{
				if(inIDX)inIDX+=",";
				inIDX+=obj.eq(k).val();

				if(countObj.eq(j).val())
				{
					if(inCount)inCount+=",";
					inCount+=countObj.eq(j).val();
				}
			}
		}

		if(!inIDX.length)
		{
			alert("Please select item");
			return;
		}
		param="inIDX=" + inIDX + "&inCount=" + inCount;
	}

	$.ajax({
		url:'/order/ajaxCartAddCheck.php',
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			v = _response.split("##");
			popupLoding=0;

			if(v[1]=="error")
			{
				fnErrorAjax();
				return;
			}
			else if(v[1]=="step2")
			{
				//-- 2 단계 Go(재고파악)
				fnCheckCartIn('',2,v[2]);
				return;
			}
			else if(v[1]=="step3")
			{
				//-- 3 단계 Go(재고없는 제품 확인 팝업)
				//-- 재고 없는 제품 출력
				fnCartCheck("stock",v[2]);
				return;
			}
			else if(v[1]=="step4")
			{
				//-- 4 단계 Go(옵션파악)
				fnCheckCartIn('',4,v[2]);
				return;
			}
			else if(v[1]=="step5")
			{
				//-- 5 단계 Go(옵션팝업띄우기)
				fnCartCheck("option",v[2]);
				return;
			}
			else if(v[1]=="step6")
			{
				fnCheckCartIn('',6,v[2]);
				return;
			}
			else if(v[1]=="OK")
			{
				//-- 종료(장바구니에 담김)
				if(isDrag==0)
				{
					fnViewPopup("pop_04");
				}
				else
				{
					disablePopup();
					isDrag=0;
				}

				$("INPUT[id='inPcount']").val(1);
				return;
			}
			else
			{
				//-- 기타 메세지
				fnErrorAjax();
				isDrag=0;
				return;
			}
		}
	});
}

/*=======================================================================
체크한 상품 재고/옵션 체크
=======================================================================*/
function fnCartCheck(t,cartCode)
{
	if(t=="stock") urlStr = "cartStockCheck.html"
	else if(t=="option") urlStr = "cartOptionCheck.html"

	param = "cartCode=" + cartCode;
	$.ajax({
		url:'/_Include/pop/' + urlStr,
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			loadPopup();
			jQuery("#popContent").html(popBox1 + _response + popBox2);
			fnPopupResize();
			centerPopup();
		}
	});
}//-- end function

/*--------------------------------
팝업 닫기
--------------------------------*/
function fnClosePopup()
{
	disablePopup();
}

/*--------------------------------
팝업 열기
--------------------------------*/
function fnViewPopup(t,param)
{
	var urlStr = "/_Include/pop/" + t + ".html";
	$.ajax({
		url:urlStr,
		type:"POST",
		data : param,
		cache:false,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			loadPopup();
			divPop = popBox1 + _response + popBox2;
			$("#popContent").html(divPop);
			fnPopupResize();
			centerPopup();
		}
	});
	return;
}

function fnPopupResize()
{
	var h = $("#popContent").height();
	var w = $("#popContent").width();
	$("#popupContact").css({
		"position": "absolute",
		"width": w,
		"height": h
	});	
}


/*=============================================================================
	즐겨찾기
=============================================================================*/
function fnInFavorite(FAtype,idx)
{
	var param="qIDX=" + idx + "&inFAtype=" + FAtype;
	$.ajax({
		url:'/product/ajaxAddFavorite.php',
		type:"POST",
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			if(_response=="##overlap##")
			{
				//-- 이미 존재함
				alert("Already been added");
				return;
			}
			else if(_response=="##error##")
			{
				fnErrorAjax();
			}
			else if(_response=="##productOK##")
			{
				fnViewPopup("pop_05");
			}
			else if(_response=="##brandOK##")
			{
				fnViewPopup("pop_06");
			}
		}
	});
}

function fnInFavorite2(FAtype, idx) {
    var param = "qIDX=" + idx + "&inFAtype=" + FAtype;

    $.ajax({
        url: '/product/ajaxAddFavorite.php',
        type: "POST",
        data: param,
        dataType: "text",
        error: fnErrorAjax,
        success: function (_response) {
            if (_response == "##overlap##") {
                // 이미 존재함
                alert("Already been added");
                return;
            } else if (_response == "##error##") {
                fnErrorAjax();
            } else if (_response == "##productOK##" || _response == "##brandOK##") {
                // 하트 채우기
                var favoriteIcon = document.getElementById("favorite-icon-" + idx);
                if (favoriteIcon) {
                    favoriteIcon.innerHTML = '<a href="#" onClick="javascript:fnRemoveFavorite(2, \'' + idx + '\'); return false;">♥ Favorited</a>';
                }

                // 팝업 표시
                if (_response == "##productOK##") {
                    fnViewPopup("pop_05");
                } else if (_response == "##brandOK##") {
                    fnViewPopup("pop_06");
                }
            }
        }
    });
}

/*=============================================================
String 객체의 문자열 모두 변환하기 (기본 replace 함수는 1회 1단어 변환)
=============================================================*/
String.prototype.replaceAll = function( searchStr, replaceStr )
{
	var temp = this;
	while( temp.indexOf( searchStr ) != -1 )
	{
		temp = temp.replace( searchStr, replaceStr );
	}
	return temp;
}

/*===============================================================================================
	EmailCheck
===============================================================================================*/
function checkEmail(obj)
{
	v = obj.id.replace("2_SELECT","") + "2";
	t = document.getElementById(v);
	try{
		t_obj = t;
	}catch(E){
		alert( "[ " + v + " ] 라는 객체가 존재하지 않습니다.");
	}
	if(obj.selectedIndex > 0){
		t_obj.value = obj.value;
		t_obj.readOnly=true;
	}else{
		t_obj.value = "";
		t_obj.readOnly=false;
		t_obj.focus();
	}
}

/*----------------------------------------------------------------
숫자 + 영문만 입력 가능
----------------------------------------------------------------*/
function onlyNumEng()
{
	var inChar = String.fromCharCode(event.keyCode);
	code = parseFloat(event.keyCode);
		
	//- 우측 키패드 numpad keycode값 96 ~ 105    Tab : 9   BackSpace : 8  Delete : 46    왼쪽 : 37   오른쪽 : 39
	if((code==32 || code==13) && !(code==8 || code==9 || code==46 || code==37 || code==39 || (code>=96 && code<=105)) || code==229)
	{
		event.returnValue = false;
	}
	else
	{
		event.returnValue = true;
	}
	return;
}
		
function isTelNumPasteCheck(obj) {
	if (isCtrl && event.keyCode == 86) {
		try{
			var clip = window.clipboardData.getData("Text");
			tmp = clip.replaceAll(" ","").split("-");
			isNum=1;
			len = tmp.length;
			
			if(len == 3) {
				//-- 숫자형식인지 검사
				for(k=0;k<len;k++) {
					if(isNaN(tmp[k])) {
						isNum=0;
						break;	
					}        	
				}
			  
				if(isNum)	{
					//-- 객체 구하기
					objID = obj.id.substr(0,obj.id.length-1);		        	
					for(k=1;k<=3;k++) {
						targetObj = document.getElementById(objID + k);
						targetObj.value = tmp[k-1];
					}
				}
			}
		//alert(clip);
		}catch(ex){}
	}
}

function ShowLayer(obj_id, opt, left, top){
	var oDiv = document.getElementById(obj_id);
	var oStyle = oDiv.style;
	oStyle.display = (opt) ? 'inline' : 'none';
	if (left > 0) oStyle.left = left;
	if (top> 0) oStyle.top = top;
}
function ShowHideLayer (obj_id,opt){
	document.getElementById(obj_id).style.display = (opt) ? 'inline' : 'none';
}

function fileDownload(fcode,tbl,fldCode,qIDX,fNum)
{
	window.open("/_Include/fileDownload.php?fcode=" + fcode + "&fldCode=" + fldCode + "&tbl=" + tbl + "&qIDX=" + qIDX + "&fNum=" + fNum,"_blank");
	return;
}

function fileDown(fcode,tbl,fldCode,qIDX,fNum)
{
	window.open("/_Include/fileDownload.php?fcode=" + fcode + "&fldCode=" + fldCode + "&tbl=" + tbl + "&qIDX=" + qIDX + "&fNum=" + fNum,"_self");
	return;
}

function fileDown2(fcode,tbl,fldCode,qIDX,fNum)
{
	window.open("/_Include/fileDown2.php?fcode=" + fcode + "&fldCode=" + fldCode + "&tbl=" + tbl + "&qIDX=" + qIDX + "&fNum=" + fNum,"_self");
	return;
}
/*=======================================================================
전체 선택 / 해제
=======================================================================*/
function allCheck(t)
{
	var frm=document.formObject;
	var elementcnt = frm.elements.length;
	for(i=0;i<elementcnt;i++) {
		if(frm.elements[i].type=="checkbox" && frm.elements[i].name=="delCheck[]")frm.elements[i].checked = t;
	}
}

/*=======================================================================
전체 선택 삭제
=======================================================================*/
function checkDel()
{
	var frm=document.formObject;
	var chkcount=0;
	var elementcnt = frm.elements.length;
	for(i=0;i<elementcnt;i++) {
		if(frm.elements[i].type=="checkbox" && frm.elements[i].name=="delCheck[]" && frm.elements[i].checked)chkcount++;
	}
	if(chkcount<1){
		alert("삭제하실 항목을 선택하여 주십시오");
		return;
	}
	if(confirm("선택하신 항목을 삭제하시겠습니까?")){
		frm.submit();
	}
}
/*=======================================================================
전체 선택 변경
=======================================================================*/
function checkEdt()
{
	var frm=document.formObject;
	var chkcount=0;
	var elementcnt = frm.elements.length;
	for(i=0;i<elementcnt;i++) {
		if(frm.elements[i].type=="checkbox" && frm.elements[i].name=="delCheck[]" && frm.elements[i].checked)chkcount++;
	}
	if(chkcount<1){
		alert("변경하실 항목을 선택하여 주십시오");
		return;
	}	
	if(confirm("선택하신 항목을 변경하시겠습니까?")){
		frm.submit();
	}
}

/*=======================================================================
한개만 선택 삭제
=======================================================================*/
function fnDataGoDelete(qIDX)
{	
	if(confirm("정말 삭제 하시겠습니까?")){
		var frm=document.formObject;
		var chkcount=0;
		var elementcnt = frm.elements.length;

		allCheck(false);

		var d = document.createElement("input");
		d.type = "text";
		d.value = qIDX;
		d.name = "delCheck[]";
		frm.appendChild(d);
		frm.submit();
	}
}

function fnDataGoDelete1(qIDX)
{

	if(confirm("정말 취소 하시겠습니까?")){
		var frm=document.formObject;
		var chkcount=0;
		var elementcnt = frm.elements.length;

		allCheck(false);

		var d = document.createElement("input");
		d.type = "text";
		d.value = qIDX;
		d.name = "delCheck[]";
		frm.appendChild(d);
		frm.submit();
	}
}

function goExcel(){
	var frm=document.formObject;
	var chkcount=0;
	var elementcnt = frm.elements.length;
	for(i=0;i<elementcnt;i++) {
		if(frm.elements[i].type=="checkbox" && frm.elements[i].name=="delCheck[]" && frm.elements[i].checked)chkcount++;
	}
	
	if(chkcount<1){
		alert("엑셀 다운로드할 항목을 선택하여 주십시오");
		return;
	}
	frm.submit();
}

/*=============================================================================
주문 메모 불러오기
=============================================================================*/
function fnOpenMemo(qIDX,t,p,ad){
	var param = "stepCode=list&OMtype=" + t + "&page=" + p + "&OIDX=" + qIDX;
	if(ad) param+="&isAdminMode=1";
	
	$.ajax({
		url:"/_Include/ajax/ajaxOrderMemo.php",
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response){
			v = _response.split("##");
			popupLoding=0;
			if(v[1]=="error"){
				fnErrorAjax();
				return;
			} else if(v[1]=="SaveOK") {
			} else if(v[1]=="DeletedOK") {
			} else {
				//-- 기타 메세지 (높이 100으로 고정)
				loadPopup();
				jQuery("#popContent").html(popBox1 + _response + popBox2);
				fnPopupResize();
				centerPopup();
				//$("#popupContact").css("top",100);
				return;
			}
		}
	});
}

/*=============================================================================
	송장번호 조회
=============================================================================*/
function viewDeliveryCode(t,code){	
	if(t==1) {
		//== 대한통운
		winstr = "https://trace.cjlogistics.com/next/tracking.html?wblNo=" + code;
		window.open(winstr,"_blank");
	} else if(t==4)	{
		//== 한진택배
		winstr = "http://www.hanjin.co.kr/Delivery_html/inquiry/result_waybill.jsp?wbl_num=" + code;
		window.open(winstr,"_blank");
	} else if(t==5)	{
		//== 현대택배
		winstr = "http://www.hlc.co.kr/personalService/tracking/06/tracking_goods_result.jsp?InvNo=" + code;
		window.open(winstr,"_blank");
	} else if(t==6)	{
		//== KGB택배
		winstr = "http://www.kgbls.co.kr/sub5/trace.asp?f_slipno=" + code;
		window.open(winstr,"_blank");
	} else 	{
		alert("바로조회 서비스가 준비중 입니다.\n\n해당 관련 홈페이지에서 직접 조회해주시기 바랍니다.");
	}
}

/*=======================================================================
엔터키에 따른 액션 제어
nOjb = 엔터키 입력시 focus 줄 객체 id
nScript = 엔터키 입력시 실행할 스크립트
=======================================================================*/
function fnEnterAction(nObj,nScript){
	if(event.keyCode!=13)return;
	var frm = document.formObject;
	if(nObj) {
		try{
			obj = document.getElementById(nObj);
			obj.focus();
		}catch(E){}
		try{
			frm[nObj].focus();
		}catch(E){}
	}

	if(nScript) eval(nScript);
}
/*=======================================================================
관리자모드 수정 페이지로 가기 (다른곳에서도 이용될수 있슴)
=======================================================================*/
function goPageMode(m,qIDX) {
	if(!m)return;
	addStr="";
	if(m=="list")	{
		if(isPop) { self.close(); return; }
		p = listPageUrl;
	} else if(m=="add") {
		p = editPageUrl;
	} else if(m=="edit") {
		p = editPageUrl;
		addStr = "&qIDX=" + qIDX;
	}
	document.location=p+"?path="+path+addStr;
}

/*=============================================================================
	입고알람
=============================================================================*/
function fnAddAlram(qIDX,addParam)
{
	if(addParam) var param = addParam;
	else var param="qIDX=" + qIDX;

	$.ajax({
		url:'/product/ajaxAddAlarmNew.php',
		type:"POST",
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			var v = _response.split("##");
			
			if(v[1]=="OK")
			{
				alert("We will answer as soon as possible.");
				fnClosePopup();
				return;
			}
			else if(v[1]=="noMember")
			{
				alert("로그인 정보가 없습니다.\n\n로그인을 하지 않으셨다면 로그인 후 다시 이용해 주세요.");
				fnClosePopup();
				return;
			}
			else if(v[1]=="over")
			{
				alert("재고 알람신청은 하루 20건까지 신청이 가능합니다.");
				fnClosePopup();
				return;
			}
			else
			{							
				loadPopup();
				divPop = _response;
				$("#popContent").html(divPop);
				fnPopupResize();
				centerPopup();
			}				
		}
	});		
}

function fnAjaxSearchClose(){
	//resutlTbl = document.getElementById("ajaxResultTBL");
	try
	{
		resutlTbl = document.getElementById("ajaxResultDIV");
		resutlTbl.parentNode.removeChild(resutlTbl);
	}
	catch(E){}

	return;
}

/*=======================================================================
기본 AJAX 검색 ( Jquery 사용 / 버튼 객체의 바로 하단에 검색결과 출력 )
obj : 클릭한 결과를 입력할 객체들 구분자 |
      ex)    txtbox1|txtbox2|txtbox3....
			주의)  입력객체 수와 ajax 처리 파일이서 넘겨주는 결과값이 일치할 것.
btn  : 검색 버튼 객체
fName : ajax 처리 파일 명  (기본경로  홈/_Include/ajax/ )
=======================================================================*/
var dataInputObj;
var ajaxFileObj;
function fnAjaxSearchInit(obj,btn,fName){
	if(obj) {
		dataInputObj = obj.split("|");
	} else {
		alert("호출 오류!");
		return;
	}

	if(fName) ajaxFileObj=fName;
	//-- 결과 테이블 객체 구하기
	try
	{
		//==== 개체를 찾았다면 보임/숨김 처리 ===============================
		resutlTbl = document.getElementById("ajaxResultTBL");
		if(resutlTbl.style.display=="block")
		{
			fnAjaxSearchClose();
			return;
		}
		else
		{
			//resutlTbl.style.display="block";
		}

		//==== 개체를 찾았다면 보임/숨김 처리 끝===============================
	}
	catch(E)
	{
		//==== 개체를 찾을수 없다면 생성 ==================================

		//-- div 객체 하나 생성
		var ajaxDiv = document.createElement("div");
		ajaxDiv.style.zIndex=9999999999;
		ajaxDiv.style.position="absolute";
		ajaxDiv.style.left= event.screenX - 200;
		sc = document.documentElement.scrollTop;
		if(!sc)sc = document.body.scrollTop;
		//alert(sc + " / " + window.event.screenY);
	
		ajaxDiv.style.top= event.screenY + sc - 100;
		
		str = "<table cellspacing=0 cellpadding=0 style='width:600px;height:100px;border:3px solid #dfdfdf;position:absolute;background-Color:white;' id='ajaxResultTBL' style='display:block;'>";
		str = str + "<tr></tr>";
		str = str + "<tr>";
		str = str + "<td height=30 style='background-color:#efefef;' align=center width=550><b></b></td>";
		str = str + "<td height=10 style='background-color:#efefef;cursor:pointer;' align=center onclick='fnAjaxSearchClose();' ><b><font size=4>X</font></b></td>";
		str = str + "</tr>";
		str = str + "<tr><td  colspan=2 id='ajaxResultTD' ></td></tr>";
		str = str + "</table>";

		ajaxDiv.id='ajaxResultDIV';

		ajaxDiv.innerHTML = str;
		
		document.body.appendChild(ajaxDiv);
		//pObj.insertBefore(ajaxDiv,pObj.firstChild);
		//==== 개체를 찾을수 없다면 생성 끝 ==================================
	}
	
		//== 좌표조절 ===================
		divObj = jQuery("#ajaxResultDIV");
		resutlTbl = document.getElementById("ajaxResultTBL");
		var rect = btn.getBoundingClientRect();
		w = parseFloat(resutlTbl.style.width.replace("px",""));
		l=rect.left - (w/2);
 		t=rect.top;
 		
 		st = document.documentElement.scrollTop;
 		
 		//-- 객체가 100 px 이상 넘어가버리면 18로 강제로 줘버린다. (높이구하는게 왜이래?);
 		objHeight = btn.height;
 		if(!objHeight || objHeight>=100)objHeight = 18;
 		
 		//================================
 		//alert(ajaxDiv.style.width);
	 	if(l || t)
	 	{
	 		divObj.css("left",l + "px");
			divObj.css("top",(t+st+objHeight) + "px");
	 	}
	 	
	 	//alert(btn.tagName + " / " + btn.height + " / " + btn.style.height + " / " + btn.className);
	 	//alert(t + " / " + st + " / " + objHeight + " / " + (t+st+objHeight));
	
		fnAjaxSearchAction();
}

function fnAjaxSearchAction(objID){
	val = jQuery("#" + objID).val();
	var param = "";

	if(val)param=param+"inKeyword=" + val;
	jQuery("#ajaxResultTD").html("<center><img src='/image_1/ajax-loader.gif'>");
	
	$.ajax({
		url:'/_Include/ajax/' + ajaxFileObj,
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			jQuery("#ajaxResultTD").html(_response);jQuery("#inKeyword").focus();	}
	});
}

function fnAjaxSearchSelected(ajaxData,focusObj)
{
	try
	{
		ajaxData=ajaxData.split("#$#");

		for(k=0;k<dataInputObj.length;k++)
		{
			jQuery("#" + dataInputObj[k]).val(ajaxData[k]);
		}
		fnAjaxSearchClose();

	}catch(E)
	{
		alert("객체를 찾을 수 없습니다.");
	}
}

function fnAjaxSearchClose()
{
	//resutlTbl = document.getElementById("ajaxResultTBL");
	try
	{
		resutlTbl = document.getElementById("ajaxResultDIV");
		resutlTbl.parentNode.removeChild(resutlTbl);
	}
	catch(E){}

	return;
}

function fnInFavoriteNew(idx, qMode)
{
	var param="qIDX=" + idx + "&qMode=" + qMode;
	$.ajax({
		url:'/product/ajaxAddFavoriteNew.php',
		type:"POST",
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			if(_response=="##overlap##")
			{
				//-- 이미 존재함
				alert("It is already added to wishlist");
				return;
			}
			else if(_response=="##error##")
			{
				fnErrorAjax();
			}
			else if(_response=="##productOK##")
			{
				console.log('wishlist added');
			}
			else if(_response=="##DELETED##")
			{
				console.log('wishlist deleted');
			}
			else if(_response=="##brandOK##")
			{
				fnViewPopup("pop_06");
			}
			else if(_response=="##NOLOGIN##")
			{
				alert("Please login first.");
			}
		}
	});
}
function toggleFavorite(element, qIDX) {
	const img = element.querySelector('img');
	if (!img) return;

	if (img.src.indexOf('ico-wishlist_added.svg') !== -1) {
		img.src = '/img/ico/ico-wishlist.svg';
        fnInFavoriteNew(qIDX, 'delete');
	} else {
		img.src = '/img/ico/ico-wishlist_added.svg';
        fnInFavoriteNew(qIDX, 'add');
	}	  
}

function ajaxLogin() {
	if (document.getElementById("inMID").value.length < 1) {
		alert("Please enter user id");
		document.getElementById("inMID").focus();
		return;
	}

	if (document.getElementById("inMPW").value.length < 1) {
		alert("Please enter user password");
		document.getElementById("inMPW").focus();
		return;
	}
	
	document.frmLogin.submit();
}

