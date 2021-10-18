<?php
   /*
   Plugin Name: Debo Image CDN (JetPack Photon)
   Plugin URI: https://debo.com.ng
   description: This is a simple plugin to totally load all wordpress images (themes, plugins and media library) from the free JetPack Photon CDN for performance speed, geolocation and bandwidth consumption reducton purpose. No settings required it works once activated.
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
    
function dcwpurl($url){ 
    
  	if(strpos($url, "wp.com")!== false){return $url;} // this means the job is done no need to try to do it again to avoid redundance
	
    if(strpos($url, ".jpg") == false || strpos($url, ".jpeg") == false || strpos($url, ".png") == false || strpos($url, ".gif") == false){
        
        return $url; // limit operation to image file extentions
    }

	if(strpos($url, "https://") !== false && strpos($url, "wp.com") === false){return str_replace('https://', 'https://i1.wp.com/', $url); }// convert https (secure) to https cdn
	
	if(strpos($url, 'http://') !== false && strpos($url, "wp.com") === false)
	{return str_replace('http://', 'http://i1.wp.com/', $url); }// convert http (unsecure) to http cdn
	
	return $url; // return the same url unalttered if all the above conditions do not apply
      	 
    
}// end of dcwpurl func
      	 
}  // end of if function has not already been declared elsewhere

add_filter('wp_get_attachment_url', 'dcwpurl'); // hook to image src filter


function dwpcdn($buffer){
    
      # This function enables on-the-fly update of images src not changeable by the dcwpcdn function in media library and those called via ajax

	$dsturl = preg_replace('#(https\:\/\/|http\:\/\/|\/\/)#', '', site_url());	
		
	return 	$buffer = preg_replace('#(https\:\/\/|http\:\/\/|\/\/)('. $dsturl .'([\/|.|\w|\s|-])*(\.)(?:jpg|jpeg|gif|png))#', 'https://i1.wp.com/$2', $buffer);
	
	}// end of dwpcdn

function Dbuffer_start() { ob_start("dwpcdn"); } 

add_action('after_setup_theme', 'Dbuffer_start', 4); // give it early periority of 4 to prevent it from bypassing a cache plugin that may be earlier than it.


function wpimg_cdnp_refetch(){ echo "<link rel='dns-prefetch' href='//i1.wp.com' />";}
add_action('wp_head', 'wpimg_cdnp_refetch', 1); // add dns meta refresh to html head


?>
