<script type="text/javascript">
	function copyToClipboard (text) {
	  window.prompt ("Press Ctrl+C to copy", text);
	}
</script><div class="wrap" style="overflow:hidden;">
	<?php /*if(trim(TW_SID) == '' || trim(TW_ATOKEN) == ''){?>
		<div class="error"><p><strong>Please setup correct values on setting page. For this plugin to operate correctly.</strong></p></div>
	<?php }*/?>
	<div id="icon-users" class="icon32"><br></div> <h2><?php echo ucwords($data['phoneRow']['name'])?></h2>
	<p><strong>Twilio Number: <?php echo /*$data['phoneRow']['name'] . ' : ' .*/ $this->formatPhone($data['phoneRow']['phn_no'])?></strong></p>
	<p><strong>Destination Number: <?php echo $this->formatPhone($data['phoneRow']['dest_no'])?></strong></p><?php
	$twillo_url = get_bloginfo('url') . '/?tw_phone_call=' . $data['phoneRow']['p_id']?><p><strong>
<label class="description" for="twillo_url">Twilio Url: </label> &nbsp; <?php echo $twillo_url?> &nbsp; <a href="#" onclick="copyToClipboard('<?php echo $twillo_url?>');"><img style="vertical-align:top;" title="Copy" alt="Copy" src="<?php echo MY_BASE_URL?>images/copy.png" /></a></strong></p>
<p class="description"><strong>Instructions:</strong> To setup call tracking, copy the URL above into the 'Voice Request URL' field for this twilio phone number.</p>
<p><strong>Fallback URL: http://twimlets.com/forward?PhoneNumber=<?php echo $data['phoneRow']['dest_no']?>&Timeout=30</strong></p>
<p class="description"><strong>Instructions:</strong> Copy the URL above into the 'Voice Fallback URL' field incase the Request URL does not work at any given time.</p>
<p style="border:1px solid #888; width:auto; margin-right:10px; padding:10px;"><img src="<?php echo MY_BASE_URL . 'images/phone_grey.png'?>" style="vertical-align:middle" alt="" /> <strong style="color:#000;font-size: 16px;text-decoration:none;font-weight:bold;">Calls</strong> &nbsp; &nbsp; <a style="color:#464646;font-size: 16px;text-decoration:none;" href="?page=add-phone-number&action=edit&p_id=<?php echo $data['phoneRow']['p_id']?>"><img src="<?php echo MY_BASE_URL . 'images/edit.png'?>" style="vertical-align:bottom" alt="" /> Settings</a></p>
	<?php $this->wp_messages($data);?><br />
<!--<link media="all" type="text/css" href="http://code.jquery.com/ui/1.8.3/themes/smoothness/jquery-ui.css" rel="stylesheet" />-->
<link media="all" type="text/css" href="http://code.jquery.com/ui/1.8.24/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<link media="all" type="text/css" href="<?php echo MY_BASE_URL?>css/flot.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo MY_BASE_URL?>css/music.css" type="text/css" />
<!--<script src="<?php //echo MY_BASE_URL?>js/jquery-ui.js"></script>-->
<script src="//code.jquery.com/ui/1.8.24/jquery-ui.js"></script>
<script src="<?php echo MY_BASE_URL?>js/flot/jquery.flot.min.js"></script>
<script src="<?php echo MY_BASE_URL?>js/jquery.jplayer.min.js"></script>
<script type="text/javascript">
	var datasets = {'calls' : {
				label: "Calls",
				data: [<?php
				$graphArr = array();
				$graphUniqueArr = array();
				$xaxisArr = array();
				$xaxisDateArr = array();
				$date = $data['graph_from'];
				$end_date = $data['graph_to'];
				$maxVal = 0;
				$i = 0;
				while (strtotime($date) <= strtotime($end_date) && strtotime($date) <= strtotime(date('Y-m-d')))
				{
					$i++;
					$val =(int) $data['callsByDayArr'][$date];
					$valUnique =(int) $data['callsByDayUniqueArr'][$date];
					$graphArr[] = '[' . $i . ', ' . $val . ']';
					$graphUniqueArr[] = '[' . $i . ', ' . $valUnique . ']';
					$xaxisArr[] = '[' . $i . ', "' . date ("j M", strtotime($date)) . '"]';
					$xaxisDateArr[] = $i . ': "' . date ("j M", strtotime($date)) . '"';
					$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
					
					if($val > $maxVal)
					{
						$maxVal = $val;
					}
				}
				$yTickSize = ceil(($maxVal / 10));
				if($yTickSize == 0)
					$yTickSize = 1;

				echo implode(', ', $graphArr);
				
				$xaxisTicks = implode(', ', $xaxisArr);
				?> ],
				color: '#71c73e',
				points: { radius: 4, fillColor: '#71c73e' }
			},
			'unique' : {
				label: "Unique Calls",
				data: [<?php echo implode(', ', $graphUniqueArr);?> ],
				color: '#2583AE',
				points: { radius: 4, fillColor: '#2583AE' }
			}
		};
	var xaxisArr = {<?php echo implode(', ', $xaxisDateArr);?>};
	var yTickSize = <?php echo $yTickSize;?>;
	var xaxisTicks = [<?php echo $xaxisTicks?>];
	var datePickMax = '<?php echo date('Y-m-d')?>';
