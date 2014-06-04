<?PHP
    /*
     *
     *  通用工具方法类
     *
     */
    
    function logInfo($str)
    {
        file_put_contents('operateDataLog.txt',$str,FILE_APPEND);
    }
    
    function connectMysql()
    {
        $con = mysql_connect("localhost","root","FY2014");
        if (!$con)
        {
            $str = "can't connect mysql";
            logInfo($str);
            return NULL;
        }
        
        return $con;
    }
    
    function selectDB($dbName, $con)
    {
        $sel = mysql_select_db($dbName, $con);
        if(!$sel)
        {
            $str = "can't select db file";
            logInfo($str);
            return false;
        }
        
        mysql_query("set names utf8");
        
        return true;
    }
    
    //连接Mysql
    function linkMysql()
    {
        $con = connectMysql();
        if (!$con)
        {
            logInfo("check connect");
            return false;
        }
        
        $dbName = "fyweixin";
        
        if (!selectDB($dbName, $con))
        {
            logInfo("check db file name");
            return false;
        }
        
        return true;
    }
    
    //判断性别 0:女 1:男 2:未知
    function judgeSex($sex)
    {
        logInfo("sex");
        
        if ($sex == "男")
            return 1;
        else if ($sex == "女")
            return 0;
        else
            return 2;//其他
    }
    //根据性别标识 获取性别 0:女 1:男 2:未知
    function getSex($sex)
    {
        if ($sex == 0)
            return "女";
        if ($sex == 1)
            return "男";
        if ($sex == 2)
            return "未知性别";
    }
    
    //签到的目的地  经纬度
    define("DESTINATION_X", 31.296270);//纬度
    define("DESTINATION_Y", 120.666644);//经度
    
    //模拟枚举
    class TimeTag
    {
        var $NOT_TODAY = 0;
        var $AM = 1;
        var $PM = 2;
    }
    
    //时间戳转换成Y-M-D H:M:S
    function timestampToYYMMDDHHMMSS()
    {
        $t = time();
        
        $convertTime = date("Y-m-d H:i:s", $t);
        
        return $convertTime;
    }
    
    //计算签到地点与教室距离
    /*
     *  @desc 根据两点间的经纬度计算距离
     *  @param float $lat 纬度值
     *  @param float $lng 经度值
     *  返回数据的单位为“米”
     */
    function getDistance($lat1, $lng1, $lat2, $lng2)
    {
    		$earthRadius = 6367000.0000000000;
        
        //纬度角
        $lat = (double)$lat2 - (double)$lat1;
        //经度角
        $lng = (double)$lng2 - (double)$lng1;
        //两点之间的直线距离平方
        $len = 4 * $earthRadius * $earthRadius - 2 * $earthRadius * $earthRadius * cos($lng)  - 2 * $earthRadius * $earthRadius * cos($lat);
        //两点之间 处于同一平面上 所形成的弧度 对应的角度
        $a = cos((2 * $earthRadius * $earthRadius - $len) / 2 * $earthRadius * $earthRadius);
        //两点间的距离
        $s = 2 * $earthRadius * pi() * $a / 360.000000000;
        
        $s = $s / 10000.00000000;
        
        return abs($s);
    }
    
    //是否是当天
    function isToday($compareTime)
    {
        $dayTime = date("Y-m-d", time());
        $comTime = substr($compareTime, 0, 10);
        
//        file_put_contents('log.txt', $comTime . "is\n",FILE_APPEND);
        
        if (strtotime($comTime) == strtotime($dayTime))
            return true;
        else
            return false;
    }
    
    //判断已经注册的时间是  上午  或者  下午
    //日期格式要求为 如：2014-01-01 12:00:00
    function amORpm($compareTime)
    {
        $noonTime = "12:00:00";
        
        $timeTag = new TimeTag();
        
        if (strlen($compareTime) < 19)
            return $timeTag->NOT_TODAY;
        
        $comTime = substr($compareTime, 11, 8);
        
        if (strtotime($noonTime) > strtotime($comTime))
        {
            return $timeTag->AM;               //已注册的时间是在  上午
        }
        else if (strtotime($noonTime) <= strtotime($comTime))
        {
            return $timeTag->PM;               //已注册的时间是在  下午
        }
    }
    
    //比较两个时间大大小
    function compareTowTime($regTime, $compareTime, $bIsTotalTime=false)
    {
        if (strlen($regTime) < 19 || strlen($compareTime) < 19)
            return 0;//时间格式不对
        
        if (!$bIsTotalTime)
        {
            $reg = substr($regTime, 11, 8);
            $com = substr($compareTime, 11, 8);
            
            if(strtotime($reg) >= strtotime($com))
                return 1;
            else
                return 2;
        }
        else
        {
            if(strtotime($regTime) >= strtotime($compareTime))
                return 1;
            else
                return 2;
        }
    }
    
    
?>