<?
	$isAdminMode = @$_POST["isAdminMode"];	
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($stepCode=="list")
	{		
		#-- 사용자는 무조건 사용자 확인 메모만 볼수 있음
		if(!$isAdminMode)$OMtype=1;
				
		#-- Memo 불러오기		
		/*
		$sql = "select a.*,b.Mname from 2011_orderMemo as a left join 2011_memberInfo as b on a.OMMID = b.MID where OIDX='" . $OIDX . "' and OMtype='" . $OMtype . "'  ";
		$pg = new Paging();
		if($listSize)$pg->set_page_size($listSize);
		$order = " OMregdate desc ";
		
		if(!$page)$page=1;
		$pg->set_page($sql,$where,$order,$page,$group);
		
		$pg->set_icon("<img src='/image_2/icon_11.gif' border=0>","<img src='/image_2/icon_12.gif' border=0>");
		*/
		
		$sql = "select a.*,b.Mname from 2011_orderMemo as a left join 2011_memberInfo as b on a.OMMID = b.MID where OIDX='" . $OIDX . "' and OMtype='" . $OMtype . "' order by OMregdate desc  ";
		$result = sql_query($sql);
		
		?>		
		<style>
			.orderMemoHeader { text-align:center; height:25px; font-weight:bold; border-bottom:1px solid #afafaf; }
			.orderMemoList { height:25px; border-bottom:1px solid #efefef; }			
		</style>		
		<?if($isAdminMode){?>
		<script>
			function fnSaveMemo() {
				var txt = jQuery("#inOMtxt").val();
				if(!txt) {
					alert("내용을 입력해 주세요.");
					jQuery("#inOMtxt").focus();
					return;
				}				
				var midx = jQuery("#MOIDX").val();
				
				txt = txt.replaceAll("'","");
				txt = txt.replaceAll('"',"");
				
				var param = "stepCode=save&OMtype=<?=$OMtype?>&isAdminMode=1&OIDX=<?=$OIDX?>&MOIDX=" + midx + "&content=" + txt;
				
				$.ajax({
					url:'/_Include/ajax/ajaxOrderMemo.php',
					type:"POST",
					data : param,
					dataType:"text",
					error:fnErrorAjax,
					success:function(_response){
						v = _response.split("##");
						if(v[1]=="OK") {
							fnOpenMemo(v[2],v[3],1,'<?=$isAdminMode?>');
						}
					}
				});
			}
			
			function fnEditMemo(idx) {
				jQuery("#MOIDX").val(idx);
				jQuery("#inOMtxt").val(jQuery("#txt"+idx).html());
				jQuery("#canBtn").css("visibility","visible");
			}
			
			function fnEditMemoCancel() {
				jQuery("#inOMtxt").val('');
				jQuery("#MOIDX").val('');
				jQuery("#canBtn").css("visibility","hidden");
			}
			
			function fnDeleteMemo(idx)
			{				
				if(!confirm('삭제하시겠습니까?')) { return; }
				
				var param = "stepCode=delete&OMtype=<?=$OMtype?>&OIDX=<?=$OIDX?>&MOIDX=" + idx;				
				$.ajax({
					url:'/_Include/ajax/ajaxOrderMemo.php',
	 				type:"POST",
					data : param,
					dataType:"text",
					error:fnErrorAjax,
					success:function(_response){
						v = _response.split("##");
						if(v[1]=="OK") {
							fnOpenMemo(v[2],v[3],1,'<?=$isAdminMode?>');
						} else {
							alert(_response);
						}
					}
				});
			}			
		</script>
		<?}?>

		<table width="600" border="0" cellspacing="0" cellpadding="0">
			<tr><td align="right"><!--<img src="../image_1/v_11.jpg" width="15" height="15" vspace="5" />--><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td></tr> 
			<?if($isAdminMode){?>
			<Tr>
				<td align="center">
				
					<table width='100%' class='tbl' style='border:1px solid #afafaf;background-color:#efefef;'>
						<tr>
							<td onclick='fnOpenMemo(<?=$OIDX?>,1,1,"<?=$isAdminMode?>");' style='border-right:1px solid #afafaf;cursor:pointer;'><input type='radio' name='mType' id='mType1' value='1' <?if($OMtype==1)echo "checked";?>> 고객 안내 메모</td>
							<td onclick='fnOpenMemo(<?=$OIDX?>,2,1,"<?=$isAdminMode?>");' style='border-right:1px solid #afafaf;cursor:pointer;'><input type='radio' name='mType' id='mType2' value='2' <?if($OMtype==2)echo "checked";?>> 관리자 참고 메모</td>
						</tr>
					</table>
				
				</td>
			</tr>
			<?}?>
			<Tr>
				<td align="center">
					<br><font color=black style='font-weight:bold;font-size:17px;'>[ 
					<?
						if($OMtype==1)echo "고객 안내 메모";
						else echo "관리자 참고 메모";
					?>
					]</font>
				</td>
			</tr>
			<?if($isAdminMode){?>
			<tr>
				<td align=center>
				
					<script>
						function goMemo(str){
							var memo = "";
							if(str == 1){
								memo = "죄송합니다. 아래 상품은 재고 부족으로 인해 미출고 되었습니다. 미출고 금액은 포인트 처리 해드리겠습니다.";
							} else if(str == 2){
								memo = "기본 배송비 박스단위는 1박스입니다. 박스수량이 늘어날 경우 추가배송비가 발생됩니다.";
							} else if(str == 3){
								memo = "기본 배송비 박스단위는 1박스입니다. 박스수량 3박스로 인해 추가배송비 15,000원이 발생되어 포인트로 차감됩니다.";
							}
							document.getElementById("inOMtxt").value = memo;
						}
					</script>
					
					<table width='100%' class='tbl'>
						<tr>
							<td colspan="2">
								<label><input type="radio" name="btnM" onClick="goMemo(1);"> <b>오차발생</b></label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="btnM" onClick="goMemo(2);"> <b>추가배송비</b></label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="btnM" onClick="goMemo(3);"> <b>기타</b></label>
							</td>
						</tr>
						<tr>
							<td>메모 : <input type='text' style='width:470px;' id='inOMtxt' onkeydown='if(event.keyCode==13)fnSaveMemo();'></td>
							<td style='width:50px;padding-top:20px;'>				  			
								<span class='button medium icon'><span class='check'></span><input type='button' value='저장' onclick='fnSaveMemo()'  /></span>			  			
								<span class='button medium icon' style='visibility:hidden;' id='canBtn'><span class='check'></span><input type='button' value='수정취소' onclick='fnEditMemoCancel()'  /></span>
								<input type='hidden' id='MOIDX' value=''>
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
			<?}?>
			<tr><td height="1" align="right" bgcolor="#E1E1E1"></td></tr>
			<tr>
				<td align="center" valign="top">
		    	
		    		<table class='tbl' style='width:100%;'>
						<tr>
							<td class='orderMemoHeader' style='width:100px;'>날짜</td>
							<td class='orderMemoHeader' style='width:100px;'>작성자</td>

			    			<?if($isAdminMode){?>
							<td class='orderMemoHeader' style='width:200px;'>내용</td>
							<td class='orderMemoHeader' style='width:50px;'>수정</td>
							<td class='orderMemoHeader' style='width:50px;'>삭제</td>
							<?}else{?>
							<td class='orderMemoHeader' style='width:300px;'>내용</td>
							<?}?>
						</tr>
						<?
							while($rs = sql_fetch_array($result)){
								# DB 레코드 결과값 모두 변수로 전환
								foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
						?>
						<tr>
							<td class='orderMemoList' align="center"><?=date("Y-m-d H:i:s",$dbOMregdate)?></td>
							<td class='orderMemoList' align="center"><?=$dbMname?></td>
							<td class='orderMemoList' align="left" style="padding:5px 0px;" id='txt<?=$dbIDX?>'><?=$dbOMmemo?></td>
							<?if($isAdminMode){?>							
							<?if($dbOMMID==$MID){?>
							<td class='orderMemoList' align="center"><a href='javascript:fnEditMemo("<?=$dbIDX?>");'>[수정]</a></td>
							<td class='orderMemoList' align="center"><a href='javascript:fnDeleteMemo("<?=$dbIDX?>");'>[삭제]</a></td>
							<?}else{?>
							<td></td><td></td>
							<?}?>
							<?}?>
						</tr>
						<?
							}
							if(!mysqli_num_rows($result)){
						?>
						<tr><td colspan=5 class='orderMemoList'><center>- 메모가 없습니다 -</center></td></tr>
						<?}?>
					</table>
					
				</td>
			</tr>
			<tr><td height="90" align="center"><img src="/image_3/p_bt16.jpg" width="128" height="38" style='cursor:pointer;' onclick='fnClosePopup()' /></td></tr>
		</table>		
		<?
			} else if($stepCode=="save") {
				//-- 저장 처리
				if($MOIDX) {
					$sql = "update 2011_orderMemo set ";
					$sql.= "OMmemo = '" . $content . "' ";
					$sql.= " where IDX='" . $MOIDX . "'";
					sql_query($sql);
				} else {
					$sql = "insert into 2011_orderMemo (OIDX,OMtype,OMMID,OMmemo,OMregdate,OMdeleted) values(";
					$sql.= "'" . $OIDX . "',";
					$sql.= "'" . $OMtype . "',";
					$sql.= "'" . $MID . "',";
					$sql.= "'" . $content . "',";
					$sql.= "'" . date(time()) . "',";
					$sql.= "'0')";
					sql_query($sql);
				}
				echo "##OK##" . $OIDX . "##" . $OMtype;
			} else if($stepCode=="delete") {
				//-- 삭제 처리
				$sql = "delete from 2011_orderMemo where IDX='" . $MOIDX . "'";
				sql_query($sql);
				
				echo "##OK##" . $OIDX . "##" . $OMtype;
			}
		?>