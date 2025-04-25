<?
	# 권한 검사 영역	
	if(!$RAuth)
	{
		if($dbBRAuth==99)echo "<script>alert('접근 권한이 없습니다.');history.back();</script>";
		if($dbBRAuth==1)echo "<script>alert('회원가입을 해주세요.');history.back();</script>";
		exit();
	}	
	# 선처리 영역
	
	# 해당 글 가져오기
	$sql = "Select * from 2011_boardData where IDX='" . $IDX . "'";
	$rs = sql_fetch($sql);
	if(!$rs['IDX']){
		echo "<script>alert('접근 권한이 없습니다.');history.back();</script>";
	}
	
	# DB 레코드 결과값 모두 변수로 전환
	foreach ($rs as $fieldName => $fieldValue){
		$fieldName = "db" . $fieldName;
		$$fieldName = $fieldValue;
	}
	#-- 비밀글 일시 해당 글을 읽을 권한이 있는지 체크
	
	#-- PassCheck 값  0 : 패스워드 불필요 / 통과   1 : 패스워드 입력 요구 필요    2 : 패스워드 입력 틀림
	$PassCheck=0;
	$$inSecretPass = $_POST["inSecretPass"];
	
	if($dbBsecret && $dbBsecretCheck) {
		//echo $dbMID . " / " . $MID;
		//exit();
		
		#-- 자기가 쓴 글인지 체크
		# 자기가 쓴 글은 패스
		#if($dbMID == $MID){}
		
		# 관리자는 패스
		#if($ADMIN){}
		
		#입력한 패스워드가 없을경우
		
		if(!$inSecretPass)	{
			
			if($dbMID != $MID && $ADMIN != 1 && $MID){
				if($dbBlevel!=0){
					#-- 레벨 1의 글 체크
					$sql = "select * from 2011_boardData where BID='" . $dbBID . "' and Bgroup='" . $dbBgroup . "' and Blevel=0";
					$srs = sql_fetch($sql);
					
					if($srs["MID"] != $MID) $PassCheck=1;
					
				} else {
					$PassCheck=1;
				}
			} else if(!$MID) {
				$PassCheck=1;
			}
			
		} else {
			#-- 입력한 패스워드가 있을 경우 패스워드 검사
			if($inSecretPass!=$dbBPWD) {
				#-- 틀림
				$PassCheck=2;
			} else {
				#-- 맞음
				$PassCheck=0;
			}
		}
	}
	#---------------------------------------------------------------------

	#-- 비밀글이 아니거나 패스워드 입력 통과
	if($PassCheck==0) {
		#-- 글 카운트 올리기
		$sql = "update 2011_boardData set Bhit=Bhit+1 where IDX='" . $IDX . "'";
		sql_query($sql);
		
		#--- 자신의 글 및 답변 보기 기능이 설정되어 있을 경우 해당 글의 답변도 가져온다.
		#-- 자신의 글만 보여주기일 경우 답변 갯수도 가져온다. (답변이 달린경우 count(Bgroup) 의 수가 2이상
		if($dbBonlySelf && !$ADMIN) {
			$reSql = "select * from 2011_boardData where BID='" . $dbBID . "' and Bgroup='" . $dbBgroup . "' and Blevel > 0 order by Bgroup desc,Bsort";
			$reResult = sql_query($reSql);
			$reCount = mysqli_num_rows($reResult);
		}
		#-------------------------------------------------------------------------------
		
		# 검색어 색상 강조
		if($s2) {
			if($s1=="title")$dbBtitle = str_replace($s2,"<font style='font-weight:bold;color:#0077A3'>"  . $s2 . "</font>",$dbBtitle);
			if($s1=="writer")$dbBwriter = str_replace($s2,"<font style='font-weight:bold;color:#0077A3'>"  . $s2 . "</font>",$dbBwriter);
			if($s1=="content")$dbBcontent = str_replace($s2,"<font style='font-weight:bold;color:#0077A3'>"  . $s2 . "</font>",$dbBcontent);
		}
		
		//-- 파일명 배열에 저장
		$R_FILE[1] = $dbBfile1;
		$R_FILE[2] = $dbBfile2;
		$R_FILE[3] = $dbBfile3;
		$R_FILE[4] = $dbBfile4;
		
		$S_FILE[1] = $dbBsaveFile1;
		$S_FILE[2] = $dbBsaveFile2;
		$S_FILE[3] = $dbBsaveFile3;
		$S_FILE[4] = $dbBsaveFile4;
?>
<!-- 게시판부분 -->

<table border="0" align="center" cellpadding="0" cellspacing="0" class="read" width=100%>
	<tr><th scope="col" colspan="2"><?=$dbBtitle?></th></tr>
	<tr><td colspan="2" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
	<tr>
		<td></td>
		<td width="350" style="text-align:right;">Date: <span class="font10 font_va"><strong> <?=date("Y/m/d",$dbBregDate)?></strong> <?=date("H:i",$dbBregDate)?></span></td>
	</tr>
	<tr><td colspan="2" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
	<!-- 첨부파일 처리 ---------------------------------------------------->
	<?
		for($i=1;$i<=$dbBfileCount;$i++){
			if($R_FILE[$i]){
				#-- 첨부파일 확장자 아이콘 처리
				$fileName = $R_FILE[$i];
				$fileImg = "";
				if($fileName){
					$fileTmp = explode(".",$fileName);
					$fileExe = $fileTmp[sizeof($fileTmp)-1];
					if($fileExe=="docx")$fileExe="doc";
					if($fileExe)$fileImg="<img src='/_Board/fileImage/" . $fileExe . ".gif' align='absmiddle' border=0>";
				}
	?>
	<tr><td colspan="2" scope="col">첨부파일 <?=$i?> : <a href="javascript:down_file('<?=$IDX?>','<?=$i?>','<?=$bid?>')"><?=$R_FILE[$i]?></a> <?=$fileImg?></td></tr>
	<?
			}
		}
	?>
	<!-------------------------------------------------------------------->
	<tr>
		<td scope="col" colspan="2" class="none" style="height:200px;">
			<?
				$upPath = $_SERVER["DOCUMENT_ROOT"] . "/_DATA/Board/" . $bid . "/";
				$upPath = $_SERVER["DOCUMENT_ROOT"] . "/_DATA/Board/" . $bid . "/";
				for($i=1;$i<=4;$i++){
					if($S_FILE[$i]){
						$file = $upPath . strtolower($S_FILE[$i]);
						
						if(file_exists($file)){
							$img_info = getImageSize($file);
							if($img_info[0]>600)$w = 600;
							else	$w = $img_info[0];
							
							echo "<center><img src='/_DATA/Board/" . $bid . "/" . $S_FILE[$i] .  "' border=0  width=$w></center><br><br>";
						}
					}
				}
			?>
			<?=$dbBcontent?>
		</td>
	</tr>
	<tr><td colspan="2" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
</table>
<?
	if($dbBonlySelf && !$ADMIN){
		#-- 답변 글 출력
		while($reRs = sql_fetch_array($reResult)){
			
			# DB 레코드 결과값 모두 변수로 전환
			foreach ($reRs as $fieldName => $fieldValue) {
				$fieldName = "db" . $fieldName;
				$$fieldName = $fieldValue;
			}
?>
<table border="0" align="center" cellpadding="0" cellspacing="0" class="read">
	<tr><th scope="col" colspan="2"><?=$dbBtitle?></th></tr>
	<tr>
		<td></td>
		<td width="170" >Date :<span class="font10 font_va"><strong> <?=date("Y/m/d",$dbBregDate)?></strong> <?=date("H:i",$dbBregDate)?></span></td>
	</tr>
	<tr><td scope="col" colspan="2" class="none"><?=$dbBcontent?></td></tr>
	<tr><td colspan="2" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
</table>
<?
		}
	}
?>

<!-- 버튼 -->
<ul style="list-style: none; float:right; padding-top:10px;"><li><img src='/image_3/cu_bt19a.jpg' style='cursor:pointer;' onclick='BoardGoMode("list")'  align=absmiddle></li></ul>
<!-- /버튼 -->
<?
} else if($PassCheck==1) {
#-- 비밀글 패스워드 입력이 필요할 경우
?>

<table width='100%' height='100%' cellspacing=0 cellpadding=0>
	<tr>
		<td align=center>
		
			<table width='300' cellspacing=0 cellpadding=0 border=0>
				<tr><td align=center style='border-left:1px solid gray;border-top:1px solid gray;border-right:1px solid gray;' bgcolor="#e9f2f9" height=30 bgcolor=''><font style="font-size:12px" color="#0077a3"><b>비밀번호를 입력해 주세요.</b></td></tr>
				<tr><td align=center height=50 style='border-left:1px solid gray;border-bottom:1px solid gray;border-right:1px solid gray;'><input type='password' name='inSecretPass' style='border:1px solid gray;'></td></tr>
				<tr>
					<td align=center height=50><img src='/Image/Board/btnOK.gif' onclick='secretCheck()' style='cursor:pointer;'>&nbsp;&nbsp;&nbsp;<img src='/Image/Board/btnCancel.gif' onclick="BoardGoMode('list')" style='cursor:pointer;'></td>
				</tr>
			</table>
			
		</td>
	</tr>
</table>

<script>
	//-- 비밀번호 포커스 및 엔터 입력시 
	document.formObject.inSecretPass.focus();
	document.formObject.onsubmit=function(){
		secretCheck();
		return false;
	}
</script>

<?
} else if($PassCheck==2) {
	#-- 비밀글 패스워드가 틀렸을 경우
	echo "<script>alert('비밀번호가 맞지 않습니다.');BoardGoMode('list');</script>";
}

?>