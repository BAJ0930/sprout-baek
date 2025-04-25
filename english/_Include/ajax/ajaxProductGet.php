<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	
$tUrl = str_replace("http://","",strtolower($tUrl));
$urlArray = explode("/",$tUrl);
$len = sizeof($urlArray);

$host="";
$url="";

for($k=0;$k<$len;$k++)
{
	if($k==0)$host=$urlArray[$k];
	else
	{
		$url.="/" . $urlArray[$k];
	}
}

$port = 80 ;
//$host = 'cheonyu.com';
//$url = '/temp/test16_target.php';
$fp = fsockopen($host, $port, $errno, $errstr, 30);
if (!$fp) {
    //echo "$errstr ($errno)<br />\n";
    echo "error";
} else {
    $out = "GET ${url} HTTP/1.1\r\n";
    $out .= "Host: ${host}\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        $result .=  fgets($fp, 128);
    }
    fclose($fp);
}

	
$result = substr($result,strpos($result,"<html>"));

##======= 상품 이미지 추출

//서버경로에 맞게 수정
$abs_dir = $_SERVER["DOCUMENT_ROOT"] ."/_DATA/uploaderTemp/";   //파일저장 절대경로
$web_dir = "/_DATA/uploaderTemp/";//웹경로
//-----------------------------------------------------

//$result = iconv("EUC-KR","UTF-8",$result);
$temp1 = explode("TnSwitchImageBG('",$result);
//$temp1 = explode("상품 이미지 50x50",$result); ??? 한글로 왜 explode 가 안되지?
//echo $temp1[1];

//$temp2 = explode("','",$temp1[1]);

$len = sizeof($temp1);
$fCount=0;
for($k=0;$k<$len;$k++)
{
	$temp3 = explode("','",$temp1[$k+1]);
	$fName = $temp3[0];	
	if($fName)
	{
		$fCheck = explode("/",$fName);
		//업로드 이미지 저장
    $m = substr(microtime(),2,4);
    $filename = "auto" . date("YmdHis").$m.eregi_replace("(.+)(\.[gif|jpg|png])","\\2",$fCheck[sizeof($fCheck)-1]);
		$filename = str_replace(">","",$filename);
		$openUrl = str_replace("http://","",$fName);
		$temp = explode("/",$openUrl);
		$imgHost = $temp[0];
		$imgUrl = str_replace($imgHost,"",$openUrl);
		$readFile="";
		$fp = fsockopen($imgHost, 80, $errno, $errstr, 30);
		if (!$fp) {
		    //echo "$errstr ($errno)<br />\n";
		    echo "error";
		} else {
		    $out = "GET ${imgUrl} HTTP/1.1\r\n";
		    $out .= "Host: ${imgHost}\r\n";
		    $out .= "Connection: Close\r\n\r\n";
		    fwrite($fp, $out);
		    while (!feof($fp)) {
		        $readFile.=  fgets($fp, 128);
		    }
		    fclose($fp);
		}
		
		$response = rtrim(substr($readFile, (strpos($readFile, "\r\n\r\n")+4)));
					
		$wopen = fopen($abs_dir . "/" . $filename,"w");
		$write = fwrite($wopen,$response);
		fclose($wopen);
    
    $thumbSrc[$fCount]["fName"] = $filename;
    $fCount++;
  }
	
}
##======= 상품 이미지 추출 END


##======= 상품 이미지 서버에 저장






##======= 상품명 추출
$temp1 = explode('<td class="black_16px_bold" style="padding-top:11px;">',$result);
$temp2 = explode('</td>',$temp1[1]);
//echo iconv("EUC-KR","UTF-8",$temp2[0]);
$Pname = iconv("EUC-KR","UTF-8",$temp2[0]);
##======= 상품명 추출 END



##======= 소비자가 추출
$temp1 = explode('<td class="black_11px_bold" style="padding:2px 5px 0 0;">',$result);
$temp2 = explode('</td>',$temp1[1]);
$temp2[0] = substr($temp2[0],0,strpos($temp2[0]," "));

$temp2[0] = str_replace(",","",$temp2[0]);
//echo $temp2[0];
$Pprice = $temp2[0];
##======= 소비자가 추출 END


##======= 옵션 추출
$temp1 = explode("<select name='item_option'  class='input_default'>",$result);

