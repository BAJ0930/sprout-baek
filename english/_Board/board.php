<?	
	# 게시판 관련 정보 로드
	$sql = "select * from 2011_boardInfo where BID='" . $bid . "'";
	$infoRs = sql_fetch($sql);

	if(!$infoRs['IDX']){
		echo "<script> alert('권한이 없습니다.'); location.href = '/'; </script>";
		exit;
	}
		
	# DB 레코드 결과값 모두 변수로 전환
	foreach ($infoRs as $fieldName => $fieldValue)
	{
		$fieldName = "db" . $fieldName;
		$$fieldName = $fieldValue;
	}
	
	# 권한 체크
	$LAuth=0;
	$RAuth=0;
	$WAuth=0;
	$REAuth=0;
	$CAuth=0;
	
	/*
	if(($dbBLAuth==99 && $ADMIN) || ($dbBLAuth==10 && $MID) || ($dbBLAuth==0))$LAuth=1;
	if(($dbBRAuth==99 && $ADMIN) || ($dbBRAuth==10 && $MID) || ($dbBRAuth==0))$RAuth=1;
	if(($dbBWAuth==99 && $ADMIN) || ($dbBWAuth==10 && $MID) || ($dbBWAuth==0))$WAuth=1;
	if(($dbBREAuth==99 && $ADMIN) || ($dbBREAuth==10 && $MID) || ($dbBREAuth==0))$REAuth=1;
	if(($dbBCAuth==99 && $ADMIN) || ($dbBCAuth==10 && $MID) || ($dbBCAuth==0))$CAuth=1;
	*/

	if($dbBLAuth<=$Mlevel || $dbBLAuth==0)$LAuth=1;
	if($dbBRAuth<=$Mlevel || $dbBRAuth==0)$RAuth=1;
	if($dbBWAuth<=$Mlevel || $dbBWAuth==0)$WAuth=1;
	if($dbBREAuth<=$Mlevel || $dbBREAuth==0)$REAuth=1;
	if($dbBCAuth<=$Mlevel || $dbBCAuth==0)$CAuth=1;
	

	# 금지어 스크립트 변수화
	echo "<script>userBanString='" . $dbBbanWord . "'</script>";
	
	echo "<script language=javascript  src='/_Include/js/board.js'></script>\n";
	
	# 공통사용 폼 시작
	echo "<form name='formObject' method='post' enctype='multipart/form-data' onsubmit='return false;'>\n";
	
	# 현재 기능 페이지 인크루드
	if(!$pmode)$pmode="list";
	include $_SERVER["DOCUMENT_ROOT"] . "/_Board/" . $pmode . ".php";
	
	# 공통사용 폼 닫기
	echo "</form>\n";

	if($dbBuseComment && $pmode=="view"){?>
			<!-- 댓글 시작 -->
		<table class=tbl width='100%'>
		<tr>
			<td id='commentDiv'></td>
			</tr>
			<script>
			fnCommentCall('<?=$IDX?>','');
			</script>
		</table>
	<?}?>
	<!-- 댓글 끝 -->	
