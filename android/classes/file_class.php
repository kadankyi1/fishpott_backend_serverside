<?php

class fileActions {


 	public function fileSizeIsNotLargerThanMaxSize($file, $max_size) {		

		if ($file["size"] <= $max_size) {
			return true;
		} else {
			return false;
		}

	} // END OF fileSizeIsNotLargerThanMaxSize

 	public function calculateCompressionQuality($file, $max_size) {		

			return ($file["size"]/$max_size)*100;

	} // END OF fileSizeIsNotLargerThanMaxSize

	public function compress($source, $destination, $quality, $auto_compress) {

	    $info = getimagesize($source);

	    if ($info['mime'] == 'image/jpeg') 
	        $image = imagecreatefromjpeg($source);

	    elseif ($info['mime'] == 'image/gif') 
	        $image = imagecreatefromgif($source);

	    elseif ($info['mime'] == 'image/png') 
	        $image = imagecreatefrompng($source);

	    if($auto_compress === true){

	    } else {
	    	imagejpeg($image, $destination, $quality);
	    }

	    return $destination;
	} // END OF compress

	public function moveFile($source, $destination){

	    if (move_uploaded_file($source, $destination)) {
	    	return true;
	    } else {
	    	return false;
	    }

	}

}	