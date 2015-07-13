 <script type="text/javascript">
	<?php
		if($data['wp_msg'] == 'New Phone Number Added')
		{?>
			window.location.href="<?php echo get_bloginfo('url')?>/wp-admin/admin.php?page=tw-calls&p_id=<?php echo $data['p_id']?>";
		<?php
		}
	?>
	function checkVals(frm)
	{
		if(frm.name.value == '')
		{
			alert('Please enter Name.');
			frm.name.focus();
			return false;
		}
		else if(frm.phn_no.value == '')
		{
			alert('Please enter Twillo Number.');
			frm.phn_no.focus();
			return false;
		}
		else if(frm.dest_no.value == '')
		{
			alert('Please enter Destination Number.');
			frm.dest_no.focus();
			return false;
		}
	}
	function copyToClipboard (text) {
	  window.prompt ("Press Ctrl+C to copy", text);
	}
</script><div id="form_container" class="wrap">
<p><a href="?page=tw-phone-tracker">Back to Phone Numbers</a></p><br><?php $this->wp_messages($data);?>
<?php
if($data['p_id'] > 0)
{?><div id="icon-options-general" class="icon32"><br></div><h2>Phone Number Settings</h2><p><strong><?php echo $data['name'] . ' : ' . $data['phn_no']?></strong></p>
<p style="border:1px solid #888;width:auto;margin-right: 10px;padding:10px;"><a style="color:#464646;font-size: 16px;text-decoration:none;" href="?page=tw-calls&p_id=<?php echo $data['p_id']?>"><img src="<?php echo MY_BASE_URL . 'images/phone_grey.png'?>" style="vertical-align:middle" alt="" /> Calls</a> &nbsp; &nbsp; <img src="<?php echo MY_BASE_URL . 'images/edit.png'?>" style="vertical-align:bottom" alt="" /> <strong style="color:#000;font-size: 16px;text-decoration:none;font-weight:bold;"> Settings</strong></p><?php
}else{
?><div id="icon-options-general" class="icon32"><br></div><h2>Add New Phone Number</h2><p>Use the form below to setup Twillo number. To obtain a new Twillo phone number to use, login to your <a target="_blank" href="https://www.twilio.com/user/account">Twillo Dashboard</a> and purchase a new number.</p><?php
}?>
<form class="appfreedom"  method="post" action="" onsubmit="return checkVals(this);">
<div class="form_description">
</div>						
<table class="form-table">
<tbody><tr valign="top">
<th scope="row"><label class="description" for="name">Name: </label></th>
<td><input id="name" name="name" class="element text medium regular-text" type="text" maxlength="200" size="60" value="<?php echo trim($data['name']) ?>"/></td>
</tr><tr valign="top">
<th scope="row"><label class="description" for="name">Twillo Number: </label></th>
<td><?php if($data['p_id'] > 0){
	echo '<span style="color:#000;font-weight:bold;font-size:14px">' . $this->formatPhone($data['phn_no']) . '</span>';?><input name="phn_no" type="hidden" value="<?php echo trim($data['phn_no']) ?>"/><?php
}else{?>
<input id="phn_no" name="phn_no" class="element text medium regular-text" type="text" maxlength="200" size="60" value="<?php echo trim($data['phn_no']) ?>"/> <p class="description">No spaces or punctuations</p><?php
}?></td>
</tr><tr valign="top">
<th scope="row"><label class="description" for="dest_no">Destination Number: </label></th>
<td>
<input id="dest_no" name="dest_no" class="element text medium regular-text" type="text" maxlength="200" size="60" value="<?php echo trim($data['dest_no']) ?>"/> <p class="description">No spaces or punctuations</p></td>
</tr><tr valign="top">
<th scope="row"><span>Recording</span></th>
<td><input id="rec_status" name="rec_status" class="element checkbox" <?php if($data['rec_status'] != 0){ echo 'checked="checked"'; }?> type="checkbox" value="1"  /> &nbsp; <label class="description" for="rec_status">Enable Recording </label></td>
</tr>
<?php if($data['p_id'] > 0){
$twillo_url = get_bloginfo('url') . '/?tw_phone_call=' . $data['p_id']?><tr valign="top">
<th scope="row"><label class="description" for="twillo_url">Twillo Url: </label></th>
<td><?php echo $twillo_url?> <a href="#" onclick="copyToClipboard('<?php echo $twillo_url?>');"><img title="Copy" style="vertical-align:top;" alt="Copy" src="<?php echo MY_BASE_URL?>images/copy.png" /></a>
<p class="description"><strong>Instruction:</strong> To setup call tracking, copy the URL above into the 'Voice Request URL' field for this twilio phone number.</p></td>
</tr>
<tr valign="top">
<th scope="row"><span>Fallback URL</span></th>
<td><p>http://twimlets.com/forward?PhoneNumber=<?php echo trim($data['dest_no']) ?>&Timeout=30</p>
<p class="description"><strong>Instructions:</strong> Copy the URL above into the 'Voice Fallback URL' field incase the Request URL does not work at any given time.</p> </td>
</tr>
<?php
}?></tbody></table>
<input type="hidden" name="p_id" id="p_id" value="<?php echo $data['p_id'] ?>" />
<p class="submit"><input type="submit" name="submit" id="saveForm" class="button button-primary" value="<?php echo ($data['p_id'] > 0)?'Update Settings':'Save'?>"></p>
<input type="hidden" name="action" value="new_record" />
<?php wp_nonce_field( 'new-phone' ); ?>
</form>	
</div>