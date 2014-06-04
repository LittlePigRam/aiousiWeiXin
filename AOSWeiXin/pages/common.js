 
 
 function JSSetCookie(name,value)
 {
        var Days = 1; //此 cookie 将被保存 30 天
        var exp = new Date();    //new Date("December 31, 9998");
        exp.setTime(exp.getTime() + Days*24*60*60*1000);
        
        document.cookie = name + "="+ encodeURI (value) + ";expires=" + exp.toGMTString();
     
        //document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
 }
 
 
 function JSGetCookie(name)//取cookies函数        
 {
        var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
        
        if(arr != null) return decodeURI(arr[2]); return null;
        
  }
  
 function JSDeleteCookie(name)
 {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = JSGetCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
 }