<?php
include("parseMetadata.php");


// multi-byte string reverse written by Kevin van Zonnevel
// http://kvz.io/blog/2012/10/09/reverse-a-multibyte-string-in-php/
function mb_strrev ($string, $encoding = null) {
	if ($encoding === null) {
		$encoding = mb_detect_encoding($string);
	}

	$length   = mb_strlen($string, $encoding);
	$reversed = '';
	while ($length-- > 0) {
		$reversed .= mb_substr($string, $length, 1, $encoding);
	}

	return $reversed;
}


function main(){
	$subsets = array(
		"arabic" => "",
		"bengali" => "",
		"cyrillic" => "",
		"cyrillic-ext" => "",
		"devanagari" => "",
		"ethiopic" => "",
		"greek" => "",
		"greek-ext" => "",
		"gujarati" => "",
		"gurmukhi" => "",
		"hebrew" => "",
		"kannada" => "",
		"khmer" => "",
		"korean" => "",
		"lao" => "",
		"latin" => "",
		"latin-ext" => "",
		"malayalam" => "",
		"menu" => "",
		"myanmar" => "",
		"oriya" => "",
		"sinhala" => "",
		"tamil" => "",
		"telugu" => "",
		"thai" => "",
		"vietnamese" => ""
	);

	foreach($subsets as $key => $val){
		$subsets[$key] = trim(file_get_contents(realpath("./subsets/{$key}.txt")));

		// arabic + hebrew are RTL
		if(
			strcmp($key, "arabic") == 0
			||
			strcmp($key, "hebrew") == 0
		){
			$subsets[$key] = mb_strrev(mb_convert_encoding($subsets[$key], "UTF-8", "HTML-ENTITIES"));
		}
	}

	// image dimensions
	$supersampling = 3;
	$w = 1320*$supersampling;
	$h = 742*$supersampling;

	$strokeColor = "rgb(255, 255, 255)";
	$fillColor = "rgb(255, 255, 255)";
	$backgroundColor = "rgb(0, 0, 0)";

	// get font from URL
	$font = $_GET["font"] == null ? "Error: no font provided" : $_GET["font"];

	$fontMetadata = parseMetadata($font);
	if(!$fontMetadata) {
		// invalid font
		// (listFonts should have accounted for this, but just in case)
		$fontFile = "Helvetica";
		$fontName = "Missing \"{$font}/METADATA.pb\"";
		$fontCopyright = ":(";
		$fontSubsets = [];
	} else {
		// get relevant metadata
		$fontVariant = $fontMetadata["fonts"][array_rand($fontMetadata["fonts"], 1)];
		$fontFile = realpath("./ofl/".$font."/".$fontVariant["filename"][0]);
		$fontName = $fontVariant["name"][0];
		$fontCopyright = $fontVariant["copyright"][0];
		$fontSubsets = $fontMetadata["subsets"];
	}

	// convert subsets to translations
	foreach($fontSubsets as &$subset) {
		if(isset($subsets[$subset])){
			$subset = $subsets[$subset];
		}
	}
	$fontSubsets = array_filter($fontSubsets); // remove empty subsets
	$latin = in_array("latin", $fontSubsets) || in_array("latin-ext", $fontSubsets); // check for latin
	$fontSubsets = implode(", ", $fontSubsets); // convert to single string

    $imagick = new Imagick();
    $draw = new ImagickDraw();
 
    $draw->setStrokeColor($strokeColor);
    $draw->setFillColor($fillColor);

	$draw->setTextAntialias(true);
    $draw->setStrokeWidth(0);
	$draw->setTextAlignment(Imagick::ALIGN_CENTER);
	$draw->setGravity (Imagick::GRAVITY_CENTER);
    $draw->setTextEncoding("UTF-8");


	// title + copyright only use font
	// if latin, since they're in english
    if($latin){
    	$draw->setFont($fontFile);
    }else{
    	$draw->setFont("Helvetica");
    }

	// font name
	$fontSizeTitle = 100*$supersampling;
	do{
		$fontSizeTitle -= 1;
    	$draw->setFontSize($fontSizeTitle);
		$metricsTitle = $imagick->queryFontMetrics($draw, $fontName, false);
    }while($metricsTitle["textWidth"] > $w/8*7);

	// copyright notice
	$fontSizeCopyright=50*$supersampling;
	do{
		$fontSizeCopyright -= 1;
    	$draw->setFontSize($fontSizeCopyright);
    	$metricsCopyright = $imagick->queryFontMetrics($draw, $fontCopyright, false);
	}while($metricsCopyright["textWidth"]  > $w/4*3);

	$posTitle = $h/2;
    $posCopyright = $h/5*4;
    $posSubsets = $h/5*3;
    
    $draw->setFontSize($fontSizeTitle);
    $draw->annotation($w/2, $posTitle, $fontName);
 
    $draw->setFontSize($fontSizeCopyright);
    $draw->annotation($w/2, $posCopyright, $fontCopyright);

    // subsets
	$draw->setFont($fontFile); // subset always drawn in font
	$fontSizeSubsets=50*$supersampling;
	do{
		$fontSizeSubsets -= 1;
    	$draw->setFontSize($fontSizeSubsets);
    	$metricsSubsets = $imagick->queryFontMetrics($draw, $fontSubsets, false);
	}while($metricsSubsets["textWidth"]  > $w/4*3);

    $draw->setFontSize($fontSizeSubsets);
    $draw->annotation($w/2, $posSubsets, $fontSubsets);
 

 	// make the image
    $imagick->newImage($w, $h, $backgroundColor);
    $imagick->setImageFormat("png");
    $imagick->drawImage($draw);
	$imagick->scaleImage($w/$supersampling, $h/$supersampling);
 
    header("Content-Type: image/png");
    echo $imagick->getImageBlob();
}

// go!
main();
?>
