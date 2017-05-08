<?php

// utility file: generates a list of valid fonts, invalid fonts, and character subsets
// for use with OpenSourceFonts bot

include("parseMetadata.php");

function valid($font) {
	return file_exists($font."/METADATA.pb");
}

chdir('./ofl');
$dirs = array_filter(glob('*'), 'is_dir');

$fonts = [];
$invalidFonts = [];
foreach($dirs as $font){
	if(valid($font)){
		array_push($fonts, "\"{$font}\"");
	}else{
		array_push($invalidFonts, "\"{$font}\"");
	}
}

chdir('./../');
$subsets = [];
foreach($fonts as $font){
	$metadata = parseMetadata(trim($font,'"'));
	foreach($metadata["subsets"] as $subset){
		if(!in_array($subset, $subsets)){
			array_push($subsets,$subset);
		}
	}
}
asort($subsets);

echo "<br>valid fonts";
echo "<br>" . implode(', ',$fonts);
echo "<br>";
echo "<br>invalid fonts (no metadata)";
echo "<br>" . implode(', ',$invalidFonts);
echo "<br>";
echo "<br>subsets";
echo "<br>" . implode(', ',$subsets);



?>
