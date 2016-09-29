<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Validation rules used while Captain Registration

function parentEditProfile_rules()
{
	$CI =& get_instance();
	$id = 0;
	if($CI->input->post('userid'))
		$id= $CI->input->post('userid');
	return array(
			array(
					'field'	=>	'firstname',
					'label'	=>	'First name',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'lastname',
					'label'	=>	'Last name',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'dob',
					'label'	=>	'Date of birth',
					'rules'	=>	'trim'
			),
			array(
					'field' =>	'phone',
					'label' =>	'Phone',
					'rules' =>	'trim|required|valid_phone'
			),
			array(
					'field'	=>	'local_phone',
					'label'	=>	'Local phone',
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'work_phone',
					'label'	=>	'Work Phone',
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'username',
					'label'	=>	'Email',
					'rules'	=>	'trim|required|valid_email|is_unique_new[users.username,userid."'.$id.'"]'
			),
			array(
					'field'	=>	'spouse_firstname',
					'label'	=>	"Other guardian's first name",
					'rules'	=>	'trim|alpha_with_space'
			),
			array(
					'field'	=>	'spouse_lastname',
					'label'	=>	"Other guardian's last name",
					'rules'	=>	'trim|alpha_with_space'
			),
			array(
					'field'	=>	'spouse_phone1',
					'label'	=>	"Other guardian's phone1",
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'spouse_phone2',
					'label'	=>	"Other guardian's phone2",
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'spouse_relation',
					'label'	=>	"Other guardian's relationship",
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'emergency_contact',
					'label'	=>	'Emergency contact name',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'emergency_relation',
					'label'	=>	'Emergency contact relationship',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'emergency_phone',
					'label'	=>	'Emergency contact number',
					'rules'	=>	'trim|valid_phone|required'
			),
			array(
					'field'	=>	'local_address',
					'label'	=>	'Local address',
					'rules'	=>	'trim|required'
			)
			
	);
}

function registration_rules()
{
	return array(
			array(
					'field'	=>	'firstname',
					'label'	=>	'First name',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'lastname',
					'label'	=>	'Last name',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'password',
					'label'	=>	'Password',
					'rules'	=>	'trim|min_length[6]|required'
			),
			array(
					'field'	=>	'cPassword',
					'label'	=>	'Confirm password',
					'rules'	=>	'trim|matches[password]|required'
			),
			array(
					'field'	=>	'dob',
					'label'	=>	'Date of birth',
					'rules'	=>	'trim'
			),
			array(
					'field' =>	'phone',
					'label' =>	'Phone',
					'rules' =>	'trim|required|valid_phone'
			),
			array(
					'field'	=>	'local_phone',
					'label'	=>	'Local phone',
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'work_phone',
					'label'	=>	'Work Phone',
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'username',
					'label'	=>	'Email',
					'rules'	=>	'trim|valid_email|required|is_unique[users.username]'
			),
			array(
					'field'	=>	'spouse_firstname',
					'label'	=>	"Other guardian's first name",
					'rules'	=>	'trim|alpha_with_space'
			),
			array(
					'field'	=>	'spouse_lastname',
					'label'	=>	"Other guardian's last name",
					'rules'	=>	'trim|alpha_with_space'
			),
			array(
					'field'	=>	'spouse_relation',
					'label'	=>	"Other guardian's relationship",
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'spouse_phone1',
					'label'	=>	"Other guardian's phone1",
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'spouse_phone2',
					'label'	=>	"Other guardian's phone2",
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'emergency_contact',
					'label'	=>	'Emergency contact name',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'emergency_relation',
					'label'	=>	'Emergency contact relationship',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'emergency_phone',
					'label'	=>	'Emergency contact number',
					'rules'	=>	'trim|valid_phone|required'
			),
			array(
					'field'	=>	'local_address',
					'label'	=>	'Local address',
					'rules'	=>	'trim|required'
			)
				
	);
}

