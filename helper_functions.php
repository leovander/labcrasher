<?php
	//helper function to convert object to array
	function object_to_array($data)
	{
	  if (is_array($data) || is_object($data))
	  {
	      $result = array();
	      foreach ($data as $key => $value)
	      {
	          $result[$key] = object_to_array($value);
	      }
	      return $result;
	  }
	  return $data;
	}
	
	//helper function to sort the time for each class
	function cmp($a, $b)
	{
	if ($a["start"] == $b["start"]) {
	    return 0;
	}
	return ($a["start"] < $b["start"]) ? -1 : 1;
	}
	
	function pr($array) {
		print('<pre>');
		print_r($array);
		print('</pre>');
	}
	
	function p ($input) {
		print ($input);
	}
?>