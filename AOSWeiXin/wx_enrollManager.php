<?php
    /**
     * 招生管理 
     */
    
    require_once("common.class.php");
    require_once("utileMethod.php");
    
    class EnrollManager
    {
        
        public function EnrollManager()
        {
            
        }
		//$type0为报名信息 1为机器人信息   status0为未处理，1为已经处理
		private function getTMsgs($type,$status)
		{
			$con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlSel = "SELECT ID,openID,msg,type,time FROM t_msgs WHERE type = $type AND status = $status  ORDER BY time DESC LIMIT 0 ,100";
            $result = mysql_query($sqlSel);
			if($result)
			{
				$returnMsg = "";
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
				   $returnMsg .=  $row["time"] . " " . $row["msg"] . "\n";
				}
				mysql_free_result($result);
				return $returnMsg;
			}
		}
        
        /* 查看报名留言 最新100条  type=1 */
        public function  getTheEnrollInfo ()
        {
			//return $this->getTMsgs(0,0);
			$con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlSel = "SELECT name,phone,qq,remark,timeOn FROM t_entername WHERE status = 1 ORDER BY timeOn DESC LIMIT 0 ,100";
            $result = mysql_query($sqlSel);
			if($result)
			{
				$returnMsg = "";
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
				   $msg = $row["name"] . $row["phone"] . "qq:" .$row["qq"];
				   $returnMsg .=  $row["timeOn"] . " " . $msg . "\n";
				}
				mysql_free_result($result);
				return $returnMsg;
			}
        }
		 /* 查看机器对话 最新100条 */
        public function  getTheRobotInfo ()
        {
            return $this->getTMsgs(1,0);
        }
		 /* 挖掘有用数据 最新100条 */
        public function  getTheMayBeUserfulInfo ()
        {
            $con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlSel = "SELECT ID,openID,msg,type,time FROM t_msgs WHERE status = 0  ORDER BY time DESC LIMIT 0 ,100";
            $result = mysql_query($sqlSel);
			if($result)
			{
				$returnMsg = "";
			    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
                {
                    if(preg_match("/([0-9\-\－]{7,12})/",$row["msg"]))
					{
						$returnMsg .=  "[" . $row["time"] . "]\n" . $row["msg"] . "\n";
					}
				
                }
				mysql_free_result($result);
                return $returnMsg;
			}
        }
		//保存关注成员 多次关注、取消 需要调用这个函数
		public function saveEnrollMember($openID)
		{
			$con = connectMysql();
            selectDB("fyweixin", $con);
			$sqlUpdate = "UPDATE t_member SET  status = 1 , timeOn = CURRENT_TIMESTAMP  WHERE openID = \"$openID\"";
            @mysql_query($sqlUpdate);
			if(mysql_affected_rows() <=0)
			{
				 $sqlInsert = "INSERT INTO t_member(openID,timeOn,status) VALUES (\"$openID\",CURRENT_TIMESTAMP,1)";
				 @mysql_query($sqlInsert);
			}
			
		}
		
	    //此函数 为了 转移 现有的关注到 我们的数据库中
		public function saveEnrollMemberToMy($openID)
		{
			$con = connectMysql();
            selectDB("fyweixin", $con);
			$sqlInsert = "INSERT INTO t_member(openID,timeOn,status) VALUES (\"$openID\",CURRENT_TIMESTAMP,1)";
			@mysql_query($sqlInsert);
		}
		
		
		//关注成员 不关注了
		public function updateEnrollMember($openID)
		{
			$con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlupdate = "UPDATE t_member SET status = 0 , timeOff =  CURRENT_TIMESTAMP WHERE openID = \"$openID\"";
            @mysql_query($sqlupdate);
		}
		//统计关注数据
		public function statisEnrollMember()
		{
			$returnMsg = "";
			$con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlSel = "SELECT count(*) AS MEMON  FROM t_member WHERE status = 1 ";
            $result = mysql_query($sqlSel);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$returnMsg .= "正在关注成员:" .  $row["MEMON"] . "人\n";
			
			if($result)
				mysql_free_result($result);
			//
			$sqlSel = "SELECT count(*) AS MEMOFF  FROM t_member WHERE status = 0 ";
			$result = mysql_query($sqlSel);
			$row2 = mysql_fetch_array($result, MYSQL_ASSOC);
			$returnMsg .= "已经取消关注:" .  $row2["MEMOFF"] . "人\n";
			//
          	$returnMsg .= "曾经关注人数:" .  ($row["MEMON"]  + $row2["MEMOFF"]) . "人\n";
			
			if($result)
				mysql_free_result($result);
				
			return $returnMsg; 
		}
    }
    
?>