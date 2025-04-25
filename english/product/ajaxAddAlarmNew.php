<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if(!$MID) {
		echo "##noMember##";
		exit;
	}

	if(!$mode) {
		$sql = "Select * from  2011_productInfo  where IDX='" . $qIDX . "' AND Pshop='" . $shopID . "' AND Pdeleted=0 AND Pprice3>0 AND Pstate<10 AND Pagree=1 ";
		$data= sql_fetch($sql);
		
		$sql = "Select * from 2011_productOption where PIDX='" . $qIDX . "' and OPhidden=0 order by OPname,OPvalue";
		$opResult = sql_query($sql);
		
		//-- 옵션 배열화
		$opValueCount=0;
		$opNowName="";
		$opNameCnt=0;
		
		if($data[PoptionUse] > 1){
			while($opRs = sql_fetch_array($opResult)){
				$opArray[$opValueCount]["IDX"]		=	$opRs["IDX"];
				$opArray[$opValueCount]["name"]	=	$opRs["OPname"];
				$opArray[$opValueCount]["value"]	=	$opRs["OPvalue"];
				$opArray[$opValueCount]["barcode"]	=	$opRs["OPbarcode"];
				$opArray[$opValueCount]["stock"]	=	$opRs["OPstock"];
				
				//-- 차후 옵션 추가급액도 고려... 잠시 딜레이
				//$opArray[$opValueCount]["addPrice"]	=	$opRs["addPrice"];
				$opArray[$opValueCount]["addPrice"]	=	0;
				
				$opValueCount++;
				if($opNowName!=$opRs["OPname"]){
					$opNameCnt++;
					$opNowName = $opRs["OPname"];
				}
			}
		}
		#=========== 기본 출력 양식 ====================================		
?>
<script>
	function fnAddAlramSubmit(){
		
		if(!$("#inALmail").val()){
			alert("Please input your email exactly");
			return;
		}
		
		if(!$("#inALstockCount").val()){
			alert("Please input quantity to order");
			return;
		}
		
		//-- 변수 조합
		var v1 = $("#inALhp").val();
		var v2 = $("#inALday").val();
		var v3 = $("#inALstockCount").val();
		var v4 = $(':radio[name="inALopt"]:checked').val();		
		var v5 = $("#inPIDX").val();
		var inALsms1 = $(':radio[name="inALsms1"]:checked').val();		
		var inALsms2 = $(':radio[name="inALsms2"]:checked').val();
		var inALcontent1 = $("#inContent").val();
		var inALmail1 = $("#inALmail").val();

		var optArr = "";
		var opIDX = "";
		var opName = "";
		var opt = $( "#inALOpt option:selected" ).val();
		if(opt){
			optArr = opt.split("@");
			opIDX = optArr[1];
			opName = optArr[0];
		} else {
			opIDX = "0";
		}
		inALcontent1 = inALmail1 + " // " + inALcontent1;

		var param = 'mode=add&inALhp=' + v1 + '&inALday=' + v2 + '&inALstockcount=' + v3 + '&inPIDX=' + v5 +  '&inALopt=' + v4;
		param += "&inALOPIDX=" + opIDX + "&inALOPname=" + opName;
		param += "&inALsms1=" + inALsms1 + "&inALsms2=" + inALsms2 + "&inALcontent1=" + inALcontent1;
		
		fnAddAlram('',param);
	}
	function restore_input(v) {
		if(v.value.length==0) v.style.background="url('/img/alram/inputText.png')";
		else v.style.background='#FFFFFF';
	}
	function goSearch(){
		var str = document.getElementById("inPIDX").value;
		if(!str){
			alert('Please input Item No.');
			return;
		}
		var param = "inPIDX=" + str;
		$.ajax({
			url:'/product/ajaxProductSearch.php',
			type:"POST",
			cache:false,
			data : param,
			dataType:"text",
			success:function(_response){
				v = _response.split("##");
				
				if(v[1]=="OK") {
					$("#txtPname").html(v[2]);
					var img = "<img src='http://www.cheonyu.com/_DATA/product/" + v[3] + "' style='width:100px; height:100px; border:0px;'>";
					$("#txtImg").html(img);
					
					if(v[4]){
						var tOption = "<select name='inALOpt' id='inALOpt' class='alramInput'>";
						var vArr = v[4].split("^");
						for( var i = 0; i < vArr.length; i ++){
							var iArr = vArr[i].split("@");
							tOption += "<option value='" + vArr[i] + "'>" + iArr[0] + "</option>";
						}
						tOption += "</select>";
					} else {
						var tOption = "-";
					}
					$("#txtOption").html(tOption);
				} else {				
					alert('The product is not search results');
					return;
				}
			}
		});		
	}
	<? if($qIDX){ ?>
	$( document ).ready(function() {
		document.getElementById("inPIDX").style.background='#FFFFFF';
		goSearch();
	});
	<? } ?>
</script>
<style>
	.alramTD { border:1px solid #e5e5e5; font-size:11px; height:29px; padding:0px 9px; }
	.alramTDbg { background-color:#f7f7f7; font-weight:bold; }
	.alramInput { border:1px solid #d2d2d2; }
</style>
<div style="width:560px; border:0px; background-color:#f0f0f0; margin:0; padding:20px; font-size:11px; color:#484848;">
	<span style="display:block; float:left; margin:0 0 20px 95px;"><img src="/img/alram/title.png" style="display:block; border:0"></span>
	<span style="display:block; position:absolute; text-align:right; background-color:#f0f0f0; margin-left:530px; margin-top:30px;"><img src="/img/alram/btn_close.png" border="0" style="cursor:pointer;" onclick='fnClosePopup()' /></span>
	<div style="clear:both;"></div>
	<div style="display:block; width:510px; background-color:#fff; margin:0; padding:25px; border:0;">
		<ul style="list-style:none; margin:0; padding:0;">
			<li style="border-bottom:1px solid #d3d3d3;"><img src="/img/alram/title2.png" style="display:block; border:0;"></li>
			<li style="margin:15px 0px 15px 5px; line-height:150%; font-size:13px;" >Once you can apply for 'Warehousing of the goods' e-mail service, You will <br> 
<span style="color:#0095d3;">receive information of product's Estimated warehousing date/ The moment<br>of goods were stocked.</span></li>
			<li style="margin:0px 0px 5px 5px;">- 'Expectation date of warehousing of the goods' can be changed by courier company <br> &nbsp;&nbsp;situation or holiday season.</li>
			<li style="margin:0px 0px 5px 5px;">- <span style="color:#f74c4c;">Except of confirmed purchasing q'ty, the warehousing of goods quantity would be<br> &nbsp;&nbsp;rescheduled.</span></li>
			<li style="margin:0px 0px 0px 5px;">- Note: Real time answering shall be postponded <span style="color:#f74c4c;">after 3pm (local time in S.Korea)<br> &nbsp;&nbsp;(unanswerable on weekend/ Holiday)</span></li>
		</ul>
	</div>
	<div style="clear:both;"></div>
	<div style="display:block; width:510px; background-color:#fff; margin:0; padding:0px 25px 25px 25px; border:0; font-size:11px;">
		<table style="width: 100%; border-collapse: collapse;">
			<tr>
				<td class="alramTD" rowspan="5" colspan="2" id="txtImg" style="text-align:center;"></td>
				<td class="alramTD alramTDbg" style="width:92px;">Item No.<span style="color:red; font-weight:normal;">＊</span></td>
				<td class="alramTD" colspan="2">
					<input name="inPIDX" type="text" id="inPIDX" value="<?=$qIDX?>" class="alramInput" style="width:190px;background-image:url('/img/alram/inputText.png');" onFocus="this.style.background='#FFFFFF'" onBlur="restore_input(this)" required />
					<input type="button" style="border:1px solid #d2d2d2;vertical-align:bottom; background-color:#f7f7f7; color:#666666; font-size:12px; height:20px; cursor:pointer;" value="검색" onClick="goSearch();">
				</td>
			</tr>
			<tr>
				<td class="alramTD alramTDbg">Item Name</td>
				<td class="alramTD" colspan="2" id="txtPname"></td>
			</tr>
			<tr>
				<td class="alramTD alramTDbg">Option</td>
				<td class="alramTD" colspan="2" id="txtOption">-</td>
			</tr>
			<tr>
				<td class="alramTD alramTDbg">Order Q’ty</td>
				<td class="alramTD" colspan="2"><input name="inALstockCount" type="text" id="inALstockCount" size="2" value="1" maxlength="4" class="alramInput" onkeydown='onlyNum()' onblur='if(this.value<=0 || !this.value)this.value=1' />&nbsp;Q’ty</td>
			</tr>
			<tr>
				<td class="alramTD alramTDbg">Purchase<br/>sortation</td>
				<td class="alramTD" colspan="2">
					<label style="cursor:pointer;"><input type="radio" name="inALopt" id="inALopt" value="1" checked style="vertical-align:middle; cursor:pointer; font-size:10px;"> Not confirmed</label>
					<label style="cursor:pointer;"><input type="radio" name="inALopt" id="inALopt" value="2" style="vertical-align:middle; cursor:pointer; font-size:10px;"> Confirm purchasing</label>
				</td>
			</tr>
			<tr><td class="alramTD alramTDbg" colspan="5">Expection date of warehousing of goods’ e-mail service</td></tr>
			<tr>
				<td class="alramTD" colspan="5">
					<label style="cursor:pointer;"><input type="radio" name="inALsms2" id="inALsms2" value="1" checked style="vertical-align:middle; cursor:pointer;"> To apply</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label style="cursor:pointer;"><input type="radio" name="inALsms2" id="inALsms2" value="0" style="vertical-align:middle; cursor:pointer;"> No need</label>
				</td>
			</tr>
			<tr><td class="alramTD alramTDbg" colspan="5">Once stock is arrived at CHEONYU distribution center</td></tr>
			<tr>
				<td class="alramTD" colspan="5">
					<label style="cursor:pointer;"><input type="radio" name="inALsms1" id="inALsms1" value="1" checked style="vertical-align:middle; cursor:pointer;"> To apply</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label style="cursor:pointer;"><input type="radio" name="inALsms1" id="inALsms1" value="0" style="vertical-align:middle; cursor:pointer;"> No need</label>
				</td>
				<!--td class="alramTD alramTDbg" style="width:111px;">입고알림 대기기간</td>
				<td class="alramTD" style="width:102px;">
					<select class="alramInput" name="inALday" id="inALday">
						<option value="3">3일</option>
						<option value="7">7일</option>
						<option value="10">10일</option>
						<option value="20">20일</option>
						<option value="30">30일</option>
					</select>
				</td-->
			</tr>
			<tr>
				<td class="alramTD alramTDbg" style="width:105px;">Comment</td>
				<td class="alramTD" colspan="4" style="width:360px;padding:5px 10px;"><textarea rows="4" style="width:100%;height:60px;font-size:9pt;" name="inContent" id="inContent" class="alramInput"></textarea></td>
			</tr>
			<tr>
				<td class="alramTD alramTDbg">Phone Number</td>
				<td class="alramTD" colspan="4">
					<input name="inALhp" id="inALhp" type="text" style="width:260px;height:16px;" value="<?=$MHParray[0]?>" maxlength=3 class="alramInput" style='ime-mode:disabled;' onkeydown='onlyNum()' />&nbsp;<span style="color:#0095d3; font-size:11px; font-weight:normal;"></span>
				</td>
			</tr>
			<tr>
				<td class="alramTD alramTDbg">E-mail Address</td>
				<td class="alramTD" colspan="4">
					<input name="inALmail" id="inALmail" type="text" style="width:260px;height:16px;" value="" class="alramInput" />&nbsp;<span style="color:#0095d3; font-size:11px; font-weight:normal;"></span>
				</td>
			</tr>
		</table>
		<div style="clear:both;"></div>
		<div style="text-align:center; padding-top:20px;">
			<img src="/img/btn_ok2a.gif"  style='cursor:pointer' onclick='fnAddAlramSubmit("")'  border="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="/img/btn_c2a.gif" border="0" style='cursor:pointer' onclick='fnClosePopup()'  />
		</div>
		<div style="clear:both;"></div>
	</div>
</div>



				
<?
	##---------------------------------------------------
	} else if($mode == "add") {
		#-- insert 전 20건이상 신청 체크
		$today = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$sql = "select count(IDX) as cnt from 2011_stockAlram where MID='" . $MID . "' and ALregdate>=" . $today;
		$rs = sql_fetch($sql);
		
		if($rs['cnt']<20) {
			$sql = "insert into 2011_stockAlram (MID,PIDX,OPIDX,OPname,ALday,ALhp,ALstockCount,ALopt,ALstate,ALstate2,ALsms1,ALsms2,ALcontent1,ALregdate) values(";
			$sql.= "'" . $MID . "',";
			$sql.= "'" . $inPIDX . "',";				
			$sql.= "'" . $inALOPIDX . "',";
			$sql.= "'" . $inALOPname . "',";
			$sql.= "'" . $inALday . "',";
			$sql.= "'" . $inALhp . "',";
			$sql.= "'" . $inALstockcount . "',";
			$sql.= "'" . $inALopt . "',";
			$sql.= "'1',";
			$sql.= "'1',";
			$sql.= "'" . $inALsms1 . "',";
			$sql.= "'" . $inALsms2 . "',";
			$sql.= "'" . $inALcontent1 . "',";			
			$sql.= "'" . date(time()) . "')";			
			sql_query($sql);
			echo "##OK##";
			exit();
		} else {
			echo "##over##";
			exit();
		}
	} else if($mode=="Delete") {
		$sql = "delete from 2011_stockAlram where IDX='" . $inALIDX . "'";
		sql_query($sql);
		echo "##OK##";
		exit();
	}

?>