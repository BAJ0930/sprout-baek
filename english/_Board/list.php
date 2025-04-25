<?
	# 권한 검사 영역
	if(!$LAuth) {
		if($dbBLAuth==99)echo "<script>alert('접근 권한이 없습니다.');history.back();</script>";
		if($dbBLAuth==1)echo "<script>alert('회원가입을 해주세요.');history.back();</script>";
		exit();
	}
	# 선처리 영역
	
	# 해당 게시판 목록 가져오기
	$where = " where BID='" . $bid . "' ";	//- 기본 조건	
	
	#-- 자신의 글만 보여주기일 경우 답변 갯수도 가져온다. (답변이 달린경우 count(Bgroup) 의 수가 2이상
	if($dbBonlySelf && !$ADMIN) {

		$sql = "Select *,count(Bgroup) as reCount from 2011_boardData ";
		if($s2) $sql.= " and B" . $s1 . " like '%" . $s2 . "%' ";
		$groupby = " group by BID,Bgroup having MID='" . $MID . "'";

	} else {
		
		# 검색 처리
		if($s2) $where.= " and B" . $s1 . " like '%" . $s2 . "%' ";
		
		//-- 댓글 카운트 쿼리
		$sql = "Select a.*,count(b.IDX) as Ccnt from 2011_boardData a left outer join 2011_boardComment b on a.IDX = b.BIDX ";
		$groupby = " group by a.IDX ";
	
	}
	$sql .= $where . $groupby;
	$result = sql_query($sql);
	$TotalCount = mysqli_num_rows($result);
	if (!$page) $page = 1;
	$CntPerPage = 20;
	$PagePerList = 5;
	$StartPos = ($page - 1) * $CntPerPage;
	$sql .= " ORDER BY Bgroup DESC, Bsort ASC ";
	$sql .= " LIMIT ".($page - 1) * $CntPerPage.",".$CntPerPage;
	$result = sql_query($sql);	
?>
<!-- 게시판부분 -->
								
<?
	#-- 자신의 글만 보기 형태
	if($dbBonlySelf && !$ADMIN) {
?>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
	<tr><td colspan="4" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
	<tr>
		<th style="height:30px;"  scope="col">제목</th>
		<th width="80" scope="col">작성자</th>
		<th width="80" scope="col">작성일자</th>
		<th width="80" scope="col" class="none">답변</th>
	</tr>
	<tr><td colspan="4" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
	<tr><td colspan="4"></td></tr>
<?
	#-- 일반 글 목록 형태
	}else{
?>
<table border="0" cellspacing="0" cellpadding="0" width=100%>
	<tr><td colspan="4" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
	<tr>
		<th style="height:30px;" width="50" scope="col">No</th>
		<th scope="col">Subject</th>
		<th width="80" scope="col" class="none">Date</th>
	</tr>
	<tr><td colspan="4" style="border-top:1px #CCCCCC solid; height:1px; margin:0; padding:0"></td></tr>
	<tr><td colspan="4"></td></tr>
<? 
	} 


	while($rs = sql_fetch_array($result)){
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){
			$fieldName = "db" . $fieldName;
			$$fieldName = $fieldValue;
		}
		
		$linkStr="";
		$sp="";			
		$lockImg="";
		$fileExe="";
		$fileImg="";
		
		for($r=0;$r<$dbBlevel;$r++){
			$sp.= "<span style='padding-left:10px;'></span>";
		}
		if($sp) {
			$sp.= "<span style='padding-right:10px;color:<?=$boardColor1?>;font-weight:bold;font-size:12px;'>↘</span>";
			$dbBgroup="";
		}
		
		# 검색어 색상 강조
		if($s2){
			if($s1=="title")$dbBtitle = str_replace($s2,"<font style='font-weight:bold;color:<?=$boardColor1?>'>"  . $s2 . "</font>",$dbBtitle);
			if($s1=="writer")$dbBwriter = str_replace($s2,"<font style='font-weight:bold;color:<?=$boardColor1?>'>"  . $s2 . "</font>",$dbBwriter);
		}
		$linkStr="<a href='javascript:BoardGoMode(\"view\",\"\",\"" . $dbIDX . "\")'>";	
		
		#-- 비밀글 체크
		if($dbBsecret && $dbBsecretCheck){
			$lockImg = "&nbsp;<img src='/image_1/lock.jpg' align=absmiddle>";
		}
		
		#-- 첨부파일 확장자 아이콘 처리
		for($floop=1;$floop<5;$floop++){
			$fileName = $rs["Bfile" . $floop];
			
			if($fileName){
				$fileTmp = explode(".",$fileName);
				
				$fileExe = $fileTmp[sizeof($fileTmp)-1];
				if($fileExe=="docx") $fileExe="doc";
				
				break;
			}
		}
		if($fileExe)$fileImg="<img src='/_Board/fileImage/" . $fileExe . ".gif' align='absmiddle' border=0>";

		//-- 댓글 갯수 처리
		if($dbCcnt)$dbBtitle = $dbBtitle . " <b>[" . $dbCcnt . "]</b>";
		
		#-- 자신의 글만 보기 형태
		if($dbBonlySelf && !$ADMIN) {
			if($dbreCount>1)$reTxt = "답변완료";
			else $reTxt = "처리중";
		?>	
	<tr>
		<td class="notice_titie"><?=$sp.$linkStr . $dbBtitle?></a> <?=$fileImg?></td>
		<td style="height:40px;"><center><?=$dbBwriter?></td>
		<td><center><?=date("y/m/d",$dbBregDate)?></td>
	</tr>
	<tr><td colspan="4" style="border-top:1px #cccccc solid; height:1px; margin:0; padding:0"></td></tr>
	<?
		#-- 일반 전체보기 형태
		}else{
	?>
	<tr>
		<td style="height:40px;" ><center><?=$dbBgroup?></td>
		<td class="notice_titie"><?=$sp.$linkStr . $dbBtitle?></a> <?=$fileImg?> <?=$lockImg?></td>
		<td><center><?=date("Y/m/d",$dbBregDate)?></td>
	</tr>		
	<tr><td colspan="4" style="border-top:1px #cccccc solid; height:1px; margin:0; padding:0"></td></tr>
	<? } ?>
	
<?}?>
</table>
<!-- /게시판부분 -->

<!-- 페이지 / 버튼 -->
<div align="center" id="paging"><br/><? echo page_nav($TotalCount,$CntPerPage,$PagePerList,$page,$option); ?></div>

<!-- /페이지 / 버튼 -->