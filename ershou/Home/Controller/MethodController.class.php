<?php
namespace Home\Controller;
use Think\Controller;

class MethodController extends Controller {
  public $school=null;


  public function __construct(){
    parent::__construct();
    // 根据地址栏查询学校
    if(isset($_GET['school_id']) && !empty($_GET['school_id'])){
      $this->school=$this->getSchool();    
      $this->assign('school',$this->school['school_name']);
      $this->assign('school_id',$this->school['school_id']);
      // 地址栏school_id没了，但存在cookie时。重定向到cookie的school_id。
    }elseif(isset($_COOKIE['school_id']) && !isset($_GET['no_school']) ){
      // 后台模板代码中直接使用了cookie
      $this->school=array('school_name'=>$_COOKIE['school_name'],'school_id'=>$_COOKIE['school_id']);  
      // $this->assign('school',$this->school['school_name']);
      // $this->assign('school_id',$this->school['school_id']);
      if(empty($_GET)){
        $redirect=$_SERVER['REQUEST_URI'].'?school_id='.$this->school['school_id'];
        redirect($redirect);             
      }else{
        $redirect=$_SERVER['REQUEST_URI'].'&school_id='.$this->school['school_id'];
        redirect($redirect);        
      }      
      // 某些接口或post提交不需要学校
    }elseif(isset($_GET['no_school'])){
      // 地址栏和cookie都没有，给个热门的学校      
    }else{
      // 地址栏原来有get则用&否则用?
      if(empty($_GET)){
        $redirect=$_SERVER['REQUEST_URI'].'?school_id=3409';
        redirect($redirect);             
      }else{
        $redirect=$_SERVER['REQUEST_URI'].'&school_id=3409';
        redirect($redirect);        
      }

      
    }


  }


  /**
  *查询省名字和id
  * 请求：/ershou/index.php/Home/Phone/selectProvince/?receive={"key":"123"} 无其他请求参数
  *返回json
  *
  * 
   */
  public function selectProvince(){
    $School=M('region');
    $school_list=$School->where(array('region_type'=>1))->field('region_id,region_name')->select();
    $rs=array('state'=>'success','data'=>$school_list);
    exit(json_encode($rs));
  }


