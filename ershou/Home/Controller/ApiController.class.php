<?php
namespace Home\Controller;
use Think\Controller;

class ApiController extends Controller {
  public function usedList(){

    $GoodsList=M('GoodsUsed');
    $list=$GoodsList->where('1')->select();
    $list=json_encode($list);
    //$list=preg_replace("#\\\u([0-9a-f]+)#ie","iconv('UCS-2','UTF-8', pack('H4', '\\1'))",$list);

    print_r($list);
  }





}