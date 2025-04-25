<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	if($stepCode=="actOK")
	{		
		if($qIDX && $qMKIDX)
		{

			$sql = "update 2011_adminCart set ACABDATE = '" . $inDate . "' where ACAgroup='" . $qIDX . "' AND ACAMKIDX = '" . $qMKIDX . "' ";
			sql_query($sql);

			echo "##OK##".$inDate;
			exit;
		}
	}
?>
<script>
	function fnLoginGift(){
		var qIDX = jQuery("#qIDX").val();
		var qMKIDX = jQuery("#qMKIDX").val();
		var inDate = jQuery("#inDate").val();
		var obj = jQuery("#obj").val();
		var nNum = jQuery("#nNum").val();

		if(!inDate){
			alert("전표일을 입력하세요.");
			 jQuery("#inDate").focus();
			 return;
		}
		param = "stepCode=actOK&isAdminMode=1&qIDX=" + qIDX + "&qMKIDX=" + qMKIDX + "&inDate=" + inDate;
		$.ajax({
		url:'/_Include/ajax/ajaxOrderBuy.php',
			type:"POST",
			data : param,
			dataType:"text",
			error:fnErrorAjax,
			success:function(_response){
			
				obj = $("[id='buyAdd']").eq(nNum);
				obj.html(inDate);

				fnClosePopup();
			}
		});
	}
</script>

<table width="500" border="0" cellspacing="0" cellpadding="0">
<input type="hidden" name="qIDX" id="qIDX" value="<?=$qIDX?>">
<input type="hidden" name="qMKIDX" id="qMKIDX" value="<?=$qMKIDX?>">
<input type="hidden" name="obj" id="obj" value="<?=$obj?>">
<input type="hidden" name="nNum" id="nNum" value="<?=$nNum?>">
	<tr>
		<td align="right" colspan="2"><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
	</tr>
	<Tr>
		<td align="center" colspan="2"><br><font color=black style='font-weight:bold;font-size:17px;'>[매입관리 - 전표일 추가]</font><br><br></td>
	</tr>
	<tr><td height="1" align="right" bgcolor="#E1E1E1" colspan="2"></td></tr>
	<tr><td colspan="2"><br><br></td></tr>
	<tr>
		<td align=center style="height:28px;"><b>전표일</td>
		<td><input type="text" name="inDate" id="inDate" value="<?=date("Y-m-d");?>" size="12" ></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><br>달력에러..날짜 직접 입력하셔야 합니다.<br>날짜형식 : 2015-01-09 (YYYY-MM-DD)</td>
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