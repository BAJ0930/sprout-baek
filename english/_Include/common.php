<?php
//<script> location.href = "http://www.cheonyu.com"; </script>
//echo "server maintenance";
//exit;
/*******************************************************************************
** 공통 변수, 상수, 코드
*******************************************************************************/
//error_reporting( E_ALL ^ E_NOTICE );
//error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );

// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

@set_time_limit(0);
if($_SERVER['REMOTE_ADDR'] == '183.111.167.98' || $_SERVER['REMOTE_ADDR'] == '222.186.20.54'){ echo "과도한 트래픽 발생을 유발하여 차단되었습니다. 천유닷컴 개발팀에 문의해주세요. Tel. 1544-7102"; exit; }

//==========================================================================================================================
// extract($_GET); 명령으로 인해 page.php?_POST[var1]=data1&_POST[var2]=data2 와 같은 코드가 _POST 변수로 사용되는 것을 막음
//--------------------------------------------------------------------------------------------------------------------------
$ext_arr = array ('PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST',
                  'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
                  'HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS');
$ext_cnt = count($ext_arr);
for ($i=0; $i<$ext_cnt; $i++) {
    // POST, GET 으로 선언된 전역변수가 있다면 unset() 시킴
    if (isset($_GET[$ext_arr[$i]]))  unset($_GET[$ext_arr[$i]]);
    if (isset($_POST[$ext_arr[$i]])) unset($_POST[$ext_arr[$i]]);
}
# Get 로 넘어오는 문자열 변수로 저장
foreach ($_GET as $GetName => $GetValue){
	$setGet = 1;			
	$GetString.=$GetName . "=" . $GetValue . "&";
	if(preg_match('/(sleep\()/i', strtolower($GetValue))) exit;
	if(preg_match('/(union)/i', strtolower($GetValue))) exit;
	$$GetName = $GetValue;
}
# Post 로 넘어오는 값 모두 변수로 저장
foreach ($_POST as $PostName => $PostValue)	{
	$method = strtolower($_SERVER['HTTP_REFERER']);		
	$setPost = 1;
	if(preg_match('/(sleep\()/i', strtolower($PostValue))) exit;
	if(preg_match('/(union)/i', strtolower($PostValue))) exit;
	$$PostName = $PostValue;
}
//==========================================================================================================================

$outIdx = array("32947","32611","32610","32609","32608","32607","32606","32605","32604","32603","32602","32545","32544","32543","32542","32540","32539","32525","32524","17122");


$q_string = strtolower($_SERVER['QUERY_STRING']);
if(preg_match("/sleep%28/i", $q_string)) {
	echo "ERROR";
	exit;
} else if(preg_match("/name_const/i", $q_string)) {
	echo "ERROR";
	exit;
}
// config


// 이 상수가 정의되지 않으면 각각의 개별 페이지는 별도로 실행될 수 없음
define('_CHEONYU_', true);
define("__HOME_PATH__","");
define("__HOME_SERVER_PATH__","/home/cheonyu/image");
define('G5_ESCAPE_FUNCTION', 'sql_escape_string');
$imageServerRoot = "/home/cheonyu/image";

/* 환경설정 */
// 천유닷컴 배송 박스 중 제일 큰 것 가로 690 mm * 세로 450 mm * 높이 450 mm = 139 725 000 mm3
$cBoXVol = "139000000";
$_monthAgo = time() - (86400 * 30);
include_once $_SERVER['DOCUMENT_ROOT']."/_Include/func.php";

// multi-dimensional array에 사용자지정 함수적용
function array_map_deep($fn, $array)
{
    if(is_array($array)) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $array[$key] = array_map_deep($fn, $value);
            } else {
                $array[$key] = call_user_func($fn, $value);
            }
        }
    } else {
        $array = call_user_func($fn, $array);
    }

    return $array;
}


// SQL Injection 대응 문자열 필터링
function sql_escape_string($str)
{
    $str = call_user_func('addslashes', $str);

    return $str;
}


//==============================================================================
// SQL Injection 등으로 부터 보호를 위해 sql_escape_string() 적용
//------------------------------------------------------------------------------
// magic_quotes_gpc 에 의한 backslashes 제거
if (get_magic_quotes_gpc()) {
    $_POST    = array_map_deep('stripslashes',  $_POST);
    $_GET     = array_map_deep('stripslashes',  $_GET);
    $_COOKIE  = array_map_deep('stripslashes',  $_COOKIE);
    $_REQUEST = array_map_deep('stripslashes',  $_REQUEST);
}

// sql_escape_string 적용
$_POST    = array_map_deep(G5_ESCAPE_FUNCTION,  $_POST);
$_GET     = array_map_deep(G5_ESCAPE_FUNCTION,  $_GET);
$_COOKIE  = array_map_deep(G5_ESCAPE_FUNCTION,  $_COOKIE);
$_REQUEST = array_map_deep(G5_ESCAPE_FUNCTION,  $_REQUEST);
//==============================================================================

