<?php
function codeRand( $len = 0 )
{
	if( $len < 1 ) $len = 5;
	$a = 'abcdefghijklmnopqrstuvwxyz1234567890';
	$c = $a[rand(0, 25)];
	for($i=0; $i< $len-1; $i++) { $c .= $a[rand(0, 35)]; }
	return $c;
}
function cleanTitleURL( $s )
{
	$s = strtolower($s);	
	$s = preg_replace("/[^a-z0-9-_\s]/i", "", $s );
	$s = preg_replace("/\s+/", "-", trim( $s ) );
	
	return $s;
}

function make_slug ($s){
	$s = preg_replace('/\W+/','-',$s);
	$s = preg_replace('/[^A-Za-z0-9-]+/', '-', $s);
   return strtolower($s);
} 

function mailer( $to, $subject, $msg )
{
	$headers = '';
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "Return-Path: \"".SITE_FROM_EMAIL_NAME."\" <". SITE_FROM_EMAIL_ADDRESS .">\r\n";
	$headers .= "Reply-To: \"".SITE_FROM_EMAIL_NAME."\" <". SITE_FROM_EMAIL_ADDRESS .">\r\n";
	$headers .= "From: \"".SITE_FROM_EMAIL_NAME."\" <". SITE_FROM_EMAIL_ADDRESS .">\r\n";
	$headers .= "X-Priority: 1\r\n";
	$headers .= "X-Mailer: PHP/".phpversion()."\r\n";
	$headers .= "Content-Transfer-Encoding: 8bit\r\n";
	$headers .= "Priority: Urgent\r\n";
	$headers .= "Importance: high";

	return @mail($to, $subject, $msg, $headers);
}