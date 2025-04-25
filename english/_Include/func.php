<?
// 메타태그를 이용한 URL 이동
// header("location:URL") 을 대체
function goto_url($url)
{
    $url = str_replace("&amp;", "&", $url);
    //echo "<script> location.replace('$url'); </script>";

    if (!headers_sent())
        header('Location: '.$url);
    else {
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}

/*************************************************************************
**
**  SQL 관련 함수 모음
**
*************************************************************************/

function sql_query($sql, $link=null){
	global $g5;
	
	if(!$link) $link = $g5['connect_db'];

	// Blind SQL Injection 취약점 해결
	$sql = trim($sql);
	
	// union의 사용을 허락하지 않습니다.
	//$sql = preg_replace("#^select.*from.*union.*#i", "select 1", $sql);
	//$sql = preg_replace("#^select.*from.*[\s\(]+union[\s\)]+.*#i ", "select 1", $sql);
	
	// `information_schema` DB로의 접근을 허락하지 않습니다.
	$sql = preg_replace("#^select.*from.*where.*`?information_schema`?.*#i", "select 1", $sql);
	
	$sql = str_ireplace ("sleep", "", $sql);

	//$result = mysqli_query($link, $sql) or die("<p>$sql<p>" . mysqli_errno($link) . " : " .  mysqli_error($link) . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
	$result = mysqli_query($link, $sql) or die("##error##".$sql);
	
	return $result;
}

// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch($sql, $link=null){
	global $g5;

	if(!$link) $link = $g5['connect_db'];

	$result = sql_query($sql, $link);
	//$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysqli_errno() . " : " .  mysqli_error() . "<p>error file : $_SERVER['SCRIPT_NAME']");
	$row = sql_fetch_array($result);
	return $row;
}

// 결과값에서 한행 연관배열(이름으로)로 얻는다.
function sql_fetch_array($result){
	$row = @mysqli_fetch_assoc($result);
	return $row;
}

function fnLoadBrandName(){
	global $g5;

	if(!$link) $link = $g5['connect_db'];

	$query = " SELECT IDX,BRename FROM 2011_brandInfo WHERE BRdeleted = '0' ";
	$result = sql_query($query);
	$fnbrand = array();
    while ($data = sql_fetch_array($result)){
		$fnbrand[$data['IDX']] = $data['BRename'];
	}

	return $fnbrand;
}

function fnLoadMKName(){		
	global $g5;

	if(!$link) $link = $g5['connect_db'];

	$query = "SELECT IDX,MKename FROM 2011_makerInfo WHERE MKdeleted = 0";
	$result = sql_query($query);
	$fnmaker = "";
	while($data = sql_fetch_array($result)){
		$fnmaker[$data[IDX]] = $data[MKename];
	}

	return $fnmaker;
}

#=============================================================================================================================
# 글자수 줄이기 (UTF 용)
# 인자값 : 원본문자열 , 글자 제한수
#=============================================================================================================================
function strcut_utf8($str, $len, $checkmb=false, $tail='...') {
	preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);

	$m = $match[0];
	$slen = strlen($str); // length of source string
	$tlen = strlen($tail); // length of tail string
	$mlen = count($m); // length of matched characters

	if ($slen <= $len) return $str;
	if (!$checkmb && $mlen <= $len) return $str;

	$ret = array();
	$count = 0;

	for ($i=0; $i < $len; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?1:0.5;
		if ($count + $tlen > $len) break;
		$ret[] = $m[$i];
	}

	return join('', $ret).$tail;
}

// 변수 또는 배열의 이름과 값을 얻어냄. print_r() 함수의 변형
function print_r2($var)
{
    ob_start();
    print_r($var);
    $str = ob_get_contents();
    ob_end_clean();
    $str = str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
}


/*===============================================================
■ ★★★ 중요 ★★★ 구매 가격계산 함수 ★★★ 중요 ★★★
===============================================================*/
function fnGetPriceMem($no, $no1 = 0, $no2 = 0){
	global $MLV;
	if($no == "2"){
		if($MLV == "A") $ret = "30";
		else if($MLV == "B") $ret = "90";
		else if($MLV == "C")	$ret = "60";
		else if($MLV == "D") $ret = "120";
	} else if($no == "1"){
		if($MLV == "A") $ret = "20";
		else if($MLV == "B") $ret = "80";
		else if($MLV == "C")	$ret = "50";
		else if($MLV == "D") $ret = "110";
	} else {
		if($MLV == "A") $ret = "10";
		else if($MLV == "B") $ret = "70";
		else if($MLV == "C")	$ret = "40";
		else if($MLV == "D") $ret = "100";
	}
	return $ret;
}

function fnGETMemArr(){
	global $MLV;
	if($MLV == "A"){
		$buyCode[1] = "Pdiscount10";
		$buyCode[2] = "Pdiscount20";
		$buyCode[3] = "Pdiscount30";
	} else if($MLV == "B"){
		$buyCode[1] = "Pdiscount70";
		$buyCode[2] = "Pdiscount80";
		$buyCode[3] = "Pdiscount90";
	} else if($MLV == "C"){
		$buyCode[1] = "Pdiscount40";
		$buyCode[2] = "Pdiscount50";
		$buyCode[3] = "Pdiscount60";
	} else if($MLV == "D"){
		$buyCode[1] = "Pdiscount100";
		$buyCode[2] = "Pdiscount110";
		$buyCode[3] = "Pdiscount120";
	} else {
		$buyCode[1] = "Pdiscount";
		$buyCode[2] = "Pdiscount";
		$buyCode[3] = "Pdiscount";
	}
	return $buyCode;
}

function fnGETPricePer($rs, $view = 0){
	global $MLV, $g5;
	if($rs['PboxCount']>0 || $rs['PboxCount2']>0) {		
		if($rs['PoptionUse'] == 1){
			if($rs['PstockCount'] >= $rs['PboxCount'] || $rs['PstockCount'] >= $rs['PboxCount2']) $row1['cnt'] = 1;
			else $row1['cnt'] = 0;
		} else {
			$query1 = " SELECT count(IDX) as cnt FROM 2011_productOption WHERE PIDX = '".$rs['IDX']."' AND OPhidden = '0' AND ( OPstock >= '".$rs[PboxCount]."' or OPstock >= '".$rs[PboxCount2]."') ";
			$row1 = sql_fetch($query1);
		}
	}
	if($MLV == "A"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount30'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount20'];
		else $ret = $rs['Pdiscount10'];
	} else if($MLV == "B"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount90'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount80'];
		else $ret = $rs['Pdiscount70'];
	} else if($MLV == "C"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount60'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount50'];
		else $ret = $rs['Pdiscount40'];
	} else if($MLV == "D"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount120'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount110'];
		else $ret = $rs['Pdiscount100'];
	} else {
		$ret = $rs['Pdiscount'];
	}

	if($row1['cnt'] > 0) {
		if($view == 1)	{
			$retTxt = "~ ".$ret."%"; 
		} else if($view == 2) {
			$retTxt = "~<font color=#0282F0><strong>".$ret."</font></strong>%";
		} else {
			$retTxt = "~<strong>".$ret."</strong>%"; 
		}
	} else {
		$retTxt = "";
	}
	if(!$MLV) $retTxt = "";	
	return $retTxt;
}

function fnGETPricePerCart($rs){
	global $MLV, $g5;

	if($rs['PboxCount']>0 || $rs['PboxCount2']>0) {		
		if($rs['PstockCount'] >= $rs['PboxCount'] || $rs['PstockCount'] >= $rs['PboxCount2']) $row1['cnt'] = 1;
		else $row1['cnt'] = 0;
	}

	if($MLV == "A"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount30'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount20'];
		else $ret = $rs['Pdiscount10'];
	} else if($MLV == "B"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount90'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount80'];
		else $ret = $rs['Pdiscount70'];
	} else if($MLV == "C"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount60'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount50'];
		else $ret = $rs['Pdiscount40'];
	} else if($MLV == "D"){
		if($rs['PboxCount2'] > 0) $ret = $rs['Pdiscount120'];
		else if($rs['PboxCount'] > 0) $ret = $rs['Pdiscount110'];
		else $ret = $rs['Pdiscount100'];
	} else {
		$ret = $rs['Pdiscount'];
	}

	if($row1['cnt'] > 0) {
		$retTxt = "~".$ret."%"; 
	} else {
		$retTxt = "";
	}
	if(!$MLV) $retTxt = "";
	
	return $retTxt;
}

function fnCalPrice($price,$Pinfo,$su=0){
	global $todaySale;	//-- 하루세일 정보
	global $Mlevel;			//-- 회원 등급
	global $shopConfig;	//-- 기본 정보(달러계산)
	global $MLV;

	//-- 정가
	$v1 = $price;

	$v2=0;
	if($Pinfo['PIDX'])$PIDX=$Pinfo['PIDX'];
	else if($Pinfo['IDX'])$PIDX=$Pinfo['IDX'];
	
	//-- 할인율 (하루 특가 / 이벤트(기획전/빅세일) 할인가가 있을때 / 없을때)		
	if($PIDX == $todaySale['PIDX']){$v2 = $todaySale['discount'];}
	else if($Pinfo['EVdiscount']){$v2 = $Pinfo['EVdiscount'];}
	//else if($Pinfo['Pdiscount' . $Mlevel]){$v2 = $Pinfo['Pdiscount' . $Mlevel];}
	else if($MLV == 'A'){ $v2 = $Pinfo['Pdiscount10']; }
	else if($MLV == 'B'){ $v2 = $Pinfo['Pdiscount70']; }
	else if($MLV == 'C'){ $v2 = $Pinfo['Pdiscount40']; }
	else if($MLV == 'D'){ $v2 = $Pinfo['Pdiscount100']; }
	else if($Pinfo['Pdiscount'] && $Mlevel<2){$v2 = $Pinfo['Pdiscount'];}

	if($Pinfo['PboxCount'] > 0 || $Pinfo['PboxCount2'] > 0) {	//박스 수량

		if($su == 1 && $Pinfo['PboxCount2'] > 0 && $Pinfo['CAcount'] >= $Pinfo['PboxCount2']){
			if($MLV == 'A'){
				$v2 = $Pinfo['Pdiscount30'];
			} else if($MLV == 'B'){
				$v2 = $Pinfo['Pdiscount90'];
			} else if($MLV == 'C'){
				$v2 = $Pinfo['Pdiscount60'];
			} else if($MLV == 'D'){
				$v2 = $Pinfo['Pdiscount120'];
			}
		} else if($su == 1 && $Pinfo['PboxCount'] > 0 && $Pinfo['CAcount'] >= $Pinfo['PboxCount']){
			if($MLV == 'A'){
				$v2 = $Pinfo['Pdiscount20'];
			} else if($MLV == 'B'){
				$v2 = $Pinfo['Pdiscount80'];
			} else if($MLV == 'C'){
				$v2 = $Pinfo['Pdiscount50'];
			} else if($MLV == 'D'){
				$v2 = $Pinfo['Pdiscount110'];
			}
		} else {
			if($MLV == 'A'){
				$v5 = $Pinfo['Pdiscount20'];
				$v7 = $Pinfo['Pdiscount30'];
			} else if($MLV == 'B'){
				$v5 = $Pinfo['Pdiscount80'];
				$v7 = $Pinfo['Pdiscount90'];
			} else if($MLV == 'C'){
				$v5 = $Pinfo['Pdiscount50'];
				$v7 = $Pinfo['Pdiscount60'];
			} else if($MLV == 'D'){
				$v5 = $Pinfo['Pdiscount110'];
				$v7 = $Pinfo['Pdiscount120'];
			}
			$v6 = ceil($price / 100 * $v5);
			$v6 = $v1 - $v6;
			
			$v8 = ceil($price / 100 * $v7);
			$v8 = $v1 - $v8;
		}

	}

			
	//-- 할인금액
	$v3 = ceil($price / 100 * $v2);
	//$v3 = round($price / 100 * $v2);

	//-- 할인된 판매가
	$v4 = $v1 - $v3;

	if(!$v1)$v1=0;
	if(!$v2)$v2=0;
	if(!$v3)$v3=0;
	if(!$v4)$v4=0;
	if(!$v5)$v5=0;
	if(!$v6)$v6=0;

	$priceInfo['price'] = $v1;
	$priceInfo['dcPer'] = $v2;
	$priceInfo['dcPerBox'] = $v5;
	$priceInfo['dcPerBox2'] = $v7;
	$priceInfo['dc'] = $v3;
	$priceInfo['dcPrice'] = $v4;
	$priceInfo['dcPriceBox'] = $v6;
	$priceInfo['dcPriceBox2'] = $v8;

	$priceInfo['price_txt'] = number_format($v1);
	$priceInfo['dc_txt'] = number_format($v3);
	$priceInfo['dcPrice_txt'] = number_format($v4);
	
	#-- 달러 가격 추가
	$priceInfo['dollar'] = number_format($v1 / $shopConfig['CFperDollar'],2,'.','');
	$priceInfo['dollar_txt'] = $priceInfo['dollar'];
		
	$priceInfo['dcDollar'] = number_format($v4 / $shopConfig['CFperDollar'],2,'.','');
	$priceInfo['dcDollar_txt'] =  $priceInfo['dcDollar'];
	
	$priceInfo['dcDollarBox'] = number_format($v6 / $shopConfig['CFperDollar'],2,'.','');
	$priceInfo['dcDollarBox_txt'] =  $priceInfo['dcDollarBox'];
	
	$priceInfo['dcDollarBox2'] = number_format($v8 / $shopConfig['CFperDollar'],2,'.','');
	$priceInfo['dcDollarBox2_txt'] =  $priceInfo['dcDollarBox2'];

	return $priceInfo;

}

