<?php
///////////////////Default account status after registration//////////////
$accountLevel='admin';  //suspended, limited, standard, advanced or admin

///////////////////////////////////////////////////////////
////////////////Images upload /////////////////////////////
$icon='logo/favicon.png';                       //icon link
$logo='logo/logo-transparent.png';                       //logo link
$ImgSize='3';                  //Megabytes
$imgQuality='45';               //% of the original quality 70% = 70

////////////////Messages///////////////////////////////////
$messageUpdate='4000';         //chat refresh interval in miliseconds
$opacity = '0.25';               //Color opacity

////////////////////////////////////////////////////////////
$pdfIcon='40';                 ////pdf icon size////

////////////////////////////message font////////////////////////////////
$dateFont='
size="2"
style="
padding: 0px; 
"';

$messageFont='
size="3"
style="
padding: 8px; 
"';

$imgSize='95%';                 

///////////////////Repository//////////////////////////////
$updatesUrl = 'https://cloudapps.zapto.org/cma-update/';

///////////////////////// encryption keys /////////////////////
$secretkey='e7be576e45u765u7b474576u4u745';
$iv='b457345734737345y7b35yeth';

$usernameCookieName='9857y9n5mywp48ky5peo45uy9nepbmuyep4o5uy9e4pbvyupe59uyw4pm5yuw4ny9uefgrdghdgsdfg65y35h7e57w4573465g454g4';

/////////////////Error Reporting///////////////////////////

$error='0';

//hide errors
if($error==0){
    error_reporting(0);
    ini_set('display_errors', 0);
}

//show errors
if($error==1){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
