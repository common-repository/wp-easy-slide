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
function ss_get_sliders(){
	global $wpdb,$table_prefix;
	$slider_meta = $table_prefix.SLIDER_META; 
	$sql = "SELECT * FROM $slider_meta";
 	$sliders = $wpdb->get_results($sql, ARRAY_A);
	return $sliders;
}
function get_slider_posts_in_order($slider_id) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	$slider_posts = $wpdb->get_results("SELECT * FROM $table_name WHERE slider_id = '$slider_id' ORDER BY slide_order ASC, date DESC", OBJECT);
	return $slider_posts;
}
function ss_get_post_sliders($post_id){
    global $wpdb,$table_prefix;
	$slider_table = $table_prefix.SLIDER_TABLE; 
	$sql = "SELECT * FROM $slider_table 
	        WHERE post_id = '$post_id';";
	$post_sliders = $wpdb->get_results($sql, ARRAY_A);
	return $post_sliders;
}
function ss_get_prev_slider(){
    global $wpdb,$table_prefix;
	$slider_table = $table_prefix.PREV_SLIDER_TABLE; 
	$sql = "SELECT * FROM $slider_table";
	$prev_slider_data = $wpdb->get_results($sql, ARRAY_A);
	return $prev_slider_data;
}
function ss_post_on_slider($post_id,$slider_id){
    global $wpdb,$table_prefix;
	$slider_postmeta = $table_prefix.SLIDER_POST_META;
    $sql = "SELECT * FROM $slider_postmeta  
	        WHERE post_id = '$post_id' 
			AND slider_id = '$slider_id';";
	$result = $wpdb->query($sql);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function ss_slider_on_this_post($post_id){
    global $wpdb,$table_prefix;
	$slider_postmeta = $table_prefix.SLIDER_POST_META;
    $sql = "SELECT * FROM $slider_postmeta  
	        WHERE post_id = '$post_id';";
	$result = $wpdb->query($sql);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
//Checks if the post is already added to slider
function slider($post_id,$slider_id = '1') {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	$check = "SELECT id FROM $table_name WHERE post_id = '$post_id' AND slider_id = '$slider_id';";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_post_on_any_slider($post_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	$check = "SELECT post_id FROM $table_name WHERE post_id = '$post_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_slider_on_slider_table($slider_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	$check = "SELECT * FROM $table_name WHERE slider_id = '$slider_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_slider_on_meta_table($slider_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_META;
	$check = "SELECT * FROM $table_name WHERE slider_id = '$slider_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_slider_on_postmeta_table($slider_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_POST_META;
	$check = "SELECT * FROM $table_name WHERE slider_id = '$slider_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function get_slider_for_the_post($post_id) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_POST_META;
	$sql = "SELECT slider_id FROM $table_name WHERE post_id = '$post_id' LIMIT 1;";
	$slider_postmeta = $wpdb->get_row($sql, ARRAY_A);
	$slider_id = $slider_postmeta['slider_id'];
	return $slider_id;
}
function smooth_slider_word_limiter( $text, $limit = 50 ) {
    $text = str_replace(']]>', ']]&gt;', $text);
	//Not using strip_tags as to accomodate the 'retain html tags' feature
	//$text = strip_tags($text);
	
    $explode = explode(' ',$text);
    $string  = '';

    $dots = '...';
    if(count($explode) <= $limit){
        $dots = '';
    }
    for($i=0;$i<$limit;$i++){
        $string .= $explode[$i]." ";
    }
    if ($dots) {
        $string = substr($string, 0, strlen($string));
    }
    return $string.$dots;
}
function sslider_admin_url( $query = array() ) {
	global $plugin_page;

	if ( ! isset( $query['page'] ) )
		$query['page'] = $plugin_page;

	$path = 'admin.php';

	if ( $query = build_query( $query ) )
		$path .= '?' . $query;

	$url = admin_url( $path );

	return esc_url_raw( $url );
}
function smooth_slider_table_exists($table, $db) { 
	$tables = mysql_list_tables ($db); 
	while (list ($temp) = mysql_fetch_array ($tables)) {
		if ($temp == $table) {
			return TRUE;
		}
	}
	return FALSE;
}
function add_column_if_not_exist($table_name, $column_name, $create_ddl) {
     global $wpdb, $debug;
      foreach ($wpdb->get_col("DESC $table_name", 0) as $column ) {
          if ($debug) echo("checking $column == $column_name<br />");
          if ($column == $column_name) {
              return true;
          }
     }
      //didn't find it try to create it.
      $q = $wpdb->query($create_ddl);
      // we cannot directly tell that whether this succeeded!
     foreach ($wpdb->get_col("DESC $table_name", 0) as $column ) {
          if ($column == $column_name) {
             return true;
          }
      }
      return false;
}
?>