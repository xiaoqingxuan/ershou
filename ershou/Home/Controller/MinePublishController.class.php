<?php
namespace Home\Controller;
use Think\Controller;


// 发布更新等功能

class MinePublishController extends MethodController{
	public function __construct($no_login){
		parent::__construct();
		if(!isset($no_login)){
			$this->_login();
		}
		if(empty($_COOKIE['school_id']) || !isset($_COOKIE['school_id'])){
			$this->error('错误代码55，cookie的school_id没有。');
		}
		array_push($this->_rules_comp,array('school_id',$this->school['school_id'],1));
	}



	public $_rules=array(
		array('goods_name','require','抱歉,标题必须填写',1), 
		array('goods_img_1','require','抱歉,图片至少需上传一张',1), 							
		array('goods_price','0.0001,500000','抱歉,价格请填写正确的数字',1,'between'),
		array('goods_price_pre','0.0001,500000','抱歉,原价请填写正确的数字',1,'between'),		
		array('goods_desc','require','抱歉,物品描述必填',1),
		array('trade_address','require','抱歉,交易地点必填',1),
		array('cat_id','require','抱歉,请选择商品类别',1),
		array('phone_number','number','抱歉,手机号必须是数字',1),  
		array('agree_rule','on','抱歉,必须同意发布规则',1,'equal')								
		);


	public $_rules_comp=array(
		array('user_id','getId',1,'function'),
		array('add_time','getTime',1,'function'),
		);
	public $_rulesSeek=array(
		array('seek_name','require','抱歉,标题必须填写',1), 
		array('expect_price','0.0001,500000','抱歉,价格请填写正确的数字a',1,'between'),
		array('seek_desc','require','抱歉,物品描述必填',1),
		array('trade_address','require','抱歉,交易地点必填',1),
		array('cat_id','require','抱歉,请选择商品类别',1),
		array('phone_number','number','抱歉,手机号必须是数字',1),  
		array('agree_rule','on','抱歉,必须同意发布规则',1,'equal')								
		);




// 处理发布闲置
	public function actEditUsed(){


		$Goods=D('goods_used');
		$arr=$Goods->validate($this->_rules)->auto($this->_rules_comp)->create();

		if(!$arr){
			$this->error($Goods->getError());
		}else{
			// 图片处理
			//取第一张图作为缩略图	
					 
			$arr['goods_thumb']=self::img_pro($arr['goods_img_1'],'thumb',215,215);
			$arr['goods_img_1']=self::img_pro($arr['goods_img_1'],'yasuo',500,500);
			if(isset($arr['goods_img_2'])){
				$arr['goods_img_2']=self::img_pro($arr['goods_img_2'],'yasuo',500,500);
			}else{
				$arr['goods_img_2']=null;
			}
			if(isset($arr['goods_img_3'])){
				$arr['goods_img_3']=self::img_pro($arr['goods_img_3'],'yasuo',500,500);
			}else{
				$arr['goods_img_3']=null;				
			}
			if(isset($arr['goods_img_4'])){
				$arr['goods_img_4']=self::img_pro($arr['goods_img_4'],'yasuo',500,500);
			}else{
				$arr['goods_img_4']=null;

			}

			// 更新地址/QQ/手机号到用户个人信息
			$User=M('user');
			$save=array('default_phone'=>$arr['phone_number'],'default_qq'=>$arr['qq_number'],'default_address'=>$arr['trade_address']);
			$User->where(array('user_id'=>$_SESSION['user_id']))->save($save);
			


			$rs=$Goods->add($arr);

			if($rs){
				$this->success('发布成功。');
			}else{
				$this->error('抱歉，发布失败。');
			}
		}

	}	



	public function publishUsed(){
		$img_url=C('TMPL_PARSE_STRING');
		$img_url='http://'.$_SERVER['HTTP_HOST'].$img_url['__IMG__'].'/goods/original/'.$_GET['school_id'];
		$this->assign('img_src',$img_url);
		$this->assign('action',__MODULE__.'/MinePublish/actEditUsed/?school_id='.$this->school['school_id']);		
		$this->assign('edit_goods',null);		
		$this->assign('publishUsed');//nav_item_active
		$this->assign('title','发布闲置');
		$this->assign('publish_name','发布闲置');//编辑块include_edit_used的标题
		$this->assign('publish_button_name','发布');//编辑块iinclude_edit_used的标题		

		$this->readCat();
		//读取默认地址/手机号码
		$this->readDefault();

		$this->display('mine_publish_used');
	}






