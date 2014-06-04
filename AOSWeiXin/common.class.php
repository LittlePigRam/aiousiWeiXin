<?php
    
    /**
     * wechat common class
     */
	 require_once("utileMethod.php");
    
    //define("HTTP_SERVER", "http://mzm008."");
    
    function start_session($expire = 0)
    {
        if ($expire == 0) {
            $expire = ini_get('session.gc_maxlifetime');
        } else {
            ini_set('session.gc_maxlifetime', $expire);
        }
        
        if (empty($_COOKIE['PHPSESSID'])) {
            session_set_cookie_params($expire);
            session_start();
        } else {
            session_start();
            setcookie('PHPSESSID', session_id(), time() + $expire);
        }
    }
    //保存数据到t_msgs 表 type=0 表示报名留言，type＝1－机器对话
	function saveMsgsToDB($openID,$msg,$type=0)
	{
	 		$con = connectMysql();
			selectDB("fyweixin", $con);
			$sqlInsert = "INSERT INTO t_msgs(openID,msg,type) VALUES (\"$openID\",\"$msg\",\"$type\")";
			@mysql_query($sqlInsert);
	}
	//
	/**  获取目录下的文件 */
	function getDirFiles($directory)
	{
		$mydir = dir($directory);
		$files = array();
		while($file = $mydir->read())
		{
			if((is_dir("$directory/$file")) AND ($file!=".") AND ($file!=".."))
			{
				// 递归获取文件
				//echo "<li><font color=\"#ff00cc\"><b>$file</b></font></li>\n";
				//tree("$directory/$file");
			}
			else
			{
				//echo "<li>$file</li>\n";
				if(stripos($file,".png") !== false ||  stripos($file,".jpg") !== false)
					array_push($files,$file);
			}
		}
		$mydir->close();
		return $files;
	}
	
	
	/** 连接 数据库 */
	function getDBLink()
	{
		 // 连接，选择数据库
        $link = mysqli_connect('localhost', 'root', 'FY2014','fyweixin') or die('Could not connect: ' . mysqli_connect_error());
        mysqli_query($link,"set names utf8");
        
        return $link;
	}
    
?>