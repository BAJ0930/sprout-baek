<?
exit;
	# 권한 검사 영역	
	if(!$WAuth || !$REAuth && $submode=="reply"){
		if($dbBWAuth==99)echo "<script>alert('접근 권한이 없습니다.');history.back();</script>";
		if($dbBWAuth==1)echo "<script>alert('회원가입을 해주세요.');history.back();</script>";
		if($dbBREAuth==99)echo "<script>alert('접근 권한이 없습니다.');history.back();</script>";
		if($dbBREAuth==1)echo "<script>alert('회원가입을 해주세요.');history.back();</script>";
		exit();
	}
	# 저장처리 영역
	
	if($setPost){
		//-- 첨부 파일 업로드
		for($i=1;$i<=$dbBfileCount;$i++){
			$file=$_FILES["FILE" . $i][tmp_name];
			//			echo $_FILES["FILE" . $i][name];
			#-- strtolower 에서 한글깨짐 방지
			setlocale(LC_CTYPE, 'C'); 
			$r_file[$i]=strtolower($_FILES["FILE" . $i][name]);
			
			if($file){		
				$upPath = __HOME_SERVER_PATH__ . "/_DATA/Board/" . $bid . "/";
				
				//저장되는 파일 이름 변형(중복 방지)
				$exe = explode(".",$r_file[$i]);
				$len = sizeof($exe);
				$exe = $exe[$len-1];
				$s_file[$i] = date(time()) . $i . "." . $exe;
				
				// 파일 중복 체크
				while(file_exists($upPath .  $s_file[$i])){
					$s_file[$i] = date(time()) . rand(100) . "." . $exe;
				}
				$uploadfile= $upPath . $s_file[$i];
				
				//////// 확장자 검사 이미지 파일일때와 일반 파일일때의 구분
				if($exe=="gif" || $exe=="jpg" || $exe=="pcx" || $exe=="bmp"){
					img_resize_gd($file,$uploadfile,800,600);
				}else{
					//-- 이미지파일이 아니면 바로 업로드
					move_uploaded_file($file, $uploadfile);
				}
			}
		}
		//--- 첨부파일 업로드 끝
	
		if($IDX) {
			# 수정
			if($submode=="edit"){
				$sql = "update 2011_boardData set ";
				$sql.= "Bwriter='" . $inBwriter . "',";
				$sql.= "BPWD='" . $inBPWD . "',";
				$sql.= "Btitle='" . $inBtitle . "',";
				$sql.= "Bemail='" . $inBemail . "',";
				if($r_file[1])$sql.= "Bfile1='$r_file[1]',BsaveFile1='$s_file[1]',";
				if($r_file[2])$sql.= "Bfile2='$r_file[2]',BsaveFile2='$s_file[2]',";
				if($r_file[3])$sql.= "Bfile3='$r_file[3]',BsaveFile3='$s_file[3]',";
				if($r_file[4])$sql.= "Bfile4='$r_file[4]',BsaveFile4='$s_file[4]',";
				$sql.= "BsecretCheck='" . $inBsecretCheck . "',";
				$sql.= "Bcontent='" . $inBcontent . "'";
				$sql.= " where IDX='" . $IDX . "'";				
				
				sql_query($sql);
				
				echo "<script>alert('수정되었습니다.');BoardGoMode('list');</script>";
				exit();
			} else if($submode=="reply") { #답변
				
				# 그룹번호 구하기
				$sql = "select * from 2011_boardData where IDX='" . $IDX . "'";
				$brs = sql_fetch($sql);

				if(!$brs['IDX']) {
					echo "<script>alert('원본글이 존재하지 않습니다.');BoardGoMode('list');</script>";
					exit;
				}
				
				$nGroup = $brs["Bgroup"];
				$nLevel = $brs["Blevel"] + 1;
				$nSort = $brs["Bsort"] + 1;
				
				# 새로 추가될 글 자리 만들기
				$sql = "update 2011_boardData set Bsort = Bsort + 1 where BID='" . $bid . "' and Bgroup='" . $nGroup . "' and Bsort>='" . $nSort . "'";
				sql_query($sql);
				
				$sql = "insert into 2011_boardData (BID,MID,Bwriter,BPWD,Bemail,Btitle,Bcontent,";				
				$sql.= "Bfile1,Bfile2,Bfile3,Bfile4,BsaveFile1,BsaveFile2,BsaveFile3,BsaveFile4,Bgroup,Blevel,Bsort,Bhit,BsecretCheck,BregDate) ";
				$sql.= "values(";
				$sql.= "'" . $bid . "',";
				$sql.= "'" . $MID . "',";
				$sql.= "'" . $inBwriter . "',";
				$sql.= "'" . $inBPWD . "',";
				$sql.= "'" . $inBemail . "',";
				$sql.= "'" . $inBtitle . "',";
				$sql.= "'" . $inBcontent . "',";
				
				$sql.= "'$r_file[1]','$r_file[2]','$r_file[3]','$r_file[4]',";
				$sql.= "'$s_file[1]','$s_file[2]','$s_file[3]','$s_file[4]',";
				
				$sql.= "'" . $nGroup . "',";
				$sql.= "'" . $nLevel . "',";
				$sql.= "'" . $nSort . "',";
				$sql.= "'0',";
				$sql.= "'" . $inBsecretCheck . "',";
				$sql.= "'" . date(time()) . "')";
				
				sql_query($sql);
				
				echo "<script>alert('등록되었습니다.');BoardGoMode('list');</script>";
				exit();
			}
		} else {
			# 신규 저장
			
			# 그룹번호 구하기
			$sql = "select max(Bgroup) as maxBgroup from 2011_boardData where BID='" . $bid . "'";
			$brs = sql_fetch($sql);
			$nGroup = $brs['maxBgroup'] + 1;

			$sql = "insert into 2011_boardData (BID,MID,Bwriter,BPWD,Bemail,Btitle,Bcontent,";			
			$sql.= "Bfile1,Bfile2,Bfile3,Bfile4,BsaveFile1,BsaveFile2,BsaveFile3,BsaveFile4,Bgroup,Blevel,Bsort,Bhit,BsecretCheck,BregDate) ";
			
			$sql.= "values(";
			$sql.= "'" . $bid . "',";
			$sql.= "'" . $MID . "',";
			$sql.= "'" . $inBwriter . "',";
			$sql.= "'" . $inBPWD . "',";
			$sql.= "'" . $inBemail . "',";
			$sql.= "'" . $inBtitle . "',";
			$sql.= "'" . $inBcontent . "',";
			
			$sql.= "'$r_file[1]','$r_file[2]','$r_file[3]','$r_file[4]',";
			$sql.= "'$s_file[1]','$s_file[2]','$s_file[3]','$s_file[4]',";
			
			$sql.= "'" . $nGroup . "',";
			$sql.= "'0',";
			$sql.= "'0',";
			$sql.= "'0',";
			$sql.= "'" . $inBsecretCheck . "',";
			$sql.= "'" . date(time()) . "')";
			
			sql_query($sql);
			
			echo "<script>alert('등록되었습니다.');BoardGoMode('list');</script>";
			exit();
		}
	}
	
	# 선처리 영역
	if($submode && !$IDX){
		echo "<script>alert('ERROR.');BoardGoMode('list');</script>";
		exit();
	} else if($submode) {
		# 수정 / 삭제 패스워드 유지시간 30분
		if(($submode=="edit" || $submode=="delete") && ($_SESSION["passIDX"] != $IDX || $_SESSION["passCheck"]<(date(time())- 1800))){
			echo "<script>alert('ERROR.');BoardGoMode('list');</script>";
			exit();
		}
		
		# 수정/답변/삭제 공통 작업 - 원본글 데이터 가져오기
		$sql = "select * from 2011_boardData where IDX='" . $IDX . "'";
		$rs = sql_fetch($sql);
		
		if(!$rs['IDX']){
			echo "<script>alert('원본 게시물이 존재하지 않습니다.');BoardGoMode('list');</script>";
			exit();
		}
		
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){
			$fieldName = "db" . $fieldName;
			$$fieldName = $fieldValue;
		}
		
		# 수정
		if($submode=="edit"){
			#-- 수정시에는 이름과 사용자 ID가 변경되지 않도록 한다.
			#-- (관리자가 수정하더라도 사용자 정보가 유지되도록)
			$MID = $dbMID;
			$MPWD = $dbBPWD;
			$Bwriter = $dbBwriter;
			$Bemail = $dbBemail;
		} else if($submode=="reply") {	#답변
			$dbBtitle = "";
			$dbBwriter = "";
			$dbBcontent = "";
			$Bwriter = $Mname;
			$Bemail = $Memail;
		} else if($submode=="delete") {
			# 부가 정보 삭제하기
			
			# 삭제글 정보 구하기
			$sql = "select * from 2011_boardData where IDX='" . $IDX . "'";
			$delRs = sql_fetch($sql);
			
			$delBID = $delRs["BID"];
			$delGroup = $delRs["Bgroup"];
			$delLevel = $delRs["Blevel"];
			$delSort = $delRs["Bsort"];

			# 하위 글 긁어오기 (삭제글 미포함)
			$sql = "select * from 2011_boardData where BID='" . $delBID . "' and Bgroup='" . $delGroup . "' and Bsort>'" . $delSort . "' order by Bsort";
			$delResult2 = sql_query($sql);
			while($delRs2 = sql_fetch_array($delResult2)){
				
				if($delRs2["Blevel"] <= $delLevel)break;
				
				#-- 하위 글 정보 체크
				$delIDX2 = $delRs2["IDX"];
				$delBID2 = $delRs2["BID"];
				$delGroup2 = $delRs2["Bgroup"];
				$delLevel2 = $delRs2["Blevel"];
				$delSort2 = $delRs2["Bsort"];

				# 실제 글 삭제하기
				$sql = "delete from 2011_boardData where IDX='" . $delIDX2 . "'";
				sql_query($sql);
				
				# 그룹 재정렬
				$sql = "update 2011_boardData set Bsort = Bsort - 1 where BID='" . $delBID2 . "' and Bgroup='" . $delGroup2 . "' and Bsort>='" . $delSort2 . "'";
				sql_query($sql);
			}
			
			# 실제 글 삭제하기
			$sql = "delete from 2011_boardData where IDX='" . $IDX . "'";
			sql_query($sql);
			
			# 그룹 재정렬
			$sql = "update 2011_boardData set Bsort = Bsort - 1 where BID='" . $delBID . "' and Bgroup='" . $delGroup . "' and Bsort>='" . $delSort . "'";
			sql_query($sql);


			# 패스워드 인증 해제
			$_SESSION["passIDX"] = 0;
			$_SESSION["passCheck"] = 0;
			echo "<script>alert('삭제되었습니다.');BoardGoMode('list');</script>";
			exit();				
		}
	} else {
		# 새글 쓰기 - 로그인한 회원의 이름 / ID / 패스워드  셋팅					
		$Bwriter = $Mname;
		$Bemail = $Memail;
	}
	
	if($dbBfile1)$FILE_INFO[1] = "현재 업로드된 파일 : " . $dbBfile1;
	if($dbBfile2)$FILE_INFO[2] = "현재 업로드된 파일 : " . $dbBfile2;
	if($dbBfile3)$FILE_INFO[3] = "현재 업로드된 파일 : " . $dbBfile3;
	if($dbBfile4)$FILE_INFO[4] = "현재 업로드된 파일 : " . $dbBfile4;