</script>
<script src="<?php echo MY_BASE_URL?>js/graph.js"></script>
<div id="graph-wrapper">
	<div class="graph-info" id="choices">
		<a href="#" id="lines" class="active"><span></span></a> <a href="#" id="bars"><span></span></a> &nbsp; &nbsp; 
		<form id="dates-filter" action="" method="get">
			<input type="hidden" name="page" value="tw-calls" />
			<input type="hidden" name="p_id" value="<?php echo $data['phoneRow']['p_id']?>" />
			<p class="search-box">
			<label for="graph_date_from">Date From:</label>
			<input type="text" readonly="" id="graph_date_from" name="graph_date_from" value="<?php echo $data['graph_from']?>" /> -
			<label for="graph_date_from">To Date:</label>
			<input type="text" readonly="" id="graph_date_to" name="graph_date_to" value="<?php echo $data['graph_to']?>" />
			<input type="submit" name="" id="search-submit" class="button" value="Apply" /> &nbsp; &nbsp; &nbsp; &nbsp; </p>
		</form>
	</div>
	<div class="graph-container">
		<div id="graph-lines"></div>
		<div id="graph-bars"></div>
	</div>
	<center><b>Days</b></center>
</div>
<div class="calls_div"><?php /*?>
	<div class="calls_div_inn">
		<img alt="" src="<?php echo MY_BASE_URL . 'images/phone.png'?>" />
		<h5>Total Calls</h5>
		<h4><?php echo $data['totalCalls']?></h4>
	</div>
	<div class="calls_div_inn">
		<img alt="" src="<?php echo MY_BASE_URL . 'images/package.png'?>" />
		<h5>Total Unique Calls</h5>
		<h4><?php echo $data['totalUnqCalls']?></h4>
	</div>
	<?php */?><div class="calls_div_inn">
		<?php /*?><img alt="" src="<?php echo MY_BASE_URL . 'images/calendar.png'?>" />
		<div class="time"><span class="year"><?php echo date('Y')?></span> <span class="month"><?php echo date('M')?></span></div><?php */?>
		<img alt="" src="<?php echo MY_BASE_URL . 'images/cphone.png'?>" />
		<h5>Total Calls</h5>
		<h4><?php echo $data['totalCallsMonth']?></h4>
	</div>
	<div class="calls_div_inn">
		<img alt="" src="<?php echo MY_BASE_URL . 'images/group.png'?>" style="padding-bottom:10px;" />
		<h5>Total Unique Calls</h5>
		<h4><?php echo $data['totalUnqCallsMonth']?></h4>
	</div>
</div>
<div class="wrap"><p>&nbsp;</p>
	<?php $data['callsTable']->views() ?>
	<form id="calls-filter" action="" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<input type="hidden" name="p_id" value="<?php echo $_REQUEST['p_id'] ?>" />
		<input type="hidden" name="graph_date_from" value="<?php echo $_REQUEST['graph_date_from'] ?>" />
		<input type="hidden" name="graph_date_to" value="<?php echo $_REQUEST['graph_date_to'] ?>" />
		<?php $data['callsTable']->display() ?>
	</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){<?php
foreach($data['callsTable']->playerArr as $cur){?>
    jQuery("#jquery_jplayer_<?php echo $cur['c_id']?>").jPlayer({
        ready: function(event) {
            jQuery(this).jPlayer("setMedia",{
                wav: "<?php echo $cur['rec_file']?>"
            });
        },
		play: function(){
			jQuery(this).jPlayer("pauseOthers");
		},
        swfPath: "<?php echo MY_BASE_URL?>js/",
		supplied: "wav",
		cssSelectorAncestor: "#jp_container_<?php echo $cur['c_id']?>",
		wmode: "window",
		smoothPlayBar: true,
		keyEnabled: true
    });<?php
}?>
});
</script></div>