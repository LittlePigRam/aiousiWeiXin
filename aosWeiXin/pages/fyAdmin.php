<?php
    header('Content-type:text/html;charset=utf-8'); 
	require_once("../common.class.php");
	
	$openID = $_GET["openID"]; // if(empty($_COOKIE['openID']))
	if(empty($openID))
	{
		$openID = $_COOKIE['openID'];
		if(empty($openID))
		{
			echo "<script>alert('非法进入')</script>";
			exit();
		}
	}
	else
	{
		
		$link   = getDBLink();
		$sql = "SELECT openID,type FROM teacherinfo WHERE openID = \"$openID\"" ;
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
			 setcookie("openID", $openID);
		}
		else
		{
			echo "<script>alert('非法进入')</script>";
			exit();
		}
	}
	
	require_once("header.php");
?>

<style type="text/css">
img {
	height:100%;
	width: 100%;
}

</style>

  <div  class="jotitle">
    风云教育微信管理员设置
  </div>
  <div style="text-align:center"  >
    <table align="center">
      <tr>
        <td><a href="fyStuLeave.php"   target="_blank"> <img alt="请假" src="images/adminMenu1.png" /> </a></td>
        <td><a href="fyStuOnClass.php" target="_blank"> <img alt="考勤" src="images/adminMenu2.png" /> </a></td>
      </tr>
      <tr>
        <td><a href="fyStuScores.php" target="_blank"> <img alt="成绩" src="images/adminMenu3.png" /> </a></td>
        <td><a href="fyStuEnroll.php" target="_blank"> <img alt="招生" src="images/adminMenu4.png" /> </a></td>
      </tr>
       <tr>
        <td><a href="fyCommpanyOffers.php" target="_blank"> <img alt="招聘" src="images/adminMenu5.png" /> </a></td>
        <td><a href="fyAdminSetting.php"   target="_blank"> <img alt="管理员" src="images/adminMenu6.png" /> </a></td>
      </tr>
    </table>
  </div>
  <?php
	  require_once("footer.php");
?>