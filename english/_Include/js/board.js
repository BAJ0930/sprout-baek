var nURL = document.location.pathname;
var passCheck = 0;
var nIDX = "";
var nMode = "";

function BoardGoModePage(page,mode,submode,IDX) {
	uStr = "https://cheonyu.com"+ page + "?pmode=" + mode;
	if(IDX)uStr = uStr + "&IDX=" + IDX ;
	if(submode)uStr = uStr + "&submode=" + submode;
	if(submode=="delete") {
		if(!confirm("해당 글을 삭제하시겠습니까?")) {
			return;
		}
	}
	
	if(submode=="edit" || submode=="delete") {
		nIDX = IDX;
		nMode = submode;
		BoardPassGet(1);
		BoardPassCheck();
		return;
	} else {	
		document.location = uStr;
	}
}

function BoardGoMode(mode,submode,IDX) {
	uStr = nURL + "?pmode=" + mode;
	if(IDX)uStr = uStr + "&IDX=" + IDX ;
	if(submode)uStr = uStr + "&submode=" + submode;

	if(submode=="delete") {
		if(!confirm("해당 글을 삭제하시겠습니까?")) {
			return;
		}
	}
	
	if(submode=="edit" || submode=="delete") {
		nIDX = IDX;
		nMode = submode;
		BoardPassGet(1);
		BoardPassCheck();
		return;
	} else {	
		document.location = uStr;
	}
}

function BoardSearch() {	
	var frm = document.formObject;	
	frm.method="get";
	frm.action = nURL + "?pmode=list";		
	frm.submit();
}

function BoardPassGet(v) {	
	if(v==1)v="block";
	if(v==0)v="none";
	pass_div = document.getElementById("pass_div");	 	
        
	try{
		check = pass_div.innerHTML;	
	}
	catch(E)
	{
		pass_div = document.createElement("div");
		pass_div.id = "pass_div";
		pass_div.style.zIndex=91;
		pass_div.style.position="absolute";
		
		pass_div.className="backDiv";
		document.body.appendChild(pass_div);
	}
	w_Top = document.documentElement.scrollTop;
	w_width = document.documentElement.scrollWidth;
	
	if(!w_Top)w_Top = document.body.scrollTop;
	pass_div.style.height="150px";

	if(w_width || w_Top) {
		pass_div.style.width = (w_width-200)+ "px";
		pass_div.style.top = (parseInt(w_Top) + 100)  + "px";
		pass_div.style.left = 100; 
		pass_div.style.display = v;
	}
	
	if(v=="block") {		
		var frm = document.passForm;
		try{
			frm.inputPass.focus();
		}catch(E){}
	}
}

function BoardPassCheck() {
	var frm = document.passForm;	
	var turl = "/_Board/boardPassCheck.php";
	addval = "";	
	try{
		addval = "Pass=" + frm.inputPass.value;
	}
	catch(E){}
	
	//-- 현재 작동페이지가 어디인지 체크기능 추가
	p = document.location.pathname.split("/");

	
	addval = addval + "&IDX=" + nIDX + "&nPath=" + p[2];
	$.ajax({
		url:'/_Board/boardPassCheck.php',		
		type:"POST",
		data : addval,
		dataType:"text",
		error:fnErrorAjax,
		cache	:	false,
		success:function(_response) {
			//result = _response.responseText	
			result = _response;
			if(result=="PassOK") {
				uStr = nURL + "?pmode=write";
				uStr = uStr + "&IDX=" + nIDX ;
				uStr = uStr + "&submode=" + nMode;
				document.location = uStr;
			}
			else
			{
				pass_div = document.getElementById("pass_div");
				pass_div.innerHTML = result;
			}
		}

	});
	
	pass_div = document.getElementById("pass_div");

	sHtml="<table width='100%' height='100%'><tr><td align=center valign=middle cellspacing=0>";
	sHtml=sHtml + "<table width='300' height='90' cellspacing=0 style='border:1px solid #0077A3;'>";
	sHtml=sHtml + "<tr height=30><Td bgcolor='#ffffff' align=center>비밀번호 검사 중 입니다.</tr></table>";	
	sHtml=sHtml + "</td></tr></table>";
	pass_div.innerHTML = sHtml;
	
}



