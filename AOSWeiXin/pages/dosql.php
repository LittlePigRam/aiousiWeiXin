<?php
    
    require_once("header.php");
  
    // 连接，选择数据库
    $link = getDBLink();
    $re   = @mysqli_query($link,$_POST['SQLName']);
	
    $prePage = $_POST['locationPageName'];
 
    if($re != FALSE)
    {
        echo "<script>window.location=\"$prePage\"; </script>";
    }
    else
    {
        echo "<script>window.location =\"error.php?msg=" . $_POST['SQLName'] . "\";</script>";
    }
    
    require_once("footer.php");
?>