<?php
class Hb_Pricing{

	public $packageTable;
	public $db;

	public function __construct(){
		
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$this->ratesTable = new Zend_Db_Table('rates');
		
	}
	
	/* funcion to get list of all packages*/
	public function get_all_pricingrate($search=array(),$filter=array(),$sort=array()){
		
	
		$sort['rows'] = (int)$search['rows'] ==0 ? 10 :(int)$search['rows'] ;
		$sort['page'] = (int)$search['page'] ==0 ? 1:(int)$search['page'];
		$filter['sort'] = (int)$search['sort'] ==0 ? 1:$search['sort'];
	
	
		if($sort['page']>1)
		{
			$start = ($sort['page']-1)*$sort['rows'];
		}
		else if($sort['page']==1 or $sort['page']==0)
			{
				$start= 0;
			}
		
			$orderby=array('key'=>(in_array($search['key'],array('client_rate','sitter_rate','child_count')) ? $search['key']:'child_count' ),'odr'=>(in_array($search['odr'],array('asc','desc')) ? $search['odr'] : 'asc' ));
			$sql=" select rate_id,client_rate,sitter_rate,child_count from rates order by {$orderby['key']} {$orderby['odr']}  LIMIT  $start,{$sort['rows']} ";
			
			$res = $this->db->query($sql);
			$result_query = $res->fetchAll();
		
		$sql="select rate_id,client_rate,sitter_rate,child_count from rates";
		$res = $this->db->query($sql);
			
		$result = $res->fetchAll();
		$count=count($result);
		$results=array('total'=>$count,'rows'=>$result_query);
		return $results;
	}
	
	/*=========================funtion for add/edit packages by namrata=========================================*/
	/*------function to get package details---------------*/
	
	public function get_pricing_details($rate_id)
	{
		$sql=" select rate_id,client_rate,sitter_rate,child_count from rates where rate_id=$rate_id";
		$res = $this->db->query($sql);
		$result_query = $res->fetchAll();
		$results=array('rows'=>$result_query);
		return $results;
	}
	
	/*------function to update package details---------------*/
	
	public function update_pricing_details($update_data,$rate_id)
	{
		$sql=" update rates SET child_count='".$update_data['child_count']."',client_rate='".$update_data['client_rate']."',sitter_rate='".$update_data['sitter_rate']."' where rate_id=$rate_id ";
		$this->db->query($sql);
		
	}
	
	/*-----------function to add package details-------------------------*/
	
	public function add_pricing_details($insert_data)
	{
		
		$sql="INSERT INTO rates ( child_count,client_rate,sitter_rate )  VALUES ( '".$insert_data['child_count']."', '".$insert_data['client_rate']."','". $insert_data['sitter_rate']."')";
		$this->db->query($sql);
	}
	

	public function delete($rate_id)
	{
		try{

			$sql=" delete from rates where rate_id=$rate_id ";
			$this->db->query($sql);
		}
		catch(Exception $e){

		}
	}
}
