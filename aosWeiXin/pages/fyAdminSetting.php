<?php
	 if(empty($_COOKIE['openID']))
	 {
		 exit();
	 }
	 
	  require_once("header.php");
	  /*
	  adminSetting 管理
	  */
	   //表单提交 当前页 处理
	  if(!empty($_POST['SQLName']))
	  {
	     $link = getDBLink();
         @mysqli_query($link,$_POST['SQLName']);
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

    function checkMBtn(obj)
    {
	    var msg =  "修改["+  obj.name   +  "]为管理老师或普通老师?";
        if (window.confirm(msg))
        {
            var deleSQL = "update teacherinfo set type = ABS(type-1)  WHERE openID = \""  +  obj.id  + "\"";
         
            document.getElementById("SQLNameID").value     = deleSQL;
            document.form1.submit();
            return true;
        }
        return false;
    }
   
   </script>
<div  class="jotitle">
   风云微信高级设置
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  <h4>
  风云微信网页高级管理设置，这里主要列出当前老师信息。主要功能修改老师权限等。高级管理主要功能有:招聘信息审核、管理老师权限等。高级管理为<font color="green">绿色★标记。</font>
  招生管理为<font color="red">红色特级权限★标记</font>
  <h4>
</div>
<div style="padding-left:5px;padding-right:5px " >
<?php
	/*
	
	*/	
	   
		$link = getDBLink();
		
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
				
			}
		}
		//是否 特级高级管理 老师
		$funs =  $isAnrothTeacherType<2 ?"disabled=\"disabled\"":"";
		
		echo   "<table><tr><th>ID</th><th>姓名</th><th>性别</th><th>班级</th><th>手机号码</th><th>功能</th></tr>";
	
		//详细记录了
		$sql = "SELECT openID,name,sex,className,tel,type FROM teacherinfo";
		//echo  $sql;
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
		   //记录从新开始 	
		   //mysqli_data_seek($result,0);
		   $rowid = 0;
		   while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			 {
			 		$rowid++;
					$state  = $row['type']?"green":"";
					$inStyle =  "color:$state";
					$labStyle =  "display:none";
					if($row['type'] >=2)
					{
						$state = "red";
					    $inStyle =  "display:none";
						$labStyle = "";
					}
					
					$hitMsg = $row['type']?"★":"";
					$sex    = $row['sex']==1?"女♀":"男♂";   //♀♂
					
					echo "<tr ><td width=\"5%\">$rowid</td><td><font color=\"$state\">{$row['name']}$hitMsg</font></td><td>$sex</td><td>{$row['className']}</td><td>{$row['tel']}</td><td><input type=\"button\" id=\"{$row['openID']}\" name=\"{$row['name']}\" value=\"M\" onClick=\"checkMBtn(this)\" style=\"$inStyle\"  $funs /><font color=\"$state\" style=\"$labStyle\">特级</font></td></tr> ";
			 }
			 mysqli_free_result($result);
		}
		
		echo "</table></div>";
		
			//假标记主要用于POSTSQL
		echo '<div style="display:none" ><form  name="form1"  method="post"><input type="text" name="SQLName" id="SQLNameID"/><input type="text" name="delFileName" id="delFileNameID" /><input type="text" name="locationPageName" id="locationPageNameID"/></form></div>';
        //下面显示 返回主页
	    echo "<div style=\"text-align:center\"> <a href=\"fyAdmin.php\"><img src=\"images/toAdmin.png\" /></a></div>";

	    require_once("footer.php");
?>
