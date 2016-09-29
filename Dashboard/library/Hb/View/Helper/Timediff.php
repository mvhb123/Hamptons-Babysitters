<?php 
class Hb_View_Helper_Timediff extends Zend_View_Helper_Abstract
{
  public function age($dob)
  {
	 
	return  $this->calculateFriendlyAge($dob);
	 
	
  }
  
  function timediff($time, $format = '%dDays %hHs %m mins ') {        
        
       
       $difference = strtotime($time)-time(); 
        if($difference < 0) 
            return false; 
        else{ 
        
            $min_only = intval(floor($difference / 60)); 
            $hour_only = intval(floor($difference / 3600)); 
            
            $days = intval(floor($difference / 86400)); 
            $difference = $difference % 86400; 
            $hours = intval(floor($difference / 3600)); 
            $difference = $difference % 3600; 
            $minutes = intval(floor($difference / 60)); 
            if($minutes == 60){ 
                $hours = $hours+1; 
                $minutes = 0; 
            } 
            
            if($days == 0){ 
                $format = str_replace('Days', '?', $format); 
                $format = str_replace('Ds', '?', $format); 
                $format = str_replace('%d', '', $format); 
            } 
            if($hours == 0){ 
                $format = str_replace('Hours', '?', $format); 
                $format = str_replace('Hs', '?', $format); 
                $format = str_replace('%h', '', $format); 
            } 
            if($minutes == 0){ 
            	/*$format = str_replace('Minutes', '?', $format);
            	$format = str_replace('Mins', '?', $format);
            	$format = str_replace('Ms', '?', $format);*/
                $format = str_replace('minutes', '?', $format); 
                $format = str_replace('mins', '?', $format); 
                $format = str_replace('ms', '?', $format);        
                $format = str_replace('%m', '', $format); 
            } 
            
            $format = str_replace('?,', '', $format); 
            $format = str_replace('?:', '', $format); 
            $format = str_replace('?', '', $format); 
            
            $timeLeft = str_replace('%d', number_format($days), $format);        
            $timeLeft = str_replace('%ho', number_format($hour_only), $timeLeft); 
            $timeLeft = str_replace('%mo', number_format($min_only), $timeLeft); 
            $timeLeft = str_replace('%h', number_format($hours), $timeLeft); 
            $timeLeft = str_replace('%m', number_format($minutes), $timeLeft); 
                
            if($days == 1){ 
                $timeLeft = str_replace('Days', 'Day', $timeLeft); 
                $timeLeft = str_replace('Ds', 'D', $timeLeft); 
            } 
            if($hours == 1 || $hour_only == 1){ 
                $timeLeft = str_replace('Hours', 'Hour', $timeLeft); 
                $timeLeft = str_replace('Hs', 'H', $timeLeft); 
            } 
            if($minutes == 1 || $min_only == 1){ 
            	/*$timeLeft = str_replace('Minutes', 'Minute', $timeLeft);
            	$timeLeft = str_replace('Mins', 'Min', $timeLeft);
            	$timeLeft = str_replace('Ms', 'M', $timeLeft);*/
                $timeLeft = str_replace('minutes', 'minute', $timeLeft); 
                $timeLeft = str_replace('mins', 'min', $timeLeft); 
                $timeLeft = str_replace('ms', 'm', $timeLeft);            
            } 
            $timeLeft = str_replace('mins', 'min', $timeLeft);
                
          return $timeLeft; 
        } 
    } 
  
}
