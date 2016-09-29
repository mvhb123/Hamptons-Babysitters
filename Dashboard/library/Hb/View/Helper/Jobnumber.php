<?php 
class Hb_View_Helper_Jobnumber extends Zend_View_Helper_Abstract
{
  public function jobnumber($jobId)
  {
	  return str_pad($jobId,4,0,STR_PAD_LEFT);
   
  }
}
