<?
	if(!$isAdminMode)$isManagerMode=1;
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
?>

	<table style='border:0px solid #ff0000;height:50px;width:100%' align=center>
		<tr>
			<td height=25 align=center >
				검색을 원하시는 제조사 혹은 브랜드 명을 입력해 주세요.
			</td>
		</tr>
		<tr>
			<td height=25 align=center >
				<input type='text' name='inKeyword' id='inKeyword' value='<?=$inKeyword?>' onkeydown='if(event.keyCode==13)fnAjaxSearchAction("inKeyword");'>
				<span class='button small icon' style='margin-top:3px;'><span class='refresh'></span><input type='button' value='검색' onclick='fnAjaxSearchAction("inKeyword")'  /></span>
			</td>
		</tr>
		<Tr>
		<td>

<?
		$sql = "Select a.*,b.MKkname  from 2011_brandInfo as a left join 2011_makerInfo as b on a.MKIDX = b.IDX left join 2011_makerSeller as c on b.IDX=c.MKIDX ";
		if($inKeyword)
		{
			$sql.=" where (BRkname like '%" . $inKeyword . "%' or MKkname like '%" . $inKeyword . "%')   ";			
			if(!$isAdminMode)$sql.= " and c.MSID='" . $MID . "'  ";	
		}
		else if($_SESSION["preBrandIDX"])
		{
			$sql.=" where a.IDX='" . $_SESSION["preBrandIDX"] . "'  ";
			if(!$isAdminMode)$sql.= " and c.MSID='" . $MID . "'  ";
		}
		else
		{
			if(!$isAdminMode)$sql.= " where c.MSID='" . $MID . "' ";
			else $sql.="where 1";
		}
		
		
		
		$sql.= " and MKshop='1000u' and  a.BRshop='1000u' and a.BRdeleted=0 and b.MKdeleted=0 order by MKkname asc,BRkname asc ";
		
		$makerResult = sql_query($sql);
		

		if(mysqli_num_rows($makerResult)<1)
		{
			echo "<div style='height:200px;overflow-x:hidden;overflow-y:scroll;overflow:hidden;'><table style='width:100%'>";
			echo "<tr><td align=center height=30> -검색결과가 없습니다-</td></tr>";
		}
		else
		{
			echo "<div style='height:200px;overflow-x:hidden;overflow-y:scroll;overflow:hidden;width:100%;border:0px solid red;'><table style='width:100%'>";
		}
		
		echo "<table width='100%' cellspacing=0 cellpadding=0 border=0>";
		echo "<tr><td width='50%' align=center style='height:20px;background-color:#efefef;font-weight:bold;'>제조사</td>";
		echo "<td width='50%' align=center  style='height:20px;background-color:#efefef;font-weight:bold;'>브랜드</td></tr>";

		while($rs=sql_fetch_array($makerResult))
		{
			echo "<Tr>";
			echo "	<td align=center style='width:50%;height:25px;border-bottom:1px solid #efefef;'>";
			//echo "	<a href='javascript:fnAjaxSearchSelected(\"" . $rs["IDX"] . "#$#" . $rs["BRkname"] . "\")'>" . $rs["MKkname"] . "</a>";
			echo "	" . $rs["MKkname"] . "</a>";
			echo "	</td>";
			echo "	<td align=center style='width:50%;height:25px;border-bottom:1px solid #efefef;'>";
			echo "	<a href='javascript:fnAjaxSearchSelected(\"" . $rs["IDX"] . "#$#" . $rs["BRkname"] . "\")'>" . $rs["BRkname"] . "</a>";
			echo "	</td>";
			echo "</tr>";
		}//-- end Loop


		echo "</table>";
		echo "</div> ";

?>

		</td>
	</tr>
	</table>