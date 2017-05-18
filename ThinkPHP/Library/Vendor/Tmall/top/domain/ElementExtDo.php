<?php

/**
 * DO 的根据type选择相关的字段填写
 * @author auto create
 */
class ElementExtDo
{
	
	/** 
	 * text元素必填字段,文字描述，必填，最多1000字
	 **/
	public $desc;
	
	/** 
	 * pic 元素必填字段，小于200KB
	 **/
	public $img;
	
	/** 
	 * item元素必填字段，商品id
	 **/
	public $item_id;
	
	/** 
	 * item元素选填字段，商品图片，方图，小于300KB
	 **/
	public $item_image;
	
	/** 
	 * item元素必填字段，商品标题
	 **/
	public $item_title;
	
	/** 
	 * item元素选填字段，商品url
	 **/
	public $item_url;
	
	/** 
	 * pic 元素选填字段
	 **/
	public $link;
	
	/** 
	 * item元素选填字段，商品推荐理由
	 **/
	public $recommend_reason;
	
	/** 
	 * item 元素展示类型字段，默认为2 左图右文样式
	 **/
	public $show_style;
	
	/** 
	 * title元素必填字段，标题，20字以内。标题模块只能填一次，且必须放在第一位，不允许放在其他位置
	 **/
	public $title;
	
	/** 
	 * title元素选填字段，标题背景图片，小于300KB, 建议是750*280的图片，否则效果不好
	 **/
	public $title_image_url;
	
	/** 
	 * 元素类型：title,item, pic,text  这4种类型
	 **/
	public $type;	
}
?>