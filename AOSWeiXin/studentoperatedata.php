<?php
    /**
     * wechat student operate data
     */
    
    require_once("utileMethod.php");
    
    /*
     *
        学员基础信息类
     *
     */
    class StudentInfo
    {
        var $openID;
        var $name;
        var $tel;
        var $className;
        var $sex;
        var $specialty;
        var $school;
        var $weinxinNum;
        var $image;
        
        public function StudentInfo()
        {
            
        }
        //插入数据
        public function insterStudentInfo($openID, $name, $tel, $className, $sex, $specialty, $school, $weinxinNum, $image)
        {
            if ($this->checkStudentInfo($openID))
                return false;
            
            if (!linkMysql())
                return false;
            
            $tableName = "studentinfo";
            
            $newSex = judgeSex($sex);
            
            logInfo("$newSex\n");
            
            $result = mysql_query("INSERT INTO $tableName (openID, name, tel, className, sex, specialty, school, weinxinNum, image) VALUES (\"$openID\", \"$name\", \"$tel\", \"$className\", $newSex, \"$specialty\", \"$school\", \"$weinxinNum\", \"$image\")");
            
            if($result)
                return true;
            else
                return false;
            
            mysql_close($con);
            
        }
        
        //根据openID查询数据
        public function checkStudentInfo($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentinfo";
            
            $sqlSel="SELECT name, tel, className, sex, specialty, school, weinxinNum, image FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            if($num_rows == 1)
            {
                $row = mysql_fetch_array($result);
                return $row;
            }
            else
                return false;
            
//            mysql_close($con);
        }
        
        public function getStudentName($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentinfo";
            
            $sqlSel="SELECT name FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $studentName = "";
            
            $num_rows = mysql_num_rows($result);
            
            if($num_rows == 1)
            {
                $row = mysql_fetch_array($result);
                
                if ($row)
                    $studentName = $row['name'];
            }
            
            return $studentName;
            
            mysql_free_result($result);
            mysql_close($con);
        }
        
        //通过班级名 获取学生信息
        public function getStudentInfoByClassName($stdClassName)
        {
            $stuArray = array();
            
            if (!linkMysql())
                return false;
            
            $tableName = "studentinfo";
            
            $sqlSel="SELECT openID, name, tel, sex, specialty, school, weinxinNum, image FROM $tableName WHERE className='" . $stdClassName . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;

            $num_rows = mysql_num_rows($result);
            
            if($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
                    array_push($stuArray, $row);
                }
            }
            
            return $stuArray;
        }
    }
    
    /*
     *
        学员签到类
     *
     */
    
    define("FRONT_NUM",10);
    
    class StudentRegister
    {
        var $openID;
        var $name;
        var $time;
        var $location;
        
        public function StudentRegister()
        {
            
        }
        
        //插入签到人员数据
        public function insterStudentRegister($openID, $time, $location, $location_x, $location_y, $distance)
        {
            if (!linkMysql())
                return false;
            
//            $name = "";
//            $studentInfo = new StudentInfo();
//            $name = $studentInfo->getStudentName($openID);
//            
//            $className = checkStudentInfo
//            
            $studentInfo = new StudentInfo();
            $row = $studentInfo->checkStudentInfo($openID);
            
            $name = $row['name'];
            
            $className = $row['className'];
            
            $tableName = "studentregister";
            
            $result = mysql_query("INSERT INTO $tableName (openID, name, time, location, location_x, location_y, distance, className) VALUES (\"$openID\", \"$name\", \"$time\", \"$location\", \"$location_x\", \"$location_y\", \"$distance\", \"$className\")");
            
            if($result)
                return true;
            else
                return false;
            
            mysql_close($con);
        }
        
        //查询当前签到之人前n位签到之人
        public function checkRegisterStudentsBefore($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentregister";
            
            $sqlSel = "SELECT * from $tableName order by time asc";
            
            $result = mysql_query($sqlSel);
            
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $over = 0;
            
            $checkArray = array();
            
            if ($num_rows >= 1)
            {
                $row = mysql_fetch_array($result);
                
                while ($row && $over < FRONT_NUM)
                {
                    if (strcmp($row['openID'],$openID) == 0)
                    {
                        $row = mysql_fetch_array($result);
                        continue;
                    }
                        
                    //是否是今天签到?
                    if (!isToday($row['time']))
                    {
                        $row = mysql_fetch_array($result);
                        continue;
                    }
                    
                    //是否是下午签到？
                    $tag = $this->checkIsRegisted($openID);
                    if ($tag == 2)
                    {
                        //下午的话 排除上午时间
                        if (amORpm($row['time']) == 1)
                        {
                            $row = mysql_fetch_array($result);
                            continue;
                        }
                    }
                    
                    $this->removeSameElementFromArray($checkArray, $row);
                    
                    $over ++;
                    
                    array_push($checkArray, $row);
                    $row = mysql_fetch_array($result);
                }
            }
            
            return $checkArray;
            
            mysql_free_result($result);
            
            mysql_close($con);
        }
        //删除数组中相同的元素
        private function removeSameElementFromArray(&$array, $row)
        {
            if (count($array) == 0) return;
            
            for ($i = 0; $i < count($array); $i ++)
            {
                if (strcmp($array[$i]['openID'], $row['openID']) == 0)
                {
                    array_splice($array, $i, 1);
                }
            }
        }
        
        //是否已经签到
        public function checkIsRegisted($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentregister";
            
            $sqlSel = "SELECT name, time FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            if ($num_rows >= 1)
            {
                $todayArray = array();
                
                while ($row = mysql_fetch_array($result))
                {
                    $regTime = $row['time'];
                    
                    if (!isToday($regTime))
                    {
                        continue;
                    }
                    else
                    {
                        array_push($todayArray, $row);
                    }
                    
                    mysql_free_result($result);
                }
                
                if (count($todayArray) == 0)
                {
                    return 0;
                }
                else
                {
                    $curTime = date("Y-m-d H:i:s", time());
                    $timeTag = amORpm($curTime);
                    return $timeTag;
                }
                
            }
            else
                return 3;//代表没有取到数据
            
            mysql_close($con);
        }
        
        //获取今天 上午签到 的时间
        public function amRegisterTime($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentregister";
            
            $sqlSel = "SELECT name, time FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $todayArray = array();
            
            while ($row = mysql_fetch_array($result))
            {
                $regTime = $row['time'];
                
                if (!isToday($regTime))
                {
                    continue;
                }
                else
                {
                    array_push($todayArray, $row);
                }
                
                mysql_free_result($result);
            }
            
            foreach ($todayArray as $value)
            {
                $timeTag = amORpm($value['time']);
                if ($timeTag == 1)
                {
                    return $value['time'];
                }
            }
            
            mysql_close($con);
        }
        
        //通过字段名 获取学生考勤
        public function getStudentTotalRegTimeByField($field, $info)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentregister";
            
            $sqlSel = "SELECT openID, time FROM $tableName WHERE $field='" . $info . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $studentName = "";
            
            $num_rows = mysql_num_rows($result);
            /*
            $timeArray = array ();
            
            if($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
                    array_push($timeArray, $row['time']);
                }
            }
            
            return $timeArray;
            */
			
			$tArray = array ();
            
            if($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
                    array_push($tArray, $row);
                }
            }
            
            return $tArray;
			
            mysql_free_result($result);
            mysql_close($con);
        }
        
        
        //学员查看自己某天的签到
        function getCurDayRegister($openID, $curTime)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentregister";
            
            $sqlSel = "SELECT name, time FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $curRegTimeArray = array();
            
            if ($num_rows >= 1)
            {
                $shouldTimeAMBegin = strtotime(date("Y-m-d", $curTime) . " 08:00:00");
                $shouldTimeAMEnd = strtotime(date("Y-m-d", $curTime) . " 09:00:00");
                
                $shouldTimePM = strtotime(date("Y-m-d", $curTime) . " 17:00:00");
                
                while ($row = mysql_fetch_array($result))
                {
                    if (strtotime(date("Y-m-d", $curTime)) == strtotime(date("Y-m-d", $row['time'])))
                    {
//                        if ((compareTowTime($row['time'], $shouldTimeAMEnd) == 2 && compareTowTime($row['time'], $shouldTimeAMBegin) == 1)
//                            || compareTowTime($row['time'], $shouldTimePM) == 1)
//                        {
                            array_push($curRegTimeArray, $row);
//                        }
                    }
                }
            }
            
            return $curRegTimeArray;
        }
        
        //学员查看自己某一时间段里的签到
        function getAPeriodRegTime($openID, $fromTime, $toTime)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentregister";
            
            $sqlSel = "SELECT name, time FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $periodTimeArray = array();
            
            if ($num_rows >= 1)
            {
                $b = true;
                
                while ($row = mysql_fetch_array($result))
                {
                    if (compareTowTime($row['time'], $fromTime, $b) == 1 && compareTowTime($row['time'], $toTime, $b) == 2)
                    {
                        array_push($periodTimeArray, $row);
                    }
                }
            }
            
            return $periodTimeArray;
        }
    }
    
    /*
     *
        学员请假类
     *
     */
    class StudentLeave
    {
        var $openID;
        var $name;
        var $beginTime;
        var $endTime;
        var $reason;
        var $remark;
        var $state;
        
        public function StudentLeave()
        {
            
        }
        
        //插入请假人员数据
        public function insterStudentLeave($openID, $reason)
        {
            if (!linkMysql())
                return false;
            
            $studentInfo = new StudentInfo();
            $row = $studentInfo->checkStudentInfo($openID);
            
            $name = $row['name'];
            
            $className = $row['className'];
            
            $tableName = "studentleave";
            
            $result = mysql_query("INSERT INTO $tableName (openID, name, reason, className) VALUES (\"$openID\", \"$name\", \"$reason\", \"$className\")");
            
            if ($result)
                return true;
            else
                return false;
            
            mysql_close($con);
        }
        
        //获取学生当前的请假信息
        public function getStuCurrentLeaveInfo($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentleave";
            
            $sqlSel = "SELECT name, applyTime, reason FROM $tableName WHERE openID='" . $openID . "'" . " order by applyTime asc";
            
            $result = mysql_query($sqlSel);
            
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $info = array ();
            
            if ($num_rows >= 1)
            {
                $info = mysql_fetch_array($result);
                
                while ($row = mysql_fetch_array($result))
                {
                    $info = $row;
                }
                
                return $info;
            }
            
            return false;
            
            mysql_close($con);
        }
        
        //获取 此学生 所有请假信息
        public function getTheStuAllLeaveInfo($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentleave";
            
            $sqlSel = "SELECT name, applyTime, reason FROM $tableName WHERE openID='" . $openID . "'" . " order by applyTime asc";
            
            $result = mysql_query($sqlSel);
            
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $info = array ();
            
            if ($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
                    array_push($info, $row);
                }
                
                return $info;
            }
            
            return false;
            
            mysql_close($con);
        }
        
        //是否已请假过
        public function checkIsLeft($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentleave";
            
            $sqlSel = "SELECT name FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            if ($num_rows == 1)
            {
                mysql_free_result($result);
                return true;
            }
        }
    }
?>