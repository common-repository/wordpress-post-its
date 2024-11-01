<?php
/*
Plugin Name: Word Press Post-its
Plugin URI: http://www.grupomayanguides.com/wpost-its/
Description:  
Author: Adam Jones
Version: 1.0
Author URI: http://www.grupomayanguides.com/
*/

// ******************************************
// *********** INSTALACION ******************
// ******************************************

register_activation_hook(__FILE__,'wppostits_install');

$wppostits_table_name = "postits_messages";

function wppostits_install () {
   global $wpdb;
   $wppostits_table_name = "postits_messages";
   
   $table_name = $wpdb->prefix . $wppostits_table_name;
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  id_postit mediumint(9) NOT NULL AUTO_INCREMENT,
	  postit_date DATE NOT NULL,
	  postit_date_expires DATE NOT NULL,	  
	  postit_title VARCHAR(55) NOT NULL,
	  postit_message TEXT NOT NULL,
	  UNIQUE KEY id_postit (id_postit)
	);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

   }
}



function wppostits_getwidget()
{	
		$optStyle = get_option("wppostits_style");
		if (!$optStyle)
		  $optStyle = "wppostits_style1.png";
		  
        return '<div id="wppostits_widget" style="background:url(' . get_option("siteurl") 
    			    . '/wp-content/plugins/wordpress_postits/' . $optStyle .') no-repeat; ' .
    			    'width: 150px; height:150px;"><br><br><br>' .
  			 '<div id="wppostits_title"   style="margin:0px 0px 0px 20px;"></div> ' .
    		 '<div id="wppostits_message" style="margin:4px 0px 0px 20px; width:120px; height: 80px; border: 0px solid black;">' .
    		'</div> ' .
    	   '</div>' . 
    	    '<div>' ."\n" .   
				'<font style="font-size:6px;">Created by ' . 
					'<a href="http://www.grupomayanguides.com/" target="_TOP" title="grupo mayan">wppostits plugin</a></font>' . 
             '</div>' . "\n";
	
}



function wppostits_widget($args) {
  extract($args);
  echo $before_widget;
  echo $before_title;?><?php echo $after_title;
  echo wppostits_getwidget();
  echo $after_widget;
}

function wppostits_setmessages()
{  
	global $wpdb;
	global $wppostits_table_name;
?>
	<script type="text/javascript">
		var arrMessages = new Array();
		var arrTitles   = new Array();
		
		<?php 
			
			$messages = $wpdb->get_results( 
											 "SELECT * FROM " . 
											 $wpdb->prefix .  $wppostits_table_name .
											 " WHERE postit_date_expires > '" . date("Y-m-d") . "'" .
											 " ORDER BY postit_date"
										   );
		if ($messages)
		{										    
			for ($actualm = 0; $actualm < count($messages); $actualm++)
			{
		?>
		 	  arrMessages[<?php echo $actualm ?>] = "<?php echo $messages[$actualm]->postit_message ?>";
		 	  arrTitles[<?php echo $actualm ?>] = "<?php echo $messages[$actualm]->postit_title ?>";
		 	  
		 <? } ;?>
		
		var iActualMessages = 0;
		<?
			$optTimeInterval = get_option("wppostits_timeinterval");

  			if (!$optTimeInterval || $optTimeInterval == "0")
				$optTimeInterval = "5";

			$optTimeInterval *= 1000;
			 
		?>
		setInterval ('wppostits_setMessages();',<?php echo $optTimeInterval  ?>);
		
		function wppostits_setMessages ()
		{
			var div = document.getElementById("wppostits_message");
			if (div == null)
			  return;
			
			var divTitle = document.getElementById("wppostits_title");
			  
			iActualMessages++;
			if (iActualMessages == arrMessages.length)
			 iActualMessages = 0;
			div.innerHTML      = arrMessages[iActualMessages];
			divTitle.innerHTML = "<b>" + arrTitles[iActualMessages] + "</b>";
		}
		
	</script>
<?php	
	};
}

function wppostits_widget_control()
{
  
  $optTimeInterval = get_option("wppostits_timeinterval");
  
  
  if (!$optTimeInterval)
		$optTimeInterval = "5";

  $style1checked = "";
  $style2checked = "";
  $optStyle = get_option("wppostits_style");
  if ($optStyle == "wppostits_style2.png")
  	$style2checked = "checked";
  else
  	$style1checked = "checked";
   
  
  
  if ($_POST['wppostits-Submit'])
  {
	    update_option("wppostits_timeinterval",  $_POST['wppostits_timeinterval']);
	    update_option("wppostits_style",  $_POST['wppostits_style']);
  }
  
  ?>
   <p>
    <label for="wppostits_timeinterval">Time interval between messages: </label>
    <input type="text" id="wppostits_timeinterval" name="wppostits_timeinterval" size="3" maxlength="3" 
    	   value="<?php echo $optTimeInterval;?>" /> seconds.<br>
    
    <table>
    <tr><td colspan="4">Style:</td></tr>
    <tr>
    	<td valign="middle">
	    	<input type="radio" name="wppostits_style" value="wppostits_style1.png" <?php echo $style1checked ?>>
	    </td>
	    <td>
		    <img 
	    	src="<?php echo get_option("siteurl"). '/wp-content/plugins/wordpress_postits/wppostits_style1_icon.jpg' ?>"> 
	    </td>
    <td valign="middle">
    	<input type="radio" name="wppostits_style" value="wppostits_style2.png" <?php echo $style2checked ?>>
    </td>
    <td>
	    <img 
	    	src="<?php echo get_option("siteurl"). '/wp-content/plugins/wordpress_postits/wppostits_style2_icon.jpg' ?>">
    </td>
    </tr>
    </table>
    <input type="hidden" id="wppostits-Submit" name="wppostits-Submit" value="1" />
  </p>
  <?php
};


include ("wpress_postits_list.php");
include ("wpress_postits_addedit.php");
// Hook for adding admin menus
add_action('admin_menu', 'wppostits_add_pages');

// action function for above hook
function wppostits_add_pages() {
    
	// Add a new submenu under Options:
	 if ($_REQUEST["wppostits_id_postit"] || $_REQUEST["wppostit_addnew"] )
   		add_options_page('WPost-Its !', 'WPost-Its !', 8, 'wppostits', 'wppostits_edit_page');
     else
		add_options_page('WPost-Its !', 'WPost-Its !', 8, 'wppostits', 'wppostits_list_page');
}





function wppostits_init()
{
  register_sidebar_widget(__('WordPress Post-its'), 'wppostits_widget');
  register_widget_control('WordPress Post-its', 'wppostits_widget_control', 300, 200 );
}

add_action('wp_footer', 'wppostits_setmessages');
add_action("plugins_loaded", "wppostits_init");


?>
