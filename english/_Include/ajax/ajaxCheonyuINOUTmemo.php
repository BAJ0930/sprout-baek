<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	$opt = array("","외근", "출장", "지각", "휴가", "외출", "기타");
	
	if($stepCode=="actOK")
	{
		$wdate = $str1;
		$cMID = $str3;
		
		$query = " SELECT * FROM memberINOUT WHERE CMID = '$cMID' AND CDATE = '$wdate' ";
		$data = sql_fetch($query);
		if($data['NO']){
			
			$query = " UPDATE memberINOUT SET MEMO1 = '$inKind' , MEMO2 = '$inMemo' WHERE NO = '$data[NO]' ";
			$result = sql_query($query);

		} else {
			
			$query = " INSERT INTO memberINOUT VALUES ('', '$cMID', '$wdate', '', '', '$inKind', '$inMemo', now()) ";			
			$result = sql_query($query);

		}

		echo "##OK##";
		exit;
	}
?>
<script>
	function fnLoginGift(){
		var qIDX = jQuery("#qIDX").val();
		var inKind = jQuery("#inKind").val();
		var str1 = jQuery("#str1").val();	/* 날짜 */ 
		var str2 = jQuery("#str2").val(); /* 이름 */
		var str3 = jQuery("#str3").val(); /* 아이디 */
		var inMemo = jQuery("#inMemo").val();

		param = "stepCode=actOK&isAdminMode=1&qIDX=" + qIDX + "&inKind=" + inKind + "&str1=" + str1 + "&str2=" + str2 + "&str3=" + str3 + "&inMemo=" + inMemo;
		$.ajax({
		url:'/_Include/ajax/ajaxCheonyuINOUTmemo.php',
			type:"POST",
			data : param,
			dataType:"text",
			error:fnErrorAjax,
			success:function(_response){
				document.location.reload();
			}
		});
	}
</script>
<table width="500" border="0" cellspacing="0" cellpadding="0">
<input type="hidden" name="qIDX" id="qIDX" value="<?=$qIDX?>">
<input type="hidden" name="str1" id="str1" value="<?=$str1?>">
<input type="hidden" name="str2" id="str2" value="<?=$str2?>">
<input type="hidden" name="str3" id="str3" value="<?=$str3?>">
	<tr>
		<td align="right" colspan="2"><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
	</tr>
	<Tr>
		<td align="center" colspan="2"><br><font color=black style='font-weight:bold;font-size:17px;'>[근태관리 메모]</font><br><br></td>
	</tr>
	<tr><td height="1" align="right" bgcolor="#E1E1E1" colspan="2"></td></tr>
	<tr><td colspan="2"><br><br></td></tr>
	<tr>
		<td align=center style="height:28px; width:100px;"><b>이름</td>
		<td><?=$str2?></td>
	</tr>
	<tr>
		<td align=center style="height:28px;"><b>날짜</td>
		<td><?=$str1?></td>
	</tr>
	<?
		$query = "SELECT * FROM memberINOUT WHERE CMID = '$str3' AND CDATE = '$str1'";
		$row = sql_fetch($query);
	?>
	<tr>
		<td align=center style="height:28px;"><b>구분</td>
		<td>
			<select name="inKind" id="inKind">
				<option value="0">-- 선택 --</option>
				<? 
					for($k = 1; $k < count($opt); $k ++){
						if($row['MEMO1'] == $k) $sel = " selected ";
						else $sel = "";
				?>
				<option value="<?=$k?>" <?=$sel?>><?=$opt[$k];?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td align=center style="height:28px;"><b>내용</td>
		<td><input type='text' style='width:380px;' id='inMemo' name='inMemo' value="<?=$row['MEMO2']?>"></td>
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