#-- 옵션 존재여부 체크
if(sizeof($temp1>1))
{	
	$temp22 = explode("</select>",$temp1[1]);
	$temp2 = explode("<option",$temp22[0]);
	
	
	$len = sizeof($temp2);
	for($k=1;$k<$len;$k++)
	{
		$temp3 = substr($temp2[$k],strpos($temp2[$k],">")+1,strpos($temp2[$k],"<"));
		
		$temp3 = str_replace("</option>","",$temp3);
		
		if($k==1)$opType = iconv("EUC-KR","UTF-8",$temp3);
		else $opValue[$k-1] = iconv("EUC-KR","UTF-8",$temp3);
	}
	
	/*
	#-- 옵션 체크
	$len = sizeof($opValue);
	for($k=1;$k<=$len;$k++)
	{
		echo $opType . " : " . $opValue[$k] . "<br>";
	}
	*/
	
	$PoptionCount=$len;
}
else
{
	$PoptionCount=0;
}




##======= 옵션 추출 END

##======= 재료 / 크기 / 무게 / 제조사  추출  (적용은 보류)
$temp1 = explode('border-bottom:1px solid #eaeaea;padding-top:3px;">',$result);

$len = sizeof($temp1);

for($k=1;$k<$len;$k++)
{
	$temp2 = explode('</td>',$temp1[$k]);
	$str = $temp2[0];
	
	if($k==2)
	{
		$temp3 = explode("(",$str);
		$str = $temp3[0];
	}
	else if($k==3)
	{
		$temp3 = explode("/",$str);
		$str = $temp3[0];
	}
	
	//echo iconv("EUC-KR","UTF-8",$str) . "<br>";
}
##======= 재료 / 크기 / 제조사  추출 END



##======= 상세내용 추출

$temp1 = explode('<span class="gray11px02">',$result);

if(sizeof($temp1)==1)
{
	$temp1 = explode('<div class="gray11px02">',$result);
	$temp2 = explode('</div>',$temp1[1]);
}
else
{
	$temp2 = explode('</span>',$temp1[1]);
}



//-- 불필요한 태그 삭제
//$htmlTag = strtolower($temp2[0]);	//-- 소문자로 변환
$htmlTag = $temp2[0];
$htmlTag = str_replace("<CENTER>","",$htmlTag);
$htmlTag = str_replace("<br>","",$htmlTag);
$htmlTag = str_replace("</br>","",$htmlTag);
$htmlTag = str_replace("<p>","",$htmlTag);
$htmlTag = str_replace("</p>","",$htmlTag);
$htmlTag = str_replace("<center>","",$htmlTag);
$htmlTag = str_replace("</center>","",$htmlTag);

$temp3 = explode("<",$htmlTag);

$len = sizeof($temp3);

//echo $len;

$objCnt=0;
for($k=1;$k<$len;$k++)
{	
	$temp4 = $temp3[$k];	
	//echo $temp4;	
	//-- 객체 타입 체크
	if(strtolower(substr($temp4,0,3))=="img")
	{
		$cObj[$objCnt]["type"]="img";
		
	}
	else if(substr($temp4,0,5)=="embed")
	{
		$cObj[$objCnt]["type"]="embed";
		
	}	
	else
	{
		//-- 알수없는 타입이면 그냥 빽
		//echo "찾을 수 없음 : " . $temp4 . "<br>";
		continue;
	}
	
	//-- 객체 주소 체크
	$temp5=explode("src=",$temp4);
	
	$temp5[1] = str_replace("'","",$temp5[1]);
	$temp5[1] = str_replace('"',"",$temp5[1]);	
	
	#-- 공백으로 구분?
	$temp6=explode(" ",$temp5[1]);
	if(sizeof($temp6)>1)$cObj[$objCnt]["src"]=$temp6[0];
	else 
	{
		$temp6=explode('>',$temp5[1]);
		if(sizeof($temp6)>1)$cObj[$objCnt]["src"]=$temp6[0];
		else
		{
				//echo "없다??";
		}
	}
	
	//echo "<-- " . $k . " " .   $cObj[$k]["type"]  . "<br>";
	
	if($cObj[$objCnt]["src"])
	{
		//-- 폭 체크
		$temp5=explode("width=",$temp4);
		
		$temp5[1] = str_replace("'","",$temp5[1]);
		$temp5[1] = str_replace('"',"",$temp5[1]);	
		
		#-- 공백으로 구분?
		$temp6=explode(" ",$temp5[1]);
		if(sizeof($temp6)>1)$cObj[$objCnt]["width"]=$temp6[0];
		else 
		{
			$temp6=explode('>',$temp5[1]);
			if(sizeof($temp6)>1)$cObj[$objCnt]["width"]=$temp6[0];
			else
			{
					//echo "없다??";
			}
		}
		
		//-- 높이 체크
		$temp5=explode("height=",$temp4);
		
		$temp5[1] = str_replace("'","",$temp5[1]);
		$temp5[1] = str_replace('"',"",$temp5[1]);	
		
		#-- 공백으로 구분?
		$temp6=explode(" ",$temp5[1]);
		if(sizeof($temp6)>1)$cObj[$objCnt]["height"]=$temp6[0];
		else 
		{
			$temp6=explode('>',$temp5[1]);
			if(sizeof($temp6)>1)$cObj[$objCnt]["height"]=$temp6[0];
			else
			{
					//echo "없다??";
			}
		}
	}	
	
	$objCnt++;
}


