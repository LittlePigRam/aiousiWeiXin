<?php
	
	  require_once("header.php");
	  /*
	  企业提供的 招聘信息
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

    //状态 审核通过
    function checkMBtn(obj)
    {
		var msg =  "审核通过或不通过:"+  obj.name  +  ",编号为:["  +  obj.id   +  "]?";
        if (window.confirm(msg))
        {
            var updateSQL = "update t_company_offer set status = ABS(status-1)   WHERE ID = \""  +  obj.id  + "\"";
            document.getElementById("SQLNameID").value     = updateSQL;
			document.getElementById("locationPageNameID").value = "fyCommpanyOffers.php";
            document.form1.submit();
            return true;
        }
        return false;
    }
	//删除
	function delMBtn(obj)
    {
	    var msg =  "确定删除["+ obj.name  +  "]的,编号为:["  +  obj.id   +  "]的这条记录?";
        if (window.confirm(msg))
        {
            var deleSQL = "delete from  t_company_offer  WHERE ID = "  +  obj.id ;
            
            document.getElementById("SQLNameID").value     = deleSQL;
			document.getElementById("locationPageNameID").value = "fyCommpanyOffers.php";
            document.form1.submit();
            return true;
        }
        return false;
    }
   
	
   
   </script>
<div  class="jotitle">
  企业招聘信息
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  企业招聘信息列表,同一个企业微信账号发布的信息可以自己删除,新增的发布信息需要学校管理人员审核，审核通过后发布出来。默认排序按照发布的时间先后。<font color="red">红色标记未审核</font>,<font color="green">绿色为审核通过</font>。
</div>
<div style="padding-left:5px;padding-right:5px " >
<?php
	
	$passHidd = true;   //display:block
	$delHidd  = true;   //display:block
	
	$isAnrothTeacherType = -1;  
	$openID = $_GET["openID"];   // if(empty($_COOKIE['openID']))
	if(!empty($_GET["openID"]))
	{
		$passHidd = true;     //display:block
		$delHidd  = false;    //display:block
	}
	else
	{
		$openID  = $_COOKIE['openID'];
	}
	//是否授权老师  empty($_COOKIE['openID']
	if(!empty($openID))
	{
		 
		$link   = getDBLink();
		$sql = "SELECT openID,type FROM teacherinfo WHERE openID = \"$openID\"" ;
		$result = mysqli_query($link,$sql);
		if($result && mysqli_num_rows($result) > 0)
		{
			 $row = mysqli_fetch_array($result,MYSQL_ASSOC);
			 $isAnrothTeacherType = $row["type"];
			 mysqli_free_result($result);
		}
		if($isAnrothTeacherType > 0)
		{
			$passHidd = false;   //display:block
			$delHidd  = false;   //display:block
		}
		else
		{
			//echo "<font color=\"red\">权限不够!</font>";
		}
	}
	
	
	$pasBtns =  $passHidd?"class=\"tdHidd\"":"";
	$delBtns =  $delHidd?"class=\"tdHidd\"":"";
	$funs =  ($passHidd && $delHidd)?"class=\"tdHidd\"":"";
	
	  
		//详细记录了
		$link   = getDBLink();
		$sql = "SELECT ID, openID,comName,offerMsg,remark,status,timeOn FROM t_company_offer WHERE status=0  ORDER BY timeOn DESC" ;  //0 审核通过
		if($isAnrothTeacherType >= 0)
		{
		 	$sql = "SELECT ID, openID,comName,offerMsg,remark,status,timeOn FROM t_company_offer ORDER BY timeOn DESC" ;
		}
		else
		{
			if(!empty($_GET["openID"]))
			{
			 	$sql = "SELECT ID, openID,comName,offerMsg,remark,status,timeOn FROM t_company_offer WHERE openID = \"{$_GET['openID']}\" ORDER BY timeOn DESC" ;
			}
		}	
		$result = mysqli_query($link,$sql);
		//while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
		//while($row = $result->fetch_array(MYSQL_ASSOC))
		if($result && mysqli_num_rows($result) > 0)
		{
		   while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
		   {
	
			$state   = $row['status']?"red":"green";  //1 还没有审核  0 成功审核
			$hitMsg  = $row['status']?"未审核":"已审核";
			
			echo   "<table><tr><td width=\"12%\">序号</td><td>{$row['ID']}[<font color=\"$state\">$hitMsg</font>]</td><td rowspan=\"5\" $funs><input type=\"button\" id=\"{$row['ID']}\" name=\"{$row['comName']}\" value=\"P\"  $pasBtns  onClick=\"checkMBtn(this)\"/><br/><input type=\"button\" id=\"{$row['ID']}\" name=\"{$row['comName']}\" value=\"D\" $delBtns  onClick=\"delMBtn(this)\"/></td></tr>
			               <tr><td>时间</td><td>{$row['timeOn']}</td></tr>
						   <tr><td>企业</td><td>{$row['comName']}</td></tr>
			               <tr><td>招聘</td><td>{$row['offerMsg']}</td></tr>
						   <tr><td>备注</td><td>{$row['remark']}</td></tr></table>";
		   }
			 //mysqli_free_result($result);
			 $result->close();
		}
		
		echo "</div>";
		
		
			//假标记主要用于POSTSQL
		echo '<div style="display:none" ><form  name="form1"  method="post"><input type="text" name="SQLName" id="SQLNameID"/><input type="text" name="delFileName" id="delFileNameID" /><input type="text" name="locationPageName" id="locationPageNameID"/></form></div>';
    
		
		//显示返回主页的
	    if($isAnrothTeacherType > -1)
		{
	    	echo "<div style=\"text-align:center\"> <a href=\"fyAdmin.php\"><img src=\"images/toAdmin.png\" /></a></div>";
		}
	    require_once("footer.php");
?>
