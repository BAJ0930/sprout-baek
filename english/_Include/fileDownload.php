<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";

	//$sql = "select " . $fldCode . "realFile" . $fNum . " AS rFile," . $fldCode . "saveFile" . $fNum . " AS sFile from 2011_" . $tbl . "Info where IDX='" . $qIDX . "' AND RTMID = '" . $MID . "' AND RTdeleted = '0' ";
	$sql = "select " . $fldCode . "realFile" . $fNum . " AS rFile," . $fldCode . "saveFile" . $fNum . " AS sFile from 2011_" . $tbl . "Info where IDX='" . $qIDX . "' ";
	$rs = sql_fetch($sql);
	
	$filename=$rs['sFile'];
	$newfilename=urlencode($rs['rFile']);

	if(!$filename){
		echo "
			<script>
				alert('Sorry, file is not existed.');
				history.go(-1);
			</script>
		";
		exit;
	}
	
	$filename = iconv("EUC-KR","UTF-8",trim($filename));
	$upPath = "/home/cheonyu/image/_DATA/" . $fcode . "/";
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