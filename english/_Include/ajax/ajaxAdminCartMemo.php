<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($stepCode=="list")
	{				
		#-- Memo 불러오기
		
		$sql = "select a.*,b.Mname from 2011_adminCartMemo as a left join 2011_memberInfo as b on a.ACMMID = b.MID where ACMMKIDX='" . $qMKIDX . "' and ACMgroup='" . $qGRP . "' order by ACMregdate desc  ";
		$result = sql_query($sql);
		
		?>
		
		<style>
			.memoHeader
			{
				text-align:center;
				height:25px;
				font-weight:bold;
				border-bottom:1px solid #afafaf;
			}
			.memoList
			{
				height:25px;
				border-bottom:1px solid #efefef;
			}
		</style>
		
		
		<script>
			function fnSaveMemo()
			{
				txt = jQuery("#inACMtxt").val();
				
				if(!txt)
				{
					alert("내용을 입력해 주세요.");
					jQuery("#inACMtxt").focus();
					return;
				}
				
				midx=jQuery("#MOIDX").val();
				
				txt = txt.replaceAll("'","");
				txt = txt.replaceAll('"',"");
				
				param = "stepCode=save&content=" + txt + "&qMKIDX=<?=$qMKIDX?>&MOIDX=" + midx + "&qGRP=<?=$qGRP?>";
				
				$.ajax({
	 			url:'/_Include/ajax/ajaxAdminCartMemo.php',
	 				type:"POST",
					data : param,
					dataType:"text",
					error:fnErrorAjax,
					success:function(_response){
						
						v = _response.split("##");
						if(v[1]=="OK")
						{
							fnOpenMemo(1);
						}
					}
				});
			}
			
			function fnEditMemo(idx)
			{
				jQuery("#MOIDX").val(idx);
				jQuery("#inACMtxt").val(jQuery("#txt"+idx).html());
				jQuery("#canBtn").css("visibility","visible");
			}
			
			function fnEditMemoCancel()
			{
				jQuery("#inACMtxt").val('');
				jQuery("#MOIDX").val('');
				jQuery("#canBtn").css("visibility","hidden");
			}
			
			function fnDeleteMemo(idx)
			{
				if(!confirm('삭제하시겠습니까?'))return;	
				param = "stepCode=delete&MOIDX=" + idx;
				
				$.ajax({
	 			url:'/_Include/ajax/ajaxAdminCartMemo.php',
	 				type:"POST",
					data : param,
					dataType:"text",
					error:fnErrorAjax,
					success:function(_response){
						
						v = _response.split("##");
						if(v[1]=="OK")
						{
							fnOpenMemo(1);
						}
						else
						{
							alert(_response);
						}
					}
				});
			}
			
			
		</script>
		
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  
		  <tr><td height="1" align="center" bgcolor="#E1E1E1"><b>직 원 용 메 모</b></td></tr>

		  <tr>
		    <td align="center" valign="top">
		    	
		    	<table class='tbl' style='width:100%;'>
		    		<tr>
		    			<td class='memoHeader' style='width:100px;'>날짜</td>
		    			<td class='memoHeader' style='width:100px;'>작성자</td>
		    			
		    			<td class='memoHeader' style='width:200px;'>내용</td>
		    			<td class='memoHeader' style='width:50px;'>수정</td>
		    			<td class='memoHeader' style='width:50px;'>삭제</td>
		    			
		    			
		    		</tr>
		    		
		    		<?
         	while($rs = sql_fetch_array($result))
   					{
  						# DB 레코드 결과값 모두 변수로 전환
							foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
					?>
		    		<tr>
		    			<td class='memoList'><?=date("Y-m-d H:i:s",$dbACMregdate)?></td>
		    			<td class='memoList'><?=$dbMname?></td>
		    			<td class='memoList' align=left id='txt<?=$dbIDX?>'><?=$dbACMmemo?></td>
		    			
		    			
		    				<?if($dbACMMID==$MID){?>
		    				<td class='memoList'>
			    				<a href='javascript:fnEditMemo("<?=$dbIDX?>");'>[수정]</a>
		    				</td>
		    				<td class='memoList'>
			    				<a href='javascript:fnDeleteMemo("<?=$dbIDX?>");'>[삭제]</a>
			    			</td>
		    				<?}else{?>
		    					<td></td><td></td>
		    				<?}?>
		    			
		    			
		    		</tr>
		    	<?}
		    	
		    	if(!mysqli_num_rows($result))
		    	{
		    	?>
		    		<tr>
		    			<td colspan=5 class='memoList'><center>- 메모가 없습니다 -</center></td>
		    		</tr>
		    	<?}?>
		    	
		    	</table>
		    	
		    </td>
		  </tr>
		  
		  <tr>
		  	<td align=center>
		  		
		  		<br>
		  		<table width='100%' class='tbl'>
		  			<tr>
		  				<td style='width:600'>메모 : <input type='text' style='width:350px;' maxlength=150 id='inACMtxt' onkeydown='if(event.keyCode==13)fnSaveMemo();'>(100자)</td>
				  		<td align=left>
			  			<span class='button medium icon' style='margin-toppx;'><span class='check'></span><input type='button' value='저장' onclick='fnSaveMemo()'  /></span>
			  			<span class='button medium icon' style='margin-toppx;visibility:hidden;' id='canBtn'><span class='check'></span><input type='button' value='수정취소' onclick='fnEditMemoCancel()'  /></span>
			  			<input type='hidden' id='MOIDX' value=''>
				  		</td>
				  	</tr>
				  </table>
		  	</td>
		  </tr>
		  
		</table>		
		
		
		
		<?
	}
	else if($stepCode=="save")
	{
		//-- 저장 처리
		
		if($MOIDX)
		{
			$sql = "update 2011_adminCartMemo set ";
			$sql.= "ACMmemo = '" . $content . "' ";
			$sql.= " where IDX='" . $MOIDX . "'";
			sql_query($sql);
		}
		else
		{
			$sql = "insert into 2011_adminCartMemo (ACMMKIDX,ACMgroup,ACMMID,ACMmemo,ACMregdate,ACMdeleted) values(";
			$sql.= "'" . $qMKIDX . "',";
			$sql.= "'" . $qGRP . "',";
			$sql.= "'" . $MID . "',";
			$sql.= "'" . $content . "',";
			$sql.= "'" . date(time()) . "',";
			$sql.= "'0')";
			sql_query($sql);
		}
		echo "##OK##" . $OIDX . "##" . $OMtype;
	}
	
	else if($stepCode=="delete")
	{
		//-- 삭제 처리
		
		$sql = "delete from 2011_adminCartMemo where IDX='" . $MOIDX . "'";
		sql_query($sql);
		
		echo "##OK##" . $OIDX . "##" . $OMtype;
	}
	
	?>