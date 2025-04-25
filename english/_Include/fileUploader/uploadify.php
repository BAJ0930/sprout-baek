<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
if (!empty($_FILES)) {
	
	$targetPath = "/home/cheonyu/image". $_REQUEST['folder'] . '/';
	//$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	
	//-- 파일명 재생성
	for($k=1;$k<=20;$k++)
	{
		$fTemp = @$_FILES['Filedata' . $k]['name'];
		$tempFile = @$_FILES['Filedata' . $k]['tmp_name'];
		if($fTemp)break;
	}

	//$fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	// $fileTypes  = str_replace(';','|',$fileTypes);
	// $typesArray = split('\|',$fileTypes);
	// $fileParts  = pathinfo($_FILES['Filedata']['name']);	
	$fTemp2 = explode(".",$fTemp);
	$fileTypes = $fTemp2[sizeof($fTemp2)-1];
	
	$fName = date(time()) . "." . $fileTypes;

	$targetFile =  str_replace('//','/',$targetPath) . $fName;
	
	// if (in_array($fileParts['extension'],$typesArray)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
		
		move_uploaded_file($tempFile,$targetFile);
		//echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
		
		echo $fName;
		
	// } else {
	// 	echo 'Invalid file type.';
	// }
}
?>