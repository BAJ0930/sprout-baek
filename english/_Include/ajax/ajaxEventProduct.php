<?
include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
ob_clean();

if($mode=="search") {
	
	if(!$s1) {
?>
		<table cellspacing=0 cellpadding=0 width=100%>
			<tr><td height=30 align=center>- 검색어를 입력해 주세요. -</td></tr>
		</table>
<?
		exit();
	} else {
		
		$s1 = iconv("euc-kr","utf-8",$s1);
		$sql = "select a.*,b.EVname from 2011_productInfo as a left join 2011_eventInfo as b on a.EVIDX=b.IDX  where Pshop = '1000u' AND Pname like '%" . $s1 . "%' AND Pdeleted=0 AND Pprice3>0 AND Pstate<10 AND Pagree=1";
		
		$option = "s1=".$s1;
		if (!$page) $page = 1;
		if (!$listSize) $listSize = 20;
		$CntPerPage = $listSize;
		$PagePerList = 5;
		$StartPos = ($page - 1) * $CntPerPage;
		$sql .= " ORDER BY Pname DESC";
		$result = sql_query($sql);
		$TotalCount = mysqli_num_rows($result);
		$sql .= " LIMIT ".($page - 1) * $CntPerPage.",".$CntPerPage;
		$result = sql_query($sql);
		
		if(!$TotalCount) {
?>
			<table cellspacing=0 cellpadding=0 width=100%>
				<tr><td height=30 align=center>- 검색된 상품이 없습니다. -</td></tr>
			</table>
<?
			exit();
		}
		
		$cols=5;
		$rows=$listSize/$cols;
	}
?>

<input type='hidden'  id='ajaxPath' name='ajaxPath' value='<?=$newpath?>'>

<table cellspacing=0 cellpadding=0 >
	<tr><td height=30 align=center  bgcolor='#efefef'><b>검색결과 : <?=$TotalCount?> 개</b></td></tr>
	<tr>
		<td align="left" >
			<table width='97%' class='input2'>
				<?
					for($k=1;$k<=$rows;$k++) {
						if($rowOut==1) break;
				?>
				<tr>
				<?
					for($i=1;$i<=$cols;$i++) {
						if($rs = sql_fetch_array($result)) {
							# DB 레코드 결과값 모두 변수로 전환
							foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
							
							$dbPprice2=$dbPprice2;
							$pImg = "<img src='/_DATA/product/" . $dbPsaveFile1 . "' width='80' height='80'  border=0 />";
						} else {
							//-- 레코드 없음
							$dbIDX = "";
							$dbPname="";
							$dbPprice2="";
							$pImg = "";
							
							//-- 줄 끝내기 표시
							$rowOut=1;
						}
						//-- 검색어 색상 변환
						if($s1)$dbPname = str_replace($s1,"<font color=darkorange><b>" . $s1 . "</b></font>",$dbPname);
				?>
					<td valign=top align=center width=150>
					
						<table  border="0" width='<?=$tdWidth?>' height=150 cellpadding="0" cellspacing="0" class="input2">
							<tr><td align="center" valign="middle" ><?=$link?><div id='pIMG'  IDX='<?=$dbIDX?>' Stock='<?=$dbPstockCount?>'><?=$pImg?></div></a></td></tr>
							<tr><td align=center><?=@number_format($dbPprice2)?> 원</td></tr>
							<tr><td align="center" height=20><?=$link . $dbPname?><?if($dbEVname){?><br><font color=blue>설정됨 : [<?=$dbEVname?>]</font><?}?></td></tr>
							<?if($dbIDX){?>
							<tr>
								<td align="center" height=20><input type="checkbox" name="inPcheck" id="inPcheck" value='<?=$dbIDX?>' <?if(!$dbPstockCount)//echo "disabled";?> /><?if(!$dbPstockCount)echo "<br><font color=red>재고없음</font>";?></td>
							</tr>
							<input type='hidden' name='inMaxStock' id='inMaxStock' value='<?=$dbPstockCount?>'>
							<?}?>
							<tr><td height="5" align="center"></td></tr>
						</table>
					</td>
					<?if($i!=$cols){?><td valign="bottom" ><img src="/image_1/dot_02.gif" width="1" height="76" /></td><?}?>
					<?}//-- colsEnd?>
				</tr>
				<?if($k!=$rows){?>
				<tr><td height="10"></td></tr><?}?>
				<?}//-- rows End?>
				<tr><td height="30" align=center colspan=10><div align="center" id="paging"><p>&nbsp;</p><? echo page_navAjax($TotalCount,$listSize,$PagePerList,$page,$option,"fnLoadProduct"); ?><p>&nbsp;</p><p>&nbsp;</p></div></td></tr>
			</table>
			<?
				} else if($mode=="list") {
					
					$sql = "select * from 2011_productInfo where EVIDX='" . $EVIDX . "'";
					if (!$page) $page = 1;
					if (!$listSize) $listSize = 20;
					$CntPerPage = $listSize;
					$PagePerList = 5;
					$StartPos = ($page - 1) * $CntPerPage;
					$sql .= " ORDER BY Pname DESC";
					$result = sql_query($sql);
					$TotalCount = mysqli_num_rows($result);
					$sql .= " LIMIT ".($page - 1) * $CntPerPage.",".$CntPerPage;
					$result = sql_query($sql);
				
					if(!$TotalCount) {
			?>
			<table cellspacing=0 cellpadding=0 width=100%><tr><td height=30 align=center>- 검색된 상품이 없습니다. -</td></tr></table>
			<?
					exit();
				}
				
				$cols=5;
				$rows=$listSize/$cols;
			?>
			<input type='hidden'  id='ajaxPath' name='ajaxPath' value='<?=$newpath?>'>
			
			<table cellspacing=0 cellpadding=0 >
				<tr><td height=30 align=center bgcolor='#efefef'><b>등록상품 : 총 <?=$TotalCount?> 개</b></td></tr>
				<tr>
					<td align="left">
						<table width='97%' class='input2'>
							<?
								for($k=1;$k<=$rows;$k++) {
									if($rowOut==1)break;
							?>
							<tr>
							<?for($i=1;$i<=$cols;$i++)
{
if($rs = sql_fetch_array($result))
{
# DB 레코드 결과값 모두 변수로 전환
foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}

$dbPprice2=$dbPprice2;
$pImg = "<img src='/_DATA/product/" . $dbPsaveFile1 . "' width='80' height='80'  border=0 />";
}
else
{
//-- 레코드 없음
$dbIDX = "";
$dbPname="";
$dbPprice2="";
$pImg = "";

//-- 줄 끝내기 표시
$rowOut=1;
}
?>
<td valign=top align=center width=150 >

<table  border="0" width='<?=$tdWidth?>' height=150 cellpadding="0" cellspacing="0" class="input2">
<tr>
<td align="center" valign="middle" ><?=$link?><?=$pImg?></a>
<?if($dbIDX){?><br><font style='font-size:10px;'><?=$dbEVdiscount?>% ↓</font><?}?>
</td>
</tr>
<tr>
<td align=center>
<?=@number_format($dbPprice2)?> 원
</td>
</tr>
<tr>
<td align="center" height=20><?=$link . $dbPname?></td>
</tr>
<?if($dbIDX){?>
<tr><td align="center" height=20>
<a href='javascript:fnLoadProduct2("","delete","&inIDX=<?=$dbIDX?>")'><img src='../image/icon_delete.gif' align=absmiddle > 삭제하기</a>

</td></tr>
<input type='hidden' name='inMaxStock' id='inMaxStock' value='<?=$dbPstockCount?>'>
<?}?>
<tr><td height="5" align="center"></td></tr>
</table>
</td>
<?if($i!=$cols){?><td valign="bottom" ><img src="/image_1/dot_02.gif" width="1" height="76" /></td><?}?>
<?}//-- colsEnd?>
</tr>
<?if($k!=$rows){?><tr><td height="10"></td></tr><?}?>
<?}//-- rows End?>
<tr><td height="30" align=center colspan=10><div align="center" id="paging"><p>&nbsp;</p><? echo page_navAjax($TotalCount,$listSize,$PagePerList,$page,$option,"fnLoadProduct2"); ?><p>&nbsp;</p><p>&nbsp;</p></div></td></tr>
</table>

<?	


}
else if($mode=="save")
{

echo $inIDX;

$IDX = explode(",",$inIDX);

for($k=0;$k<sizeof($IDX);$k++)
{
if($IDX[$k])
{
$sql = "update 2011_productInfo set EVIDX='" . $EVIDX . "',EVdiscount='" . $EVdiscount . "' where IDX='" . $IDX[$k] . "';";
sql_query($sql);
}
}

echo "##saveOK##";

}
else if($mode=="delete")
{
$sql = "update 2011_productInfo set EVIDX='0',EVdiscount='0' where IDX='" . $inIDX . "'";
sql_query($sql);

echo "##deleteOK##";
}
?>