?>
<!-- 게시판부분 -->
<?
	$boardColor1="#CCCCCC";
	$boardColor2="#EFEFEF";
	
	if(!$Bwriter){ $Bwriter = $_SESSION[MANAGER_ID]; }
?>

<?if(!$noTitle){?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" >
<tr><td height="29" width="100%" align="left" style='padding-left:20px;' colspan=2>
<font style="font-size:12px"><b>글작성</b></font></td></tr>
</table>
<?}?>

<script type="text/javascript" src="/_Include/se/js/HuskyEZCreator.js" charset="utf-8"></script>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style='border:1px solid <?=$boardColor1?>;'>
	<tr>
		<td align="center" width="90" height="30" style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>'  ><font style="font-size:12px">제목</font></td>
		<td align=left style='padding-left:10px;'width=550><input type='text' name='inBtitle' class='inputBox' size="80" value='<?=$dbBtitle?>'></td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<?if(!$MID){?>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>' ><font style="font-size:12px">작성자</font></td>
		<td align=left style='padding-left:10px;'><input type='text' name='inBwriter' class='inputBox' value='<?=$dbBwriter?>'></td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>' ><font style="font-size:12px">비밀번호</font></td>
		<td align=left style='padding-left:10px;'><input type='text' name='inBPWD' class='inputBox'></td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>' ><font style="font-size:12px">이메일</font></td>
		<td align=left style='padding-left:10px;'><?=FN_Set_Email("","","inBemail")?></td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<?}else{?>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>' ><font style="font-size:12px">작성자</font></td>
		<td align=left style='padding-left:10px;'><?=$Bwriter?></td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>' ><font style="font-size:12px">이메일</font></td>
		<td align=left style='padding-left:10px;'><?=$Bemail?></td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<input type='hidden' name='inBwriter' value='<?=$Bwriter?>'>
	<input type='hidden' name='inBPWD' value='<?=$MPWD?>'>
	<input type='hidden' name='inBemail' value='<?=$Bemail?>'>
	<?}?>

	<? if($dbBsecret) { ?>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>' ><font style="font-size:12px">비밀글</font></td>
		<td align=left style='padding-left:10px;'><input type='checkbox' name='inBsecretCheck'  <?if($dbBsecretCheck)echo "checked"?> value='1'> 체크하시면 비밀글로 등록됩니다.</td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<?}?>
	<input type='hidden' name='inMID' value='<?=$MID?>'>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>'  ><font style="font-size:12px">내용</font></td>
		<td align=left style='height:300px;padding:10px;' ><textarea name="inBcontent" id="inBcontent" rows="10" cols="100" style="width:95%; height:500px; display:none;"><?=$dbBcontent?></textarea></td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	
	<!-- 첨부파일 처리 ---------------------------------------------------->
	<?for($i=1;$i<=$dbBfileCount;$i++){	?>
	<tr>
		<td align="center" width="90" height="30"  style='background-Color:<?=$boardColor2?>;border-right:1px solid <?=$borderColor1?>' ><font style="font-size:12px">첨부파일 <?=$i?></td>
		<td align=left style='padding-left:10px;' height=25 width='550'>
			<input type=file name='FILE<?=$i?>' style='width:65%;height:16px;'>
			<?	if($FILE_INFO[$i]) echo "<br>&nbsp;&nbsp;" . $FILE_INFO[$i]; ?>
		</td>
	</tr>
	<tr><td height="1" bgcolor="#dddddd" colspan="3" style="margin:0px; padding:0px;"></td></tr>
	<? } ?>
	<!-------------------------------------------------------------------->