var userBanString="";
function StringCheck(str)
{
	/*- 기본 차단 단어 -*/
	var banStr = "18아,18놈,18새끼,18년,18뇬,18노,18것,18넘,개년,개놈,개뇬,개새,개색끼,개세끼,개세이,개쉐이,개쉑,개쉽,개시키,개자식,개좆,게색기,게색끼,광뇬,뇬,눈깔,뉘미럴,니귀미,니기미,니미,도촬,되질래,뒈져라,뒈진다,디져라,디진다,디질래,병쉰,병신,뻐큐,뻑큐,뽁큐,삐리넷,새꺄,쉬발,쉬밸,쉬팔,쉽알,스팔,스패킹,스팽,시발,시벌,시부랄,시부럴,시부리,시불,시브랄,시팍,시팔,시펄,실밸,십8,십쌔,십창,싶알,쌉년,썅놈,쌔끼,쌩쑈,썅,써벌,썩을년,쎄꺄,쎄엑,쓰바,쓰발,쓰벌,쓰팔,씨8,씨댕,씨바,씨발,씨뱅,씨봉알,씨부랄,씨부럴,씨부렁,씨부리,씨불,씨브랄,씨빠,씨빨,씨뽀랄,씨팍,씨팔,씨펄,씹,아가리,아갈이,엄창,접년,잡놈,재랄,저주글,조까,조빠,조쟁이,조지냐,조진다,조질래,존나,존니,좀물,좁년,좃,좆,좇,쥐랄,쥐롤,쥬디,지랄,지럴,지롤,지미랄,쫍빱,凸,퍽큐,뻑큐,빠큐";
	if(userBanString)
	{
		if(banStr.substr(banStr.length-1)!=",")banStr = banStr + ",";
		banStr = banStr + userBanString;
	}
	
	banStr = banStr.replaceAll(",","|");
	chars = "(" + banStr + ")";
	var CHK_STRING = new RegExp(chars);
	if (banStr != "")
	{
		if (CHK_STRING.test(str))
		{
			alert("["+RegExp.$1+"] 은(는) 차단된 단어입니다.");
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}


/*=======================================================================
페이징 결과의 정렬 순서 변경
=======================================================================*/
function setOrder(f,t)
{
	document.location= nURL + "?path=" + path + "&order1=" + f + "&order2=" + t;
}

/*=======================================================================
파일 다운로드
=======================================================================*/
function down_file(BIDX,FILE_NUM,BID){
	
	window.open("/_Include/boardDownload.php?BIDX=" + BIDX + "&FILE_NUM=" + FILE_NUM + "&BID=" + BID,"_self");
	return;
}

/*=======================================================================
이미지 파일 보기
=======================================================================*/
function view_file(BIX,FILE_NUM,BID){
	//window.open("/_Include/boardFileView.php?BIDX=" + BIDX + "&FILE_NUM=" + FILE_NUM + "&BID=" + BID,"_viewFile","width=100,height=100");
	return;
}

/*=======================================================================
비밀번호 체크 스크립트
=======================================================================*/
function secretCheck()
{
	var frm = document.formObject;
	
	if(!frm.inSecretPass.value)
	{
		alert('비밀번호를 입력해 주세요.');
		frm.inSecretPass.focus();
		return;
	}
	
	frm.submit();
	
}

/*========================================================================
	Comment Script
========================================================================*/
function fnCommentCall(bIDX,p)
{
	
	if(!p)p=1;
	
	var param = "bIDX=" + bIDX + "&page=" + p;
	
$.ajax({
		url:'/_Board/ajaxBoardComment.php',		
		type:"POST",
		data : param,
		dataType:"text",
		error:fnErrorAjax,
		cache	:	false,
		success:function(_response)		
						{
							jQuery("#commentDiv").html(_response);
						}

	});
		
}





var onCommentChange = 0;
function fnCommentChange()
{
	onCommentChange=1;
}

function fnCommentSave(qIDX,bIDX,repType,p)
{
	if(!p)p=1;
	
	if(repType == 'new'){
		if(jQuery("#CommentForm").find("#inCommentTxt").val() == "" || onCommentChange==0){
			alert("내용을 입력해 주세요.");
			jQuery("#CommentForm").find("#inCommentTxt").val("");
			jQuery("#CommentForm").find("#inCommentTxt").focus();
			return;
		}
		
		var contents = encodeURIComponent(jQuery("#CommentForm").find("#inCommentTxt").val());
		var param = "inCommentTxt=" + contents + "&bIDX=" + bIDX + "&repType=" + repType + "&cPage=" + p;
	

				$.ajax({
						url:'/_Board/ajaxBoardComment.php',		
						type:"POST",
						data : param,
						dataType:"text",
						error:fnErrorAjax,
						cache	:	false,
						success:function(_response)	{fnCommentProcessEnd(_response);}
				
					});
			
			
			onCommentChange=0;
	}else if(repType == 'edit'){
		
		
		if(jQuery("#CommentListForm").find("#inCommentTxt").val() == ""){
			alert("내용을 입력해 주세요.");
			jQuery("#CommentListForm").find("#inCommentTxt").focus();
			return;
		}
		
		var contents = encodeURIComponent(jQuery("#CommentListForm").find("#inCommentTxt").val());
		var param = "qIDX=" + qIDX + "&inCommentTxt=" + contents + "&bIDX=" + bIDX + "&repType=" + repType + "&cPage=" + p;
		
				$.ajax({
						url:'/_Board/ajaxBoardComment.php',		
						type:"POST",
						data : param,
						dataType:"text",
						error:fnErrorAjax,
						cache	:	false,
						success:function(_response)	{fnCommentProcessEnd(_response);}
				
					});
		
	}else if(repType == "delete"){
			
			if(confirm("삭제하시겠습니까?"))
			{
				
				var param = "qIDX=" + qIDX + "&bIDX=" + bIDX + "&repType=" + repType + "&cPage=" + p;
		
				$.ajax({
						url:'/_Board/ajaxBoardComment.php',		
						type:"POST",
						data : param,
						dataType:"text",
						error:fnErrorAjax,
						cache	:	false,
						success:function(_response)	{fnCommentProcessEnd(_response);}
				
					});
			}
	}
	
}

function fnCommentProcessEnd(_response)
{
	
	var v = _response.responseText;
	var _result = v.split("##");
	
	
	if(_result[2]=="required login")
	{
		alert("로그인을 해주세요.");
	}
	else if(_result[2]=="fail id")
	{
		//-- 이런경우는 없으나...비정상 접근 예방
		alert("자신의 글만 삭제가 가능합니다.");
	}
	else if(_result[2]=="fail pass")
	{
		//-- 이런경우는 없으나...비정상 접근 예방
		alert("패스워드가 맞지 않습니다.");
	}
	else if(_result[2]=="OK")
	{
			fnCommentCall(_result[1],_result[3]);
	}
	else if(_result[2]=="error")
	{
		alert("오류가 발생하였습니다. 관리자에게 문의해 주세요");	
	}
	else
	{
		alert("result value : " + _result[2]);	
	}
	
	
}

var txtVal="";
function fnCommentEdit(qidx,bidx,page)
{
	if(txtVal=="")txtVal = jQuery("#CommentListForm").find("#Ctxt" + qidx).html();
	
	txtVal = txtVal.replaceAll("<BR>","\r\n");
	
	str = "<table width='100%' class=tbl style='width:100%;height:80px;padding:10px;'><tr><td>";
	str = str +  "<textarea id='inCommentTxt' style='width:96%;height:70px;border:1px solid #afafaf;background-color:#f3f3f3;color:#3f3f3f;'>" + txtVal + "</textarea> ";
	str = str + "</td>";
	str = str + "<td width=80><img src='/image/Board/btn_commentSave.gif' style='cursor:pointer;' onclick='fnCommentSave(\"" + qidx + "\",\"" + bidx +"\",\"edit\",\"" + page + "\");txtVal=\"\";'></td>";
	str = str + "<td width=80><img src='/image/Board/btn_commentCancel.gif' style='cursor:pointer;' onclick='fnCommentEditCancel(\"" + qidx + "\")'></td>";
	str = str + "</tr><table>";
	
	
	
	jQuery("#CommentListForm").find("#Ctxt" + qidx).html(str);
	
}

function fnCommentEditCancel(qidx)
{
	txtVal = txtVal.replaceAll("\r\n","<BR>");
	
	jQuery("#CommentListForm").find("#Ctxt" + qidx).html(txtVal);
	txtVal="";
}