//상품 목록 페이지에서 썸네일 이미지
function getImgUrl($file){
	global $imageServerRoot;
	
	$fileArray = explode("/",$file);
	$chkUrlThumb = "/_DATA/product/".$fileArray[0]."/thumb/".$fileArray[1];
	//$chkUrl = "/_DATA/product/".$file;
	if(is_file($imageServerRoot.$chkUrlThumb)){
		$url = $chkUrlThumb;
	} else {
		$url = "/_DATA/noimage.gif";
	}
	return $url;
}

function getProductReady($idx,$opidx=""){
	if($opidx) $opWhere = " AND ORPoption ='" . $opidx ."' ";
	else $opWhere = " AND ORPoption = 0 ";

	$query = " SELECT ifnull(sum(ORPcount),0) AS Pready FROM 2011_orderProduct WHERE PIDX = '" . $idx . "' ". $opWhere . " AND ORPcountCheck = 1 AND ORPdeleted = 0 GROUP BY PIDX  ";
	$data = sql_fetch($query);
	return $data['Pready'];
}

function getListInfo($rs, $dbPstockCount){
	global $_Minus;
	
	$rData = array();

	$icon = "";
	$icon2 = "";
	$icon3 = "";
	$boxin = "";

	$addCheckMsg = "";
	if($_Minus == "1") $rs['PorderMinus'] = 1;

	//-- 아이콘 처리	                					
	if($rs['Picon1'] == 2) $icon = "icon_new.svg";
	else if($rs['Picon1']==3) $icon = "icon_sale01.svg";
	else if($rs['Picon1']==4) $icon = "icon_hit.gif";
	else if($rs['Picon1']==5) $icon = "icon_gift.svg";
	
	//-- 품절시 아이콘
	if($rs['PorderMinus'] != 1 && $dbPstockCount < 1) $icon = "icon_sold02.svg";

	#-- 일시품절 아이콘
	//if($rs['Pstate'] == 3) $icon = "icon_sold03.gif";

	//-- 단종시 아이콘
	//if($rs['Pstate'] == 4) $icon = "icon_sold01.gif";
	
	//-- 재고 처리(정상이 아닐경우 재고 강제 처리)
	if($rs['Pstate'] > 9) $dbPstockCount = 0;
	
	#-- 등록일 기준 14일 이내 new 아이콘 뜨기
	if($rs['Pregdate']>=date(time())-(60*60*24*14)) $icon2 = "icon_new.svg";
	
	#-- 옵션이 있을경우 옵션 아이콘 표시
	if($rs['PoptionUse'] == 2 || $rs['PoptionUse'] == 3) $icon3 = "btn_option.svg";
	
	if($icon) $icon="<img src='/image_1/" . $icon . "' align='absmiddle'>";
	if($icon2) $icon2="<img src='/image_1/" . $icon2 . "' align='absmiddle'>";
	if($icon3) $icon3="<img src='/image_1/" . $icon3 . "' align='absmiddle'>";

	#-- 박스 수량이 있을경우  처리
	if($rs['Pboxin2']) $boxin2 = "<img src='/image_3/boxinBG.svg' align=absmiddle>" . $rs['Pboxin2'] . "EA";
	
	if(!$dbPstockCount && $rs['PorderMinus'] != 1) $nCount=0;
	else $nCount=1;

	if($rs['PcartWith']==2) $addCheckMsg=" onclick='alert(\"The product can not be purchased with other product together.\");this.checked=false' ";

	$rData['icon'] = $icon;
	$rData['icon2'] = $icon2;
	$rData['icon3'] = $icon3;
	$rData['boxin2'] = $boxin2;
	$rData['nCount'] = $nCount;
	$rData['addCheckMsg'] = $addCheckMsg;
	$rData['dbPstockCount'] = $dbPstockCount;

	return $rData;

}


//상단 메뉴 2차 카테고리 불러오기
function getSubCategory($idx){
	$query = " SELECT IDX, CTename FROM 2011_categoryInfo WHERE CTlv3 > 0 AND CTcode LIKE '" . $idx ."%' ORDER BY CTcode ASC ";
	$result = sql_query($query);
	$tmpBody = "";
    while ($data = sql_fetch_array($result)){
		$tmpBody .= "<li><a href=\"/product/list.html?cateIDX=".$data['IDX']."\">".$data['CTename']."</a></li>";
	}
	return $tmpBody;
}

//카테고리 영문
function getCategoryNew($idx){
	$query = "SELECT CTcode FROM 2011_categoryInfo WHERE IDX = '$idx'";
	$data = sql_fetch($query);
	return $data['CTcode'];
}

// 세션변수 생성
function set_session($session_name, $value)
{
    $$session_name = $_SESSION[$session_name] = $value;
}

// 세션변수값 얻음
function get_session($session_name)
{
    return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}

// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
    setcookie(md5($cookie_name), base64_encode($value), time() + $expire, '/', ".cheonyu.com");
}

// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
    $cookie = md5($cookie_name);
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}

function getOptionArr($option, $value){
	$opArr = explode("&",$option);
	$valueArr = explode("=",$value);
	$str = "&";
	for($i = 0; $i < count($opArr); $i ++){
		$opValue = explode("=",$opArr[$i]);
		if($opValue[0] == "") continue;
		if($opValue[0] == $valueArr[0]) continue;
		$str = $str.$opValue[0]."=".$opValue[1]."&";
	}
	return $str.$value;
}

function page_nav($total,$scale,$p_num,$page,$query) {
	global $_SERVER;
		
	$total_page = ceil($total/$scale);
	if (!$page) $page = 1;
	$page_list = ceil($page/$p_num)-1;

	$navigation = "<ul>";
                
	// 페이지 리스트의 첫번째가 아닌 경우엔 [1]...[prev] 버튼을 생성한다. 
	if ($page_list>0) 
	{ 
		//$navigation .= "<li><a href='$_SERVER[PHP_SELF]?page=1$query' class='nextpre'>처음</a></li>"; 
		$prev_page = ($page_list)*$p_num; 
		$navigation .= "<li><a href='$_SERVER[PHP_SELF]?page=$prev_page$query' class='nextpre'><</a></li>"; 
	} 
	else
	{
		//$navigation .= "<li><a href='$_SERVER[PHP_SELF]?page=1$query' class='nextpre'><<</a></li>"; 
		$prev_page = ($page_list)*$p_num; 
		//$navigation .= "<li>이전</li>"; 
	}

    // 페이지 목록 가운데 부분 출력
	$page_end=($page_list+1)*$p_num;
	if ($page_end>$total_page) $page_end=$total_page;

	for ($setpage=$page_list*$p_num+1;$setpage<=$page_end;$setpage++)
	{
		if ($setpage==$page) {
			$navigation .= "<li><a href='#' class='now'>$setpage</a></li>";
		} else {
			$navigation .= "<li><a href='$_SERVER[PHP_SELF]?page=$setpage$query'>$setpage</a></li>";
		}
	}

    // 페이지 목록 맨 끝이 $total_page 보다 작을 경우에만, [next]...[$total_page] 버튼을 생성한다.
    if ($page_end<$total_page) 
	{
		$next_page = ($page_list+1)*$p_num+1;
		$navigation .= "<li><a href='$_SERVER[PHP_SELF]?page=$next_page$query' class='nextpre'>></a></li>"; 
		//$navigation .= "<li><a href='$_SERVER[PHP_SELF]?page=$total_page$query' class='nextpre'>>></a></li>";
	}
	else
	{
		$next_page = ($page_list+1)*$p_num+1;
		//$navigation .= "<li>다음</li>"; 
		//$navigation .= "<li><a href='$_SERVER[PHP_SELF]?page=$total_page$query' class='nextpre'>끝</a></li>";
	}
       
    return $navigation;
}


function page_navAjax($total,$scale,$p_num,$page,$query,$ajaxLink,$getMID="") {	
	$total_page = ceil($total/$scale);
	if (!$page) $page = 1;
	$page_list = ceil($page/$p_num)-1;

	$navigation = "<ul>";
	if ($page_list>0) 
	{ 
		//if($getMID) $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$getMID."' , '1')\" class='nextpre'>처음</a></li>"; 
		//else $navigation .= "<li><a href=\"javascript:".$ajaxLink."('1')\" class='nextpre'>처음</a></li>"; 
		$prev_page = ($page_list)*$p_num;
		if($getMID) $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$getMID."' , '".$prev_page."')\" class='nextpre'><</a></li>"; 
		else $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$prev_page."')\" class='nextpre'><</a></li>"; 
	} 
	else
	{
		//if($getMID) $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$getMID."' , '1')\" class='nextpre'>처음</a></li>"; 
		//else $navigation .= "<li><a href=\"javascript:".$ajaxLink."('1')\" class='nextpre'>처음</a></li>"; 
		$prev_page = ($page_list)*$p_num; 
	}
	$page_end=($page_list+1)*$p_num;
	if ($page_end>$total_page) $page_end=$total_page;

	for ($setpage=$page_list*$p_num+1;$setpage<=$page_end;$setpage++)
	{
		if ($setpage==$page) {
			$navigation .= "<li><a href='#' class='now'>$setpage</a></li>";
		} else {
			if($getMID) $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$getMID."' , '".$setpage."')\">$setpage</a></li>";
			else $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$setpage."')\">$setpage</a></li>";
		}
	}
    if ($page_end<$total_page) 
	{
		$next_page = ($page_list+1)*$p_num+1;
		if($getMID) $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$getMID."' , '".$next_page."')\" class='nextpre'>></a></li>"; 
		else $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$next_page."')\" class='nextpre'>></a></li>"; 
		//if($getMID) $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$getMID."' , '".$total_page."')\" class='nextpre'>끝</a></li>";
		//else $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$total_page."')\" class='nextpre'>끝</a></li>";
	}
	else
	{
		$next_page = ($page_list+1)*$p_num+1;
		//if($getMID) $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$getMID."' , '".$total_page."')\" class='nextpre'>끝</a></li>";
		//else $navigation .= "<li><a href=\"javascript:".$ajaxLink."('".$total_page."')\" class='nextpre'>끝</a></li>";
	}
       
    return $navigation;
}

#=============================================================================================================================
# JAVA SCRIPT Alert 함수 + 페이지 이동 (경고메세지창 띄운 후 페이지 이동/History 남지 않음)
# 메세지가 있다면 메세지 출력
# 이동 페이지 지정이 있다면 페이지 이동
#=============================================================================================================================
function FN_Location($message,$location)
{
	global $isPop;

	echo "<script>";
	$resultString = "";
	if($message)$resultString.=  "alert('$message');";

	if($isPop)
	{
		echo "window.opener.location.reload();self.close();</script>";
		exit();
	}

	if($location)
	{
		if($location=="back")$resultString.= "history.back();";
		else $resultString.= "location.replace('$location');";
	}
	$resultString.= "</script>";

	echo $resultString;
	exit();
}

// XSS 관련 태그 제거
function clean_xss_tags($str)
{
    $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

    return $str;
}

// 로그인 후 이동할 URL
function login_url($url='')
{
    if (!$url) $url = "/";
    return urlencode(clean_xss_tags(urldecode($url)));
}

