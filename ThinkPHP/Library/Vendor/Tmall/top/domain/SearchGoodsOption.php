<?php

/**
 * Alibaba ICBU 在线批发产品搜索参数
 * @author auto create
 */
class SearchGoodsOption
{
	
	/** 
	 * category_id 搜索类目
	 **/
	public $category_id;
	
	/** 
	 * keyword 搜索关键词
	 **/
	public $keyword;
	
	/** 
	 * min_order_from 最小起订量区间
	 **/
	public $min_order_from;
	
	/** 
	 * min_order_to 最小起订量区间
	 **/
	public $min_order_to;
	
	/** 
	 * page_no 当前页面
	 **/
	public $page_no;
	
	/** 
	 * page_size 每页大小
	 **/
	public $page_size;
	
	/** 
	 * price_from_cent 最小价格
	 **/
	public $price_from_cent;
	
	/** 
	 * price_to_cent 最大价格
	 **/
	public $price_to_cent;
	
	/** 
	 * 产品筛选tags
	 **/
	public $product_refine_tags;
	
	/** 
	 * sort_by 排序方式
	 **/
	public $sort_by;	
}
?>