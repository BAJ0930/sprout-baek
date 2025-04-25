<?
	$isAdminMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($stepCode=="cartIn")
	{
		$IDXarray =  explode(",",$qIDX);
		$IDXlen = sizeof($IDXarray);
		
		$sql ="SET group_concat_max_len = 512;";
		sql_query($sql);

		$mainTbl = "select a.IDX,a.Pname,a.Pbarcode,a.Pstate,a.Pagree,a.PstockCount,a.Pposition,a.PstockDate,a.PoptionUse,  ";	
		$mainTbl.="a.Pprice1,a.Pprice2,a.Pprice3,a.PsaveFile1,a.Pcategory1,a.Pcategory2,a.Pcategory3, ";
		$mainTbl.="b.BRkname,a.MKIDX,c.MKkname  ";
		$mainTbl.="from 2011_productInfo as a ";
		$mainTbl.="left join 2011_brandInfo as b on a.BRIDX = b.IDX  ";
		$mainTbl.="left join 2011_makerInfo as c on a.MKIDX=c.IDX  ";
		$mainTbl.="left join 2011_makerSeller as g on b.MKIDX=g.MKIDX  ";
		
		$sql ="SELECT ";
		$sql.="a.* ,z.OPsellInfo,zz.ORPcount,y.OPreadyInfo,yy.ORPready ";
		$sql.="FROM ( ##mainTbl## ) as a  ";
		
		//-- 옵션 재고 / 판매수량 구하기 서브쿼리
		$sql.="left join (select e.PIDX,GROUP_CONCAT(e.IDX,'|',e.OPstock,'|',e.OPvalue,'|',ifnull(f.ORPcount,0) SEPARATOR '##') as OPsellInfo  ";
		$sql.="from 2011_productOption as e  ";
		$sql.="left join (select orp.PIDX,orp.ORPoption,sum(orp.ORPcount) as ORPcount from 2011_orderProduct as orp left join 2011_orderInfo as ord on orp.OIDX = ord.IDX where orp.ORPdeleted=0 and ord.Odeleted=0 ";
		if($s12)$sql.=" and ord.Oregdate>=" . $s12_timestamp . " ";
		if($s13)$sql.=" and ord.Oregdate<" . $s13_timestamp . " ";
		$sql.= " group by PIDX,ORPoption ) as f on e.IDX = f.ORPoption   ";
		$sql.="group by e.PIDX ) as z on a.IDX = z.PIDX ";
		
		//-- 옵션없이 상품 자체 판매수량
		$sql.="left join (select orp.PIDX,sum(orp.ORPcount) as ORPcount from 2011_orderProduct as orp left join 2011_orderInfo as ord on orp.OIDX = ord.IDX where orp.ORPdeleted=0 and ord.Odeleted=0 ";
		if($s12)$sql.=" and ord.Oregdate>=" . $s12_timestamp . " ";
		if($s13)$sql.=" and ord.Oregdate<" . $s13_timestamp . " ";
		$sql.=" group by PIDX) as zz on a.IDX = zz.PIDX ";
		
		
		//-- 옵션 재고 / 판매수량 구하기 서브쿼리
		$sql.="left join (select e.PIDX,GROUP_CONCAT(e.IDX,'|',ifnull(f.ORPcount,0) SEPARATOR '##') as OPreadyInfo  ";
		$sql.="from 2011_productOption as e  ";
		$sql.="left join (select PIDX,ORPoption,sum(ORPcount) as ORPcount from 2011_orderProduct where  ORPdeleted=0 and ORPcountCheck=1  group by PIDX,ORPoption) as f on e.IDX = f.ORPoption  ";
		$sql.="group by e.PIDX ) as y on a.IDX = y.PIDX ";
		
		//-- 옵션없이 상품 자체 판매수량
		$sql.="left join (select PIDX,sum(ORPcount) as ORPready from 2011_orderProduct where  ORPdeleted=0 and ORPcountCheck=1  group by PIDX) as yy on a.IDX = yy.PIDX ";

		
		for($k=0;$k<$IDXlen;$k++)
		{
			if($k!=0)$mainWhere.=" or ";
			$mainWhere.=" a.IDX='" . $IDXarray[$k] . "' ";
		}
		
		$mainTbl.= "where " . $mainWhere;
		
		$sql = str_replace("##mainTbl##",$mainTbl,$sql);
		
		//echo $sql;
		
		$result = sql_query($sql);
		
		?>
		
		<!-------------------------------------------------------------->
		<script>
		function fnSaveAdminCart()
		{
			
			countObj = $("INPUT[id='inADcartCount']");
			
			len = countObj.length;
			
			dataStr="";
			
			for(k=0;k<len;k++)
			{
				tmp = countObj.eq(k).attr("name").split("##");
				
				pIDX = tmp[0];
				pOption = tmp[1];
				pCount = countObj.eq(k).val();
				
				if(!pCount)pCount=0;
				
				//-- 데이터 조합
				if(dataStr!="")dataStr+="##";
				dataStr+= pIDX + "|" + pOption + "|" + pCount + "|" + pMKIDX;
				
			}
			
			param = "dataStr=" + dataStr + "&stepCode=cartSet";
			

			
			loadPopup();
			jQuery("#popContent").html(popBox1 + "<center><img src='/image_1/ajax-loader.gif'>" + popBox2);
			fnPopupResize();
			centerPopup();		
		
			$.ajax({
				url:"/_Include/ajax/ajaxAdminCart.php",
				type:"POST",
				cache:false,
				data : param,
				dataType:"text",
				error:fnErrorAjax,
				success:function(_response)
				{
					v = _response.split("##");
					popupLoding=0;
	
					if(v[1]=="error")
					{
						alert('일부 제품이 오류로 인하여 작업이 중단되었습니다.\n\n제품번호 : ' + v[2]);
						fnClosePopup();
						return;
					}
					else if(v[1]=="SaveOK")
					{
						alert("발주목록에 저장되었습니다\n\n※ 실제 발주는 [상품관리 > 발주관리] 에서 처리하시기 바랍니다.");
						fnClosePopup();
						return;
					}
					else
					{
						loadPopup();
						jQuery("#popContent").html(popBox1 + _response + popBox2);
						fnPopupResize();
						centerPopup();		
					}
					
					
				}
			});
			
		}
		
		
		</script>
		<!-------------------------------------------------------------->
		
		<!-- 본문 영역 -->
		<table width='800'  >
			<tr>
		    <td align="right" ><!--<img src="../image_1/v_11.jpg" width="15" height="15" vspace="5" />--><img src="/image_1/v_12.jpg" width="15" height="15" hspace="5" vspace="5" style='cursor:pointer;' onclick='fnClosePopup()' /></td>
		  </tr>
		  <tr>
		  	<td>
		  		
		  		
		  				
						<table width='100%' class='adminDataHeadTbl' >
							
							<tr>
								<td class='adminDataHeadTD'>번호</td>
								<td class='adminDataHeadTD'>이미지</td>
								<td class='adminDataHeadTD'>제품명</td>
								<td class='adminDataHeadTD'>제조사<br>브랜드</td>
								<td class='adminDataHeadTD'>판매가</td>
								<td class='adminDataHeadTD'>옵션</td>
								<td class='adminDataHeadTD'>실재고</td>
								<td class='adminDataHeadTD'>발송<br>대기</td>
								<td class='adminDataHeadTD'>누적판매</td>
								<td class='adminDataHeadTD'>신청수량</td>
								
							</tr>
							
							<!--  목록 -->

							<?while($rs=sql_fetch_array($result)){
								# DB 레코드 결과값 모두 변수로 전환
								foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}



								#=== 판매수량 구하기 =================== (loop 문 안에서 쿼리날리는거 진짜 싫지만 방법이 없음 ㅡ_-;;)
								$opName=array();
								$opValue=array();
								$opStock=array();
								$opSellCount=array();
								$opLink=array();
								$cnt=0;

								
								
								#------ 옵션이 있을때 처리 ----------------------------------
								if($dbOPsellInfo && $dbPoptionUse==2)
								{
									
									$opInfo = explode("##",$dbOPsellInfo);
									$opLen = sizeof($opInfo);
									for($opCount=0;$opCount<$opLen;$opCount++)
									{
										$opTmp = explode("|",$opInfo[$opCount]);

										$opIDX[$opCount] = $opTmp[0];
										$opStock[$opCount] = $opTmp[1];
										$opValue[$opCount] = $opTmp[2];
										$opSellCount[$opCount] = $opTmp[3];										
										$opReady[$opCount] = 0;
									}
									
									
									#--- 팔린 갯수 처리
									$readyInfo = explode("##",$dbOPreadyInfo);
									
									$opLen = sizeof($opInfo);
									
									for($j=0;$j<$opLen;$j++)
									{
											$opTmp = explode("|",$readyInfo[$j]);
											
											for($opCount=0;$opCount<$opLen;$opCount++)
											{
												if($opIDX[$opCount]==$opTmp[0])
												{
													$opReady[$opCount] = $opTmp[1];
													$opStock[$opCount] = $opStock[$opCount] - $opTmp[1];
												}
											}
										}
									
								}
								else
								{
								#------ 옵션이 없을때 처리 ----------------------------------
									
									$opReady[0] = 0;
									$opSellCount[0]=0;
									$opReady[0]=0;
								
									$opIDX[0] = 0;
									$opStock[0] = $dbPstockCount;
									$opValue[0] = "-";
									if($dbORPcount)$opSellCount[0] = $dbORPcount;									
									if($dbORPready)
									{
										$opReady[0] = $dbORPready;
										$opStock[0] = $dbPstockCount - $dbORPready;
									}
								}
							?>

							<tr>
								
								<td class='adminDataTD' ><?=$dbIDX?></td>
								<td class='adminDataTD' >
									<?if($dbPsaveFile1){
										
										$fPosition = explode("/",$dbPsaveFile1);
         						$thumb = $fPosition[0] . "/thumb/" . $fPosition[1];	
         						$pImg = "<img src='/_DATA/product/" . $thumb . "' width='50' height='50'  border=0 />";
									
									echo $pImg;
								}?>
								</a></td>
								<td class='adminDataTD' ><?=$dbPname?> <?=$stockDateStr?></td>

								<td class='adminDataTD'><?=$dbMKkname?><br><?=$dbBRkname?></td>
								<td class='adminDataTD'><?=$dbPprice3?></td>
																
								<td class='adminDataTD' nowrap style='padding-top:5px;padding-bottom:5px;line-height:22px;'><?for($k=0;$k<sizeof($opValue);$k++)echo $opLink2[$k] . $opValue[$k] . "<br>";?></td>
								<td class='adminDataTD' nowrap style='padding-top:5px;padding-bottom:5px;line-height:22px;'><?for($k=0;$k<sizeof($opValue);$k++)echo $opLink[$k] . $opStock[$k] . "</a><br>";?></td>
								<td class='adminDataTD' nowrap style='padding-top:5px;padding-bottom:5px;line-height:22px;'><?for($k=0;$k<sizeof($opValue);$k++)echo $opLink[$k] . $opReady[$k] . "</a><br>";?></td>
								<td class='adminDataTD' nowrap style='padding-top:5px;padding-bottom:5px;line-height:22px;'><?for($k=0;$k<sizeof($opValue);$k++)echo $opSellCount[$k] . "<br>";?></td>
								<td class='adminDataTD' nowrap style='padding-top:5px;padding-bottom:5px;line-height:22px;'><?for($k=0;$k<sizeof($opValue);$k++){?><input type='text' name='<?=$dbIDX?>##<?=$opIDX[$k]?>##<?=$dbMKIDX?>' id='inADcartCount' size=3 maxlength=3 value=0 style='ime-mode:disabled;' onkeydown='onlyNum()'>개 <br><?}?></td>
							</tr>

							<?}?>
							<!--  목록 끝 -->
						</table>
						<br><br><br>
						<Center>
						<span class='button medium icon' style='margin-top:3px;'><span class='add'></span><input type='button' value='저 장' onclick='fnSaveAdminCart()'   /></span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span class='button medium icon' style='margin-top:3px;'><span class='delete'></span><input type='button' value='취 소' onclick='fnClosePopup()'   /></span>
						
					</td>
				</tr>
			</table>
		
		
		
		<?
		
		
		exit();
	}
	else if($stepCode=="cartSet")
	{
		/*--------------------------------------
				dataStr 구조 (구분자 '|')
				
				제품IDX | 옵션IDX | 수량
		--------------------------------------*/
		
		#-- 제품 분할
		$dataArray = explode("##",$dataStr);	
		$pLen = sizeof($dataArray);
		
		for($k=0;$k<$pLen;$k++)
		{
			
			$d = explode("|",$dataArray[$k]);
			
			if($d[2])
			{
				if($s5) {
					$ACAgroup = $s5;
					
					$sql = "select * from 2011_adminCart where ACAMID='" . $MID . "' and ACAgroup=" . $ACAgroup;
					$data = sql_fetch($sql);

					$sendDate = $data[ACAsendDate];

				} else {
					$ACAgroup = "2000000000";
				}
				#-- 카트에 담기 -------------------------
				#---- Step1. 카트 중복 체크 ------------
				$sql = "select * from 2011_adminCart where ACAMID='" . $MID . "' and ACAPIDX='" . $d[0] . "' and ACAoption='" . $d[1] . "' and ACAgroup=" . $ACAgroup;
				$result = sql_query($sql);
				
				if($rs = sql_fetch_array($result))
				{				
					##-- 수량 증가(업데이트 해야하나?)
					$cIDX = $rs["IDX"];
					
					##-- 기존 내용 업데이트
					$sql = "update 2011_adminCart set ";
					$sql.= "ACAcount='" . $d[2] . "' where IDX='" . $cIDX . "'";;
					sql_query($sql);
				}
				else
				{
					##-- 새로 추가
					$sql = "insert into 2011_adminCart (ACAMID,ACAMKIDX,ACAPIDX,ACAoption,ACAcount,ACAPRICE1,ACAPRICE2,ACAgroup,ACAreturn,ACAregdate) values(";
					$sql.= "'" . $MID . "',";
					$sql.= "'" . $d[3] . "',";
					$sql.= "'" . $d[0] . "',";
					$sql.= "'" . $d[1] . "',";
					$sql.= "'" . $d[2] . "',";
					$sql.= "'" . $d[4] . "',";
					$sql.= "'" . $d[5] . "',";
					$sql.= "'" . $ACAgroup . "',";
					//$sql.= "'2000000000',";
					$sql.= "'" . $d[6] . "',";
					$sql.= "'" . date(time()) . "')";
					
					//echo $sql . "<br><Br>";
					
					sql_query($sql);

					if($s5){
						$last_uid =  mysqli_insert_id($g5['connect_db']);
						sql_query(" UPDATE 2011_adminCart SET ACAsendDate = '$sendDate' WHERE IDX = '$last_uid' ");
					}
				}
			}
			
		}
		//exit();
		
		##-- 마지막 브랜드 체크 (바로가기 처리 위함)
		$sql = "select c.IDX as cIDX,c.MKkname as cMKkname from 2011_productInfo as a left join 2011_brandInfo as b on a.BRIDX = b.IDX left join 2011_makerInfo as c on a.MKIDX=c.IDX where a.IDX='" . $d[0] . "'";
		$mkRs = sql_fetch($sql);
		echo "##SaveOK##" . $mkRs['cIDX'] . "##" . $mkRs['cMKkname'] . "##" . $s5;
	}
?>
