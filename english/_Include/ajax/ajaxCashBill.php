<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	/*=====================================================================
	테스트 "Y" / 정상작동 "N" Flag
	=====================================================================*/
	$isTest = "Y";
	
	if($stepCode=="send")
	{
		#-- 발급 확인창
		$sql = "select * from 2011_cashbillInfo  where IDX='" . $CBIDX . "'";
		$rs = sql_fetch($sql);
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
		?>
		
		<script>
			function fnSetCBtotalPrice()
			{
				v1 = parseInt(jQuery("#inCBsupply").val());
				v2 = parseInt(jQuery("#inCBVAT").val());
				if(!v1)
				{
					jQuery("#inCBsupply").val(0);
					v1=0;
				}
				if(!v2)
				{
					jQuery("#inCBVAT").val(0);
					v2=0;
				}
				totVal = v1+v2;
				jQuery("#totTD").html(commify(totVal));				
			}
			
			function  fnCBsave()
			{
				fnSetCBtotalPrice();
				
				v1 = parseInt(jQuery("#inCBsupply").val());
				v2 = parseInt(jQuery("#inCBVAT").val());
				
				amt = parseInt(jQuery("#inCBAMT").val());
				
				totVal = v1+v2;
				
				if(totVal!=amt)
				{
					if(!confirm("결제총액과 공급가+부가세가 일치하지 않습니다.\n\n계속 진행하시겠습니까?"))
					{
						return false;
					}
				}
				
				var frm = document.cashBillForm;
				
				frm.allat_cert_no.value = jQuery("#inCBcertCode").val().replaceAll("-","");
				frm.allat_supply_amt.value = v1;	//-- 공급가액 입력
				frm.allat_vat_amt.value =	v2;			//-- 부가세 입력
				
				return ftn_cashapp(frm);
			}
			
			function ftn_cashapp(dfm) {
			  var ret;
			  ret = invisible_CashApp(dfm);//Function 내부에서 submit을 하게 되어있음.
			  if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
			    // 오류 코드 : 0001~9998 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
			    alert(ret.substring(4,ret.length));   // Message 가져오기
			    return false;
			  }
			  if( ret.substring(0,4)=="9999" ){
			    // 오류 코드 : 9999 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
			    alert(ret.substring(8,ret.length));     // Message 가져오기
			    return false;
			  }
			  return true;
			}
			
		</script>
		
		<table width="342" border="0" cellspacing="0" cellpadding="0">
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
		    		<form name='cashBillForm' action='cashbillAction.php' method='post' target='cashIframe'>
		    			<input type=hidden name="allat_shop_id" value="cheonyu.com" size=26 maxlength=20>	<!-- 상점ID -->
		    			<input type=hidden name="allat_apply_ymdhms" value="<?=date("Ymdhis")?>" size=26 maxlength=14> <!-- 거래 요청일시 YYYYMMDDHHIISS -->
		    			<input type=hidden name="allat_shop_member_id" value="cheonyu.com" size=26 maxlength=100> <!-- 회원ID -->
		    			<input type=hidden name="allat_cert_no" value="" size=26 maxlength=13>	<!-- 인증정보 -->
		    			<input type=hidden name="allat_supply_amt" value="" size=26 maxlength=10>	<!-- 공급가액 -->
		    			<input type=hidden name="allat_vat_amt" value="" size=26 maxlength=20>	<!-- 부가세 -->
		    			<input type=hidden name="allat_product_nm" value="천유상품" size=26 maxlength=100>	<!-- 상품명 -->
		    			<input type=hidden name="allat_receipt_type" value="NBANK" size=26 maxlength=10>	<!--allat_receipt_type : ABANK(계좌이체), NBANK(무통장), VBANK(가상계좌)-->
		    			<input type=hidden name="allat_seq_no" value="" size=26 maxlength=10>	<!-- allat_seq_no : 올앳거래 일련번호 - Null -->
		    			<input type=hidden name=allat_enc_data value=''>	<!-- 주문정보 암호화 필드 Null -->
		    			<input type=hidden name="allat_test_yn" value="<?=$isTest?>" size=26 maxlength=1>	<!-- allat_test_yn : 테스트(Y),서비스(N) -->
		    			<input type=hidden name="CBIDX" value="<?=$CBIDX?>" size=5 maxlength=5>	<!-- 결제IDX -->
		    			<input type=hidden name="SetType" value="send" size=5 maxlength=5>	<!-- 처리방식 -->
		    			
		    		</form>
		    		<tr>
		    			<td height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2>[ 현금영수증 발행하기 ]</td>
	    			</tr>
		    		
		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>실제결제금액</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=number_format($dbCBAMT)?> <input type='hidden' value='<?=$dbCBAMT?>' id='inCBAMT'></td>
	    			</tr>
		    		
		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>인증정보</td>
	    				<td style='border-bottom:1px solid #efefef;'><input type='text'  style='text-align:right;' name='inCBcertCode' id='inCBcertCode' value='<?=$dbCBcertCode?>'></td>
	    			</tr>
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>공급가</td>
	    				<td style='border-bottom:1px solid #efefef;'><input type='text'  style='text-align:right;' size=8 name='inCBsupply' id='inCBsupply' value='<?=$dbCBsupply?>' onkeydown='onlyNum()' onblur='fnSetCBtotalPrice()'></td>
	    			</tr>
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>부가세(VAT)</td>
	    				<td style='border-bottom:1px solid #efefef;'><input type='text' style='text-align:right;' size=8 name='inCBVAT' id='inCBVAT' value='<?=$dbCBVAT?>' onkeydown='onlyNum()' onblur='fnSetCBtotalPrice()'></td>
	    			</tr>
	    			
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>총액</td>
	    				<td style='border-bottom:1px solid #efefef;' id='totTD'><?=number_format($dbCBsupply+$dbCBVAT)?></td>
	    			</tr>
	    		</table>
		    	
		    </td>
		  </tr>
		  
		  
		  <tr>
		    <td height="90" align="center"><table width="270" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="128"><img src="/image_3/p_bt17.jpg" width="128" height="38" style='cursor:pointer;' onClick="if(fnCBsave())fnClosePopup();" /></td>
		        <td>&nbsp;</td>
		        <td width="128"><img src="/image_3/p_bt16.jpg" width="128" height="38" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
		      </tr>
		    </table></td>
		  </tr>
		</table>		
		
		
		
		<?
	}
	else if($stepCode=="Cancel")
	{
		#-- 취소 확인창
		$sql = "select * from 2011_orderPayment as a left join 2011_orderInfo as b on a.OIDX = b.IDX left join 2011_cashbillInfo as c on a.IDX = c.PAYIDX where a.IDX='" . $OPAYIDX . "'";		
		$sql = "select * from 2011_cashbillInfo  where IDX='" . $CBIDX . "'";
		$rs = sql_fetch($sql);
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
		?>
		<script>
				function ftn_cashcan(dfm) {
				  var ret;
				  ret = invisible_CashCan(dfm);//Function 내부에서 submit을 하게 되어있음.
				  if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
				    // 오류 코드 : 0001~9998 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
				    alert(ret.substring(4,ret.length));   // Message 가져오기
				  }
				  if( ret.substring(0,4)=="9999" ){
				    // 오류 코드 : 9999 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
				    alert(ret.substring(8,ret.length));     // Message 가져오기
				  }
				}
			</script>

					<form name='cashBillForm' action='cashbillAction.php' method='post' target='cashIframe'>
		    			<input type=hidden name="allat_shop_id" value="cheonyu.com" size=26 maxlength=20>	<!-- 상점ID -->
		    			<input type=hidden name="allat_cash_bill_no" value="<?=$dbCBcashBillNo?>" size=26 maxlength=10>	<!-- 현금영수증 일련번호 -->
		    			<input type=hidden name="allat_supply_amt" value="<?=$dbCBsupply?>" size=26 maxlength=10>	<!-- 취소 공급가액 -->
		    			<input type=hidden name="allat_vat_amt" value="<?=$dbCBVAT?>" size=26 maxlength=10>	<!-- 취소 VAT 금액 -->
		    			<input type=hidden name=allat_enc_data value=''>	<!-- 암호화 필드 -->
		    			<input type=hidden name=allat_opt_pin value="NOVIEW">	<!-- 올앳 참조필드 -->
		    			<input type=hidden name=allat_opt_mod value="WEB">	<!-- 올앳 참조필드 -->
		    			<input type=hidden name="allat_test_yn" value="<?=$isTest?>" size=26 maxlength=1>	<!-- allat_test_yn : 테스트(Y),서비스(N) -->
		    			<input type=hidden name="CBIDX" value="<?=$CBIDX?>" size=5 maxlength=5>	<!-- 결제IDX -->
		    			<input type=hidden name="SetType" value="cancel" size=5 maxlength=5>	<!-- 처리방식 -->
		    			
		    		</form>
		
		<table width="342" border="0" cellspacing="0" cellpadding="0">
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
		    			<td height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2>[ 현금영수증 발행결과 ]</td>
	    			</tr>
		    		
		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>인증정보</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=$dbCBcertCode?></td>
	    			</tr>
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>공급가</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=number_format($dbCBsupply)?></td>
	    			</tr>
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>부가세(VAT)</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=number_format($dbCBVAT)?></td>
	    			</tr>
	    			
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>총액</td>
	    				<td style='border-bottom:1px solid #efefef;' id='totTD'><?=number_format($dbCBsupply + $dbCBVAT)?></td>
	    			</tr>

		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>현금영수증<br>일련번호</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=$dbCBcashBillNo?></td>
	    			</tr>
	    			
		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>발급날짜</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=date("Y-m-d H:i:s",$dbCBapprovalDate)?></td>
	    			</tr>


		    		<tr>
		    			<td height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2>
		    				
		    			<font color=red>해당 영수정보를 "취소"하시려면 "확인"을 눌러주세요.</font>	
		    			</td>
	    			</tr>
	    				    			
	    		</table>
		    	
		    </td>
		  </tr>
		  <tr>
		    <td height="90" align="center"><table width="270" border="0" cellspacing="0" cellpadding="0">

		      <tr>
		        <td width="128"><img src="/image_3/p_bt17.jpg" width="128" height="38" style='cursor:pointer;' onClick="ftn_cashcan(document.cashBillForm);" /></td>
		        <td>&nbsp;</td>
		        <td width="128"><img src="/image_3/p_bt16.jpg" width="128" height="38" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
		      </tr>
		    </table></td>
		  </tr>
		</table>
	<?		
	}
	else if($stepCode=="view")
	{
		#-- 발급 조회
		//$sql = "select * from 2011_orderPayment as a left join 2011_orderInfo as b on a.OIDX = b.IDX left join 2011_cashbillInfo as c on a.IDX = c.PAYIDX where a.IDX='" . $OPAYIDX . "'";		
		$sql = "select * from 2011_cashbillInfo  where IDX='" . $CBIDX . "'";
		$rs = sql_fetch($sql);
		# DB 레코드 결과값 모두 변수로 전환
		foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
		?>
		<table width="342" border="0" cellspacing="0" cellpadding="0">
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
		    			<td height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center colspan=2>[ 현금영수증 발행결과 ]</td>
	    			</tr>
		    		
		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>인증정보</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=$dbCBcertCode?></td>
	    			</tr>
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>공급가</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=number_format($dbCBsupply)?></td>
	    			</tr>
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>부가세(VAT)</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=number_format($dbCBVAT)?></td>
	    			</tr>
	    			
	    			<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>총액</td>
	    				<td style='border-bottom:1px solid #efefef;' id='totTD'><?=number_format($dbCBsupply + $dbCBVAT)?></td>
	    			</tr>

		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>현금영수증<br>일련번호</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=$dbCBcashBillNo?></td>
	    			</tr>
	    			
		    		<tr>
		    			<td width='100' height=30 style='border-bottom:1px solid #efefef;font-weight:bold' align=center>발급날짜</td>
	    				<td style='border-bottom:1px solid #efefef;'><?=date("Y-m-d H:i:s",$dbCBapprovalDate)?></td>
	    			</tr>
	    			
	    		</table>
		    	
		    </td>
		  </tr>
		  <tr>
		    <td height="90" align="center"><table width="270" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td align=center><img src="/image_3/p_bt17.jpg" width="128" height="38" style='cursor:pointer;' onClick="fnClosePopup();" /></td>
		      </tr>
		    </table></td>
		  </tr>
		</table>
		<?
	}

?>
