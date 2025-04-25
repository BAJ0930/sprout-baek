<?	
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	

	//-- 값 체크
	//fcode,tbl,fldCode,qIDX,fNum
	
		//-- 에이 꼬였네... 강제 변환
		if($tbl=="bigsale")$tbl="product";
	
if(strlen($fcode) > 20) exit;

/*if(strpos($tbl,"Info")===false)$sql = "select " . $fldCode . "realFile" . $fNum . "," . $fldCode . "saveFile" . $fNum . " from 2011_" . $tbl . "Info where IDX='" . $qIDX . "'";
else $sql = "select " . $fldCode . "realFile" . $fNum . "," . $fldCode . "saveFile" . $fNum . " from 2011_" . $tbl . " where IDX='" . $qIDX . "'";*/
$sql = "select " . $fldCode . "realFile" . $fNum . " as readFile ," . $fldCode . "saveFile" . $fNum . " as saveFile from 2011_" . $tbl . " where IDX='" . $qIDX . "'";


$rs = sql_fetch($sql);

$filename=$rs['saveFile'];
$newfilename=urlencode($rs['readFile']);

$filename = iconv("EUC-KR","UTF-8",trim($filename));
$upPath = $imageServerRoot."/_DATA/" . $fcode . "/";


$file = $upPath . $filename;

if(!file_exists($file)){
	//-- UTF8 로 셋팅
	ob_clean();
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
	echo "<script>alert('Sorry, file is not existed.');history.back();</script>";
}else{
	ob_clean();
	$file_size = filesize($file);
	header("Content-Type: application/octet-stream");
	Header("Content-Disposition: attachment;; filename=" . $newfilename);
	header("Content-Transfer-Encoding: binary");
	Header("Content-Length: ".(string)(filesize($file)));
	Header("Cache-Control: cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: 0"); 	
		
   $fp = fopen($file, "r"); 
   if(!fpassthru($fp)) fclose($fp);

}
?> 