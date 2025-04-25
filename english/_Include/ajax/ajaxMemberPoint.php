<?
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

$MID = $_POST["MID"];

#-- MID / mode / step / page
if($step=="view")	{
	/*====================================================================
	포인트 내역 보기
	======================================================================*/

	if($mode=="point"){
		$tStr="포인트 내역";
		$nPoint = fnGetMemberTotalPoint($MID,$shopCode);	//-- 포인트
	} else if($mode=="gift") {
		$tStr="상품권 내역";
		$nPoint = fnGetMemberTotalGift($MID,$shopCode);//-- 상품권
		$aPoint = fnGetMemberAddGift($MID,$shopCode);//--총누적
		$uPoint = fnGetMemberUseGift($MID,$shopCode);//--총사용
	}
?>

<script>
function fnPointClose() {
	nP = "<?=number_format($nPoint)?>";
	nM = "<?=$mode?>";
	
	fnChangePoint(nM,nP);
	disablePopup();
}
</script>

<table cellspacing=0 width=550 cellpadding=0>
	<tr><td align="right"><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnPointClose()' /></td></tr>
	<tr><td height="1" align="right" bgcolor="#E1E1E1"></td></tr>
	<tr>
		<Td align=center>
		<br>
			<table width="500" border="0" cellspacing="0" cellpadding="0" >
				<tr><td  class='adminEditTitle' colspan=2 align=center><b><?=$tStr?></b></td></tr>
				<tr>
					<td class='adminEditTitle'>현재 포인트</td>
					<td class='adminEditTd'><?=number_format($nPoint)?> P</td>
				</tr>
				<?if($mode=="gift") {?>
				<tr>
					<td class='adminEditTitle'>총누적적립금액</td>
					<td class='adminEditTd'><?=number_format($aPoint)?> P</td>
				</tr>
				<tr>
					<td class='adminEditTitle'>총누적사용금액</td>
					<td class='adminEditTd'><?=number_format($uPoint)?> P</td>
				</tr>
				<?}?>
			</table>
			<br><br>
			
			<table width="500" border="0" cellspacing="0" cellpadding="0" >
				<tr><td  class='adminEditTitle' colspan=2 align=center><b>내역 추가하기</b></td></tr>
				<tr>
					<td class='adminEditTitle'>포인트</td>
					<td class='adminEditTd' align=left>
						<table>
							<tr>
								<Td><input type='text' id='inPoint' style='width:50px;ime-mode:disabled;' onkeydown='onlyNum2(this.value);' onkeyup='jQuery("#pointHan").html(Number(this.value).read())' value=0> P</td>
								<td id='pointHan' style='padding-left:20px;'></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='adminEditTitle'>사유</td>
					<td class='adminEditTd' align=left><input type='text' style='width:90%;'  id='inContent'></td>
				</tr>
				<Tr>
					<td colspan=2 align=center><!-- 버튼 영역 --><span class='button medium icon' style='margin-top:3px;'><span class='add'></span><input type='button' value='저장' onclick='fnAddMemberPoint()'  /></span></td>
				</tr>
			</table>
			
			<br><br>
			
			<?
				if($mode=="point") {
					$sql = "select a.IDX,sum(a.POpoint * a.POcount) as TPoint,a.POstate,a.POregdate,a.OIDX,a.POcontent,b.Ocode from ";	
					if($shopCode)$sql.="shop_" . $shopCode . ".";
					$sql.="2011_memberPoint as a left join 2011_orderInfo as b on a.OIDX = b.IDX where a.MID='" . $MID . "' and a.POdeleted=0 AND (b.Odeleted=0 or b.Odeleted IS NULL ) group by POregdate ,POtype";
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
			<table width="500" border="0" cellspacing="0" cellpadding="0" >
				<tr><td height="2" bgcolor="#CCCCCC" colspan=6></td></tr>
				<tr><td height="5" bgcolor="#ffffff" colspan=6></td></tr>
				<tr>
					<td width="50" align="center" style='border-right:1px solid #CCCCCC;'>NO.</td>
					<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>일자</td>
					<td width="150" align="center" style='border-right:1px solid #CCCCCC;'>주문번호</td>
					<td width="150" align="center" style='border-right:1px solid #CCCCCC;'>적립금액</td>
					<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>사용금액</td>
					<td width="80" align="center" >상태</td>
				</tr>
				<tr><td height="5" bgcolor="#ffffff" colspan=6></td></tr>
				<tr><td height="2" bgcolor="#CCCCCC" colspan=6></td></tr>
				<?				
					$i = $TotalCount - $StartPos;
					while($rs = sql_fetch_array($result)){
						# DB 레코드 결과값 모두 변수로 전환
						foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
						
						$pointPlus=0;
						$pointMinus=0;
						$stStr = "";
						if($dbTPoint>=0)$pointPlus=$dbTPoint;
						else $pointMinus=$dbTPoint;
						
						if($dbPOstate==2) {
							$stStr="<font color=green>완료</font>";
						} else {
							$stStr="<font color=red>대기중</font>";
						}
						
						if($dbOIDX==0)$dbOcode = $dbPOcontent;
				?>
				<tr>
					<td height="30" align="center"><?=$i?></td>
					<td align="center"><?=date("Y-m-d",$dbPOregdate)?></td>
					<td align="center"><?=$dbOcode?></td>
					<td align="center"><?=number_format($pointPlus)?> P</td>
					<td align="center"><?=number_format($pointMinus)?> P</td>
					<td align="center" class="style16"><?=$stStr?></td>
				</tr>
				<tr><td height="2" bgcolor="#CCCCCC" colspan=6></td></tr>
				<?
						$i --;
					}
				?>
				<tr>
					<td colspan=6 align=center height=30><div align="center" id="paging"><? echo page_navAjax($TotalCount,$listSize,$PagePerList,$page,$option,"fnPageMemberPoint"); ?></div></td>
				</tr>
			</table>
			<?
				} else if($mode=="gift") {
					/*====================================================================
					상품권 내역 보기
					======================================================================*/
					#--- 상품권 구매내역 출력	(다른샵 상품권은 고려해보겠음 - 사용시 테이블 생성 필요)
					
					$sql = "select a.IDX,a.GFprice,a.GFpoint,a.GFcount,a.GFstate,a.GFregdate,b.GFdepositCheck,a.GFcontent ";
					$sql .= " from ";
					$sql.="2011_memberGift  as a left join ";
					$sql.="2011_memberGiftPayment as b on a.IDX = b.GFIDX where a.MID='" . $MID . "' and a.GFdeleted=0";
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
			<table width="500" border="0" cellspacing="0" cellpadding="0" >
				<tr><td height="2" bgcolor="#CCCCCC" colspan=7></td></tr>							
				<tr><td height="5" bgcolor="#ffffff" colspan=7></td></tr>
				<tr>
					<td width="50" align="center" style='border-right:1px solid #CCCCCC;'>NO.</td>
					<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>일자</td>
					<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>주문번호</td>
					<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>결제금액</td>
					<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>적립금액</td>
					<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>사용금액</td>
					<!--<td width="100" align="center" style='border-right:1px solid #CCCCCC;'>남은금액</td>-->
					<td width="80" align="center" >상태</td>
				</tr>
				<tr><td height="5" bgcolor="#ffffff" colspan=7></td></tr>
				<tr><td height="2" bgcolor="#CCCCCC" colspan=7></td></tr>
			<?
				while($rs = sql_fetch_array($result)){
					# DB 레코드 결과값 모두 변수로 전환
					foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}

					$GFplus=0;
					$GFMinus=0;
					$GFprice=0;
					$stStr = "";
					if($dbGFpoint>=0)$GFplus=$dbGFpoint*$dbGFcount;
					else $GFMinus=$dbGFpoint*$dbGFcount;

					if($dbGFprice)$GFprice=$dbGFprice*$dbGFcount;

					if($dbGFstate==2) {
						$stStr="<font color=green>완료</font>";
					} else if($dbGFstate==1) {
						$stStr="<font color=red>대기중</font>";
					}
					
					if($dbOIDX==0)$dbOcode = $dbGFcontent;
			?>
			<tr>
				<td width="50" height="30" align="center"><?=$dbIDX?></td>
				<td width="100" align="right"><?=date("Y-m-d",$dbGFregdate)?></td>
				<td width="100" align="right"><?=$dbOcode?></td>
				<td width="100" align="right"><?=number_Format($GFprice)?> P</td>
				<td width="100" align="right"><?=number_Format($GFplus)?> P</td>
				<td width="100" align="right"><?=number_Format($GFMinus)?> P</td>
				<!--<td width="100" align="right"><?=number_Format($dbGFwpoint)?> P</td>-->
				<td align="center" class="style16"><?=$stStr?></td>
			</tr>
			<tr><td height="2" bgcolor="#CCCCCC" colspan=7></td></tr>
			<?}?>
			<tr>
				<td colspan=7 align=center height=30><div align="center" id="paging"><? echo page_navAjax($TotalCount,$listSize,$PagePerList,$page,$option,"fnPageMemberPoint"); ?></div></td>
			</tr>
			</table>
			<?
				}//-- end if
			?>
		</td>
	</tR>