/** 과세유형 함수 */
function fnPtaxType($val) {
	switch ($val) {
		case 1 : $returnVal = "Taxable"; break;
		case 2 : $returnVal = "Tax-free"; break;
	}
	return $returnVal;
}

/** 배송방법 함수 */
function fnPdeliveryType($val) {
	switch ($val) {
		case 1 : $returnVal = "Cheonyu.com's direct shipping"; break;
		case 2 : $returnVal = "Manufacturer's direct shipping"; break;
	}
	return $returnVal;
}

/** 배송비 함수 */
function fnPdeliveryPriceType($val) {
	switch ($val) {
		case 1 : $returnVal = "Optional free-shipping"; break;
		case 2 : $returnVal = "Additional freight is needed"; break;
	}
	return $returnVal;
}

/** 원산지 함수 */
function fnPmade($val1,$val2) {
	switch ($val1) {
		case 1 : $returnVal = "KOREA"; break;
		case 2 : $returnVal = "CHINA"; break;
		case 3 : $returnVal = $val2; break;
		case 4 : $returnVal = "INDONESIA"; break;
		case 5 : $returnVal = "JAPAN"; break;
		case 6 : $returnVal = "VIETNAM"; break;
	}
	$returnVal = str_replace("일본","JAPAN",$returnVal);
	$returnVal = str_replace("중국","CHINA",$returnVal);
	return $returnVal;
}

// 제조사명 가져오기
function getMKkname($idx){
	global $g5;
	$query = " SELECT MKaidx FROM 2011_makerInfo WHERE IDX = '$idx' ";
	$data = sql_fetch($query);
	$query = " SELECT MKename FROM 2011_makerInfo WHERE IDX = '" . $data['MKaidx'] . "' ";
	$data = sql_fetch($query);
	return $data['MKename'];
}

#=============================================================================================================================
# 랜덤코드 만들어서 리턴해 준다.
# 인자값 : 랜덤코드 길이
#=============================================================================================================================

function randCode($strLen)
{
	//$char = array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,1,2,3,4,5,6,7,8,9,0);
	//-- 영문자 O 와 숫자 0 이 혼동되므로 삭제
	$char = array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,P,Q,R,S,T,U,V,W,X,Y,Z,1,2,3,4,5,6,7,8,9);

	for($i=0;$i<$strLen;$i++)
	{
		$rand = rand(0,sizeof($char)-1);
		$Code .= $char[$rand];
	}

return $Code;
}

function randCodeNum($strLen)
{
	//$char = array(1,2,3,4,5,6,7,8,9,0);
	//-- 영문자 O 와 숫자 0 이 혼동되므로 0 삭제
	$char = array(1,2,3,4,5,6,7,8,9);

	for($i=0;$i<$strLen;$i++)
	{
		$rand = rand(0,sizeof($char)-1);
		$Code .= $char[$rand];
	}

return $Code;
}

function randCodeString($strLen)
{
	//$char = array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z);
	//-- 영문자 O 와 숫자 0 이 혼동되므로 O 삭제
	$char = array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,P,Q,R,S,T,U,V,W,X,Y,Z);

	for($i=0;$i<$strLen;$i++)
	{
		$rand = rand(0,sizeof($char)-1);
		$Code .= $char[$rand];
	}

return $Code;
}


// function cut_string_utf8($str, $max_len, $suffix)
// 유니코드용 문자열 자르기 함수.
//
function cut_string_utf8($str, $max_len, $suffix)
{
	$n = 0;
	$noc = 0;
	$len = strlen($str);
	
	while ( $n < $len )
	{
		$t = ord($str[$n]);
		if ( $t == 9 || $t == 10 || (32 <= $t && $t <= 126) )
		{
			$tn = 1;
			$n++;
			$noc++;
		}
		else if ( 194 <= $t && $t <= 223 )
		{
			$tn = 2;
			$n += 2;
			$noc += 2;                     
		}
		else if ( 224 <= $t && $t < 239 )
		{
			$tn = 3;
			$n += 3;
			$noc += 2;
		}
		else if ( 240 <= $t && $t <= 247 )
		{
			$tn = 4;
			$n += 4;
			$noc += 2;
		}
		else if ( 248 <= $t && $t <= 251 )
		{
			$tn = 5;
			$n += 5;
			$noc += 2;
		}
		else if ( $t == 252 || $t == 253 )
		{
			$tn = 6;
			$n += 6;
			$noc += 2;
		}
		else { $n++; }
		
		if ( $noc >= $max_len ) { break; }
	}
	//if ( $noc <= $max_len ){echo $noc;return $str;}
	//if ( $noc > $max_len ) { $n -= $tn; }
	
	
	$strArray[0] = substr($str, 0, $n);
	$strArray[1] = substr($str, $n);
	
	return $strArray;
}
	
