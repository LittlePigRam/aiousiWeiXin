<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">

        <link rel="stylesheet" type="text/css" href="../css/pageLayoutCss.css"/>

        <script src="../../jquery-2.1.1.min.js"></script>
        <script>
            function freshRegInfoByClassName(className) {
                /*
                $("#showRegInfo").load("test.php?className=" + className, function(response, status, xhr){

                        if (status == "success")
                        {
                            $("#showRegInfo").html($(response));
                            //alert(className);
                        }
                    });
                */

                var claName = document.getElementById("claName").value;
                var beginTime = document.getElementById("beginTime").value;
                var endTime = document.getElementById("endTime").value;

                document.cookie = "className" + "=" + claName + ";beginTime" + "=" + beginTime + "endTime" + "=" + endTime;
                window.location = "fystuRegister.php";
            }

            function freshRegInfoByFromTime(beginTime) {
                alert(beginTime);
            }

            function freshRegInfoByToTime(toTime) {
                alert(toTime);
            }

            window.onload = function()
            {
                //检查select各项所选
                if (document.cookie.length > 0)
                {
                    var className = getCookie('className');
                    if(className)
                        document.getElementById("claName").value = className;
                    else
                        document.getElementById("claName").index = 0;

                    var beginTime = getCookie('beginTime')
                    if (beginTime)
                        document.getElementById("beginTime").value = beginTime;
                    else
                        document.getElementById("beginTime").index = 0;

                    var endTime = getCookie('endTime');
                    if (endTime)
                        document.getElementById("endTime").value = endTime;
                    else
                        document.getElementById("endTime").index = 0;
                }
            }

            //js 获取cookie的函数
            function getCookie(objName)
            {
                var strArr = document.cookie.split(";");
                if (strArr.length == NULL)
                    return null;

                for (var i = 0; i < strArr.length; i++)
                {
                    if (strArr[i] == objName)
                    {
                        return unescape(strArr[i]);
                    }
                }
            }

        </script>
    </head>

    <body>
    <?php
        //学生签到信息查询
        require_once("../../../utileMethod.php");
        echo "<div id=\"selctedInfo\">";

            //按照老师表 搜索所有班级名字
            if (!linkMysql())
                return false;

            $tableName = "teacherinfo";

            $sqlSel = "SELECT * From $tableName ORDER BY className";

            $result = mysql_query($sqlSel);
            if (!$result)
            {
                echo "<script>alert(\"fystuRegister.php  get teacherinfo fail\")</script>";
                return false;
            }

            $num_rows = mysql_num_rows($result);

            //按照学生签到表 搜索签到学生
            $stuTableName = "studentregister";

            $stuSqlSel = "";

            if (isset($_COOKIE["className"]))
            {
                $stuSqlSel = "SELECT * From $stuTableName Where className = ' " . $_COOKIE['className'] . " 'ORDER BY className,name";
                //echo $_COOKIE["className"] . "\n";
            }
            else if (isset($_COOKIE["beginTime"]))
                $stuSqlSel = "SELECT * From $stuTableName Where time = '" . $_COOKIE["beginTime"] . "' ORDER BY className,time";
            else
                $stuSqlSel = "SELECT * From $stuTableName ORDER BY className,name";

            $stuResult = mysql_query($stuSqlSel);

            if (!$stuResult)
            {
                echo "<script>alert(\"fystuRegister.php  get studentRegisterInfo fail\")</script>";
                return false;
            }

            $stuNum_rows = mysql_num_rows($stuResult);

            echo "$stuNum_rows";

            echo"<table>
                <tr>
                    <td>OpenID</td>
                    <td>姓名</td>
                    <td>
                        <select id=\"claName\" onchange=\"freshRegInfoByClassName(this.options[this.selectedIndex].value)\">
                            <option value=\"all\">all</option>";

                             if ($num_rows >= 1)
                             {
                                //return $result;
                                while ($row = mysql_fetch_array($result))
                                {
                                     if (strcmp($row['className'],"") != 0 && strcasecmp($row['className'],"all") != 0)
                                        echo "<option value=\"{$row['className']}\">" . $row['className'] . "</option>";
                                }
                             }

                            echo"
                        </select>
                    </td>
                    <td>
                        <select id=\"beginTime\" onchange=\"freshRegInfoByFromTime(this.options[this.selectedIndex].value)\">
                            <option>null</option>
                            <option>time1</option>
                            <option>time2</option>
                            <option>time3</option>
                            <option>time4</option>
                        </select>
                    </td>
                    <td>
                        <select id=\"endTime\" onchange=\"freshRegInfoByToTime(this.options[this.selectedIndex].value)\">
                            <option>null</option>
                            <option>time11</option>
                            <option>time22</option>
                            <option>time33</option>
                            <option>time44</option>
                        </select>
                    </td>
                </tr>";

                if ($stuNum_rows >= 1)
                {

                   //return $result;
                   while ($stuRow = mysql_fetch_array($stuResult))
                   {
                        echo"<tr>";

                        if (strcmp($stuRow['openID'],"") == 0)
                            contnue;
                        echo "<td>" . $stuRow['openID'] . "</td>";
                        echo "<td>" . $stuRow['name'] . "</td>";
                        echo "<td>" . $stuRow['className'] . "</td>";
                        echo "<td>" . $stuRow['time'] . "</td>";
                        echo "<td>" . $stuRow['time'] . "</td>";

                        echo "</tr>";
                   }
                }
                else
                    echo "<script>alert(\"fystuRegister.php  studentRegisterInfo rows = 0 学生数据信息为空\")</script>";

            echo "</table></div>";

        echo "<div id=\"showRegInfo\">";

        echo "</div>";

        ?>

    </body>

</html>