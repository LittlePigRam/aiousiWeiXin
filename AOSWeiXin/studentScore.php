<?php
    /**
     * wechat student score
     */
    
    /*
     *
        学员成绩信息类
     *
     */
    class StudentScoreInfo
    {
        public function StudentScoreInfo()
        {
            
        }
        //插入数据
        public function insterStudentInfo($openID, $name, $className, $subject, $score, $time)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentscore";
            
            $result = mysql_query("INSERT INTO $tableName (openID, name, tel, className, sex, specialty, school, weinxinNum, image) VALUES (\"$openID\", \"$name\", \"$tel\", \"$className\", $newSex, \"$specialty\", \"$school\", \"$weinxinNum\", \"$image\")");
            
            if($result)
                return true;
            else
                return false;
            
            mysql_close($con);
        }
        
        //查询学生成绩信息
        public function searchStuScoreInfo($openID)
        {
            if (!linkMysql())
                return false;
            
            $tableName = "studentscore";
            
            $sqlSel="SELECT * FROM $tableName WHERE openID='" . $openID . "'";
            
            $result = mysql_query($sqlSel);
            if (!$result) return false;
            
            $num_rows = mysql_num_rows($result);
            
            $scoreArray = array();
            
            if($num_rows >= 1)
            {
                while ($row = mysql_fetch_array($result))
                {
                    array_push($scoreArray, $row);
                }
            }
            
            return $scoreArray;
            
            mysql_free_result($result);
            mysql_close($con);
        }
    }
    
    ?>