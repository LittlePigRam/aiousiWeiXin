<?php
	 if(empty($_COOKIE['openID']))
	 {
		 exit();
	 }
	  require_once("header.php");
	  /*
	  请假高级管理
	  */
?>

<!-- <script type="text/javascript" src="tablecloth/tablecloth.js"></script>  -->

<style type="text/css">
th{
	color:blue;
}
</style>
<script type="text/javascript">

    function checkMBtn(obj)
    {
	    var msg =  "标记"+  obj.name   +  "这条请假记录为异常或正常?";
        if (window.confirm(msg))
        {
            var deleSQL = "update studentleave set state = ABS(state-1)  WHERE ID = \""  +  obj.id  + "\"";
            
            document.getElementById("SQLNameID").value     = deleSQL;
			var deva = JSGetCookie("stuName");
            if(deva)
			{
				 document.getElementById("locationPageNameID").value = "fyStuLeave.php?stuName=" + deva;
			}
			var deva2 = JSGetCookie("stuClassName");
            if(deva2)
			{
				 document.getElementById("locationPageNameID").value = "fyStuLeave.php?stuClassName=" + deva2;
			}
			if(!deva && !deva2)
			{
				 document.getElementById("locationPageNameID").value = "fyStuLeave.php";
			}
			
            document.form1.submit();
            return true;
        }
        return false;
    }
       
    function selectstuNameCheck(obj)
    {
		JSSetCookie("stuName",obj.value);
		JSDeleteCookie("stuClassName");
        window.location = "fyStuLeave.php?stuName=" + obj.value;
    }
	function selectstuClassNameCheck(obj)
    {
        //SetCookie("cookie_videoType",document.getElementById("videoTypeSelectNameID").value);
        //window.location = "videoListPage.php?videoType=" + document.getElementById("videoTypeSelectNameID").value;
		 JSSetCookie("stuClassName",obj.value);
		 JSDeleteCookie("stuName");
		 window.location = "fyStuLeave.php?stuClassName=" + obj.value;
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
  学员请假统计
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  <h4>
  下面列出所有学员请假情况(走微信流程的),可以根据姓名和班级筛选，可以标记记录为正常或异常。蓝色为已处理(正常)
  <h4>
</div>
<div style="padding-left:5px;padding-right:5px " >
<?php
	/*
	某个老师下的学生请假情况
	*/	
	   
	   echo   "<table><tr><th>申请时间</th>
	           <th><select name=\"stuName\" id=\"stuNameID\" style=\"width:60\" onchange= \"selectstuNameCheck(this)\"><option value=\"姓名\" >姓名</option>";
      /*
	  <option value="社会新闻" >社会新闻</option>
      <option value="娱乐八卦" >娱乐八卦</option>
      <option value="卡通动画" >卡通动画</option>
      <option value="其他视频" >其他视频</option>
	  */
        $opetionClassNameArray = array();
		$link = getDBLink();
		$sql = "SELECT name, openID,className  FROM studentleave GROUP BY openID ORDER BY openID"; 
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
		echo "</th><th>请假事由</th><th>TAG</th></tr>";
		
		//详细记录了
		$sql = "SELECT ID, name, className,reason, applyTime,state	FROM studentleave WHERE name !=\"\" " ; 
		if(! empty($_GET["stuName"]) && strcmp($_GET["stuName"],"姓名") != 0)
		{
			$sql .=  "AND name = \"{$_GET['stuName']}\"";
		}
		if(! empty($_GET["stuClassName"]) && strcmp($_GET["stuClassName"],"班级") != 0)
		{
			$sql .=  "AND className = \"{$_GET['stuClassName']}\"";
		}
		$sql .= "ORDER BY applyTime DESC" ;
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
		   //记录从新开始 	
		   //mysqli_data_seek($result,0);
		   while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			 {
			 		$state = $row['state']?"blue":"";
					echo "<tr class=\"table-tr-bg\" ><td width=\"60\">{$row['applyTime']}</td><td width=\"60\"><font color=\"$state\">{$row['name']}</font></td><td width=\"30\">{$row['className']}</td><td><font color=\"$state\">{$row['reason']}</font></td><td><input type=\"button\" id=\"{$row['ID']}\" name=\"{$row['name']}\" value=\"M\" onClick=\"checkMBtn(this)\" style=\"color:$state\" /></td></tr> ";
			 }
			 mysqli_free_result($result);
		}
		
		echo "</table></div>";
		//假标记主要用于POSTSQL
		echo '<div style="display:none"><form action="dosql.php" name="form1"  method="post"><input type="text" name="SQLName" id="SQLNameID"/><input type="text" name="delFileName" id="delFileNameID" /><input type="text" name="locationPageName" id="locationPageNameID"/></form></div>';
    
		
		 echo "<div style=\"text-align:center\"> <a href=\"fyAdmin.php\"><img src=\"images/toAdmin.png\" /></a></div>";
		
	    require_once("footer.php");
?>
