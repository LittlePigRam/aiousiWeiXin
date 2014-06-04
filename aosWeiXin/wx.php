<?php
/**
  * wechat php ΢�Ź���ƽ̨�� ע�� �ص�ҳ�� 
  * ΢�ŷ����� ���� ���ص�POST ���ݶ�Ҫͨ�����ҳ��
  */
/*
define("TOKEN", "mzm13913501028");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
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
}
*/
   
   
    
    require_once("wx_messageLogic.php");
    require_once("wx_sendMessage.php");
    
    
    header('Content-type:text/html;charset=utf-8');
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    
    //file_put_contents('log.txt',$postStr . "\n",FILE_APPEND);
	//ת�ƹ�ע�û���ע�û�
	$enrollMana = new EnrollManager();
	$enrollMana->saveEnrollMemberToMy($postObj->FromUserName);
	
    
    //extract post data
    if (!empty($postStr))
    {
        
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $msgType = $postObj->MsgType;
        $userMsgBody = new ClassMsgBody();
        
        //������ �յ� ��Ϣ�Ļ��� ��Ϣ
        $userMsgBody->ToUserName   = $postObj->ToUserName;
        $userMsgBody->FromUserName = $postObj->FromUserName;
        $userMsgBody->CreateTime   = $postObj->CreateTime;
        $userMsgBody->MsgType      = $postObj->MsgType;
        $userMsgBody->MsgId        = $postObj->MsgId;
        
        //�������յ��û����͸�����Ϣ��
        //Content
        
        if(!empty($postObj->Content))
        {
            $userMsgBody->Content  = $postObj->Content;
        }
        
        if(!empty($postObj->PicUrl))
        {
            $userMsgBody->PicUrl  = $postObj->PicUrl;
        }
        if(!empty($postObj->MediaId))
        {
            $userMsgBody->MediaId = $postObj->MediaId;
        }
        if(!empty($postObj->Format))
        {
            $userMsgBody->Format  = $postObj->Format;
        }
        if(!empty($postObj->ThumbMediaId))
        {
            $userMsgBody->ThumbMediaId = $postObj->ThumbMediaId;
        }
        if(!empty($postObj->Location_X))
        {
            $userMsgBody->Location_X =  $postObj->Location_X;
        }
        if(!empty($postObj->Location_X))
        {
            $userMsgBody->Location_X =  $postObj->Location_X;
        }
        if(!empty($postObj->Location_Y))
        {
            $userMsgBody->Location_Y =  $postObj->Location_Y;
        }
        if(!empty($postObj->Scale))
        {
            $userMsgBody->Scale =  $postObj->Scale;
        }
        if(!empty($postObj->Label))
        {
            $userMsgBody->Label =  $postObj->Label;
        }
        if(!empty($postObj->Title))
        {
            $userMsgBody->Title =  $postObj->Title;
        }
        if(!empty($postObj->Description))
        {
            $userMsgBody->Description =  $postObj->Description;
        }
        if(!empty($postObj->Url))
        {
            $userMsgBody->Url =  $postObj->Url;
        }
        //������ �յ��¼���Ϣ��
        if(!empty($postObj->Event))
        {
            $userMsgBody->Event = $postObj->Event;
        }
        if(!empty($postObj->EventKey))
        {
            $userMsgBody->EventKey =  $postObj->EventKey;
        }
        if(!empty($postObj->Ticket))
        {
            $userMsgBody->Ticket =  $postObj->Ticket;
        }
        //���� �յ��ϱ�����λ�õ�
        if(!empty($postObj->Latitude))
        {
            $userMsgBody->Latitude =  $postObj->Latitude;
        }
        if(!empty($postObj->Longitude))
        {
            $userMsgBody->Longitude =  $postObj->Longitude;
        }
        if(!empty($postObj->Precision))
        {
            $userMsgBody->Precision =  $postObj->Precision;
        }
        
       
        //�������յ���΢�ŷ���������xml��Ϣ �Լ���װһ��
        //�����Ƕ�������Ϣ �����߼����� ����
        $messageLogic = new ClassMessageLogic($userMsgBody);
        //�����û�����
        $messageLogic->anlayUserCommand();

    
    }
    
?>