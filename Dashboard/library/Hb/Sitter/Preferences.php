<?php
class Hb_Sitter_Preferences{
	
	public function __construct(){
		
	}
	public function getPreferenceTables(){
	$this->preferGroupTable = new Zend_Db_Table('preference_group');
	$this->preferMasterTable = new Zend_Db_Table('preference_master');
	$this->preferSitterTable = new Zend_Db_Table('sitter_preference');
	
	}
	
	
	public function addNewGroup($data){
		$insert = array('group_name'=>$data['group_name']);
		
		return $this->group_id = $this->preferGroupTable->insert($insert);
		
	}
	public function deleteGroup($group_id){
		
	}
	public function updateGroup($data,$group_id){
		
	}
	
	public function addNewPreferences(array $data){
		
		$insert = array('group_id'=>$data['group_name'],
						'prefer_name'=>$data['prefer_name'],
						'prefer_desc'=>$data['prefer_desc'],
						);
		
		 return $this->prefer_id = $this->preferMasterTable->insert($insert);
		
	}
	public function updatePreference(array $data,$prefer_id){
		
	}
	public function deletePreference(array $data,$prefer_id){
		
	}
	
}

