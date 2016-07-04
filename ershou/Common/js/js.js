$(document).ready(function(){


// content页的图片tab标签效果
var title=$('.tab_title li');
var content=$('.tab_content li');
title.mouseover(function(){
	$(this).addClass('current_').siblings().removeClass('current_');
	var index=$(this).index();
	content.eq(index).show().siblings().hide();
}).mouseout(function(){

});




// 提交学校
$('#submit').click(function(){
  var ddd = dialog({
    title: '提交我的学校',
    content:"<input type='text' placeholder='省名' id='submit_province' name='submit_province'><br /><br /><input type='text' placeholder='校名' id='submit_school' name='submit_school'>",
    ok: function () {
      var value = $('#submit_school').val();
      remind(':）感谢您反馈，我们会尽快添加。',2000);
    }
  });
  ddd.show();  

});

// to_top
var to_top=$('.to_top');
var top_distance=330;//到顶部距离
$(window).scroll(function(){
	if($(this).scrollTop()>top_distance){
		to_top.fadeIn();
	}else{
		to_top.fadeOut();			
	}
});
to_top.click(function(){
	$('body,html').animate({scrollTop:0},'slow');
});


//下架
$('.is_sale').click(function(){
	var goods_id=$(this).attr('act_id');
	var code=$(this).attr('code');				
	var is_sale_select=$('#'+goods_id+' #is_sale');
	var is_sale=$(this).attr('is_sale');
	var this_=$(this);
	$.ajax({
		type:'POST',
		url:_MODULE_+'/MinePublish/isSale/?no_school=1',
		data:{'goods_id':goods_id,'is_sale':is_sale,'code':code},
		success:function(rs){
			var rs=eval(rs);
			var bool=rs[0].bool;
			if(bool==1){
				if(is_sale==0){
					this_.attr('is_sale',1);
					if(code=='seek'){
						remind('操作成功。');
						var html='发布中...';							
					}else if(code=='used'){
						remind('操作成功。');
						var html='在售中...';														
					}

					var html_='<span class="glyphicon glyphicon-arrow-down"></span> <span>下架';
				}
				if(is_sale==1){
					this_.attr('is_sale',0);
					if(code=='seek'){
						remind('操作成功。');						
						var html='已下架';							
					}else if(code=='used'){
						remind('操作成功。');						
						var html='已售';														
					}
					var html_='<span class="glyphicon glyphicon-arrow-up"></span> <span>上架';						
				}
				is_sale_select.html(html);
				this_.html(html_);
			}else{
				alert('抱歉，下架失败');
			}
		},
		error:function(){
			remind('抱歉, 操作失败。');			
		}
	});
});

//删除
$('.delete').click(function(){
	var goods_id=$(this).attr('act_id');
	var code=$(this).attr('code');		
	var tr=$('#'+goods_id);

	$.ajax({
		type:'POST',
		url:_MODULE_+'/Method/delete/?no_school=1',
		data:{'goods_id':goods_id,'code':code},
		success:function(rs){

			var rs=eval(rs);
			var bool=rs[0].bool;
			if(bool==1){
				remind('删除成功。');				
				tr.hide('slow');
			}else{
				alert('抱歉，删除失败');
			}				

		},
		error:function(){
			remind('抱歉, 操作失败。');							
		}
	});
});



});




// 提醒
function remind(str,remain_time){
  remain_time =arguments[1]?arguments[1]:1000;	
	var d = dialog({
		content: str
	});
	d.show();
	setTimeout(function () {
		d.close().remove();
	}, remain_time);

}


/**
 * 用ajax实现点击变化的对象
 */
 ;(function($){	 
 	$.fn.change=function(options){  
 		var defaults={	
 			this_:$(this)
 		}
 		var options=$.extend(defaults,options);	
 		this.each(function(){

 			$(this).click(function(){
 				$.ajax({
 					type:'POST',
 					url:options.url,
 					data:{'goods_id':$(this).attr('goods_id'),'now':options.this_.attr('now'),'type':'used'},
 					success:function(rs){
 						if(rs=='1'){
 							if(options.this_.attr('now')==1){
 								options.this_.attr('now','0');
 								options.this_.html(options.css_0);
 								remind('成功取消收藏。');

 							}else{
 								options.this_.attr('now','1');
 								options.this_.html(options.css_1);
 								remind('成功收藏。'); 								         				
 							}
 						}else if(rs=='01'){
 							alert('抱歉，操作失败。原因是未登录。请您登陆后操作。');
 						}
 						else{
							remind('抱歉, 操作失败。');							
 						}
 					}
 				});
 			});

 		});
 	}
 })(jQuery);


