<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();


if($mode=="search")
{

	if(!$s1)
	{
		?>
		<table cellspacing=0 cellpadding=0 width=100%>
			<tr>
				<td height=30 align=center>
					- 검색어를 입력해 주세요. -
				</td>
			</tr>
		</table>
		<?
		exit();
	}
	else
	{

		$s1 = iconv("euc-kr","utf-8",$s1);

		$sql = "select a.*,b.EVname from 2011_productInfo as a left join 2011_eventInfo as b on a.EVIDX=b.IDX  where Pname like '%" . $s1 . "%'";

		if (!$page) $page = 1;
		if (!$listSize) $listSize = 10;
		$CntPerPage = $listSize;
		$PagePerList = 5;
		$StartPos = ($page - 1) * $CntPerPage;
		$sql .= " ORDER BY Pname DESC";
		$result = sql_query($sql);
		$TotalCount = mysqli_num_rows($result);
		$sql .= " LIMIT ".($page - 1) * $CntPerPage.",".$CntPerPage;
		$result = sql_query($sql);

		if(!$TotalCount)
		{
			?>

		<table cellspacing=0 cellpadding=0 width=100%>
			<tr>
				<td height=30 align=center>

					- 검색된 상품이 없습니다. -
				</td>
			</tr>
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
					<tr><td height=30 align=center  bgcolor='#ffffff'><b>※ 설정하실 제품을 클릭하세요 ※</b></td></tr>
					<tr>
            <td align="left" >
            	<table width='97%' class='input2'>
               		<?for($k=1;$k<=$rows;$k++)
                		{
                			if($rowOut==1)break;?>
                		<tr>
                			<?for($i=1;$i<=$cols;$i++)
                				{
                					if($rs = sql_fetch_array($result))
                					{
                						# DB 레코드 결과값 모두 변수로 전환
										foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}

                						$dbPprice2=$dbPprice2;
                						$pImg = "<img src='" . __HOME_PATH__ . "/_DATA/product/" . $dbPsaveFile1 . "' width='80' height='80'  border=0 />";
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
                					//-- 검색어 색상 변환
                					$dbPname2 = str_replace("'","",$dbPname);
                					if($s1)$dbPname = preg_replace("/".$s1."/","<font color=darkorange><b>" . $s1 . "</b></font>",$dbPname);
                					
                					?>
                					
                					
                					
   		             			<td valign=top align=center width=150>

			                    <table  border="0" width='<?=$tdWidth?>' height=150 cellpadding="0" cellspacing="0" class="input2" style='cursor:pointer;' onmouseover='this.style.border="1px solid orange"' onmouseout='this.style.border="1px solid #ffffff"' onclick='fnSetProudct("<?=$dbIDX?>","<?=$dbPname2?>","<?if($dbEVname)echo "1";?>","<?if(!$dbPstockCount)echo "1";?>")'>
			                      <tr>
			                        <td align="center" valign="middle" ><?=$link?><div id='pIMG'  IDX='<?=$dbIDX?>' Stock='<?=$dbPstockCount?>'><?=$pImg?></div></a></td>
			                      </tr>
			                      <tr>
			                        <td align="center" height=20><?=$link . $dbPname?>
			                         <?if($dbEVname){?>
			                      		<br><font color=blue>설정됨 : [<?=$dbEVname?>]</font>
			                    			<?}?>
			                        </td>
			                      </tr>
			                      <tr>
			                      	<td align=center>
			                      		<?=number_format($dbPprice2)?> 원
			                      	</td>
			                      </tr>
			                      <Tr>
			                      	<td>
			                      		<?if(!$dbPstockCount)echo "-재고없음-";?>
			                      	</td>
			                      </tr>
														
														
			                      <tr><td height="5" align="center"></td></tr>
			                    </table>
			                </td>
			                <?if($i!=$cols){?><td valign="bottom" ><img src="/image_1/dot_02.gif" width="1" height="76" /></td><?}?>
											<?}//-- colsEnd?>
		                </tr>
                   	<?if($k!=$rows){?><tr><td height="10"></td></tr><?}?>
		              	<?}//-- rows End?>
		              	<tr><td height="30" align=center colspan=10><div align="center" id="paging"><? echo page_navAjax($TotalCount,$listSize,$PagePerList,$page,$option,"fnPageMemberPoint"); ?></div></td></tr>
			            </table>
<?
}
?>