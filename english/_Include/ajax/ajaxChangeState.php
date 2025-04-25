<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();


	if($qIDX)
	{
		if($keyCode=="advice")
		{		
			$sql = "update 2011_memberInfo set Madvice='" . $newMode . "' where IDX='" . $qIDX . "'";
			sql_query($sql);
			echo "##OK##";
		}
		else if($keyCode=="gift")
		{
			$sql = "update 2011_memberGiftPayment set GFdepositCheck = '" . $newMode . "' where GFIDX='" . $qIDX . "'";
			sql_query($sql);
			$sql = "update 2011_memberGift set GFstate = '" . $newMode . "', GFregnm = '" . $_SESSION[__ADMIN_NAME___] . "' where IDX='" . $qIDX . "'";
			sql_query($sql);
			
			$sql = "update 2011_memberPoint set POstate = '" . $newMode . "' where PIDX='" . $qIDX . "'";
			sql_query($sql);
			
			echo "##OK##";
		}
		else if($keyCode=="gift2")
		{
			$sql = "update 2011_memberGift set GFconstate = '" . $newMode . "', GFconfirm = '" . $_SESSION[__ADMIN_NAME___] . "' where IDX='" . $qIDX . "'";
			sql_query($sql);
			
			$sql = "update 2011_memberPoint set POstate = '" . $newMode . "' where PIDX='" . $qIDX . "'";
			sql_query($sql);

			echo "##OK##";
		}
		else if($keyCode=="point")
		{
			$sql = "update 2011_memberPoint set POconstate = '" . $newMode . "', POconfirm = '" . $_SESSION[__ADMIN_NAME___] . "' where IDX='" . $qIDX . "'";
			sql_query($sql);
			echo "##OK##";
		}
		else
		{
			echo "##error##";
		}
	}
	else
	{
		echo "##error##";
	}


?>
