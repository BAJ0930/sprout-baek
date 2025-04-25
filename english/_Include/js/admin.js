
/*============================================
상단메뉴 롤오버시 서브메뉴 출력 제어 함수
============================================*/
function subView(num,menu)
{
	
	for(k=0;k<=15;k++)
	{
		try
		{
			objMain = document.getElementById("adminTopMenu" + k);
			objSub = document.getElementById("adminTopSub" + k);
			if(k==num)
			{
				objSub.style.display = "block";
				off = menu.offsetLeft;
				if(objSub.offsetWidth)w = objSub.offsetWidth;
				else if(objSub.style.offsetWidth)w = objSub.style.offsetWidth;
				else if(objSub.style.pixelWidth)w = objSub.style.pixelWidth;
				
				//-- 5번 메뉴 이상은 우측 정렬
				if(k>=4)
				{
					off = parseInt(menu.offsetLeft) - parseInt(w) + 93;										
					if(off<0)off=10;
					if(w<=96)off= off - parseInt((96-w)/2);
				}
				else
				{
					//-- 크기가 작으면 가운데 정렬
					if(w<=96)off= off + parseInt((96-w)/2);
				}
				objMain.className="adminTopMenuOver";
				objSub.style.marginLeft = off;
			}
			else
			{
				objSub.style.display = "none";
				objMain.className="adminTopMenu";
			}
			
		}
		catch(E)
		{
			
		}
	}
}


function subView2(num,menu)
{
	
	for(k=0;k<=15;k++)
	{
		try
		{
			objMain = document.getElementById("adminTopMenu" + k);
			objSub = document.getElementById("adminTopSub" + k);
			if(k==num)
			{
				objSub.style.display = "block";
			}
			else
			{
				objSub.style.display = "none";
			}
			
		}
		catch(E)
		{
			
		}
	}
}

function fnAdminMenuOver(num,mod)
{
	obj = document.getElementById("adminTopIcon" + num);
	
	if(mod==1)
	{
		obj.src=obj.src.replace(".gif","Over.gif");
	}
	else
	{
		obj.src=obj.src.replace("Over.gif",".gif");
	}
	
}

/*============================================
Data List 클릭시 수정 페이지로 이동 함수
============================================*/
function fnDataGoEdit(qIDX)
{
	//document.location=?ditPageUrl + "?path=" + path + "&qIDX=" + qIDX;
	//window.open(editPageUrl + "?isPop=1&path=" + path + "&qIDX=" + qIDX,"_blank","'scrollbars=no,toolbar=no,location=no,resizable=no,status=no,menubar=no,resizable=no");
	window.open(editPageUrl + "?isPop=1&qIDX=" + qIDX,"_blank");
}
/*============================================
Data List 클릭시 수정 페이지로 이동 함수
============================================*/
function fnDataGoEdit2(qIDX)
{
	//document.location=?ditPageUrl + "?path=" + path + "&qIDX=" + qIDX;
	//window.open(editPageUrl + "?isPop=1&path=" + path + "&qIDX=" + qIDX,"_blank","'scrollbars=no,toolbar=no,location=no,resizable=no,status=no,menubar=no,resizable=no");
	window.open("productEdit.php?isPop=1&qIDX=" + qIDX,"_blank");
}
/*============================================
Data List 클릭시 수정 페이지로 이동 함수
============================================*/
function fnDataGoEdit3(qIDX)
{
	//document.location=?ditPageUrl + "?path=" + path + "&qIDX=" + qIDX;
	//window.open(editPageUrl + "?isPop=1&path=" + path + "&qIDX=" + qIDX,"_blank","'scrollbars=no,toolbar=no,location=no,resizable=no,status=no,menubar=no,resizable=no");
	window.open("productOrderEdit.php?qIDX=" + qIDX,"_blank");
}

/*============================================
Data List 클릭시 수정 페이지로 이동 함수
============================================*/
function fnDataGoEdit4(qIDX,qReturn)
{
	//document.location=?ditPageUrl + "?path=" + path + "&qIDX=" + qIDX;
	//window.open(editPageUrl + "?isPop=1&path=" + path + "&qIDX=" + qIDX,"_blank","'scrollbars=no,toolbar=no,location=no,resizable=no,status=no,menubar=no,resizable=no");
	//window.open(editPageUrl + "?isPop=1&path=" + path + "&qIDX=" + qIDX,"_blank");
	if(qReturn == 1){
		window.open("productOrderReturnEdit.php?isPop=1&qIDX=" + qIDX,"_blank");
	} else {
		window.open("productOrderEdit.php?isPop=1&qIDX=" + qIDX,"_blank");
	}
}

