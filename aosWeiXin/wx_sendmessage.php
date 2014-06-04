<?php
    /**
     * wechat recvice message and send message
     * 发送 接受 处理 微信 信息的 类
     */
    
    /** 发送被动消息 和 客服消息 */
    /** 文本类型信息
     var $Content;
     
     语音信息
     var  $MediaId;
     var  $Format;
     
     视屏信息
     var $MediaId;
     var $ThumbMediaId;
     //
     var $Title;
     var $Description;

     地理位置信息
     var $Location_X;
     var $Location_Y;
     var $Scale;
     var $Label;
     
     链接信息
     var $Title;
     var $Description;
     var $Url;
     
     图片信息 如果发送图文 信息 比如：news 则需要linkMsg类型
     图片类型信息
     var $PicUrl;
     var $MediaId;
     */
    
    class ClassMsgBody
    {
        var $ToUserName;
        var $FromUserName;
        var $CreateTime;
        var $MsgType;
        var $MsgId;
        
        function ClassMsgBody()
        {
            $CreateTime = time();
        }
    }

    
    class WechatSendMessage
    {
        //收到的用户信息
        var $receiveMessage;
        public function WechatSendMessage($classMsgBody)
        {
            $this->receiveMessage = $classMsgBody;
        }
        
        private function checkSignature()
        {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];
            
            $token = TOKEN;
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            
            if( $tmpStr == $signature ){
                return true;
            }else{
                return false;
            }
        }
        
        
        private function recviceMessage($recvMsg)
        {
            
        }
        
        /** 发送 */
        public function sendMessage($sendMsg)
        {
            $msgType = $sendMsg->MsgType;
            
            $sendMessagexml = "<xml>
            <ToUserName><![CDATA[$sendMsg->ToUserName]]></ToUserName>
            <FromUserName><![CDATA[$sendMsg->FromUserName]]></FromUserName>
            <CreateTime>$sendMsg->CreateTime</CreateTime>
            <MsgType><![CDATA[$sendMsg->MsgType]]></MsgType>";
            
            if(strcmp($msgType,'text') == 0)
            {
                $sendMessagexml .=  "<Content><![CDATA[$sendMsg->Content]]></Content>";
            }
            else if(strcmp($msgType,'image') == 0)
            {
                $sendMessagexml .= "<Image><MediaId><![CDATA[$sendMsg->MediaId]]></MediaId></Image>";
            }
            else if(strcmp($msgType,'voice') == 0)
            {
                $sendMessagexml .= "<Voice><MediaId><![CDATA[$sendMsg->MediaId]]></MediaId></Voice>";
            }
            else if(strcmp($msgType,'video') == 0)
            {
                $sendMessagexml .= "<Video><MediaId><![CDATA[$sendMsg->MediaId]]></MediaId> <Title><![CDATA[$sendMsg->Title]]></Title> <Description><![CDATA[$sendMsg->Description]]></Description></Video>";
            }
			else if(strcmp($msgType,'link') == 0)
			{
				 $sendMessagexml .= "<Title><![CDATA[$sendMsg->Title]]></Title><Description><![CDATA[$sendMsg->Description]]></Description><Url><![CDATA[$sendMsg->Url]]></Url>";
			}
            else if(strcmp($msgType,'news') == 0)
            {
                //newsItems 需要发送的news 条目 数组 不能超过10个
                //$sendMsg->newsItems
                //$item->Title,$item->Description,$item->PicUrl,$item->Url;
                $sendMessagexml .= "<ArticleCount> " . count($sendMsg->newsItems) .  "</ArticleCount><Articles>";
                foreach ($sendMsg->newsItems as $key=>$item)
                {
                    $Title = $item["Title"];
                    $Desc  = $item["Description"];
                    $PicUrl= $item["PicUrl"];
                    $Url   = $item["Url"];
                    
                    $sendMessagexml .= "<item><Title><![CDATA[$Title]]></Title><Description><![CDATA[$Desc]]></Description><PicUrl><![CDATA[$PicUrl]]></PicUrl><Url><![CDATA[$Url]]></Url></item>";
                }
                $sendMessagexml .= "</Articles>";
            }
            else
            {
                
            }
            
            $sendMessagexml .= "<FuncFlag>0</FuncFlag></xml>";
            
            //file_put_contents('log.txt',$sendMessagexml . "\n",FILE_APPEND);
            
            echo $sendMessagexml;
            
          
        }
        
        public function sendMessageText($text)
        {
            //$keyword      = trim($postObj->Content);
            //快速回复文本
            $sendMsg = new ClassMsgBody();
            $sendMsg->ToUserName   =  $this->receiveMessage->FromUserName;
            $sendMsg->FromUserName =  $this->receiveMessage->ToUserName;
            $sendMsg->MsgType = 'text';
			
			//长字符 分页发送
			if(strlen($text) > 650)
			{
				file_put_contents('longTXT',$text);
				$text = substr($text,0,650);
				$text .= "...[输入NE下一页]";
			}
			
            $sendMsg->Content =  $text;
            
            $this->sendMessage($sendMsg);
        }
        //$newsItems 数组 发送图文信息
        public function sendMessageNews($newsItems)
        {
            //$keyword      = trim($postObj->Content);
            $sendMsg = new ClassMsgBody();
            $sendMsg->ToUserName   =  $this->receiveMessage->FromUserName;
            $sendMsg->FromUserName =  $this->receiveMessage->ToUserName;
            $sendMsg->MsgType = 'news';
            $sendMsg->newsItems = $newsItems;
           
            $this->sendMessage($sendMsg);
        }
		
		/** 发送链接 试试 */
 		public function sendMessageLink($Title,$Desc,$Link)
        {
            $sendMsg = new ClassMsgBody();
            $sendMsg->ToUserName   =  $this->receiveMessage->FromUserName;
            $sendMsg->FromUserName =  $this->receiveMessage->ToUserName;
            $sendMsg->MsgType = 'link';
          	$sendMsg->Title = $Title;
			$sendMsg->Description = $Desc;
			$sendMsg->Url = $Link;
	
            $this->sendMessage($sendMsg);
        }
        
        //被动发送图文信息
        /*
         <xml>
         <ToUserName><![CDATA[toUser]]></ToUserName>
         <FromUserName><![CDATA[fromUser]]></FromUserName>
         <CreateTime>12345678</CreateTime>
         <MsgType><![CDATA[news]]></MsgType>
         <ArticleCount>2</ArticleCount>
         <Articles>
         <item>
         <Title><![CDATA[title1]]></Title>
         <Description><![CDATA[description1]]></Description>
         <PicUrl><![CDATA[picurl]]></PicUrl>
         <Url><![CDATA[url]]></Url>
         </item>
         <item>
         <Title><![CDATA[title]]></Title>
         <Description><![CDATA[description]]></Description>
         <PicUrl><![CDATA[picurl]]></PicUrl>
         <Url><![CDATA[url]]></Url>
         </item>
         </Articles>
         </xml>
         */
        
    }
    
    
    
    
    ?>