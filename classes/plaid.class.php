<?php
class PlaidIntPlugin extends PlaidIntCommon {
	
	var $wp_all_pages = false;
	public $classes_array = array();
	
	var $notifications_email = array();
	var $plaidintegra_default_options;	
	var $ajax_prefix = 'plaidint';	
	var $allowed_inputs = array();
	public $allowed_html;

	var $_aPostableTypes = array(
        'post',
        'page',
		'product',
        'gf_entries',
    );
	
		
	public function __construct()	{
		
		$this->load_classes();			
		$this->ini_module();	
		$this->set_allowed_html();
		/* Plugin slug and version */
		$this->slug = 'plaidplugin';	

		add_action( 'init',   array(&$this,'getbwp_shortcodes'));	
		
		/* Allowed input types */
		$this->allowed_inputs = array(
			'text' => __('Text','plaid-integra'),			
			'textarea' => __('Textarea','plaid-integra'),
			'select' => __('Select Dropdown','plaid-integra'),
			'radio' => __('Radio','plaid-integra'),
			'checkbox' => __('Checkbox','plaid-integra'),			
		    'datetime' => __('Date Picker','plaid-integra')
		);	
		
		
		$this->update_default_option_ini();		
		
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( plaidintegra_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];			
		
		add_action('admin_menu', array(&$this, 'add_menu'), 11);
		add_action('admin_head', array(&$this, 'admin_head'), 13 );
		add_action('admin_init', array(&$this, 'admin_init'), 15);				
		
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 12);					
		add_action('wp_enqueue_scripts', array(&$this, 'add_front_end_scripts'), 12);
		add_action('wp_enqueue_scripts', array(&$this, 'add_front_end_styles'), 14);
		add_action('ini', array(&$this, 'create_actions'), 11);	

		add_filter( 'gform_entry_detail_meta_boxes', array(&$this, 'register_plaid_meta_box' ), 10, 3 );
	
		add_action( 'wp_ajax_plaid_in_create_link_token',  array( &$this, 'plaid_in_create_link_token' ));
		add_action( 'wp_ajax_nopriv_plaid_in_create_link_token',  array( &$this, 'plaid_in_create_link_token' ));

		add_action( 'wp_ajax_plaid_in_exchange_token',  array( &$this, 'plaid_in_exchange_token' ));
		add_action( 'wp_ajax_nopriv_plaid_in_exchange_token',  array( &$this, 'plaid_in_exchange_token' ));

		add_action( 'wp_ajax_plaid_create_invoice_form',  array( &$this, 'plaid_create_invoice_form' ));
		add_action( 'wp_ajax_plaid_create_invoice_conf',  array( &$this, 'plaid_create_invoice_conf' ));
		add_action( 'wp_ajax_plaid_edit_invoice_form',  array( &$this, 'plaid_edit_invoice_form' ));

		add_action( 'wp_ajax_plaid_client_add_confirm',  array( &$this, 'client_add_confirm' ));
		add_action( 'wp_ajax_plaid_create_client_form',  array( &$this, 'create_client_form' ));

