<?
	if(@$_POST["isMode"]=="totalAdmin")$isAdminMode=1;
	else $isManagerMode=1;
	
	include_once $_SERVER['DOCUMENT_ROOT']."/_common.php";
	ob_clean();
	
	if($mode=="edit")
	{
			//========== ��� ���� ��� =====================================
			//-- ���� ���� �ҷ�����
			$sql = "select a.OIDX,a.IDX as ORPIDX, b.IDX as PIDX,c.IDX as OPIDX, a.ORPcount,b.PstockCount,c.OPstock from 2011_orderProduct as a left join 2011_productInfo as b on a.PIDX = b.IDX left join 2011_productOption as c on a.ORPoption=c.IDX where a.IDX='" . $qIDX . "'";
			$result = sql_query($sql);
			if($rs = sql_fetch_array($result))
			{
				$oidx = $rs["OIDX"];

				$idx1 = $rs["ORPIDX"];
				$idx2 = $rs["PIDX"];
				$idx3 = $rs["OPIDX"];

				$v1 = $rs["ORPcount"];
				$v2 = $rs["PstockCount"];
				$v3 = $rs["OPstock"];

				//-- ��� ��/���� ���ϱ�
				$chVal = $nCount - $v1;

				//-- �ֹ����� ����
				$sql = "update 2011_orderProduct set ORPcount='" . $nCount . "' where IDX='" . $qIDX . "'";
				sql_query($sql);

				$sql = "update 2011_productInfo set PstockCount=PstockCount-(" . $chVal . ") where IDX='" . $idx2 . "'";
				sql_query($sql);

				if($idx3)
				{
					$sql = "update 2011_productOption set OPstock=OPstock-(" . $chVal . ") where IDX='" . $idx3 . "'";
					sql_query($sql);
				}
			}
			else
			{
				echo "##error##";
				exit();
			}

			//-- ���� �Ϸ�� ��� �ٿ�  1) ��ǰ ��ü ���   2) �ɼ� ���
			$sql = "update 2011_productInfo set PstockCount=PstockCount-" . $dbCAcount . " where IDX='" . $dbPIDX . "'";
			sql_query($sql);

			$sql = "update 2011_productOption set OPstock=OPstock-" . $dbCAcount . " where IDX='" . $dbOPIDX . "'";
			sql_query($sql);

			//-- ����Ʈ ���� ����
			$sql = "update 2011_memberPoint set POcount='" . $nCount . "' where OIDX='" . $oidx . "' and PIDX='" . $idx2 . "'";
			sql_query($sql);

		//============= ���� ���� =========================================

		echo "##OK##" . $nNum . "##" . $nCount;
	}
	else if($mode=="delete")
	{
		//============= ��ǰ ���� =========================================

		$sql = "select OIDX,PIDX,ORPcount,ORPoption from 2011_orderProduct where IDX='" . $qIDX . "'";
		$result = sql_query($sql);
		
		
		#-- ó��
		if($rs = sql_fetch_array($result))
		{
			#-- ��ǰ ���� ����
			$PIDX = $rs["PIDX"];
			$ORPcount = $rs["ORPcount"];
			$opIDX = $rs["ORPoption"];
			
			$sql = "update 2011_orderProduct set ORPdeleted=1 where IDX='" . $qIDX . "'";
			sql_query($sql);

			//-- ����Ʈ ���� ����
			$sql = "update 2011_memberPoint set POdeleted='1' where OIDX='" . $rs["OIDX"] . "' and PIDX='" . $rs["PIDX"] . "'";
			sql_query($sql);
			
			//-- ��� ����  1) ��ǰ ��ü ���   2) �ɼ� ���
			$sql = "update 2011_productInfo set PstockCount=PstockCount+" . $ORPcount . " where IDX='" . $PIDX . "'";
			sql_query($sql);
	
			$sql = "update 2011_productOption set OPstock=OPstock+" . $ORPcount . " where IDX='" . $opIDX . "'";
			sql_query($sql);
			
		}
		
		echo "##OK##" . $nNum . "##" . $nCount;
	}
	else if($mode=="totalPrice")
	{
		#-- �ڽ� �߼۰�
		$_TOT_PRICE1=0;
		$_TOT_COUNT1=0;
		$_DELIVERY_PRICE1=0;

		#-- Ÿ��ü �߼۰�
		$_TOT_PRICE2=0;
		$_TOT_COUNT2=0;
		$_DELIVERY_PRICE2=0;
	
		#============= �ֹ� ���θ� Ȯ��
		$sql = "select Oshop from 2011_orderInfo where IDX='" . $qIDX . "'";
		$rs = sql_fetch($sql);
		$Oshop=$rs["Oshop"];
		
		//============= �ֹ��� ��ǰ �Ѿ� / �� ���� / ��ۺ� ���� =========================================
		if($Oshop!=$masterSeller)
		{
			#=== �ٸ� ���θ����� �ֹ��� (��ü ��� ����)
			
			$default_sql = " SELECT b.ORPdeliveryUser, sum(b.ORPdeliveryPay) as ORPdeliveryPay,sum(ceil(ORPprice1-(ORPprice1/100*Pdiscount" . $shopArray[$Oshop . "LV"] . "))*b.ORPcount) as TOT_PRICE,sum(b.ORPcount) as TOT_COUNT,ODpay ";
			$default_sql.= " FROM 2011_orderProduct AS b ";
			$default_sql.= " LEFT JOIN 2011_orderInfo AS a ON a.IDX = b.OIDX ";
			$default_sql.= " LEFT JOIN (select sum(ODpay) as ODpay," . $qIDX . " as OIDX from 2011_orderDeliveryInfo where ##where1## AND OIDX = '" . $qIDX . "') as c on b.OIDX = c.OIDX ";
			$default_sql.= " LEFT JOIN 2011_productInfo AS d on b.PIDX=d.IDX ";
			$default_sql.= " WHERE b.ORPdeleted =a.Odeleted ";
			$default_sql.= " AND b.OIDX = '" . $qIDX . "' ";
			$default_sql.= " and ##where2## ";
			
		}
		else
		{
			#=== �ڽ��� ���θ����� �ֹ���
			
			#-- �⺻ SQL
			$default_sql = " SELECT b.ORPdeliveryUser, sum(b.ORPdeliveryPay) as ORPdeliveryPay,sum(b.ORPprice2*b.ORPcount) as TOT_PRICE,sum(b.ORPcount) as TOT_COUNT,ODpay ";
			$default_sql.= " FROM 2011_orderProduct AS b ";
			$default_sql.= " LEFT JOIN 2011_orderInfo AS a ON a.IDX = b.OIDX ";
			$default_sql.= " LEFT JOIN (select sum(ODpay) as ODpay," . $qIDX . " as OIDX from 2011_orderDeliveryInfo where ##where1## AND OIDX = '" . $qIDX . "') as c on b.OIDX = c.OIDX ";
			$default_sql.= " WHERE b.ORPdeleted =a.Odeleted ";
			$default_sql.= " AND b.OIDX = '" . $qIDX . "' ";
			$default_sql.= " and ##where2## ";
		}

		if($isAdminMode)
		{
			$sql1 = str_replace("##where1##","ODuser='" . $masterSeller . "'  ",$default_sql);
			$sql1 = str_replace("##where2##","ORPdeliveryUser='" . $masterSeller . "'  ",$sql1);
			
			$sql2 = str_replace("##where1##","ODuser<>'" . $masterSeller . "'  ",$default_sql);
			$sql2 = str_replace("##where2##","ORPdeliveryUser<>'" . $masterSeller . "'  ",$sql2);									
		}
		else if($isManagerMode)
		{
			$sql1 = str_replace("##where1##","ODuser='" . $MID . "'",$default_sql);
			$sql1 = str_replace("##where2##","ORPdeliveryUser='" . $MID . "'",$sql1);
			
			$sql2 = str_replace("##where1##","ODuser<>'" . $MID . "'",$default_sql);
			$sql2 = str_replace("##where2##","ORPdeliveryUser<>'" . $MID . "'",$sql2);
			
		}
		else
		{
			echo "Error!!!";
			exit();
		}
		
		$sql1.=" group by a.IDX ";
		$sql2.=" group by a.IDX ";
		
		
		$result1 = sql_query($sql1);
		$result2 = sql_query($sql2);
		
		$rs1 = sql_fetch_array($result1);
		$rs2 = sql_fetch_array($result2);
		
		#-- ���� �ʱ�ȭ
		$_TOT_PRICE1=0;
		$_TOT_COUNT1=0;
		$_DELIVERY_PRICE1=0;
		$_ADDDELIVERY_PRICE1=0;
		
		$_TOT_PRICE2=0;
		$_TOT_COUNT2=0;
		$_DELIVERY_PRICE2=0;
		$_ADDDELIVERY_PRICE2=0;
		
		if($rs1["TOT_PRICE"])$_TOT_PRICE1=$rs1["TOT_PRICE"];
		if($rs1["TOT_COUNT"])$_TOT_COUNT1=$rs1["TOT_COUNT"];
		if($rs1["ODpay"])$_DELIVERY_PRICE1=$rs1["ODpay"];
		if($rs1["ORPdeliveryPay"])$_ADDDELIVERY_PRICE1=$rs1["ORPdeliveryPay"];
		
		if($rs2["TOT_PRICE"])$_TOT_PRICE2=$rs2["TOT_PRICE"];
		if($rs2["TOT_COUNT"])$_TOT_COUNT2=$rs2["TOT_COUNT"];
		if($rs2["ODpay"])$_DELIVERY_PRICE2=$rs2["ODpay"];
		if($rs2["ORPdeliveryPay"])$_ADDDELIVERY_PRICE2=$rs2["ORPdeliveryPay"];


		echo "##OK##" . $_TOT_PRICE1 . "##" . $_TOT_COUNT1 . "##" . $_DELIVERY_PRICE1 . "##" . $_ADDDELIVERY_PRICE1;
		echo "##" . $_TOT_PRICE2 . "##" . $_TOT_COUNT2 . "##" . $_DELIVERY_PRICE2 . "##" . $_ADDDELIVERY_PRICE2;

	}
	else if($mode=="updateAddr")
	{
		//-- �ּ��� ����

		$inOpost = $post1 . "-" . $post2;

		$sql = "update 2011_orderInfo set Opost='" . $inOpost . "', Oaddr1='" . $addr1 . "' , Oaddr2='" . $addr2 . "', Oname = '" . $name . "', Otel = '". $tel . "', Ohp = '" . $hp . "' where IDX='" . $qIDX . "'";
		sql_query($sql);

		$returnStr = "[ " . $inOpost . " ] " . $addr1 . " " . $addr2;

		echo $mode . "##OK##" . $returnStr . "##". $name . "##" . $tel . "##" . $hp;

	}
	else if($mode=="updatePayment")
	{
		//-- ��������� ���˻��� ����
		$sql = "update 2011_orderPayment set Obill='" . $inObill . "', OdepositCheck='" . $inOdepositCheck . "' , OpayType='" . $inOpayType . "' where IDX='" . $payIDX . "'";
		sql_query($sql);

		//-- ���� �ֹ��� ��� ������ �Ϸ� �Ǿ����� üũ
		$nState = fnUpdatePayState($qIDX);

		//-- ù��° ��������� ����Ǹ� "�ְ������"�� ����
		if($grpNum==1)
		{
			$sql = "update 2011_orderInfo set OpayType='" . $inOpayType . "' where IDX = '" . $qIDX . "'";
			sql_query($sql);
		}
		echo $mode . "##OK##" . $nState;
	}
	else if($mode=="updateDelivery")
	{
		//-- ��¥ ��ȯ
		if($inOdeliveryDate)
		{
			$t = explode("-",$inOdeliveryDate);
			$TimeStamp = mktime(0,0,0,$t[1],$t[2],$t[0]);
		}

		//-- ��۰����� ���˻��� ����
		$sql = "update 2011_orderInfo set Obreakdown='" . $inObreakdown . "' where IDX='" . $qIDX . "'";
		sql_query($sql);
		
		//$sql = "update 2011_orderDeliveryInfo set ODstate ='" . $inOstate . "', ODcom='" . $inOdeliveryCom . "' , ODcode='" . $inOdeliveryCode . "' , ODdate='" . $TimeStamp . "' where OIDX='" . $qIDX . "' and ODuser='" . $MID . "'";
		
		if($inOstate>=3)$ODorner = $MID;
		else $ODorner = "";
		
		if($isAdminMode)$sql = "update 2011_orderDeliveryInfo set ODstate ='" . $inOstate . "', ODcom='" . $inOdeliveryCom . "' , ODcode='" . $inOdeliveryCode . "' , ODdate='" . $TimeStamp . "',ODorner='" . $ODorner . "' where OIDX='" . $qIDX . "' and ODuser='" . $masterSeller . "'";				
		else if($isManagerMode)$sql = "update 2011_orderDeliveryInfo set ODstate ='" . $inOstate . "', ODcom='" . $inOdeliveryCom . "' , ODcode='" . $inOdeliveryCode . "' , ODdate='" . $TimeStamp . "',ODorner='" . $ODorner . "' where OIDX='" . $qIDX . "' and ODuser='" . $MID . "'";
				
		sql_query($sql);

		//-- Point ���º��� (�����Ϸ� ó�� // ������� ó��)
		if($inOstate==4)$sql = "update 2011_memberPoint set POstate=2 where OIDX='" . $qIDX . "' and POtype=1";
		else if($inOstate!=4)$sql = "update 2011_memberPoint set POstate=1 where OIDX='" . $qIDX . "' and POtype=1";
		sql_query($sql);
		

		if($inOstate==4)$sql = "select * from 2011_orderProduct where OIDX='" . $qIDX . "' and ORPcountCheck=1  and ORPdeleted=0";
		else $sql = "select * from 2011_orderProduct where OIDX='" . $qIDX . "' and ORPcountCheck=2  and ORPdeleted=0";
		
		$result = sql_query($sql);
		while($rs = sql_fetch_array($result))
		{
			
			$dbIDX = $rs["IDX"];
			$dbPIDX = $rs["PIDX"];
			$dbORPcount = $rs["ORPcount"];
			$dbORPoption = $rs["ORPoption"];

			if($inOstate==4)
			{
				if($dbORPoption)
				{
					#-- ���� ��� Ȯ��
					$sql = "select OPstock from 2011_productOption where IDX='" . $dbORPoption . "'";
					$stockRs = sql_fetch($sql);
					$nStock = $stockRs[OPstock];
						
					$sql = "update 2011_productOption set OPstock=OPstock-" . $dbORPcount . " where IDX='" . $dbORPoption . "'";
					sql_query($sql);
					
					#-- �Ǹż��� ������Ʈ
					$sql3 = "update 2011_productOption set OPsellCount=OPsellCount + " . $dbORPcount . " where IDX='" . $dbORPoption . "'";
					sql_query($sql3);
						
				}
				else
				{
					#-- ���� ��� Ȯ��
					$sql = "select PstockCount from 2011_productInfo where IDX='" . $dbPIDX . "'";
					$stockRs = sql_fetch($sql);
					$nStock = $stockRs[PstockCount];
				}
				
				$sql = "update 2011_productInfo set PstockCount=PstockCount-" . $dbORPcount . " where IDX='" . $dbPIDX . "'";
				sql_query($sql);			
				
				$sql = "update 2011_orderProduct set ORPcountCheck=2 where IDX='" . $dbIDX . "'";
				sql_query($sql);
				
				#-- �Ǹż��� ������Ʈ
				$sql3 = "update 2011_productInfo set PsellCount=PsellCount + " . $dbORPcount . " where IDX='" . $dbPIDX . "'";
				sql_query($sql3);
				
				$sql = "insert into 2011_LogStock (PIDX,OPIDX,MID,LSOIDX,LScount,LSstock1,LSstock2,LSregdate) values(";
				$sql.= "'" . $dbPIDX . "',";
				$sql.= "'" . $dbORPoption . "',";
				$sql.= "'" . $MID . "',";
				$sql.= "'" . $qIDX . "',";				
				$sql.= "'" . (($dbORPcount)*-1) . "',";
				$sql.= "'" . $nStock . "',";
				$sql.= "'" . ($nStock + (($dbORPcount)*-1)) . "',";				
				$sql.= "'" . date(time()) . "')";
				sql_query($sql);
				
			}
			else
			{
		
				if($dbORPoption)
				{
					
					#-- ���� ��� Ȯ��
					$sql = "select OPstock from 2011_productOption where IDX='" . $dbORPoption . "'";
					$stockRs = sql_fetch($sql);
					$nStock = $stockRs[OPstock];
					
					$sql = "update 2011_productOption set OPstock=OPstock+" . $dbORPcount . " where IDX='" . $dbORPoption . "'";
					sql_query($sql);
					
					#-- �Ǹż��� ������Ʈ
					$sql3 = "update 2011_productOption set OPsellCount=OPsellCount - " . $dbORPcount . " where IDX='" . $dbORPoption . "'";
					sql_query($sql3);
					
				}
				else
				{
					#-- ���� ��� Ȯ��
					$sql = "select PstockCount from 2011_productInfo where IDX='" . $dbPIDX . "'";
					$stockRs = sql_fetch($sql);
					$nStock = $stockRs[PstockCount];	
				}
				
				$sql = "update 2011_productInfo set PstockCount=PstockCount+" . $dbORPcount . " where IDX='" . $dbPIDX . "'";
				sql_query($sql);	
				
				$sql = "update 2011_orderProduct set ORPcountCheck=1 where IDX='" . $dbIDX . "'";
				sql_query($sql);
				
				#-- �Ǹż��� ������Ʈ
				$sql3 = "update 2011_productInfo set PsellCount=PsellCount - " . $dbORPcount . " where IDX='" . $dbPIDX . "'";
				sql_query($sql3);
				
				
				$sql = "insert into 2011_LogStock (PIDX,OPIDX,MID,LSOIDX,LScount,LSstock1,LSstock2,LSregdate) values(";
				$sql.= "'" . $dbPIDX . "',";
				$sql.= "'" . $dbORPoption . "',";
				$sql.= "'" . $MID . "',";
				$sql.= "'" . $qIDX . "',";
				$sql.= "'" . ($dbORPcount) . "',";
				$sql.= "'" . $nStock . "',";
				$sql.= "'" . ($nStock + $dbORPcount) . "',";
				
				$sql.= "'" . date(time()) . "')";
				sql_query($sql);
				
			}
		}

		
		
		

		echo $mode . "##OK##";

	}
	else if($mode=="list")
	{
		
		#-- �ұ� ���� �����
?>		

		
		
<?		
	}
	else
	{
		echo "##error##";
	}
?>
