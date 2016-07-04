<?php 




/**
 * 返回user_id
 * @return [type] [description]
 */
function getId(){
  return $_SESSION['user_id'];
}


/**
 * 返回时间戳
 * @return [type] [description]
 */
function getTime(){
  return time();
}

function getCity(){
  return $_COOKIE['user_city'];
}



function verify_check($verify){
  $verify_check = new \Think\Verify();
  $rs=$verify_check->check($verify);
  return $rs;
}



function isLogin(){
  if(isset($_SESSION['user_email'])){
    $user=M('user');
    $where['user_email']=$_SESSION['user_email'];           
    $arr_user=$user->field('user_name,user_icon')->where($where)->find();
    return $arr_user;
  }else{
    return false;
  }

}


/**
 * 时间戳格式化为 xx天前
 * @param  $time时间戳
 * @return $rs字符串
 */
function time_format($time){
 $sub=time()-$time;
 if ($sub<60) {
  $rs=$sub.'秒前';
  return $rs; 
}
if($sub>=60){
  if($sub<3600){
   $min=$sub/60;
   $rs=floor($min);
   $rs=$rs.'分钟前';
   return$rs;    	    			
 }
 if($sub>=3600 && $sub<3600*24){
   $house=$sub/3600;
   $rs=floor($house);
   $rs=$rs.'小时前';
   return$rs;    			
 }
 if($sub>=3600*24 && $sub<3600*24*365){
   $one_day=3600*24;
   $day=$sub/$one_day;
   $rs=floor($day);
   $rs=$rs.'天前';
   return$rs;   
 }
 if($sub>=3600*24*365){
   $one_years=3600*24*365;
   $years=$sub/$one_years;
   $rs=floor($years);
   $rs=$rs.'年前';
   return$rs;   
 }    		
}

}






?>