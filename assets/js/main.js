function check()
{
	var userName = document.getElementById('name').value;
	
	console.log(userName);
	
	$.ajax({  
		type: "POST",   //提交的方法
		datatype:"json",
		url:"count_point.php", //提交的地址  
		data:
		{
			name:userName
		},
		success: function(data) {  //成功
			console.log(data.state);  //就将返回的数据显示出来
			if (data.state == 'success')
			{
				layui.layer.msg('签到成功',{time:1000});
				var infoData = JSON.parse(data.return);
				
				var str = "连续签到 "+infoData['signin_day']+" 天, 积分 "+infoData['points']+" 分";
				document.getElementById('info').innerHTML = str;
			}
			else if(data.state == 'noexist')
			{
				document.getElementById('name').value = "";
				layui.layer.alert('无此用户');
			}
			else if(data.state == 'alreadySign')
			{
				document.getElementById('name').value = "";
				layui.layer.alert('今日已签到');
			}
		}
	});
}

function pointSearch()
{
	var userName = document.getElementById('name').value;
	
	console.log(userName);
	
	var str = "javascript:location.href='signin_record.php?name="+encodeURI(userName)+"'";
					
	layui.layer.msg('正在跳转页面',{time:5000});
	setTimeout(str, 1000); 
}