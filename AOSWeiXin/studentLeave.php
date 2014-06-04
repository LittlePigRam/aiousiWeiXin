<?php

    /*
     * wechat student leave
     */
    
    require_once("studentoperatedata.php");
    require_once("teacheroperatedata.php");
    require_once("PHPMailer-master/class.phpmailer.php");
	
    //学员请假时 发送邮件通知老师
    function sendLeaveEmailToTeacherLu($openID)
    {
        $studentLeaveInfo = new StudentLeave();
        $leaveArray = $studentLeaveInfo->getTheStuAllLeaveInfo($openID);
        
        $row = $leaveArray[count($leaveArray) - 1];
        
        if (!$row) return;
        
        $studenInfo = new StudentInfo();
        $stuInfo = $studenInfo->checkStudentInfo($openID);
        if (!$stuInfo) return;
        
        $teacherInfo = new TeacherInfo();
        
        $teacherArray = $teacherInfo->getTeacherInfoByField("className", $stuInfo['className']);
        if (count($teacherArray) == 0) return;
        
        //file_put_contents('operateDataLog.txt',$to . "\n",FILE_APPEND);
        
        $subject = $row['name'] . "申请请假";
        $message = $row['name'] . "于" . $row['applyTime'] . "向你发出请假申请，请尽快与其沟通。\n" . "请假原由:\n" . " " . " " . " " . " " . $row['reason'];
        $from = "mazm@fengyuntec.com";
        $headers = "From: $from";
        
        $mail = new PHPMailer(); //建立邮件发送类
        $mail->IsSMTP(); // 使用SMTP方式发送
        $mail->Host = "mail.fengyuntec.com"; // 您的企业邮局域名
        $mail->SMTPAuth = true; // 启用SMTP验证功能
        $mail->Username = "luyy"; // 邮局用户名
        $mail->Password = "fengyunte"; // 邮局密码
        $mail->Port=25;
        $mail->From = "luyy@fengyuntec.com"; //邮件发送者email地址
        $mail->FromName = "luyy";
        
        //发送给多人
        foreach ($teacherArray as $key=>$value)
        {
            $mail->ClearAddresses();
            $mail->AddAddress($value['Email'] . "@fengyuntec.com", "luyy");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
            
            //$mail->AddReplyTo("", "");
            //$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
            //$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
            $mail->Subject = $subject; //邮件标题
            $mail->Body = $message; //邮件内容
            //        $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
            //        $mail->SMTPDebug = 1;
            $mail->CharSet = "utf-8";
            
            if(!$mail->Send())
			{
				file_put_contents('log.txt', "发送邮件失败~To:" . $value['Email'] . "@fengyuntec.com\n",FILE_APPEND);
                return false;
			}
        }
        
        return true;
    }
    
	
	//发送邮件 mzm 修改，因为考虑到服务器只有80端口开发，邮件端口是发不出去的，所以这里发送邮件是增加http接口 间接发送邮件
	function sendLeaveEmailToTeacher($openID)
	{
		 $re = sendLeaveEmailToTeacherLu($openID);
		 if($re === false)
		 {
			  $studentLeaveInfo = new StudentLeave();
			  $leaveArray = $studentLeaveInfo->getTheStuAllLeaveInfo($openID);
			  
			  $row = $leaveArray[count($leaveArray) - 1];
			  
			  if (!$row) return $re;
			  
			  $studenInfo = new StudentInfo();
			  $stuInfo = $studenInfo->checkStudentInfo($openID);
			  if (!$stuInfo) return $re;
			  
			  $teacherInfo = new TeacherInfo();
			  $teacherArray = $teacherInfo->getTeacherInfoByField("className", $stuInfo['className']);
			  if (count($teacherArray) == 0) return $re;
			  $to = "";
			  foreach ($teacherArray as $key=>$value)
			  {
				  $to .= $value . '~';
			  }
			  
			  
			  $subject = $row['name'] . "申请请假";
			  $message = $row['name'] . "于" . $row['applyTime'] . "向你发出请假申请，请尽快与其沟通。\n" . "请假原由:\n" . " " . " " . " " . " " . $row['reason'];
			  
			  //通过Get 方式 发送 邮件信息 这里暂时使用mzm008
			  header("Location:http://mzm008.gicp.net/weixinServer/fengyun/studentLeave.php?openID=$openID&subject=$subject&message=$message&to=$to");
		 }
		 return true;
    }
	//mzm add 直接发送
	function sendLeaveEmail($subject,$message,$to)
    {
        $mail = new PHPMailer(); //建立邮件发送类
        $mail->IsSMTP(); // 使用SMTP方式发送
        $mail->Host = "mail.fengyuntec.com"; // 您的企业邮局域名
        $mail->SMTPAuth = true; // 启用SMTP验证功能
        $mail->Username = "luyy"; // 邮局用户名
        $mail->Password = "fengyunte"; // 邮局密码
        $mail->Port=25;
        $mail->From = "luyy@fengyuntec.com"; //邮件发送者email地址
        $mail->FromName = "luyy";
		$mail->ClearAddresses();
		//$mail->AddAddress($value['Email'] . "@fengyuntec.com", "luyy");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
		
	    /*
		$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
		$mail->Subject = $subject; //邮件标题
		$mail->Body = $message; //邮件内容
		$mail->CharSet = "utf-8";
		
		if(!$mail->Send())
			return false;
		*/
        //发送给多人
		$mail->ClearAddresses();
		$teacherArray = explode("~",$to);
        foreach ($teacherArray as $key=>$value)
        {
          
            $mail->AddAddress($value, "");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
            //$mail->AddReplyTo("", "");
            //$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
            $mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
            $mail->Subject = $subject; //邮件标题
            $mail->Body = $message; //邮件内容
            //        $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
            //        $mail->SMTPDebug = 1;
            $mail->CharSet = "utf-8";
            if(! $mail->Send())
			{
				  file_put_contents('log.txt', "HTTP发送邮件失败~To:" . $value. "\n",FILE_APPEND);
				  return false;
			}
        }
   
        return true;
    }
	
     //mzm add send Email first via local second HTTP $to 可以多人中间用～
	function sendEmail($subject,$message,$to)
	{
		if(sendLeaveEmail($subject,$message,$to) === false)
		{
			 //通过Get 方式 发送 邮件信息 这里暂时使用mzm008
			  header("Location:http://mzm008.gicp.net/weixinServer/fengyun/studentLeave.php?openID=$openID&subject=$subject&message=$message&to=$to");
		}
	}
	
	//$openID = $_GET["openID"]; 
	if(!empty($_GET['subject'])  &&  !empty($_GET['message']) &&  !empty($_GET['to']))
	{
		sendLeaveEmail($_GET['subject'],$_GET['message'],$_GET['to']);
	}
	
?>