<?
	$isAdminMode = $_POST["isAdminMode"];	
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";

	if($act == "ok"){
		if($modIDX == "1"){

			for($i = 0; $i < count($inACAIDX); $i ++){
				$pp = str_replace(",","",$inACAreplyPrice[$i]);
				sql_query( " UPDATE 2011_adminCart SET ACAreplyPrice = '$pp', ACAEndCount = '$inACAcountEnd[$i]' WHERE IDX = '$inACAIDX[$i]' ");
			}

			echo "
				<script>
					alert('수정되었습니다.');
					opener.location.reload();
					self.close();
				</script>
			";

		} else {
			echo " <script> self.close(); </script> ";
		}
		exit;
	}

	if($qGRP && $qMKIDX) {
		$sql = "select a.*,b.Pname,b.Pprice2,b.Pprice1,b.Pcategory1,d.MKkname,";
		$sql.= "d.MKperson1,d.MKpersonLevel1,d.MKemail1,d.MKhp1,d.MKtel1,d.MKfax1,MKpersonCheck1,";	//-- 담당자1
		$sql.= "d.MKperson2,d.MKpersonLevel2,d.MKemail2,d.MKhp2,d.MKtel2,d.MKfax2,MKpersonCheck2,";	//-- 담당자2
		$sql.= "d.MKperson3,d.MKpersonLevel3,d.MKemail3,d.MKhp3,d.MKtel3,d.MKfax3,MKpersonCheck3,";	//-- 담당자3
		$sql.="f.Mname,e.OPbarcode,b.Pbarcode,d.MKbuyMargin,";
		$sql.= "e.OPvalue from 2011_adminCart as a ";
		$sql.= "left join 2011_productInfo as b on a.ACAPIDX = b.IDX ";
		$sql.= "left join 2011_brandInfo as c on b.BRIDX = c.IDX ";
		$sql.= "left join 2011_makerInfo as d on b.MKIDX = d.IDX ";
		$sql.= "left join 2011_productOption as e on a.ACAoption = e.IDX ";
		$sql.= "left join 2011_memberInfo as f on a.ACAMID = f.MID ";
		$sql.= "where a.ACAgroup='" . $qGRP . "' and d.IDX='" . $qMKIDX . "' ";
		$result = sql_query($sql);		
		if(!mysqli_num_rows($result))
		{
			echo "
				<script> 
					alert('발주서에 담겨진 제품이 없습니다.');
					self.close();
				</script>
			";			
			exit();
		}

	} else {
		##--- 처리 없음 -----
		echo "<script>alert('잘못 된 정보입니다.');self.close();</script>";
		exit();
	}
	
$day21 =date("Y-m-d",strtotime("-30 day"));
$day22 =date("Y-m-d");

