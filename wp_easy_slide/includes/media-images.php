<?php
/*
Plugin Name: WordPress Easy Slider
Plugin URI: http://www.dallasprowebdesigners.com/free-wordpress-plugins.html
Description: Smooth Slider adds a smooth content and image slideshow with customizable background and slide intervals to any location of your blog
Version: 2.3.2	
Author: Max Steel
Author URI: http://www.dallasprowebdesigners.com/
Wordpress version supported: 2.9 and above
*/

/*  Copyright 2009-2010  Max Steel  (email : info@dallasprowebdesigners.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//For media files
function smooth_slider_media_lib_edit($form_fields, $post){
global $smooth_slider;
if (current_user_can( $smooth_slider['user_level'] )) {
    if ( substr($post->post_mime_type, 0, 5) == 'image') {
		$post_id = $post->ID;
		$extra = "";

		if(isset($post->ID)) {
			$post_id = $post->ID;
			if(is_post_on_any_slider($post_id)) { $extra = 'checked="checked"'; }
		} 
		
		$post_slider_arr = array();
		
		$post_sliders = ss_get_post_sliders($post_id);
		if($post_sliders) {
			foreach($post_sliders as $post_slider){
			   $post_slider_arr[] = $post_slider['slider_id'];
			}
		}
		
		$sliders = ss_get_sliders();
		
			  
	  $form_fields['slider'] = array(
              'label'      => __('Check the box and select the slider'),
              'input'      => 'html',
              'html'       => "<input type='checkbox' style='margin-top:6px;' name='attachments[".$post->ID."][slider]' value='slider' ".$extra." /> &nbsp; <strong>Add this Image to </strong>",
              'value'      => 'slider'
           );
	  
	  $sname_html='';
 
	  foreach ($sliders as $slider) { 
	     if(in_array($slider['slider_id'],$post_slider_arr)){$selected = 'selected';} else{$selected='';}
         $sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';
      } 
	  $form_fields['slider_name[]'] = array(
              'label'      => __(''),
              'input'      => 'html',
              'html'       => '<select name="attachments['.$post->ID.'][slider_name][]" multiple="multiple" size="2" style="height:4em;">'.$sname_html.'</select>',
              'value'      => ''
           );
     
	 $sslider_link= get_post_meta($post_id, 'slide_redirect_url', true);  
	 $sslider_nolink=get_post_meta($post_id, 'sslider_nolink', true);
	 if($sslider_nolink=='1'){$checked= "checked";} else {$checked= "";}
	 $form_fields['sslider_link'] = array(
              'label'      => __('Slide Link URL'),
              'input'      => 'html',
              'html'       => "<input type='text' style='clear:left;' class='text urlfield' name='attachments[".$post->ID."][sslider_link]' value='" . esc_attr($sslider_link) . "' />",
              'value'      => $sslider_link
           );
     $form_fields['sslider_nolink'] = array(
              'label'      => __('Do not link this slide to any page(url)'),
              'input'      => 'html',
              'html'       => "<input type='checkbox' name='attachments[".$post->ID."][sslider_nolink]' value='1' ".$checked." />",
              'value'      => 'slider'
           );
  }
  else {
     unset( $form_fields['slider'] );
	 unset( $form_fields['slider_name[]'] );
	 unset( $form_fields['sslider_link'] );
	 unset( $form_fields['sslider_nolink'] );
  }
  return $form_fields;
}
}

add_filter('attachment_fields_to_edit', 'smooth_slider_media_lib_edit', 10, 2);

function smooth_slider_media_lib_save($post, $attachment){
global $smooth_slider;
if (current_user_can( $smooth_slider['user_level'] )) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	$post_id=$post['ID'];
	
	if(isset($attachment['slider']) and !isset($attachment['slider_name'])) {
	  $slider_id = '1';
	  if(is_post_on_any_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	  }
	  
	  if(isset($attachment['slider']) and $attachment['slider'] == "slider" and !slider($post_id,$slider_id)) {
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
	  }
	}
	if(isset($attachment['slider']) and $attachment['slider'] == "slider" and isset($attachment['slider_name'])){
	  $slider_id_arr = $attachment['slider_name'];
	  $post_sliders_data = ss_get_post_sliders($post_id);
	  
	  foreach($post_sliders_data as $post_slider_data){
		if(!in_array($post_slider_data['slider_id'],$slider_id_arr)) {
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		  $wpdb->query($sql);
		}
	  }
    
		foreach($slider_id_arr as $slider_id) {
			if(!slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			}
		}
	}
	
	$sslider_link = get_post_meta($post_id,'slide_redirect_url',true);
	$link=$attachment['sslider_link'];
	if(!isset($link) or empty($link)){$link=get_permalink($post_id);}
	if($sslider_link != $link) {
	  update_post_meta($post_id, 'slide_redirect_url', $link);	
	}
	
	$sslider_nolink = get_post_meta($post_id,'sslider_nolink',true);
	if($sslider_nolink != $attachment['sslider_nolink']) {
	  update_post_meta($post_id, 'sslider_nolink', $attachment['sslider_nolink']);	
	}
}	
	return $post;	
} 

add_filter('attachment_fields_to_save', 'smooth_slider_media_lib_save', 10, 2);
?>