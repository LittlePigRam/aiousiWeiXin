<?php
    /**
     * 风云微信的业务逻辑 类
     *
     
     *缓冲 用户的 OpenID and eventKey 表示该信息是否属于 命令消息
     *
     
     */
    require_once("common.class.php");
    require_once("studentOperateData.php");
    require_once("teacherOperateData.php");
	require_once("u_teacherOperateData.php");
    require_once("wx_sendMessage.php");
    require_once("utileMethod.php");
    //机器人对话
    require_once("wx_messageRobot.php");
    require_once("News.php");
    require_once("studentLeave.php");
    require_once("studentScore.php");
    //招生统计
	require_once("wx_enrollManager.php");
    
    class ClassMessageLogic
    {
        //收到的用户信息Object
        var $receiveMessage;
        var $openID;
        var $eventKey; //这个变量很关键 表示缓冲用户的点击菜单的命令 使用session
        
        //发送对象
        var $sendMsgObject;
        
        //数据模型对象
        var $stuInfoData;
        //学员签到 数据模型对象
        var $stuRegisterData;
        //学员请假 数据模型对象
        var $studentLeaveData;
        
        //老师数据模型对象
        var $teacherInfoData;
		//university老师数据模型对象
        var $u_teacherInfoData;
        
        function ClassMessageLogic($classMsgBody)
        {
            $this->receiveMessage = $classMsgBody;
            $this->openID = $classMsgBody->FromUserName;
            
            $this->sendMsgObject = new WechatSendMessage($classMsgBody); //WechatSendMessage
            //数据模型对象
            $this->stuInfoData = new StudentInfo();
            //学员签到 数据模型对象
            $this->stuRegisterData = new StudentRegister();
            //学员请假 数据模型对象
            $this->studentLeaveData = new StudentLeave();
            //老师数据模型对象
            $this->teacherInfoData = new TeacherInfo();
			//university老师数据模型对象
            $this->u_teacherInfoData = new U_TeacherInfo();
            
            //
            $this->eventKey = "";
        }
        
        //分析用户命令
        function anlayUserCommand()
        {
            //先分析一下是不是请求命令event 类型
            if(strcmp($this->receiveMessage->MsgType,"event") == 0)
            {
                if(strcmp($this->receiveMessage->Event,"subscribe") == 0)
				{
					//处理关注用户
					$enrollMana = new EnrollManager();
					$enrollMana->saveEnrollMember($this->openID);
					$msg = "你好，欢迎关注苏州风云教育。你的关注就是我们的荣幸" . ChatRobot::getFace("高兴")  . "有什么问题，直接问我吧...";
                    $this->sendMsgObject->sendMessageText($msg);
				}
				if(strcmp($this->receiveMessage->Event,"unsubscribe") == 0)
				{
					//处理 取消关注用户
					$enrollMana = new EnrollManager();
					$enrollMana->updateEnrollMember($this->openID);
				}
                
				if(! empty($this->receiveMessage->EventKey))
                {
					$this->dealwithCommandText();
                }
            }
            //文本类型 秘语注册学员信息
            if(strcmp($this->receiveMessage->MsgType,"text") == 0)
            {
				$contextTemp = strtoupper($this->receiveMessage->Content);
                
				//格式：FYWXREGS@学生姓名@性别@风云班级名字@手机号@微信号@学校@专业@FYWXREGS
                if(preg_match("/^FYWXREGS@(.*?)/",$contextTemp))
                {
                    if(!preg_match("/^FYWXREGS@(.*?)@(.*?)@(.*?)@(.*?)@(.*?)@(.*?)@(.*?)@FYWXREGS$/",$contextTemp))
                    {
                        $format = "FYWXREGS@学生姓名@性别@风云班级名字@手机号@微信号@学校@专业@FYWXREGS";
                        $msg = "[学员注册]\n" . ChatRobot::getFace("不高兴") ."对不起,学员注册失败!格式如下:\n" . $format;
                        $this->sendMsgObject->sendMessageText($msg);
                        return;
                    }
                    else
                    {
                        //
                        $regArray = explode("@",$contextTemp);
                        if($this->stuInfoData->insterStudentInfo($this->openID, $regArray[1], $regArray[4], $regArray[3], $regArray[2], $regArray[7], $regArray[6], $regArray[5], "http://"))
                        {
                            $msg = "[学员注册]\n恭喜你,学员注册成功!" . ChatRobot::getFace("高兴");
                            $this->sendMsgObject->sendMessageText($msg);
                        }
                        else
                        {
                            $msg = "[学员注册]\n" . ChatRobot::getFace("不高兴") ."对不起,学员注册失败!可能已经注册了,如需修改请联系老师.";
                            $this->sendMsgObject->sendMessageText($msg);
                        }
                        return;
                    }
                    return;
                }
                //文本类型 秘语注册老师信息
                //格式：FYWXREGT@老师姓名@风云班级名字@公司邮箱前缀(@前面)@FYWXREGT
                if(preg_match("/^FYWXREGT@(.*?)/",$contextTemp))
                {
                    if(!preg_match("/^FYWXREGT@(.*?)@(.*?)@([A-Z]{3,10})@FYWXREGT$/",$contextTemp))
                    {
                        $format = "FYWXREGT@老师姓名@风云班级名字@公司邮箱前缀@FYWXREGT";
                        $msg    = "[老师注册]\n" . ChatRobot::getFace("不高兴")."对不起,老师注册失败!格式如下:\n" . $format;
                        $this->sendMsgObject->sendMessageText($msg);
                        return;
                    }
                    else
                    {
                        //
                        $regArray = explode("@",$contextTemp);//$this->receiveMessage->Content);
                        
                        if ($this->teacherInfoData->insterTeacherInfo($this->openID, $regArray[1], $regArray[2], $regArray[3]))
                        {
                            $msg = "[老师注册]\n恭喜你,老师注册成功!" . ChatRobot::getFace("高兴");
                            $this->sendMsgObject->sendMessageText($msg);
                        }
                        
                        return;
                    }
                    return;
                }
				
				//大学老师专属注册入口
				//格式：FYWXREGUT!院校名!教师姓名!风云班级名字!手机号!邮箱!性别!微信号或者QQ号!FYWXREGUT
                if(preg_match("/^FYWXREGUT!(.*?)/",$contextTemp))
                {
                    if(!preg_match("/^FYWXREGUT!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!FYWXREGUT$/",$contextTemp))
                    {
                        $format = "FYWXREGUT!院校名!教师姓名!风云班级名字!手机号!邮箱!性别!微信号或者QQ号!FYWXREGUT";
                        $msg = "[大学老师专属注册入口]\n" . ChatRobot::getFace("不高兴") ."对不起,注册失败!格式如下:\n" . $format;
                        $this->sendMsgObject->sendMessageText($msg);
                        return;
                    }
                    else
                    {
                        //
                        $regArray = explode("!",$contextTemp);
                        if($this->u_teacherInfoData->insterTeacherInfo($this->openID, $regArray[1], $regArray[2], $regArray[3], $regArray[4], $regArray[5], $regArray[6], $regArray[7], "http://"))
                        {
                            $msg = "[大学老师专属注册入口]\n恭喜您,注册成功!\n您可在“学员&教务”菜单中查询您学校学生在风云教育的请假、成绩、考勤信息！" . ChatRobot::getFace("高兴");
                            $this->sendMsgObject->sendMessageText($msg);
                        }
                        else
                        {
                            $msg = "[大学老师专属注册入口]\n" . ChatRobot::getFace("不高兴") ."对不起,注册失败!可能已经注册了,如需修改请联系开发管理老师.\n开发者电话：18362775409（陆洋洋）";
                            $this->sendMsgObject->sendMessageText($msg);
                        }
                        return;
                    }
                    return;
                }
				
				
				//直接编辑命令查询 某学生的考勤记录  //KQCX@
				$contextTemp = strtoupper($this->receiveMessage->Content);
				if(preg_match("/^KQCX@(.{4,10})/",$contextTemp))
				{
					  $regArray = explode("@",$this->receiveMessage->Content);
					  $stuName  =  $regArray[1];
					  $msgText  = "[考勤记录]-" . $stuName . "\n";
					
					  $link   = getDBLink();
					  $sql = "SELECT name,time,distance FROM studentregister WHERE name = \"$stuName\" " ; 
					  $result = mysqli_query($link,$sql);
					  while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
					  {
						 $msgText .=   $row["time"] . "\n距离=" . $row["distance"] . "\n";
					  }
					  $this->sendMsgObject->sendMessageText($msgText);
				}
				
				//直接编辑命令查询 某学生的请假记录  //QJCX@
				//$contextTemp = strtoupper($this->receiveMessage->Content);
				if(preg_match("/^QJCX@(.{4,10})/",$contextTemp))
				{
					  $regArray = explode("@",$this->receiveMessage->Content);
					  $stuName  =  $regArray[1];
					  $msgText  = "[请假记录]-" . $stuName . "\n";
					
					  $link   = getDBLink();
					  $sql = "SELECT ID, name, className,reason, applyTime,state FROM studentleave WHERE name = \"$stuName\" " ; 
					  $result = mysqli_query($link,$sql);
					  while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
					  {
						 $stus =  $row["state"]?"[批准]":"[异常]"; 
						 $msgText .=   $row["applyTime"] . "\n事由:" . $row["reason"] .  $stus   .  "\n";
					  }
					  $this->sendMsgObject->sendMessageText($msgText);
				}
				
				
	            //特殊 如果用户直接NE 表示发送文字太多 分页发送
				if (file_exists("longTXT") == true &&  strcmp(strtoupper($this->receiveMessage->Content),"NE") == 0)  //strcmp(strtoupper($commandText),"B")==0)
				{
					$msgN = file_get_contents("longTXT");
					
				    $text = substr($msgN,650);
					if(strlen($text) > 650)
					{
						file_put_contents("longTXT",$msgN);
						$text = substr($text,0,650);
						$text .= substr(substr($msgN,650),650);
					}
					else
					{
						$text .= "数据已加载完毕！";
						unlink("longTXT");
					}
					$this->sendMsgObject->sendMessageText($text);
					return;
				}
				else if (file_exists("longTXT") == true)
				{
					unlink("longTXT");
				}
                        
                ////文本信息 需要知道是否是 有命令缓冲的，即我们需要分析 入库的有用文本
                $evenKeyInSession =  $this->isCommandInSession($this->openID); //是否等待命令后的内容文本
                if(strcmp($evenKeyInSession,'expried') == 0)
                {
                    $this->sendMsgObject->sendMessageText("对不起，你的请求已经失效，请重新选择一下菜单！");
                }
                else if(strcmp($evenKeyInSession,'notinsession') == 0) //其他 非正式 需要一个机器人
                {
					 saveMsgsToDB($this->openID,$this->receiveMessage->Content,1); // 1 机器对话
					
				    $msg = ChatRobot::getTheNearestText($this->receiveMessage->Content) . "非法操作命令，如想使用菜单某项功能，请重新选择相应的菜单按钮！";
                    $this->sendMsgObject->sendMessageText($msg);
                }
                else
                {
                    $this->anlayUserTextCommand($this->receiveMessage->Content);
                    
                }
                //删除命令延时
                $this->removeOpenIDAndKeyInSession();
            }
            
            //地理位置类型
            if (strcmp($this->receiveMessage->MsgType, "location") == 0)
            {
                $evenKeyInSession =  $this->isCommandInSession($this->openID); //是否等待命令后的内容文本
                if(strcmp($evenKeyInSession,'expried') == 0)
                {
                    $this->sendMsgObject->sendMessageText("对不起，你的请求已经失效，请重新选择一下菜单！");
                }
                else if(strcmp($evenKeyInSession,'notinsession') == 0)
                {
                    $this->sendMsgObject->sendMessageText("无效操作或超时,请使用下面的菜单。" . ChatRobot::getFace("不高兴"));
                }
                else
                {
					$location_x = $this->receiveMessage->Location_X;
					$location_y = $this->receiveMessage->Location_Y;
					$distance = getDistance($location_x, $location_y, $DESTINATION_X, $DESTINATION_Y);

                    //正确分解格式，入库 以及 返回 根据缓冲EventKey命令
                    if ($this->stuRegisterData->insterStudentRegister($this->openID, timestampToYYMMDDHHMMSS(), $this->receiveMessage->Label, $location_x, $location_y, $distance))
                    {
                        $msgText = "[学员签到]\n恭喜签到成功!" . ChatRobot::getFace("高兴");
                        $this->sendMsgObject->sendMessageText($msgText);
                    }
                }
				//删除命令延时
                $this->removeOpenIDAndKeyInSession();
            }
        }
        
        //分析用户命令文本eg:
        function anlayUserTextCommand($commandText)
        {
            //这里要根据命令$this->eventKey 来格式判断，是否输入正确
			//减免学费和报名  使用网页输入方式
			/*
            if(strcmp($this->eventKey,"MENU_APPLAY_DISCOUNTS") == 0)
            {
				 if(preg_match("/([0-9\-\－]{7,12})/",$commandText))
				 {
					saveMsgsToDB($this->openID,$commandText);
				 	$msg = "已经收到你的信息，我们会尽快联系你的。咨询电话:0512-69172201" . ChatRobot::getFace("握手");
				 	$this->sendMsgObject->sendMessageText($msg);
				 }
				 else
				 {
					 $msg =  ChatRobot::getFace("不高兴")  . "对不起，没有检测到你的联系电话号码。请重新选择一下菜单，然后输入你的手机号或电话号码。 咨询电话:0512-69172201";
				 	 $this->sendMsgObject->sendMessageText($msg);
				 }
				 
            }*/
            //收到请假理由后
            if(strcmp($this->eventKey,"MENU_STUDENT_ASKLEAVE") == 0)
            {
                if ($this->studentLeaveData->insterStudentLeave($this->openID, $this->receiveMessage->Content))
                {
                    $msg = "";
                    if (sendLeaveEmailToTeacher($this->openID))
                        $msg .= "你的请假申请邮件已经成功发送到老师邮箱，等待老师跟你近一步确认。确认后方可确定能否完成请假！";
                    else
                        $msg .= "申请请假邮件发送失败，请重新申请！";
                    
                    $this->sendMsgObject->sendMessageText($msg);
                }
            }
			//招生管理 子菜单处理相应
			if(strcmp($this->eventKey,"MENU_MANAGE_NEWSTUDENT") == 0)
            {
				 $enrollMana = new EnrollManager();
				if(strcmp($commandText,"1") == 0 || strcmp(strtoupper($commandText),"B")==0)
                {
					$msg =  "[查看最新报名]\n" . $enrollMana->getTheEnrollInfo();
                    $this->sendMsgObject->sendMessageText($msg);
                }
                else if(strcmp($commandText,"2") == 0 || strcmp(strtoupper($commandText),"E")==0)
                {
					$msg =  "[统计关注数据]\n" . $enrollMana->statisEnrollMember();
                    $this->sendMsgObject->sendMessageText($msg);
                }
                else if(strcmp($commandText,"3") == 0 || strcmp(strtoupper($commandText),"D")==0)
                {
                    $msg =  "[查看机器对话]\n" . $enrollMana->getTheRobotInfo();
                    $this->sendMsgObject->sendMessageText($msg);
                }
				else if(strcmp($commandText,"4") == 0 || strcmp(strtoupper($commandText),"W")==0)
                {
                    $msg =  "[挖掘有用数据]\n" . $enrollMana->getTheMayBeUserfulInfo();
                    $this->sendMsgObject->sendMessageText($msg);
                }
				else
				{
					$this->sendMsgObject->sendMessageText("菜单选择错误!请输入菜单对应的数字或字母，请重新进入主菜单。" . ChatRobot::getFace("不高兴"));
				}
            }
			
			//最新开班 子菜单处理相应
			if(strcmp($this->eventKey,"MENU_MANAGE_NEWCLASS") == 0)
            {
				$enrollMana = new EnrollManager();
				
				$newBeginMsg = "您若想报名可以点击菜单[招生&风云]-->[我要报名].\n详情您也可以咨询风云教育官方电话:tel:0512-69172201\nQQ:269778837";
				
				if(strcmp($commandText,"1") == 0 || strcmp(strtoupper($commandText),"I")==0)
                {
					$iosInfo = "ios最新开班时间于2014年5月中旬!\n" . $newBeginMsg;
					$msg =  "[查看ios最新开班]\n" . $iosInfo;
                    $this->sendMsgObject->sendMessageText($msg);
                }
                else if(strcmp($commandText,"2") == 0 || strcmp(strtoupper($commandText),"J")==0)
                {
					$javaInfo = "JAVA最新开班时间于,2014年5月5号!\n" . $newBeginMsg;
					$msg =  "[查看JAVA最新开班]\n" . $javaInfo;
                    $this->sendMsgObject->sendMessageText($msg);
                }
                else if(strcmp($commandText,"3") == 0 || strcmp(strtoupper($commandText),"T")==0)
                {
					$testInfo = "测试最新开班时间于,2014年4月初!\n" . $newBeginMsg;	
                    $msg =  "[查看测试最新开班]\n" . $testInfo;
                    $this->sendMsgObject->sendMessageText($msg);
                }
				else
				{
					$this->sendMsgObject->sendMessageText("菜单选择错误!请输入菜单对应的数字或字母，请重新进入主菜单。" . ChatRobot::getFace("不高兴"));
				}
            }
            
            //考勤管理子菜单处理
            if(strcmp($this->eventKey,"MENU_MANAGER_ATTEND") == 0)
            {
				$bIsU_Teacher = $this->u_teacherInfoData->checkTeacherInfo($this->openID);
				
				if(strcmp($commandText,"1") == 0 || strcmp(strtoupper($commandText),"N")==0)
                {
					if ($bIsU_Teacher)
						$msg = "[班级学员近期考勤记录]\n" . $this->u_teacherInfoData->getStudentNearRegister($this->openID, "");
					else
						$msg = "[班级学员近期考勤记录]\n" . $this->teacherInfoData->getStudentNearRegister($this->openID, "");
                    $this->sendMsgObject->sendMessageText($msg);
                }
                else if(strcmp($commandText,"2") == 0 || strcmp(strtoupper($commandText),"A")==0)
                {
					if ($bIsU_Teacher)
						$msg = "[班级学员全部考勤记录]\n" . $this->u_teacherInfoData->getStudentRegister($this->openID);
					else
						$msg = "[班级学员全部考勤记录]\n" . $this->teacherInfoData->getStudentRegister($this->openID);
                    $this->sendMsgObject->sendMessageText($msg);
                }
                else if(strcmp($commandText,"3") == 0 || strcmp(strtoupper($commandText),"E")==0)
                {
					if ($bIsU_Teacher)
						$msg = $this->u_teacherInfoData->getStuRegAbnormal($this->openID);
					else
						$msg = $this->teacherInfoData->getStuRegAbnormal($this->openID);
                    
                    $textMsg = "[班级学员异常考勤记录]\n";
                    
                    $textMsg .= $msg;
                    
                    $this->sendMsgObject->sendMessageText($textMsg);
                }
				else
				{
					$this->sendMsgObject->sendMessageText("菜单选择错误!请输入菜单对应的数字或字母，请重新进入主菜单。" . ChatRobot::getFace("不高兴"));
				}
            }
            //
            
            
        }
        //分析用户Event命令
        function anlayUserEventCommand($commandText)
        {
            
        }
        
        
        function addOpenIDAndKeyInSession($openID,$eventKey)
        {
            $this->eventKey = $eventKey;
            
            //$lifeTime = 60 * 2;  //8分钟
            //session_set_cookie_params($lifeTime);
            //session_start();
            //$_SESSION[$openID] = $openID;
            $con = connectMysql();
            selectDB("fyweixin", $con);
			$sqlUpdate = "UPDATE t_temp_eventKey SET eventKey = \"$eventKey\" WHERE openID = \"$openID\"";
            @mysql_query($sqlUpdate);
			if(mysql_affected_rows() <=0)
			{
				 //$sqlUpdate = "UPDATE t_temp_eventKey SET eventKey = \"$eventKey\" WHERE openID = \"$openID\"";
				 $sqlInsert = "INSERT INTO t_temp_eventKey(openID,eventKey) VALUES (\"$openID\",\"$eventKey\")";
				 @mysql_query($sqlInsert);
			}
            
        }
        function removeOpenIDAndKeyInSession()
        {
            
            $this->eventKey = "";
            $con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlDel = "DELETE FROM t_temp_eventKey WHERE openID = \"$this->openID\"";
            @mysql_query($sqlDel);
            
            session_start();
            //这种方法是将原来注册的某个变量销毁
            unset($_SESSION[$openID]);
            
        }
        //判断EventKey 有没有缓冲
        function isCommandInSession($openID)
        {
            
            $con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlSel = "SELECT openID,eventKey FROM t_temp_eventKey WHERE openID = \"$this->openID\"";
            $result = mysql_query($sqlSel);
            if ($result && mysql_num_rows($result) >0)
            {
                $row = mysql_fetch_array($result);
                $this->eventKey = $row["eventKey"];
                //$sqlDel = "DELETE FROM t_temp_eventKey WHERE openID = \"$this->openID\" ";  
                //@mysql_query($sqlDel);
                return $this->eventKey;
                
            }
            return "notinsession";
            
        }
        
        //处理点击命令菜单后的 发送提示信息
        function dealwithCommandText()
        {
			//分页效果操作后会存在分页保留的文件  当从新执行其他按钮方式后 重新输入NE仍然会执行分页的情况
			//故  在此删除此分页文件
			if (file_exists("longTXT") == true)
			{
				unlink("longTXT");
			}
			
            if(strcmp($this->receiveMessage->MsgType,"event") != 0) return;
            if(empty($this->receiveMessage->EventKey)) return;
            
            //荣誉资质
            if(strcmp($this->receiveMessage->EventKey,"MENU_FENGYUN_CIMMA") == 0)
            {
                $news = new News();
                $newsItems = $news->getNewsItemsArray("荣誉资质",4);
                if(!empty($newsItems) && count($newsItems) > 0)
                {
                    $this->sendMsgObject->sendMessageNews($newsItems);
                }

            }
            //申请学费减免和报名
            else if(strcmp($this->receiveMessage->EventKey,"MENU_APPLAY_DISCOUNTS") == 0)
            {
                $msg = "[我要报名]\n请点击链接,输入报名信息！";
				$stuInputUrl =  "http://"  . $_SERVER['HTTP_HOST'] . "/weixinServer/fengyun/pages/fyNewStuInput.php";
				$this->sendMsgObject->sendMessageText($msg . ChatRobot::getFace("花") . "\n" .  $stuInputUrl);
				//
				return;  //这个功能不需要记住命令
            }
            //招生管理
            else if(strcmp($this->receiveMessage->EventKey,"MENU_MANAGE_NEWSTUDENT") == 0)
            {
                $teaInfoArray = $this->teacherInfoData->getTeacherInfo($this->openID);
                
                if($teaInfoArray[0]["type"] >=2) //表示权限高
                {
                   $this->sendMsgObject->sendMessageText("[招生管理]\n菜单选择:\n1查看最新报名(B)\n2统计关注数据(E)\n3查看机器对话(D)\n4挖掘有用信息(W)");
                }
                else
                {
                    $this->sendMsgObject->sendMessageText(ChatRobot::getFace("不高兴") . "权限不够!");
					return;
                }
                
            }
			//最新开班
            else if(strcmp($this->receiveMessage->EventKey,"MENU_MANAGE_NEWCLASS") == 0)
            {
                $this->sendMsgObject->sendMessageText("[最新开班]\n菜单选择:\n1查看ios最新开班(I)\n2查看JAVA最新开班(J)\n3查看测试最新开班(T)");
            }
            //地理位置的发送 学生考勤签到
            else if(strcmp($this->receiveMessage->EventKey,"MENU_STUDENT_LOCATION") == 0)
            {
                //第一步判断是否是注册学员
                if(! $this->stuInfoData->checkStudentInfo($this->openID))
                {
                    $this->sendMsgObject->sendMessageText(ChatRobot::getFace("不高兴") ."对不起，你还不是风云注册学员！");
					return;
                }
                else
                {
                    $location_x = $this->receiveMessage->Location_X;
					$location_y = $this->receiveMessage->Location_Y;
					$distance = getDistance($location_x, $location_y, $DESTINATION_X, $DESTINATION_Y);
                    $label = $this->receiveMessage->Label;
                    
					$html5 = "http://"  . $_SERVER['HTTP_HOST'] . "/weixinServer/fengyun/test1/BMap.php?openID=" . $this->openID;
                    $this->sendMsgObject->sendMessageText("[考勤签到]\n请直接发送你的地理位置信息!....\n或者打开下面的地址以定位地理位置" . $html5);
                }
            }
			//申请请假或请假查询
            else if(strcmp($this->receiveMessage->EventKey,"MENU_STUDENT_ASKLEAVE") == 0) //申请请假
            {
                /*if(! $this->stuInfoData->checkStudentInfo($this->openID))
                {
                    $this->sendMsgObject->sendMessageText(ChatRobot::getFace("不高兴") . "对不起，你还不是风云注册学员！");
					return;
                }
                else
                {
                   
				    $evenKeyInSession =  $this->isCommandInSession($this->openID); //是否有该等待命令
                	if(strcmp($evenKeyInSession,'MENU_STUDENT_ASKLEAVE') == 0 )    //MENU_STUDENT_ASKLEAVE
                	{
                        //删除命令延时
                		$this->removeOpenIDAndKeyInSession();
						$this->sendMsgObject->sendMessageText("[申请请假]\n已经取消该请假申请命令,如需再申请，请再次选择该菜单。");
						return;
                	}
                    else
					{
				    	$this->sendMsgObject->sendMessageText("[申请请假]\n写点请假原由。例子:明天回学校或明天去上海面试\n(注:系统会自动发送邮件给相关老师,如需取消，请再次选择该菜单)");
					}
                }*/
				
				 if ($this->teacherInfoData->checkTeacherInfo($this->openID))
				{
					$leaveMsg = $this->teacherInfoData->getAllStudentLeaveInfo($this->openID);
					$this->sendMsgObject->sendMessageText($leaveMsg);
				}
				else if ($this->u_teacherInfoData->checkTeacherInfo($this->openID))
				{
					$leaveMsg = $this->u_teacherInfoData->getAllStudentLeaveInfo($this->openID);
					$this->sendMsgObject->sendMessageText($leaveMsg);
				}
				else if($this->stuInfoData->checkStudentInfo($this->openID))
                {
					 $evenKeyInSession =  $this->isCommandInSession($this->openID); //是否有该等待命令
                	if(strcmp($evenKeyInSession,'MENU_STUDENT_ASKLEAVE') == 0 )    //MENU_STUDENT_ASKLEAVE
                	{
                        //删除命令延时
                		$this->removeOpenIDAndKeyInSession();
						$this->sendMsgObject->sendMessageText("[申请请假]\n已经取消该请假申请命令,如需再申请，请再次选择该菜单。");
						return;
                	}
                    else
					{
				    	$this->sendMsgObject->sendMessageText("[申请请假]\n写点请假原由。例子:明天回学校或明天去上海面试\n(注:系统会自动发送邮件给相关老师,如需取消，请再次选择该菜单)");
					}
                }
                else
                {
                   $this->sendMsgObject->sendMessageText(ChatRobot::getFace("不高兴") . "对不起，你还不是风云注册学员！");
					return;
                }
            }
			//学生成绩查询
            else if(strcmp($this->receiveMessage->EventKey,"MENU_STUDENT_TESTMARK") == 0)
            {
				if ($this->teacherInfoData->checkTeacherInfo($this->openID))
				{
					$scoreText = $this->teacherInfoData->getAllStudentScoreInfo($this->openID);
					$this->sendMsgObject->sendMessageText($scoreText);
				}
				else if ($this->u_teacherInfoData->checkTeacherInfo($this->openID))
				{
					$scoreText = $this->u_teacherInfoData->getAllStudentScoreInfo($this->openID);
					$this->sendMsgObject->sendMessageText($scoreText);
				}
				else if($this->stuInfoData->checkStudentInfo($this->openID))
                {
					//输出学生的测试考试分数
                    
                    $scoreText = "[学员成绩]\n";
                    
                    $stuScoreInfo = new StudentScoreInfo();
                    $scoreArray = $stuScoreInfo->searchStuScoreInfo($this->openID);
					
					$scoreText .= "★" . $this->stuInfoData->getStudentName($this->openID) . "\n";
					
                    if (count($scoreArray) == 0)
                        $scoreText .= "没有考试成绩信息！";
                    
                    foreach ($scoreArray as $value)
                    {
                        $scoreText .= $value['time'] . " " . $value['subject'] . " " . $value['score'] . "分\n";
                    }
                    
                    $this->sendMsgObject->sendMessageText($scoreText);
                }
                else
                {
					$this->sendMsgObject->sendMessageText(ChatRobot::getFace("不高兴") . "对不起，你还不是风云注册学员！");
					return;
                }
            }
          
            
            //学员考勤记录管理
            else if(strcmp($this->receiveMessage->EventKey,"MENU_MANAGER_ATTEND") == 0)
            {
				if ($this->teacherInfoData->checkTeacherInfo($this->openID))
                {
					$this->sendMsgObject->sendMessageText("[学员考勤管理]\n菜单选择:\n1查看本班最近(N)\n2查看本班所有(A)\n3统计本班异常(E)");
                }
				else if ($this->u_teacherInfoData->checkTeacherInfo($this->openID))
				{
					$this->sendMsgObject->sendMessageText("[学员考勤管理]\n菜单选择:\n1查看本班最近(N)\n2查看本班所有(A)\n3统计本班异常(E)");
				}
                else if ($this->stuInfoData->checkStudentInfo($this->openID))
                {
					$msgText = "[学员个人考勤记录]\n";
                    $stuName = "★" . $this->stuInfoData->getStudentName($this->openID);
                    $msgText .= $stuName;
                    $timeArray = $this->stuRegisterData->getAPeriodRegTime($this->openID, (date("Y-m", time()) . "-01" . " " . "00:00:00"), date("Y-m-d H:i:s", time()));
                    if (count($timeArray) == 0)
                        $msgText .= "\n没有签到数据信息，请找管理员查明原因!";
                    else
                    {
                        $msgText .= "\n";
						foreach ($timeArray as $value)
						{
							$msgText .= $value['time'] . "\n";
						}	
                    }
                        
                    $this->sendMsgObject->sendMessageText($msgText); 
                }
                else
                {
                    $this->sendMsgObject->sendMessageText(ChatRobot::getFace("不高兴")  . "对不起，你还不是风云教育的老师或注册学员!\n如需查询某学生考勤记录请直接发送:KQCX@学生姓名\n请假查询发送:QJCX@学生姓名");
					return;
                }
            }
            //高级 教务 管理
            else if(strcmp($this->receiveMessage->EventKey,"MENU_MANAGER_ADVANCE") == 0)
			{
				 $msg = "[高级管理]\n该功能为老师VIP入口...\n";
				 $managerUrl =  "http://"  . $_SERVER['HTTP_HOST'] . "/weixinServer/fengyun/pages/fyAdmin.php?openID=" . $this->openID;
				 
				if($this->teacherInfoData->checkTeacherInfo($this->openID))
				{
					 $this->sendMsgObject->sendMessageText($msg . ChatRobot::getFace("花") . "\n" .  $managerUrl);
				}
                else
				{
					 $this->sendMsgObject->sendMessageText($msg .  ChatRobot::getFace("不高心") . "\n权限不够!");
				}
			}
		   //招聘 企业 菜单 处理	
		   //显示最新招聘信息
		    else if(strcmp($this->receiveMessage->EventKey,"MENU_COMPANY_NEWOFFER") == 0)
			{
				 //采用news 图文发布 通过打开连接方式
			    $news = new News();
                $newsItems = $news->getNewsItemsArray("最新招聘",1);
                if(!empty($newsItems) && count($newsItems) > 0)
                {
                    $this->sendMsgObject->sendMessageNews($newsItems);
                }
			    $this->sendMsgObject->sendMessageText("ddd");
			}
			//我要 发布 招聘 信息 MENU_COMPANY_PROVIDE
			else if(strcmp($this->receiveMessage->EventKey,"MENU_COMPANY_PROVIDE") == 0)
			{
				 //给除一个发布的输入窗口或者微信格式输入
				 $msg = "[发布招聘信息]\n由于信息较多,这里使用页面输入的方式";
				 $managerUrl =  "http://"  . $_SERVER['HTTP_HOST'] . "/weixinServer/fengyun/pages/fyCommpanyOffersInput.php?openID=" . $this->openID;
				 
				 $this->sendMsgObject->sendMessageText($msg . ChatRobot::getFace("花") . "\n" .  $managerUrl);
			}
			else
			{
			}
            //命令session 保持10分钟
            $this->addOpenIDAndKeyInSession($this->openID,$this->receiveMessage->EventKey);
           }
        
    }
    
    
 ?>