/*===============================================================
■ SMS 발송 함수
===============================================================*/
function SendSocketPost($host,$path,$query,$port=80) {
	$fp = @fsockopen($host, $port, $errno, $errstr, 5);
	if(!$fp) {
		@fclose($fp);
		return "ERROR : $errstr ($errno)";
	} else {
		fputs($fp, "POST $path HTTP/1.0\n");
		fputs($fp, "Host: $host\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
		fputs($fp, "Content-length: ". strlen($query) ."\n");
		fputs($fp, "Connection: close\n\n");
		fputs($fp, $query);
		while($currentHeader = fgets($fp, 1024)) {
			if($currentHeader == "\r\n") {
				break;
			}
		}
		$strLine = "";
		while(!feof($fp)) {
			$strLine .= fgets($fp, 1024);
		}
		fclose($fp);
		return trim($strLine);
	}
}

//-- 발신하는 사람 번호
$defaultsphone="1544-7102";	
function smsSendNew($rphone,$msg,$sphone="",$smsGroup="",$smsMode="TEST",$smsOcode=""){
	global $defaultsphone, $MID, $g5;
	
	#-- 발송 금지처리
	if($rphone=="010-7592-1114")return;
	$sphone = str_replace("02-1544-7102","1544-7102",$sphone);
	if(!$sphone)$sphone = $defaultsphone;
	$cnt=1;
	while($cnt){
		$cutStr = cut_string_utf8($msg,78,"");
		$strConvert[$cnt-1] = $cutStr[0];	
		if(strlen($cutStr[1]))$msg = $cutStr[1];
		else break;
		if($cnt>=10)break;
		$cnt++;
	}
	$smsLoop = sizeof($strConvert);
	$successCount = 0;
	$failCount = 0;

	#==========================================================				
	# SMS 발송 시작
	#==========================================================
	for($k=0;$k<$smsLoop;$k++){
		if(!trim($strConvert[$k]))break;
		
		$sphone = str_replace("-","",$sphone);
		$rphone = str_replace("-","",$rphone);
		
		$phone		= $rphone;
		//$msg		= $smsMessage;
		$senddate	= "";									// 예약발송 Y-m-d H:i:s
		$authkey	= "805dd638ee728eec08df125b0fc13de23f50bf";		// 인증키
		
		$host = "sms1.pulun.co.kr";
		$path = "/sendsms.php";
		$query.= "callback=".$sphone;
		$query.= "&phone=".$phone;
		$query.= "&msg=".urlencode($msg);
		$query.= "&senddate=".$senddate;
		$query.= "&authkey=".$authkey;

		$result = SendSocketPost($host,$path,$query);

		//-- 전체나 개인회원 전체로 문자가 나갈경우 번호가 불필요하게 길어지는 관계로 몇명 조합하여 발송되었는지만 저장.
		if($smsGroup=="All")$smsNumberResult = "전회원 (" . $nCnt . " 명)";
		else if($smsGroup=="Pserson")$smsNumberResult = "개인회원 (" . $nCnt . " 명)";

		//-- SMS결과 기록
		$sql = "insert into 2011_smsLog(SMStype,SMSgroup,SMSsphone,SMSrphone,SMSmsg,SMSmode,SMSocode,SMSresult,MID,SMSIP,SMSregdate) values(";
		$sql.= "'1',";
		$sql.= "'" . $smsGroup . "',";	//-- SMS Group
		$sql.= "'" . $sphone . "',";
		$sql.= "'" . $rphone . "',";
		$sql.= "'" . $strConvert[$k] . "',";
		$sql.= "'" . $smsMode . "',";
		$sql.= "'" . $smsOcode . "',";
		$sql.= "'" . $result . "',";
		$sql.= "'" . $MID . "',";
		$sql.= "'" . $_SERVER['REMOTE_ADDR'] . "',";
		$sql.= "'" . date(time()) . "')";

		sql_query($sql);

		if ($result == 1){
			$successCount++;
		} else {
			$failCount++;
		}    		
	}//---------- SMS Loop End
	
	#-- 성공갯수 실패 갯수 리턴
	return "##success##" . $successCount . "##" . $failCount;
	//return $result;        
}	//-- SMS function End

#=============================================================================================================================
#Email 입력창 형식 출력 함수
#=============================================================================================================================
function FN_Set_Email($email,$class,$objName)
{

	if($email <> "@")$emailArray = explode("@",$email);

	if($emailArray[1]=="naver.com")$selected[1]="selected";
	if($emailArray[1]=="hanmail.net")$selected[2]="selected";
	if($emailArray[1]=="gmail.com")$selected[3]="selected";
	if($emailArray[1]=="nate.com")$selected[4]="selected";
	
	if($emailArray[1]=="hotmail.com")$selected[5]="selected";
	if($emailArray[1]=="yahoo.co.kr")$selected[6]="selected";
	if($emailArray[1]=="lycos.co.kr")$selected[7]="selected";
	if($emailArray[1]=="korea.com")$selected[8]="selected";

	if(!$objName)$objName="inEmail";


	for($i=1;$i<=10;$i++)
	{
		if($selected[$i])
		{
			$disable = " readonly  style='background-Color=#efefef;'";
		}
	}

	$str ="<input name='" . $objName . "1' id='" . $objName . "1' value='" . $emailArray[0] . "'  class='" . $class . "' style='ime-mode:inactive' type='text' id='inEmail1' size='15' maxlength='50'>";
	$str.=" @ ";
	$str.="<input name='" . $objName . "2' id='" . $objName . "2' value='" . $emailArray[1] . "'  class='" . $class . "' style='ime-mode:inactive' type='text' id='inEmail2' size='15' maxlength='50' $disable >&nbsp;";
	$str.="<select name='" . $objName . "2_SELECT' id='" . $objName . "2_SELECT' onchange='checkEmail(this)' style='font:11px;width:120px;'>";
	$str.="	<option>: Direct input :</option>";
	$str.="	<option value='naver.com' $selected[1] >naver.com</option>";
	$str.="	<option value='hanmail.net' $selected[2] >hanmail.net</option>";
	$str.="	<option value='gmail.com' $selected[3] >gmail.com</option>";
	$str.="	<option value='nate.com' $selected[4] >nate.com</option>";
	$str.="	<option value='hotmail.com' $selected[5] >hotmail.com</option>";
	$str.="	<option value='yahoo.co.kr' $selected[6] >yahoo.co.kr</option>";
	$str.="	<option value='lycos.co.kr' $selected[7] >lycos.co.kr</option>";
	$str.="	<option value='korea.com' $selected[8] >korea.com</option>";
	$str.="</select>";
	return $str;
}

function addPostcode($zip1,$zip2){
	global $g5;
	$query = "SELECT MONEY FROM 2011_DELIVERY WHERE ZIP1 = '$zip1' AND ZIP2 = '$zip2'";
	$data = sql_fetch($query);
	if($data['MONEY']){
		return $data['MONEY'];
	} else {
		return "0";
	}
}

function setCardID(){
	global $MID, $uid, $tmp_cart_id, $g5, $_SESSION;
	if(!$tmp_cart_id) $tmp_cart_id = $_SESSION['ss_cart_id'];
	
	$sql = sql_query("DELETE FROM 2011_cartInfo WHERE CAstate='0' AND MID='" . $uid . "'");
	$sql = sql_query("DELETE FROM 2011_cartInfo WHERE CAstate='6' AND CAorder='1' AND MID='" . $uid . "'");

	if($MID && $tmp_cart_id){
		$wtime = time() - (86400 * 2);
		$sql = " UPDATE 2011_cartInfo SET MID = '$MID' WHERE MID = '$tmp_cart_id' AND CAstate = '1' AND (CAcheck = '0' or CAcheck = '1') AND CAorder = '0' AND CAregdate >= '$wtime' ";
		sql_query($sql);
	}
}

/*===============================================================
■ 주문번호(IDX)를 받아서 해당하는 주문의 입금상태 체크 및 업데이트
===============================================================*/
function fnUpdatePayState($Oidx){
	global $g5, $MID;
	$sql = "select min(OdepositCheck) as MinOCheck from 2011_orderPayment where OIDX='" . $Oidx . "'";
	$rs = sql_fetch($sql);
	$nState = $rs['MinOCheck'];
	$sql = "update 2011_orderInfo set OpayState='" . $nState . "' where IDX='" . $Oidx . "'";
	sql_query($sql);

	return $nState;
}

/*===============================================================
■ 회원ID에 따라 포인트 적립여부 확인 (단, 도매회원은 포인트 없음
===============================================================*/
function fnGetMemberTotalPoint($id,$shopCode=""){
	global $g5, $MID;
	$tPoint = 0;
	if(!$id) $id=$MID;
	$sql = "select sum(POpoint * POcount) AS sumPoint from ";
	if($shopCode)$sql.="shop_" . $shopCode . ".";
	$sql.="2011_memberPoint where MID='" . $id . "' and  POdeleted=0 and POstate=2 group by MID";
	if($rs = sql_fetch($sql))$tPoint = $rs['sumPoint'];
	return $tPoint;
}

/*===============================================================
■ 회원ID에 따라 상품권포인트 확인
===============================================================*/
function fnGetMemberTotalGift($id,$shopCode=""){
	global $g5, $MID;
	$tPoint = 0;
	if(!$id) $id=$MID;
	$sql = "select sum(GFpoint * GFcount) AS sumPoint  from ";
	if($shopCode)$sql.="shop_" . $shopCode . ".";
	$sql.="2011_memberGift where MID='" . $id . "' and  GFdeleted=0 and GFstate=2 group by MID";
	if($rs = sql_fetch($sql))$tPoint = $rs['sumPoint'];
	return $tPoint;
}

/*===============================================================
■ 회원ID에 따라 상품권총누적포인트 확인
===============================================================*/
function fnGetMemberAddGift($id,$shopCode=""){
	global $g5, $MID;
	$tPoint = 0;
	if(!$id)$id=$MID;
	$sql = "select sum(GFpoint * GFcount) AS sumPoint  from ";
	if($shopCode)$sql.="shop_" . $shopCode . ".";
	$sql.="2011_memberGift where MID='" . $id . "' and  GFdeleted=0 and GFstate=2 and GFtype=1 group by MID";
	if($rs = sql_fetch($sql))$tPoint = $rs['sumPoint'];
	return $tPoint;
}

/*===============================================================
■ 회원ID에 따라 상품권총사용포인트 확인
===============================================================*/
function fnGetMemberUseGift($id,$shopCode=""){
	global $g5, $MID;
	$tPoint = 0;
	if(!$id)$id=$MID;
	$sql = "select sum(GFpoint * GFcount) AS sumPoint  from ";
	if($shopCode)$sql.="shop_" . $shopCode . ".";
	$sql.="2011_memberGift where MID='" . $id . "' and  GFdeleted=0 and GFstate=2 and GFtype=2 group by MID";
	if($rs = sql_fetch($sql))$tPoint = $rs['sumPoint'];
	return $tPoint;
}


/*===============================================================
■ 이미지 사이즈 조절 <펌 - 일부수정 >
※ 호출 방법
img_resize_gd(실제파일,디렉토리 위치 포함 저장파일,최대폭,최대높이);
===============================================================*/
function img_resize_gd($file,$save_file,$max_width,$max_height){
	$img_info = getImageSize($file);		// img_info[0] : 이미지 width  1 : height   2 : type
	if($img_info[2] == 1){
		$src_img = ImageCreateFromGif($file);
	}elseif($img_info[2] == 2){
		$src_img = ImageCreateFromJPEG($file);
	}elseif($img_info[2] == 3){
		$src_img = ImageCreateFromPNG($file);
	}else{
		return 0;
	}
	
	//--------- 사이즈 다시 설정 ---------------
	if($img_info[0]<=$max_width){
		$re_width = $img_info[0];
		$re_height = $img_info[1];
	}else{
		$re_width = $max_width;
		$tmp = $img_info[0] - $max_width;
		$tmp2 = ceil($tmp / $img_info[0] * 100);
		$re_height = $img_info[1] - ($img_info[1] /100 * $tmp2);
	}
	
	if($re_height>$max_height){
		$tmp = $re_height - $max_height;
		$tmp2 = ceil($tmp / $re_height * 100);
		$re_width = $re_width  - ($re_width /100 * $tmp2);
		$re_height = $max_height;
	}
	//-----------------------------------------

	if($img_info[2] == 1){
		$dst_img = imagecreate($re_width, $re_height);
	}else{
		$dst_img = imagecreatetruecolor($re_width, $re_height);
	}
	$bgc = ImageColorAllocate($dst_img, 255, 255, 255);

	$srcx=0;
	$srcy=0;

	ImageCopyResampled($dst_img, $src_img, $srcx, $srcy, 0, 0, $re_width, $re_height, ImageSX($src_img),ImageSY($src_img));
	
	if($img_info[2] == 1){
		ImageInterlace($dst_img);
		ImageGif($dst_img, $save_file);
	}elseif($img_info[2] == 2){
		ImageInterlace($dst_img);
		ImageJPEG($dst_img, $save_file,100);
	}elseif($img_info[2] == 3){
		ImagePNG($dst_img, $save_file);
	}
	ImageDestroy($dst_img);
	ImageDestroy($src_img);
	////////// 이미지 크기 변환 끝 //////////////////
}

/*===============================================================
■ 이미지 사이즈 조절 <펌>
※ 호출 방법
img_resize(실제파일,저장파일,최대폭);
===============================================================*/
function img_resize($jpg1,$jpg2,$width) {
	//,$width,$height,$quality=80
	$quality=100;
	if (!$jpg1||!$jpg2) return;
	$ori_size=$size=@getimagesize($jpg1);       //0:width 1:height
	if (!$size) return;

	if($size[0]>$width){
		if ($size[0]>=$size[1]) {
			$size[1]=intval($size[1]*$width/$size[0]);
			$size[0]=$width;
		} else if ($size[0]<$size[1]) {
			$size[0]=intval($size[0]*$width/$size[1]);
			$size[1]=$width;
		}
	}
	
	if (!$jpg1||!$jpg2) return;
	
	if ($width>200) {
		$draw="-draw 'image  Over 15,15,0,0 ../img/jpg_logo.jpg' ";
		$x=$size[0]-150;
		$y=$size[1]-30;
		$draw.="-draw 'image  Over $x,$y,0,0 ../img/jpg_logo.jpg' ";
	}
	
	$str="convert -scene 0 -compress JPEG -quality 80 -resize $size[0]"."x$size[1] $draw \"$jpg1\" \"$jpg2\"";
	
	echo $str;
	exec($str);
}

/*===============================================================
■ 관리자모드 에디트 페이지 체크 함수
※ 관리자모드에만 쓰이긴 하지만 다른 곳에서 쓰일수도 있어서 공용함수에 넣음.
===============================================================*/
function fnGetEditPage($url=""){	
	if(!$url)$url = $_SERVER['PHP_SELF'];
	$pageTemp = explode("/",$url);
	$pageTemp2 = explode(".",$pageTemp[sizeof($pageTemp)-1]);

	$fname["list"] = $pageTemp2[0] . "." . $pageTemp2[1];
	$fname["list"] = str_replace("Edit","",str_replace("Product","",$fname["list"]));

	$fname["edit"] = $pageTemp2[0] . "Edit." . $pageTemp2[1];

	return $fname;
}

#=============================================================================================================================
# 저장 및 삭제시 돌아갈 페이지를 판단해서 돌려준다. (저장이나 삭제시 무조건 리스트페이지로 간다)
#=============================================================================================================================
function getRefreshPage(){
	global $path;
	global $newpath;
	
	$p = $path;
	if($newpath)$p=$newpath;
	
	$bPage = str_replace("Edit","",$_SERVER["PHP_SELF"]);
	return $bPage . "?path=" . $p;
}

function fnGetAdminName($fnMID){
	$query = " SELECT Mname FROM 2011_memberInfo WHERE MID = '$fnMID' ";
	$row = sql_fetch($query);
	return $row['Mname'];
}

function fnGetMemberInfo($fnMID){
	$query = " SELECT * FROM 2011_memberInfo WHERE MID = '$fnMID' AND Mdeleted = 0 ";
	$row = sql_fetch($query);
	return $row;
}

function listInfo() {
	global $TotalCount, $page, $CntPerPage;
	$totalPage = ceil($TotalCount / $CntPerPage);
	$pinfo = $TotalCount . " 개의 결과 / 전체 " . $totalPage . " page 에서 현재 " . $page . " page";
	return $pinfo;
}

function changeSize() {
	global $listSize;
	if($listSize==10) $sz1 = "selected";
	if($listSize==20) $sz2 = "selected";
	if($listSize==50) $sz3 = "selected";
	if($listSize==70) $sz4 = "selected";
	if($listSize==100) $sz5 = "selected";
	if($listSize==200) $sz6 = "selected";
	if($listSize==500) $sz7 = "selected";

	$sinfo = "<select name='listSize' onchange='location.href=document.location.pathname + \"?path=\" + path + \"&page=1&listSize=\" + this.value'>\n";
	$sinfo.="<option value=10 " . $sz1 . ">10개씩 보기</option>\n";
	$sinfo.="<option value=20 " . $sz2 . ">20개씩 보기</option>\n";
	$sinfo.="<option value=50 " . $sz3 . ">50개씩 보기</option>\n";
	$sinfo.="<option value=70 " . $sz4 . ">70개씩 보기</option>\n";
	$sinfo.="<option value=100 " . $sz5 . ">100개씩 보기</option>\n";
	$sinfo.="<option value=200 " . $sz6 . ">200개씩 보기</option>\n";
	$sinfo.="<option value=500 " . $sz7 . ">500개씩 보기</option>\n";
	$sinfo.="</select>\n";
	return $sinfo;
}

/*===========================================================================
하위폴더 모두 삭제 (제로보드 발췌한 자료 블로그에서 퍼옴)
============================================================================*/
function zRmDir($path) { 
	$directory = dir($path); 
	while($entry = $directory->read()) { 
		if ($entry != "." && $entry != "..") { 
			if (Is_Dir($path."/".$entry)) { 
				zRmDir($path."/".$entry); 
			} else { 
				@UnLink ($path."/".$entry); 
			} 
		} 
	} 
	$directory->close(); 
	@RmDir($path); 
}


function getMemberLV($lv){
	$resultHtml = "";

	if($lv == "A") $ret = "<font style='color:#FF0000;'><b>VIP</b></font>";
	else if($lv == "B") $ret = "<font style='color:#940CF3;'><b>VVIP</b></font>";
	else if($lv == "C") $ret = "<font style='color:#F3730C;'><b>VVVIP</b></font>";
	else if($lv == "D") $ret = "<font style='color:#000000;'><b>MVP</b></font>";
	
	$resultHtml = $ret;
	return $resultHtml;
}

/*===============================================================
■ 파일 업로더 호출 함수
※ 각종 js 파일 위치 확인하도록 합시다.
===============================================================*/

$includeUploaded=0;
function fnCallUploder($num,$fInfo,$max_width=100)
{
	//-- 기존 첨부된 파일 체크
	if($fInfo["realFile"])
	{
		$fTemp = explode(".",$fInfo["realFile"]);
		$fileTypes = strtolower($fTemp[sizeof($fTemp)-1]);

		$fPath = __HOME_SERVER_PATH__ . "/_DATA/" . $fInfo["fileCode"] . "/" . $fInfo["saveFile"];
		$fURL = __HOME_PATH__ . "/_DATA/" . $fInfo["fileCode"] . "/" . $fInfo["saveFile"];

		if($fileTypes=="gif" || $fileTypes=="jpg" || $fileTypes=="bmp" || $fileTypes=="jpeg")
		{
			if(file_exists($fPath))
			{
				//-- Image Size Check
				if(!$max_width)$max_width=80;

				$img_info = getImageSize($fPath);

				//--------- 사이즈 다시 설정 ---------------
				if($img_info[0]<=$max_width){
					$re_width = $img_info[0];
					$re_height = $img_info[1];
				}else{
					$re_width = $max_width;
					$tmp = $img_info[0] - $max_width;
					$tmp2 = ceil($tmp / $img_info[0] * 100);
					$re_height = $img_info[1] - ($img_info[1] /100 * $tmp2);
				}
				$fileHTML="<img src='" . $fURL . "' alt='클릭하시면 원본을 확인하실 수 있습니다.' width='" . $re_width . "' height='" . $re_height . "'>";
			}
			else
			{
				$fileHTML="<br>-Can't find file-";
			}
		}
		else
		{
			$fileHTML="-미리보기 없음-";
		}
		$fileName = "[ " . $fInfo["realFile"] . " ]";
	}

	
	if($includeUploaded==0)
	{
		$str ="<link href='" . __HOME_PATH__ . "/_Include/fileUploader/uploadify.css' type='text/css' rel='stylesheet' />\n";
		$str.="<script type='text/javascript' src='" . __HOME_PATH__ . "/_Include/fileUploader/swfobject.js'></script>\n";
		$str.="<script type='text/javascript' src='" . __HOME_PATH__ . "/_Include/fileUploader/jquery.uploadify.v2.1.4.min.js'></script>\n";

		$str.="<script>\n";
		
		$str.="function fnUploadPreView(fNum,fName,fFile,fWidth)\n";
		$str.="{\n";

		$str.="if(!fWidth)fWidth=100;\n";
		$str.="ftmp = fName.split('.');\n";
		$str.="fEXE = ftmp[ftmp.length-1];\n";
		$str.="	jQuery('#uploadPreviewDiv' + fNum).css('display','block');\n";
		$str.="if(fEXE=='jpg' || fEXE=='jpeg' || fEXE=='gif' || fEXE=='bmp'){\n";
		$str.="	jQuery('#uploadPreviewDiv' + fNum).html('<img width=' + fWidth + '  src=\"" . __HOME_PATH__ . "/_DATA/uploaderTemp/'+fFile+'\" alt=\"' + fName + '\">');\n";
		$str.="}else{\n";
		$str.="	jQuery('#uploadPreviewDiv' + fNum).html('<br>-미리보기 없음-[ ' + fName + ']');\n";
		$str.="}\n";
		$str.="}\n";
		

		
		$str.="</script>\n\n";
		

		$includeUploaded=1;
	}
		
	$str.="<table cellspacing=0 cellpadding=0 style='width:250px;height:80px;' border=0 >";
	$str.="<tr><td width=200 align=center valign=top style='padding-top:20px;'>";
	$str.="<script type='text/javascript'>\n";
	$str.="jQuery(document).ready(function() {\n";
	$str.="  jQuery('#file_upload" . $num . "').uploadify({\n";
	$str.="    'uploader'  : '" . __HOME_PATH__ . "/_Include/fileUploader/uploadify.swf',\n";
	$str.="    'script'    : '" . __HOME_PATH__ . "/_Include/fileUploader/uploadify.php',\n";
	$str.="    'cancelImg' : '" . __HOME_PATH__ . "/_Include/fileUploader/cancel.png',\n";
	$str.="    'folder'    : '" . __HOME_PATH__ . "/_DATA/uploaderTemp',\n";
	$str.="    'multi'     : false,\n";
	$str.="    'auto'      : true,\n";
	$str.="	 'fileDataName' : 'Filedata" . $num . "',";
	$str.="    'removeCompleted': false,\n";

	//$str.="    'fileExt'   : '*.jpg;*.gif;*.png',\n";
	//$str.="    'fileDesc'    : 'Image Files',\n";
	//$str.="    'sizeLimit' : 1024000,\n";

	$str.="  'onSelectOnce'   : function(event,data) {\n";
	$str.="      jQuery('#status-message').text(data.filesSelected + ' files have been added to the queue.');\n";
	$str.="    },\n";
	$str.="  'onAllComplete'  : function(event,data) {\n";
	$str.="      jQuery('#status-message').text(data.filesUploaded + ' files uploaded, ' + data.errors + ' errors.');\n";

	//$str.="      alert('업로드 완료!');\n";
	$str.="    },\n";
	$str.="  'onError'				: function(event,ID,fileObj,errorObj) {\n";
	$str.="  		alert('오류가 발생하였습니다. 관리자에게 문의하시기 바랍니다.');\n";
	$str.=" 	 },\n";

	$str.="  'onComplete'				: function(event, ID, fileObj, response, data) {\n";
	//$str.="			alert(fileObj.name);\n";//-- 파일 정보 콜
	$str.="  		jQuery('#file_upload" . $num . "').uploadifyCancel(ID);\n";	//-- 완료된 파일 정보창 닫기
	//$str.="  		alert(response);\n";	//-- 업로드 완료된 파일명 출력(uploadify.php 파일과 연동)
	$str.="			jQuery('#inFileName" . $num . "').val(fileObj.name);\n";
	$str.="			jQuery('#inFileSaveName" . $num . "').val(response);\n";
	$str.="			fnUploadPreView(" . $num . ",fileObj.name,response," . $max_width . ");\n";
	
	$str.=" 	 }\n";
	$str.="  });\n";
	$str.="});\n";
	


	#---- 등록한 이미지 삭제 기능
	$str.="function fnDelThumnail(sfile,fNum)\n";
	$str.="{\n";
	
	$str.="if(!confirm('등록된 이미지를 삭제하시겠습니까?'))return;";
	
	$str.="param='';\n";
	$str.="tempFile = jQuery('#inFileSaveName' + fNum).val()\n";
	//$str.="alert(HOME_PATH);\n";
	$str.="param=param+'saveFile=' + sfile;\n";
	$str.="if(tempFile)param=param+'&tempFile=' + tempFile;\n";
	//$str.="alert(param);\n";
	
	#-- 일단 화면 및 업로드한 파일명 삭제
	$str.="			str=\"<div style='border:1px solid #efefef;text-align:center;display:none;' id='uploadPreviewDiv\" + fNum + \"'></div>\";\n";		
	$str.="			str+=\"<br><span class='button small' style='margin-top:3px;'><input type='button' value='삭제' onclick='fnDelThumnail(\\\"\\\",\" + fNum + \")'  /></span>\";\n";
	
	$str.="	jQuery('#preview' + fNum).html(str);\n";
	
	$str.="	$.ajax({\n";
	$str.="			url:'/_Include/fileUploader/uploadifyDelete.php',\n";
	$str.="			type:'POST',\n";
	$str.="			cache:false,\n";
	$str.="			data : param,\n";
	$str.="			dataType:'text',\n";
	$str.="			error:fnErrorAjax,\n";
	$str.="			success:function(_response)\n";
	$str.="			{\n";
	
	//$str.="			alert(_response);\n";
	
	$str.="				v = _response.split('##');\n";
	$str.="				for(k=1;k<=v.length;k++){;\n";		
	
	$str.="				if(v[k]=='saveDeleted')\n";
	$str.="					{\n";
	
	$str.="					}///--End If\n";
	
	$str.="				if(v[k]=='tempDeleted')\n";
	$str.="					{\n";
	
	$str.="					}///--End If\n";
	
	$str.="				}//--End For\n";
	
	$str.="			}\n";
	
	$str.="		});\n";

	/*
	$str.="	jQuery('#uploadPreviewDiv' + fNum).html('');\n";
	$str.="	nows= jQuery('#inNowFileSaveName' + fNum).val();\n";
	$str.="	nowr= jQuery('#inNowFileRealName' + fNum).val();\n";
	
	
	$str.="	ins= jQuery('#inFileName' + fNum).val();\n";
	$str.="	inr= jQuery('#inFileSaveName' + fNum).val();\n";
	$str.="	alert(inr);\n";
	*/
	
	
	$str.="}\n";
	#-------------------------------------------------
	
	
	
	$str.="</script>";		
	
	$str.= "<input id='file_upload" . $num . "' name='file_upload" . $num . "' type='file' />";
	$str.= "<input id='inFileName" . $num . "' name='inFileName" . $num . "' type='hidden' />";
	$str.= "<input id='inFileSaveName" . $num . "' name='inFileSaveName" . $num . "' type='hidden' />";
	$str.= "<input id='inNowFileSaveName" . $num . "' name='inNowFileSaveName" . $num . "' value='" . $fInfo["saveFile"] . "' type='hidden' />";
	$str.= "<input id='inNowFileRealName" . $num . "' name='inNowFileRealName" . $num . "' value='" . $fInfo["realFile"] . "' type='hidden' />";
	$str.="</td><td style='" . $re_width . "px;height:100px;' id='preview" . $num . "' align=center>";
	$str.="<div style='width:" . $re_width . "px;border:1px solid #efefef;text-align:center;";
	if($fInfo["realFile"])$str.="display:block;'";
	else $str.="display:none;'";
	$str.=" id='uploadPreviewDiv" . $num . "'>";
	if($fInfo["realFile"])$str.=$fileHTML;
	$str.="</div>";
	if($fInfo["realFile"])$str.="<a href=\"javascript:fileDown('" . $fInfo["fileCode"] . "','" . $fInfo["fileCode"] . "','" . $fInfo["fieldCode"] . "','" . $fInfo["dataIDX"] . "','" . $fInfo["fileNum"] ."')\">" . $fInfo["realFile"] . " <br>[Down]</a>";
	
	//$str.= "<br><input id='inDeleted" . $num . "' name='inDeleted" . $num . "' type='button' value='삭제'>";
	
	$str.= "<br><span class='button small' style='margin-top:3px;'><input type='button' value='삭제' onclick='fnDelThumnail(\"" . $fInfo["fileCode"] . "/" . $fInfo["saveFile"] . "\"," . $num . ")'  /></span>";
	
	$str.="</td></tr>";
	$str.="</table>";
	echo $str;
}


/*===============================================================
	■ 관리자모드 레벨 리턴 함수
	※ 관리자모드에만 쓰이긴 하지만 다른 곳에서 쓰일수도 있어서 공용함수에 넣음.
===============================================================*/
function getHtmlCode2Level($lv,$isPrint=0,$isUser=0)
{
	switch($lv)
	{
	  case ($lv==1):
			$colorName="일반";
			$colorCode="#000000";
			break;
		case ($lv==10):
			$colorName="레드";
			$colorCode="#FF0000";
			break;
		case ($lv==20):
			$colorName="그린";
			$colorCode="#00FF00";
			break;
		case ($lv==30):
			$colorName="핑크";
			$colorCode="#FFA2DD";
			break;
		case ($lv==40):
			$colorName="퍼플";
			$colorCode="#940CF3";
			break;
		case ($lv==50):
			$colorName="오렌지";
			$colorCode="#F3730C";
			break;
		case ($lv==60):
			$colorName="브라운";
			$colorCode="#6E543F";
			break;
		case ($lv==70):
			$colorName="민트";
			$colorCode="#19C2E0";
			break;
		case ($lv==80):
			$colorName="옐로우";
			$colorCode="#D8D000";
			break;
		case ($lv==90):
			$colorName="블루";
			$colorCode="#0000FF";
			break;
	}

	$resultHtml="";
	
	if($isPrint)$colorCode="#000000";

	if($colorName && $colorCode)
	{
		$resultHtml = "<font style='color:" . $colorCode . ";'><b>" . $colorName . "</font>";
		if(!$isUser)if($lv>=10)$resultHtml.= ($lv/10) . "</b>가";
	}
	return $resultHtml;
}


/*===============================================================
■ Paging Class
===============================================================*/
class Paging {
	//------ 페이징에 필요한 변수 및 기본값 설정
	var $page_size=10;			//-- 한번에 보여지는 페이지수  1 2 3 4 [5] 6....10
	var $list_size=10;			//-- 한페이지에 보여지는 목록수
	var $page=1;				//-- 현재 페이지 번호

	var $left_icon = "<font size=4><img src='/image_2/icon_11.gif' border=0></font>";
	var $right_icon = "<font size=4><img src='/image_2/icon_12.gif' border=0></font>";

	//------- Return 해주는 값들
	var $total_cnt;				// $total_cnt : 조건을 걸지않고 레코드 갯수
	var $total_result;		// $total_result : 조건을 걸지않고 레코드 결과
	var $total_where_cnt;			// $total_where_cnt : 조건을 걸고 레코드 수
	var $total_page;			// $total_page : 조건을 걸고 전체 페이지 수
	var $result;				// 조건을 건 레코드 결과
	var $count_sql;
	
	//------- Test 를 위한 SQL
	var $result_sql;

	/*----------------------------------------------------------------------------------
	☞  set_page_size : 페이지 크기 설정(기본으로 설정되어있지만 수정시 사용한다)
	 1. $list_size : 한페이지 출력될 게시물 갯수  	 2. $page_size : 아래에 표시되는 페이지들 갯수
	----------------------------------------------------------------------------------*/
	function set_page_size($list_size=10,$page_size=10){
		if($list_size)$this->list_size = abs($list_size);
		if($page_size)$this->page_size = abs($page_size);
	}

	/*----------------------------------------------------------------------------------
	☞  set_page : 페이징할때 필요한 기본 구성요소를 받아서 쿼리문 설정
	 1. $sql : 기본 쿼리문
	 2. $where : 조건문 (검색시 작성된 조건문)
	 3. $order : 정렬문 (order by 문자 제외. 필드명과 정렬방식만 필요)
	 4. $path : 이동시 들고다니는 문자열
	----------------------------------------------------------------------------------*/
	function set_page($sql,$where,$order,$page,$group,$having=""){

		if($page)$this->page = abs($page);

		$result_sql = $sql;

		#-- Group by 가 애매하구나...
		//---- group by 가 있을경우 where 문 붙이기 전에 group by 한번 붙여서 쿼리를 날려야 한다
		if($group)$result_sql.=" group by " . $group;

		if($having)$result_sql.= " having " . $having;
		
		/*=========== 쿼리가 복잡해 질수록 너무 큰 부담이 된다!!! 빼자! ==========================
		$result = mysql_query($result_sql) or die($result_sql . "<br><br>" . mysql_error());
		$this->total_cnt = @mysql_num_rows($result); 		//-- 조건을 붙이지 않은 기본 sql 문의 레코드 갯수
		$this->total_result = $result;
		*/			
		$this->total_cnt=0;

		//---- result_sql 에 group by 가 있으면 다시 떼어낸다


		//---- 조건문 붙이기 기본으로 넘긴 sql 문에 where 문이 들어가 있는지 체크해서 붙인다.

		$chk = 'where';
		$tmp = strpos($result_sql,'where');
		$tmp2 = strpos($result_sql,'group by');
		if($where  && $tmp && !$tmp2){$result_sql.= " and " . $where;}
		else if($where)
		{
				#-- where 과 group by 의 위치 측정. 만약 group by 가 where 보다 앞에 있다면 서브쿼리로 인정하고 group by 관련 기능은 패스
				$wp = strrpos($result_sql,"where");					
				$gp = strrpos($result_sql,"group by");
				
				if($wp < $gp)
				{
					$tmpGroup = explode("group by" , $result_sql);
					$result_sql = $tmpGroup[0];
					if($tmp)$result_sql.= " and " .  $where;
					else $result_sql.=" where " . $where;
					if($tmpGroup[1])$result_sql.= " group by " . $tmpGroup[1];
				}
				else
				{
					if($tmp)$result_sql.= " and " .  $where;
					else $result_sql.=" where " . $where;
				}
		}
		
		$this->count_sql = $result_sql;

		//---- group by 가 있을경우 where 문 붙이고 다시 바로 group by 를 붙여준다
		//if($group)$result_sql.=" group by " . $group;
		
		//-- 결과를 세션에 넣어놓고 비교 후 달라진 쿼리일 경우 새로 카운트함
		if($_SESSION["paging_sql"]!=$result_sql)
		{
			$result = sql_query($result_sql);
			$this->total_where_cnt=mysqli_num_rows($result); 		//-- 조건붙이고 레코드 갯수
			
			$_SESSION["paging_sql"] = $result_sql;
			$_SESSION["paging_cnt"] = $this->total_where_cnt;
			//echo "<!-- Paging 새로처리 -->";
		}
		else
		{
			//echo "<!-- Paging 세션에서 -->";
			$this->total_where_cnt = $_SESSION["paging_cnt"];
		}
		
		
		//------------------------------------------------------------------------------

		//---- 정렬문 붙이기
		if($order)$result_sql.=" order by " . $order;


		//--- 쿼리문 확인을 위해 최종 결과 쿼리 저장
		$this->result_sql = $result_sql;

		//---- 한페이지 분량의 레코드를 가져오기 위해 sql 에 limit 걸기
		$limit=(($this->page-1)*$this->list_size);

		$result_sql.=" limit $limit,$this->list_size ";

		$this->result = sql_query($result_sql);					//-- 조건붙이고 한페이지 결과물
		$this->result_sql = $result_sql;
		//---- 총페이지 계산
		$this->total_page = ceil(abs($this->total_where_cnt) / abs($this->list_size));//전체 페이지 수
	}
	function pageNavi($fun="",$mmid="")
	{

		if(!$fun)$fun="goPage";

		//---- 각종 페이지수 계산 ---------------------------------------------------------
		$start_page = floor($this->page/$this->page_size)*$this->page_size; // 시작페이지 계산
		if($start_page==0)$start_page=1;
		$end_page=$start_page + $this->page_size-1;  // 끝페이지 계산
		if($start_page==1)$end_page=$this->page_size;
		if($end_page>$this->total_page){$end_page=$this->total_page;}
		//--------------------------------------------------------------------------------

		//----- 페이지 출력 ---------------------------------------------------------------
		
		$page_num.="<ul>";

		if($start_page>1)$page_num.="<li><a href='javascript:" . $fun . "(" . ($start_page-1) . ")' class='nextpre'>이전</li>";

		for($i=$start_page;$i<=$end_page;$i++)
		{
			$page_num.="<li>";
			if($i == $this->page) $page_num.="<a href='#' class='now'>" . $i . "</a>";
			else $page_num.= "<a href='javascript:" . $fun . "(" . $i . ")' >" . $i . "</a>";
			$page_num.="</li>";
		}

		if($end_page<$this->total_page)$page_num.="<li><a href='javascript:" . $fun . "(" . ($end_page+1) . ")' class='nextpre'>다음</a></li>";
		$page_num.="</ul>";
		//--------------------------------------------------------------------------------
		return $page_num;
	}


	/*----------------------------------------------------------------------------------
	 listInfo : 현재 페이지 정보 출력
	----------------------------------------------------------------------------------*/
	function listInfo()
	{
		//$pinfo = "전체 " . $this->total_cnt . " 개 중 " . $this->total_where_cnt . " 개 / 전체 " . $this->total_page . " page 에서 현재 " . $this->page . " page";
		$pinfo = $this->total_where_cnt . " 개의 결과 / 전체 " . $this->total_page . " page 에서 현재 " . $this->page . " page";
		return $pinfo;
	}


	function changeSize()
	{

		if($this->list_size==10)$sz1="selected";
		if($this->list_size==20)$sz2="selected";
		if($this->list_size==50)$sz3="selected";
		if($this->list_size==70)$sz4="selected";
		if($this->list_size==100)$sz5="selected";
		if($this->list_size==200)$sz6="selected";
		if($this->list_size==500)$sz7="selected";



		$sinfo = "<select name='listSize' onchange='document.location=document.location.pathname + \"?path=\" + path + \"&page=1&listSize=\" + this.value'>\n";
		$sinfo.="<option value=10 " . $sz1 . ">10개씩 보기</option>\n";
		$sinfo.="<option value=20 " . $sz2 . ">20개씩 보기</option>\n";
		$sinfo.="<option value=50 " . $sz3 . ">50개씩 보기</option>\n";
		$sinfo.="<option value=70 " . $sz4 . ">70개씩 보기</option>\n";
		$sinfo.="<option value=100 " . $sz5 . ">100개씩 보기</option>\n";
		$sinfo.="<option value=200 " . $sz6 . ">200개씩 보기</option>\n";
		$sinfo.="<option value=500 " . $sz7 . ">500개씩 보기</option>\n";
		$sinfo.="</select>\n";
		return $sinfo;
	}

}



/* 페이징 함수 */
/*===============================================================
■ 새로운 페이징 Paging Class ( 다중 테이블 join 시 속도 올리기)
===============================================================*/
class Paging_new {
	//------ 페이징에 필요한 변수 및 기본값 설정
	var $page_size=10;			//-- 한번에 보여지는 페이지수  1 2 3 4 [5] 6....10
	var $list_size=10;			//-- 한페이지에 보여지는 목록수
	var $page=1;				//-- 현재 페이지 번호
	var $left_icon = "<font size=4><img src='/image_2/icon_11.gif' border=0></font>";			//-- 좌측으로 아이콘
	var $right_icon = "<font size=4><img src='/image_2/icon_12.gif' border=0></font>";		//-- 우측으로 아이콘

	//------- Return 해주는 값들
	var $total_cnt;				// $total_cnt : 조건을 걸지않고 레코드 갯수
	var $total_result;		// $total_result : 조건을 걸지않고 레코드 결과
	var $total_where_cnt;			// $total_where_cnt : 조건을 걸고 레코드 수
	var $total_page;			// $total_page : 조건을 걸고 전체 페이지 수
	var $result;				// 조건을 건 레코드 결과
	
	//------- Test 를 위한 SQL
	var $result_sql;
	var $count_sql;

	/*----------------------------------------------------------------------------------
	☞  set_page_size : 페이지 크기 설정(기본으로 설정되어있지만 수정시 사용한다)
	 1. $list_size : 한페이지 출력될 게시물 갯수  	 2. $page_size : 아래에 표시되는 페이지들 갯수
	----------------------------------------------------------------------------------*/
	function set_page_size($list_size=10,$page_size=10){
		if($list_size)$this->list_size = abs($list_size);
		if($page_size)$this->page_size = abs($page_size);
	}

	/*----------------------------------------------------------------------------------
	☞  set_icon : 왼쪽 오른쪽 아이콘 설정(기본으로 설정되어있지만 수정시 사용한다)
	 1. $left_icon : 좌측 아이콘           	 2. $right_icon : 우측 아이콘
	----------------------------------------------------------------------------------*/
	function set_icon($left_icon,$right_icon){
		if($left_icon)$this->left_icon = $left_icon;
		if($right_icon)$this->right_icon = $right_icon;
	}

	/*----------------------------------------------------------------------------------
	☞  set_page : 페이징할때 필요한 기본 구성요소를 받아서 쿼리문 설정
	 1. $sql : 기본 쿼리문
	 2. $mainTbl : 기본 테이블 쿼리(해당 테이블을 기준으로 레코드 처리)
	 
	 3. $where : 조건문 (검색시 작성된 조건문)
	 4. $mainWhere : 기본테이블 조건문 (검색시 작성된 조건문)
			 
	 4. $order : 정렬문 (order by 문자 제외. 필드명과 정렬방식만 필요)
	 4. $mainOrder : 기본테이블 정렬문 (order by 문자 제외. 필드명과 정렬방식만 필요)
	 
	 5. $path : 이동시 들고다니는 문자열
	----------------------------------------------------------------------------------*/
	/*----------------------------------------------------------------------------------
	☞ 데이터가 많은 테이블에서 레코드 카운트로 카운팅을 하는 미친 라이브러리 클래스 대신 쓰기 위해 만듬!!!
	----------------------------------------------------------------------------------*/
	function set_page2($sql,$countSql,$mainTbl,$where,$mainWhere,$order,$mainOrder,$page,$group,$having="",$option=''){

		if($page)$this->page = abs($page);

		$result_sql = $sql;
		
		##============= 기본 테이블 조합 ===============================
		if($mainTbl=="")
		{
			echo "기본 테이블 미지정.";
			exit();
		}
		
		if(strpos($mainWhere,'where')===false && $mainWhere)$mainWhere = " where " . $mainWhere;
		$mainTbl.=$mainWhere;
		$countTbl.=$mainWhere;
		
		if(strpos($mainOrder,'order by')===false && $mainOrder)$mainOrder = " order by " . $mainOrder;			
		$mainTbl.=$mainOrder;
		

		#-- Group by 가 애매하구나...
		//---- group by 가 있을경우 where 문 붙이기 전에 group by 한번 붙여서 쿼리를 날려야 한다
		if($group)$result_sql.=" group by " . $group;
		if($having)$result_sql.= " having " . $having;
		
		/*=========== 쿼리가 복잡해 질수록 너무 큰 부담이 된다!!! 빼자! ==========================
		$result = mysql_query($result_sql) or die($result_sql . "<br><br>" . mysql_error());
		$this->total_cnt = @mysql_num_rows($result); 		//-- 조건을 붙이지 않은 기본 sql 문의 레코드 갯수
		$this->total_result = $result;
		*/			
		$this->total_cnt=0;

		//---- result_sql 에 group by 가 있으면 다시 떼어낸다


		//---- 조건문 붙이기 기본으로 넘긴 sql 문에 where 문이 들어가 있는지 체크해서 붙인다.

		$chk = 'where';
		$tmp = strpos($result_sql,'where');
		$tmp2 = strpos($result_sql,'group by');
		if($where  && $tmp && !$tmp2){$result_sql.= " and " . $where;}
		else if($where)
		{
			#-- where 과 group by 의 위치 측정. 만약 group by 가 where 보다 앞에 있다면 서브쿼리로 인정하고 group by 관련 기능은 패스
			$wp = strrpos($result_sql,"where");
			$gp = strrpos($result_sql,"group by");
			
			if($wp < $gp)
			{
				$tmpGroup = explode("group by" , $result_sql);
				$result_sql = $tmpGroup[0];
				if($tmp)$result_sql.= " and " .  $where;
				else $result_sql.=" where " . $where;
				if($tmpGroup[1])$result_sql.= " group by " . $tmpGroup[1];
			}
			else
			{
				if($tmp)$result_sql.= " and " .  $where;
				else $result_sql.=" where " . $where;
			}
		}

		//---- group by 가 있을경우 where 문 붙이고 다시 바로 group by 를 붙여준다
		//if($group)$result_sql.=" group by " . $group;
		
		//-- 결과를 세션에 넣어놓고 비교 후 달라진 쿼리일 경우 새로 카운트함
		
		if ($option == 'countQuery') {
			$count_sql = $countSql;
		}
		else {
			$count_sql = $countSql.str_replace("a.","",$mainWhere);
		}

		if($_SESSION["paging_sql"]!=$count_sql)
		{	
			$result = sql_query($count_sql);
			$rs=sql_fetch_array($result);
			$this->total_where_cnt=$rs['cnt']; 		//-- 조건붙이고 레코드 갯수
			$_SESSION["paging_sql"] = $count_sql;
			$_SESSION["paging_cnt"] = $this->total_where_cnt;
			echo "<!-- Paging 새로처리 -->";
		}
		else
		{
			echo "<!-- Paging 세션에서 -->";
			$this->total_where_cnt = $_SESSION["paging_cnt"];
		}
		
		//------------------------------------------------------------------------------

		//---- 정렬문 붙이기
		if($order)$result_sql.=" order by " . $order;


		//--- 쿼리문 확인을 위해 최종 결과 쿼리 저장
		$this->result_sql = $result_sql;

		//---- 한페이지 분량의 레코드를 가져오기 위해 sql 에 limit 걸기
		$limit=(($this->page-1)*$this->list_size);

		if ($option == 'countQuery') {
			if ($where == '' && $having == '') {
				$mainTbl.=" limit $limit,$this->list_size ";
				$result_sql = str_replace("##mainTbl##",$mainTbl,$result_sql);
			}
			else  {
				$result_sql = str_replace("##mainTbl##",$mainTbl,$result_sql);
				$result_sql.=" limit $limit,$this->list_size ";
			}
		}
		else {
			$mainTbl.=" limit $limit,$this->list_size ";
			$result_sql = str_replace("##mainTbl##",$mainTbl,$result_sql);
		}

		$this->result = sql_query($result_sql);					//-- 조건붙이고 한페이지 결과물

		$this->count_sql = $count_sql;
		$this->result_sql = $result_sql;
		//---- 총페이지 계산
		$this->total_page = ceil(abs($this->total_where_cnt) / abs($this->list_size));//전체 페이지 수
	}

	/*----------------------------------------------------------------------------------
	☞  pageNavi : 페이징 계산하여 출력해주기 (인자값으로 다른 자바스크립트함수를 호출 할 수도 있다)
	----------------------------------------------------------------------------------*/
	function pageNavi($fun="")
	{

		if(!$fun)$fun="goPage";

		//---- 각종 페이지수 계산 ---------------------------------------------------------
		$start_page = floor($this->page/$this->page_size)*$this->page_size; // 시작페이지 계산
		if($start_page==0)$start_page=1;
		$end_page=$start_page + $this->page_size-1;  // 끝페이지 계산
		if($start_page==1)$end_page=$this->page_size;
		if($end_page>$this->total_page){$end_page=$this->total_page;}
		//--------------------------------------------------------------------------------

		//----- 페이지 출력 ---------------------------------------------------------------
		$page_num.="<table id='pageArea' ><tr>";

		if($start_page>1)$page_num.="<td id='pageNum' style='cursor:pointer;width:15px;' onclick='" . $fun . "(" . ($start_page-1) . ")'>" . $this->left_icon ."</td>\n";

		for($i=$start_page;$i<=$end_page;$i++)
		{
			$page_num.="<td id='pageNum'><div style='cursor:pointer;width:25px;text-align:center;border:1px solid #ffffff;' onmouseover='this.style.border=\"1px solid #2B45A4\";' onmouseout='this.style.border=\"1px solid #ffffff\";' onclick='" . $fun . "(" . $i . ")' " ;
			if($i == $this->page)$page_num.=" style='color:#2B45A4;font-size:14px;font-family:Tahoma,\"돋움\";font-weight:bold;font-size:13px;'><b><font color='#2B45A4'>" . $i . "</font></b>";
			else $page_num.= " ><b>" . $i . "</b>";
			$page_num.="</div></td>\n";
		}

		if($end_page<$this->total_page)$page_num.="<td id='pageNum' style='cursor:pointer;width:25px;' onclick='" . $fun . "(" . ($end_page+1) . ")'>" . $this->right_icon ."</td>\n";
		$page_num.="</tr></table>";

		//--------------------------------------------------------------------------------
		return $page_num;
	}


	/*----------------------------------------------------------------------------------
	 listInfo : 현재 페이지 정보 출력
	----------------------------------------------------------------------------------*/
	function listInfo()
	{
		//$pinfo = "전체 " . $this->total_cnt . " 개 중 " . $this->total_where_cnt . " 개 / 전체 " . $this->total_page . " page 에서 현재 " . $this->page . " page";
		$pinfo = $this->total_where_cnt . " 개의 결과 / 전체 " . $this->total_page . " page 에서 현재 " . $this->page . " page";
		return $pinfo;
	}

	function changeSize()
	{

		if($this->list_size==10)$sz1="selected";
		if($this->list_size==20)$sz2="selected";
		if($this->list_size==50)$sz3="selected";
		if($this->list_size==70)$sz4="selected";
		if($this->list_size==100)$sz5="selected";
		if($this->list_size==200)$sz6="selected";

		$sinfo = "<select name='listSize' onchange='document.location=document.location.pathname + \"?path=\" + path + \"&page=1&listSize=\" + this.value'>\n";
		$sinfo.="<option value=10 " . $sz1 . ">10개씩 보기</option>\n";
		$sinfo.="<option value=20 " . $sz2 . ">20개씩 보기</option>\n";
		$sinfo.="<option value=50 " . $sz3 . ">50개씩 보기</option>\n";
		$sinfo.="<option value=70 " . $sz4 . ">70개씩 보기</option>\n";
		$sinfo.="<option value=100 " . $sz5 . ">100개씩 보기</option>\n";
		$sinfo.="<option value=200 " . $sz6 . ">200개씩 보기</option>\n";
		$sinfo.="</select>\n";
		return $sinfo;
	}

}



/*===============================================================
■ Category Class
===============================================================*/
class Category {
	
	/*===== 자주 쓰이는 지역변수 =======================*/
	var $_CATE_INFO_ = array();			//-- 카테고리 정보를 다 담고 있는 배열(DB와 같은형태)
	var $_CATE_INFO_IDX_ = array(); //-- 카테고리 정보를 다 담고 있는 배열(IDX 기준);

	var $_CATE_SORT_=0;								//-- 현재 카테고리의 순번
	var $_CATE_LEVEL_=0;							//-- 현재 카테고리의 레벨
	var $_CATE_IDX_=0;								//-- 현재 카테고리의 고유번호
	var $_CATE_KNAME_=0;								//-- 현재 카테고리의 한글명
	var $_CATE_ENAME_=0;								//-- 현재 카테고리의 영문명


	var $_CATE_COUNT_=0;							//-- 전체 카테고리 갯수
	var $_CATE_LOCATION_ = array();		//-- 카테고리 위치 정보

	var $_CATE_SUB_ = array();				//-- 서브 카테고리 정보(현제 레벨의 바로 밑의 정보들)


	//-- 카테고리 정보 배열 생성
	function	init()
	{		
		$cateCnt = 0;
		$sql = "Select * from 2011_categoryInfo order by CTlv1,CTlv2,CTlv3,CTlv4";
		$result = sql_query($sql);

		while($rs = sql_fetch_array($result))
		{
			//-- 좌측메뉴 / 상단메뉴 / 기타 등등에 이용되는 순서기준 배열
			//echo $cateRs2["CTlevel"] . "<br>";
			
			#-- 자신의 레벨 구하기
			$myLevel=0;
			for($k=1;$k<=4;$k++)if($rs["CTlv" . $k])$myLevel++;
			
			$this->_CATE_INFO_[$rs["CTlv1"]][$rs["CTlv2"]][$rs["CTlv3"]][$rs["CTlv4"]]["IDX"] = $rs["IDX"];
			$this->_CATE_INFO_[$rs["CTlv1"]][$rs["CTlv2"]][$rs["CTlv3"]][$rs["CTlv4"]]["Kname"] = $rs["CTkname"];
			$this->_CATE_INFO_[$rs["CTlv1"]][$rs["CTlv2"]][$rs["CTlv3"]][$rs["CTlv4"]]["Ename"] = $rs["CTename"];
			$this->_CATE_INFO_[$rs["CTlv1"]][$rs["CTlv2"]][$rs["CTlv3"]][$rs["CTlv4"]]["level"] = $myLevel;
			
			$this->_CATE_INFO_IDX_[$rs["IDX"]]["lv1"]=$rs["CTlv1"];
			$this->_CATE_INFO_IDX_[$rs["IDX"]]["lv2"]=$rs["CTlv2"];
			$this->_CATE_INFO_IDX_[$rs["IDX"]]["lv3"]=$rs["CTlv3"];
			$this->_CATE_INFO_IDX_[$rs["IDX"]]["lv4"]=$rs["CTlv4"];
		}
	}


	//-- 전체 배열을 돌려준다.
	function getCategoryInfo()
	{
		return $this->_CATE_INFO_;
	}

	//-- 현재 카테고리의 위치 정보를 배열형태로 돌려준다
	//-- ex) 카테고리1 > 카테고리2 > 카테고리3 ...
	function setCategoryPosition($cateIDX)
	{
		if(!$cateIDX)return false;
		//echo $cateIDX;
		$this->_CATE_IDX_=$cateIDX; 	//-- 현재 카테고리의 고유번호
		
		$lv1=$this->_CATE_INFO_IDX_[$this->_CATE_IDX_]["lv1"];
		$lv2=$this->_CATE_INFO_IDX_[$this->_CATE_IDX_]["lv2"];
		$lv3=$this->_CATE_INFO_IDX_[$this->_CATE_IDX_]["lv3"];
		$lv4=$this->_CATE_INFO_IDX_[$this->_CATE_IDX_]["lv4"];
		
		//echo "[" . $lv1 . "]";;
		
		$targetObj = $this->_CATE_INFO_[$lv1][$lv2][$lv3][$lv4];
		$level = $targetObj["level"];
		$this->_CATE_LEVEL_=$level;
		
		#-- 배열 초기화
		for($k=0;$k<=4;$k++)$this->_CATE_LOCATION_[$k]=array();
		
		for($k=$level;$k>0;$k--)
		{
			#-- 코드 줄일수 있는데 이거 피곤해서 귀찮네 ㅡ_-;
			if($k==1)$targetObj = $this->_CATE_INFO_[$lv1][0][0][0];
			else if($k==2)$targetObj = $this->_CATE_INFO_[$lv1][$lv2][0][0];
			else if($k==3)$targetObj = $this->_CATE_INFO_[$lv1][$lv2][$lv3][0];
			else if($k==4)$targetObj = $this->_CATE_INFO_[$lv1][$lv2][$lv3][$lv4];
			
			$this->_CATE_LOCATION_[$targetObj["level"]]["IDX"]=$targetObj["IDX"];
			$this->_CATE_LOCATION_[$targetObj["level"]]["Kname"]=$targetObj["Kname"];
			$this->_CATE_LOCATION_[$targetObj["level"]]["Ename"]=$targetObj["Ename"];
		}
		return true;
		
	}//-- End Function
	
	
		#========= 다음에 클래스 안으로 넣어버립시다~~~ ===========================

	#--- 카테고리 배열을 1차원 배열로 변경해 준다.
	function fnCategoryConvert($targetObj,$myLevel,$cateArray)
	{				
		$size = sizeof($targetObj);
		
		if($myLevel==0)$size++;
		
		for($k=1;$k<$size;$k++)
		{
			if($myLevel==0)$targetObj2 = $targetObj[$k][0][0][0];
			else if($myLevel==1)$targetObj2 = $targetObj[$k][0][0];
			else if($myLevel==2)$targetObj2 = $targetObj[$k][0];
			else if($myLevel==3)$targetObj2 = $targetObj[$k];
			
			$s = sizeof($cateArray);
			$cateArray[$s]["IDX"] = $targetObj2["IDX"];
			$cateArray[$s]["Kname"] = $targetObj2["Kname"];
			$cateArray[$s]["Ename"] = $targetObj2["Ename"];
			$cateArray[$s]["level"] = $targetObj2["level"];
			
			//if($myLevel==0)echo $targetObj2["Kname"] . " / " ;;
			
			if($myLevel<3)
			{
				#== 재귀호출
				$cateArray = $this->fnCategoryConvert($targetObj[$k],$myLevel+1,$cateArray);
			}
		}
		
		return $cateArray;
	}


	function fnGetCategoryGroup($cateIDX)
	{
			if(!$cateIDX)return false;

			$CIDX = $this->_CATE_INFO_IDX_[$cateIDX];
			
			$lv1 = $CIDX["lv1"];
			$lv2 = $CIDX["lv2"];
			$lv3 = $CIDX["lv3"];
			$lv4 = $CIDX["lv4"];
			
			#-- level 체크
			$myLevel = $this->_CATE_INFO_[$lv1][$lv2][$lv3][$lv4]["level"];
			
			#-- 일단 자기 자신 넣고 시작
			$s = sizeof($cateArray);
			$cateArray[$s]["IDX"] = $this->_CATE_INFO_[$lv1][$lv2][$lv3][$lv4]["IDX"];
			$cateArray[$s]["Kname"] = $this->_CATE_INFO_[$lv1][$lv2][$lv3][$lv4]["Kname"];
			$cateArray[$s]["Ename"] = $this->_CATE_INFO_[$lv1][$lv2][$lv3][$lv4]["Ename"];
			$cateArray[$s]["level"] = $this->_CATE_INFO_[$lv1][$lv2][$lv3][$lv4]["level"];
			
			#-- 코드 줄일수 있는데 이거 피곤해서 귀찮네 ㅡ_-;
			if($myLevel==1)$targetObj = $this->_CATE_INFO_[$lv1];
			else if($myLevel==2)$targetObj = $this->_CATE_INFO_[$lv1][$lv2];
			else if($myLevel==3)$targetObj = $this->_CATE_INFO_[$lv1][$lv2][$lv3];
			else if($myLevel==4)return $cateArray;  #-- 레벨4가 해당 함수 호출하지는 않음
			
			$cateArray = $this->fnCategoryConvert($targetObj,$myLevel,$cateArray);
			return $cateArray;
	}
	
	#========= 다음에 클래스 안으로 넣어버립시다~~~ ===========================

}//-- End Class


function getHtmlCode4Level($lv,$isPrint=0,$isUser=0)
{
	switch($lv)
	{
	  case ($lv==1):
			$colorName="일반";
			$colorCode="#000000";
			break;
		case ($lv==10):
			$colorName="레드";
			$colorCode="#FF0000";
			break;
		case ($lv==20):
			$colorName="그린";
			$colorCode="#00FF00";
			break;
		case ($lv==30):
			$colorName="핑크";
			$colorCode="#FFA2DD";
			break;
		case ($lv==40):
			$colorName="퍼플";
			$colorCode="#940CF3";
			break;
		case ($lv==50):
			$colorName="오렌지";
			$colorCode="#F3730C";
			break;
		case ($lv==60):
			$colorName="브라운";
			$colorCode="#6E543F";
			break;
		case ($lv==70):
			$colorName="민트";
			$colorCode="#19C2E0";
			break;
		case ($lv==80):
			$colorName="옐로우";
			$colorCode="#D8D000";
			break;
		case ($lv==90):
			$colorName="블루";
			$colorCode="#0000FF";
			break;
	}

	$resultHtml="";
	
	if($isPrint)$colorCode="#000000";

	if($colorName && $colorCode)
	{
		$resultHtml = "<font style='color:" . $colorCode . ";'><b>" . $colorName . "VIP</b></font>";
		//if(!$isUser)if($lv>=10)$resultHtml.= ($lv/10) . "</b>가";
	}
	return $resultHtml;
}


function getSizeInfo($rsData){
	/*==================================================================
	크기 / 무게 문자열 생성
	==================================================================*/
	$PsizeInfo = "";
	$dbPsizeWidth = $rsData['PsizeWidth'];
	$dbPsizeLength = $rsData['PsizeLength'];
	$dbPsizeHeight = $rsData['PsizeHeight'];
	$dbPweight = $rsData['Pweight'];

	if($dbPsizeWidth && $dbPsizeWidth!="0.00")
	{
		$tmp = preg_split('[.]',$dbPsizeWidth);
		if (substr($tmp[1],1,1) > 0) {
			$PsizeInfo=number_format($dbPsizeWidth,2);
		} else if (substr($tmp[1],0,1) > 0) {
			$PsizeInfo=number_format($dbPsizeWidth,1);
		} else {
			$PsizeInfo=number_format($dbPsizeWidth,0);
		}
		
		$PsizeStr = "Width";
	}

	if($dbPsizeLength && $dbPsizeLength !="0.00")
	{
		if($PsizeInfo)
		{
			$PsizeInfo.=" X ";
			$PsizeStr.=" X ";
		}
		$tmp = preg_split('[.]',$dbPsizeLength);
		if (substr($tmp[1],1,1) > 0) {
			$PsizeInfo.=number_format($dbPsizeLength,2);
		} else if (substr($tmp[1],0,1) > 0) {
			$PsizeInfo.=number_format($dbPsizeLength,1);
		} else {
			$PsizeInfo.=number_format($dbPsizeLength,0);
		}
		$PsizeStr.= "Height";
	}

	if($dbPsizeHeight && $dbPsizeHeight != "0.00")
	{
		if($PsizeInfo)
		{
			$PsizeInfo.=" X ";
			$PsizeStr.=" X ";
		}
		$tmp = preg_split('[.]',$dbPsizeHeight);
		if (substr($tmp[1],1,1) > 0) {
			$PsizeInfo.=number_format($dbPsizeHeight,2);
		} else if (substr($tmp[1],0,1) > 0) {
			$PsizeInfo.=number_format($dbPsizeHeight,1);
		} else {
			$PsizeInfo.=number_format($dbPsizeHeight,0);
		}
		$PsizeStr.= "Depth";
	}

	if($dbPweight && $dbPweight!="0.00")$dbPweight=$dbPweight . " g";
	else $dbPweight="-";

	if(!$PsizeInfo)$PsizeInfo="-";
	else
	{
		$PsizeInfo = $PsizeInfo . " (mm " . $PsizeStr . ")";
	}
	if($dbPsizeInfo) {
		if($PsizeInfo == "-"){
			$PsizeInfo = $dbPsizeInfo;
		} else {
			$PsizeInfo = $PsizeInfo . " " . $dbPsizeInfo;
		}
	}
	return $PsizeInfo;
}

function fnPutNewCategory($no,$cate){
	global $g5;
	$query1 = " SELECT CTcode FROM 2011_categoryInfo WHERE IDX = '$cate' ";
	$row = sql_fetch($query1);		
	if($row['CTcode']){
		sql_query(" UPDATE 2011_productInfo SET Pcategory3 = '$row[CTcode]' WHERE IDX = '$no' ");
	}		
}

function fnPutNewCategory2($no,$cate){
	global $g5;
	$query1 = " SELECT CTcode FROM 2011_categoryInfo WHERE IDX = '$cate' ";
	$row = sql_fetch($query1);		
	if($row[CTcode]){
		sql_query(" UPDATE 2011_productInfo SET Pcategory2 = '$row[CTcode]' WHERE IDX = '$no' ");
	}
}

function fnGetNewCategory2($cate){
	global $g5;
	$query1 = " SELECT IDX FROM 2011_categoryInfo WHERE CTcode = '$cate' ";
	$row = sql_fetch($query1);
	if(!$cate) $row[IDX] = "";
	return $row[IDX];
}

function round_up ($value, $places=0) {
	if ($places < 0) { $places = 0; }
	$mult = pow(10, $places);
	return ceil($value * $mult) / $mult;
}

//DHL 부피구하기
function english_p_floor( $val, $d ){
	return floor($val * pow (10, $d) )/ pow (10, $d) ;
}
function english_sgn($x){
	return $x ? ($x>0 ? 1 : -1) : 0;
}
function english_round_down($num, $d = 0){
	return sgn($num)*p_floor(abs($num), $d);
}

function getEmsAmount($ems_option, $country, $weight, $length, $width, $height){
	/*
		$ems_option : ems / emspremium
		보안키 771865f1cc0217f4
		인증키 771865f1cc0217f491680673121058
		고객번호  0005099258
		getEmsAmount('ems', 'US', 1000, 10, 10, 10);
	*/
	$premiumcd = ($ems_option == "ems") ? "31" : "32";
	$data = array(
		'regkey' => '771865f1cc0217f491680673121058',	//인증키
		'premiumcd' => $premiumcd,	// 국제우편물 구분코드
		'em_ee' => 'em',			// 국제우편물 종류코드
		'boyn' => 'N',				// 보험가입 여부
		'boprc' => '0',				// 보험가입금액 KRW
		'apprno' => '60051C0062',	// 계약승인번호
		'countrycd' => $country,	// 도착 국가코드
		'totweight' => $weight * 1000,		// 우편물 총중량 g
		'boxlength' => $length,		// 포장상자 세로길이 cm
		'boxwidth' => $width,		// 포장상자 가로길이 cm
		'boxheight' => $height		// 포장상자 높이 cm
	);

	$url = "https://eship.epost.go.kr/api.EmsTotProcCmd.ems" . "?" . http_build_query($data, '', );

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	curl_close($ch);

	$xml = simplexml_load_string($response);
	$price = trim($xml->EmsTotProcCmd->emsTotProc);
	return ($price) ? $price : 0;
}
//거래명세표 층별 분리 / orderProduct 입력 / sku 1층, 2층, 3층		
function fnCheckFloorOrder($rack){		
	$ret = 0;
	
	$v1 = substr(trim($rack), 0, 1);
	if($v1 == 'P' || $v1 == 'B' || $v1 == 'C' || $v1 == 'A') {
		$ret = 2;
	} else if($v1 == 'D' || $v1 == 'F'){
		$ret = 3;
	} else {
		$v3 = substr(trim($rack), 0, 3);
		if($v3 >= 190 && $v3 <= 364) $ret = 2;
		else if($v3 >= 496) $ret = 3;
		else if($v3 >= 100 && $v3 <= 106) $ret = 1;
	}
	
	return $ret;
}

function getWeeksFromPreviousMonthToToday($today) {
    // 이전 달의 첫날
    $previousMonth = (clone $today)->modify('first day of last month');
    $previousMonthFirstDay = $previousMonth->setTime(0, 0);

    // 오늘 날짜
    $todayEnd = (clone $today)->setTime(23, 59, 59);

    $weeks = [];
    $currentWeekStart = clone $previousMonthFirstDay;

    while ($currentWeekStart <= $todayEnd) {
        $currentWeekEnd = clone $currentWeekStart;
        $currentWeekEnd->modify('next sunday');
        if ($currentWeekEnd > $todayEnd) {
            $currentWeekEnd = $todayEnd; // 오늘까지만 포함
        }
        $weeks[] = [
            'month' => $currentWeekStart->format('n'), // 월 (1 ~ 12)
            'week' => null, // 주차 계산
            'start' => $currentWeekStart->format('Y-m-d'),
            'end' => $currentWeekEnd->format('Y-m-d')
        ];
        $currentWeekStart->modify('next monday'); // 다음 주 시작
    }

    // 각 월의 주차를 계산
    $weekCounter = [];
    foreach ($weeks as &$week) {
        $month = $week['month'];
        if (!isset($weekCounter[$month])) {
            $weekCounter[$month] = 1;
        } else {
            $weekCounter[$month]++;
        }
        $week['week'] = $weekCounter[$month]; // 몇 번째 주인지 저장
    }

    return $weeks;
}
?>