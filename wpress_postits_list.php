<?php

function wppostits_list_page()
	{
			
	  global $wpdb;
	  global $wppostits_table_name;
	  
?>
<div class="wrap">

<script type="text/javascript">
	function delete_message(idmessage, title)
	{
		if (confirm("Are you sure you want to delete the message " +  title + "?"))
		{
			document.forms["wppostits_listform"].wppostits_messagetodelete.value = idmessage;
			document.forms["wppostits_listform"].wppostits_action.value = "delete";
			document.forms["wppostits_listform"].submit();
		}
	}
</script>
<?php 
	if ( $_POST["wppostits_action"] == "delete" )
	{
		$wpdb->query("DELETE FROM " . $wpdb->prefix .  $wppostits_table_name .
					 " WHERE id_postit = " . $_POST["wppostits_messagetodelete"] );	
	}
	
?>
<h2>WordPress Post-Its !</h2>
<form name="wppostits_listform" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"
	  method="post" >
	<input type="hidden" name="wppostits_messagetodelete" value="">
	<input type="hidden" name="wppostits_action" value="">
				   
	<p class="submit">
		<input type="button" value="Add New Post-It" 
			   onclick="document.location='options-general.php?page=wppostits&wppostit_addnew=ok'">
		<br>
	</p>
</form>		
<br>
<table class="widefat fixed" cellspacing="0">
<thead>
<tr class="thead">
	<th scope="col" class="manage-column column-name" style="">Post it Message</th>
	<th scope="col" class="manage-column column-name" style="">Expires</th>
	<th scope="col" class="manage-column column-email" style="">&nbsp;</th>
	<th scope="col" class="manage-column column-email" style="">&nbsp;</th>
</tr>
</thead>

<tbody id="users" class="list:user user-list">
<?php
	
	$query = " SELECT *  FROM " .
			  $wpdb->prefix . $wppostits_table_name .
			  " ORDER BY postit_date, postit_title ";
			  
	$myMessages = $wpdb->get_results($query);
	
	foreach ($myMessages as $message)
	{
 ?>
        <tr id='user-1' class="alternate">
			<td class="username column-username">
				<img src="<?php echo get_option("siteurl") . 
    			        '/wp-content/plugins/wordpress_postits/post-it-icon.jpg' ?>" border="0" align="middle">
				<?php echo $message->postit_title; ?>
		    </td>
		    <td class="username column-username">
				<?php echo $message->postit_date_expires; ?>
		    </td>
		   
		   <td class="username column-username">
				<a href="options-general.php?page=wppostits&wppostits_id_postit=<?php echo $message->id_postit; ?>">
				Edit Post-It</a>
		   </td>
		   <td class="username column-username">
				<a href="javascript:delete_message(<?php echo $message->id_postit ?>,'<?php echo $message->postit_title ?>');">
				Delete Post-It</a>
		   </td>		
		</tr>
        
        <?
    }

?>
	
    </tbody>
</table>
<?php 
	}

?>