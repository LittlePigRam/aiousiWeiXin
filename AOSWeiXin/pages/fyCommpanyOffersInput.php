<?php
	
	  require_once("header.php");
	  /*
	  企业提供的 招聘信息
	  */
	$openID = $_GET["openID"]; // if(empty($_COOKIE['openID']))
	if(empty($openID))
	{
		echo "<script>alert('非法进入！！！请通过微信，然后获取url地址')</script>";
		exit();
	}
	
	//form 表单数据提交到本页处理
	if(!empty($_POST['comName'])   &&  !empty($_POST['offerInfoName']) )
	{
		$link = getDBLink();
		$sql  = "INSERT INTO t_company_offer(openID,comName,offerMsg,remark) VALUES 
		          (\"$openID\",\"{$_POST['comName']}\",\"{$_POST['offerInfoName']}\",\"{$_POST['remarkInfoName']}\")";
		mysqli_query($link,$sql);
		if(mysqli_affected_rows($link) == 1)
		{
			echo "<script>alert('输入成功！')</script>";
			//Header("Location: http://www.php.net"); 
			//header("Location:fyCommpanyOffers.php?openID=$openID");
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

	
	function checkForm()
    {
        //if(document.getElementById("comNameID").value)
        var name  = document.getElementById("comNameID").value;
        if(name==null || name.length < 2)
        {
            alert("请填写招聘企业名称！(>=2个字符哦)");
            return false;
        }
        
        var description  = document.getElementById("offerInfoNameID").value;
        if(description==null || description.length < 5)
        {
            alert("请填写招聘信息！(>5个字符哦)");
            return false;
        }
		
        return true;
    }
</script>
   
   
<div  class="jotitle">
  企业发布招聘信息
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  企业招聘信息列表,同一个企业微信账号发布的信息可以自己删除,新增的发布信息需要学校管理人员审核，审核通过后发布出来。默认排序按照发布的时间先后。如果输入文字较多，可以使用网页版微信。
</div>
<div style="padding-left:5px;padding-right:5px " >

  <form  method="POST" onsubmit="return checkForm()">
  <table><tr><th>企业名字</th></tr>
  <tr><td><input type="text" name="comName" id="comNameID"  style="width:100%"            /></td></tr>
  <tr><th>招聘信息</th></tr>
  <tr><td><textarea rows="8" name="offerInfoName" id="offerInfoNameID"  style="width:100%"></textarea></td></tr>
  <tr><th>备注信息</th></tr>
  <tr><td><textarea rows="5" name="remarkInfoName" id="remarkInfoNameID" style="width:100%"></textarea></td></tr>
  <tr><td><input type="submit" value="提  交" /></td></tr>
  </table>
  </form>  
  <!-- <a href="fyCommpanyOffers.php?openID">
  </div>    -->
		
		
<?php
	echo "<a href=\"fyCommpanyOffers.php?openID=$openID\">删除我的招聘</a></div>";

	require_once("footer.php");

?>
