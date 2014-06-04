<?php
	  require_once("header.php");
?>
<style type="text/css">
img {
	height: 120px;
	width: 200px;
}
</style>
 <div  class="jotitle">
  风云教育@学员就业
</div>
<div style="text-align:left;padding-left:10px;padding-right:10px"  >
  <h4>
  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp经过几个月的封闭式授课、练习、做项目，学员们已经具备一定的开发能力和学习能力，已经能够很好的融入到工作中...。
  <h4>
</div>
<div style="text-align:center" >
<?php
       
        $files = getDirFiles("./studentsJobPic");
        foreach($files  as $key => $value)
        {
            echo   "<img alt=\"\" src=\"studentsJobPic/$value\" /><p>";
        }
		echo "更多... http://www.17education.com";
    	echo "</div>";

	 
	    require_once("footer.php");
?>