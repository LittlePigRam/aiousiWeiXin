<?php
    /**
     * news 咨询类  获取图文 信息 类
     */
    
    require_once("common.class.php");
    require_once("utileMethod.php");
    
    class News
    {
        
        public function News()
        {
            
        }
        
        /* 类型 直接从 t_news 表读取 */
        public function  getNewsItemsArray ($newType,$nums)
        {
            $con = connectMysql();
            selectDB("fyweixin", $con);
            $sqlSel = "SELECT title,description,imageUrl,url,type,time FROM t_news WHERE type = \"$newType\"  ORDER BY time ASC LIMIT 0 , $nums";
            $result = mysql_query($sqlSel);
            if ($result)
            {
                $newsItems = array();
                $num = 0;
                while($row = mysql_fetch_array($result, MYSQL_ASSOC))
                {
                    $imageUrl = $row["imageUrl"];
                    if(stripos($imageUrl, "http://")  === false)
                    {
                        $imageUrl =  "http://"  . $_SERVER['HTTP_HOST'] . "/weixinServer/" . $row["imageUrl"];
                    }
                    
                    $url = $row["url"];
                    if(stripos($url, "http://") === false)
                    {
                         $url =  "http://" . $_SERVER['HTTP_HOST'] . "/weixinServer/" . $row["url"];
                    }
                    
    
                    $newsItems[] = array("Title"       => $row["title"],
                                         "Description" => $row["description"],
                                         "PicUrl"      => $imageUrl,
                                         "Url"         => $url
                                         );
                    $num++;
                    if($num == $nums)
                    {
                        return $newsItems;
                    }
                    
                }
                return $newsItems;
            }

        }
        
    }
    
?>