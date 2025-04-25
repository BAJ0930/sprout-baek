<?php
// default redirection
$url = $_REQUEST["callback"].'?callback_func='.$_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

// SUCCESSFUL
if(bSuccessUpload) {

	$nowdate = date("Ym");
	if(!file_exists("/home/cheonyu/image/_DATA/editor/".$nowdate))
	{
		mkdir("/home/cheonyu/image/_DATA/editor/".$nowdate,0707);
	}

	//서버경로에 맞게 수정
	$abs_dir = "/home/cheonyu/image/_DATA/editor/".$nowdate;   //파일저장 절대경로
	$web_dir = "/_DATA/editor/".$nowdate;                  //웹경로
	//-----------------------------------------------------

	$tmp_name = $_FILES['Filedata']['tmp_name'];
	$name = $_FILES['Filedata']['name'];
	
	$filename_ext = strtolower(array_pop(explode('.',$name)));
	$allow_file = array("jpg", "png", "bmp", "gif");
	
	if(!in_array($filename_ext, $allow_file)) {
		$url .= '&errstr='.$name;
	} else {

		$m = substr(microtime(),2,4);
		//$filename = date("YmdHis").$m.eregi_replace("(.+)(\.[gif|jpg|png])","\\2",$_FILES['Filedata']['name']);
		$filename = date("YmdHis").$m.".".$filename_ext;
		$alt = $_FILES['Filedata']['name'];
		
		$u = "{$web_dir}/{$filename}";
		$result=move_uploaded_file($_FILES['Filedata']['tmp_name'], "{$abs_dir}/{$filename}");
		//$newPath = $uploadDir.urlencode();
		//@move_uploaded_file($tmp_name, $newPath);
		$url .= "&bNewLine=true";
		$url .= "&sFileName=".urlencode($filename);
		$url .= "&sFileURL=".$web_dir."/".urlencode($filename);
	}
}
// FAILED
else {
	$url .= '&errstr=error';
}
	
header('Location: '. $url);
?>