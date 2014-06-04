<?php
	 if(empty($_COOKIE['openID']))
	 {
		 exit();
	 }
	  require_once("header.php");
	  /*
	  考勤高级管理
	  */
?>

<!-- <script type="text/javascript" src="tablecloth/tablecloth.js"></script>  -->

<style type="text/css">
th{
	color:blue;
}
</style>
<script type="text/javascript">

       
    function selectstuNameCheck(obj)
    {
		JSSetCookie("stuName",obj.value);
		JSDeleteCookie("stuClassName");
        window.location = "fyStuOnClass.php?stuName=" + obj.value;
    }
	function selectstuClassNameCheck(obj)
    {
        //SetCookie("cookie_videoType",document.getElementById("videoTypeSelectNameID").value);
        //window.location = "videoListPage.php?videoType=" + document.getElementById("videoTypeSelectNameID").value;
		 JSSetCookie("stuClassName",obj.value);
		 JSDeleteCookie("stuName");
		 window.location = "fyStuOnClass.php?stuClassName=" + obj.value;
    }
	window.onload=function()
    {
        //页面加载完成后执行以下表达式
        //initialSomeDocValues();
        var deva = JSGetCookie("stuName");
        if(deva)
            document.getElementById("stuNameID").value = deva;
        else
            document.getElementById("stuNameID").index = 0;
		
		var deva2 = JSGetCookie("stuClassName");
        if(deva2)
            document.getElementById("stuClassNameID").value = deva2;
        else
            document.getElementById("stuClassNameID").index = 0;
    }
   
   </script>
<div  class="jotitle">
  学员考勤签到
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  下面列出所有学员考勤签到情况(走微信流程的),可以根据姓名和班级筛选。
</div>
<div style="padding-left:5px;padding-right:5px " >
<?php
	/*
	某个老师下的学生请假情况
	*/	

	   echo   "<table><tr>
	           <th><select name=\"stuName\" id=\"stuNameID\" style=\"width:60\" onchange= \"selectstuNameCheck(this)\"><option value=\"姓名\" >姓名</option>";
     
	    $opetionClassNameArray = array();
		$link = getDBLink();
		$sql = "SELECT openID,name,className FROM studentregister GROUP BY openID ORDER BY openID"; 
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				echo "<option value=\"{$row['name']}\" >{$row['name']}</option>";
				//
				if(! in_array($row["className"], $opetionClassNameArray))
				{
					array_push($opetionClassNameArray,$row["className"]);
				}
			}
			mysqli_free_result($result);
			//
		}
	   echo  "</select></th><th><select name=\"stuClassName\"
       id=\"stuClassNameID\" style=\"width:60\" onchange= \"selectstuClassNameCheck(this)\"><option value=\"班级\" >班级</option>";
		foreach ($opetionClassNameArray as $key => $value)
		{
			echo "<option value=\"$value\" >$value</option>"; 
		}
	   echo  "</select></th><th>签到时间</th><th>签到距离</th><th>TAG</th></tr>";
		
		//详细记录了
		$sql = "SELECT  name, className,time,distance FROM studentregister WHERE name !=\"\" " ; 
		if(! empty($_GET["stuName"]) && strcmp($_GET["stuName"],"姓名") != 0)
		{
			$sql .=  "AND name = \"{$_GET['stuName']}\"";
		}
		if(! empty($_GET["stuClassName"]) && strcmp($_GET["stuClassName"],"班级") != 0)
		{
			$sql .=  "AND className = \"{$_GET['stuClassName']}\"";
		}
		$sql .= "ORDER BY name" ;
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
		   //记录从新开始 	
		   //mysqli_data_seek($result,0);
		   //签到异常时间
		   	$time1_1  = date("H:i:s",strtotime("09:00:00"));
			$time1_2  = date("H:i:s",strtotime("12:00:00"));
			$time2_1  = date("H:i:s",strtotime("17:00:00"));
		   while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			 {
			 		$color   = "green";
					$hintMsg = "正常";
					$siTime = date("H:i:s",strtotime($row['time']));  
					if($siTime > $time1_1 && $siTime < $time2_1) 
					{
						//异常
						$color = "red";
						$hintMsg = "异常";
					}
					
					echo "<tr><td width=\"60\">{$row['name']}</td><td width=\"30\">{$row['className']}</td><td width=\"100\"><font color=\"$color\">{$row['time']}</font></td><td>{$row['distance']}</td><td><font color=\"$color\">$hintMsg</font></td></tr> ";
			 }
			 mysqli_free_result($result);
		}
		
		echo "</table></div>";
	
	    echo "<div style=\"text-align:center\"> <a href=\"fyAdmin.php\"><img src=\"images/toAdmin.png\" /></a></div>";
		
	    require_once("footer.php");
?>
