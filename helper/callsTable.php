<?php
class Calls_Table extends WP_List_Table
{
	var $playerArr = array();
	function __construct()
	{
		global $status, $page;
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'call',     //singular name of the listed records
			'plural'    => 'calls',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );
	}
	function column_default($item, $column_name)
	{
		return stripslashes($item[$column_name]);
	}
	
	function column_s_time($item)
	{
		$s_time = date('j M, Y; h:i:s A', strtotime($item['s_time']));
		
		if($item['rec'] == 1)
		{
			$actions['inactivate'] = sprintf('<a href="?page=%s&p_id=%s%s&action=%s&call=%s">Move To Trash</a>',$_REQUEST['page'],$_REQUEST['p_id'],'&graph_date_from='.$_REQUEST['graph_date_from'].'&graph_date_to='.$_REQUEST['graph_date_to'],'inactivate',$item['c_id']);
		}
		else
		{
			$actions = array(
				'activate'      => sprintf('<a href="?page=%s&p_id=%s%s&action=%s&call=%s">Restore</a>',$_REQUEST['page'],$_REQUEST['p_id'],'&graph_date_from='.$_REQUEST['graph_date_from'].'&graph_date_to='.$_REQUEST['graph_date_to'],'activate',$item['c_id']),
				'delete'    => sprintf('<a href="?page=%s&p_id=%s%s&action=%s&call=%s">Delete</a>',$_REQUEST['page'],$_REQUEST['p_id'],'&graph_date_from='.$_REQUEST['graph_date_from'].'&graph_date_to='.$_REQUEST['graph_date_to'],'delete',$item['c_id']),
			);
		}
		//Return the title contents
		return sprintf('%1$s %2$s',
		/*$1%s*/ $s_time,
		/*$3%s*/ $this->row_actions($actions)
		);
	}
	
	function get_bulk_actions()
	{
		if($_GET['post_status'] == 'inactive')
		{
			$actions = array(
				'activate'    => 'Restore',
				'delete'    => 'Delete'
			);
		}
		else
		{
			$actions = array(
				'inactivate'	=> 'Move To Trash'
			);
		}
		return $actions;
	}
	
	function process_bulk_action()
	{
		global $wpdb;
		$phones = '';
		if( is_array($_GET['call']) && count($_GET['call']) > 0 )
		{
			$calls = implode(',', $_GET['call']);
		}
		elseif(is_numeric($_GET['call']) && $_GET['call'] > 0)
		{
			$calls = $_GET['call'];
		}
		
		$currentAction = trim($this->current_action());
		if(trim($calls) != '')
		{
			$calls = '(' . $calls . ')';
			if('delete' == $currentAction)
			{
				$wpdb->query( "DELETE FROM " . CALLS_TABLE . " WHERE c_id in " . $calls );
			}
			elseif('inactivate' == $currentAction)
			{
				$wpdb->query( "update " . CALLS_TABLE . " set rec = 0 WHERE c_id in " . $calls );
			}
			elseif('activate' == $currentAction)
			{
				$wpdb->query( "update " . CALLS_TABLE . " set rec = 1 WHERE c_id in " . $calls );
			}
		}
	}
	
	function column_cb($item)
	{
		return sprintf(
		'<input type="checkbox" name="%1$s[]" value="%2$s" />',
		/*$1%s*/ $this->_args['singular'], 
		/*$2%s*/ $item['c_id']
		);
	}
	
	function column_c_from($item)
	{
		$result = preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '+1 ($1) $2-$3', trim($item['c_from']));
		if(  $result )
		{
			return $result;
		}
		return $item['c_from'];
	}
	
	function column_duration($item)
	{
		$mins = str_pad(floor(($item['duration'] / 60)), 2, "0", STR_PAD_LEFT);
		$secs = str_pad(($item['duration'] % 60), 2, "0", STR_PAD_LEFT);
		return $mins . ':' . $secs;
	}
	
	function column_rec_file($item)
	{
		$playerCode = '<div id="jquery_jplayer_' . $item['c_id'] . '" class="jp-jplayer"></div><div id="jp_container_' . $item['c_id'] . '" class="jp-audio"><div class="jp-type-single"><div class="jp-gui jp-interface"><ul class="jp-controls"><li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li><li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li><li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li><li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li><li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li><li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li></ul><div class="jp-progress"><div class="jp-seek-bar"><div class="jp-play-bar"></div></div></div><div class="jp-volume-bar"><div class="jp-volume-bar-value"></div></div><div class="jp-current-time"></div><div class="jp-duration"></div><ul class="jp-toggles"><li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li><li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li></ul></div><div class="jp-no-solution"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div></div></div>';
		
		$playerCode = '<div id="jquery_jplayer_' . $item['c_id'] . '" class="jp-jplayer"></div><div id="jp_container_' . $item['c_id'] . '" class="jp-audio"><div class="jp-type-single"><div class="jp-gui jp-interface"><ul class="jp-controls"><li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li><li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li></ul></div><div class="jp-no-solution"><span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.</div></div></div>';
		if(strpos($item['rec_file'], '//') !== false)
		{
			$this->playerArr[] = array(
					'c_id'		=> $item['c_id'],
					'rec_file'	=> $item['rec_file']
				);
			
			return $playerCode;
		}
		elseif(strpos($item['rec_file'], 'wp-content') !== false)
		{
			$this->playerArr[] = array(
					'c_id'		=> $item['c_id'],
					'rec_file'	=> get_bloginfo('url') . $item['rec_file']
				);
			
			return $playerCode;
		}
		else
		{
			return 'N/A';
		}
	}
	/** ************************************************************************
	* REQUIRED! This method dictates the table's columns and titles. This should
	* return an array where the key is the column slug (and class) and the value 
	* is the column's title text. If you need a checkbox for bulk actions, refer
	* to the $columns array below.
	* 
	* The 'cb' column is treated differently than the rest. If including a checkbox
	* column in your table you must create a column_cb() method. If you don't need
	* bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	* 
	* @see WP_List_Table::::single_row_columns()
	* @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	**************************************************************************/
	function get_columns()
	{
		$columns = array(
			'cb'       => '<input type="checkbox" />',
			's_time'	=> 'Date Time',
			'c_from'	=> 'Caller',
			'duration'	=> 'Duration',
			'rec_file'	=> 'Recording'
		);
		return $columns;
	}
	
	function get_views()
	{
		global $wpdb;
		
		if(trim($_SESSION['calls']['graph_date_from']) != '' && trim($_SESSION['calls']['graph_date_to']) != '')
		{
			$graph_from = trim($_SESSION['calls']['graph_date_from']);
			$graph_to = trim($_SESSION['calls']['graph_date_to']);
		}
		else
		{
			$graph_from = date('Y-m-01');
			$graph_to = date('Y-m-d');
		}
		
		$query = "SELECT a.* FROM " . CALLS_TABLE . " as a where p_id='".$_REQUEST['p_id']."' and s_time >= '" . $graph_from . "' and s_time < '" . date ("Y-m-d", strtotime("+1 day", strtotime($graph_to))) . "'";
		$all = $wpdb->query($query . ' and rec = 1');
		$inactive = $wpdb->query($query . ' and rec = 0');
		
		${$_GET['post_status'] . 'cnt'} = ' class="current"';
		
		$toRet = array(
					'all' => sprintf('<a href="?page=%s&p_id=%s%s"' . $cnt. '>All <span class="count">(' . $all . ')</a>',$_REQUEST['page'],$_REQUEST['p_id'],'&graph_date_from='.$_REQUEST['graph_date_from'].'&graph_date_to='.$_REQUEST['graph_date_to']),
					'inactive' => sprintf('<a href="?page=%s&p_id=%s%s&post_status=inactive"' . $inactivecnt. '>Trash <span class="count">(' . $inactive . ')</a>',$_REQUEST['page'],$_REQUEST['p_id'],'&graph_date_from='.$_REQUEST['graph_date_from'].'&graph_date_to='.$_REQUEST['graph_date_to'])
				);
		
		return $toRet;
	}
	/** ************************************************************************
	* Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
	* you will need to register it here. This should return an array where the 
	* key is the column that needs to be sortable, and the value is db column to 
	* sort by. Often, the key and value will be the same, but this is not always
	* the case (as the value is a column name from the database, not the list table).
	* 
	* This method merely defines which columns should be sortable and makes them
	* clickable - it does not handle the actual sorting. You still need to detect
	* the ORDERBY and ORDER querystring variables within prepare_items() and sort
	* your data accordingly (usually by modifying your query).
	* 
	* @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	**************************************************************************/
	function get_sortable_columns()
	{
		$sortable_columns = array(
			's_time'     	=> array('s_time',false),     //true means its already sorted
			'duration'	=> array('duration',false)
		);
		return $sortable_columns;
	}
	/** ************************************************************************
	* REQUIRED! This is where you prepare your data for display. This method will
	* usually be used to query the database, sort and filter the data, and generally
	* get it ready to be displayed. At a minimum, we should set $this->items and
	* $this->set_pagination_args(), although the following properties and methods
	* are frequently interacted with here...
	* 
	* @uses $this->_column_headers
	* @uses $this->items
	* @uses $this->get_columns()
	* @uses $this->get_sortable_columns()
	* @uses $this->get_pagenum()
	* @uses $this->set_pagination_args()
	**************************************************************************/
	function prepare_items()
	{
		global $wpdb, $_wp_column_headers; $screen = get_current_screen();
		$phoneId =(int) $_GET['p_id'];
		if(trim($_SESSION['calls']['graph_date_from']) != '' && trim($_SESSION['calls']['graph_date_to']) != '')
		{
			$graph_from = trim($_SESSION['calls']['graph_date_from']);
			$graph_to = trim($_SESSION['calls']['graph_date_to']);
		}
		else
		{
			$graph_from = date('Y-m-01');
			$graph_to = date('Y-m-d');
		}
		/* -- Preparing your query -- */
		$query = "SELECT * FROM " . CALLS_TABLE . " where p_id = '" . $phoneId . "' and s_time >= '" . $graph_from . "' and s_time < '" . date ("Y-m-d", strtotime("+1 day", strtotime($graph_to))) . "'";
		if($_GET['post_status'] == 'inactive')
		{
			$query.=' and rec != 1';
		}
		else
		{
			$query.=' and rec = 1';
		}
		/* -- Ordering parameters -- */ 
		//Parameters that are going to be used to order the result 
		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 's_time'; 
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'desc';
		if(!empty($orderby) & !empty($order))
		{
			$query.=' ORDER BY ' . $orderby . ' ' . $order; 
		}  
		/* -- Pagination parameters -- */ 
		$per_page = 20;
		//Number of elements in your table? 
		$totalitems = $wpdb->query($query); //return the total number of affected rows
		$totalpages = ceil($totalitems / $per_page);
		
		if(!empty($paged) && !empty($perpage))
		{
			$offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; 
		}
		/* -- Register the pagination -- */ 
		$this->set_pagination_args( array( "total_items" => $totalitems, "total_pages" => $totalpages, "per_page" => $perpage, ) ); 
		/**
		* REQUIRED. Now we need to define our column headers. This includes a complete
		* array of columns to be displayed (slugs & titles), a list of columns
		* to keep hidden, and a list of columns that are sortable. Each of these
		* can be defined in another method (as we've done here) before being
		* used to build the value for our _column_headers property.
		*/
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		/**
		* REQUIRED. Finally, we build an array to be used by the class for column 
		* headers. The $this->_column_headers property takes an array which contains
		* 3 other arrays. One for all columns, one for hidden columns, and one
		* for sortable columns.
		*/
		$this->_column_headers = array($columns, $hidden, $sortable);
		/**
		* Optional. You can handle your bulk actions however you see fit. In this
		* case, we'll handle them within our package just to keep things clean.
		*/
		$data = $wpdb->get_results($query, ARRAY_A);
		/***********************************************************************
		* ---------------------------------------------------------------------
		* vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		* 
		* In a real-world situation, this is where you would place your query.
		* 
		* ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		* ---------------------------------------------------------------------
		**********************************************************************/
		/**
		* REQUIRED for pagination. Let's figure out what page the user is currently 
		* looking at. We'll need this later, so you should always include it in 
		* your own package classes.
		*/
		$current_page = $this->get_pagenum();
		/**
		* REQUIRED for pagination. Let's check how many items are in our data array. 
		* In real-world use, this would be the total number of items in your database, 
		* without filtering. We'll need this later, so you should always include it 
		* in your own package classes.
		*/
		$total_items = count($data);
		/**
		* The WP_List_Table class does not handle pagination for us, so we need
		* to ensure that the data is trimmed to only the current page. We can use
		* array_slice() to 
		*/
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		/**
		* REQUIRED. Now we can add our *sorted* data to the items property, where 
		* it can be used by the rest of the class.
		*/
		$this->items = $data;
		/**
		* REQUIRED. We also have to register our pagination options & calculations.
		*/
		$this->set_pagination_args( array(
		'total_items' => $total_items,                  //WE have to calculate the total number of items
		'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
		'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}	
}