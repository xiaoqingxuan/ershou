<?php
function img_src($dir_name){
  $dir='./ershou/Common/images/goods/'.$dir_name.'/'.$_COOKIE['school_id'];
  if(!is_dir($dir)){
    $rs=mkdir($dir);
    if(!$rs){
      exit('建立学校图片目录失败。');
    }
  }
  return $dir;
}
if(isset($_GET['school_id'])){
  $src=$_GET['school_id'];
}else{
  $src=$_COOKIE['school_id'];
}


return array(
  //'配置项'=>'配置值'
  'SHOW_PAGE_TRACE' => true,

  //自定义常量
  'COOKIE_TIME'=>time()+3600*24*7,//学校的cookie时间
  'IMG_ORI_SRC'=>img_src('original'),//原图存放路径  
  'IMG_YASUO_SRC'=>img_src('yasuo'),//压缩存放路径
  'IMG_THUMB_SRC'=>img_src('thumb'),//缩略图存放路径
  'IMG_YASUO_SRC_ALL'=>'http://'.$_SERVER['HTTP_HOST'].'/ershou/ershou/Common/images/goods/yasuo/'.$src,//压缩存放完整路径
  'IMG_THUMB_SRC_ALL'=>'http://'.$_SERVER['HTTP_HOST'].'/ershou/ershou/Common/images/goods/thumb/'.$src,//缩略图存放完整路径


  // 模板常量
  'TMPL_PARSE_STRING'=>array(
    '__WEB_NAME__'=>'闲置网',
    '__JS__'=>'/ershou/ershou/Common/js',
    '__CSS__'=>'/ershou/ershou/Common/css',
    '__IMG__'=>'/ershou/ershou/Common/images',             
    '__UPLOAD_IMG__'=>'./ershou/Common/images',//相对于index.php
    '__UPLOAD__'=>'/ershou/ershou/Common/Uploads', 
    '__SEARCH_REMIND__'=>'多词搜索时请使用空格分离',
    '__INVALID_REASON__'=>'因物品已卖出',
    '__PUBLISH_REGULAR__'=>'#',//发布规则的url
    '__EDIT_USED__'=>'发布闲置',//主页header里发布按钮的内容
    '__USED_ZONE__'=>'闲置区',//导航栏里的
    '__SEEK_ZONE__'=>'求购区',    
    ),


  //自定义命名变量
  'MINE'=>array(
    'seek'=>'求购区',
    'used'=>'闲置区',
    ),


  // 数据库
  'DB_TYPE'=>'mysql',//数据库类型
  'DB_HOST'=>'localhost',//服务器地址
  'DB_NAME'=>'ershou',//库名
  'DB_USER'=>'root',//用户名
  'DB_PWD'=>'111111',//密码
  'DB_CHARSET'=>'utf8',//字符类型
  'DB_PREFIX'=>'es_',//数据表前缀
  'DB_PORT'=>3306,//端口号


  // // 数据库
  // 'DB_TYPE'=>'mysql',//数据库类型
  // 'DB_HOST'=>'qdm157037618.my3w.com',//服务器地址
  // 'DB_NAME'=>'qdm157037618_db',//库名
  // 'DB_USER'=>'qdm157037618',//用户名
  // 'DB_PWD'=>'w237067041',//密码
  // 'DB_CHARSET'=>'utf8',//字符类型
  // 'DB_PREFIX'=>'es_',//数据表前缀
  // 'DB_PORT'=>3306,//端口号


  );