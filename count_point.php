<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'encoding.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'db/conn.php');
	require_once($_SERVER['DOCUMENT_ROOT']."mail/sendmail.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'db/jsonResponse.php');
	session_start();
	
	$sqlAttr = array();
	$signinData = array();
	$pointData = array();
	$respData = array();
	
	$sqlAttr['name']=$_POST['name'];
	
	$sql_1 = "SELECT *  FROM `t_member` WHERE `f_fullname` = '".$sqlAttr['name']."'";
	$rs_1 = $conn->execute($sql_1);
	
	if(count($rs_1) == 0)
	{
		$resp = array('state'=>'noexist');
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
		exit;
	}
	else
	{
		$userID = $rs_1[count($rs_1)-1]['id'];
		
		$sql_signin = "SELECT *  FROM `t_sign_in` WHERE `f_uid` = '".$userID."'";
		$signData = $conn->execute($sql_signin);
		
		if(count($signData) == 0)		//第一次签到
		{
			$sql_point = "SELECT * FROM `t_points_list` WHERE `f_item` LIKE 'signin' AND `f_subitem` LIKE '1'";
			$addPoint = $conn->execute($sql_point);
			
			$signinData['f_uid'] = $userID;
			$signinData['f_signin_day'] = 1;
			$signinData['f_last_date'] = date("Y-m-d");
			$conn->insert('t_sign_in', $signinData);
			
			$pointData['f_uid'] = $userID;
			$pointData['f_reason'] = '每日签到';
			$pointData['f_datetime'] = date("Y-m-d H:i:s");
			$pointData['f_points'] = $addPoint[0]['f_points'];
			$pointData['f_device'] = 'PC';
			$conn->insert('t_member_points_record', $pointData);
			
			$sql_total = "select sum(f_points) as sum from t_member_points_record where f_uid = ".$userID;
			$totalPoint = $conn->execute($sql_total);
			
			$respData['signin_day'] = $signinData['f_signin_day'];
			$respData['points'] = $totalPoint[0]['sum'];
		}
		else
		{
			$todayDate = date("Y-m-d");
			
			if (strtotime($todayDate) - strtotime($signData[0]['f_last_date']) == 0)		//今天已经签到过
			{
				$resp = array('state'=>'alreadySign');
				echo json_encode($resp, JSON_UNESCAPED_UNICODE);
				exit;
			}
			else if (strtotime($todayDate) - strtotime($signData[0]['f_last_date']) <= 86400)		//昨天有签到
			{
				$sql_point = "SELECT * FROM `t_points_list` WHERE `f_item` LIKE 'signin'";
				$addPoint = $conn->execute($sql_point);
				
				for($i = 0; $i < count($addPoint); $i++)
				{
					if($addPoint[$i]['f_subitem'] == $signData[0]['f_signin_day'] + 1 && $addPoint[$i]['f_subitem'] != 1)
					{
						$pointData['f_uid'] = $userID;
						$pointData['f_reason'] = '连续签到';
						$pointData['f_datetime'] = date("Y-m-d H:i:s");
						$pointData['f_points'] = $addPoint[$i]['f_points'];
						$pointData['f_device'] = 'PC';
						$conn->insert('t_member_points_record', $pointData);
					}
				}
				
				$days = $signData[0]['f_signin_day'] + 1;
			}
			else if (strtotime($todayDate) - strtotime($signData[0]['f_last_date']) > 86400)	//昨天没有签到
			{
				$days = 1;
			}
			
			$sql_point = "SELECT * FROM `t_points_list` WHERE `f_item` LIKE 'signin' AND `f_subitem` LIKE '1'";
			$addPoint = $conn->execute($sql_point);
			
			$signinData['f_signin_day'] = $days;
			$signinData['f_last_date'] = $todayDate;
			$place = "WHERE `t_sign_in`.`f_uid` = ".$userID;
			$conn->update('t_sign_in', $signinData, $place);
			
			$pointData['f_uid'] = $userID;
			$pointData['f_reason'] = '每日签到';
			$pointData['f_datetime'] = date("Y-m-d H:i:s");
			$pointData['f_points'] = $addPoint[0]['f_points'];
			$pointData['f_device'] = 'PC';
			$conn->insert('t_member_points_record', $pointData);
			
			$sql_total = "select sum(f_points) as sum from t_member_points_record where f_uid = ".$userID;
			$totalPoint = $conn->execute($sql_total);
			
			$respData['signin_day'] = $signinData['f_signin_day'];
			$respData['points'] = $totalPoint[0]['sum'];
		}
	}
	
	$return = json_encode($respData, JSON_UNESCAPED_UNICODE);
	
	$resp = array('state'=>'success','return'=>$return);
	
	echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	exit;
?>