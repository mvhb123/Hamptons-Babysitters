<?php
class Hb_Packages{

	public $packageTable;
	public $db;

	public function __construct(){
		
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$this->packageTable = new Zend_Db_Table('packages');
		
	}
	
	public function search($search=array()){
		
		if($search['amount']!=''){
			$query = ' and price="'.$search['amount'].'"';
		}
		
		$sql="Select * from packages where status = 0 $query";
		$res =$this->db->query($sql);
		$result=$res->fetchAll();
		
		foreach($result as $d){
			$data[$d['package_id']]=$d;
		}
		return $data;
	}
	public function searchByAmount($search=array()){
		
		if($search['amount']!=''){
			$query = ' and price="'.$search['amount'].'"';
		}
		
		 $sql="Select * from packages where 1 $query";
		
		$res =$this->db->query($sql);
		 $result=$res->fetchAll();
		 //print_r($result);
		return $result[0];
		
		
	}
	public function contactMail($data){
		
		$this->hbSettings = new Hb_Settings();
		$mailTemplate = $this->hbSettings->getMailTemplates(array('mail_name'=>'contact_mail'));
		$mailTemplate=$mailTemplate[0];
		
		$to = $mailTemplate['to'];
		$from=$data['email'];
		$subject = $mailTemplate['subject'];
			
		
		$cc = [];   #array($data['email']);//explode(',',$mailTemplate['cc']);
		$bcc = explode(',',$mailTemplate['bcc']);

		
		$text = str_replace('{viewlink}','',$mailTemplate['description']);
		
		$replace = array(
						'{name}'=>ucwords($data['name']),
						'{email}'=>$data['email'],
						'{numberofdays}'=>$data['numberofdays'],
						'{budget}'=>$data['budget'],
						'{comments}'=>$data['comments']
						);
				$text = str_ireplace(array_keys($replace),$replace,$text);

		$mail = new Zend_Mail();
		$mail->setBodyText($text);   
		$mail->setBodyHtml($text);  
		$mail->setFrom($from,$data['name']);  
		if(!empty($cc))foreach($cc as $c){$mail->addCc($c);};
	    if(!empty($bcc))foreach($bcc as $c){$mail->addBcc($c);}; 
		//echo $to;
		//echo $to_name;die();
		$mail->addTo($to);   
		$mail->setSubject($subject);
		$mail->send();
	}
	
	
	/* funcion to get list of all packages*/
	public function get_all_package($search=array(),$filter=array(),$sort=array()){
		
	
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
		
			$orderby=array('key'=>(in_array($search['key'],array('package_name','credits','price')) ? $search['key']:'package_name' ),'odr'=>(in_array($search['odr'],array('asc','desc')) ? $search['odr'] : 'asc' ));
			$sql=" select package_id,credits,price,package_name,ordering from packages where status=0 order by {$orderby['key']} {$orderby['odr']}  LIMIT  $start,{$sort['rows']} ";
			
			$res = $this->db->query($sql);
			$result_query = $res->fetchAll();
		
		$sql="select package_id,credits,price,package_name,ordering from packages where status=0";
		$res = $this->db->query($sql);
			
		$result = $res->fetchAll();
		$count=count($result);
		$results=array('total'=>$count,'rows'=>$result_query);
		return $results;
	}
	
	/*=========================funtion for add/edit packages by namrata=========================================*/
	/*------function to get package details---------------*/
	
	public function get_package_details($package_id)
	{
		$sql=" select package_id,credits,price,package_name,ordering from packages where package_id=$package_id";
		$res = $this->db->query($sql);
		$result_query = $res->fetchAll();
		$results=array('rows'=>$result_query);
		return $results;
	}
	
	/*------function to update package details---------------*/
	
	public function update_package_details($update_data,$package_id)
	{
		$sql=" update packages SET package_name='".$update_data['package_name']."',credits='".$update_data['credit']."',price='".$update_data['price']."' where package_id=$package_id ";
		$this->db->query($sql);
		
	}
	
	/*-----------function to add package details-------------------------*/
	
	public function add_package_details($insert_data)
	{
		
		$sql="INSERT INTO packages ( credits, price,package_name,ordering )  VALUES ( '".$insert_data['credit']."', '".$insert_data['price']."','". $insert_data['package_name']."','".$insert_data['package_order']."')";
		$this->db->query($sql);
	}
	
	public function get_max_package()
	{

		$sql=" select max(ordering) as p_id from packages";
		$res = $this->db->query($sql);
		$result_query = $res->fetchAll();
		$p_id=$result_query[0]['p_id'];
		$package_id=intval($p_id);
		return($package_id+1);	
	}

	public function delete($package_id)
	{


		try{

			$sql=" update packages SET status=1 where package_id=$package_id ";
			$this->db->query($sql);
		}
		catch(Exception $e){

		}
	}
}
