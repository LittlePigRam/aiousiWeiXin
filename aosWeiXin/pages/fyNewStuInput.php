<?php
	
	  require_once("header.php");
	  require_once("../studentLeave.php");
	  /*
	  招生 新学生报名 登记 输入
	  */
	
	//form 表单数据提交到本页处理
	if(!empty($_POST['stuName'])   &&  !empty($_POST['stuPhone']) )
	{
		$link = getDBLink();
		$sql  = "INSERT INTO t_entername(name,phone,qq,remark) VALUES 
		          (\"{$_POST['stuName']}\",\"{$_POST['stuPhone']}\",\"{$_POST['stuQQ']}\",\"{$_POST['remarkInfoName']}\")";
		mysqli_query($link,$sql);
		if(mysqli_affected_rows($link) == 1)
		{
			echo "<script>alert('报名成功! 我们会在24小时内联系你！谢谢')</script>";
			//Header("Location: http://www.php.net"); 
			//header("Location:fyCommpanyOffers.php?openID=$openID");
			//mzm add 直接发送
			$subject = "新学生报名啦～～";
			$message = "<h2>新学生报名啦～～<p>[" .  $_POST['stuName'] ."]刚刚提交了微信报名信息！</h2>";
			$to = "sunjh@fengyuntec.com";
	        sendEmail($subject,$message,$to);
			echo "<script>window.close();</script>";
		}
	}
	
?>

<!-- <script type="text/javascript" src="tablecloth/tablecloth.js"></script>  -->

<style type="text/css">
th{
	color:blue;
}
</style>
<script type="text/javascript">

	function checkMobile(obj)
	{ 
		var sMobile = obj; 
		if(!(/^1[0-9]{10}$/.test(sMobile)))
		{ 
			return false; 
		} 
		return true;
	} 
	function checkQQ(obj)
	{ 
		var snum = obj; 
		if(!(/^[0-9]{7,11}$/.test(snum)))
		{ 
			return false; 
		} 
		return true;
	} 
	
	
	
	function checkForm()
    {
        //if(document.getElementById("comNameID").value)
        var name  = document.getElementById("stuNameID").value;
        if(name==null || name.length < 2)
        {
            alert("请填写你的名字！(>=2个字符哦)");
            return false;
        }
        var mobileNO  = document.getElementById("stuPhoneID").value;
        if(! checkMobile(mobileNO))
        {
            alert("不是完整的11位手机号！"); 
            return false;
        }
		var qqNO  = document.getElementById("stuQQID").value;
        if(! checkQQ(qqNO))
        {
            alert("QQ号码不对哦！"); 
            return false;
        }
		
        return true;
    }
</script>
   
   
<div  class="jotitle">
  我要报名
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  我们对3人以上组团报名予以每人10%的优惠。对已报名学员成功推荐新学员予以10%和5%的优惠(优惠不叠加)。鼓励大家相互推荐！如果是推荐、组团、学校、专业等其他信息都可以写在备注信息内。
</div>
<div style="padding-left:5px;padding-right:5px " >

  <form  method="POST" onsubmit="return checkForm()">
  <table><tr><th>信息</th><th>内容</th></tr>
  <tr><td>你的姓名</td><td><input type="text" name="stuName"  id="stuNameID"  style="width:100%"            /></td></tr>
  <tr><td>手机号码</td><td><input type="text" name="stuPhone" id="stuPhoneID"  style="width:100%"            /></td></tr>
  <tr><td>QQ 号码</td><td><input type="text" name="stuQQ" id="stuQQID"  style="width:100%"            /></td></tr>
  <tr><td>备注信息</td><td><textarea rows="4" name="remarkInfoName" id="remarkInfoNameID" style="width:100%"></textarea></td></tr>
  <tr><td  colspan="2"><input type="submit" value="提  交" /></td></tr>
  </table>
  </form>  
  <!-- <a href="fyCommpanyOffers.php?openID">
  </div>    -->
		
		
<?php

	require_once("footer.php");

?>
