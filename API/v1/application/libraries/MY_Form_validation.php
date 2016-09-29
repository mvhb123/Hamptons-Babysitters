<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_Form_validation Class
 *
 * Extends Form_Validation library
 *
 * Adds one validation rule, "unique" and accepts a
 * parameter, the name of the table and column that
 * you are checking, specified in the forum table.column
 * and id of user
 */
class MY_Form_validation  extends CI_Form_validation {

	// very important to fetch error as array inherit the property of parent class
	var $_error_array = array();
	function __construct()
	{
	    parent::__construct();
		
		// Setting error delimiters
		$this->set_error_delimiters('<span class="red">','</span>');
	}
	
	/**
	 * Unique
	 *
	 * @access    public
	 * @param    string
	 * @param    field
	 * @return    bool
	 */
	
	public function is_unique_new($str, $field)
	{
		$exploded_field = explode(",",$field);
		list($table, $field)=explode('.', $exploded_field[0]);
		$exp = explode('.', $exploded_field[1]);
		$this->CI->db->select('*');
		$this->CI->db->from($table);
		$this->CI->db->where($field,$str);
		$skip_field = $exp[0];
		$skip_value = $exp[1];
		if($skip_field!=""&&$skip_value!="")
			$this->CI->db->where_not_in($skip_field,str_replace('"','',$skip_value));
		$query = $this->CI->db->get();
		return $query->num_rows() === 0;
	}
	
	
	
	/**
	 * valid_registration
	 *
	 * @access    public
	 * @param    string
	 * @param    field
	 * @return    bool
	 */
	function valid_registration($str, $field)
	{
		$CI =& get_instance();
		$filtered_str = $CI->db->escape($str);
		
		// Check if user with email id already exist
		$query = $CI->db->query("SELECT `user_id` FROM `user` WHERE `email_address` = $filtered_str LIMIT 0,1");
		if($query->num_rows()!=0)
		{
				$CI->form_validation->set_message('valid_registration', 'This %s is already registered.');
				return FALSE;
		}
	}
	// --------------------------------------------------------------------

	
	
	/**
	 * Numeric with dash, + and ()
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function valid_phone($str)
	{
		$CI =& get_instance();
		$CI->form_validation->set_message('valid_phone', 'Please enter a valid %s.');
		return ( ! preg_match("/^([+()0-9-])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	// To validate date of birth
	function valid_date_of_birth($str)
	{
		
		$CI =& get_instance();
		
		if($CI->input->post('date_of_birth_m') && $CI->input->post('date_of_birth_d') && $CI->input->post('date_of_birth_y'))
		{
			$CI->form_validation->set_message('valid_date_of_birth', 'Please enter a valid %s.');
			
			if(!checkdate($CI->input->post('date_of_birth_m'), $CI->input->post('date_of_birth_d'), $CI->input->post('date_of_birth_y')))
			{
				return FALSE;
			}
			
			$supplied_date = $CI->input->post('date_of_birth_y').'-'.$CI->input->post('date_of_birth_m').'-'.$CI->input->post('date_of_birth_d').' '.date('H:i:s');
			$supplied_timestamp = strtotime($supplied_date);
			$current_timestamp = strtotime('now');
			
			if($supplied_timestamp > $current_timestamp)
			{
				return FALSE;
			}
			
			return TRUE;
		}
		
		$CI->form_validation->set_message('valid_date_of_birth', 'The %s field is mandatory.');
		return FALSE;
	}
	    function select_check($str)
    {
    	if ($str == 'Yes' || $str =='No')
		{
		
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('select_check', 'The %s field can be yes or no ');
			return FALSE;
		}
    }
    
	function old_password($str, $field)
	{
		$CI =& get_instance();
		list($table, $column1, $column2, $id) = explode(".", $field, 4);
		
		$filtered_str = $CI->db->escape($str);
		$CI->form_validation->set_message('old_password', 'Incorrect %s.');
		if(!($id))
		{
			$query = $CI->db->query("SELECT * FROM $table WHERE $column1 = $filtered_str");
		}
		else
		{
			$query = $CI->db->query("SELECT * FROM $table WHERE BINARY $column1 = $filtered_str AND $column2 = '$id'");
		}
		$row = $query->row();
		return (empty($row)) ? FALSE : TRUE;
	}
	
 	function address_check($str)
	{
		$CI =& get_instance();
		$address = json_decode($str,true);
		
		if($address===null)
		{
			$CI->form_validation->set_message('address_check', "Address is required");
			return false;
		}
		
		$r= true;
	
		/* if(!array_key_exists("zipcode",$address)||!isset($address['zipcode'])||$address['zipcode']=== null)
		{
			$CI->form_validation->set_message('address_check', "Zip is required");
			$r= FALSE;
		} */
	
