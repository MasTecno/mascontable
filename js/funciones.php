<?php
// $Pref=randomText(35);
// $Suf=randomText(8);
function randomText($length) { 
    //$pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
    // $pattern = "1234567890aBCdEFghIJKlMNopQRsTUVwXYz";
	// $key="";
    // for($i = 0; $i < $length; $i++) { 
    //     $key .= $pattern{rand(0, 35)}; 
    // } 
    // return $key; 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;

}

function descript($stringDat){
	$dat=$stringDat;
	$rest = substr($dat, 0, 35);
	$dat=str_replace($rest, "", $dat);
	$rest = substr($dat, -8);
	$dat=str_replace($rest, "", $dat);
	return $dat;
}

// $Pref=randomTextSV(35);
// $Suf=randomTextSV(8);
function randomTextSV($length) { 
    // $pattern = "1234567890aBCdEFghIJKlMNopQRsTUVwXYz"; 
	// $key="";
    // for($i = 0; $i < $length; $i++) { 
    //     $key .= $pattern{rand(0, 35)}; 
    // } 
    // return $key; 

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;

}

function descriptSV($stringDat){
	$dat=$stringDat;
	$rest = substr($dat, 0, 35);
	$dat=str_replace($rest, "", $dat);
	$rest = substr($dat, -8);
	$dat=str_replace($rest, "", $dat);
	return $dat;
}

?>