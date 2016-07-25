<?php
class tw_controller
{
	function view($view = '', $data = array())
	{
		$viewPath = MY_BASE . 'views/' . $view . '.php';
		if(file_exists($viewPath))
		{
			include_once($viewPath);
		}
		else
		{
			echo '<h1 style="color:#FF0000">Wrong parameters, please try again.<h1>';
		}
	}
	
	function loadApi($sid, $token)
	{
		require_once(MY_BASE . 'api/Twilio.php');
		$client = new Services_Twilio($sid, $token);
		return $client;
	}
	
	function wp_messages($data)
	{
		$wp_error = isset( $data['wp_error'] ) ? trim( $data['wp_error'] ) : "";
		$wp_msg = isset( $data['wp_msg'] ) ? trim( $data['wp_msg'] ) : "";
		if( $wp_error != ''){
			echo '<div id="message" class="error"><p><strong>' . $wp_error . '</strong></p></div>';
		}
		if( $wp_msg != ''){
			echo '<div id="message" class="updated fade"><p><strong>' .  $wp_msg  . '</strong></p></div>';
		}
	}
	
	function formatPhone($item)
	{
		$result = preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '+1 ($1) $2-$3', trim($item));
		if(  $result )
		{
			return $result;
		}
		return $item;
	}
	
	function settings()
	{
		if (!current_user_can('manage_options'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		$data = array();
		if($_POST['tw_submit'] == 'Update Settings')
		{
			if(trim($_POST['tw_sid']) == '' || $_POST['tw_atoken'] == '')
			{
				$data['wp_error'] = 'Account SID and Auth Token are required and must not left empty.';
			}
			else
			{
				$accounts = array();
				
				$sid = trim($_POST['tw_sid']);
				$token = trim($_POST['tw_atoken']);
				$data['tw_sid'] = $sid;
				$data['tw_atoken'] = $token;
				
				try
				{
					$client = $this->loadApi($sid, $token);
					
					foreach($client->accounts->getIterator(0, 50, array("Status" => "active")) as $account)
					{
						if(trim($account->friendly_name) != '')
						{
							$accounts[] = $account->friendly_name;
						}
					}
					
					if(count($accounts) > 0)
					{
						$data['wp_msg'] = 'Please check, if following are the account active with your profile then you have enter correct credentials:<br />' . implode('<br />', $accounts);
						update_option('tw_sid', $sid);
						update_option('tw_atoken', $token);
					}
					else
					{
						$data['wp_error'] = 'Please check again your Account SID and Auth Token both, as we are not able to verfy them.';
					}
				}
				catch(Exception $e)
				{
					$data['wp_error'] = 'Twillo Error: ' . $e->getMessage() . "\n Please try again later.";
				}
			}
		}
		else
		{
			$data['tw_sid'] = get_option('tw_sid');
			$data['tw_atoken'] = get_option('tw_atoken');
		}
		$this->view('settings', $data);
	}
	
	function rand_color()
	{
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}
	
	function phone_numbers()
	{
		global $wpdb;
		$data = array();
		if (!current_user_can('manage_options'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		/* graph data start */
		if(trim($_GET['graph_date_from']) != '' && trim($_GET['graph_date_to']) != '')
		{
			$_SESSION['calls']['graph_date_from'] = trim($_GET['graph_date_from']);
			$_SESSION['calls']['graph_date_to'] = trim($_GET['graph_date_to']);
		}
		
		if(trim($_SESSION['calls']['graph_date_from']) != '' && trim($_SESSION['calls']['graph_date_to']) != '')
		{
			$data['graph_from'] = trim($_SESSION['calls']['graph_date_from']);
			$data['graph_to'] = trim($_SESSION['calls']['graph_date_to']);
		}
		else
		{
			$data['graph_from'] = date('Y-m-01');
			$data['graph_to'] = date('Y-m-d');
		}
		
		/* graph data end */
		
		require_once(MY_BASE . 'helper/phonesTable.php');
		
		//Create an instance of our package class...
		$phonesTable = new Phones_Table();
		//Fetch, prepare, sort, and filter our data...
		$curGridData = $phonesTable->prepare_items();
		
		$callsByDayArr = array();
		$phoneNo = array();
		
		if(is_array($curGridData) && count($curGridData) > 0)
		{
			$p_ids = array();
			foreach($curGridData as $cur)
			{
				$phoneNo[$cur['p_id']] = $cur['phn_no'];
				$p_ids[] = $cur['p_id'];
			}
			
			$callsByDay = $wpdb->get_results("SELECT cll.*,phn.phn_no FROM " . CALLS_TABLE . " as cll join " . PHONES_TABLE . " as phn on cll.p_id = phn.p_id where phn.status = 1 and cll.s_time >= '" . $data['graph_from'] . "' and cll.s_time < '" . date ("Y-m-d", strtotime("+1 day", strtotime($data['graph_to']))) . "' and cll.rec = 1 and cll.p_id in (" . implode(',', $p_ids) . ")", ARRAY_A);
			foreach($callsByDay as $cur)
			{
				$curDate = date('Y-m-d', strtotime($cur['s_time']));
				$tmpVal =(int) $callsByDayArr[$cur['p_id']][$curDate];
				$callsByDayArr[$cur['p_id']][$curDate] = ($tmpVal + 1);
			}
		}
		$data['callsByDayArr'] = $callsByDayArr;
		$data['phoneNo'] = $phoneNo;
		
		$data['phonesTable'] = $phonesTable;
		$this->view('phone_numbers', $data);
	}
	
	function add_phone()
	{
		global $wpdb;
		$data = array();
		if (!current_user_can('manage_options'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		if( isset( $_GET["p_id"] ))
		{
			$p_id = trim($_GET["p_id"]);
			$p_id = htmlspecialchars($p_id);
			if( !is_numeric($p_id) )
			$p_id = 0;
		}
		else   
			$p_id = 0;
		
		if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "new_record")
		{
			$data = $_POST;
			$rec_status = (isset($_POST['rec_status']) && $_POST['rec_status'] == 1)?1:0;
			
			$phn_no = preg_replace('/\D/', '', $_POST['phn_no'] );
			$dest_no = preg_replace('/\D/', '', $_POST['dest_no'] );

			$chkNoExists = $wpdb->query("SELECT p_id FROM " . PHONES_TABLE . " where phn_no = '" . $phn_no . "' and p_id <> '" . trim($_POST['p_id']) . "'");
			


			if( trim($_POST['name']) == '' || $phn_no == '' || $dest_no == ''){
				$tw_no = '';
				if(trim($_POST['p_id']) == '')
				{
					$tw_no = ', Twillo Number';
				}
				$data['wp_error'] = "Please enter value for Name" . $tw_no . " and Destination Number.";
			}
			elseif($chkNoExists > 0){
				$data['wp_error'] = $phn_no . " Twillo number already exists.";
			}
			else
			{
				if (isset ($_POST['p_id'])) 
					$id =  $_POST['p_id'];
				else			
					$id = 0;
				
				$name = trim( $_POST['name'] );
				
				
				$table = PHONES_TABLE;
				// add value to new record array
				$new_record = array(
					'name'			=>   $name,
					'phn_no'		=>   $phn_no,
					'dest_no'		=>   $dest_no,
					'rec_status'	=>   $rec_status,
					'status'		=>   1
				); 
				//save the post
				if($id == 0)
				{
					$new_record['l_fetch_time'] = date('Y-m-d H:i:s');
					$new_record['created'] = date('Y-m-d H:i:s');
					$wpdb->insert($table, $new_record);
					$data['p_id'] = $wpdb->insert_id;
					$data['wp_msg'] = "New Phone Number Added";
				}
				else 
				{
					//if we have an ID, update
					$where = array( 'p_id' => $id );
					$p_id = $id;
					$wpdb->update( $table, $new_record, $where );
					$data['wp_msg'] = "Settings Updated";
				}
			}
		}
		if( $p_id > 0)
		{
			$sql = "SELECT * FROM " . PHONES_TABLE . " where p_id = " . $p_id;
			try
			{
				$tmpMsg = $data['wp_msg'];
				$tmpError = $data['wp_error'];
				$data = $wpdb->get_row( $sql, ARRAY_A );
				$data['wp_msg'] = $tmpMsg;
				$data['wp_error'] = $tmpError;
			}
			catch(PDOException $e)
			{
				$data['wp_error'] = 'No record found';
			}
		}
		$this->view('add_phone', $data);
	}
	
	function called($p_id)
	{
		global $wpdb;
		header("Content-Type:text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?><Response>';
		if($_REQUEST['AccountSid'] == get_option('tw_sid'))
		{
			$callId = trim($_REQUEST['CallSid']);
			$direction = trim($_REQUEST['Direction']);
			$from = trim($_REQUEST['Caller']);
			$to = trim($_REQUEST['Called']);
			if($callId != '')
			{
				$phn_sql = "SELECT * FROM " . PHONES_TABLE . " where p_id = '" . $p_id . "' and status = 1";
				$phoneRow = $wpdb->get_row( $phn_sql, ARRAY_A );
				if(is_array($phoneRow) && $phoneRow['p_id'] > 0)
				{
					$call_sql = "SELECT * FROM " . CALLS_TABLE . " where p_id = '" . $phoneRow['p_id'] . "' and call_sid = '" . $callId . "'";
					$callRow = $wpdb->get_row( $call_sql, ARRAY_A );
					if(is_array($callRow) && $callRow['c_id'] > 0)
					{
						echo '<Say>Possible multiple notifications for same call, this call is already been notified.</Say>';
					}
					else
					{
						if(strpos($from, 'client:') !== false)
						{
							$from = str_replace('client:', '', $from);
						}
						$new_record = array(
								'p_id'		=> $phoneRow['p_id'],
								'c_from'	=> $from,
								's_time'	=> date('Y-m-d H:i:s'),
								'rec'		=> 1,
								'call_sid'	=> $callId
							);
						$wpdb->insert(CALLS_TABLE, $new_record);
						$cid = $wpdb->insert_id;
						$recUrl = get_bloginfo('url') . '/?tw_phone_record=' . $cid;
						
						echo '<Dial action="' . $recUrl . '"';
						if($phoneRow['rec_status'] == '1')
						{
							echo ' record="true"';
						}
						
						echo '><Number>' . $phoneRow['dest_no'] . '</Number></Dial>';
					}
				}
				else
				{
					echo '<Say>Phone number to be called not verified or currently inactive.</Say>';
				}
			}
			else
			{
				echo '<Say>Not an inbound call.</Say>';
			}
		}
		else
		{
			echo '<Say>Wrong SID.</Say>';
		}
		echo '</Response>';
	}
	
	function recording($callId)
	{
		global $wpdb;
		if(is_numeric($callId) && $callId > 0)
		{
			$call_sql = "SELECT * FROM " . CALLS_TABLE . " where c_id = '" . $callId . "'";
			$callRow = $wpdb->get_row( $call_sql, ARRAY_A );
			if(is_array($callRow) && $callRow['c_id'] > 0)
			{
				if(trim($_POST['DialCallDuration']) != '' || trim($_POST['RecordingUrl']) != '')
				{
					$new_record = array();
					if(trim($_POST['DialCallDuration']) != '')
					{
						$new_record['duration'] = $_POST['DialCallDuration'];
					}
					if(trim($_POST['RecordingUrl']) != '')
					{
						$new_record['rec_file'] = $_POST['RecordingUrl'];
						
						$uploadDir = wp_upload_dir();
						if(copy($_POST['RecordingUrl'], $uploadDir['path']. '/recording_' . $callRow['c_id'] . '.wav'))
						{
							$new_record['rec_file'] = $uploadDir['url']. '/recording_' . $callRow['c_id'] . '.wav';
						}
					}
					$where = array( 'c_id' => $callRow['c_id'] );
					$wpdb->update( CALLS_TABLE, $new_record, $where );
				}
				echo 'Valid data is saved.';
			}
			else
			{
				echo 'This call record does not found in our database.';
			}
		}
		else
		{
			echo 'This call record does not found in our database.';
		}
	}
	
	function calls()
	{
		global $wpdb;
		$data = array();
		if (!current_user_can('manage_options'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		if(trim($_GET['graph_date_from']) != '' && trim($_GET['graph_date_to']) != '')
		{
			$_SESSION['calls']['graph_date_from'] = trim($_GET['graph_date_from']);
			$_SESSION['calls']['graph_date_to'] = trim($_GET['graph_date_to']);
		}
		
		if(trim($_SESSION['calls']['graph_date_from']) != '' && trim($_SESSION['calls']['graph_date_to']) != '')
		{
			$data['graph_from'] = trim($_SESSION['calls']['graph_date_from']);
			$data['graph_to'] = trim($_SESSION['calls']['graph_date_to']);
		}
		else
		{
			$data['graph_from'] = date('Y-m-01');
			$data['graph_to'] = date('Y-m-d');
		}
		$phoneId =(int) $_GET['p_id'];
		if(is_numeric($phoneId) && $phoneId > 0)
		{
			require_once(MY_BASE . 'helper/callsTable.php');
			//Create an instance of our package class...
			$callsTable = new Calls_Table();
			
			$callsTable->process_bulk_action();
			
			$phone_sql = "SELECT * FROM " . PHONES_TABLE . " where p_id = '" . $phoneId . "'";
			$phoneRow = $wpdb->get_row( $phone_sql, ARRAY_A );
			$data['phoneRow'] = $phoneRow;
			
			//$data['totalCalls'] = $wpdb->query("SELECT c_id FROM " . CALLS_TABLE . " where p_id = '" . $phoneId . "'");
			//$data['totalUnqCalls'] = $wpdb->query("SELECT c_id FROM " . CALLS_TABLE . " where p_id = '" . $phoneId . "' group by c_from");
			
			$data['totalCallsMonth'] = $wpdb->query("SELECT c_id FROM " . CALLS_TABLE . " where p_id = '" . $phoneId . "' and s_time >= '" . $data['graph_from'] . "' and s_time < '" . date ("Y-m-d", strtotime("+1 day", strtotime($data['graph_to']))) . "' and rec = 1");
			$data['totalUnqCallsMonth'] = $wpdb->query("SELECT c_id FROM " . CALLS_TABLE . " where p_id = '" . $phoneId . "' and s_time >= '" . $data['graph_from'] . "' and s_time < '" . date ("Y-m-d", strtotime("+1 day", strtotime($data['graph_to']))) . "' and rec = 1 group by c_from");
			
			$callsByDayUniqTck = array();
			$callsByDayArr = array();
			$callsByDayUniqueArr = array();
			$callsByDay = $wpdb->get_results("SELECT * FROM " . CALLS_TABLE . " where p_id = '" . $phoneId . "' and s_time >= '" . $data['graph_from'] . "' and s_time < '" . date ("Y-m-d", strtotime("+1 day", strtotime($data['graph_to']))) . "' and rec = 1", ARRAY_A);
			foreach($callsByDay as $cur)
			{
				$curDate = date('Y-m-d', strtotime($cur['s_time']));
				$tmpVal =(int) $callsByDayArr[$curDate];
				$callsByDayArr[$curDate] = ($tmpVal + 1);
				if(!is_array($callsByDayUniqTck[$curDate]) || !in_array($cur['c_from'], $callsByDayUniqTck[$curDate]))
				{
					$callsByDayUniqTck[$curDate][] = $cur['c_from'];
					$tmpVal =(int) $callsByDayUniqueArr[$curDate];
					$callsByDayUniqueArr[$curDate] = ($tmpVal + 1);
				}
			}
			$data['callsByDayArr'] = $callsByDayArr;
			$data['callsByDayUniqueArr'] = $callsByDayUniqueArr;
			
			//Fetch, prepare, sort, and filter our data...
			$callsTable->prepare_items();
			$data['callsTable'] = $callsTable;
			
			$this->view('calls', $data);
		}
		else
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}
}
