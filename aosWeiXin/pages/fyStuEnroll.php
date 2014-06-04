<?php
      /*
	  招生 网页 高级管理
	  */
	 if(empty($_COOKIE['openID']))
	 {
		 exit();
	 }
	 
	 require_once("header.php");
	
	$link = getDBLink();
	  //form 表单数据提交到本页处理
	if(!empty($_POST['SQLName'])  )
	{
		$sql = $_POST['SQLName'];
		mysqli_query($link,$sql);
	}
	 
	  $openID  = $_COOKIE['openID'];
	  //是否授权老师  empty($_COOKIE['openID']
	  $isAnrothTeacherType = -1;
	  if(!empty($openID))
	  {
		  $sql = "SELECT openID,type FROM teacherinfo WHERE openID = \"$openID\"" ;
		  $result = mysqli_query($link,$sql);
		  if($result && mysqli_num_rows($result) > 0)
		  {
			  $row = mysqli_fetch_array($result,MYSQL_ASSOC);
			  $isAnrothTeacherType = $row["type"];
			  /*
			  if($isAnrothTeacherType < 2)
			  {
				  echo "<script>alert('权限不够！')</script>";
				  exit();
			  }*/
		  }
	  }
?>

<!-- <script type="text/javascript" src="tablecloth/tablecloth.js"></script>  -->

<style type="text/css">
th{
	color:blue;
}
.tdHidd
{
	display:none;
}
</style>
<script type="text/javascript">

       
    function selectstatusNameCheck(obj)
    {
		JSSetCookie("statusName",obj.selectedIndex);
		window.location = "fyStuEnroll.php?statusName=" + obj.selectedIndex;
    }
	function selectstatusModiNameCheck(obj)
	{
		  //var msg =  "标记编号:["+  obj.id  + "]"  +  obj.name  + ",这条记录为:" + obj.value + "状态?";
          //if (window.confirm(msg))
          //{
              var sql = "update t_entername set status = "  +  obj.selectedIndex   +  " WHERE ID = " +   obj.id  ;
              alert(sql);
			  document.getElementById("SQLNameID").value  = sql;
			  document.form1.submit();
              //return true;
		  //}
		  //return false;
	}
	
	window.onload=function()
    {
        //页面加载完成后执行以下表达式
        //initialSomeDocValues();
		var deva = JSGetCookie("statusName");
        if(deva)
		{
		    //document.getElementById("statusNameID").value = devaNaem;
			document.getElementById("statusNameID").selectedIndex = deva;
		}
        else
            document.getElementById("statusNameID").index = 4;
    }
   
   </script>
<div  class="jotitle">
   招生管理
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  <h4>
  招生管理，可以标记记录为成功交费、待跟进、失败。该功能需要高级管理权限。<br />
  <font color="green">成功为绿色、</font><font color="red">失败为红色、</font><font color="blue">待更进为蓝色、</font>新报名默认黑色。
  <h4>
</div>
<div style="padding-left:5px;padding-right:5px " >
<?php
	/*
	*/	
	//招生为特有权限
	 if($isAnrothTeacherType < 2)
	 {
		  echo "<font color=\"red\">权限不够！</font>";
		  echo "<div style=\"text-align:center\"> <a href=\"fyAdmin.php\"><img src=\"images/toAdmin.png\" /></a></div>";
	      require_once("footer.php");
		  return;
	 }
	
	
	   echo   "<table><tr><th width=\"5%\">ID</th><th width=\"20%\">时间</th><th width=\"20%\">姓名</th><th>信息</th><th width=\"15%\"><select name=\"statusName\"
       id=\"statusNameID\" onchange= \"selectstatusNameCheck(this)\" ><option value=\"失 败\" >失 败</option><option value=\"新报名\" >新报名</option><option value=\"待跟进\" >待跟进</option><option value=\"已交费\" >已交费</option><option value=\"所 有\" >所 有</option></select></th></tr>";
		
		//详细记录了 1-新报名 0无效 2更进 3成功
		$sql = "SELECT ID,name,phone,qq,remark,timeOn,timeUp,status FROM t_entername  WHERE phone != \"\" ";
		if(isset($_GET["statusName"]) &&  $_GET["statusName"] != 4)
		{
			$sql .=  "AND status =" .  $_GET['statusName']; 
		}
		$sql .= " ORDER BY timeOn DESC" ;
		//echo  $sql;
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
		   //记录从新开始 	
		   //mysqli_data_seek($result,0);
		   while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			 {
			 		$state  =  "";
					$optionsel0 =   "";  //$optionsel =   "selected = \"selected\" ";
					$optionsel1 =   "";
					$optionsel2 =   "";
					$optionsel3 =   "";  
					if($row['status'] == 0)
					{
						$state  =  "red";
						$optionsel0 =   "selected = \"selected\" ";
					}
					if($row['status'] == 1)
					{
						$state  =  "";
						$optionsel1 =   "selected = \"selected\" ";
					}
					if($row['status'] == 2)
					{
						$state  =  "blue";
						$optionsel2 =   "selected = \"selected\" ";
					}
					if($row['status'] == 3)
					{
						$state  =  "green";
						$optionsel3 =   "selected = \"selected\" ";
					}
					$deMsg = "手机号:" . $row['phone'] . "<br/>QQ:" . $row['qq']  . "<br/>备注:" . $row['remark'];
				
					echo "<tr ><td>{$row['ID']}</td><td>{$row['timeOn']}</td><td><font color=\"$state\">{$row['name']}</font></td><td><font color=\"$state\">$deMsg</font></td><td><select name=\"{$row['name']}\"
       id=\"{$row['ID']}\" onchange= \"selectstatusModiNameCheck(this)\" style=\"color:$state\"><option  $optionsel0 value=\"失败\" >失败</option><option   $optionsel1  value=\"新报名\" >新报名</option><option $optionsel2  value=\"待跟进\" >待跟进</option><option $optionsel3  value=\"已交费\">已交费</option></select></td></tr> ";
					//
			 }
			 mysqli_free_result($result);
		}
		
		echo "</table></div>";
		//假标记主要用于POSTSQL
		echo '<div style="display:none"><form name="form1"  method="post"><input type="text" name="SQLName" id="SQLNameID"/><input type="text" name="delFileName" id="delFileNameID" /><input type="text" name="locationPageName" id="locationPageNameID"/></form></div>';
    
	    echo "<div style=\"text-align:center\"> <a href=\"fyAdmin.php\"><img src=\"images/toAdmin.png\" /></a></div>";

	    require_once("footer.php");
?>
