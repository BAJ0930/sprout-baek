<?
	#==================================================================================
	# 각종 리스트에서 사용되는 path 제어 및 넘어온 검색어 셋팅
	#==================================================================================
	/*
		1. Get 으로 넘어온 값을 우선으로 변수에 할당
		2. Get 으로 넘어온 값이 없을 경우 path 에 있는 값 할당.

		path의 구조 (통합 사용)
		page,order1,order2,listSize,s1,s2,s3,s4,s5,s6,s7,s8,s9,userVal1,userVal2,userVal3

		구분자 : ##
	*/
	#path 의 각 변수 명칭 설정
	$pathName = array("bid","page","order1","order2","listSize","viewType","cateIDX","s1","s2","s3","s4","s5","s6","s7","s8","s9","s10","s11","s12","s13","s14","s15");
	if($path!="")
	{
		$path = str_replace(" ","+",$path);
		$dePath = base64_decode($path);
		
		$dePath = explode("##",$dePath);


		if(sizeof($pathName) != sizeof($dePath)-1)
		{
			echo "path 설정이 잘못되었습니다.";
		}
		else
		{
			for($pl=0;$pl<sizeof($pathName);$pl++)
			{
				# 해당값이 실제로  Get으로 넘어왔는지 체크
				$gCheck = 0;
				foreach ($_GET as $gName => $gValue)
				{
					if($gName == $pathName[$pl])
					{
						$gCheck=1;
						break;
					}
				}

				if($gCheck)
				{
					${$pathName[$pl]} = $_GET[$pathName[$pl]];
				}
				else
				{
					${$pathName[$pl]} = $dePath[$pl];
				}
			}
		}
	}

	# 다시 패스 설정
	$pathTemp = "";
	for($pl=0;$pl<sizeof($pathName);$pl++)
	{
		$pathTemp.= ${$pathName[$pl]} ."##";
	}
	
 	$newpath = base64_encode($pathTemp);

 	# 기본 페이지
 	if(!$page)$page=1; 	
 	
 ?>