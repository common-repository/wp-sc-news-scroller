<?php
/*
Plugin Name: WP SC News-Scroller
Plugin URI: http://www.solvercircle.com
Description: Wordpress SC News Scroller plugin is a jquery based wordpress plugin which is used to show the wordpress news post with nice sliding effect from up to down.
Version: 1.0.1
Author: SolverCircle
Author URI: http://www.solvercircle.com
*/


//installing

define("SC_BASE_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));

function ns_admin_register_head(){
	$cssurl = SC_BASE_URL.'/css/nsstyle.css';
    echo "<link rel='stylesheet' type='text/css' href='$cssurl' />\n";
}
add_action('admin_head', 'ns_admin_register_head');

function news_scroller_install(){
	$newoptions = get_option('newsscroller_options');
	add_option('newsscroller_options', $newoptions);
}

function news_scroller_init_new(){
	global $table_prefix, $wpdb;
	$table_newsscroller = $table_prefix."newsscroller";
	
	$sql = "CREATE TABLE IF NOT EXISTS $table_newsscroller (
			  id int(11) NOT NULL auto_increment,
			  `title` mediumtext NOT NULL default '',
			  `description` mediumtext NOT NULL default '',
			  `status` mediumtext NOT NULL default '',
			  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY  (id)
			);";
			
	$wpdb->query($sql);
	

}

function edit_news_data($title,$description,$status,$nid){
	global $wpdb;
	
	$sql="UPDATE wp_newsscroller SET title='".$title."', description='".$description."', status='".$status."' WHERE id='".$nid."'";
	$wpdb->query($sql);
	$msgs='<div class="updated" id="message"><p>News edited successfully</p></div>';
	select_all_tdata($msgs);
}

function delete_news_data($nid){
	global $wpdb;
	$sql="DELETE FROM wp_newsscroller WHERE id='".$nid."'";
	$wpdb->query($sql);
	$msgs='<div class="updated" id="message"><p>News deleted successfully</p></div>';
	select_all_tdata($msgs);
}

function edit_news(){
	global $wpdb;
	
	$ids=$_POST['news_id'];
	$sql="SELECT * FROM wp_newsscroller WHERE id='".$ids."'";
	$result=$wpdb->get_row($sql);
	
	if($result->status==1){ $active='selected="selected"';}else{ $active='';}
	if($result->status==0){ $nactive='selected="selected"';}else{ $nactive='';}
	
	?>
	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" >
	<input type="hidden" name="nids" id="nids" value="<?php echo $result->id;?>" />
	<?php
	echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
	echo '<div id="post-body"><div id="post-body-content"><div id="namediv" class="stuffbox">';
	echo '<h3>Edit News</h3>';
	echo '<div class="inside">';
	echo '<table><tr>';
	echo '<td width="200"><label>Tittle</label></td>';
	echo '<td><input type="text" name="n_title" id="n_title" value="'.$result->title.'" /></td></tr>';
	echo '<tr><td valign="top"><label>Description</label></td>';
	echo '<td><textarea name="n_description" id="n_description" cols="70" rows="3" >'.$result->description.'</textarea></td></tr>';
	echo '<tr><td><label>Status</label></td>';
	echo '<td><select name="status"><option value="1" '.$active.'>Active</option><option value="0" '.$nactive.'>Not Active</option></select></td></tr>';
	echo '<input type="hidden" name="newsscroller_edit_data" value="true" />';
	echo '<tr><td colspan="2" align="right"><input type="submit" value="Edit News" class="button-primary" style="width:100px; border:none;" /></td></tr>';
	echo '</table>';
	?></form><?php
	echo '</div>';
	echo '</div></div></div>';
	echo '</div>';
}

function add_news_scroller_admin_page(){
	add_object_page('News-Scroller', 'News-Scroller', 8, __FILE__, 'menu_news_index');
	add_submenu_page( __FILE__, 'News-Scroller', 'View News', 8,  __FILE__,'menu_news_index' );
}

function add_ns_sub_menu(){
	$page_ref = add_submenu_page(__FILE__, 'News-Scroller', 'New news', 8, 'add-page', 'newsscroller_options');
}

function menu_news_index(){
	if(isset($_POST['newsscroller_edit'])){
		echo '<div class="wrap">';
		echo '<div class="icon32 nws_icon"><br></div><h2>News Scroller</h2><br />';
		edit_news();
		echo '</div>';
	}
	elseif(isset($_POST['newsscroller_edit_data'])){
		echo '<div class="wrap">';
		echo '<div class="icon32 nws_icon"><br></div><h2>News Scroller</h2><br />';
		$title=$_POST['n_title'];
		$description=$_POST['n_description'];
		$status=$_POST['status'];
		$nid=$_POST['nids'];
		edit_news_data($title,$description,$status,$nid);
		echo '</div>';
	}
	elseif(isset($_POST['newsscroller_delete'])){
		echo '<div class="wrap">';
		echo '<div class="icon32 nws_icon"><br></div><h2>News Scroller</h2><br />';
		$nid=$_POST['news_id'];
		delete_news_data($nid);
		echo '</div>';
	}
	else{
		echo '<div class="wrap">';
		echo '<div class="icon32 nws_icon"><br></div><h2>News Scroller</h2><br />';
		select_all_tdata();
		echo '</div>';
	}
}

function menu_test_set_title() {
    global $title;
    $title = 'Menu';
}