	public function publishSeek(){

		$this->assign('action',__MODULE__.'/MinePublish/actEditSeek/');		
		$this->assign('edit_goods',null);		
		$this->assign('publishSeek');//nav_item_active
		$this->assign('title','发布求购');
		$this->assign('publish_name','发布求购');//编辑块include_edit_used的标题
		$this->assign('publish_button_name','发布');//编辑块iinclude_edit_used的标题		

		//读取栏目
		$this->readCat();
		//读取默认地址/手机号码
		$this->readDefault();


		$this->display('mine_publish_seek');
	}

	public function actEditSeek(){
		$Goods=M('goods_seek');
		$arr=$Goods->validate($this->_rulesSeek)->auto($this->_rules_comp)->create();

		if(!$arr){
			$this->error($Goods->getError());
		}else{
			$arr['seller_id']=$_SESSION['user_id'];
			$rs=$Goods->add($arr);
			if($rs){
				$this->success('发布成功');
			}
		}

	}	
	public function editSeek(){

		$seek_id=$_GET['seek_id'];

		$Seek=M('goods_seek');
		$find=$Seek->field('click_count,add_time,seek_school,city',true)->where(array('seek_id'=>$seek_id))->find();

		$cat_list=$this->formatCat($find['cat_id']);
		$this->assign('cat_list',$cat_list);

		$this->assign('action',__CONTROLLER__.'/actUpdateSeek/?seek_id='.$seek_id);		
		$this->assign('edit_goods',$find);
		$this->assign('listSeek');//nav_item_active
		$this->assign('title','修改内容');
		$this->assign('publish_name','修改内容');//编辑块include_edit_used的标题
		$this->assign('publish_button_name','更新');//编辑块include_edit_used的标题		
		$this->display('mine_edit_seek');
	}


/**
 * 读取栏目并格式化 将需要的栏目名移动到第一个位置 返回新的cat_list
 * @return [type] [description]
 */
protected function formatCat($cat_id){
	/**读取栏目**/
	$cat_list=parent::readCat();		
	$sql='select cat_name,cat_id from es_category where cat_id='.$cat_id;
	$M=M();
	$cat_now=$M->query($sql);

	foreach ($cat_list as $k => $v) {
		if($v['cat_id']==$cat_id){
			unset($cat_list[$k]);
			break;
		}
	}

	array_unshift($cat_list,$cat_now);
	$cat_list[0]=$cat_now[0];

	return $cat_list;
}

/**
 * 将多张图片放入一个数组 返回$find['img_array']
 * @param  $goods_img，需要放入一个数组的图片集合数组，包含键值。范例：		$goods_img=array($find['goods_img_1'],$find['goods_img_2'],$find['goods_img_3'],$find['goods_img_4']);
 * @param  $find $goods_img所在的数组
 * @param  $arr_name 放到$find时的关联键名字 默认img_array
 * @return 加工后的$find(多张图放在一个数组，放在了$find['img_array'])
 */
protected function imgToArray($goods_img,$find,$arr_name){
	if(!isset($arr_name)){
		$arr_name='img_array';
	}
	$img_count=count($goods_img);
	for($i=0;$i<$img_count;$i++){
			//删除去掉为空的单元
		if(empty($goods_img[$i])){
			unset($goods_img[$i]);
		}
	}
		// 赋值给原来的数组
	$find[$arr_name]=$goods_img;
	return $find;
}

public function editUsed(){
	$goods_id=$_GET['goods_id'];
	$Goods=M('goods_used');
	$find=$Goods->field('click_count,add_time,goods_school',true)->where(array('goods_id'=>$goods_id))->find();


		// 将多张图片放到一个数组
	$goods_img=array($find['goods_img_1'],$find['goods_img_2'],$find['goods_img_3'],$find['goods_img_4']);
	$find=$this->imgToArray($goods_img,$find);
		// 生成img_array_two单元，存放文件名和完整路径，删原来的$find['img_array']
	$img_url=C('TMPL_PARSE_STRING');
	$img_url='http://'.$_SERVER['HTTP_HOST'].$img_url['__IMG__'].'/goods/yasuo/'.$_GET['school_id'];
	for($i=0;$i<count($find['img_array']);$i++){
		$c[$i]['file_name']=$find['img_array'][$i];
		$c[$i]['src']=$img_url.'/'.$c[$i]['file_name'];

	}
	$find['img_array_two']=$c;
	unset($find['img_array']);
	$cat_list=$this->formatCat($find['cat_id']);


	$this->assign('cat_list',$cat_list);

	$this->assign('action',__MODULE__.'/MinePublish/actUpdate/?school_id='.$this->school['school_id'].'&goods_id='.$goods_id);		
	$this->assign('edit_goods',$find);
	$this->assign('index');//nav_item_active
	$this->assign('title','修改内容');
	$this->assign('publish_name','修改内容');//编辑块include_edit_used的标题
	$this->assign('publish_button_name','更新');//编辑块include_edit_used的标题		
	$this->assign('cat_list',$cat_list);
	$this->display('mine_edit_used');
}