/*============================================
Data List 클릭시 수정 페이지로 이동 함수 - 복사 용도 -
============================================*/
function fnDataGoCopy(qIDX)
{
	document.location=editPageUrl + "?qIDX=" + qIDX + "&isCopy=1";
}


/*=============================================
	검색처리
=============================================*/
function fnAdminSearch()
{
	p = document.location.pathname;
	
	searchStr = "path=" + path + "&page=1";
	
	for(k=1;k<=15;k++)
	{
		obj = jQuery("#s" + k);
		objType = obj.attr("tagName");
		
		if(objType=="INPUT" || objType=="SELECT")
		{
			//-- 일반 텍스트
			sVal = obj.val();			
			sVal = sVal.replaceAll("'","");
			sVal = sVal.replaceAll('"',"");			
			searchStr+= "&s" + k + "=" + encodeURIComponent(sVal);
		}
	}//-- end For

	document.location=p + "?" + searchStr;
	
}


/*=============================================
	검색처리
=============================================*/
function fnAdminSearchPay()
{
	p = document.location.pathname;
	
	searchStr = "path=" + path + "&page=1";
	
	for(k=1;k<=15;k++)
	{
		obj = jQuery("#s" + k);
		objType = obj.attr("tagName");
		
		if(objType=="INPUT" || objType=="SELECT")
		{
			//-- 일반 텍스트
			sVal = obj.val();			
			sVal = sVal.replaceAll("'","");
			sVal = sVal.replaceAll('"',"");			
			if(k == 11){
				if(document.getElementById("s11").checked == true){
					searchStr+= "&s" + k + "=" + encodeURIComponent(sVal);
				} else {
					searchStr+= "&s" + k + "=";
				}
			} else if(k == 7){
				if(document.getElementById("s7").checked == true){
					searchStr+= "&s" + k + "=" + encodeURIComponent(sVal);
				} else {
					searchStr+= "&s" + k + "=";
				}
			} else {
				searchStr+= "&s" + k + "=" + encodeURIComponent(sVal);
			}
		}
	}//-- end For
	
	document.location=p + "?" + searchStr;
	
}

/*=============================================
	엑셀 처리
=============================================*/
function fnAdminExcel()
{
	
}


/*=============================================
	정렬 처리 함수
=============================================*/
function fnAdminOrder(o1,o2)
{
	p = document.location.pathname;	
	qStr = "path=" + path + "&page=1";
	qStr+= "&order1=" + o1 + "&order2=" + o2;
	document.location=p + "?" + qStr;
}

/*=============================================
	테이블 정렬기능 붙이기 fNum : td번호 / fName : 필드명
=============================================*/
function setOrderBy(fNum,fName)
{
	tdObj = $("TD.adminDataHeadTD");
	txt = tdObj.eq(fNum).html();
	
	var orderBtn1 = "/image_2/icon_08.gif";
	var orderBtn1_1 = "/image_2/icon_08_1.gif";
	var orderBtn2 = "/image_2/icon_09.gif";
	var orderBtn2_1 = "/image_2/icon_09_1.gif";
	
	o = nOrder.split(" ");
	if(o[0]==fName&&o[1]=="ASC")orderBtn1 = orderBtn1_1;
	else if(o[0]==fName&&o[1]=="DESC")orderBtn2 = orderBtn2_1;
	
	
	
	newTxt = "<table><tr><td rowspan=2 style='padding-right:5px;' class='adminHEADTD'>" + txt + "</td><td><img src='" + orderBtn1 + "' style='cursor:pointer;display:block;' onclick=\"fnAdminOrder('" + fName + "','ASC')\"></td></tr>";
	newTxt+= "<tr><td ><img src='" + orderBtn2 + "'  style='cursor:pointer;margin-top:2px;display:block;' onclick=\"fnAdminOrder('" + fName + "','DESC')\"></td></tr></table>";
	
	tdObj.eq(fNum).html(newTxt);
}


/*=============================================
	회원정보보기
=============================================*/
	function fnViewMember(mid,shop)
	{
		if(shop=="undefined" || !shop)shop="";
		window.open("../Member/memberEdit.php?isPop=1&qMID=" + mid + "&shopCode=" + shop,"Minfo","width=800,height=800,scrollbars=yes");
	}
	

