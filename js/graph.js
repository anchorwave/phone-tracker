	jQuery(document).ready(function () {
		
		var choiceContainer = jQuery("#choices");
		jQuery.each(datasets, function(key, val) {
			choiceContainer.append(" &nbsp; |  &nbsp; <label style='color:" + val.color + "' for='id" + key + "'><input type='checkbox' name='" + key +
				"' checked='checked' id='id" + key + "'></input> " + val.label + "</label>");
		});

		choiceContainer.find("input").click(plotAccordingToChoices);
		
		function plotAccordingToChoices(){
			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = jQuery(this).attr("name");
				if (key && datasets[key]) {
					data.push(datasets[key]);
				}
			});
			jQuery.plot(jQuery('#graph-lines'), data,{
				series: {
					points: {
						show: true,
						radius: 5
					},
					lines: {
						show: true
					},
					shadowSize: 0
				},
				grid: {
					color: '#646464',
					borderColor: '#888888',
					borderWidth: 1,
					hoverable: true
				},
				xaxis: {
					tickColor: 'transparent',
					ticks: xaxisTicks
				},
				yaxis: {
					tickSize: yTickSize
				},
				selection: {
					mode: "x"
				}
			});
			 
			// Bars
			jQuery.plot(jQuery('#graph-bars'), data, {
				series: {
					bars: {
						show: true,
						barWidth: .9,
						align: 'center'
					},
					shadowSize: 0
				},
				grid: {
					color: '#646464',
					borderColor: '#888888',
					borderWidth: 1,
					hoverable: true
				},
				xaxis: {
					tickColor: 'transparent',
					ticks: xaxisTicks
				},
				yaxis: {
					tickSize: yTickSize
				},
				selection: {
					mode: "x"
				}
			});
		}
		
		plotAccordingToChoices();
		
		var yaxisLabel = jQuery("<div class='axisLabel yaxisLabel'></div>")
		.text("Number of Calls")
		.appendTo(jQuery('.graph-container'));
		yaxisLabel.css("margin-top", yaxisLabel.width() / 2 - 20);
		
		jQuery('#graph-bars').hide();
	 
		jQuery('#lines').on('click', function (e) {
			jQuery('#bars').removeClass('active');
			jQuery('#graph-bars').fadeOut();
			jQuery(this).addClass('active');
			jQuery('#graph-lines').fadeIn();
			e.preventDefault();
		});
		 
		jQuery('#bars').on('click', function (e) {
			jQuery('#lines').removeClass('active');
			jQuery('#graph-lines').fadeOut();
			jQuery(this).addClass('active');
			jQuery('#graph-bars').fadeIn().removeClass('hidden');
			e.preventDefault();
		});
		
		function showTooltip(x, y, contents) {
			jQuery('<div id="tooltip">' + contents + '</div>').css({
				top: y - 16,
				left: x + 20
			}).appendTo('body').fadeIn();
		}
		 
		var previousPoint = null;
		var previousIdx = null;
		 
		jQuery('#graph-lines, #graph-bars').bind('plothover', function (event, pos, item) {
			if (item) {
				if (previousPoint != item.dataIndex || previousIdx != item.seriesIndex) {
					previousPoint = item.dataIndex;
					previousIdx = item.seriesIndex;
					jQuery('#tooltip').remove();
					var x = item.datapoint[0],
						y = item.datapoint[1];
						showTooltip(item.pageX, item.pageY, xaxisArr[x] + ': ' + y + ' call(s)');
				}
			} else {
				jQuery('#tooltip').remove();
				previousPoint = null;
			}
		});
		
		jQuery( "#graph_date_from" ).datepicker({
		  dateFormat: 'yy-mm-dd',
		  defaultDate: "+1w",
		  maxDate: datePickMax,
		  changeMonth: true,
		  numberOfMonths: 2,
		  onClose: function( selectedDate ) {
			jQuery( "#graph_date_to" ).datepicker( "option", "minDate", selectedDate );
			jQuery( "#graph_date_to" ).focus();
		  }
		});
		jQuery( "#graph_date_to" ).datepicker({
		  dateFormat: 'yy-mm-dd',
		  defaultDate: "+1w",
		  maxDate: datePickMax,
		  changeMonth: true,
		  numberOfMonths: 2,
		  onClose: function( selectedDate ) {
			jQuery( "#graph_date_from" ).datepicker( "option", "maxDate", selectedDate );
			jQuery( "#dates-filter" ).submit();
		  }
		});
		jQuery( "#graph_date_to" ).datepicker( "option", "minDate", jQuery( "#graph_date_from" ).val() );
	});