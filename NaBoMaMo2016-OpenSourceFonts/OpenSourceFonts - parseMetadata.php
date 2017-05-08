<?php function parseMetadata($font){
	$file = file(realpath("./ofl/{$font}/METADATA.pb"));
	if(!$file){
		return false;
	}
	$result = [];
	$result["fonts"] = [];
	$scope = &$result;

	foreach($file as $line) {
		// split into key-val pair
		$line = explode(": ",trim($line));

		if(count($line) > 1){
			$key = $line[0];
			$val = $line[1];
			// if we don't have an entry array yet, make one
			if(!isset($scope[$key])) {
				$scope[$key] = [];
			}

			// remove wrapping quotation marks
			if(strcmp($val[0],'"') == 0 && strcmp($val[strlen($val)-1],'"') == 0){
				$val = substr($val,1,-1);
			}

			// turn \" into "
			$val = stripslashes($val); 
			
			array_push($scope[$key], $val);
		} else {
			if ($line[0] == "fonts {") {
				array_push($result["fonts"], []);
				$scope = &$result["fonts"][count($result["fonts"])-1];
			} else {
				$scope = &$result;
			}
		}
	}

	return $result;
}
?>
