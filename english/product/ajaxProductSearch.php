<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if(!$MID) {
		echo "##noMember##";
		exit;
	}

	$sql = "Select * from  2011_productInfo  where IDX='" . $inPIDX . "' AND Pshop='" . $shopID . "' AND Pdeleted=0 AND Pprice3>0 AND Pstate<10 AND Pagree=1 ";
	$data= sql_fetch($sql);

	if(!$data['IDX']) { echo "##noProduct##"; exit; }
	
	$opEndName = "";

	if($data['PoptionUse'] > 1){
	
		$sql = "Select * from 2011_productOption where PIDX='" . $inPIDX . "' and OPhidden=0 order by OPname,OPvalue";
		$opResult = sql_query($sql);
		
		//-- 옵션 배열화
		$opValueCount=0;
		$opNowName="";
		$opNameCnt=0;
		while($opRs = sql_fetch_array($opResult)){
			/*$opArray[$opValueCount]["IDX"]		=	$opRs["IDX"];
			$opArray[$opValueCount]["name"]	=	$opRs["OPname"];
			$opArray[$opValueCount]["value"]	=	$opRs["OPvalue"];
			$opArray[$opValueCount]["barcode"]	=	$opRs["OPbarcode"];
			$opArray[$opValueCount]["stock"]	=	$opRs["OPstock"];*/

			if($opValueCount == 0) $jum = "";
			else $jum = "^";

			if($opRs["OPvalueEng"]) $opVal = $opRs["OPvalueEng"];
			else $opVal = $opRs["OPvalue"];

			//$opEndName = $opEndName . $jum . $opRs["OPvalue"] . "@" . $opRs["IDX"];
			$opEndName = $opEndName . $jum . $opVal . "@" . $opRs["IDX"];
			
			$opValueCount++;
		}
		#=========== 기본 출력 양식 ====================================		
	}
	$dbPsaveFile1 = $data['PsaveFile1'];
	$fPosition = explode("/",$dbPsaveFile1);
	$thumb = $fPosition[0] . "/thumb/" . $fPosition[1];			

	echo "##OK##".$data['PengName']."##".$thumb."##".$opEndName;

?>