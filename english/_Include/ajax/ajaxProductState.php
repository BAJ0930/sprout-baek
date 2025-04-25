<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($stepCode=="Stock")
	{

		if($OPIDX)
		{
			#-- 재고 확인 쿼리
			$sql = "select * from 2011_productInfo as a left join 2011_productOption as b on a.IDX = b.PIDX  where a.IDX='" . $PIDX . "' and b.IDX='" . $OPIDX . "'";		
			$rs = sql_fetch($sql);
			# DB 레코드 결과값 모두 변수로 전환
			foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
			$nStock = $dbOPstock;
			
			#-- 발송대기 갯수 확인
			$sql = "select sum(ORPcount) as ORPready from 2011_orderProduct where ORPdeleted=0 and ORPcountCheck=1 and ORPoption='" . $OPIDX . "'";
		}
		else
		{
			#-- 재고 확인 쿼리
			$sql = "select * from 2011_productInfo  where IDX='" . $PIDX . "'";
			$rs = sql_fetch($sql);
			# DB 레코드 결과값 모두 변수로 전환
			foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
			$nStock = $dbPstockCount;
			
			#-- 발송대기 갯수 확인
			$sql = "select sum(ORPcount) as ORPready from 2011_orderProduct where ORPdeleted=0 and ORPcountCheck=1 and PIDX='" . $PIDX . "'";			
		}
		$rs = sql_fetch($sql);
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
		$nReady = $dbORPready;
		if(!$nReady)$nReady=0;
		
		#-- 최근 재고 로그 확인
		$sql = "select a.LSregdate,a.LSOIDX,a.LSreason,ifnull(b.Mname,f.MKkname) as Mname,d.MID as MMID,c.OPvalue,a.LScount,a.LSstock1,a.LSstock2,d.OorderName,d.Oname from 2011_LogStock as a ";
		$sql.= " left join 2011_memberInfo as b on a.MID = b.MID ";
		$sql.= " left join 2011_productOption as c on a.OPIDX = c.IDX ";
		$sql.= " left join 2011_orderInfo as d on a.LSOIDX = d.IDX ";
		$sql.= " left join 2011_makerSeller as e on a.MID = e.MSID ";
		$sql.= " left join 2011_makerInfo as f on e.MKIDX = f.IDX ";
		$sql.= " where a.PIDX='" . $PIDX . "' and OPIDX='" . $OPIDX . "' order by LSregdate DESC limit 0,10";
		
		
		$STresult = sql_query($sql);
		?>
		
		<script>
			function fnSetNewCount(obj)
			{
				if(!isNaN(obj.value) && obj.value!="")
				{
					
					t = (<?=$nStock - $nReady?> + parseInt(obj.value));
					
					if(t<0)
					{
						alert("※ 마이너스 수량이 너무 큽니다.\n\n현 재고가 '0 미만'으로 변경 될 수 없습니다. ※");
						obj.value = 0;
						jQuery("#totStock").html(<?=$nStock - $nReady?>  + " 개");						
						return false;
					}
					
					jQuery("#totStock").html((<?=$nStock - $nReady?> + parseInt(obj.value)) + " 개");
					return true;
				}
				else
				{
					obj.value=0;
					jQuery("#totStock").html(<?=$nStock - $nReady?>  + " 개");
				}
			}
			
			
			function setCountMode()
			{
				
				nMode = $("INPUT[id='inStockType']:checked").val();
				
				
				//-- 일단 입력값 0 만들기
				objCountBOX = $("INPUT[id='inStock']").eq(0);
				objCountBOX.val('');
				
				objTitle = $("TD[id='inputTitle']").eq(0);
				objTitle.css("color","red");
				if(nMode==1)
				{
					objTitle.html("신규입/출고");
					jQuery("#nowStockTD").css("display","block");
					jQuery("#nowStockTD2").css("display","none");
					
				}	
				if(nMode==2)
				{
					objTitle.html("반품입고수");
					jQuery("#nowStockTD").css("display","block");
					jQuery("#nowStockTD2").css("display","none");
				}
				else if(nMode==3)
				{
					objTitle.html("현재갯수");
					jQuery("#nowStockTD").css("display","none");
					jQuery("#nowStockTD2").css("display","block");
				}
				
				objCountBOX.keydown(function(evt){
					
					//-- 일반 입고시에는 - 입력가능
					if(nMode==1)
					{
						if(event.keyCode==13){if(fnSetNewCount(this)){fnStockSave('<?=$PIDX?>','<?=$OPIDX?>');}}else{onlyNum2(this.value)}						
					}
					//-- 그외에는 - 입력 불가능
					else
					{
						if(event.keyCode==13){if(fnSetNewCount(this)){fnStockSave('<?=$PIDX?>','<?=$OPIDX?>');}}else{onlyNum(this.value)}
					}
					
				});
				
				
			}
			
		</script>
		
		<table width="600" border="0" cellspacing="0" cellpadding="0">
		 <tr>
		    <td align="right"><!--<img src="../image_1/v_11.jpg" width="15" height="15" vspace="5" />--><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
		  </tr>
		  <tr>
		    <td height="1" align="right" bgcolor="#E1E1E1"></td>
		  </tr>
		  <tr>
		    <td align="center"></td>
		  </tr>
		  <tr>
		    <td align="center" valign="top">
		    	
		    	<table width='100%' cellspacing=0 cellpadding=0>
		    		<tr>
		    			<td height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2>[ 재고수정하기 ]</td>
	    			</tr>
		    		
		    		<tr>
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
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>입고방식</td>
	    				<td style='border-bottom:1px solid #efefef;'>
	    					<input type='radio' value='1' id='inStockType' name='inStockType' onclick='setCountMode()' <?if(!$_SESSION["ssStockType"] || $_SESSION["ssStockType"]==1)echo "checked";?>> 일반입/출고
	    					&nbsp;&nbsp;&nbsp;&nbsp;
	    					<input type='radio' value='3' id='inStockType' name='inStockType' onclick='setCountMode()' <?if($_SESSION["ssStockType"]==3)echo "checked";?>> 재고맞춤
	    				</td>
	    			</tr>
	    			
	    			
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center id='inputTitle'>신규입/출고</td>
	    				<td style='border-bottom:1px solid #efefef;'>
	    					<input type='text' name='inStock' id='inStock' style='ime-mode:disabled;text-align:right;' size=5 onkeydown="if(event.keyCode==13){if(fnSetNewCount(this)){fnStockSave('<?=$PIDX?>','<?=$OPIDX?>');}}else{onlyNum2(this.value)};" onblur='fnSetNewCount(this)' value='' maxlength=8> 개
	    				</td>
	    			</tr>
	    			
	    			<tr id='nowStockTD'>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>현재고</td>
	    				<td style='border-bottom:1px solid #efefef;' id='totStock'><?=$nStock - $nReady?> 개</td>
	    			</tr>
	    			
	    			<tr id='nowStockTD2' style='display:none;'>
	    				<td style='border-bottom:1px solid #efefef;' align=center colspan=2><b><font color=red>현재 총 재고수량을 입력하세요</font></b></td>
	    			</tr>
	    			
	    			<tr>
		    			<td  height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2 bgcolor='#efefef'>[최근 입/출고 현황] ※ 주문번호 클릭시 주문서 열람 ※ <a href="/_Admin/Product/StockState.php?PIDX=<?=$PIDX?>&OPIDX=<?=$OPIDX?>" target="_blank">[전체목록]</a></td>
	    			</tr>
		    		
		    		<tr>
		    			<td  height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2 >
		    				
		    				<table width='100%' style='width:100%;'>
		    					<tr>
		    						<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>입/출고일</td>
		    						<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>담당자</td>
		    						<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>옵션명</td>
		    						<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>주문번호</td>
		    						<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>주문자</td>
		    						<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>수량</td>
		    						<td height=30 align=center style='font-weight:bold;border-bottom:1px solid #efefef;'>변경</td>
		    					</tr>
		    					<?
		    					$rCnt = mysqli_num_rows($STresult);
		    					while($STrs=sql_fetch_array($STresult)){
		    							foreach ($STrs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
											$nStock = $dbPstockCount;
											
											if($dbOPvalue=="")$dbOPvalue="-";
											
											$Olink="";
											if($dbLSOIDX==0)
											{
												if($dbLSreason)$dbLSOIDX="<font color=orange>" . $dbLSreason . "</font>";
												else $dbLSOIDX="직접반영";
												
												$order="-";
											}
											else
											{
												if($dbMMID){
													$query = "SELECT McomName FROM 2011_memberInfo WHERE MID = '$dbMMID'";
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

												if($dbLSreason == '반품' or $dbLSreason == '불량반품') $dbLSOIDX="<font color=orange>" . $dbLSreason . "</font>";
												
												$Olink = "<a href='javascript:fnOpenOrder(" . $dbLSOIDX . ")'>";
											}
											
											
		    						?>
		    						<tr>
			    						<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=date("Y-m-d H:i:s",$dbLSregdate)?></td>
			    						<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$dbMname?></td>
			    						<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$dbOPvalue?></td>
			    						<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$Olink . $dbLSOIDX?></a></td>
			    						<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$order?></td>
			    						<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$dbLScount?></td>
			    						<td height=30 align=center style='border-bottom:1px solid #efefef;'><?=$dbLSstock1?> → <?=$dbLSstock2?></td>
		    						</tr>
		    					<?}
		    					
		    					if(!$rCnt)
		    					{?>
		    						<td height=30 align=center style='border-bottom:1px solid #efefef;' colspan=7>-입고기록 없음-</td>
		    					<?}?>
		    					
		    				</table>
		    				
		    			</td>
	    			</tr>
		    		
		    		
		    		
	    		</table>
		    	
		    </td>
		  </tr>
		  
		  
		  <tr>
		    <td height="90" align="center"><table width="270" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="128"><img src="/image_3/p_bt17.jpg" width="128" height="38" style='cursor:pointer;' onClick="fnStockSave('<?=$PIDX?>','<?=$OPIDX?>')" /></td>
		        <td>&nbsp;</td>
		        <td width="128"><img src="/image_3/p_bt16.jpg" width="128" height="38" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
		      </tr>
		    </table></td>
		  </tr>
		</table>
		<script>
			
			function fnStockFocus()
			{
				jQuery("#inStock").focus();	
			}
			
			
			//-- 일단 로드되면 셋팅
			setCountMode();
			
			setTimeout("fnStockFocus()",1000);
		</script>
		
		<?
	}
	else if($stepCode=="STOCKsave")
	{
		//echo $PIDX . " / " . $OPIDX . " / " . $inStock . " 개 저장처리";
		
		//echo $inStockType;
		$_SESSION["ssStockType"] = $inStockType;
		$nStock=0;
		if($OPIDX)
		{

			#-- 기존 재고 확인
			$sql = "select OPstock from 2011_productOption where IDX='" . $OPIDX . "'";
			$rs = sql_fetch($sql);
			$nStock = $rs[OPstock];	
			
			#-- 옵션 상품 재고 업데이트 (재고맞춤의 경우 해당 재고로 바로 셋팅)
			if($inStockType==3)$sql = "update 2011_productOption set OPstock=" . $inStock . " where IDX='" . $OPIDX . "'";
			else $sql = "update 2011_productOption set OPstock=OPstock+" . $inStock . " where IDX='" . $OPIDX . "'";
			sql_query($sql);			
			
			$OBJtype = "OPSTOCK";
			$OBJIDX = $OPIDX;
		}
		else
		{
			#-- 기존 재고 확인
			$sql = "select PstockCount from 2011_productInfo where IDX='" . $PIDX . "'";
			$rs = sql_fetch($sql);
			$nStock = $rs[PstockCount];	
			
			$OBJtype = "PSTOCK";
			$OBJIDX = $PIDX;
		}
		
		
		#-- 본상품 재고 업데이트 (재고맞춤의 경우 해당 재고로 바로 셋팅)
		if($inStockType==3)
		{
			$sql = "update 2011_productInfo set PstockCount=PstockCount-" . $nStock . "+" . $inStock . " where IDX='" . $PIDX ."'";
		}
		else $sql = "update 2011_productInfo set PstockCount=PstockCount+" . $inStock . " where IDX='" . $PIDX ."'";
		sql_query($sql);		
		
		if($inStockType==2)
		{
			$LSreason = "반품입고";
			$newStock = $nStock + $inStock;
		}
		else if($inStockType==3)
		{
			$LSreason = "재고맞춤";
			$newStock = $inStock;
		}
		else
		{
			 $LSreason="";
			 $newStock = $nStock + $inStock;
		}
		
		//=== 입고 로그 기록 =====================
		if($inStock!=0 || $inStockType==3)
		{
			$sql = "insert into 2011_LogStock (PIDX,OPIDX,MID,LSOIDX,LScount,LSstock1,LSstock2,LSreason,LSregdate) values(";
			$sql.= "'" . $PIDX . "',";
			$sql.= "'" . $OPIDX . "',";
			$sql.= "'" . $MID . "',";
			$sql.= "'0',";
			$sql.= "'" . $inStock . "',";
			$sql.= "'" . $nStock . "',";
			$sql.= "'" . $newStock . "',";
			$sql.= "'" . $LSreason . "',";
			$sql.= "'" . date(time()) . "')";
			
			sql_query($sql);
		}
		
			$sql = "select a.*,b.Pname,ifnull(c.OPstock,b.PstockCount) as StockCount from 2011_stockAlram as a ";
			$sql.= "left join 2011_productInfo as b on a.PIDX=b.IDX  ";
			$sql.= "left join 2011_productOption as c on b.IDX=c.PIDX and a.OPIDX=ifnull(c.IDX,0)  ";
			$sql.= "where a.PIDX='" . $PIDX . "' ";
			$sql.= "and a.ALstate=1 and a.ALdeleted = 0 and (ALregdate + (ALday*60*60*24))>='" . date(time()) . "' and a.ALstockCount<=ifnull(c.OPstock,b.PstockCount) ";
			
			$smsResult = sql_query($sql);
			
			$smsSendCnt = 0;
			while($smsRs = sql_fetch_array($smsResult))
			{
				#== SMS 발송
				$ALIDX = $smsRs["IDX"];
				$smsNum = $smsRs["ALhp"];
				$Pname = strcut_utf8($smsRs["Pname"],20,"..");
				$msg = "[천유입고알람]\\'" . $Pname . "\\' 제품이 입고되었습니다.";
				#smsSend($smsNum,$msg,'','','stockSMS','');
				@smsSendNew($smsNum,$msg,'','','stockSMS','');
				
				#== 입고 알람 상태 변경
				$sql = "update 2011_stockAlram set ALstate=2,ALsendDate='" . date(time()) . "' where IDX='" . $ALIDX . "'";
				sql_query($sql);
				$smsSendCnt++;
			}
		
		
		#--- 주문 차감하기
		if($inStockType==2)
		{
			/*
				사용안함
				2011_orderProductReturn table 로 대체
			*/
			#-- 반품 기록하기
			$sql = "insert into 2011_productReturn(PIDX,OPIDX,PRcount,PRregdate) values(";
			$sql.= "'" . $PIDX . "',";
			$sql.= "'" . $OPIDX . "',";
			$sql.= "'" . $inStock . "',";
			$sql.= "'" . date(time()) . "')";			
			sql_query($sql);
			
			#-- 판매수량 차감하기
			$sql = "update 2011_productInfo set PsellCount=PsellCount-" . $inStock . " where IDX='" . $PIDX . "'";
			sql_query($sql);
			
			if($OPIDX)
			{
				$sql = "update 2011_productOption set OPsellCount=OPsellCount-" . $inStock . " where IDX='" . $OPIDX . "'";
				sql_query($sql);
			}
		}
		
		
		#-- 상품 재고 업데이트 결과 출력
		//echo "##StockSaveOK##" . $OBJtype . "##" . $OBJIDX . "##" . $OBJvalue . "##";
		echo "##StockSaveOK##" . $OBJtype . "##" . $OBJIDX . "##" . $inStock . "##" . $inStockType;
		exit();
	}
	else if($stepCode=="State")
	{
		$sql = "select Pstate from 2011_productInfo where IDX='" . $PIDX . "'";
		$rs = sql_fetch($sql);
		
		$nState = $rs[Pstate];
		
		$newState = $nState+1;
		if($newState==4)$newState=10;
		else if($newState==13)$newState=1;
		
		$sql = "update 2011_productInfo set Pstate='" . $newState . "' where IDX='" . $PIDX . "'";
		sql_query($sql);
		
		#-- 상품 재고 업데이트 결과 출력
		echo "##StateSaveOK##" . $PIDX . "##" . $newState . "##";
	}

?>
