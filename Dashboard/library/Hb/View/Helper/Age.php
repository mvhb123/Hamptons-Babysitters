<?php 
class Hb_View_Helper_Age extends Zend_View_Helper_Abstract
{
  public function age($dob)
  {
	 
	return  $this->calculateFriendlyAge($dob);
	 
	
  }
  
  public function calculateFriendlyAge($dob) {
	// array that hold the year, month and days text
	$friendlyAgeText = array();

	// internal class for get the date
	//$oMD = new MagicDate($unformattedDate);
	
	$dobArray = explode('-',$dob);
	$year = $dobArray[0];
	$month = $dobArray[1];
	$day = $dobArray[2];
	
	$yearDiff = date("Y") - $year;
	$monthDiff = date("m") - $month;
	$dayDiff = date("d") - $day;

	  if ($monthDiff <= 0) { // trick: now is January, and the birthdate on December
		  $yearDiff--;
		  $monthDiff += 12;
	  }
	  if ($dayDiff < 0) { // trick: now is 20, and the birthdate is past Month, on 25.
		  $monthDiff--;
		  $dayDiff += 30; // dirty: no support for 28-29 days on february, 31 days on some months
	  }
	  if ($monthDiff == 12) {
		  $monthDiff = 0;
		  $yearDiff = 1;
	  }

	// the age is already numeric, now convert to friendly text
	switch ($yearDiff):
		case 0:
			break;
		case 1:
			$friendlyAgeText[] = "1 yr";
			break;
		default:
			$friendlyAgeText[] = $yearDiff . " yrs";
			break;
	endswitch;
	if ($yearDiff <= 4) { // support "with months" for child born 4 years ago
		switch ($monthDiff):
			case 0: break;
			case 1:
				$friendlyAgeText[] = "1 mo";
				break;
			default:
				$friendlyAgeText[] = $monthDiff . " mo";
				break;
		endswitch;
		if ($yearDiff == 0 && $monthDiff <= 3 && $dayDiff > 0) { // support "with days" for baby born 3 months ago
			$friendlyAgeText[] = $dayDiff . " day(s)";
		}
	}

	return join(" and ", $friendlyAgeText);
}
  
}
