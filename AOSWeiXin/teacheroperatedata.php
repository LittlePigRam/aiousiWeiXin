<?php
    /*
     * wechat teacher operate data
     */
    
    require_once("utileMethod.php");
    require_once("studentOperateData.php");
	require_once("studentScore.php");
    require_once("wx_messageRobot.php");
    
    /*
     *
     老师 基础信息类
     *
     */
    class TeacherInfo
    {
        var $openID;
        var $name;
        var $className;
        var $tel;
        var $Email;
        var $sex;
        var $weixinNum;
        var $image;
        
        //学生签到 数据模型类
        var $stuRegData;
        //学生信息 数据模型类
        var $stuInfoData;
        
        public function TeacherInfo()
        {
            $this->stuRegData = new StudentRegister();
            $this->stuInfoData = new StudentInfo();
        }
        
        //插入数据
        public function insterTeacherInfo($openID, $name, $className, $Email, $tel="18888888888", $sex="1", $weixinNum="88888888", $image="http://")
        {
            //            if ($this->checkTeacherInfo($openID))
            //                return false;
            
            if (!linkMysql())
                return false;
            
            $tableName = "teacherinfo";
            
            $newSex = judgeSex($sex);
            
            $result = mysql_query("INSERT INTO $tableName (openID, name, className, tel, Email, sex, weixinNum, image) VALUES (\"$openID\", \"$name\", \"$className\", \"$tel\", \"$Email\", $newSex, \"$weixinNum\", \"$image\")");
            
            if($result)
                return true;
            else
                return false;
            
            mysql_close($con);
        }
        
        //根据openID查询数据
        public function checkTeacherInfo($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "teacherinfo";
            
            $sqlSel="SELECT name, className, tel, Email, sex, weixinNum, image FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            if($num_rows >= 1)
            {
                mysql_free_result($result);
                return true;
            }
            else
                return false;
            
            //            mysql_close($con);
        }
        //获取 老师 信息
        public function getTeacherInfo($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "teacherinfo";
            
            $sqlSel="SELECT name, className, tel, Email, sex, weixinNum,type,image FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $classNameArray = array();
			
			$someClassNameArray = array();
            
            if($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
					if (strcmp($row['className'], 'ALL') == 0)
					{
							//获取当前正在开设的班级名字
							$someClassNameArray = $this->setAllClassNameForTeacher($someClassNameArray, 'ios9期', $row);
							array_push($classNameArray, $someClassNameArray);
							$someClassNameArray = $this->setAllClassNameForTeacher($someClassNameArray, 'JAVA32期', $row);
							array_push($classNameArray, $someClassNameArray);
							$someClassNameArray = $this->setAllClassNameForTeacher($someClassNameArray, 'JAVA33期', $row);
							array_push($classNameArray, $someClassNameArray);
							$someClassNameArray = $this->setAllClassNameForTeacher($someClassNameArray, 'JAVA34期', $row);
							array_push($classNameArray, $someClassNameArray);
							$someClassNameArray = $this->setAllClassNameForTeacher($someClassNameArray, '测试55期', $row);
							array_push($classNameArray, $someClassNameArray);
							$someClassNameArray = $this->setAllClassNameForTeacher($someClassNameArray, '测试56期', $row);
							array_push($classNameArray, $someClassNameArray);
					}
					else
						array_push($classNameArray, $row);
                }
            }
            
            return $classNameArray;
        }
		
		//当班级名为ALL的老师时  将此老师的管理班级设置为所有的班级
		public function setAllClassNameForTeacher(&$someClassNameArray, $className, $row)
		{
			$someClassNameArray = array(
						
						'name' => $row['name'],
						'className' => $className,
						'tel' => $row['tel'],
						'Email' => $row['Email'],
						'sex' => $row['sex'],
						'weixinNum' => $row['weixinNum'],
						'type' => $row['type'],
						'image' => $row['image'],
						'openID' => openID
				
				);
				
				return $someClassNameArray;
		}
		
        //根据字段 获取 老师 信息
        public function getTeacherInfoByField($field, $info)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "teacherinfo";
            
            $sqlSel="SELECT name, className, tel, Email, sex, weixinNum,type,image FROM $tableName WHERE $field='" . $info . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $classNameArray = array();
            
            if($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
                    array_push($classNameArray, $row);
                }
            }
            
            //当请假发送邮件时 是否有特权老师
            if (strcmp($field, "className") == 0)
            {
                $tempArray = $this->getPrivilegedUserByFiels("ALL");
                
                for ($i = 0; $i < count($tempArray); $i ++)
                {
                    array_push($classNameArray, $tempArray[$i]);
                }
            }
            
            return $classNameArray;
        }
        
        //获取 具有特权的老师 (班级字段的特权)
        public function getPrivilegedUserByFiels($field)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "teacherinfo";
            
            $sqlSel="SELECT name, className, tel, Email, sex, weixinNum,type,image FROM $tableName WHERE className='" . $field . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $privilegedUserArray = array();
            
            if($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
                    array_push($privilegedUserArray, $row);
                }
            }
            
            return $privilegedUserArray;
        }
        
		//查看本班最近一段时间的学生考勤 若时间参数为空 则
		public function getStudentNearRegister($openID, $prdTime)
		{
			$array = $this->getTeacherInfo($openID);

			$nearArray = array();

            foreach ($array as $value)
            {
                $stuArray = $this->stuInfoData->getStudentInfoByClassName($value['className']);
                
				array_push($nearArray, $value['className'] . ":");

                foreach ($stuArray as $stuValue)
                {
                    $stuTimeArray = $this->stuRegData->getStudentTotalRegTimeByField("openID", $stuValue['openID']);
                    
					array_push($nearArray, "★" . $stuValue['name']);

					foreach ( $stuTimeArray as $key=>$tValue)
                    {
						$timeValue = $tValue['time'];
						$curTime = strtotime(date("Y-m-d", time()));
		                $beginMonthTime = strtotime(date("Y-m", time()) . "-01");
                
                        $subTime = ($curTime - $beginMonthTime) / 3600;
                        if ($subTime < 30)
                        {
                            $beginMonthTime = $beginMonthTime - (30 - $subTime) * 3600;
                        }
                        
		                if (strtotime($timeValue) >= $beginMonthTime || strtotime($timeValue) <= $curTime)
						{
							array_push($nearArray, $timeValue);
						}
                    }
                }
            }

            $textMsg = "";

			if (count($nearArray) <= 1)
			{
				$textMsg .= ChatRobot::getFace("不高兴") . " " . "你的班级还没有学生签到哦。\n目测你们班级没学生啊！\n亲，果断快马加鞭招生的说。\n";
			}
			else
			{
				$lastStr = $nearArray[0];

				foreach ($nearArray as $value)
				{
					if (amORpm($value) == 1 && strpos($lastStr, "-"))
						$textMsg .= "\n";

					$lastStr = $value;

					/*if (strpos($lastStr, "-") && (strtotime(date("Y-m-d", strtotime($lastStr))) != strtotime(date("Y-m-d", strtotime($value)))))
					{
						$textMsg .= "\n";
						$lastStr = $value;
					}*/

					$textMsg .= $value . "\n";
				}
			}

            return $textMsg;
		}

        //获取老师 对应的班级 学生考勤
        public function getStudentRegister($openID)
        {
            $array = $this->getTeacherInfo($openID);
            
            $textMsg = "";
            
            foreach ($array as $value)
            {
                $textMsg .= $value['className'] . ":\n";
                
                $stuArray = $this->stuInfoData->getStudentInfoByClassName($value['className']);
                
                if (count($stuArray) == 0)
                {
                    $textMsg .= ChatRobot::getFace("不高兴") . " " . "你的班级还没有学生签到哦。\n目测你们班级没学生啊！\n亲，果断快马加鞭招生的说。\n";
					continue;
                }
                
                foreach ($stuArray as $stuValue)
                {
                    $textMsg .= "★" . $stuValue['name'] . "\n";
                    
                    $stuTimeArray = $this->stuRegData->getStudentTotalRegTimeByField("openID", $stuValue['openID']);
                    
					$lastStr = "";

					foreach ($stuTimeArray as $key=>$tValue)
                    {
						$timeValue = $tValue['time'];
						
                        $key = $key + 1;
                        
                        if (amORpm($timeValue) == 1  && strpos($lastStr, "-"))
                            $textMsg .= "\n";
                        
						$lastStr = $timeValue;

                        $textMsg .= $timeValue . "\n";
                    }
                }
            }
            
            return $textMsg;
        }
        
        //获取 学生异常签到时间 异常签到时间指：上午9：00以后签到，或者下午16：30：00之前签到
        public function getStuRegAbnormal($openID)
        {
            $array = $this->getTeacherInfo($openID);
            
            $textMsg = "";
            
            $tempStuArray = array ();
            $oneTotalTimeArray = array ();//所有人所有天数的array
            $dayTimeArray = array ();//某人所有天数的array
            $tempTimeArray = array ();//某人每一天的array
            $lastDay = "";
            
            foreach ($array as $value)
            {
                $textMsg .= $value['className'] . ":\n";
                
                $stuArray = $this->stuInfoData->getStudentInfoByClassName($value['className']);
                
                if (count($stuArray) == 0)
                {
                    $textMsg .= ChatRobot::getFace("不高兴") . " " . "你的班级还没有学生签到哦。\n目测你们班级没学生啊！\n亲，果断快马加鞭招生的说。\n";
					continue;
                }
                
				foreach ($stuArray as $stuValue)
                {
					$lastName = $stuValue['name'];
                    
                    $stuTimeArray = $this->stuRegData->getStudentTotalRegTimeByField("openID", $stuValue['openID']);
                    
                    array_push($tempStuArray, $stuValue);
					
					foreach ($stuTimeArray as $key=>$tValue)
                    {
						$timeValue = $tValue['time'];
                        if ($lastDay != date("Y-m-d", strtotime($timeValue)))
                        {
                            if (count($tempTimeArray) != 0)
                            {
                                array_push($dayTimeArray, $tempTimeArray);
                                $tempTimeArray = array();
                                $lastDay = date("Y-m-d", strtotime($timeValue));
                            }
                        }
                        
                        if ($key == 0)
                            $lastDay = date("Y-m-d", strtotime($timeValue));
                        
                        array_push($tempTimeArray, $timeValue);
                        
                        //加入 此人最后一天的数据
                        if ($key == (count($stuTimeArray) - 1))
                        {
                            if (count($tempTimeArray) != 0)
                            {
                                array_push($dayTimeArray, $tempTimeArray);
                                $tempTimeArray = array();
                            }
                        }
					}
					
                    array_push ($oneTotalTimeArray, $dayTimeArray);
                    $dayTimeArray = array ();
				}
			}

            $bAM = true;//早上条件是否满足
            $bPM = true;//下午条件是否满足
            $bAddName = false;//记录是否已经加入学生姓名
            
            foreach ($tempStuArray as $key=>$theStuValue)
            {
                $stuName = "★" . $theStuValue['name'] . "\n";
                $bAddName = false;
                foreach ($oneTotalTimeArray[$key] as $timeValue)
                {
                    $timeMsg = "";
                    $bAM = true;
                    $bPM = true;
                    
                    foreach ($timeValue as $value)
                    {
                        $timeMsg .= $value . "\n";
                        
                        if (!$bAM && !$bPM)
                            continue;
                        
                        if (compareTowTime($value, (date("Y-m-d", strtotime($value)) . " 09:00:00")) == 2 && $bAM == true)
                            $bAM = false;
                        if (compareTowTime($value, (date("Y-m-d", strtotime($value)) . " 16:30:00")) == 1 && $bPM == true)
                            $bPM = false;
                    }
                    
                    if ($bAM || $bPM)
                    {
                        if (!$bAddName)
                        {
                            $textMsg .= $stuName;
                            $bAddName = true;
                        }
                        
                        $textMsg .= $timeMsg;
                    }
                }
            }

			return $textMsg;
		}
		
		//获取老师正在观测的各班级学生成绩
		public function getAllStudentScoreInfo($openID)
		{
			$array = $this->getTeacherInfo($openID);
			
			$scoreText = "[学生成绩查询]\n";
			
			foreach ($array as $value)
            {
                $scoreText .= $value['className'] . ":\n";
                
                $stuArray = $this->stuInfoData->getStudentInfoByClassName($value['className']);
                
                if (count($stuArray) == 0)
                {
                    $scoreText .= " " . " " . " " . " " . ChatRobot::getFace("不高兴") . " " . $value['className'] . "班级还没有学生哦。\n";
					continue;
                }
				
				foreach ($stuArray as $stuValue)
				{
					$stuScoreInfo = new StudentScoreInfo();
                    $scoreArray = $stuScoreInfo->searchStuScoreInfo($stuValue['openID']);
					
					$scoreText .= "★" . $this->stuInfoData->getStudentName($stuValue['openID']) . "\n";
					
                    if (count($scoreArray) == 0)
					{
                        $scoreText .= "无成绩信息！\n";
						continue;
					}
                    
                    foreach ($scoreArray as $scoreValue)
                    {
                        $scoreText .= $scoreValue['time'] . " " . $scoreValue['subject'] . " " . $scoreValue['score'] . "分\n";
                    }
				}
			}
			
			return $scoreText;
		}
		
		//获取老师teaching各班级学生请假情况
		public function getAllStudentLeaveInfo($openID)
		{
			$array = $this->getTeacherInfo($openID);
			
			$leaveText = "[学生请假查询]\n";
			
			foreach ($array as $value)
            {
                $leaveText .= $value['className'] . ":\n";
                
                $stuArray = $this->stuInfoData->getStudentInfoByClassName($value['className']);
                
                if (count($stuArray) == 0)
                {
                    $leaveText .= " " . " " . " " . " " . ChatRobot::getFace("不高兴") . " " . $value['className'] . "班级还没有学生哦。\n";
					continue;
                }
				
				foreach ($stuArray as $stuValue)
				{
					$stuLeave = new StudentLeave();
                    $leaveArray = $stuLeave->getTheStuAllLeaveInfo($stuValue['openID']);
					
					$leaveText .= "★" . $this->stuInfoData->getStudentName($stuValue['openID']) . "\n";
					
                    if (count($leaveArray) == 0)
					{
                        $leaveText .= "无请假记录信息！\n";
						continue;
					}
                    
                    foreach ($leaveArray as $leaveValue)
                    {
                        $leaveText .= $leaveValue['applyTime'] . " " . $leaveValue['reason'] . "\n";
                    }
				}
			}
			return $leaveText;
		}
    }
    
?>