<?
	//$nPath = $nPath;
	if(@$nPath=="_manager") $isManagerMode=1;
	if(@$nPath=="_Admin") $isAdminMode=1;

	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";

	$sql = "select BPWD as BPWD,MID from 2011_boardData where IDX='" . $IDX . "'";
	$rs = sql_fetch($sql);

	if(!$rs['MID']) {
		echo "해당 게시물이 삭제되었거나 찾을 수 없습니다.";	
		exit();
	}
	
	if($MID || $ADMIN) {
		if($rs["MID"]==$MID || $ADMIN)	{
			$_SESSION["passIDX"] = $IDX;
			$_SESSION["passCheck"] = date(time());
			ob_clean();
			echo "PassOK";
			exit();
		}
	}
	
	# 패스워드가 없다면 기본 폼 출력
	if(!$Pass) {
		$str="<table width='100%' height='100%'><form name='passForm' id='passForm'><tr><td align=center valign=middle cellspacing=0>";		
		$str.="<table width='300' height='90' cellspacing=0 style='border:1px solid #0f0f0f;'>";
		$str.="<tr height=30><Td bgcolor='#1B5891' align=center><font color='#ffffff'><b>비밀번호를 입력하세요.</b></font></td></tr>";
		$str.="<tr height=30><Td bgcolor='#ffffff' align=center><input type='password' class='inputBox' name='inputPass' value='' onkeydown='if(event.keyCode==13){BoardPassCheck();return false;}'></tr>";
		$str.="<tr height=30><Td bgcolor='#ffffff' align=center><table><tr>";
		$str.="<td><div onclick='BoardPassCheck()' style='cursor:pointer;' style='border:1px solid #1B5891;width:50px;height:18px;background-color:#1B5891;color:#ffffff;padding-top:5px;'>확 인</div></td>";
		$str.="<td width=20></td>";
		$str.="<td><div onclick='BoardPassGet(0)' style='cursor:pointer;' style='border:1px solid #1B5891;width:50px;height:18px;background-color:#1B5891;color:#ffffff;padding-top:5px;'>취 소</div></td>";
		$str.="</tr></table>";
		
		$str.="</td></tr>";
		$str.="</table>";
		$str.="</td></tr></form></table>";
		echo $str;
	} else {
	# 패스워드가 있다면 검사
		
		$sql = "select old_password('" . $Pass . "') as pass";
		$pRs = sql_fetch($sql);
		
		if($rs['BPWD']==$pRs['pass']) {	
			$_SESSION["passIDX"] = $IDX;
			$_SESSION["passCheck"] = date(time());			
			ob_clean();
			echo "PassOK";
		} else {
			$str="<table width='100%' height='100%'><form name='passForm' id='passForm'><tr><td align=center valign=middle cellspacing=0>";		
			$str.="<table width='300' height='90' cellspacing=0 style='border:1px solid #0f0f0f;'>";
			$str.="<tr height=30><Td bgcolor='#1B5891' align=center><font color='#ffffff'><b>비밀번호 입력 오류</b></font></td></tr>";
			$str.="<tr height=30><Td bgcolor='#ffffff' align=center>비밀번호가 맞지 않습니다.</tr>";
			$str.="<tr height=30><Td bgcolor='#ffffff' align=center>";			
			$str.="<div onclick='BoardPassGet(0)' style='cursor:pointer;' style='border:1px solid #1B5891;width:50px;height:18px;background-color:#1B5891;color:#ffffff;padding-top:5px;'>확 인</div>";
			$str.="</td></tr>";
			$str.="</table>";
			$str.="</td></tr></form></table>";
			echo $str;
		}
		
	}
?>