/*=============================================
	엑셀변환페이지로 링크 
=============================================*/
	function fnConvertExcel(nPage , sql)
	{
		//document.location="orderExcel.php?qSql=<?=base64_encode($pg->result_sql)?>";
		nPage = nPage.replace(".php","");
		if(nPage.substr(0,5)=="order")nPage="order";
		
		window.open("../convertExcel/" + nPage + "Excel.php?qSql=" + sql);
	}
	function fnConvertExcel2(nPage , sql)
	{		
		window.open("../convertExcel/" + nPage + "Excel.php?qSql=" + sql);
	}
	function fnConvertExcelManager(nPage , sql, year, month)
	{
		//document.location="orderExcel.php?qSql=<?=base64_encode($pg->result_sql)?>";
		nPage = nPage.replace(".php","");
		
		window.open("../convertExcel/" + nPage + "Excel.php?qSql=" + sql + '&dateYear=' + year + '&dateMonth=' + month);
	}

/*=============================================
	체크한 항목만 엑셀변환페이지로 링크 
=============================================*/	
	function fnCheckConvertExcel(nPage , sql)
	{		
		
		chkObj = $("INPUT[name='delCheck[]']:checked");
		chkLen = chkObj.length;
		
		if(!chkLen)
		{
			alert("체크된 항목이 없습니다.");
			return;
		}
		
		if(nPage.substr(0,5)=="order")nPage="order";
		
		//-- IDX 값 조합
		qIDX="";
		for(k=0;k<chkLen;k++)
		{
			if(qIDX)qIDX+=",";
			qIDX+= chkObj.eq(k).val();
		}
		
		
		nPage = nPage.replace(".php","");
		window.open("../convertExcel/" + nPage + "Excel.php?qSql=" + sql + "&qIDX=" + qIDX);
	}
	


	/*=============================================
	체크한 항목만 엑셀등록 링크 
	=============================================*/	
	function fnCheckConvertExcelD(nPage)
	{		
		
		chkObj = $("INPUT[name='delCheck[]']:checked");
		chkLen = chkObj.length;
		
		if(!chkLen)
		{
			alert("체크된 항목이 없습니다.");
			return;
		}
		
		//-- IDX 값 조합
		qIDX="";
		for(k=0;k<chkLen;k++)
		{
			if(qIDX)qIDX+=",";
			qIDX+= chkObj.eq(k).val();
		}
		
		
		nPage = nPage.replace(".php","");
		window.open("../convertExcel/" + nPage + "Excel.php?qIDX=" + qIDX,"newD", "width=300, height=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no");
	}
	


	


	
	
	
	/*=============================================
	객체 줄 전체 효과
=============================================*/
	function fnSetRow(nObj,t)
	{
		
		TR = nObj.parentNode.parentNode;
		
		len = TR.childNodes.length;
		
		if(t==1)
		{
			bColor="#A4EDFF";
		}
		else if(t==2)
		{
			bColor="#FFFFFF";
		}
		
		
		
		for(k=0;k<len;k++)
		{
			try
			{
				
				TDOBJ = TR.childNodes(k);
				
				if(TDOBJ.tagName=="TD")	
				{
					TDOBJ.style.backgroundColor=bColor;
				}
			}
			catch(E)
			{
				break;
			}
		}	
	}		



/*=============================================
	주문서 상세보기
=============================================*/
function fnOpenOrder(idx)
{
	w = window.screen.width - 100;
	h = window.screen.height - 100;
	
	if(w>=1024)w=1100;

	window.open("../Order/orderEditPopup.php?qIDX=" + idx,"_blank","width=" + w + ",height=" + h + ",scrollbars=yes,top=0,left=0,resize=yes");
	return;
}

function fnOpenOrderReturn(idx)
{
	w = window.screen.width - 100;
	h = window.screen.height - 500;
	
	if(w>=1024)w=1100;

	window.open("../Order/orderEditPopupReturn.php?qIDX=" + idx,"_blank","width=" + w + ",height=" + h + ",scrollbars=yes,top=0,left=0,resize=yes");
	return;
}
function fnOpenOrderMember(strQuery,qMID)
{
	w = window.screen.width - 100;
	h = window.screen.height - 500;
	
	if(w>=1024)w=1100;

	window.open("../Order/orderMember.php?qSTR=" + strQuery + "&qMID=" + qMID,"_blank","width=" + w + ",height=" + h + ",scrollbars=yes,top=0,left=0,resize=yes");
	return;
}
/* 근태관리 인쇄 */
function fnOpenINOUT(s1,s2,s3)
{
	w = window.screen.width - 100;
	h = window.screen.height - 100;
	
	if(w>=1024)w=1100;

	window.open("../Temp/inPopup.php?s1=" + s1 + "&s2=" + s2 + "&s3=" + s3,"_blank","width=" + w + ",height=" + h + ",scrollbars=yes,top=0,left=0,resize=yes");
	return;
}

