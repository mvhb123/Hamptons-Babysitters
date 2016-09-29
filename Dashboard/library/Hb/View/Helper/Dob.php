<?php 
class Hb_View_Helper_Dob extends Zend_View_Helper_Abstract
{
  public function dob($dob)
  {
	 $d = explode('-',$dob);
		if(count($d)==3&&(int)$d[1]>=1)
	 return $d[1].'/'.$d[2].'/'.$d[0];
	 else return '';
	
  }
}
