<?php

function wppostits_edit_page() {
	global $wpdb;
	global $wppostits_table_name;
  
	// *** Post-It Info
	$id_postit      	 = $_REQUEST["wppostits_id_postit"];
	$postit_title		 = "";
	$postit_message 	 = "";
	$postit_date    	 = date("Y-m-d");
	$postit_date_expires = date("Y-m-d",time(date("Y-m-d")) + (3600 * 24 * 30));
			
	// *******  SAVE POST-IT ********
	if ( !($_POST["wppostits_submit"] == "ok") )
	{
		if ($id_postit)
		{   $query = "SELECT * FROM " . $wpdb->prefix . $wppostits_table_name .  
							 			     " WHERE id_postit = $id_postit ";
			
		    $postitInfo = $wpdb->get_results($query);
			
			$postit_title		 = $postitInfo[0]->postit_title;
			$postit_message 	 = $postitInfo[0]->postit_message;
			$postit_date    	 = $postitInfo[0]->postit_date;
			$postit_date_expires = $postitInfo[0]->postit_date_expires;
		}
	}
	else
	{
		   // *** Postits Data
		   $postit_title        = str_replace("\'"," ",$_POST["wppostits_postit_title"]);
		   $postit_message 	    = str_replace("\'"," ",$_POST["wppostits_postit_message"]);
		   $postit_date    	    = str_replace("\'"," ",$_POST["wppostits_postit_date"]);
		   $postit_date_expires = str_replace("\'"," ",$_POST["wppostits_postit_date_expires"]);
		   
		   if ($id_postit)
		   {
			    	 $query = " UPDATE " . $wpdb->prefix . $wppostits_table_name .  
				   				  " SET postit_title   		= '$postit_title', " . 
				   				      " postit_message 		= '$postit_message', " . 
				   				      " postit_date  		= '$postit_date',  "  . 
				   				      " postit_date_expires = '$postit_date_expires' " . 
			    	 		 " WHERE id_postit = $id_postit ";
			    	 
			    	$wpdb->query($query);
		   }
		   else
		   {
				    $query = "INSERT INTO " . $wpdb->prefix . $wppostits_table_name  .  
				   					 "( postit_title, postit_message, postit_date, postit_date_expires )  " . 
				   				"VALUES ('$postit_title', '$postit_message', '$postit_date', '$postit_date_expires') "; 
			    
			     
			   		 $wpdb->query($query);
			    	 $lastID = $wpdb->get_results("SELECT MAX(id_postit) as lastid_postit " .
			    		  						   " FROM " . $wpdb->prefix . $wppostits_table_name .
			    							 	  " WHERE postit_title = '$postit_title'");
			    	 
			    	 $id_postit = $lastID[0]->lastid_postit;
		  }
			    
	}
	 

?>
<div class="wrap">
<script type="text/javascript">
	function validateInfo(forma)
	{
		if (forma.wppostits_postit_title.value == "")
		{
			alert("You must type a title");
			forma.wppostits_postit_title.focus();
			return false;
		}
		
		if (forma.wppostits_postit_message.value == "")
		{
			alert("You must type a postit message");
			forma.wppostits_postit_message.focus();
			return false;
		}
		
		if (forma.wppostits_postit_date.value == "")
		{
			alert("The date cannot be empty");
			forma.wppostits_postit_date.focus();
			return false;
		}
		
		if (forma.wppostits_postit_date_expires.value == "")
		{
			alert("The expire date cannot be empty");
			forma.wppostits_postit_date_expires.focus();
			return false;
		}
		
		
	return true;
}
</script>

<form name="wppostits_form" method="post" onsubmit="return validateInfo(this);" 
	  action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	  

<?php
    // Now display the options editing screen

    // header
	if ($id_postit)
    	echo "<h2>" . __( 'Edit Post-It',    'mt_trans_domain' ) . "</h2>";
    else
       	echo "<h2>" . __( 'Add New Post-It', 'mt_trans_domain' ) . "</h2>";

    // options form
    
 ?>
    <?php if ( $_POST["wppostits_submit"] == "ok" ) { ?>
    <div class="updated"><p><strong><?php _e('Post-it information saved.', 'mt_trans_domain' ); ?></strong></p></div><br>	
    <? }; ?>

 	
 	<span class="stuffbox" >
 		
		 <label for="wppostits_postit_date">Date</label>
		 <span class="inside">	
		 	<input type="text" size="10" maxlength="11" id="wppostits_postit_date" name="wppostits_postit_date"
		 		   value="<?php echo $postit_date ?>"> 
	     </span>
	     
	     <br>
	     
	     <label for="wppostits_postit_date_expires">Expires</label>
		 <span class="inside">	
		 	<input type="text" size="10" maxlength="11" id="wppostits_postit_date_expires" name="wppostits_postit_date_expires"
		 		   value="<?php echo $postit_date_expires ?>"> 
	     </span>
	     
	     <br>
	     
	     <label for="wppostits_postit_title">Title</label>
		 <span class="inside">	
		 	<input type="text" size="15" maxlength="15" id="wppostits_postit_title" name="wppostits_postit_title"
		 		   value="<?php echo $postit_title ?>"> 
	     </span>
	     
	     <br>
	     
	     <label for="wppostits_postit_message">Message</label>
		 <span class="inside">	
		 	<input type="text" size="20" maxlength="100" id="wppostits_postit_message" name="wppostits_postit_message"
		 		   value="<?php echo $postit_message ?>"> 
	     </span>
	     
	     <br>
	     
 	</span>
 

<p class="submit">
	<input type="hidden" name="wppostits_submit" value="ok">
	<input type="hidden" name="wppostits_id_postit" value="<?php echo $id_postit ?>">
	<input type="submit" name="Submit" value="<?php _e('Save Post-It Information', 'mt_trans_domain' ) ?>" />&nbsp;
	<input type="button" name="Return" value="<?php _e('Return to Post-It List', 'mt_trans_domain' ) ?>"
		   onclick="document.location='options-general.php?page=wppostits' " />
</p>

</form>

</div> <!-- **** DIV WRAPPER *** -->

<?php } ?>