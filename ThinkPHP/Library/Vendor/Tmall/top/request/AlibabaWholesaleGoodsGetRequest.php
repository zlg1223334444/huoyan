<?php
/**
 * TOP API: alibaba.wholesale.goods.get request
 * 
 * @author auto create
 * @since 1.0, 2015.04.07
 */
class AlibabaWholesaleGoodsGetRequest
{
	/** 
	 * country_code
	 **/
	private $countryCode;
	
	/** 
	 * id
	 **/
	private $id;
	
	private $apiParas = array();
	
	public function setCountryCode($countryCode)
	{
		$this->countryCode = $countryCode;
		$this->apiParas["country_code"] = $countryCode;
	}

	public function getCountryCode()
	{
		return $this->countryCode;
	}

	public function setId($id)
	{
		$this->id = $id;
		$this->apiParas["id"] = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getApiMethodName()
	{
		return "alibaba.wholesale.goods.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