// $member 에 값을 직접 넘길 수 있음
$config = array();
$member = array();
$board  = array();
$group  = array();
$g5     = array();

//==============================================================================
// 공통
//------------------------------------------------------------------------------
$connect_db = mysqli_connect("localhost", "cheonyu", "eoqkr0915") or die('MySQL Connect Error!!!');
$select_db  = mysqli_select_db($connect_db,"cheonyu") or die('MySQL DB Error!!!');
$g5['connect_db'] = $connect_db;
mysqli_set_charset($connect_db, 'utf8');

@ini_set("session.use_trans_sid", 0);    // PHPSESSID를 자동으로 넘기지 않음
@ini_set("url_rewriter.tags",""); // 링크에 PHPSESSID가 따라다니는것을 무력화함
@session_cache_limiter("no-cache, must-revalidate");

ini_set("session.cache_expire", 180); // 세션 캐쉬 보관시간 (분)
ini_set("session.gc_maxlifetime", 10800); // session data의 garbage collection 존재 기간을 지정 (초)
ini_set("session.gc_probability", 1); // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다. 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고하십시오.
ini_set("session.gc_divisor", 100); // session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다. 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다. session.gc_divisor의 기본값은 100입니다.

session_set_cookie_params(0, '/');
ini_set("session.cookie_domain", ".cheonyu.com");

@session_start();

// 4.00.03 : [보안관련] PHPSESSID 가 틀리면 로그아웃한다.
if (isset($_REQUEST['PHPSESSID']) && $_REQUEST['PHPSESSID'] != session_id())
    goto_url('/member/logout.php');


ob_start();

// 자바스크립트에서 go(-1) 함수를 쓰면 폼값이 사라질때 해당 폼의 상단에 사용하면
// 캐쉬의 내용을 가져옴. 완전한지는 검증되지 않음
header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0


	/*=================================================================
	세션 분할 이유 - 관리자는 일반 사용자로도 로그인이 가능하여야 함.
	===================================================================*/
	define("__ADMIN_ID__","ADMIN_ID");
	define("__U_ID__","USER_ID");
	define("__MANAGER_ID__","MANAGER_ID");

	#--- 사용자 일반 정보
	define("__MID__","NOWMID");	//<== 현재 작동중인 모드의 ID
	define("__MIDX__","MIDX");
	define("__MPWD__","MPWD");
	define("__MNAME__","MNAME");
	define("__MLEVEL__","MLEVEL");
	define("__MLV__","MLV");
	define("__MHP__","MHP");
	define("__MEMAIL__","MEMAIL");			
		
	$MID = $_SESSION[__U_ID__];	

	#-- 관리자모드에서는 관리자 ID 세션 로드
	if($isAdminMode && $_SESSION[__ADMIN_ID__])$MID = $_SESSION[__ADMIN_ID__];
		
	#-- 메니져모드에서는 메니져 ID 세션 로드
	if($isManagerMode && $_SESSION[__MANAGER_ID__])$MID = $_SESSION[__MANAGER_ID__];
				
	# 회원정보 로드
	if($MID){
		/*=================================================================
		1. 현재 페이지를 작동하는데에 다른 아이디 정보가 필요하다면 새로운 정보를 불러온다.
		2. 세션이 계속 유지만 되고 있다면 더이상 DB를 로드하지 않는다.
		===================================================================*/
		
		#-- 일반(도매) 회원 및 관리자 정보 로드
		$sql = "select * from 2011_memberInfo where MID='" . $MID . "'";
		$m_rs = sql_fetch($sql);
		
		if($m_rs["Mbirthday"] != "1") {
			echo "<script> location.href = '/member/logout.html'; </script>";
			exit;
		}
				
		if($_SESSION[__MID__] != $MID){
				
			if($isManagerMode && $_SESSION[__MANAGER_ID__]){
				#-- 판매자 정보 로드	
				$sql = "select * from 2011_makerSeller as a left join 2011_makerInfo as b on a.MKIDX = b.IDX  where a.MSID='" . $MID . "'";
				$m_rs = sql_fetch($sql);
					
				$_SESSION[__MIDX__] = $m_rs["IDX"];
				$_SESSION[__MID__] = $m_rs["MSID"];
				$_SESSION[__MPWD__] = $m_rs["MSpwd"];
				$_SESSION[__MNAME__] = $m_rs["MKkname"];
				$_SESSION[__MLEVEL__] = 95;
				$_SESSION[__MLV__] = "B";
				$_SESSION[__MHP__] = $m_rs["MKhp"];
				$_SESSION[__MEMAIL__] = $m_rs["MKemail1"];
				$_SESSION[__MKIDX__] = $m_rs["MKIDX"];
			} else {
				#-- 일반(도매) 회원 및 관리자 정보 로드
				$sql = "select * from 2011_memberInfo where MID='" . $MID . "'";
				$m_rs = sql_fetch($sql);
					
				$_SESSION[__MIDX__] = $m_rs["IDX"];
				$_SESSION[__MID__] = $m_rs["MID"];
				$_SESSION[__MPWD__] = $m_rs["MPWD"];
				$_SESSION[__MNAME__] = $m_rs["Mname"];
				$_SESSION[__MLEVEL__] = $m_rs["Mlevel"];
				$_SESSION[__MLV__] = $m_rs["MLV"];
				$_SESSION[__MHP__] = $m_rs["MHP"];
				$_SESSION[__MEMAIL__] = $m_rs["Memail"];
				$_SESSION["Mallow"] = $m_rs["Mallow"];
				$_SESSION[__MKIDX__] = '';
			}
				
			#-- 직원 레벨 강제 30으로! (구매등급을 30으로 할당 ㅡ_-)
			//if(!$isAdminMode && $m_rs["Mlevel"]==98)$_SESSION[__MLEVEL__]=30;
				
		}
				
		#=== 세션에서 정보를 가져온다.			
		$MIDX = $_SESSION[__MIDX__];
		$MPWD = $_SESSION[__MPWD__];
		$Mname = $_SESSION[__MNAME__];
		$Mlevel = $_SESSION[__MLEVEL__];
		$MLV = $_SESSION[__MLV__];
		$MHP = $_SESSION[__MHP__];
		$Memail = $_SESSION[__MEMAIL__];
		$Mmkidx = $_SESSION[__MKIDX__];
				
		$MHParray = explode("-",$MHP);
		$MemailArray = explode("@",$Memail);
				
		if($Mlevel>=98)$ADMIN=1;
			
		$uid = $MID;
				
		if($Mlevel==98){
			#-- 관리자 로그인시 권한도 로드
			$allowTemp = explode(",",$_SESSION["Mallow"]);
			for($k=0;$k<sizeof($allowTemp);$k++)	{
				$Mallow[$allowTemp[$k]]=1;
			}
		} else if($Mlevel==99) {
			$MenuAllow=1;
		}		
	} else {
		//-- 세션 아이디로 임시아이디 생성
		$tmp_cart_id = get_cookie('ck_guest_cart_id');
		if($tmp_cart_id) {
			set_session('ss_cart_id', $tmp_cart_id);
		} else {
			$tmp_cart_id = session_id();
			set_session('ss_cart_id', $tmp_cart_id);
			set_cookie('ck_guest_cart_id', $tmp_cart_id, (30 * 86400));
		}
		$uid = $tmp_cart_id;
	}
	
	if (isset($_REQUEST['url'])) {
		$url = strip_tags(trim($_REQUEST['url']));
		$urlencode = urlencode($url);
	} else {
		$url = '';
		$urlencode = urlencode($_SERVER['REQUEST_URI']);
	}

