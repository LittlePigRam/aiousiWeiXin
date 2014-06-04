<?php

    /*
     *从excel导入学员成绩信息
     */
    
    header("Content-Type:text/html;charset=utf-8");
    
    require_once("php-excel-reader_jb51/excel_reader2.php");
//    require_once("utilemethod.php");
    
    echo "<meta http-equiv=\"Content-type\" content=\"charset=utf-8\">";
    
    if ($_FILES["file"]["error"] > 0)
    {
        die ("未选择任何要上传的数据文件，请重新选择！<br />" . "Error:" . $_FILES["file"]["error"]);
    }
    
    $data = new Spreadsheet_Excel_Reader();
    
    $data->setOutputEncoding('utf-8');
    
    echo $_FILES["file"]["tmp_name"] . "<br>";
    
    $data->read($_FILES["file"]["tmp_name"]);//$_FILES["file"]["temp_name"]
    
    $connect = mysql_connect("localhost","root","FY2014") or die("链接数据库失败");
    mysql_query("set names 'utf8'");
    mysql_select_db("fyweixin",$connect) or die (mysql_error());
    
//    if (!linkMysql())
//        return false;
    
    $tableName = "studentscore";
    
    error_reporting(E_ALL ^ E_NOTICE);
    
    for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++)
    {
        $a = $data->sheets[0]['cells'][$i][1];//取得第一列的数据
        $b = $data->sheets[0]['cells'][$i][2];//取得第二列的数据
        $c = $data->sheets[0]['cells'][$i][3];//取得第三列的数据
        $d = $data->sheets[0]['cells'][$i][4];//取得第四列的数据
        $e = $data->sheets[0]['cells'][$i][5];//取得第五列的数据
        $f = $data->sheets[0]['cells'][$i][6];//取得第六列的数据
        
		if (strcmp($a,"") == 0) continue;
		
        //$f = date("Y-m-d H:i:s" ,strtotime($f));
        
        $sql = "INSERT INTO $tableName (openID,name,className,subject,score,time) VALUES('$a','$b','$c','$d','$e','$f')";
        echo $sql.'<br />';
        $res = mysql_query($sql)  or die ("错误：$sql");
    }
    
    /*require_once ('Excel/reader.php');  // 应用导入excel的类
    
    
    
    $data = new Spreadsheet_Excel_Reader();  //实例化类
    
    
    $data->setOutputEncoding('utf-8');//设置编码
    
    $data->read($_FILES["file"]["name"]);//读取excel临时文件
    
    $connect = mysql_connect("localhost","root","secret") or die("链接数据库失败");
    mysql_query("set names 'utf8'");
    mysql_select_db("justForTest",$connect) or die (mysql_error());
    
    if ($data->sheets[0]['numRows']>0){ //判断excel里面的行数是不是大于0行 $data->sheets[0]['numRows']是excel的总行数  这里的$data->sheets[0]表示excel中的第一sheets
        
        for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {  //将execl数据插入数据库  $i表示从excel的第$i行开始读取
            $sql="insert into `user` (`name`,`score`,`class`) values(
            '{$data->sheets[0]['cells'][$i][1]}',  //$i是excel中的行号
            '{$data->sheets[0]['cells'][$i][2]}',
            '{$data->sheets[0]['cells'][$i][3]}'
            )";
            $res = mysql_query($sql)  or die ("错误：$sql");
        }
        
        
        
        if ($data->sheets[1]['numRows']>0){   //判断excel里面的行数是不是大于0行  $data->sheets[0]['numRows']是excel的总行数 这里的$data->sheets[1]表示第二个sheets
            for ($i = 2; $i <= $data->sheets[1]['numRows']; $i++) {  //将execl数据插入数据库  $i表示从excel的第$i行开始读取
                $sql="insert into `user` (`name`,`score`,`class`) values(
                '{$data->sheets[0]['cells'][$i][1]}',  //$i是excel中的行号
                '{$data->sheets[0]['cells'][$i][2]}',
                '{$data->sheets[0]['cells'][$i][3]}'
                )";
                $res = mysql_query($sql)  or die ("错误：$sql");
            }
        }
    }
    */
?>