function newsscroller_options(){
	global $wpdb;
	news_scroller_init_new();
	if(isset($_POST['newsscroller_submit'])){
		insert_tdata();
		menu_news_index();
	}
	else{
		echo '<div class="wrap">';
		echo '<div class="icon32 nws_icon"><br></div><h2>News Scroller</h2><br />';
		echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
		echo '<div id="post-body"><div id="post-body-content"><div id="namediv" class="stuffbox">';
		echo '<h3>Add News</h3>';
		echo '<div class="inside">';
		?><form method="post" enctype="multipart/form-data" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" ><?php
		echo '<table><tr>';
		echo '<td width="200"><label>Tittle</label></td>';
		echo '<td><input type="text" name="n_title" id="n_title" /></td></tr>';
		echo '<tr><td valign="top"><label>Description</label></td>';
		echo '<td><textarea name="n_description" id="n_description" cols="70" rows="3" ></textarea></td></tr>';
		echo '<tr><td><label>Status&emsp;</label></td>';
		echo '<td><select name="status"><option value="1">Active</option><option value="0">Not Active</option></select></td></tr>';
		echo '<input type="hidden" name="newsscroller_submit" value="true" />';
		echo '<tr><td colspan="2" align="right"><input type="submit" value="Post News" class="button-primary" style="width:100px; border:none;" /></td></tr>';
		echo '</table>';
		?></form><?php
		echo '</div>';
		echo '</div></div></div>';
		echo '</div>';
		echo '</div>';
	}
}


function insert_tdata(){
	global $table_prefix, $wpdb;
	if(isset($_POST['newsscroller_submit'])){
		$post1 = $_POST['n_title'];
		$post2 = $_POST['n_description'];
		$post3 = $_POST['status'];
		
		$sqls="INSERT INTO wp_newsscroller (title, description, status) VALUES ('$post1', '$post2', '$post3')";
		$wpdb->query($sqls);
		
		echo '<div class="updated" id="message"><p>News added successfully</p></div>';
	}
}

function select_all_tdata($msgs=''){
	echo $msgs;
	global $table_prefix, $wpdb;
	$sql="SELECT * FROM wp_newsscroller";
	$tdata_ps=$wpdb->get_results($sql);
	
	echo '<table cellspacing="0" class="widefat fixed">';
	echo '<thead><tr>';
	echo '<th class="manage-column" width="50">Sn</th>';
	echo '<th class="manage-column" width="200">Title</th>';
	echo '<th class="manage-column" width="500">Description</th>';
	echo '<th class="manage-column" width="100">Status</th>';
	echo '<th class="manage-column"></th>';
	echo '<th class="manage-column"></th>';
	echo '</tr></thead>';
	echo '<tfoot><tr><th colspan="6"></th></tr></tfoot>';	
	$i=1;
	foreach($tdata_ps as $tda){
		echo '<tr>';
		echo '<td width="50">'.$i++.'</td>';
		echo '<td width="200">'.$tda->title.'</td>';
		echo '<td width="500">'.$tda->description.'</td>';
		echo '<td width="100">';
		if(($tda->status)==1){
			echo 'Active';
		}
		else{ echo 'Not Active';}
		echo '</td>';
		echo '<td>';
		?><form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" ><?php
		echo '<input type="hidden" name="news_id" id="news_id" value="'.$tda->id.'" />';
		echo '<input type="hidden" name="newsscroller_edit" value="true" />';
		echo '<input type="submit" value="Edit" class="edit_btn" />';
		?></form><?php
		echo '</td>';
		echo '<td >';
		?><form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" ><?php
		echo '<input type="hidden" name="news_id" id="news_id" value="'.$tda->id.'" />';
		echo '<input type="hidden" name="newsscroller_delete" value="true" />';
		echo '<input type="submit" value="Delete" class="delete_btn" />';
		?></form><?php
		echo '</td>';
		echo '</tr>';
	}
	
	echo '</table>';

}

function news_scroller_uninstall(){
	delete_option('scroller_options');
}

//===================================widget area  ==============================================

function news_scroller_widget(){
	global $wpdb;
	$sql="SELECT * FROM wp_newsscroller WHERE status='1'";
	$tdata_ps=$wpdb->get_results($sql);
	?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.2.74.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$('.slideshow').cycle({
				fx: 'scrollUp' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
			});
		});
	</script>
    <?php
	echo '<div class="slideshow" style="height:150px; border:solid 1px #cccccc; padding:5px;">';
	foreach($tdata_ps as $tda){
		echo '<div style="height:160px; width:100%; border-bottom:solid 1px #cccccc; padding:0px 5px 0px 5px">';
		echo '<div style="font-weight:bold; height:25px; margin-top:10px;">'.$tda->title.'</div>';
		echo '<div>'.$tda->description.'</div>';
		echo '</div>';
	}
	echo '</div>';
}

function widget_news_scroller($args) {
  extract($args);
  echo $before_widget;
  echo $before_title;?>News Scroller<?php echo $after_title;
  news_scroller_widget();
  echo $after_widget;
}


function news_scroller_widget_init(){
	register_sidebar_widget("News_Scroller", "widget_news_scroller");
}

//===============================================================================================

add_action('admin_menu', 'add_news_scroller_admin_page');
register_activation_hook( __FILE__, 'news_scroller_install' );
register_deactivation_hook( __FILE__, 'news_scroller_uninstall' );

add_action('admin_menu', 'add_ns_sub_menu');

add_action("plugins_loaded", "news_scroller_widget_init");

?>