//-- 기본 판매자 ID
$masterSeller = "1000u";
$masterSellerName = "cheonyu";
$shopID = "1000u";

$sql = " select IDX,CFbankOrner,CFbankName,CFbankCode,CFperDollar,CFdeliveryLimit,CFdeliveryPay,CFcomDeliveryLimit,CFcomDeliveryPay,CFcomDeliveryLimit2,CFcomDeliveryPay2,CFcomDeliveryLimit3,CFcomDeliveryPay3,CFpersonTerms,CFcompanyTerms,CFpersonInfo,CFsizesum5 from 2011_configInfo ";
$shopConfig = sql_fetch($sql);	

$checkWeight = $shopConfig['CFsizesum5'];

$sql = " SELECT PP FROM nPrice ORDER BY NO DESC LIMIT 0,1 ";
$shopConfig2 = sql_fetch($sql);
$shopConfig[CFperDollar] = $shopConfig2['PP'];

$uid = addslashes(trim($uid));
$MID = addslashes(trim($MID));

$minusID = array("justchi");
$_Minus = "";
if (in_array($MID, $minusID)) {
	$_Minus = "1";
}
//$shopArray["dada"] = "다다문구";
//$shopArray["dadaLV"] = 20;
/* 환경설정 */
// 천유닷컴 배송 박스 중 제일 큰 것 가로 690 mm * 세로 450 mm * 높이 450 mm = 139 725 000 mm3
$cBoXVol = "139000000";
$cBoxVol_7 = 103635 / 6000;
$cBoxVol_3 = 57120 / 6000;
$cBoxVol_1 = 27280 / 6000;
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">	
<meta http-equiv="X-UA-Compatible" content="IE=9">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Wholesale Shopping - Cheonyu.com</title>
<script type="text/javascript" src="/_Include/js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="/_Include/js/popupCore.js"></script>
<script type="text/javascript" src="/_Include/js/script.js?<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/_Include/js/script.js')?>"></script>
<script type="text/javascript" src="/_Include/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" />
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
<link href="https://fonts.cdnfonts.com/css/montserrat" rel="stylesheet">
<link href="/css/style.css?<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/css/style.css')?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://www.cheonyu.com/css/icomoon/icomoon.css?5">
<link rel="icon" href="/favicon.ico?250120" type="image/x-icon">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-BY38R1E5YB"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-BY38R1E5YB');
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MF3LDHVL');</script>
<!-- End Google Tag Manager -->
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MF3LDHVL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->