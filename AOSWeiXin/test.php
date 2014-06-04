<?php
    /**
     * wechat recvice message and send message
     */
    require_once("wx_messageLogic.php");
    
    $messageLogic = new ClassMessageLogic($userMsgBody);
    //分析用户命令
    $messageLogic->addOpenIDAndKeyInSession("dfadfasdfadsfasdfasdfasdfadsfadsfa","tttttttttttttttttttttttt");
    
?>