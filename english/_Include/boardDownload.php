<?	
exit;
	include $_SERVER["DOCUMENT_ROOT"]  . "/_Include/systemInit.php";
	ob_clean();
	
	$sql = "select * from 2011_boardData where IDX=".$BIDX;
	$rs = sql_fetch($sql);
	
	$filename=$rs["BsaveFile$FILE_NUM"];
	$newfilename=$rs["Bfile$FILE_NUM"];
	
	$filename = trim($filename);
	$upPath = $_SERVER["DOCUMENT_ROOT"] . "/_DATA/Board/" . $BID . "/";
	$file = $upPath . $filename;

	if(!file_exists($file)){
		echo "<script>alert('Sorry, file is not existed.');history.back();</script>";
	}else{
		ob_end_clean();
		
		$file_size = filesize($file);
		
		header("Content-Type: application/octet-stream");
		Header("Content-Disposition: attachment;; filename=$newfilename");
		header("Content-Transfer-Encoding: binary");
		Header("Content-Length: ".(string)(filesize($file)));
		Header("Cache-Control: cache, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0"); 
		
		$fp = fopen($file, "r"); 
		if(!fpassthru($fp)) fclose($fp);
	}
?> 