<?
	if(@$_POST["isMode"]=="totalAdmin")$isAdminMode=1;
	else $isManagerMode=1;
	
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();

	$MID = $_SESSION[ADMIN_ID];
	
	if($mode=="edit")
	{
		$queryA = " SELECT * FROM 2011_orderProduct WHERE IDX = '$IDX' AND ORPdeleted = 0 ";
		$resultA = sql_query($queryA);
		
		$queryB = " SELECT * FROM 2011_orderPayment WHERE OIDX = '$OIDX' ";
		$resultB = sql_query($queryB);

		if(mysqli_num_rows($resultA) && mysqli_num_rows($resultB)){

			$data = sql_fetch_array($resultA);
			$row = sql_fetch_array($resultB);
			
			//-- 주문 갯수 수정
			if($kind == 1){ 
				$query3 = " UPDATE 2011_orderProduct SET ORPcountOLD = '$inPcount', ORPOLDUSER = '$MID' WHERE IDX = '$IDX' ";
			} else {
				$query3 = " UPDATE 2011_orderProduct SET ORPprice2OLD = '$inOPrice', ORPOLDUSER2 = '$MID' WHERE IDX = '$IDX' ";
			}
			$result3 = sql_query($query3);


			$dbAMTShopORI = 0;
			$query4 = " SELECT * FROM 2011_orderProduct WHERE OIDX = '$OIDX' AND ORPdeleted = 0 ";
			$result4 = sql_query($query4);
			while($rs = sql_fetch_array($result4)){
				
				if($rs["ORPOLDUSER"]){
					$dbORPcount = $rs["ORPcountOLD"];
				} else {
					$dbORPcount = $rs["ORPcount"];
				}
				if($rs["ORPOLDUSER2"]){
					$dbORPprice2OLD = $rs["ORPprice2OLD"];
				} else {
					$dbORPprice2OLD = $rs["ORPprice2"];
				}
				$dbAMTShopORI += ($dbORPcount * $dbORPprice2OLD);

			}

			/*============================================
			배송정보 불러오기
			============================================*/
			$sql5 = "select * from 2011_orderDeliveryInfo as b  where b.OIDX='" . $OIDX . "' ";
			$odResult = sql_query($sql5);
			#-- 배송정보 조합
			$totODpay=0;
			while($odRs = sql_fetch_array($odResult))
			{
				$totODpay+= $odRs["ODpay"];
			}
			if($dbAMTShopORI > 0){
				$dbAMTShopORI = $dbAMTShopORI + $totODpay;

				sql_query(" UPDATE 2011_orderPayment SET AMTOLD = '$dbAMTShopORI' WHERE OIDX = '$OIDX' ");
			}

			echo "##OK##";

		} else {
			echo "##error##";
		}
		exit;

		echo "##OK##" . $nNum . "##" . $nCount;
	}
	else
	{
		echo "##error##";
	}
?>
