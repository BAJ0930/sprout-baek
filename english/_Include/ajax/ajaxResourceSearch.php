<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
?>

	<table style='border:0px solid #ff0000;height:50px;width:100%' align=center>
		<tr>
			<td height=25 align=center >
				검색을 원하시는 재료 명을 입력해 주세요.
			</td>
		</tr>
		<tr>

			<td height=25 align=center >
				<input type='text' name='inKeyword' id='inKeyword' value='<?=$inKeyword?>' onkeydown='if(event.keyCode==13)fnAjaxSearchAction("inKeyword");'>

				<span class='button small icon' style='margin-top:3px;'><span class='refresh'></span><input type='button' value='검색' onclick='fnAjaxSearchAction("inKeyword")'  /></span>

			</td>

		</tr>
		<Tr>
		<td >

<?
		$inKeyword = iconv("EUC-KR","UTF-8",$inKeyword);
		
		$sql = "Select *  from 2011_resourceInfo";
		if($inKeyword)$sql.=" where RSkname like '%" . $inKeyword . "%' ";
		$sql.= " order by IDX asc ";
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

		while($rs=sql_fetch_array($makerResult))
		{
			echo "<Tr>";
			echo "	<td align=center style='height:25px;border-bottom:1px solid #efefef;'>";
			echo "	<a href='javascript:fnAjaxSearchSelected(\"" . $rs["IDX"] . "#$#" . $rs["RSkname"] . "\")'>" . $rs["RSkname"] . "</a>";
			echo "	</td>";
			echo "</tr>";
		}//-- end Loop


		echo "</table>";
		echo "</div> ";

?>

		</td>
	</tr>
	</table>