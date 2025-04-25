<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	echo "<a href='javascript:fnTaxView(1);'>---- 새로고침 $Tmode -----------</a>";
	
	
	if($Tmode=="list")
	{
		#== 목록 출력
		
		/*
		MID
		Tmonth
		page
		Ttype
		*/
	
		$oTime1 = mktime(0,0,0,$Tmonth,1,$Tyear);
		$oTime2 = mktime(0,0,0,$Tmonth+1,1,$Tyear);
		
		$sql = "select sum(AMT) as AMT,sum(OusePoint) as OusePoint,a.OpayType,a.IDX,a.OIDX,b.Ocode,b.Oregdate,d.Mname,d.McomName,c.ODdate from 2011_orderPayment as a left join 2011_orderInfo as b on a.OIDX = b.IDX left join 2011_orderDeliveryInfo as c on b.IDX = c.OIDX left join 2011_memberInfo as d on b.MID = d.MID where b.Odeleted=0 and b.MID='" . $TMID . "' and ODstate=4 ";
		
		if($Ttype!=4) $where = " and a.OpayType='" . $Ttype . "'";
		else $where = " and a.OusePoint>0";
		
		$sql = $sql . $where . " group by a.OIDX ";
		if (!$page) $page = 1;
		if (!$listSize) $listSize = 10;
		$CntPerPage = $listSize;
		$PagePerList = 5;
		$StartPos = ($page - 1) * $CntPerPage;
		$sql .= " ORDER BY a.IDX DESC";
		$result = sql_query($sql);
		$TotalCount = mysqli_num_rows($result);
		$sql .= " LIMIT ".($page - 1) * $CntPerPage.",".$CntPerPage;
		$result = sql_query($sql);
	
	?>
	
			
			<table class='tbl' width='600' border=0>
	
				<tr>
			    <td align="right" colspan=8><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
			  </tr>
			  <tr>
			    <td height="1" align="right" bgcolor="#E1E1E1" colspan=8></td>
			  </tr>
	
				<tr>
				<td style='padding-top:10px;height:30px' colspan=8>
					<b>주/문/리/스/트</b>
				</td>
			</tr>
	      <tr>
	        <td height="2" bgcolor="#CCCCCC"  colspan=8></td>
	      </tr>
	      <tr>
	        <td width="80" align="center" style='height:30px;'>번호</td>
	        <td width="120" align="center" style='height:30px;'>주문번호</td>
	        <td width="60" align="center"  style='height:30px;'>업체명</td>
	        <td width="60" align="center" style='height:30px;'>결제금액</td>
	        <td width="60" align="center" style='height:30px;'>사용포인트</td>
	        <td width="60" align="center" style='height:30px;'>결제방법</td>
	        <td width="60" align="center" style='height:30px;'>주문일자</td>
	        <td width="60" align="center" style='height:30px;'>배송일자</td>
	      </tr>
	
	      <tr>
	        <td height="2" bgcolor="#CCCCCC"  colspan=8></td>
	      </tr>
	
					<?
	         	while($rs = sql_fetch_array($result))
	   					{
	  						# DB 레코드 결과값 모두 변수로 전환
							foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}

							if($dbOregdate)$dbOregdate=date("Y-m-d",$dbOregdate);
							else $dbOregdate="-";

							if($dbODdate)$dbODdate=date("Y-m-d",$dbODdate);
							else $dbODdate="-";
							
							
							if($dbOpayType==1)$typeStr="무통장";
							else if($dbOpayType==2)$typeStr="카드";
							else if($dbOpayType==3)$typeStr="에스크로";

						?>
	
			      <tr>
			          <td  align="center" valign="middle" style='height:30px;'><?=$dbIDX?></td>
			          <td  align="center" valign="middle" style='height:30px;'><a href='javascript:fnOpenOrder("<?=$dbOIDX?>")'><?=$dbOcode?></a></td>
			          <td  align="center" valign="middle" style='height:30px;'><?=$dbMcomName?></td>
			          <td  align="center" valign="middle" style='height:30px;' align=right><?=number_format($dbAMT)?></td>
			          <td  align="center" valign="middle" style='height:30px;' align=right><?=number_format($dbOusePoint)?></td>
			          <td  align="center" valign="middle" style='height:30px;'><?=$typeStr?></td>
			          <td  align="center" valign="middle" style='height:30px;'><?=$dbOregdate?></td>
			          <td  align="center" valign="middle" style='height:30px;'><?=$dbODdate?></td>
			      </tr>
			      <tr>
			        <td height="1" bgcolor="#CCCCCC" colspan=8></td>
			      </tr>	
	    	<?}?>
	
	      <tr>
	        <td height="25" valign="middle" colspan=8 align=center><div align="center" id="paging"><? echo page_navAjax($TotalCount,$listSize,$PagePerList,$page,$option,"fnTaxView"); ?></div></td>
	      </tr>
		</table>
	<?}
	else if($Tmode=="chMode")
	{
		#-- 발급체크 변경
		$sql = "update ";
	}
	
	?>

