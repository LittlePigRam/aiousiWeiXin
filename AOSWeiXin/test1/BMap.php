<?php
   /*
   此页面直接获取html5地理位置
   */
	require_once("../common.class.php");
	require_once("../studentoperatedata.php");
    require_once("../utilemethod.php");
    
    header("content-type:text/html;charset=utf-8");
	//form 表单数据提交到本页处理
	if(!empty($_POST['lngName'])   &&  !empty($_POST['latName']) )
	{
		  $openID = $_GET['openID'];
          $location_x = $_POST['lngName'];
          $location_y = $_POST['latName'];
    
		  $distance = getDistance($location_x, $location_y, $DESTINATION_X, $DESTINATION_Y);
		  $stuRegisterData = new StudentRegister();
		  
		  // public function insterStudentRegister($openID, $time, $location, $location_x, $location_y, $distance)
		  if ($stuRegisterData->insterStudentRegister($openID, timestampToYYMMDDHHMMSS(),$_POST['placeName'],$location_x, $location_y, $distance))
		  {
			  echo "<script>alert(\"签到成功，可返回主页！\");</script>";
		  }
		  else
		  {
			  echo "<script>alert(\"未能签到，返回主页重新签到！\");</script>";
		  }
	}
	
?>

<!DOCTYPE html>
<html>
    <head>
        <title>H5地理位置Demo</title>
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width">
        <meta name="format-detection" content="false">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="http://api.map.baidu.com/api?v=1.3" type="text/javascript"></script>
        <script type="text/javascript" src="convertor.js"></script>
        <script type="text/javascript" src="../pages/common.js"></script>
    </head>
    <body>
        <div id="map" style="width:320px; height:200px">
        </div>
        <div>
        <form name="form1" method="POST" onsubmit="return checkForm()">
        <table><tr><td>经 度:</td><td><input type="text" name="lngName" id="lngNameID" readonly="readonly"  style="width:90%"  /><td><tr>
               <tr><td>纬 度:</td><td><input type="text" name="latName" id="latNameID" readonly="readonly"  style="width:90%"  /><td><tr>
               <tr><td>地 址:</td><td><input type="text" name="placeName" id="placeNameID"  readonly="readonly" style="width:90%"  /><td><tr></table>
               <tr><td colspan="2"><input type="submit" value="提交签到位置" style="width:60%" /></td><tr>
        </form>  
        </div>      
        
    </body>
    
    <script type="text/javascript">
	
	
	function checkForm()
	{
			//if(document.getElementById("comNameID").value)
			var lngName  = document.getElementById("lngNameID").value;
			var latName  = document.getElementById("latNameID").value;
			if(lngName==null || lngName.length < 2 || latName==null || latName.length < 2)
			{
				alert("没有获取到经纬度，请刷新页面重新定位！");
				return false;
			}
			
			return true;
    }

  
    function handleSuccess(position){
        // 获取到当前位置经纬度  本例中是chrome浏览器取到的是google地图中的经纬度
        var lng = position.coords.longitude;
        var lat = position.coords.latitude;
       
	    document.getElementById("lngNameID").value = lng;
		document.getElementById("latNameID").value = lat;
	    
	    // 调用百度地图api显示
        var map = new BMap.Map("map");
        var ggPoint = new BMap.Point(lng, lat);
        // 将google地图中的经纬度转化为百度地图的经纬度
        BMap.Convertor.translate(ggPoint, 2, function(point){
                                 var marker = new BMap.Marker(point);
                                 map.addOverlay(marker);
                                 map.centerAndZoom(point, 15);
                                 });
								 
	    /*
	    var msg =  "获取到地理位置\n经度:"+  lng  +  "\n纬度:"  +  lat  +  "\确认提交位置签到?";
        if (window.confirm(msg))
        {
            document.form1.submit();
            return true;
        }
        return false;
		*/
        
        //反向地理编码 获取位置名称
        var gc = new BMap.Geocoder();
        var dress = gc.getLocation(ggPoint, function(rs){
                                   var addComp = rs.addressComponents;
                                   document.getElementById("placeNameID").value=addComp.province+addComp.city+addComp.district+addComp.street+addComp.streetNumber;
                               
                                   });
        
        
        //将坐标 和 openID 存放到cookie中，以备php使用
        /*
		var es;
        es = document.location.href.split("=")[1];
        document.cookie  = 'position=' + String(lat) + '#' + String(lng) + '#' + String(es);
        window.location = "../insterStuRegister.php?openID=" + String(es) + "&&latitude=" + lat + "&&lngitude=" + lng;
        */
    }
    
    function handleError(error)
	{
        alert("HTML5无法定位,未能签到，返回主页发送地理位置签到！");
    }
	
	
		
	function startGetExplorLocation()
	{
		  if (window.navigator.geolocation)
		  {
			  var options = {
				  enableHighAccuracy: true,
			  };
			  window.navigator.geolocation.getCurrentPosition(handleSuccess, handleError, options);
		  } else {
			  alert("浏览器不支持html5来获取地理位置信息");
		  }
	}
	
	window.onload=function()
    {
        //页面加载完成后执行以下表达式
        //initialSomeDocValues();
 		startGetExplorLocation();
    }  
	  
	
    </script>

</html>