/*=============================================
	관리자모드 링크 칸 색상 처리
=============================================*/
var tmpColor='';
function fnTDOverOut(obj,st)
{
	var tmpColor='';
	overColor="#A4EDFF";	
	if(st==1)
	{
		tmpColor = obj.style.backgroundColor;
		obj.style.backgroundColor = overColor;
	}
	else
	{
		obj.style.backgroundColor = tmpColor;
	}
	return;
}


function fnTROverOut(obj,st)
{
	var tmpColor='';
	overColor="#EFEFEF";
	
	var chLen = obj.childNodes.length;
	
	for(k=0;k<chLen;k++)
	{
		var chObj = obj.childNodes[k];
		
		if(st==1)
		{
			tmpColor = chObj.style.backgroundColor;
			chObj.style.backgroundColor = overColor;
		}
		else
		{
			chObj.style.backgroundColor = tmpColor;
		}
		
	}
	return;
}



function fnCheonyuINOUT(str1,str2){
	if(str1 == "IN"){
		var memo = str2 + "님 업무를 시작하시겠습니까?";
	} else if(str1 == "OUT"){
		var memo = str2 + "님 업무를 종료하시겠습니까?";
	}
	if (confirm(memo) == true){

		param = "qMODE=" + str1;	
		$.ajax({
			url:'/_Include/ajax/ajaxCheonyuINOUT.php',
			type:"POST",
			cache:false,
			data : param,
			dataType:"text",
			error:fnErrorAjax,
			success:function(_response){
				v = _response.split("##");
				if(v[1]=="OK"){
					if(v[2] == "ALREADYIN"){
						alert('오늘은 이미 업무시작 클릭 하셨어요.');
						return;
					} else if(v[2] == "ALREADYOUT"){
						alert('오늘은 이미 업무종료 클릭 하셨어요.');
						return;
					} else {
						document.location.reload();
					}
				} else {
					alert('오류가 발생하였습니다.');
					return;
				}
			}
		});

	}
}


