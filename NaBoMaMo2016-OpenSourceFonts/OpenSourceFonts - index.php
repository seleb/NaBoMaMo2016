<?php
	function main(){
		// image dimensions
		$w = 1320;
		$h = 742;
		
		// create image
		$img = imagecreate($w,$h);
		// image colors
		$colour2 = imagecolorallocate($img, 0, 0, 0);
		$colour1 = imagecolorallocate($img, 255, 255, 255);

		// get font from URL
		$font = $_GET["font"] == null ? "Error: no font provided" : $_GET["font"];

		// retrieve font metadata and parse it into file, name, and copyright notice
		$fontMetadataFile = "ofl/".$font."/METADATA.pb";
		$fontMetadata = file($fontMetadataFile);
		$fontVariants = explode("fonts", implode($fontMetadata));
		$fontVariant = split("\n",$fontVariants[mt_rand(1,count($fontVariants)-1)]);
		
		$fontFile = "ofl/".$font."/".substr(trim(array_pop(explode(": ", $fontVariant[4], 2))),1,-1);
		$fontName = substr(trim(array_pop(explode(": ", $fontVariant[6], 2))),1,-1);
		$fontCopyright = str_replace('\\"', '"', substr(trim(array_pop(explode(": ", $fontVariant[7], 2))),1,-1));

		// background
		imagefilledrectangle($img, 0, 0, $w, $h, $colour2);

		// print font name
		$fontSize = 100;
		do{
			$fontSize -= 1;
			$bounds = imagettfbbox ($fontSize, 0, $fontFile, $fontName);
		}while($bounds[2] - $bounds[0] > $w/8*7);
		$bounds = imagettfbbox ($fontSize, 0, $fontFile, $fontName);
		imagefttext ($img, $fontSize, 0, $w/2 - ($bounds[2] - $bounds[0])/2, $h/2, $colour1, $fontFile, $fontName);

		// print copyright notice
		$fontSize=50;
		do{
			$fontSize -= 1;
			$bounds = imagettfbbox ($fontSize, 0, $fontFile, $fontCopyright);
		}while($bounds[2] - $bounds[0] > $w/4*3);
		imagefttext ($img, $fontSize, 0, $w/2 - ($bounds[2] - $bounds[0])/2, $h/3*2, $colour1, $fontFile, $fontCopyright);

		// expire instantly
		header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

		// output image
		header('Content-Type: image/png');
		imagepng($img);



		// free resources
		imagecolordeallocate($img, $colour1);
		imagecolordeallocate($img, $colour2);
		imagedestroy($img);
	}

	// go!
	main();
?>