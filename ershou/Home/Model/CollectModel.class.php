<?php
namespace Home\Model;
use Think\Model\RelationModel;

class CollectModel extends RelationModel{

	protected $_link = array(
		'goods_used'=>array(
			'mapping_type'=>self::BELONGS_TO,
			'foreign_key'=>'goods_id',
			'mapping_fields'=>'goods_name,goods_price,goods_id,is_sale,goods_thumb',
			)

		);






}



?>