		add_action( 'wp_ajax_plaid_delete_account',  array( &$this, 'delete_account' ));	
				
		
    }

	

	function add_styles(){
		
		global $wp_locale, $plaidplugin , $pagenow; 		

	   wp_enqueue_script( 'jquery-ui-core' );
	   wp_enqueue_script('jquery-ui-dialog');
	   wp_enqueue_style("wp-jquery-ui-dialog"); 	   
	   wp_enqueue_script( 'jquery-ui-datepicker' );	
	   wp_register_style('plaidintegra_fontawesome', plaidintegra_url.'admin/css/font-awesome/css/font-awesome.min.css');
	   wp_enqueue_style('plaidintegra_fontawesome');

	   wp_register_style('plaidintegra_admin', plaidintegra_url.'admin/css/admin.css');
	   wp_enqueue_style('plaidintegra_admin');

	   	/*google graph*/		
	   wp_register_script('plaidintegra_jsgooglapli', 'https://www.gstatic.com/charts/loader.js');
	   wp_enqueue_script('plaidintegra_jsgooglapli');		

	   wp_register_style('plaidintegra_datepicker', plaidintegra_url.'admin/css/jquery-ui.css');
	   wp_enqueue_style('plaidintegra_datepicker');   
	   wp_register_script( 'plaidintegra_admin',plaidintegra_url.'admin/scripts/admin.js', array( 
			   'jquery-ui-core'	), null , true);
	   wp_enqueue_script( 'plaidintegra_admin' );	
	   
   }

   public function ini_module(){

		global $wpdb;		
		$query = '
			CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'plaid_int_accounts(
			  `account_id` int(11) NOT NULL AUTO_INCREMENT,				 			
			  `account_user_id` int(11) NOT NULL,				
			  `plaid_item_id` varchar(200) NOT NULL,
			  `plaid_access_token` varchar(200) NOT NULL,
			  `plaid_public_token` varchar(200) NOT NULL,				  
			  `institution_id` varchar(200) NOT NULL,
			  `institution_name` varchar(200) NOT NULL,
			  `plaid_account_number` varchar(200) NOT NULL,
			  `plaid_account_id` varchar(200) NOT NULL,
			  `plaid_account_routing` varchar(200) NOT NULL,
			  `plaid_account_wire_routing` varchar(200) NOT NULL,				  
			  `plaid_account_name` varchar(200) NOT NULL,			
			  `plaid_account_type` varchar(200) NOT NULL,	
			  `plaid_account_balance` decimal(11,2) NOT NULL,
			  `plaid_account_creation_date` datetime NOT NULL,			 	  
			  PRIMARY KEY (`account_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			';
			
		$wpdb->query( $query );	
	
		$query = '
			CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'plaid_int_invoices(
			  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,				 			
			  `invoice_client_id` int(11) NOT NULL,	
			  `invoice_plaid_access_token` varchar(300) NOT NULL,						
			  `invoice_amount` decimal(11,2) NOT NULL,
			  `invoice_date` date NOT NULL,
			  `invoice_date_due` date NOT NULL,
			  `invoice_date_paid` date NOT NULL,				  
			  `invoice_notes` varchar(300) NOT NULL,
			  `invoice_terms` text NOT NULL,
			  `invoice_key` varchar(300) NOT NULL,
			  `invoice_status` int(11) NOT NULL DEFAULT 0,						   	  
			  PRIMARY KEY (`invoice_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			';
			
		$wpdb->query( $query );	

		$query = '
			CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'plaid_int_invoice_items(
			  `item_id` int(11) NOT NULL AUTO_INCREMENT,				 			
			  `item_invoice_id` int(11) NOT NULL,				
			  `item_product` varchar(200) NOT NULL,
			  `item_qty` int(11) NOT NULL,
			  `item_unit_price` decimal(11,2) NOT NULL,	
			  `item_total` decimal(11,2) NOT NULL,					 	 	  
			  PRIMARY KEY (`item_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			';
			
		$wpdb->query( $query );	
		$this->update_table();
	
	}

	function register_plaid_meta_box( $meta_boxes, $entry, $form ) {

		if ( ! isset( $meta_boxes['plaid_acct_details'] ) ) {		
	 
			$meta_boxes[ 'plaid_acct_details' ] = array(
				'title'    => 'Plaid Linked Accounts',
				'callback' =>  array(&$this, 'add_plaid_acc_details_meta_box'),
				'context'  => 'normal',
			);

		}		
	 
		return $meta_boxes;
	}

	public function get_sales_total_by_day($date)	{
		
		global $wpdb;
		
		$total = 0;
		
		$sql =  'SELECT count(*) as total, appo.*, usu.* FROM ' . $wpdb->prefix . 'getbwp_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";					
		$sql .= " WHERE DATE(appo.booking_time_from) = %s AND usu.ID = appo.booking_staff_id AND  appo.booking_status = '1' ";
		
		$sql = $wpdb->prepare($sql,array($date));	
		$appointments = $wpdb->get_results($sql );		
		foreach ( $appointments as $appointment ){
				$total= $appointment->total;		
		}
					
		
		return $total;
	}
	
	public function get_graph_total_monthly () 	{
		global $wpdb;
		
		$date_format =  'm/d/Y';		
		$days_of_month = date("t");		
		$day = 1; 
		
		$vals='';
		while($day <= $days_of_month) {
			
			//get sales
			$date = date("Y").'-'.date("m").'-'.$day;					
			$total = $this->get_sales_total_by_day($date);
			$day_format =$day;			
			$vals .= "['".$day_format."', $total]";			
			$day++;
			
			if($day <= $days_of_month){
				
				$vals .= ',';		
			}
		} 
		
		return $vals;		
		
	}

	public function create_client_form()	{	
	
		global $wpdb;	
		
		
		$display = true;				
		
		$html = '';		
		$html .= '<div class="getbwp-sect-adm-edit">';

		$html .= '<p>'.__('Here you can add new clients. Please fill in with the full name and email then click on the Add button.','get-bookings-wp').'</p>';
		
	
		$html .= '<div class="getbwp-edit-service-block">';	
		
		

			
		$html .= '<div class="getbwp-field-separator"><p>'.__('First Name','get-bookings-wp').':</p><input type="text" name="client_name" id="client_name" class="" /></div>';	
		$html .= '<div class="getbwp-field-separator"><p >'.__('Last Name','get-bookings-wp').':</p><input type="text" name="client_lname" id="client_lname" class="" /></div>';							
				
		$html .= '<div class="getbwp-field-separator"><p>'.__('Email','get-bookings-wp').':</p><input type="text" name="client_email" id="client_email" class="" /></div>';					
		
		$html .= '<div class="getbwp-field-separator" id="getbwp-err-message"></div>';	
	
			
			
			$html .= '</div>';
		
		$html .= '</div>';		
		

		echo $html;
		die();		
	
	}

	public function plaid_edit_invoice_form()	{				
		
		$html = '';		

		$invoice_id = $_POST['invoice_id'];

		$auxCommons = new PlaidIntCommon();
		$auxInvoice = new PlaidInvoice();
		$invoice =  $auxInvoice->get_with_id($invoice_id);


		$html .= '<div class="invo-col-plaid">';
		$html .= '<h2>'.__('Invoice #:','plaidplugin').'</h2>' ;	
		$html .= '<p>'.$invoice->invoice_id.'</p>' ;
		$html .= '</div>';
		
		$html .= '<div class="invo-col-plaid">';
		$html .= '<h2>'.__('Client:','plaidplugin').'</h2>' ;	
		$html .= '<p>'.$invoice->display_name.'</p>' ;
		$html .= '</div>';


		$html .= '<div class="invo-col-plaid">';
		$html .= '<h2>'.__('Issue Date:','plaidplugin').'</h2>' ;	
		$html .= '<p>'.date('m/d/Y', strtotime($invoice->invoice_date)).'</p>' ;
		$html .= '</div>';	

		$html .= '<div class="invo-col-plaid">';
		$html .= '<h2>'.__('Due Date:','plaidplugin').'</h2>' ;	
		$html .= '<p>'.date('m/d/Y', strtotime($invoice->invoice_date_due)).'</p>' ;
		$html .= '</div>';		

		if($invoice->invoice_date_paid == '0000-00-00'){
			$paid_date = "N/A";
		}else{
			$paid_date = date('m/d/Y', strtotime($invoice->invoice_date_paid));
		}
		
		$html .= '<div class="invo-col-plaid">';
		$html .= '<h2>'.__('Paid Date:','plaidplugin').'</h2>' ;	
		$html .= '<p>'.$paid_date.'</p>' ;
		$html .= '</div>';	


		if($invoice->invoice_status==0){
			$invoice_status = 'UNPAID';
		}elseif($invoice->invoice_status==1){
			$invoice_status = '<span class="plaid-paid-invoice">PAID</span>';
		}
		$html .= '<div class="invo-col-plaid">';
		$html .= '<h2>'.__('Status:','plaidplugin').'</h2>' ;	
		$html .= '<p>'.$invoice_status.'</p>' ;
		$html .= '</div>';	

		$html .= '<div>';

		$html .= '<h2>'.__('Items:','plaidplugin').'</h2>' ;	
		$html .= $this->get_invoice_items($invoice->invoice_id) ;

		$html .= '</div>';

	
		$html .= '<div>';
		$html .= '<h2>'.__('Linked Plaid Accounts:','plaidplugin').'</h2>' ;	
		$html .= $this->get_plaid_accounts_by_access_token($invoice->invoice_plaid_access_token) ;

		$html .= '</div>';

		
			
		echo $html;		
		die();		
	
	}

	function delete_account()	{		
		global  $wpdb;		
		$acc_id = sanitize_text_field($_POST['acc_id']);						
		$sql ="DELETE FROM " . $wpdb->prefix . "plaid_int_accounts WHERE account_id=%d ;";			
		$sql = $wpdb->prepare($sql,array($acc_id));	
		$rows = $wpdb->get_results($sql);
		die();	
	}

	function delete_invoice()	{		
		global  $wpdb;		
		$acc_id = sanitize_text_field($_POST['acc_id']);						
		$sql ="DELETE FROM " . $wpdb->prefix . "plaid_int_accounts WHERE account_id=%d ;";			
		$sql = $wpdb->prepare($sql,array($acc_id));	
		$rows = $wpdb->get_results($sql);
		die();	
	}

	
	public function plaid_create_invoice_form()	{				
		
		$html = '';	

		$auxCommons = new PlaidIntCommon();
		$terms = $this->get_option('plaid_invoice_terms');

        $arg = array();
        $users = get_users($arg); 
        //print_r($users);

		$html .= '<div class="plaid-invoice-divisor">' ;

			$html .= '<div class="plaid-invoice-col2">' ;
			
				$html .= '<h2>'.__('Client:','plaidplugin').'</h2>' ;	

				$html .= '<select name="invoice_client" id="invoice_client">';
				$html .= '<option value="" selected="selected" >'.__('Select Client', 'plaidplugin').'</option>';

				if (!empty($users))	{			
					foreach($users as $user) {	
						
						$selected='';
						if($staff_id==$user->ID){
							$selected='selected';				
						}
								
						$html .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.' - '.$user->user_email.'</option>';		
					}
					$html .= '</select>';
				
				
				}

			$html .= '</div>';

			$html .= '<div class="plaid-invoice-col2">' ;

				$html .= '<h2>'.__('Due date in days:','plaidplugin').'</h2>' ;	

				$html .= '<select name="invoice_due_date" id="invoice_due_date">';
				$html .= '<option value="" selected="selected" >'.__('Select Days', 'plaidplugin').'</option>';
				$html .= $auxCommons->get_select_value(1,30);
				$html .= '</select>';

			$html .= '</div>';

		$html .= '</div>';

		$html .= '<div class="plaid-invoice-divisor">' ;

			$html .= '<div class="plaid-invoice-col4">' ;

				$html .= '<h2>'.__('Product/Service:','plaidplugin').'</h2>' ;	
				$html .= '<p><input type="text" id="invoice_item_name" value=""></p>' ;

			$html .= '</div>';

			$html .= '<div class="plaid-invoice-col4">' ;

				$html .= '<h2>'.__('Price per Item:','plaidplugin').'</h2>' ;	
				$html .= '<p><input type="text" class="nuva_numbers_comma_only" id="invoice_item_price" placeholder="0.00" value=""></p>' ;

			$html .= '</div>';

			$html .= '<div class="plaid-invoice-col4">' ;

				$html .= '<h2>'.__('Quantity:','plaidplugin').'</h2>' ;	
				$html .= '<p><input type="text" class="nuva_numbers_comma_only" id="invoice_qty" placeholder="1" value="1"></p>' ;

			$html .= '</div>';

			$html .= '<div class="plaid-invoice-col4">' ;

				$html .= '<h2>'.__('Total:','plaidplugin').'</h2>' ;	
				$html .= '<p><input type="text" class="nuva_numbers_comma_only" id="invoice_total" placeholder="0.00" value=""></p>' ;

			$html .= '</div>';

		$html .= '</div>';

		$html .= '<h2>'.__('Terms:','plaidplugin').'</h2>' ;	
		$html .= '<p><textarea name="invoice_terms" type="text" id="invoice_terms" class="large-text code text-area uultra-setting-options-texarea" rows="5">'.$terms.'</textarea></p>' ;
		$html .= '<p><input name="plaid_send_email" type="checkbox" value="1" id="plaid_send_email" tabindex="1" ><label>Send invioce to client</label></p>' ;


		$html .= '<p id="getbwp-add-client-message"></p>' ;		
			
		echo $html;		
		die();		
	
	}

	function get_invoice_items( $invoice_id ) {
	
	
		$html = '';
		$auxInvoice = new PlaidInvoice();	
		$accounts = $auxInvoice->get_all_invoice_items($invoice_id);
		$total = 0;
		
		if (!empty($accounts)){


			$html .='<table width="100%" class="wp-list-table widefat fixed posts table-generic">
			<thead>
				<tr>
					
				<th>Product</th>
				<th>Unit</th>
				<th>Price</th>
				<th>Total</th>
				
					
				</tr>
			</thead>
			
			<tbody>';
			
			$total = 0;
			foreach($accounts as $account) {

				$html .='<tr>
				
				<td>'.  esc_attr($account->item_product).'</td>				
				<td>'.  esc_attr($account->item_qty).'</td>
				<td>'. esc_attr($account->item_unit_price).'</td>
				<td>'.  esc_attr($account->item_total).'</td>               
				
			   
			</tr>';

			

			$total = $total+$account->item_total;


			}

			$total = number_format($total,2);

			$html .='  <tr>
			<td></td>
			<td></td>
			<td>Total</td>
			<td>'. $total.'</td>
		</tr>';

			$html .='</tbody>
			</table>';
		} else {	

			$html .= 'There are no items ';


		
		}
	
		return $html;
	}	

	/**
	 * The callback used to echo the content to the meta box.
	 *
	 * @param array $args An array containing the form and entry objects.
	 */
	function get_plaid_accounts_by_access_token( $access_token ) {
	
	
		$html = '';	
		$accounts = $this->get_all_accounts_by_token( $access_token);

		if (!empty($accounts)){


			$html .='<table width="100%" class="wp-list-table widefat fixed posts table-generic">
			<thead>
				<tr>
					<th width="2%">#</th>
									
					<th>Inst. Name</th>  
					<th>Inst. Id</th>          
					<th>Account #</th>
					<th>Account Routing</th>
					<th>Account Name</th>
					<th>Account Type</th>
					<th>Balance</th>
				
					
				</tr>
			</thead>
			
			<tbody>';
			
			$i = 1;
			foreach($accounts as $account) {

				$html .='<tr>
				<td>'. $i.'</td>
				
				
				<td>'.  esc_attr($account->institution_name).'</td>
				<td>'. esc_attr($account->institution_id).'</td>
				<td>'.  esc_attr($account->plaid_account_number).'</td>
				<td>'.  esc_attr($account->plaid_account_routing).'</td>
				<td>'.  esc_attr($account->plaid_account_name).'</td>
				<td>'.  esc_attr($account->plaid_account_type).'</td>    
				<td>$'.  esc_attr($account->plaid_account_balance).'</td>                    
				
			   
			</tr>';

			$i++;


			}

			$html .='</tbody>
			</table>';
		} else {	

			$html .= 'There are no accounts linked to this form ';


		
		}
	
		return $html;
	}	

	/**
	 * The callback used to echo the content to the meta box.
	 *
	 * @param array $args An array containing the form and entry objects.
	 */
	function add_plaid_acc_details_meta_box( $args ) {
	
		$form  = $args['form'];
		$entry = $args['entry'];
		$entry_id = $entry['id'];

		$html = '';


		$access_token = rgar( $entry, '3' ); 
		 //get accounts

		$accounts = $this->get_all_accounts_by_token( $access_token);

		if (!empty($accounts)){


			$html .='<table width="100%" class="wp-list-table widefat fixed posts table-generic">
			<thead>
				<tr>
					<th width="2%">#</th>
					<th>Client</th>					
					<th>Inst. Name</th>  
					<th>Inst. Id</th>          
					<th>Account #</th>
					<th>Account Routing</th>
					<th>Account Name</th>
					<th>Account Type</th>
					<th>Balance</th>
				
					
				</tr>
			</thead>
			
			<tbody>';
			
			$i = 1;
			foreach($accounts as $account) {

				$html .='<tr>
				<td>'. $i.'</td>
				<td>'.  esc_attr($account->display_name).'</td>				
				<td>'.  esc_attr($account->institution_name).'</td>
				<td>'. esc_attr($account->institution_id).'</td>
				<td>'.  esc_attr($account->plaid_account_number).'</td>
				<td>'.  esc_attr($account->plaid_account_routing).'</td>
				<td>'.  esc_attr($account->plaid_account_name).'</td>
				<td>'.  esc_attr($account->plaid_account_type).'</td>   
				<td>'.  esc_attr($account->plaid_account_balance).'</td>                
				
			   
			</tr>';

			$i++;


			}

			$html .='</tbody>
			</table>';
		} else {	

			$html .= 'There are no accounts linked to this form ';


		
		}
	
		echo $html;
	}	

	public function client_add_confirm()	{
	
		$user_id = '';

		$client_name = sanitize_text_field($_POST['client_name'])	;
		$client_lname = sanitize_text_field($_POST['client_lname']);
		$email = sanitize_text_field($_POST['client_email']);
		
		$user_name = strtolower($client_name.$this->genRandomString());				
		$user_pass = wp_generate_password( 12, false);		
		
		/* Create account, update user meta */
		$sanitized_user_login = sanitize_user($user_name);
		
		if(email_exists($email)){			
			
			$error .=__('<strong>ERROR:</strong> This email is already registered. Please choose another one.','get-bookings-wp');
		
		}elseif(username_exists($user_name)){
			
			$error .=__('<strong>ERROR:</strong> This username is already registered. Please choose another one.','get-bookings-wp');
		
		}elseif($client_name=='' || $email=='' || $client_lname==''){
			
			$error .=__('<strong>ERROR:</strong> All fields are mandatory.','get-bookings-wp');			
		}
		
		if($error==''){			
			/* We create the New user */
			$user_id = wp_create_user( $sanitized_user_login, $user_pass, $email);
			
			if($user_id){
				$display_name =$client_name.' '.$client_last_name ;
				$respon = $display_name.' ('.$email.')';
				wp_update_user( array('ID' => $user_id, 'display_name' => esc_attr($display_name), 'first_name' => esc_attr($client_name)) );
			}

			$respon = $respon;			
			$response = array('response' => 'OK', 'content' => $respon, 'user_id' => $user_id);	
		
		}else{
			$error = $error;			
			$response = array('response' => 'ERROR', 'content' => $error, 'user_id' => $user_id);	
		}	
		echo json_encode($response) ;	
		die();
	}

	public function plaid_create_invoice_conf(){		
		global $wpdb;
		
		$html='';
		$invoice_client = sanitize_text_field($_POST['invoice_client']);
		$invoice_item_name = sanitize_text_field($_POST['invoice_item_name']);

		$invoice_due_date = sanitize_text_field($_POST['invoice_due_date']);
		$creation_date =date('Y-m-d');
		$invoice_due_date=  date("Y-m-d", strtotime("$creation_date + $invoice_due_date day"));

		$invoice_item_price = sanitize_text_field($_POST['invoice_item_price']);
		$invoice_qty = sanitize_text_field($_POST['invoice_qty']);
		$invoice_total = sanitize_text_field($_POST['invoice_total']);
		$invoice_terms = sanitize_text_field($_POST['invoice_terms']);

		$rand_key = $this->get_random_invoice_hash();
		
		if($invoice_client!=''){
			$new_record = array('invoice_client_id' => $invoice_client,	
								'invoice_date' => date('Y-m-d'),
								'invoice_date_due' => $invoice_due_date,
								'invoice_amount' => $invoice_total,								
								'invoice_key' => $rand_key,
								'invoice_terms' => $invoice_terms);								
			$wpdb->insert( $wpdb->prefix . 'plaid_int_invoices', $new_record, array( '%d', '%s', '%s', '%s', '%s' , '%s'));			
			$invoice_id = $wpdb->insert_id; 

			//let's create the item
			$new_record = array(
								'item_invoice_id' => $invoice_id,
								'item_product' => $invoice_item_name,
								'item_qty' => $invoice_qty,
								'item_unit_price' => $invoice_item_price,
								'item_total' => $invoice_total);								
			$wpdb->insert( $wpdb->prefix . 'plaid_int_invoice_items', $new_record, array(  '%d', '%s', '%s' , '%s', '%s'));			
			
			$html ='OK INSERT';	    		
		}
		
		echo  $html;
		die();
	}

	function get_random_invoice_hash(){
		$random_hash = $this->genRandomString(6);
		$encrypt_hash = password_hash($random_hash, PASSWORD_DEFAULT);
		return $encrypt_hash;
	}

	public function genRandomString($length = 5){		
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";	
		$real_string_legnth = strlen($characters) ;			
		$string="ID";	
		for ($p = 0; $p < $length; $p++){
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}	
		return strtolower($string);
	}

	function update_table(){
		global $wpdb;		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'plaid_int_accounts where field="plaid_account_balance" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'plaid_int_accounts add column plaid_account_balance decimal(11,2) NOT NULL ; ';
			$wpdb->query($sql);
		}	
		
	}	

	/**
	* Add the shortcodes
	*/
	function getbwp_shortcodes(){	    		
		add_shortcode( 'plaidint_button', array(&$this,'make_button') );
		add_shortcode( 'plaidint_invoice', array(&$this,'make_invoice') );		
	}
	
	function make_button ($atts){
		extract( shortcode_atts( array(		
			
			'custom_legend' => __('CLICK HERE TO AUTHORIZE','plaid-integration-frz'),
			
		), $atts ) );

		$html = '<a href="#" id="link-button-plaid">'.$custom_legend.'</a>';
		return $html;

	}

	function make_invoice ($atts){
		extract( shortcode_atts( array(		
			
			'custom_legend' => __('CLICK HERE TO AUTHORIZE','plaid-integration-frz'),
			
		), $atts ) );

		$auxInvoice = new PlaidInvoice();
		$html = $auxInvoice->get_invoice_template();
		//$html = '<a href="#" id="link-button-plaid">'.$custom_legend.'</a>';
		return $html;

	}

    public function plaid_in_create_link_token()   {

		$client_id = $this->get_option('plaid_client_id');
		$secret = $this->get_option('plaid_secret');
		$redirect = $this->get_option('plaid_redirect');
		$webhook = $this->get_option('plaid_webhook');    
		$company_name = $this->get_option('plaid_company_name'); 
		$plaid_invoice_key = $_POST['plaid_invoice_key'];

		$auxInvo = new PlaidInvoice();
		$invoice = $auxInvo->get_with_key($plaid_invoice_key);
		
		$plaid_mode = $this->get_option('plaid_enviroment');        
		$url = 'https://'.$plaid_mode.'.plaid.com/link/token/create';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		
		$user = array("client_user_id"=> $invoice->invoice_client_id ,
		"legal_name"=> $invoice->display_name ,	
		);

		//print_r($user);

		$body = array(
			'client_id'   =>$client_id,
			'secret'     => $secret,
			'client_name' => $company_name,
			'user' =>$user,
			'products' =>  ["auth"],
			"country_codes" => ["US"],
			"language" => "en",
			"webhook" => $webhook ,
			"redirect_uri" => $redirect,
			
		);
		
		$response = wp_remote_post(
            $url,
            array(
				'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body' => json_encode($body)
            )
        );		
		
		$response = json_decode($response["body"]);
		//$response = $response["body"];		
		$link_token =$response->{'link_token'}; 

		//print_r($response);

		echo  json_encode( array('link_token' => $link_token) );
		die();
    }

	public function plaid_in_exchange_token()   {
		global $wpdb;

		$client_id = $this->get_option('plaid_client_id');
		$secret = $this->get_option('plaid_secret');
		$redirect = $this->get_option('plaid_redirect');
		$webhook = $this->get_option('plaid_webhook');   

		$public_token = $_POST['public_token'];  
		$accounts = $_POST['accounts']; 
		$institution_name = $_POST['institution']['name']; 
		$institution_id = $_POST['institution']['institution_id'];
		$link_session_id = $_POST['link_session_id'];
		$link_token = $_POST['link_token'];
		$plaid_invoice_key = $_POST['plaid_invoice_key'];

		$auxInvo = new PlaidInvoice();
		$invoice = $auxInvo->get_with_key($plaid_invoice_key);


		$company_name = $this->get_option('plaid_company_name');
	

		$transaction_date = date('Y-m-d');   
		$plaid_mode = $this->get_option('plaid_enviroment');         
		//$url = 'https://'.$plaid_mode.'.plaid.com/link/token/create';
		$url = 'https://'.$plaid_mode.'.plaid.com/item/public_token/exchange';
		
		$user = array("client_user_id"=> $invoice->invoice_client_id ,
		"legal_name"=> $invoice->display_name ,	
		);

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		$body = array(
			'client_id'   =>$client_id,
			'secret'     => $secret,
			'public_token' => $public_token			
		);	
		
		$response = wp_remote_post(
            $url,
            array(
				'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body' => json_encode($body)
            )
        );

		$response = json_decode($response["body"]);	

		$access_token =$response->{'access_token'}; 
		$item_id =$response->{'item_id'};	

		update_user_meta ($user_id, 'plaid_access_token',$access_token);

		$plaid_mode = $this->get_option('plaid_enviroment');     
		$url = 'https://'.$plaid_mode.'.plaid.com/auth/get';

		$body = array(
			'client_id'   =>$client_id,
			'secret'     => $secret,
			'access_token' => $access_token		,			
		);
		
		$response = wp_remote_post(
            $url,
            array(
				'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body' => json_encode($body)
            )
        );

		$response = json_decode($response["body"]);

		try {

			$accunts_list =$response->{'accounts'}; 
			$acc_numbers =$response->{'numbers'}->{'ach'}; 
			
			$i = 0;
			foreach($acc_numbers as $ach) {		

				//get account name
				$account_number = $ach->account;
				$account_id = $ach->account_id;
				$account_routing = $ach->routing;
				$account_wire_routing = $ach->wire_routing;
				$account_details = $accunts_list[$i];
				$account_name = $account_details->name;
				$account_type = $account_details->subtype;	

				//balances
				$balance = $account_details->balances->available;
			//	echo "bal: ". $balance ;

				$query = "INSERT INTO " . $wpdb->prefix ."plaid_int_accounts (
					`account_user_id`,
		            `plaid_item_id`,
					`plaid_access_token`,
					`plaid_public_token` ,
					`institution_id` ,
					`institution_name` , 
					`plaid_account_number`, 
					`plaid_account_id`,
					`plaid_account_routing`,
					`plaid_account_wire_routing`,
					`plaid_account_name`,
					`plaid_account_type`,	
					`plaid_account_balance`	,				
					`plaid_account_creation_date`	
					
									
					) 

					VALUES ('$user_id',
					'$item_id',
					'$access_token',
					'$public_token', 
					'$institution_id', 
					'$institution_name', 
					'$account_number', 
					'$account_id', 
					'$account_routing', 
					'$account_wire_routing', 
					'$account_name', 
					'$account_type',
					'$balance',
					'".date('Y-m-d')."')";
					
				$wpdb->query( $query );	
				$i++;

			}

			//update invoice
			$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . 'plaid_int_invoices  
			SET invoice_plaid_access_token =%s  ,  
			invoice_status =%s,
			invoice_date_paid =%s
			WHERE invoice_key = %s ;',array($access_token, 1, $transaction_date, $plaid_invoice_key));
			$results = $wpdb->query($sql);
		
		} catch (\Exception $e) {

			echo "error: ". $e->getMessage();
           
        }	

		$res = array('access_token' =>$access_token);
		echo json_encode($res);		
		die();
    }

	/*Get By Token*/
	public function get_all_accounts_by_token ($token){
		global $wpdb;		
		$sql = 'SELECT account.*, usu.*	 FROM ' . $wpdb->prefix . 'plaid_int_accounts account  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = account.account_user_id )";		
		$sql .= " WHERE usu.ID = account.account_user_id AND account.plaid_access_token =  %s ";		
		$sql = $wpdb->prepare($sql,array($token));			
		$orders = $wpdb->get_results($sql );	
		return $orders ;		
	
	}

	/*Get Latest*/
	public function get_all_accounts ($howmany=200){
		global $wpdb;		
		$sql = 'SELECT account.*, usu.*	 FROM ' . $wpdb->prefix . 'plaid_int_accounts account  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = account.account_user_id )";		
		$sql .= " WHERE usu.ID = account.account_user_id ORDER BY account.account_id DESC  LIMIT %d ";		
		$sql = $wpdb->prepare($sql,array($howmany));			
		$orders = $wpdb->get_results($sql );	
		return $orders ;		
	
	}

	function delete_all_accounts(){		
		global  $wpdb;						
		$sql ="DELETE FROM " . $wpdb->prefix . "plaid_int_accounts WHERE account_id <> 0 ;";
		$rows = $wpdb->query($sql);		
	}
	
	public function uultra_if_windows_server()	{
		$os = PHP_OS;
		$os = strtolower($os);			
		$pos = strpos($os, "win");	
		
		if ($pos === false) {
			
			//echo "NO, It's not windows";
			return false;
		} else {
			//echo "YES, It's windows";
			return true;
		}			
	
	}	
	
	/**
	 * This has been added to avoid the window server issues
	 */
	public function uultra_one_line_checkbox_on_window_fix($choices)
	{		
		
		if($this->uultra_if_windows_server()) //is window
		{
			$loop = array();		
			$loop = explode(",", $choices);
		
		}else{ //not window
		
			$loop = array();		
			$loop = explode(PHP_EOL, $choices);	
			
		}			
		
		return $loop;
	
	}
	
	
	function admin_head(){
		$screen = get_current_screen();
		$slug = $this->slug;
		
	}
		
		
	
	/*Post value*/
	function get_post_value($meta) 
	{			
				
		if (isset($_POST[$meta]) ) {
				return sanitize_text_field($_POST[$meta]);
			}
			
			
	}
	
	function load_classes(){		
		
		$this->common = new PlaidIntCommon();
			
	}	
	
	function ini_plugin(){
		
		$is_admin = is_admin() && ! defined( 'DOING_AJAX' );
		
		/* Add hooks */
		if ( ! $is_admin  ) {			
			$this->create_actions();
		}		
	}
	
	public function update_default_option_ini () {
		$this->options = get_option('plaidintegra_options');		
		if (!get_option('plaidintegra_options')) {			
			update_option('plaidintegra_options', $this->plaidintegra_default_options );
		}
		
		
	}	
	
	
	function admin_init() 
	{
		
		$this->tabs = array(
		    'main' => __('Dashboard','plaid-integra'),	
			'accounts' => __('Accounts','plaid-integra'),	
			'clients' => __('Clients','plaid-integra'),	
			'invoices' => __('Invoices','plaid-integra'),	
			'settings' => __('Settings','plaid-integra')	,
			'help' => __('Help','plaid-integra')		

		);	
		
		$this->default_tab = 'main';		
		$this->default_tab_membership = 'main';	
		
	}
	
	function add_menu() {
		global $plaidplugin, $plaidintegra_activation ;			
		$menu_label = __('ACH Invoicer','plaid-integra');		
		add_menu_page( __('ACH Invoicer','plaid-integra'), $menu_label, 'manage_options', $this->slug, array(&$this, 'admin_page'), plaidintegra_url .'admin/images/small_logo_16x16.png', '159.140');
		do_action('plaidintegra_admin_menu_hook');
	}
	

	function admin_tabs( $current = null ) {
		
		global $plaidplugincomplement, $plaidintegra_custom_fields;
		
			$tabs = $this->tabs;
			$custom_badge = '';
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :					
				
				
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'><span class='plaidplugin-adm-tab-legend'>".$name."</span></a>";
				else :
					$links[] = "<a class='nav-tab ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'><span class='plaidplugin-adm-tab-legend'>".$name."</span></a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	
	
	function do_action(){
		global $userultra;				
		
	}
		
	
	/* set a global option */
	function plaidintegra_set_option($option, $newvalue)
	{		
		$settings = get_option('plaidintegra_options');	
		if($settings=='')	{
			$settings = array();
		}
		$settings[$option] = $newvalue;
		update_option('plaidintegra_options', $settings);
	}
	
	
	public function add_front_end_styles()	{
		global $wp_locale;
		
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script('jquery-ui');	
		$rand = rand( 1, 99999999999 );
		wp_enqueue_style('plaidintegra_custom_css', plaidintegra_url . 'css/custom.css', array(),  false);
			
		wp_register_script( 'plaidplugin-jspdf', plaidintegra_url.'js/jspdf.umd.min.js',array('jquery'),  null);
		wp_enqueue_script('plaidplugin-jspdf');

		wp_register_script( 'plaidplugin-htmlcanvas', plaidintegra_url.'js/html2canvas.min.js',array('jquery'),  null);
		wp_enqueue_script('plaidplugin-htmlcanvas');
		
		/*Users JS*/		
		wp_register_script( 'plaidplugin-front_js', plaidintegra_url.'js/plaid.js',array('jquery'),  null);
		wp_enqueue_script('plaidplugin-front_js');

		wp_add_inline_script( 'plaidplugin-front_js', 'const PLAIDFRONTV = ' . json_encode( array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),			
		) ), 'before' );
		
		
	}
	
	
	function add_front_end_scripts() {		
		wp_register_script("plaidintegra_inilink_js", "https://cdn.plaid.com/link/v2/stable/link-initialize.js");
		wp_enqueue_script("plaidintegra_inilink_js");		
	}
		
		
	function get_option($option) 
	{
		$settings = get_option('plaidintegra_options');
		if (isset($settings[$option])) 
		{
			if(is_array($settings[$option]))
			{
				return $settings[$option];
			
			}else{
				
				return stripslashes($settings[$option]);
			}
			
		}else{
			
		    return '';
		}
		    
	}
	
		
	function initial_setup() {
		
		global $plaidplugin, $wpdb, $plaidplugincomplement ;
		
		$inisetup   = get_option('plaidintegra_ini_setup');
		
		if (!$inisetup) 
		{				
					
			update_option('plaidintegra_ini_setup', true);
		}
		
		
	}
	
		
	function include_tab_content() {
		
		global $plaidplugin, $wpdb, $plaidplugincomplement ;
		
		$screen = get_current_screen();
		
		if( strstr($screen->id, $this->slug ) ) 
		{
			if ( isset ( $_GET['tab'] ) ) 
			{
				$tab = $_GET['tab'];
				
			} else {
				
				$tab = $this->default_tab;
			}
			
			//
			
			

			require_once (plaidintegra_path.'admin/tabs/'.$tab.'.php');
			
			
		}
	}

	public function set_allowed_html(){

		global $allowedposttags;
		

		$allowed_html = wp_kses_allowed_html( 'post' );

		$allowed_html['select'] = array(
			'name' => array(),
			'id' => array(),
			'class' => array(),
			'style' => array()
		);

		$allowed_html['option'] = array(
			'name' => array(),
			'id' => array(),
			'class' => array(),
			'value' => array(),
			'selected' => array(),
			'style' => array()
		);

		$allowed_html['input'] = array(
			'name' => true,
			'id' => true,
			'class' => true,
			'value' => true,
			'selected' => true,
			'style' =>true
		);

		$allowed_html['table'] = array(
			'name' => true,
			'id' => true,
			'class' => true,			
			'style' => true
		);

		$allowed_html['td'] = array(
			'name' =>true,
			'id' => true,
			'class' => true,
			'style' => true
		);

		$allowed_html['tr'] = array(
			'name' => array(),
			'id' => array(),
			'class' => array(),
			
		);

		$allowed_atts = array(
			'align'      => array(),
			'span'      => array(),
			'checked'      => array(),
			'class'      => array(),
			'selected'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'display'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data'       => array(),
			'title'      => array(),
			'getbwp-data-date'      => array(),
			'getbwp-data-timeslot'      => array(),
			'getbwp-data-service-staff'      => array(),
			'getbwp-max-capacity'      => array(),
			'getbwp-max-available'      => array(),
			'data-nuve-rand-id'      => array(),
			'data-nuve-rand-key'      => array(),
			'data-location'      => array(),
			'data-cate-id'      => array(),
			'data-category-id'      => array(),
			'data-staff-id'      => array(),
			'data-staff_id'      => array(),
			'data-id'      => array(),
			'appointment-id'      => array(),
			'message-id'      => array(),
			
			'appointment-status'      => array(),
			'getbwp-staff-id'      => array(),				
			'service-id'      => array(),			
			'staff-id'      => array(),	
			'user-id'      => array(),	
			'staff_id'      => array(),		
			'widget-id'      => array(),
			'day-id'      => array(),
			'break-id'      => array(),	
			'category-id'      => array(),			
			'/option'      => array(),
			'label'      => array(),
			
			

			
		);



		$allowedposttags['button']     = $allowed_atts;
		$allowedposttags['form']     = $allowed_atts;
		$allowedposttags['label']    = $allowed_atts;
		$allowedposttags['input']    = $allowed_atts;
		$allowedposttags['textarea'] = $allowed_atts;
		$allowedposttags['iframe']   = $allowed_atts;
		$allowedposttags['script']   = $allowed_atts;
		$allowedposttags['style']    = $allowed_atts;
		$allowedposttags['display']    = $allowed_atts;
	
		$allowedposttags['select']    = $allowed_atts;
		$allowedposttags['option']    = $allowed_atts;
		$allowedposttags['optgroup']    = $allowed_atts;
		$allowedposttags['strong']   = $allowed_atts;
		$allowedposttags['small']    = $allowed_atts;
		$allowedposttags['table']    = $allowed_atts;
		$allowedposttags['span']     = $allowed_atts;
		$allowedposttags['abbr']     = $allowed_atts;
		$allowedposttags['code']     = $allowed_atts;
		$allowedposttags['pre']      = $allowed_atts;
		$allowedposttags['div']      = $allowed_atts;
		$allowedposttags['img']      = $allowed_atts;
		$allowedposttags['h1']       = $allowed_atts;
		$allowedposttags['h2']       = $allowed_atts;
		$allowedposttags['h3']       = $allowed_atts;
		$allowedposttags['h4']       = $allowed_atts;
		$allowedposttags['h5']       = $allowed_atts;
		$allowedposttags['h6']       = $allowed_atts;
		$allowedposttags['ol']       = $allowed_atts;
		$allowedposttags['ul']       = $allowed_atts;
		$allowedposttags['li']       = $allowed_atts;
		$allowedposttags['em']       = $allowed_atts;
		$allowedposttags['hr']       = $allowed_atts;
		$allowedposttags['br']       = $allowed_atts;
		$allowedposttags['tr']       = $allowed_atts;
		$allowedposttags['td']       = $allowed_atts;
		$allowedposttags['p']        = $allowed_atts;
		$allowedposttags['a']        = $allowed_atts;
		$allowedposttags['b']        = $allowed_atts;
		$allowedposttags['i']        = $allowed_atts;

		$this->allowed_html = $allowedposttags;

	}
	
		// update settings
    function update_settings() 
	{
		foreach($_POST as $key => $value) 
		{
            if ($key != 'submit')
			{			
					
								
					$this->plaidintegra_set_option($key, $value) ;
					//special setting for page
					if($key=="plaidintegra_my_account_page")
					{						
						
						 update_option('plaidintegra_my_account_page',$value);				 
						 
						 
					}  

            }
        }
		
		//get checks for each tab
		
		
		 if ( isset ( $_GET['tab'] ) )
		 {
			 
			    $current = $_GET['tab'];
				
          } else {
                $current = $_GET['page'];
          }	 
            
		$special_with_check = $this->get_special_checks($current);
         
        foreach($special_with_check as $key)
        {
           
            
                if(!isset($_POST[$key]))
				{			
                    $value= '0';
					
				 } else {
					 
					  $value= $_POST[$key];
				}	 	
         
			
			$this->plaidintegra_set_option($key, $value) ;  
			
			
            
        }
         
      $this->options = get_option('plaidintegra_options');

        echo '<div class="updated"><p><strong>'.__('Settings saved.','plaid-integra').'</strong></p></div>';
    }
	
	public function get_special_checks($tab) {
		$special_with_check = array();
		
		if($tab=="settings"){			
		
		 $special_with_check = array('social_media_fb_active',  'social_media_google', 'twitter_connect',  'mailchimp_active', 'mailchimp_auto_checked',  'aweber_active', 'aweber_auto_checked',  'password_1_letter_1_number' , 'password_one_uppercase' , 'password_one_lowercase', 'recaptcha_display_registration', 'recaptcha_display_loginform' ,'recaptcha_display_comments','recaptcha_display_forgot_password','recaptcha_display_registration_native', 'recaptcha_display_loginform_native' ,'recaptcha_display_comments_native','recaptcha_display_forgot_password_native');
		}	
		
		
	
	return  $special_with_check ;
	
	}	
	
	
	function admin_page() 
	{
		global $plaidpluginembers;

		
		
		if (isset($_POST['plaidintegra_update_settings']) ) {
            $this->update_settings();
        }
		
				
		
		
			
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin"> 
        
       
            
                <h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?>               
                
                 
                
                </h2>  
  
            

			<div class="<?php echo $this->slug; ?>-admin-contain">    
            
               
			
				<?php 		
				
				
					$this->include_tab_content(); 
				
				
				?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }
	
	
}
?>