?>	
<title>::: 주문서 수정 :::</title>
<style>
	.opTitle { background-color:#efefef;font-weight:bold;color:black;padding:5px;text-align:center; }
	.opTD { padding:3px; }
</style>
<script>
	function commify(n) {
	  var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
	  n += '';                          // 숫자를 문자열로 변환

	  while (reg.test(n))
		n = n.replace(reg, '$1' + ',' + '$2');

	  return n;
	}

	function fnSetPriceEnd()
	{
		countObj = $("INPUT[id='inACAcountEnd']");
		priceObj = $("INPUT[id='inACAreplyPrice']");
		priceTD = $("TD[id='TDprice1']");
		
		len = countObj.length;
		
		totPrice=0;		
		totCnt=0;

		for(k=0;k<len;k++)
		{
			cnt = countObj.eq(k);
			pr = priceObj.eq(k);
			price = cnt.val() * pr.val().replace(',','');
			price = Math.round(price);
			
			totPrice += price;
			totCnt = parseInt(totCnt) + parseInt(cnt.val());
			
			//-- TD 객체에 할당
			priceTD.eq(k).html(commify(price));
			priceObj.eq(k).val(commify(pr.val()));
		}
		$("#TDtotPrice2").eq(0).html(commify(totPrice));
		$("#TDtotSu2").eq(0).html(totCnt);
		$("#modIDX").val("1");		
	}

	function fnSave(){
		document.xform.submit();
	}

	
	function fnSendAdd(MKIDX,ACAgroup)
	{
		if(!confirm('상품을 추가하시겠습니까?\n\n저장을 먼저 하신 후 상품 추가하세요.'))return;	
		var openNewWindow = window.open("about:blank");
		openNewWindow.location.href = "/_Admin/Product/productOrderList.php?page=1&s1=a.Pname&s2=&s4=&s8=&s9=&s10=" + MKIDX + "&s12=<?=$day21?>&s13=<?=$day22?>&s6=on&s5=" + ACAgroup;
	}
	
</script>
<center>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<Tr><td align="center"><br><font style='font-weight:bold;font-size:17px;'>주문서 수정</font><br><br></td></tr>
</table>
<table class='tbl' width="95%" style='border:2px solid black' border="1" bordercolor="black">
<form name="xform" method="post" action="<?=$PHP_SELF?>">
<input type="hidden" name="modIDX" id="modIDX" value="0">
<input type="hidden" name="act" value="ok">
	<tr>
		<td class="opTitle" style="width:300px;">품 명</td>
		<td class="opTitle">주문<br>수량</td>
		<td class="opTitle">실출고<br>수량</td>	
		<td class="opTitle">실입고<br>수량</td>
		<td class="opTitle">소비자가</td>
		<td class="opTitle">주문단가</td>
		<td class="opTitle">주문단가<br>합계</td>
		<td class="opTitle">입고단가<br>합계</td>
	</tr>
	<?
		while($rs = sql_fetch_array($result)){
			foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
			
			$opStr="";
			if($dbOPvalue)$opStr=" (" . $dbOPvalue . ")";

			
			if ( strpos($dbACAreplyPrice,'.')!==false ) { 
				$dbACAreplyPrice = $dbACAreplyPrice;
			} else { 
				$dbACAreplyPrice = number_format($dbACAreplyPrice);
			}
	?>
	<input type='hidden' name='inACAIDX[]' id='inACAIDX' value='<?=$dbIDX?>'>
	<tr>
		<td class="opTD"><?=$dbPname?><?=$opStr?></td>
		<td class="opTD" align="center"><?=$dbACAcount?></td>
		<td class="opTD" align="center"><?=$fFont.$dbACAreplyCount?></td>
		<td class="opTD" align="center"><input type='text' name='inACAcountEnd[]' id='inACAcountEnd' value='<?=$dbACAEndCount?>' onkeydown='onlyNum();' style='text-align:center;width:50px;border:1px solid gray;ime-mode:disabled;' maxlength=4 onblur='if(this.value=="")this.value=0;fnSetPriceEnd();' ></td>
		<td class="opTD" align="center"><?=number_format($dbPprice2)?></td>
		<td class="opTD" align="center"><input type="text" name="inACAreplyPrice[]" id="inACAreplyPrice" value="<?=$dbACAreplyPrice?>" size="10" style="text-align:right"  onblur='if(this.value=="")this.value=0;fnSetPriceEnd();'></td>
		<?		
			$dbACAreplyPrice = str_replace(",","",$dbACAreplyPrice);
		?>
		<td class="opTD" align="center"><?=number_format($dbACAreplyPrice * $dbACAcount)?></td>
		<td class="opTD" align="center" id='TDprice1'><?=number_format($dbACAreplyPrice * $dbACAEndCount)?></td>
	</tr>
	<?
			$totPrice+=$dbACAreplyPrice * $dbACAcount;
			$totSu1 += $dbACAcount;
			$totSu2 += $dbACAEndCount;
			$totPrice2+=$dbACAreplyPrice * $dbACAEndCount;
		}
	?>
	<tr>
		<td  colspan="8" style='border-top:3px double black;font-weight:bold; padding:5px;' align="center">
			총주문수량 : <span id='TDtotSu1'><?=$totSu1?></span>개&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			총입고수량 : <span id='TDtotSu2' style="color:red;"><?=$totSu2?></span>개&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			주문단가 총합계 : <span id='TDtotPrice'><?=number_format($totPrice)?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			입고단가 총합계 : <span id='TDtotPrice2' style="color:red;"><?=number_format($totPrice2)?></span>
		</td>
	</tr>
</form>
</table>

<center>
<br/>
<span class='button medium icon'><span class='add'></span><input type='button' value='상품 추가하기' onclick='fnSendAdd(<?=$dbACAMKIDX?>,<?=$dbACAgroup?>)'/></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span class='button medium icon'><span class='check'></span><input type='button' value='저장 및 닫기' onclick='fnSave()'  /></span>
<br/><br/>
</center>

<script>
	window.focus();
</script>

</body>
</html>