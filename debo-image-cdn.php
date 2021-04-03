<?php
   /*
   Plugin Name: Debo Image CDN (JetPack Photon)
   Plugin URI: https://debo.com.ng
   description: This is a simple plugin to totally load all wordpress images (themes, plugins and media library) from the free JetPack Photon CDN for performance speed and geolocation.
   Version: 1.0
   Author: Adebowale Adekoya
   Author URI: https://debo.com.ng
   Tag: image, jetpack, photon, site accelerator, cdn, performance, cloud, speed
   License: GPL2       
*/
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if (!function_exists('dcwpurl')){
function dcwpurl($url){ //this function enables on the fly update of images src url not changeable by the dwpcdn function in media library and those called via ajax
	 # if(get_post_type() == 'post'){ $url = get_the_guid(get_post_thumbnail_id());}	   
	if(strpos($url, "wp.com")!== false){return $url;} // this means the job is done no need to try to do it again to avoid conflict
    if(strpos($url, ".jpg") == false && strpos($url, ".jpeg") == false && strpos($url, ".png") == false && strpos($url, ".gif") == false){return $url;} // limit operation to image file extentions
	
	# $dccdn1 = rand(0,3);// randomly pick one of the four cdn instance
	
	if(strpos($url, "https://") !== false && strpos($url, "wp.com") === false){return str_replace('https://', 'https://i1.wp.com/', $url); }// convert https to https cdn
	if(strpos($url, 'http://') !== false && strpos($url, "wp.com") === false){return str_replace('http://', 'http://i1.wp.com/', $url); }// convert http to http cdn
	return $url; // return the same url unalttered if all the above conditions do not apply
      	 }// end of dcwpurl
}  // end of if function has not already been declared elsewhere

add_filter('wp_get_attachment_url', 'dcwpurl'); // hook to image src filter


function dwpcdn($buffer){
  #  	return 	$buffer = preg_replace('#(https\:\/\/|http\:\/\/|\/\/)((?!i\d\.wp.com)([\/|.|\w|\s|-])*(\.)(?:jpg|jpeg|gif|png))#', 'https://i1.wp.com/$2', $buffer);
	
#	return 	$buffer = preg_replace('#(https\:\/\/|http\:\/\/|\/\/)(('.get_home_url().')([\/|.|\w|\s|-])*(\.)(?:jpg|jpeg|gif|png))#', 'https://i1.wp.com/$2', $buffer);
	$dsturl = preg_replace('#(https\:\/\/|http\:\/\/|\/\/)#', '', site_url());	
		
	return 	$buffer = preg_replace('#(https\:\/\/|http\:\/\/|\/\/)('. $dsturl .'([\/|.|\w|\s|-])*(\.)(?:jpg|jpeg|gif|png))#', 'https://i1.wp.com/$2', $buffer);
	
	}// end of dwpcdn

function Dbuffer_start() { ob_start("dwpcdn"); } 
function Dbuffer_end() { ob_end_flush(); }

add_action('after_setup_theme', 'Dbuffer_start', 4); // e give it early periority to prevent it from by passing a cache plugin that might be too early
add_action('shutdown', 'Dbuffer_end');


function wpimg_cdnp_refetch(){ echo "<link rel='dns-prefetch' href='//i1.wp.com' />";}
add_action('wp_head', 'wpimg_cdnp_refetch', 1);


?>