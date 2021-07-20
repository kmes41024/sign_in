<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'encoding.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'db/conn.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'include/function_debug.php');
    session_start();
	
	$userName = $_GET['name'];
	
	$sql_1 = "SELECT *  FROM `t_member` WHERE `f_fullname` = '".$userName."'";
	$rs_1 = $conn->execute($sql_1);
	
	$userID = $rs_1[count($rs_1) - 1]['id'];
	
	$sql_record = "SELECT *  FROM `t_member_points_record` WHERE `f_uid` = '".$userID."'";
	$record = $conn->execute($sql_record);
	
	$sql_total = "select sum(f_points) as sum from t_member_points_record where f_uid = ".$userID;
	$totalPoints = $conn->execute($sql_total);
	
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>resume_list</title>

  <link href="assets/css/root.css" rel="stylesheet">
  <link href="assets/css/record_style.css" rel="stylesheet">
</head>
<body>
	<div class="panel panel-default">
		<div class="panel-title" align = "center">
			<pre>姓名: <?php echo $userName; ?>  总积分: <?php echo $totalPoints[0]['sum'];?></pre>
		</div>
		<div class="panel-body table-responsive" style = "font-size:16px;">
			<table id="example0" class="table display" style = "font-size:16px;">
				<thead>
					<tr>
						<th class = "info">编号</th>
						<th class = "info">奖励来源</th>
						<th class = "info">积分</th>
						<th class = "info">日期时间</th>
						<th class = "info">登入设备</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i = 0; $i < count($record); $i++)
						{
							echo "<tr>";
							echo "<td>".($i+1)."</td>";
							echo "<td>".$record[$i]['f_reason']."</td>";
							echo "<td>".$record[$i]['f_points']."</td>";
							echo "<td>".$record[$i]['f_datetime']."</td>";
							echo "<td>".$record[$i]['f_device']."</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="modal" id="reviewModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width:1200px">
			<div class="modal-content">
				<div class="modal-header" align="center">
					<font style="font-family:'思源黑体 CN NORMAL';font-size:20px;color:#000" class="m-modal-title"><label id="reviewModel_title"></label></font>
				</div>
				<div class="modal-body" align="center">
					<iframe src=""></iframe>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-white" data-dismiss="modal" onclick="exitPreview()">离开</button>
				</div>
			</div>
		</div>
	</div>	
</body> 
	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/plugins.js"></script>
	<script type="text/javascript" src="assets/js/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="layui/layui.all.js"></script>
	<script type="text/javascript" src="assets/js/record_main.js"></script>
</html>