</table>
<!-- /게시판부분 -->

<Br>
<!-- 버튼 -->
<table border="0" cellpadding="0" cellspacing="0" height=30 width='100%'>
	<tr>
		<td align=center>
			<img src='/image_3/cu_bt20.jpg' style='cursor:pointer;' onclick='BoardWriteOK()'  align=absmiddle>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='/image_3/cu_bt15.jpg' style='cursor:pointer;' onclick='BoardGoMode("list")'  align=absmiddle>
		</td>
	</tr>
</table>
<!-- /버튼 -->

<script>
	var oEditors = [];
	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "inBcontent",
		sSkinURI: "/_Include/se/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
				//alert("완료!");
			}
		}, //boolean
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
		
	function BoardWriteOK() {
		var frm = document.formObject;
		
		if(!VCheck(frm.inBtitle,"제목")) return false;
		if(!VCheck(frm.inBwriter,"작성자")) return false;
		if(!VCheck(frm.inBPWD,"비밀번호")) return false;

		/*================================================================
		기존 사용하던 에디터 빈값 체크 기능
		==================================================================*/
		oEditors.getById["inBcontent"].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.	
		if(document.getElementById("inBcontent").value == "") {
			alert('내용을 입력해 주세요.');
			document.getElementById("inBcontent").focus();
			return;
		}	
		frm.submit();
	}

</script>
