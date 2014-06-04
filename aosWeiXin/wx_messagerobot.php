<?php
    /**
     * 风云教育微信 机器人
     */
    class ChatRobot
    {
        var $_text;
        
        public function ChatRobot($ReceiveText)
        {
            $this->_text = $ReceiveText;
        }
		//
		static function getIntelligentreplyFromBD($ReceiveText)
		{
			$replayArray = array();
			
			$con = connectMysql();
			selectDB("fyweixin", $con);
			$sqlSelect = "SELECT title,replyWords FROM intelligentreply WHERE title like %$ReceiveText%";
			$result = @mysql_query($sqlSelect);
			if ($result && mysql_num_rows($result) > 0) 
            {
				 $line = mysql_fetch_array($result, MYSQL_ASSOC);
				 return  $line["replyWords"];
			}
			else
			{
				 $sqlSelect = "SELECT title,replyWords FROM intelligentreply";
			     $result = @mysql_query($sqlSelect);
				 while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
                 {
					 if(stripos($ReceiveText,$line["title"]) !== false)
					 {
						 array_push($replayArray, $line["replyWords"]);
					 }
				 }
				 //
				 if(count($replayArray) > 0)
				 {
					 $index = rand(0,count($replayArray)-1);
					 return $replayArray[$index];
				 }
				 else
				 {
					 $replayArray = array("/::)","/::)","/::D","/::(","/:rose","/:strong","/:share");
					 $index = rand(0,count($replayArray)-1);
					 return $replayArray[$index] ;
				 }
			}
            $histMsg1 = "欢迎关注苏州风云教育！\n咨询电话:0512-69172201";
			return $histMsg1; 
		}
        
        //获取最近的意思
        static function getTheNearestText($ReceiveText)
        {
            //$histMsg1 = "欢迎关注苏州风云教育！\n咨询电话:0512-69172201";
			$robotMsg = ChatRobot::getIntelligentreplyFromBD($ReceiveText);	
            return $robotMsg;
        }
        
		/** 兑换 表情 */
        static function getFace($faceni)
        {
            $faceArray = array
            (
                "微笑"   => "/::)",
                "高兴"   => "/::)",
                "大笑"   => "/::D",
                "不高兴" => "/::(",
                "花"    => "/:rose",
                "强"    => "/:strong",
                "握手"  => "/:share"
            );
            if(array_key_exists($faceni, $faceArray))
            {
                return $faceArray[$faceni];
            }
            else
            {
                return $faceArray[0];
            }
        }
        
    }
    
    
?>