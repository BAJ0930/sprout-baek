<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	if($stepCode=="actOK")
	{
		
		#-- 사용자는 무조건 사용자 확인 메모만 볼수 있음
		if($qIDX)
		{
			$newMode = $kind;

			$sql = " select * from 2011_memberInfo where MID = '$_SESSION[ADMIN_ID]' AND (Mlevel = 99 or Mlevel = 98) AND MPWD = old_password('$passwd') AND Mdeleted = 0";
			$result = sql_query($sql);
			if(!mysqli_num_rows($result)){
				echo "##ERROR##";
				exit;
			}

			$sql = "update 2011_memberGiftPayment set GFdepositCheck = '" . $newMode . "' where GFIDX='" . $qIDX . "'";
			sql_query($sql);
			$sql = "update 2011_memberGift set GFstate = '" . $newMode . "', GFregnm = '" . $_SESSION[__ADMIN_NAME___] . "' where IDX='" . $qIDX . "'";
			sql_query($sql);			
			$sql = "update 2011_memberPoint set POstate = '" . $newMode . "' where PIDX='" . $qIDX . "'";
			sql_query($sql);

			$sql = " SELECT * FROM 2011_memberGiftInfo WHERE GIDX = '$qIDX' ";
			$result = sql_query($sql);
			if(mysqli_num_rows($result)){
				sql_query(" UPDATE 2011_memberGiftInfo SET NAME = '$_SESSION[__ADMIN_NAME___]' , WDATE = '$inDate' , WTIME = '$inTime', MEMO = '$inOMmemo' WHERE GIDX = '$qIDX' ");
			} else {
				sql_query(" INSERT INTO 2011_memberGiftInfo ( GIDX, NAME, WDATE, WTIME, MEMO ) VALUES ('$qIDX', '$_SESSION[__ADMIN_NAME___]', '$inDate', '$inTime', '$inOMmemo') ");
			}
			$sql = " SELECT * FROM 2011_memberGiftInfo WHERE GIDX = '$qIDX' ";
			$result = sql_query($sql);
			if($newMode > 1)	$data = sql_fetch_array($result);
			
			echo "##OK##".$newMode."##".$data[NAME]."##".$data[WDATE]."##".$data[WTIME];
			exit;
		}
	}
?>
<script>
	function fnLoginGift(){
		var txt = jQuery("#inOMtxt").val();
		var kind = $("input[type='radio']:checked").val();		
		if(!txt)
		{
			alert("비밀번호를 입력해 주세요.");
			jQuery("#inOMtxt").focus();
			return;
		}		
		var qIDX = jQuery("#qIDX").val();
		var inDate = jQuery("#inDate").val();
		var inTime = jQuery("#inTime").val();
		var obj = jQuery("#obj").val();
		var obj2 = jQuery("#obj2").val();
		var inOMmemo = jQuery("#inOMmemo").val();

		if(!inDate){
			alert("입금날짜를 입력하세요.");
			 jQuery("#inDate").focus();
			 return;
		}
		if(!inTime){
			alert("입금시간을 입력하세요.");
			 jQuery("#inTime").focus();
			 return;
		}
		param = "stepCode=actOK&isAdminMode=1&qIDX=" + qIDX + "&passwd=" + txt + "&kind=" + kind + "&inDate=" + inDate + "&inTime=" + inTime + "&inOMmemo=" + inOMmemo;
		$.ajax({
		url:'/_Include/ajax/ajaxChangeState2.php',
			type:"POST",
			data : param,
			dataType:"text",
			error:fnErrorAjax,
			success:function(_response){
				v = _response.split("##");
				var memo = "";
				if(v[1]=="OK")
				{
					if(v[2]==2)
					{
						str = "O";
						fcolor="blue";

						memo = v[4] + " " + v[5] + "<br>" + v[3];
						$("[id='giftEnd']").eq(nNum).html(memo);
					}
					else if(v[2]==1)
					{
						str = "X";
						fcolor="red";
					}
					else if(v[2]==3)
					{
						str = "C";
						fcolor="green";
					}
					
					obj = $("[id='giftState']").eq(nNum);
					obj.html(str);
					obj.css("color",fcolor);
				} else if(v[1] == "ERROR"){
					alert('권한이 없거나 비밀번호가 일치하지 않습니다.');
				}
				fnClosePopup();
			}
		});
	}
</script>
<table width="500" border="0" cellspacing="0" cellpadding="0">
<input type="hidden" name="qIDX" id="qIDX" value="<?=$qIDX?>">
<input type="hidden" name="obj" id="obj" value="<?=$obj?>">
<input type="hidden" name="obj2" id="obj2" value="<?=$obj2?>">
	<tr>
		<td align="right" colspan="2"><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
	</tr>
	<Tr>
		<td align="center" colspan="2"><br><font color=black style='font-weight:bold;font-size:17px;'>[상품권 적립]</font><br><br></td>
	</tr>
	<tr><td height="1" align="right" bgcolor="#E1E1E1" colspan="2"></td></tr>
	<tr><td colspan="2"><br><br></td></tr>
	<tr>
		<td align=center style="height:28px;"><b>승인여부</td>
		<td><label><input type="radio" value="2" checked name="kind" id="kind">정상확인</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" value="3" name="kind" id="kind">취소</label></td>
	</tr>
	<tr>
		<td align=center style="height:28px;"><b>확인자</td>
		<td><?=$_SESSION[MNAME]?></td>
	</tr>
	<tr>
		<td align=center style="height:28px;"><b>입금일시</td>
		<td><input type="text" name="inDate" id="inDate" value="<?=date("Y-m-d");?>" size="12"> <input type="text" name="inTime" id="inTime" value="" size="6"></td>
	</tr>
	<tr>
		<td align=center style="height:28px;"><b>메모</td>
		<td><input type='text' style='width:300px;' id='inOMmemo' name='inOMmemo'></td>
	</tr>
	<tr>
		<td align=center style="height:28px;"><b>비밀번호</td>
		<td><input type='password' style='width:150px;' maxlength=150 id='inOMtxt' onkeydown='if(event.keyCode==13) fnLoginGift();'></td>
	</tr>
	<tr>
		<td height="90" align="center" colspan="2">
			<input type="button" value=" 확인 " onClick="fnLoginGift();" style="cursor:pointer;">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value=" 취소 " onClick="fnClosePopup();" style="cursor:pointer;">
		</td>
	</tr>
</form>
</table>	

<script>
	document.getElementById("inOMtxt").focus();
</script>