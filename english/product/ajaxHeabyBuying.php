<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($step==1){
		#-- 폼 출력
		$sql = "select a.*,c.MKkname,c.MKename from 2011_productInfo as a left join 2011_brandInfo as b on a.BRIDX = b.IDX left join 2011_makerInfo as c on b.MKIDX = c.IDX where a.IDX='" . $qIDX . "'";
		$rs = sql_fetch($sql);

		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
		
?>
<style>
.p_b { border-bottom:1px #f0f0f0 solid; }
.p_td { padding: 8px 0px 8px 20px;}
.p_td2 { padding: 10px 0px 10px 20px;}
.style1 { font-family: "Verdana"; font-size: 13px; color: #444444; font-weight: bold; }
.style2 { font-family: "Verdana"; font-size: 13px; color: #444444; }
.style3 { font-size: 12px; color: #444444; font-family: "Verdana"; }
.style5 { font-size: 11px }
.style6 { font-size: 14px; color: #FF0000; }
.mytable { width:400px; border-collapse:collapse; }  
.mytable th, .mytable td { border:1px solid #e5e5e5; }
.style7 { height:27px; width:150px; background-color:#f7f7f7;  color:#666666; font-family:verdana; font-size:11px; font-weight:bold; }
</style>

<div style="margin:-15px; padding:0; width:460px; background-color:#f0f0f0" >
	
<table width="460" cellpadding="0" cellspacing="0" border="0" align="center" style="background-color:#f0f0f0">
	<tr><td align="right" style="padding-top:20px; padding-right:20px;"><img src="/img/btn_close2.png" style='cursor:pointer' onclick='fnClosePopup()' ></td></tr>
	<tr><td class="p_b" align="center"><img src="/img/title_oneonone.png"></td></tr>
	<tr><td style="padding-top:7px;" align="center">
	
		<table width="420" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; border:1px #e4e4e4 solid; background-color:#ffffff;">
			<tr>
				<td align="center" style="padding:20px 0px;">
					
					<table width="370" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td style="height:27px; border-bottom:1px solid #d3d3d3; color:#666666; font-family:verdana; font-size:11px;">&nbsp;<b>Manufacturer</b> : <?=$dbMKename?></td>
						</tr>
						<tr>
							<td style="height:27px; border-bottom:1px solid #d3d3d3; color:#666666; font-family:verdana; font-size:11px;">&nbsp;<b>Item</b> : <?=$dbPengName?></td>
						</tr>
						<tr>
							<td style="height:27px; border-bottom:1px solid #d3d3d3; color:#666666; font-family:verdana; font-size:11px;">&nbsp;<b>Item No</b> : <?=$dbIDX?></td>
						</tr>
					</table>
				
				</td>
			</tr>
			<tr>
				<td align="center">					
					<table class="mytable">
						<tr>
							<td class="style7">&nbsp;&nbsp;Wish discount rate <font color=red>*</font></td>
							<td style="padding-left:10px;"><input name='inHBper' style="height:21px;width:20px; border:1px solid #d2d2d2" id='inHBper' value=""> %</td>
						</tr>
						<tr>
							<td class="style7">&nbsp;&nbsp;Name <font color=red>*</font></td>
							<td style="padding-left:10px;"><input name="inHBname" type="text" id="inHBname" size="30" style="height:21px; border:1px solid #d2d2d2" value='<?=$Mname?>' /></td>
						</tr>
						<tr>
							<td class="style7">&nbsp;&nbsp;Phone Number <font color=red>*</font></td>
							<td style="padding-left:10px;"><input name="inHBtel" type="text" id="inHBtel" size="30" style="height:21px; border:1px solid #d2d2d2" onkeydown='onlyNum()' value='<?=$MHP?>' /></td>
						</tr>
						<tr>
							<td class="style7">&nbsp;&nbsp;E-mail <font color=red>*</font></td>
							<td style="padding-left:10px;"><input name="inHBemail" type="text" id="inHBemail" size="30" style="height:21px; border:1px solid #d2d2d2" value='<?=$Memail?>'/></td>
						</tr>
						<tr>
							<td class="style7">&nbsp;&nbsp;Comment</font></td>
							<td style="padding-left:10px;"><textarea rows="8" name="inHBmemo" id="inHBmemo" style="width:90%; border:1px solid #d2d2d2"></textarea><br/><span style="font-weight:normal; color:#0095d3; font-size:11px;">Please enter Item’s Option and Wish Q’ty</span></td>
						</tr>
					</table>
					<br/>
					<br/>
				</td>
			</tr>
		</table>

		</td>
	</tr>
	<tr>
		<td style="padding:30px 0px;" align="center"><span style="padding-right:5px;"><img src="/img/btn_apply2.png" border="0"  style='cursor:pointer' onclick='fnHeavyBuying("2")'/></span><span><img src="/img/btn_close3.png" border="0"  style='cursor:pointer' onclick='fnClosePopup()'/></span></td>
	</tr>
</table>

</div>
<?
	}else if($step==2){
  	
		#-- 저장처리
		$sql = "insert into 2011_heavyBuying (PIDX,HBMID,HBname,HBtel,HBemail,HBmemo,HBper,HBregdate,HBstate,HBdeleted) values(";
		$sql.= "'" . $qIDX . "',";
		$sql.= "'" . $MID . "',";
		$sql.= "'" . $inHBname . "',";
		$sql.= "'" . $inHBtel . "',";
		$sql.= "'" . $inHBemail . "',";
		$sql.= "'" . $inHBmemo . "',";
		$sql.= "'" . $inHBper . "',";
		$sql.= "'" . date(time()) . "',";
		$sql.= "'1',";
		$sql.= "'0')";
		
		sql_query($sql);
		echo "##OK##";
  	
	}
?>