<!--<link media="all" type="text/css" href="http://code.jquery.com/ui/1.8.3/themes/smoothness/jquery-ui.css" rel="stylesheet" />-->
<link media="all" type="text/css" href="http://code.jquery.com/ui/1.8.24/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<link media="all" type="text/css" href="<?php echo MY_BASE_URL?>css/flot.css" rel="stylesheet" />
<!--<script src="<?php //echo MY_BASE_URL?>js/jquery-ui.js"></script>-->
<script src="//code.jquery.com/ui/1.8.24/jquery-ui.js"></script>
<script src="<?php echo MY_BASE_URL?>js/flot/jquery.flot.min.js"></script>

<script type="text/javascript">
<?php
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
	
	foreach($data['phoneNo'] as $indx => $values)
	{
		$val =(int) $data['callsByDayArr'][$indx][$date];
		$graphArr[$indx][] = '[' . $i . ', ' . $val . ']';
	}
	
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

$xaxisTicks = implode(', ', $xaxisArr);
?>
	var datasets = {<?php
			$allDataSet = array();
			foreach($data['phoneNo'] as $indx => $values)
			{
				$randCol = $this->rand_color();
				
				$allDataSet[] = '"' . $indx . $values . '" : {
					label: "' . $this->formatPhone($values) . '",
					data: [' . implode(', ', $graphArr[$indx]) . '],
					color: "' . $randCol . '",
					points: { radius: 4, fillColor: "' . $randCol . '" }
				}';
			}
			echo implode(', ', $allDataSet);
			?>};
	var xaxisArr = {<?php echo implode(', ', $xaxisDateArr);?>};
	var yTickSize = <?php echo $yTickSize;?>;
	var xaxisTicks = [<?php echo $xaxisTicks?>];
	var datePickMax = '<?php echo date('Y-m-d')?>';
</script>
<script src="<?php echo MY_BASE_URL?>js/graph.js"></script><div class="wrap" style="overflow:hidden;">
	<div id="icon-index" class="icon32"><br/></div>
	<h2>Phone Numbers <a class="add-new-h2" href="?page=add-phone-number">Add New</a></h2>
	<?php $this->wp_messages($data);?><p> </p>
	<div id="graph-wrapper">
		<div class="graph-info" id="choices">
			<a href="#" id="lines" class="active"><span></span></a> <a href="#" id="bars"><span></span></a> &nbsp; &nbsp; 
			<form id="dates-filter" action="" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page']?>" />
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
	<?php $data['phonesTable']->views() ?>
	<form id="phones-filter" action="" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $data['phonesTable']->display() ?>
	</form>
</div>
