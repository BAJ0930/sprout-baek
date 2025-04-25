<?php

include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();
	
#-- 기존 업로드 파일 삭제(휴지통으로)
if (!empty($_POST["saveFile"]) && $_POST["saveFile"]!="product/"){
	
	$sFile1 = __HOME_SERVER_PATH__ . "/_DATA/" . $_POST["saveFile"];
	if(file_exists($sFile1))
	{
		$temp = explode("/",$_POST["saveFile"]);

		//-- DIR Check
		if(!file_exists(__HOME_SERVER_PATH__ . "/_DATA/recycle bin/" . $temp[0] . "/"))
		{
			mkdir(__HOME_SERVER_PATH__ . "/_DATA/recycle bin/" . $temp[0] . "/",0777);
		}
		
		if(!file_exists(__HOME_SERVER_PATH__ . "/_DATA/recycle bin/" . $temp[0] . "/" . $temp[1] . "/"))
		{
			if(sizeof($temp)>2) mkdir(__HOME_SERVER_PATH__ . "/_DATA/recycle bin/" . $temp[0] . "/" . $temp[1] . "/",0777);
		}
		
		$sFile2 = __HOME_SERVER_PATH__ . "/_DATA/recycle bin/" . $_POST["saveFile"] ;	
		@rename($sFile1, $sFile2);
		echo "##saveDeleted";
	}
}

		echo "2222";
		exit;
#-- 임시 등록파일 삭제
if (!empty($_POST["tempFile"])){
	$tFile = __HOME_SERVER_PATH__ . "/_DATA/uploaderTemp/" . $_POST["tempFile"];	
	if(file_exists($tFile))
	{
		@unlink($tFile);
		echo "##tempDeleted";
	}
}



?>