</table>
<?
#================ 내역 보기 끝 ==========================
}
else if($step=="add")
{
echo $step;

if($inPoint>0)$stateCode=1;
if($inPoint<0)$stateCode=2;

if($mode=="point")
{
#-- 포인트 내역 추가
$sql = "insert into 2011_memberPoint (MID,PIDX,OIDX,POpoint,POcount,POcontent,POtype,POstate,POregdate,POregnm) values(";
$sql.= "'" . $MID  . "',";
$sql.= "'',";
$sql.= "'',";
$sql.= "'" . $inPoint  . "',";
$sql.= "'1',";
$sql.= "'" . $inContent  . "',";
$sql.= "'" . $stateCode . "',";
$sql.= "'2',";
$sql.= "'" . date(time()) ."',";
$sql.= "'" . $_SESSION[__ADMIN_NAME___] . "')";
sql_query($sql);

echo "##addOK##";
exit();
}
else if($mode=="gift")
{
#-- 상품권 내역 추가		
$sql = "insert into 2011_memberGift  (MID,OIDX,GFpoint,GFprice,GFcount,GFcontent,GFtype,GFstate,GFregdate,GFregnm) values(";
$sql.= "'" . $MID  . "',";		
$sql.= "'',";
$sql.= "'" . $inPoint  . "',";
$sql.= "'0',";
$sql.= "'1',";		
$sql.= "'" . $inContent  . "',";
$sql.= "'" . $stateCode . "',";
$sql.= "'2',";
$sql.= "'" . date(time())  . "',";
$sql.= "'" . $_SESSION[__ADMIN_NAME___] . "')";
sql_query($sql);

echo "##addOK##";
exit();		

}

}
?>