//Validation rules used while sitter Edit profile
function sitterEditProfile_rules()
{
	$CI =& get_instance();
	$id = 0;
	if($CI->input->post('userid'))
		$id= $CI->input->post('userid');
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'firstname',
					'label'	=>	'First name',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'lastname',
					'label'	=>	'Last name',
					'rules'	=>	'trim|alpha_with_space|required'
			),
			array(
					'field'	=>	'username',
					'label'	=>	'Email',
					'rules'	=>	'trim|valid_email|required|is_unique_new[users.username,userid."'.$id.'"]'
			),
			array(
					'field'	=>	'dob',
					'label'	=>	'Date of birth',
					'rules'	=>	'trim'
			),
			array(
					'field' =>	'phone',
					'label' =>	'Phone',
					'rules' =>	'trim|required|valid_phone'
			),
			array(
					'field'	=>	'local_phone',
					'label'	=>	'Local phone',
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field'	=>	'work_phone',
					'label'	=>	'Work Phone',
					'rules'	=>	'trim|valid_phone'
			),
			array(
					'field' =>	'cpr_holder',
					'label' =>	'CPR certification infant/Toddler',
					'rules' =>	'trim|required'
			),
			array(
					'field' =>	'cpr_adult',
					'label' =>	'CPR certification adult',
					'rules' =>	'trim|required'
			),
			array(
					'field' =>	'first_aid_cert',
					'label' =>	'First-Aid certification',
					'rules' =>	'trim|required'
			),
			array(
					'field' =>	'water_certification',
					'label' =>	'Water certification',
					'rules' =>	'trim|required'
			),
			array(
					'field' =>	'hampton_babysitter_training',
					'label' =>	'Hampton babysitter training',
					'rules' =>	'trim|required'
			),
			/* array(
					'field' =>	'have_child',
					'label' =>	'Have child',
					'rules' =>	'trim|required'
			),
			array(
					'field' =>	'have_car',
					'label' =>	'Have car',
					'rules' =>	'trim|required'
			), 
			array(
					'field' =>	'clean_drive_record',
					'label' =>	'Clean drive record',
					'rules' =>	'trim|required'
			)*/
	);
}


// Validation rule used when user logs in
function rule_login()
{
	return array(
				array(
					 'field'   => 'username',
					 'label'   => 'Email',
					 'rules'   => 'trim|valid_email|required'
				  ),
				array(
					 'field'   => 'password',
					 'label'   => 'Password',
					 'rules'   => 'trim|xss_clean|required'
				  )
			);
}

function rule_logout()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			)
	);
}


function sendMessage_rules()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'to_user',
					'label'	=>	'To',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'message',
					'label'	=>	'Message',
					'rules'	=>	'trim|required'
			)
		);
}


function view_sitter_rules()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'sitter_id',
					'label'	=>	'Sitter id',
					'rules'	=>	'trim|required'
			)
		);
}


function view_parent_rules()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'parent_id',
					'label'	=>	'Parent id',
					'rules'	=>	'trim|required'
			)
	);
}

function change_password_rules()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'current_password',
					'label'	=>	'Current password',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'password',
					'label'	=>	'Password',
					'rules'	=>	'trim|required'
			)
	);
}


function addKid_rules()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'child_name',
					'label'	=>	'Child Name',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'dob',
					'label'	=>	'Date of birth',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'sex',
					'label'	=>	'Gender',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'allergy_status',
					'label'	=>	'Allergies field',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'special_needs_status',
					'label'	=>	'Special needs',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'special_needs',
					'label'	=>	'Special needs',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'allergies',
					'label'	=>	'Allergies',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'medicator_status',
					'label'	=>	'Medications',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'medicator',
					'label'	=>	'Medications list',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'notes',
					'label'	=>	'Helpful hints',
					'rules'	=>	'trim'
			)
	);
}

function addJob_rules()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'child_id',
					'label'	=>	'Child id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'address_id',
					'label'	=>	'Address',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'alternate_address',
					'label'	=>	'Address',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'job_start_date',
					'label'	=>	'Start date/time',
					'rules'	=>	'trim|required|compareCurrentDate'
			),
			array(
					'field'	=>	'job_end_date',
					'label'	=>	'End date',
					'rules'	=>	'trim|required|compareDate|min_3_hr_difference'
			),
			array(
					'field'	=>	'preferences',
					'label'	=>	'Preference',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'notes',
					'label'	=>	'Helpful hints',
					'rules'	=>	'trim'
			),
			array(
					'field'	=>	'is_special',
					'label'	=>	'Special need sitter required',
					'rules'	=>	'trim|required'
			)
			
	);
}

function addcard_rules()
{
	return array(
			array(
					'field'	=>	'name_on_card',
					'label'	=>	'Name on card',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'card_info',
					'label'	=>	'Card detail',
					'rules'	=>	'trim|required'
			)
	);
}

function buyPackage_rules()
{
	return array(
			array(
					'field'	=>	'userid',
					'label'	=>	'User id',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'token',
					'label'	=>	'Token',
					'rules'	=>	'trim|required'
			),
			array(
					'field'	=>	'package_id',
					'label'	=>	'Package id',
					'rules'	=>	'trim|required'
			)
	);
}