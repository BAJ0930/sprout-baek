<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($OPIDX)
	{
		#-- 재고 확인 쿼리
		$sql = "select * from 2011_productInfo as a left join 2011_productOption as b on a.IDX = b.PIDX  where a.IDX='" . $PIDX . "' and b.IDX='" . $OPIDX . "'";		
		$result = sql_query($sql);
		$rs = sql_fetch_array($result);
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
		$nStock = $dbOPstock;
		
		#-- 발송대기 갯수 확인
		$sql = "select sum(ORPcount) as ORPready from 2011_orderProduct where ORPdeleted=0 and ORPcountCheck=1 and ORPoption='" . $OPIDX . "'";

		$sql2 = " SELECT a.*,b.ORPcount FROM 2011_orderInfo AS a ";
		$sql2 .= " LEFT JOIN 2011_orderProduct AS b ON a.IDX = b.OIDX ";
		$sql2 .= " WHERE  ORPdeleted=0 and ORPcountCheck=1 and ORPoption='" . $OPIDX . "'";
	}
	else
	{
		#-- 재고 확인 쿼리
		$sql = "select * from 2011_productInfo  where IDX='" . $PIDX . "'";
		$result = sql_query($sql);
		$rs = sql_fetch_array($result);
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
		$nStock = $dbPstockCount;
		
		#-- 발송대기 갯수 확인
		$sql = "select sum(ORPcount) as ORPready from 2011_orderProduct where ORPdeleted=0 and ORPcountCheck=1 and PIDX='" . $PIDX . "'";			
		
		$sql2 = " SELECT a.*,b.ORPcount FROM 2011_orderInfo AS a ";
		$sql2 .= " LEFT JOIN 2011_orderProduct AS b ON a.IDX = b.OIDX ";
		$sql2 .= " WHERE ORPdeleted=0 and ORPcountCheck=1 and PIDX='" . $PIDX . "'";
	}

	$result = sql_query($sql);
	$rs = sql_fetch_array($result);
	# DB 레코드 결과값 모두 변수로 전환
	foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
	$nReady = $dbORPready;
	if(!$nReady)$nReady=0;
		
	$STresult = sql_query($sql2);
?>

<table width="600" border="0" cellspacing="0" cellpadding="0">
	<tr><td align="right"><!--<img src="../image_1/v_11.jpg" width="15" height="15" vspace="5" />--><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td></tr>
	<tr><td height="1" align="right" bgcolor="#E1E1E1"></td></tr>
	<tr><td align="center"></td></tr>
	<tr>
		<td align="center" valign="top">
		
			<table width='100%' cellspacing=0 cellpadding=0>
				<tr>
					<td height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2>[ 발송 대기 - 주문 업체 목록 ]</td></tr><tr>
					<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>상품명</td>
					<td style='border-bottom:1px solid #efefef;'><?=$dbPname?></td>
				</tr>
				<?if($OPIDX){?>
				<tr>
					<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>옵션명</td>
					<td style='border-bottom:1px solid #efefef;'><?=$dbOPvalue?></td>
				</tr>
				<?}?>
				<tr>
					<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>총재고</td>
					<td style='border-bottom:1px solid #efefef;'><?=$nStock?> 개</td>
				</tr>
				<tr>
					<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>발송대기</td>
					<td style='border-bottom:1px solid #efefef;'><?=$nReady?> 개</td>
				</tr>
				<tr>
					<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>현재고</td>
					<td style='border-bottom:1px solid #efefef;' id='totStock'><?=$nStock - $nReady?> 개</td>
				</tr>
				<tr>
					<td  height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2 >
					
						<table width='100%' style='width:100%;'>
							<tr>
								<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>주문일시</td>
								<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>주문번호</td>
								<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>옵션명</td>
								<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>주문자</td>
								<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>발송대기 수량</td>
							</tr>
							<?
								$rCnt = mysqli_num_rows($STresult);
								while($STrs=sql_fetch_array($STresult)){
									foreach ($STrs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
									$nStock = $dbPstockCount;
									
									if($dbOPvalue=="")$dbOPvalue="-";
									$Olink="";
									
									if($dbMID){
										$query = "SELECT McomName FROM 2011_memberInfo WHERE MID = '$dbMID'";
										$data = sql_fetch($query);
										
										if($data[McomName]){
											$order = $data[McomName];
										} else {
											$order = $dbOorderName;
											if(!$order)$order = $dbOname;
										}
									} else {
										$order = $dbOorderName;
										if(!$order)$order = $dbOname;
									}
									
									$Olink = "<a href='javascript:fnOpenOrder(" . $dbIDX . ")'>";
							?>
							<tr>
								<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=date("Y-m-d H:i:s",$dbOregdate)?></td>
								<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$Olink . $dbIDX?></a></td>
								<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$dbOPvalue?></td>
								<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$order?></td>
								<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$dbORPcount?></td>
							</tr>
							<? } ?>
						</table>
						
					</td>
				</tr>
			</table>
			
		</td>
	</tr>
	<tr><td height="90" align="center"><img src="/image_3/p_bt16.jpg" width="128" height="38" style='cursor:pointer;' onclick='fnClosePopup()' /></td></tr>
</table>