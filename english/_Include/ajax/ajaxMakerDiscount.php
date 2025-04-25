<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	$sql = "select * from 2011_makerInfo where IDX='" . $MKidx . "'";
	$result = sql_query($sql);
	if($rs=sql_fetch_array($result))
	{
		
		for($i=10;$i<=90;$i=$i+10)
		{
			echo $rs["MKdiscount" . $i] . "#$#";
		}
		echo $rs["MKbuyMargin"] . "#$#";
		echo $rs["MKboxCount"] . "#$#";
		echo $rs["MKboxCount2"] . "#$#";
		echo $rs["MKdiscount"] . "#$#";
	}
	else
	{
		echo "##error##";
	}
	
?>