	public function actUpdate(){

		$goods_id=$_GET['goods_id'];
		$Goods=M('goods_used');
		$arr=$Goods->validate($this->_rules)->auto($this->_rules_comp)->create();



		// 图片值
		if(!$arr){
			$this->error($Goods->getError());
		}else{
			$arr['goods_thumb']=self::img_pro($arr['goods_img_1'],'thumb',215,215);

			$arr['goods_img_1']=self::img_pro($arr['goods_img_1'],'yasuo',500,500);
			if(isset($arr['goods_img_2'])){
				$arr['goods_img_2']=self::img_pro($arr['goods_img_2'],'yasuo',500,500);
			}else{
				$arr['goods_img_2']=null;
			}
			if(isset($arr['goods_img_3'])){
				$arr['goods_img_3']=self::img_pro($arr['goods_img_3'],'yasuo',500,500);
			}else{
				$arr['goods_img_3']=null;				
			}
			if(isset($arr['goods_img_4'])){
				$arr['goods_img_4']=self::img_pro($arr['goods_img_4'],'yasuo',500,500);
			}else{
				$arr['goods_img_4']=null;
			}

			$where['goods_id']=$goods_id;
			$rs=$Goods->where($where)->save($arr);
			if($rs){
				$this->success('修改成功',U('Mine/index'));
			}else{
				$this->error('抱歉，修改失败');
			}
		}

	}

	protected function img_pro_every(){

	}

	/**
	 * 生成215*215缩略图
	 * @param  [type] $file_name [description]
	 * @return [type]            [description]
	 */
	static function img_pro($file_name,$type,$width,$height){
		if($type=='thumb'){
			$src=C('IMG_THUMB_SRC');	
		}elseif($type=='yasuo'){
			$src=C('IMG_YASUO_SRC');	
		}
		$IMG_ORI_SRC=C('IMG_ORI_SRC');
		$IMG_THUMB_SRC=C('IMG_THUMB_SRC');	
		$Image = new \Think\Image();

		// echo $IMG_ORI_SRC.'/'.$file_name;
		
		print_r($IMG_ORI_SRC.'/'.$file_name);
		$Image->open($IMG_ORI_SRC.'/'.$file_name);
		$Image->thumb($width,$height)->save($src.'/'.$file_name);

		return $file_name;
	}

	// 更新求购
	public function actUpdateSeek(){
		$seek_id=$_GET['seek_id'];
		$Seek=M('goods_seek');
		$arr=$Seek->validate($this->_rulesSeek)->auto($this->_rules_comp)->create();


		if(!$arr){
			$this->error($Seek->getError());
		}else{
			$where['seek_id']=$seek_id;
			$rs=$Seek->where($where)->save($arr);
			if($rs){
				$this->success('修改成功',U('Mine/listSeek/'));
			}else{
				$this->error('抱歉，修改失败');
			}
		}

	}
	// 下架一个商品	
	public function isSale(){

		if($_POST['code']=='used'){
			$id=$_POST['goods_id'];
			$is_sale=$_POST['is_sale'];			
			$m='goods_used';
			$k='goods_id';
			$k2='is_sale';
		}elseif($_POST['code']=='seek'){
			$id=$_POST['goods_id'];				
			$is_sale=$_POST['is_sale'];			
			$m='goods_seek';
			$k='seek_id';
			$k2='is_seek';			
		}else{
			$rs=array('bool'=>false);
			$rs=json_encode($rs);
			echo '['.$rs.']';
			exit();
		}

		$Goods=M($m);

		if($is_sale==0){
			$is_sale=1;
		}elseif($is_sale==1){
			$is_sale=0;
		}else{
			exit('is_sale错误');
		}

		$where[$k]=$id;
		$save=array($k2=>$is_sale);
		$rs=$Goods->where($where)->save($save);

		$rs=array('bool'=>$rs);
		$rs=json_encode($rs);
		echo '['.$rs.']';
	}



}