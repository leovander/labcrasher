<?php
  //ini_set( 'display_errors', 0 );
  date_default_timezone_set('America/Los_Angeles');
  require_once("simple_html_dom.php");
  require_once("helper_functions.php");
  
  
  $majors = array("CHzE", "CzE", "CECS", "CEM", "EzE", "ENGR", "EzT", "MAE");
  foreach ($majors as $major) {
	  $schedule = getClassSchedule($major);
	  sortTimes($schedule);
	  calculateVacancy($schedule);
	  //$available = findAvailableNow ($schedule);
	  file_put_contents("COE/".$major."_available.json", json_encode($schedule));
  }
  
  function calculateVacancy(&$schedule) {  
	  //Find vacant time from the occupied time
	  foreach ($schedule as $dkey => $day) {
	    foreach ($day as $ckey => $class) {
	      foreach ($class["occupied"] as $okey => $occupied) {
	      	if ($okey==0 && $occupied["start"] > date('H:i', strtotime("08:00"))) {
		        array_push($schedule[$dkey][$ckey]["vacant"],
		        					array("start" => date('H:i', strtotime("08:00")),
	    	    						  "end" => $occupied["start"]));
		    } elseif ($okey+1 <= sizeof($schedule[$dkey][$ckey]["occupied"])-1) {
		        array_push($schedule[$dkey][$ckey]["vacant"],
		        					array("start" => $occupied["end"],
	    	    						  "end" => $schedule[$dkey][$ckey]["occupied"][$okey+1]["start"]));
	        } else {
	        	array_push($schedule[$dkey][$ckey]["vacant"],
		        					array("start" => $occupied["end"],
	    	    						  "end" => date('H:i', strtotime("23:00"))));
	        }
	      }
	    }
	  }	
  }

  function sortTimes(&$schedule = array()) {
	  foreach($schedule as $dkey => $day) {
	    foreach($day as $ckey => $class) {
	      $occupied = $schedule[$dkey][$ckey];
	      $schedule[$dkey][$ckey] = null;
	      $schedule[$dkey][$ckey]["occupied"] = $occupied;
	      $schedule[$dkey][$ckey]["vacant"] = array();
	    }
	  }
	  
	  foreach ($schedule as $dkey => $day) {
	    foreach ($day as $ckey => $class) {
	      usort($schedule[$dkey][$ckey]["occupied"], "cmp");
	    }
	  }
  }

  function getClassSchedule($major) {
	  $html = file_get_html('http://www.csulb.edu/depts/enrollment/registration/class_schedule/Fall_2014/By_College/'.$major.'.html');
	  $ret = $html->find('.sectionTable');
	  
	  $days_of_week = new stdClass();
	  $days_of_week->Monday = new stdClass();
	  $days_of_week->Tuesday = new stdClass();
	  $days_of_week->Wednesday = new stdClass();
	  $days_of_week->Thursday = new stdClass();
	  $days_of_week->Friday = new stdClass();
	
	  $today = getdate();
	  
	  foreach($ret AS $table) {
	    $rows = $table->find('tr');
	    $count = 0;
	
	    foreach($rows AS $row) {
	      if($count != 0) {
	        $room = $row->children(7)->innertext;
	        $time = $row->children(5)->innertext;
	        $days = $row->children(4)->innertext;
	
	        $time = explode("-", $time);
	
	        if (isset($time[1]) && isset($time[0])) {
	          //Logic to append 'AM' or 'PM' to the start and end time
	          if (substr($time[1], -2) == 'AM') {
	            $time[0] = $time[0].'AM';
	          } else {
	            if (preg_match('/12?/', $time[1]) && preg_match('/1(0|1)/', $time[0])) {
	              $time[0] = $time[0].'AM';
	            } else {
	              $time[0] = $time[0].'PM';
	            }
	          }
	
	          //Convert start time and end time to time object
	          $time[0] = date('H:i', strtotime($time[0]));
	          $time[1] = date('H:i', strtotime($time[1]));
	        }
	
	        preg_match_all('/(M|Tu|W|Th|F)/', $days, $matches);
	        foreach($matches[0] AS $match) {
	          switch($match) {
	            case 'M':
	              if(!isset($days_of_week->Monday->$room)) {
	                $days_of_week->Monday->$room = array();
	                array_push($days_of_week->Monday->$room, array('start' => $time[0],'end' => $time[1]));
	              } else {
	                array_push($days_of_week->Monday->$room, array('start' => $time[0],'end' => $time[1]));
	              }
	            break;
	            case 'Tu':
	              if(isset($days_of_week->Tuesday->$room)) {
	                array_push($days_of_week->Tuesday->$room, array('start' => $time[0],'end' => $time[1]));
	              } else {
	                $days_of_week->Tuesday->$room = array();
	                array_push($days_of_week->Tuesday->$room, array('start' => $time[0],'end' => $time[1]));
	              }
	            break;
	            case 'W':
	              if(isset($days_of_week->Wednesday->$room)) {
	                array_push($days_of_week->Wednesday->$room, array('start' => $time[0],'end' => $time[1]));
	              } else {
	                $days_of_week->Wednesday->$room = array();
	                array_push($days_of_week->Wednesday->$room, array('start' => $time[0],'end' => $time[1]));
	              }
	            break;
	            case 'Th':
	              if(isset($days_of_week->Thursday->$room)) {
	                array_push($days_of_week->Thursday->$room, array('start' => $time[0],'end' => $time[1]));
	              } else {
	                $days_of_week->Thursday->$room = array();
	                array_push($days_of_week->Thursday->$room, array('start' => $time[0],'end' => $time[1]));
	              }
	            break;
	            default:
	              if(isset($days_of_week->Friday->$room)) {
	                array_push($days_of_week->Friday->$room, array('start' => $time[0],'end' => $time[1]));
	              } else {
	                $days_of_week->Friday->$room = array();
	                array_push($days_of_week->Friday->$room, array('start' => $time[0],'end' => $time[1]));
	              }
	            break;
	          }
	        }
	      }
	      $count++;
	    }
	  }
	  
	  return object_to_array($days_of_week);
  }
  
  function findAvailableNow ($schedule) {
	  $today = getdate(); 
	  $now = date('H:i', strtotime($today["hours"].':'.$today["minutes"]));
	  $available = new stdClass(); 
	  
	  foreach ($schedule[$today["weekday"]] as $ckey => $class) {
	    $building = split('-', $ckey);
		if(!isset($available->$building[0])) {
		  $available->$building[0] = array(); 	
		}

        foreach ($class["vacant"] as $vkey => $vacant) {
	      if ($now >= $vacant["start"] && $now <= $vacant["end"]) {
	        
		    array_push($available->$building[0], array('room' => $ckey, 'start' => $vacant["start"], 'end' => $vacant["end"]));
		    break;
	      }
	    }
	  }
	  return object_to_array($available);
  }
?>