  public function selectSchool(){
    $parent_id=$_GET['user_province'];
    $School=M('region');
    $school_list=$School->where(array('region_type'=>2,'parent_id'=>$parent_id))->field('region_id,region_name')->select();
    $rs=array('state'=>'success','data'=>$school_list);  
    exit(json_encode($rs));    
  }




// 根据get的id查询学校
  static public function getSchool(){
    // 如果有cookie学校，并且cookie学校的id和get里的id一致，不查询数据库。
    if(isset($_COOKIE['school_id']) && $_COOKIE['school_id']==$_GET['school_id']){
      $school=array('school_name'=>$_COOKIE['school_name'],'school_id'=>$_COOKIE['school_id']);   
      return $school;
    }

    // 有学校id
    if(isset($_GET['school_id']) && !empty($_GET['school_id'])){
      $region_id=$_GET['school_id'];
      $Region=M('region');
      $field='region_name';
      $school_find=$Region->where(array('region_id'=>$region_id))->field($field)->find();
      if($school_find && !empty($school_find)){
        setcookie('school_name',$school_find['region_name'],time()+3600*24*300,'/');        
        setcookie('school_id',$region_id,time()+3600*24*300,'/');
        $school=array('school_name'=>$school_find['region_name'],'school_id'=>$region_id);
        return $school;
      }else{
        // 数据库没有该学校
        exit('get的id没有找到这个学校。');
        return false;
      }
    }

  // 首测登录(即无school的cookie)            
    if(!isset($_COOKIE['user_school'])){
      $ip=$_SERVER["REMOTE_ADDR"];
      $ip='223.73.155.47';
      $address='http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
      //可能查询超时 
      $context = stream_context_create(array(
        'http' => array(
        'timeout' => 2 //超时时间，单位为秒
        ) 
        ));  
      $rs=file_get_contents($address,0,$context);
    //因为不能访问api的失败
      if(!$rs){
        return self::school_false();
      }
    //返回的是失败或成功
      $rs=json_decode($rs,true);    

      if(!$rs['code']==0){
        // 查询城市失败 给个国内最热门的学校
        return $this->school_false();       
      }else{
        //查询城市成功 给个该城市最热门的学校
        $city=$rs['data']['city'];
        $field='goods_school,count(goods_school) as count';
        $rs=M('goods_used');
        $rs=$rs->field($field)->where(array('city'=>$city))->group('goods_school')->order('count DESC')->limit(1)->select();

        //若该城市没查到内容
        if(count($rs)==0){
          return $this->school_false();
        }

        $user_school=$rs[0]['goods_school'];
        setcookie('user_school',$user_school,time()+3600*24*300,'/');
        return $user_school;   
      }
    }else{
        //不是首测登录
      $user_school=$_COOKIE['user_school'];
      return $user_school;
    }    
  }
  // 定位学校在查询城市失败 给个国内最热门的学校
  static protected function school_false(){
    $rs=M('goods_used');
    $rs=$rs->field('goods_school,count(goods_id) as count')->group('goods_school')->order('count DESC')->limit(1)->select();

    if(count($rs)==0){
      return '没有最热门的学校~';
    }

    $user_school=$rs[0]['goods_school'];
    setcookie('user_school',$user_school,time()+3600*24*300,'/');
    return $user_school;    
  }














/**
 * 实例化数据表对象 并格式化时间 完成assign分页 返回查询到的信息
 * $current_page=null 第几页
 * @param  [type] $obj [description]
 * @return [type]      [description]
 */
static function actIns($obj,$where=array(),$field='',$page_=5,$relation=true,$current_page=null){
  if($relation){
    $Goods=D($obj);
  }else{
    $Goods=M($obj);    
  }
  $count_=$Goods->where($where)->count();

  $Page=self::page($count_,$page_);
  $This_=new MethodController();  
  $This_->assign('page',$Page->show());
  if(!empty($current_page)){//若指定第几页
    $Page->firstRow=(($current_page-1)*$Page->listRows);
  }


  if($relation){
    $list=$Goods->relation($relation)->where($where)->field($field)->limit($Page->firstRow.','.$Page->listRows)->order('add_time desc')->select();
  }else{
    $list=$Goods->where($where)->field($field)->limit($Page->firstRow.','.$Page->listRows)->order('add_time desc')->select();
  }  

//格式化时间
  if(isset($list['0']['add_time'])){
    foreach ($list as $key => $value) {
      $list[$key]['add_time']=time_format($value['add_time']);
    }
  }  
//格式化图片地址  
  $list=self::formatImgSrc($list);

  return $list;
}

/**
 * 格式化图片地址  
 * @param  array可能是1维或2维，若2维，则1维是索引。$is_one有定义则是1维。默认2维。
 * @return 格式化之后的输入参数
 */
static function formatImgSrc($list,$is_one){
  if(isset($is_one)){
    $list_return['0']=$list;
  }else{
    $list_return=$list;
  }
  //格式化图片地址  
  $img_url=C('IMG_THUMB_SRC_ALL').'/';
  
  if(isset($list_return['0']['goods_thumb']) || isset($list_return['0']['goods_img_1'])){
    foreach ($list_return as $k => $v) {
      $list_return[$k]['goods_thumb']=$img_url.$v['goods_thumb'];
      $list_return[$k]['goods_img_1']=$img_url.$v['goods_img_1'];
      if(!empty($list_return[$k]['goods_img_2'])){
        $list_return[$k]['goods_img_2']=$img_url.$v['goods_img_2'];        
      }
      if(!empty($list_return[$k]['goods_img_3'])){
        $list_return[$k]['goods_img_3']=$img_url.$v['goods_img_3'];        
      }
      if(!empty($list_return[$k]['goods_img_4'])){
        $list_return[$k]['goods_img_4']=$img_url.$v['goods_img_4'];        
      }          
    }
  }
  if(isset($is_one)){
    $list_return=$list_return['0'];
  }
  return $list_return;        
}


/**
 * 非登录退出 有登录查询用户名和用户头像
 * @return [type] [description]
 */
protected function _login(){
  if(isset($_SESSION['user_email'])){
    $user=M('user');
    $where['user_email']=$_SESSION['user_email'];           
    $arr_user=$user->field('user_name,user_icon')->where($where)->find();
    $this->assign('arr_user',$arr_user);
  }else{
    $this->error("抱歉，未登录，请先登录",U('Login/index'));
    exit();
  }

  $this->timeRem();

}

/**
 * assign时刻的提醒语
 */
public function timeRem(){
  $h=date('H',time());
  if($h>19 && $h<23){
    $time='晚上好。';
    $rem='';
  }elseif($h>=23 && $h<=24){
    $time='晚上好, ';
    $rem='早睡身体健康。';
  }elseif($h<6){
    $time='深夜了, ';
    $rem='忙碌的一天，早休息~';    
  }elseif($h>=6 && $h<=8){
    $time='早上好。';
    $rem='';
  }elseif($h>=12 && $h<=14){
    $time='午安,';
    $rem='小憩一会可以更加精神~';
  }else{
    $time='';
    $rem='';
  }
  $this->assign('time',$time);
  $this->assign('rem',$rem);

}



/**
 * ajax删一个商品 可扩展
 * @return [type] [description]
 */
public function delete(){

    if($_POST['code']=='used'){ //删二手
      $id=$_POST['goods_id'];
      $m='goods_used';
      $k='goods_id';
    }elseif($_POST['code']=='seek'){  //删求购
      $id=$_POST['goods_id'];       
      $m='goods_seek';
      $k='seek_id';
    }elseif($_POST['code']=='collection'){  //删收藏
      $id=$_POST['goods_id'];//删的值
      $m='collect';//哪个表
      $k='id';//删的键
    }else{
      $rs=array('bool'=>false);
      $rs=json_encode($rs);
      echo '['.$rs.']';
      exit();
    }


    $Goods=M($m);
    $where[$k]=$id;
    $rs=$Goods->where($where)->delete();    

    //若是删二手/求购，也删收藏表对应商品
    if(($m=='goods_used')||($m=='goods_seek')){
      $Obj=M('collect');
      $where['goods_id']=$id;
      $where['type']=$_POST['code'];      
      $rs_=$Obj->where($where)->delete();      
    }




    $rs=array('bool'=>$rs);
    $rs=json_encode($rs);
    echo '['.$rs.']';
  } 

/**
 * 读取用户的默认地址和手机号码assign
 * @return [type] [description]
 */
public function readDefault(){
    //读取默认手机号/地址
  $User=M('user');
  $default=$User->where('user_id='.$_SESSION['user_id'])->field('user_name,default_phone,default_address,default_qq')->find();
  $this->assign('default',$default);    
}



/**
 * 显示分类栏目
 * @return [type] [description]
 */
protected function readCat(){
  $Cat=M("category");
  $cat_list=$Cat->where('1')->field('cat_name,cat_id,cat_icon')->select();
  $this->assign('cat_list',$cat_list);
  return $cat_list;
}



  /**
   * 返回分页分页实例
   * @param  [type] $count_ 总数
   * @param  [type] $count 每页几个
   * @return [type]        返回分页实例
   */
  static function page($count_,$count){
    $Page=new \Think\PageBS($count_,$count);
    $Page->setConfig('prev','上一页');
    $Page->setConfig('next','下一页');
    return $Page;
  }



}