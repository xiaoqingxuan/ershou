<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends MethodController {
  public function __construct(){
    parent::__construct();
    //显示面包屑频道
    $this->channel();    
    //显示分类
    $this->catName();

  }
  protected $field_used='user_id,goods_id,goods_thumb,goods_price,goods_name,add_time,trade_address';

  public function index(){
    $field=$this->field_used;
    $where=array('school_id'=>$this->school['school_id'],'is_sale'=>'1');
    $list=$this->actIns('GoodsUsed',$where,$field,16);  
    $this->assign('list',$list);
    $this->assign('used_list_nav_boolen');//列表/导航显示开关         
    $this->display('html/index');
  }


/**
 * 查询某用户正在卖的闲置
 * @return [type] [description]
 */
  public function user_selling(){
    $field=$this->field_used;
    $where=array('school_id'=>$_GET['school_id'],'is_sale'=>'1','user_id'=>$_GET['user_id']);
    $list=$this->actIns('GoodsUsed',$where,$field,10);  
    $this->assign('list',$list);
    $cat_find['cat_name']=$_GET['user_name'].'正在售卖...';
    $this->assign('cat_find',$cat_find);
    $this->assign('used_list_nav_boolen');//列表/导航显示开关         
    $this->display('html/index');
  }



/**
 * 显示频道和栏目
 * @return [type] [description]
 */
  protected function channel(){
    // 显示频道
    $Cn=M('channel');
    if(!isset($_GET['cn_id'])){
      $_GET['cn_id']=1;
    }
    $channel=$Cn->where('cn_id='.$_GET['cn_id'])->find();
    // 显示频道下的栏目名
    if(isset($_GET['cat_id'])){
      $Cat=M('category');
      $cat_find=$Cat->field('cat_name')->where('cat_id='.$_GET['cat_id'])->find();
      $this->assign('cat_find',$cat_find);
    }


    isset($_GET['cat_id'])?$cat_now=$_GET['cat_id']:$cat_now=false;
    $this->assign('cat_now',$cat_now);
    $this->assign('channel',$channel);    
  }

/**
 * 显示分类栏目
 * @return [type] [description]
 */
  protected function catName(){
    $this->readCat();
  }

  /**点击类别**/
  public function cat(){
    if ($_GET['cn_id']==1) {
      $obj='GoodsUsed';
      $field=$this->field_used;      
      $this->title('title','used');
      $this->assign('used_list_nav_boolen');//列表/导航显示开关
    }elseif($_GET['cn_id']==2){
      $obj='GoodsSeek';
      $this->title('title','seek');
      $field='user_id,seek_name,seek_desc,trade_address,expect_price,phone_number,is_seek,add_time';
      $this->assign('seek_list_nav_boolen');//列表/导航显示开关
    }
    $where=array();
    if(isset($_GET['cat_id'])){
      $where['cat_id']=$_GET['cat_id'];
      $where['school_id']=$_GET['school_id'];
      $where['is_sale']='1';
    }

    $list=$this->actIns($obj,$where,$field,10);        
    $this->assign('list',$list);
    $this->display('html/index');
  }

/**
 *  读取配置文件命名 并assign前台
 * @param  [type] $assign assign的第一个参数
 * @param  [type] $key    MINE配置文件中的key
 * @return [type]         [description]
 */
protected function title($assign,$key){
    $C=C("MINE");
    $this->assign($assign,$C[$key]);
}





}