		if(!array_key_exists("state",$address)||!isset($address['state'])||$address['state']=== null)
		{
			$CI->form_validation->set_message('address_check', "State is required");
			$r= FALSE;
		}
	
		if(!array_key_exists("city",$address)||!isset($address['city'])||$address['city']=== null||trim($address['city'])=='')
		{
			$CI->form_validation->set_message('address_check', "City is required");
			$r= FALSE;
		}
	
		if(!array_key_exists("streat_address",$address)||!isset($address['streat_address']) || $address['streat_address']===null ||trim($address['streat_address'])=='')
		{
			$CI->form_validation->set_message('address_check', 'Street address is required');
			$r= FALSE;
		}
	
		return $r;
	}
	
	
	function enum($str, $val='')
    {
   	
    	$CI =& get_instance();
    	

        if (empty($val))
        {
        return FALSE;
        }

        $arr = explode(',', $val);
        $array = array();
        foreach($arr as $value)
        {
        $array[] = trim($value);
        }
		$CI->form_validation->set_message('enum', 'The %s should be '.$val);
        return (in_array(trim($str), $array)) ? TRUE : FALSE;
    }
    
    function valid_date_of_birth_format($datein)
	{
		$CI =& get_instance();
		//$datein = 'YYYY-MM-DDDD';
		
		$supplied_timestamp = strtotime($datein);
		$current_timestamp = strtotime('now');
		
		if($supplied_timestamp > $current_timestamp)
		{
			$CI->form_validation->set_message('valid_date_of_birth_format', 'The %s field is invalid.');
			return FALSE;
		}
			
		if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $datein)){
		    return true;
		}else{
			$CI->form_validation->set_message('valid_date_of_birth_format', 'The %s field format should be YYYY-MM-DD.');
			return false;
		}		
		
	} 	
	
	
	/**
	 * Date not changed only time changed
	 *
	 * @access    public
	 * @param    string
	 * @param    field
	 * @return    bool
	 */
	
	public function check_date($str, $field)
	{
		$CI =& get_instance();
		$str= date('Y-m-d h:i:s',strtotime($str));
		$exploded_field = explode(",",$field);
		list($table, $field)=explode('.', $exploded_field[0]);
		$exp = explode('.', $exploded_field[1]);
		$query =$CI->db->query( 'select DATEDIFF('.$field.',"'.$str.'" ) as date from '.$table.' where '. $exp[0] .'='.$exp[1].'');
	
		if($query->num_rows()>0)
		{
			$res= $query->row();
			if(abs($res->date)>0)
				return false;
			else
				return true;
		}
		else
			return true;
	}	
	
	function compareDate($str) {
		$CI =& get_instance();
		$startDate = strtotime($CI->input->post('job_start_date'));
		$endDate = strtotime($str);
	
		if ($endDate >= $startDate)
			return true;
		else {
			$CI->form_validation->set_message('compareDate', '%s should be greater than Job Start Date.');
			return false;
		}
	}
	
	function min_3_hr_difference($str) 
	{
		$CI =& get_instance();
		$startDate = strtotime($CI->input->post('job_start_date'));
		$endDate = strtotime($str);
		
		$time1 = date('H:i:s', $startDate);
		$time2 = date('H:i:s', $endDate);
		
		$time1 = strtotime("1980-01-01 $time1");
		$time2 = strtotime("1980-01-01 $time2");
		
		$hourdiff = date("H", strtotime("1980-01-01 00:00:00") + ($time2 - $time1));
		
		if (($hourdiff < 3) && ($hourdiff >= 0))  
		{
			$CI->form_validation->set_message('min_3_hr_difference', 'Job should be of minimum 3 hours');
			return false;
		}
		else 
		{
			return true;
		}
	}
	
	function compareCurrentDate($str) {
		$CI =& get_instance();
		$startDate = strtotime(date("Y-m-d H:i:s"));
		//$startDate = strtotime($CI->input->post('current_time'));
		$endDate = strtotime($str);
	
		if ($endDate >= $startDate)
			return true;
		else {
			$CI->form_validation->set_message('compareCurrentDate', '%s should be greater than current date/time.');
			return false;
		}
	}
	// --------------------------------------------------------------------
}
?>
