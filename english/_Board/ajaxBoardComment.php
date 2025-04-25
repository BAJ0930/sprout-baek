<?
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();	
	//== Comment Process ========================================================	
	
	/*===========================================================================
	Save & Edit
	===========================================================================*/
	if($repType)
	{
		ob_clean();
		echo "##" . $bIDX . "##";
		
		//-- 비회원 접근 금지
		if(!$MID)
		{
			echo "required login";
		}
		else if($repType=="new")
		{
			
			//-- 새로운 댓글 번호 구하기
			$sql = "select max(Csort) as csort from 2011_boardComment where BIDX='" . $bIDX . "'";
			$CCrs = sql_fetch($sql);
			
			$nSort = $CCrs['csort']+1;
			
			
			//-- 새로운 댓글
			$sql = "insert into 2011_boardComment values('',";
			$sql.= "'" . $bIDX . "',";
			$sql.= "'" . $MID . "',";
			$sql.= "'" . $inCommentTxt . "',";
			$sql.= "'" . date(time()) . "',";
			$sql.= "'" . $nSort . "',";
			$sql.= "'" . $_SERVER['REMOTE_ADDR'] . "')";
			sql_query($sql);
			
			echo "OK";
			//-- 무조건 1페이지로
			echo "##1";
			exit();
			
		}
		else if($repType=="edit")
		{
			//-- 수정 처리
			//-- 수정 전 작성자와 현재 명령을 내린 사람과 동일한지 비교
			$sql = "select MID from 2011_boardComment  where BIDX='" . $bIDX . "' and IDX='" . $qIDX . "'";
			$Crs = sql_fetch($sql);
			if($Crs["MID"]!=$MID)
			{
				echo "fail id";
				echo "##" . $cPage;
				exit();	
			}
			
			$sql = "update 2011_boardComment set ";
			$sql.= "CTxt = '" . $inCommentTxt . "',";
			$sql.= "CuserIP = '" . $_SERVER['REMOTE_ADDR'] . "' ";
			$sql.= "where BIDX='" . $bIDX . "' and IDX='" . $qIDX . "'";
			sql_query($sql);
			
			echo "OK";
			//-- 해당 페이지로
			echo "##" . $cPage;
			exit();
			
		}
		else if($repType=="delete")
		{
			
			//-- 삭제 전 작성자와 현재 명령을 내린 사람과 동일한지 비교
			$sql = "select MID from 2011_boardComment  where BIDX='" . $bIDX . "' and IDX='" . $qIDX . "'";
			$Crs = sql_fetch($sql);
			if($Crs["MID"]!=$MID)
			{
				echo "fail id";
				echo "##" . $cPage;
				exit();	
			}
			
			//-- 넘어 갔으면 삭제
			$sql = "delete from 2011_boardComment  where BIDX='" . $bIDX . "' and IDX='" . $qIDX . "'";
			sql_query($sql);
			
			echo "OK";
			//-- 해당 페이지로
			echo "##" . $cPage;
			exit();
		}
		
		
		exit();
	}
	/*===========================================================================
	Delete
	===========================================================================*/
	
	
	
	/*===========================================================================
	Default Form Call
	===========================================================================*/
	$sql = "Select * from 2011_boardComment where BIDX='" . $bIDX . "' order by Csort desc";
	$Cresult = sql_query($sql);
	
?>


<?if($MID=="starpe")
{?>
<a href='javascript:fnCommentCall("<?=$bIDX?>","")'>새로고침</a>
<?}?>


<!--=== Comment Input Form ==============================================-->
<table width='100%' height=100 class=tbl style='border:1px solid #bfbfbf;'>
	<form name='CommentForm' id='CommentForm'>
	<tr>
		<td width=30% align=left style='padding-left:20px;font-weight:bold;'>Comment</td>
		<td width=50% align=right></td>
		<td width=20%></td>
	</tr>
	<TR>
		<td colspan=2 style='padding:10px;width:95%;'>
			<textarea name='inCommentTxt' id='inCommentTxt' style='width:100%;height:70px;border:1px solid #afafaf;background-color:#f3f3f3;color:#afafaf;' onclick='if(onCommentChange==0)this.value="";this.style.color="#3f3f3f";onCommentChange=1;' >내용을 입력해 주세요.</textarea>
		</td>
		<td>			
			<span class='button large icon' style='margin-top:3px;'><span class='add'></span><input type='button' value='댓글저장' onclick='fnCommentSave("","<?=$bIDX?>","new")'  /></span>
		
		</td>
	</tr>
	</form>
</table>


<!--=== Comment List Form ==============================================-->
<table width='100%' height=100 class=tbl style='border:0px;'>
	<form name='CommentListForm' id='CommentListForm'>
	<?
	while($Crs = sql_fetch_array($Cresult))
	{
	?>
	<tr>
		<td>
			<table width='100%' height='100%' class='tbl' style='background-color:#efefefl;border-bottom:1px solid #efefef;'>
				<tr><td><b style='font-size:15px;'><?=$Crs["MID"]?></b> (<?=date("Y-m-d H:i:s",$Crs["Cregdate"])?>)
					
					<?
					//-- 삭제 버튼 처리
					if($Crs["MID"]==$MID)
					{?>
						&nbsp;&nbsp;&nbsp;&nbsp;
					<a href='javascript:fnCommentEdit("<?=$Crs["IDX"]?>","<?=$bIDX?>","<?=$page?>")'>수정</a>
					&nbsp;&nbsp;
					<a href='javascript:fnCommentSave("<?=$Crs["IDX"]?>","<?=$bIDX?>","delete")'>삭제</a>
						
					<?}?>
					</td></tr>
				<Tr><td id='Ctxt<?=$Crs["IDX"]?>'><?=nl2br($Crs["CTxt"])?></td></tr>
			</table>
		</td>
	</tr>
	
	<?
	}
	?>

	
	
	</form>
</table>