function fnCheonyuMEMO(str1, str2, str3, str4, str5){
	param = "qMODE=" + str1 + "&qIDX=" + str2 + "&str1=" + str3 + "&str2=" + str4 + "&str3=" + str5;	
	$.ajax({
		url:'/_Include/ajax/ajaxCheonyuINOUTmemo.php',
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response){
			v = _response.split("##");
			if(v[1]=="OK"){
				document.location.reload();
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


/*=============================================================
리스트에서 페이지 이동
=============================================================*/
function goPage(page)
{
	document.location=document.location.pathname + "?path=" + path +"&page=" + page;
}


/*=======================================================================
ajax 처리 결과 메세지 클리어
=======================================================================*/	
function msgDivClear(nNum)
{
	$("DIV[id='msgDiv']").eq(nNum).html("");
}

/*=======================================================================
관리자 페이지 주문정보 조회/수정 /_Admin/Order/orderEditPopup.php
========================================================================*/
function uncomma(str) {
	str = String(str);
	return str.replace(/[^\d]+/g, '');
}
	
//=== 옵션 사용시 수량 + - =====================================
function fnPcountPlusOrderPop(obj,v)
{
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	nNum = $("img[id='" + myID + "']").index(obj);
	inputObj = $("input[id='inPcount']").eq(nNum);
	inputObj.val(parseInt(inputObj.val())+v);
	if(parseInt(inputObj.val())<1)inputObj.val(1);
	//$("DIV[id='msgDiv']").eq(nNum).html("<font color=red>수량 변경 후 '변경' 버튼을 반드시 클릭해 주세요.</font>");
	fnPcountCheckOrderPop(obj);
}
	
//=== 옵션 사용시 수량 + - =====================================
function fnPcountPlusOrderPopMinus(obj,v)
{
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	nNum = $("img[id='" + myID + "']").index(obj);
	inputObj = $("input[id='inPcount']").eq(nNum);
	inputObj.val(parseInt(inputObj.val())+v);
	if(parseInt(inputObj.val())<0)inputObj.val(0);
	//$("DIV[id='msgDiv']").eq(nNum).html("<font color=red>수량 변경 후 '변경' 버튼을 반드시 클릭해 주세요.</font>");
	fnPcountCheckOrderPop(obj);
}

//=== 수량 제한 체크 =====================================
function fnPcountCheckOrderPop(obj)
{
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	
	myTag = obj.tagName;
	nNum = $(myTag + "[id='" + myID + "']").index(obj);
	
	obj = $("input[id='inPcount']").eq(nNum);
	maxCount = $("input[id='inMaxStock']").eq(nNum).val();
	PorderMinus = $("input[id='inPorderMinus']").eq(nNum).val();

	/*if(PorderMinus == 0 && obj.val()>parseInt(maxCount))
	{
		alert("현재 구매가능한 수량은 " + maxCount + " 개 입니다.");
		obj.val(maxCount);
	}
	else
	{
		$("div[id='msgDiv']").eq(nNum).html("<font color=red>수량 변경 후 '변경' 버튼을 반드시 클릭해 주세요.</font>");
	}*/
	
	if($("input[id='inPcount']").attr("tagName"))
	{
		count = parseInt($("input[id='inPcount']").eq(nNum).val());
		price = parseInt(uncomma($("input[id='inOPrice']").eq(nNum).val()));
		price = uncomma(price);
		
		itPrice = (count*price);
		
		$("input[id='inOPrice']").eq(nNum).val(commify(price));		
		$("TD[id='inListPrice']").eq(nNum).html(commify(itPrice) + " 원");			
	}
}

//=== 옵션 적용 =====================================
function fnPcountSetOrderPop(obj)
{
	try{if(!ajaxUrl){}}
	catch(E){alert("처리페이지가 정의되지 않았습니다.");return;}
	
	//-- 자신이 몇번째 객체 인지 체크
	myID = obj.id;
	myTag = obj.tagName;
	nNum = $(myTag + "[id='" + myID + "']").index(obj);

	IDX = $("INPUT[id='inPIDX']").eq(nNum).val();
	nCount = parseInt($("INPUT[id='inPcount']").eq(nNum).val());
	maxCount = parseInt($("INPUT[id='inMaxStock']").eq(nNum).val());

	boxPrice = parseInt($("INPUT[id='inBoxPrice']").eq(nNum).val());
	boxCount = parseInt($("INPUT[id='dcbox']").eq(nNum).val());
	dc0 = parseInt($("INPUT[id='dc0']").eq(nNum).val());
	dc9 = parseInt($("INPUT[id='dc9']").eq(nNum).val());

	if(nCount<=0)
	{
		nCount=1;
		$("INPUT[id='inPcount']").eq(nNum).val(1);
	}
	
	if(maxCount<nCount)
	{
		alert("현재 구매가능한 수량은 " + maxCount +"개 입니다.\n\n수량이 조절됩니다.");
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
				$("DIV[id='msgDiv']").eq(v[2]).html(v[3] + "개로 수정되었습니다.");
				//$("[id='htmlDCPrice']").eq(v[2]).html("<strong>1111</strong>");
				setTimeout("msgDivClear(" + v[2] + ")",2000);
			}
			else
			{
				alert(v[3] + "개로 수정되었습니다.");
			}
			fnGetTotalInfo();
		}
		else
		{
			if(v[2])
			{
				$("DIV[id='msgDiv']").eq(v[2]).html("<font color=red>오류가 발생하였습니다.</font>");
				setTimeout("msgDivClear(" + v[2] + ")",2000);
			}
			else
			{
				alert(_response);
				alert("오류가 발생하였습니다.");
			}
		}
	}
});
}

//관리자 페이지 주문서 팝업 수량/금액 변경 버튼
function fnOrderSave(obj, idx, oidx, kind){
	
	var inError = $("input[id='chkError']").val();

	var myID = obj.id;
	var myTag = obj.tagName;
	var nNum = $(myTag + "[id='" + myID + "']").index(obj);

	var inPcount = $("input[id='inPcount']").eq(nNum).val();
	var inOPrice = $("input[id='inOPrice']").eq(nNum).val();
	var inOPrice = uncomma(inOPrice);

	param='IDX=' + idx + '&mode=edit&inPcount=' + inPcount + "&OIDX=" + oidx + "&inOPrice=" + inOPrice + "&kind=" + kind;
	$.ajax({
		url:'/_Include/ajax/ajaxOrderState2.php',
		type:"POST",
		cache:false,
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		success:function(_response)
		{
			//alert(_response);
			v = _response.split("##");
			if(v[1]=="OK"){
				alert('변경되었습니다.');
				location.reload();
			} else {
				alert('ERROR :: 시스템 관리자에게 문의해주세요.');
				return;
			}
		}
	});

}



function fnProductDeleteOrderPop(obj)
{
	try{if(!ajaxUrl){}}
	catch(E){alert("처리페이지가 정의되지 않았습니다.");return;}
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

