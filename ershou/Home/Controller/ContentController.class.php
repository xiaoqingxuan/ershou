<?php
namespace Home\Controller;
use Think\Controller;

class ContentController extends MethodController {



/**
 * 将多张图片放入一个数组 返回$find['img_array']
 * @param  $goods_img，需要放入一个数组的图片集合数组，包含键值。范例：      $goods_img=array($find['goods_img_1'],$find['goods_img_2'],$find['goods_img_3'],$find['goods_img_4']);
 * @param  $find $goods_img所在的数组
 * @param  $arr_name 放到$find时的关联键名字 默认img_array
 * @param [type] $dir_name 读取的图片文件夹名默认yasuo
 * @return 加工后的$find(多张图放在一个数组，放在了$find['img_array'])
 */
protected function imgToArray($goods_img,$find,$arr_name,$dir_name){
    if(!isset($arr_name)){
        $dir_name='yasuo';
    }    
    if(!isset($arr_name)){
        $arr_name='img_array';
    }
    $img_url=C('TMPL_PARSE_STRING');
    $img_url='http://'.$_SERVER['HTTP_HOST'].$img_url['__IMG__'].'/goods/'.$dir_name.'/'.$find['school_id'].'/';
    $img_count=count($goods_img);
    for($i=0;$i<$img_count;$i++){
            //删除去掉为空的单元
        if(empty($goods_img[$i])){
            unset($goods_img[$i]);
        }else{
            $goods_img[$i]=$img_url.$goods_img[$i];            
        }
    }
        // 赋值给原来的数组
    $find[$arr_name]=$goods_img;
    return $find;
}

  public function index(){
    if(!isset($_GET['goods_id']) || $_GET['goods_id']==null ){
    	$this->error('抱歉，没有指定商品编号');
    	exit();
    }else{
    	$goods_id=$_GET['goods_id'];
    }
    $Goods=D('goods_used');
    $where=array('goods_id'=>$goods_id);
    $field='';
    $arr=$Goods->relation(true)->where($where)->find();
    $Goods->where($where)->setInc('click_count');//点击数增加

        // 将多张图片放到一个数组
    $goods_img=array($arr['goods_img_1'],$arr['goods_img_2'],$arr['goods_img_3'],$arr['goods_img_4']);
    $arr=$this->imgToArray($goods_img,$arr);


    


    if($arr){
        $arr['add_time']=time_format($arr['add_time']);
        $this->assign('goods',$arr);
        $this->assign('title',$arr['goods_name']);
    }else{
    	$this->error('抱歉，可能是商品编号出错了');
    	exit();    	
    }

    // 同类其他最新
    if(!empty($this->school['school_id'])){
        $where=array('cat_id'=>$arr['cat_id'],'school_id'=>$this->school['school_id'],'is_sale'=>'1');
        $cat_other=$Goods->where($where)->field('goods_id,goods_name,goods_thumb')->limit('5')->order('goods_id desc')->select();
        $this->assign('cat_other',$cat_other);        
    }


    //查询是否收藏
    if(isset($_SESSION['user_email'])){
        if(isset($_SESSION['user_id'])){
            $where=array('goods_id'=>$_GET['goods_id'],'type'=>'used','user_id'=>$_SESSION['user_id']);
            $rs=$this->queryLike($where);
        }else{
            $rs=false;
        }

        if($rs){
            $this->assign('like',true);
        }

    }

    $this->display();

}

/**
 * 查询是否收藏该商品        
 * @param  [type] $where [description]
 * @return boolen        [description]
 */
public function queryLike($where){
    $Like=M('collect');
    $rs=$Like->where($where)->find();
    if($rs){
        return true;
    }else{
        return false;
    }

}


/**
 * 处理点击收藏ajax请求
 * @return 
 */
public function like(){

    if(!isset($_SESSION['user_id'])){
        exit('01');
    }else{
        $_POST['user_id']=$_SESSION['user_id'];
        $_POST['add_time']=time();
    }

    if(!isset($_POST['goods_id'])){
        exit('0');
    }
    $Goods=M('collect');
    $where['goods_id']=$_POST['goods_id'];
    $where['type']=$_POST['type'];
    $where['user_id']=$_POST['user_id'];
        // $find=$Goods->where($where)->find(); 目前采用前端发来是否已经收藏减轻服务器压力所有屏蔽
    if($_POST['now']==1){
            //已经有则取消收藏
        $rs_=$Goods->where($where)->delete();
        if($rs_){
            exit('1');              
        }else{
            exit('00');
        }
    }
unset($_POST['now']);
$rs=$Goods->add($_POST);
    if($rs){
        exit('1');
    }else{
        exit('0');
    }
}




}