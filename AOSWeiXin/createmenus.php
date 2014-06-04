<?php
    /**
     * wechat php
     */
    
    require_once("httpclient.class.php");
    
	/*function accessToken($appid,$secret)
	{
		$ToolkenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
	}
	*/
	
	function curlPost($data, $url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($ch, CURLOPT_USERAGENT,
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$info = curl_exec($ch);
		if(curl_errno($ch)){
			echo 'Erron' . curl_error($ch);
		}
		curl_close($ch);
		return json_decode($info);
	}
	
    function http_post_data($url, $data_string)
    {
		echo "123";
		if (function_exists('curl_init')) {
			echo "curl_init()函数已经定义.<br />";
		} 
		else {
			echo "curl_init()函数没有定义.<br />";
		}
	
        $ch = curl_init();
		echo "\n0011\n";
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                   'Content-Type: application/json; charset=utf-8',
                                                   'Content-Length: ' . strlen($data_string))
                    );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
    }
    
    
    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $post_data post键值对数据
     * @return string
     */
    function send_post($url, $post_data) {
        
        $postdata = http_build_query($post_data);
        $options = array(
                         'https' => array(
                                          'method' => 'POST',
                                          'header' => 'Content-type:application/x-www-form-urlencoded',
                                          'content' => $postdata,
                                          'timeout' => 15 * 60 // 超时时间（单位:s）
                                          )
                         );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        echo $result . "\nok??";
        return $result;
    }
    
    /*使用方法
     $post_data = array(
     'username' => 'stclair2201',
     'password' => 'handan'
     );
     send_post('http://www.qianyunlai.com', $post_data);
     */
    
    
    
     header('Content-type:text/html;charset=utf-8');
    
    
    //https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN
    
    $accesstoken = "LVWPyn2-CMUSxceSZBbTtEKIiNBFU6HHHK50jK7IM-IgDI2QLhjZ_2SW2wJuGmRFFhv3RvmFsO3yKhHYTtwgnuCfPUCqKkGnPmKdZ4f_4zfpK24y8N9nwjErKKzaJHiRBXyVw3zEe1wrNi6HmWbRWQ";
    $postPage    =  "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $accesstoken;
    
    //echo $postPage . '<br/>';
    
    
    $url  =  $postPage;
    
    
    $btn1Array =  array(
                        
                        "type" => "click",
                        "name" => urlencode("招生&风云"),
                        "key"  => "MENU1",
                        "sub_button" =>  array(
                                               array(
                                                     "type" => "click",
                                                     "name" => urlencode("荣誉资质"),
                                                     "key"  => "MENU_FENGYUN_CIMMA"
                                                     ),
                                               array(
                                                     "type" => "click",
                                                     "name" => urlencode("我要报名"),
                                                     "key"  => "MENU_APPLAY_DISCOUNTS"
                                                     ),
                                               
                                               array(
                                                     "type" => "click",
                                                     "name" => urlencode("招生管理"),
                                                     "key"  => "MENU_MANAGE_NEWSTUDENT"
                                                     ),
													 
												array(
                                                     "type" => "click",
                                                     "name" => urlencode("最新开班"),
                                                     "key"  => "MENU_MANAGE_NEWCLASS"
                                                     )
                                               
                                               )
                        
                        );

    
    
    $btn2Array = array
    (
     "type" => "click",
     "name" => urlencode("学员&教务"),
     "key"  => "MENU2",
     "sub_button" =>  array(
                            array(
                                  "type" => "click",
                                  "name" => urlencode("每天签到"),
                                  "key"  => "MENU_STUDENT_LOCATION"
                                  ),
                            array(
                                  "type" => "click",
                                  "name" => urlencode("学生请假"),
                                  "key"  => "MENU_STUDENT_ASKLEAVE"
                                  ),
                            array(
                                  "type" => "click",
                                  "name" => urlencode("学生成绩"),
                                  "key"  => "MENU_STUDENT_TESTMARK"
                                  ),
						
						 array(
							   "type" => "click",
								"name" => urlencode("考勤查询"),
								"key"  => "MENU_MANAGER_ATTEND"
							
                            ),
						  array(
								"type" => "click",
								"name" => urlencode("教务管理"),
								"key"  => "MENU_MANAGER_ADVANCE"
									   )
					 )
 
     );
    
    
    $btn3Array = array(
                       
                       "type" => "click",
                       "name" => urlencode("企业&招聘"),
                       "key"  => "MENU3",
                       "sub_button" =>  array(
                                              array(
                                                    "type" => "click",
                                                    "name" => urlencode("最新招聘"),
                                                    "key"  => "MENU_COMPANY_NEWOFFER"
                                                    ),
                                              array(
                                                    "type" => "click",
                                                    "name" => urlencode("我要发布"),
                                                    "key"  => "MENU_COMPANY_PROVIDE"
                                                    )
                                              )
                       
                       );
    
    
    

    $data =  urldecode(json_encode(array("button"=>array($btn1Array,$btn2Array,$btn3Array))));
    
    //echo  $data;
    
    //echo "\n" . $url . "\n";
	
    //$return_content = HttpClient::quickPost($url,$data);
    //list($return_code, $return_content) = http_post_data($url, $data);
    //$return_content = http_post_data($url, $data);
	
	$return_content = curlPost($data,$url);

    echo "3333";
    print_r ($return_content);
    
    
?>