#---- 파싱 끝 이미지 서버에 저장

//월별로 저장경로 생성
$nowdate = date("Ym");
if(!file_exists($_SERVER["DOCUMENT_ROOT"] ."/_DATA/editor/".$nowdate))mkdir($_SERVER["DOCUMENT_ROOT"] ."/_DATA/editor/".$nowdate,0707);

//서버경로에 맞게 수정
$abs_dir = $_SERVER["DOCUMENT_ROOT"] ."/_DATA/editor/".$nowdate;   //파일저장 절대경로
$web_dir = "/_DATA/editor/".$nowdate;                  //웹경로
//-----------------------------------------------------

$len = sizeof($cObj);
for($k=0;$k<$len;$k++)
{
	if($cObj[$k]["type"])
	{
		//echo "타입 : " . $cObj[$k]["type"] . " / ";
		//echo "src : " . $cObj[$k]["src"] . " / ";
		//echo "width : " . $cObj[$k]["width"] . " / ";
		//echo "height : " . $cObj[$k]["height"] . " / ";
		//echo "<br>--------------------------<br>";
		
		#-- 이미지 일때만 파일명 추출 및 서버 저장 -> 새로운 경로 할당
				
		if($cObj[$k]["type"]=="img")
		{
			$fCheck = explode("/",$cObj[$k]["src"]);
			//업로드 이미지 저장
	    $m = substr(microtime(),2,4);
	    $filename = "auto" . date("YmdHis").$m.eregi_replace("(.+)(\.[gif|jpg|png])","\\2",$fCheck[sizeof($fCheck)-1]);
			$filename = str_replace(">","",$filename);
			$openUrl = str_replace("http://","",$cObj[$k]["src"]);
			$temp = explode("/",$openUrl);
			$imgHost = $temp[0];
			$imgUrl = str_replace($imgHost,"",$openUrl);
			$readFile="";
			$fp = fsockopen($imgHost, 80, $errno, $errstr, 30);
			if (!$fp) {
			    //echo "$errstr ($errno)<br />\n";
			    echo "error";
			} else {
			    $out = "GET ${imgUrl} HTTP/1.1\r\n";
			    $out .= "Host: ${imgHost}\r\n";
			    $out .= "Connection: Close\r\n\r\n";
			    fwrite($fp, $out);
			    while (!feof($fp)) {
			        $readFile.=  fgets($fp, 128);
			    }
			    fclose($fp);
			}
			
			$response = rtrim(substr($readFile, (strpos($readFile, "\r\n\r\n")+4)));

						
			$wopen = fopen($abs_dir . "/" . $filename,"w");
			$write = fwrite($wopen,$response);
			fclose($wopen);
	    
	    /*
	    echo "wget " . $cObj[$k]["src"] . " -O " . $abs_dir . "/" . $filename . "<br><br>";	    
	    echo exec("wget " . $cObj[$k]["src"] . " -O " . $abs_dir . "/" . $filename);
	    */
			$cObj[$k]["newsrc"] = "{$web_dir}/{$filename}";
		}
		
	}
	else
	{
		//echo "뭐야이건? 없다?";
		//echo "<br>--------------------------<br>";
	}
}

#---- 파싱 끝 결과 확인 END





/*===================================================================
	파싱 결과 출력
=====================================================================*/

//-- 각 항목 구분자 : ##

echo "##" . $Pname;		//-- 제품명
echo "##" . $Pprice;	//-- 가격

echo "##";

//-- 옵션 정보
if($PoptionCount)
{
	for($k=1;$k<$PoptionCount;$k++)
	{
		
		if($k==1)echo $opType;	
		else echo $opValue[$k-1];
		if($k!=$PoptionCount-1)echo "|";
	}
}
echo "##";

$len = sizeof($cObj);

for($k=0;$k<$len;$k++)
{	
	if($cObj[$k]["type"])
	{
		echo $cObj[$k]["type"] . "|";
		echo $cObj[$k]["src"] . "|";
		echo $cObj[$k]["width"] . "|";
		echo $cObj[$k]["height"] . "|";
		echo $cObj[$k]["newsrc"];
	}
	echo "@@";	//-- 파일 객체 구분자
}

echo "##";

$len = sizeof($thumbSrc);
for($k=0;$k<$len;$k++)
{
	if($thumbSrc[$k]["fName"])echo $thumbSrc[$k]["fName"] . "|";	
}


##======= 상세내용 추출 END
	
	
?>
