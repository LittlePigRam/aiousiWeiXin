<?php
	 if(empty($_COOKIE['openID']))
	 {
		 exit();
	 }
	  
	  require_once("header.php");
	  /*
	  成绩分数 高级管理
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
        window.location = "fyStuScores.php?stuName=" + obj.value;
    }
	function selectstuClassNameCheck(obj)
    {
        //SetCookie("cookie_videoType",document.getElementById("videoTypeSelectNameID").value);
        //window.location = "videoListPage.php?videoType=" + document.getElementById("videoTypeSelectNameID").value;
		 JSSetCookie("stuClassName",obj.value);
		 JSDeleteCookie("stuName");
		 window.location = "fyStuScores.php?stuClassName=" + obj.value;
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
学员成绩分数统计
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  <h4>
  下面列出所有学员成绩分数情况(需要excel导入,导入时请使用网页版微信),可以根据姓名和班级筛选。
  <h4>
  <a href="../InsterExcelForm.html">导入Excel成绩</a>
</div>
<div style="padding-left:5px;padding-right:5px " >
<?php
	/*
	*/	

	   echo   "<table><tr>
	           <th><select name=\"stuName\" id=\"stuNameID\" style=\"width:60\" onchange= \"selectstuNameCheck(this)\"><option value=\"姓名\" >姓名</option>";
     
	    $opetionClassNameArray = array();
		$link = getDBLink();
		$sql = "SELECT openID,name,className FROM studentscore GROUP BY openID ORDER BY openID"; 
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
	   echo  "</select></th><th>考试主题</th><th>成绩</th><th>时间</th></tr>";
		
		//详细记录了
		$sql = "SELECT  name, className,subject,score,time FROM studentscore WHERE name !=\"\" " ; 
		if(! empty($_GET["stuName"]) && strcmp($_GET["stuName"],"姓名") != 0)
		{
			$sql .=  "AND name = \"{$_GET['stuName']}\"";
		}
		if(! empty($_GET["stuClassName"]) && strcmp($_GET["stuClassName"],"班级") != 0)
		{
			$sql .=  "AND className = \"{$_GET['stuClassName']}\"";
		}
		$sql .= "ORDER BY className" ;
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
		   //记录从新开始 	
		   //mysqli_data_seek($result,0);
		   while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			 {
			 		$color   = "blue";
					$hintMsg = "OK";
					if($row['score'] < 60) 
					{
						$color = "red";
						$hintMsg = "BAD";
					}
					if($row['score'] >= 90) 
					{
						$color = "green";
						$hintMsg = "GOD";
					}
					
					
					echo "<tr><td width=\"60\">{$row['name']}</td><td width=\"30\">{$row['className']}</td><td width=\"100\">{$row['subject']}</td><td width=\"30\"><font color=\"$color\">{$row['score']}</font></td><td>{$row['time']}</td></tr> ";
			 }
			 mysqli_free_result($result);
		}
		
		echo "</table></div>";
	
	    echo "<div style=\"text-align:center\"> <a href=\"fyAdmin.php\"><img src=\"images/toAdmin.png\" /></a></div>";
		
	    require_once("footer.php");
?>
