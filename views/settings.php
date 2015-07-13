<script type="text/javascript">
	function checkVals(frm)
	{
		if(frm.tw_sid.value == '')
		{
			alert('Please enter Account SID.');
			frm.tw_sid.focus();
			return false;
		}
		else if(frm.tw_atoken.value == '')
		{
			alert('Please enter Auth Token.');
			frm.tw_atoken.focus();
			return false;
		}
	}
</script>
<h1>Settings</h1>
<p><strong>Using this plugin requires an account with twillo. Creat the account and then enter the your account SID and Auth Token found in your <a target="_blank" href="https://www.twilio.com/user/account">Twillo Dashboard</a></strong></p>
<p></p>
<?php $this->wp_messages($data);?>
<form action="" method="post" onsubmit="return checkVals(this);">
<p><label for="tw_sid">Account SID:</label> <input type="text" id="tw_sid" name="tw_sid" size="60" value="<?php echo $data['tw_sid']?>" /></p>
<p><label for="tw_atoken">Auth Token:&nbsp;</label> <input type="text" id="tw_atoken" name="tw_atoken" size="60" value="<?php echo $data['tw_atoken']?>" /></p>
<p><input type="submit" name="tw_submit" value="Update Settings" /></p>
</form>