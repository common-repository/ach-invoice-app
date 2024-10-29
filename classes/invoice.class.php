<?php
class PlaidInvoice extends PlaidIntCommon {
		
		
	public function __construct()	{
       	
		
    }

	function get_invoice_template(){

        ob_start();

        $invoice_key = '';
        if(isset($_GET["invoice_key"]))	{
			$invoice_key = sanitize_text_field($_GET["invoice_key"]);		
		}

        $invoice = $this->get_with_key($invoice_key);
		
        //include the specified file			
		$theme_path = get_template_directory();			
		include(plaidintegra_path.'/templates/invoice.php');					
		//assign the file output to $content variable and clean buffer
        $content = ob_get_clean();
		return  $content;
    }

	function get_all_sytem_pages()
	{
	    if($this->wp_all_pages === false)
	    {
	        $this->wp_all_pages[0] = "Select Page";
	        foreach(get_pages() as $key=>$value)
	        {
	            $this->wp_all_pages[$value->ID] = $value->post_title;
	        }
	    }
	    
	    return $this->wp_all_pages;
	}

	public function get_invoice_page_url($invoice_key)
    {
		global  $wp_rewrite ;		
		$wp_rewrite = new WP_Rewrite();				
		
		$account_page_id = $this->get_option('invoice_page_id');		
		$my_account_url = get_permalink($account_page_id);	
		$invoice_link = $my_account_url.'?invoice_key='.$invoice_key;				
		return $invoice_link;
	
	}

   /*Get Latest*/
	public function get_all_invoices ($howmany=200){
		global $wpdb;
		
		$sql = 'SELECT invoice.*, usu.*	 FROM ' . $wpdb->prefix . 'plaid_int_invoices invoice  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = invoice.invoice_client_id )";		
		$sql .= " WHERE usu.ID = invoice.invoice_client_id ORDER BY invoice.invoice_date DESC  LIMIT %d ";		
		$sql = $wpdb->prepare($sql,array($howmany));			
		$orders = $wpdb->get_results($sql );	
		return $orders ;		
	
	}

	 /*Get Latest*/
	public function get_all_invoice_items ($invoice_id){
		global $wpdb;		
		$sql = 'SELECT * FROM ' . $wpdb->prefix . 'plaid_int_invoice_items   ' ;			
		$sql .= " WHERE item_invoice_id =  %d ";		
		$sql = $wpdb->prepare($sql,array($invoice_id));			
		$orders = $wpdb->get_results($sql );	
		return $orders ;		
	

		
	}

	public function get_total_invoice_status ($status){
		global $wpdb;		
		$sql = 'SELECT count(*) as total FROM ' . $wpdb->prefix . 'plaid_int_invoices   ' ;			
		$sql .= " WHERE invoice_status =  %d ";		
		$sql = $wpdb->prepare($sql,array($status));			
		$rows = $wpdb->get_results($sql );	

		foreach ( $rows as $item ){
			return $item->total	;	
		
		}
				
	}

	public function get_graph_total_monthly ($status = 0) 
	{
		global $wpdb;
		
		$date_format =  'm/d/Y';		
		$days_of_month = date("t");		
		$day = 1; 
		
		$vals='';
		while($day <= $days_of_month) {
			
			//get sales
			$date = date("Y").'-'.date("m").'-'.$day;			
			$total = $this->get_sales_total_by_day($date, $status);
			$day_format =$day;			
			$vals .= "['".$day_format."', $total]";			
			$day++;
			
			if($day <= $days_of_month){
				
				$vals .= ',';		
			}
		} 
		
		return $vals;		
		
	}

	public function get_graph_total_gross_by_month () 
	{
		global $wpdb, $wp_locale;
		
		$date_format = 'm/d/Y';		
		$current_year = date("Y");		
		$month = 1; 
        
        $vals = "";
        $day = "";
		
		$months_array =  array_values( $wp_locale->month_abbrev );

		while($month <= 12) {
			
			//get sales
			$date = date("Y").'-'.date("m").'-'.$day;
			
			$total = $this->get_sales_total_gross_by_month($month, $current_year);
			
			$total_formated = 	$total;		
			$day_format =$months_array[$month-1];			
			$vals .= "['".$day_format."', $total]";			
			$month++;
			
			if($month <= 12){
				
				$vals .= ',';		
			}
		} 
		
		return $vals;		
		
	}

	public function get_sales_total_gross_by_month($month, $year) {
		global $wpdb;
		
		$site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );		
					  
		$sql = ' SELECT SUM(invoice_amount )  as total
		FROM ' . $wpdb->prefix . 'plaid_int_invoices   ' ;		
		$sql .= " WHERE MONTH(invoice_date_paid)  = '".$month."' AND YEAR(invoice_date_paid)  = '".$year."'  AND invoice_status = '1' ";				
		$res = $wpdb->get_results($sql);
		//echo $sql;
		if ( empty( $res ) )
		{
			return $row->total;		
		
		}else{
			
			
			foreach ( $res as $row )
			{
				if($row->total=='' || $row->total==null)
				{
					return 0;		
				}else{
					return $row->total;
				
				}			
			}			
		}	
	}

	public function get_sales_total_by_day($date, $status=1){
		
		global $wpdb;
		
		$total = 0;		
		$sql =  'SELECT count(*) as total, appo.*, usu.* FROM ' . $wpdb->prefix . 'plaid_int_invoices appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.invoice_client_id)";					
		$sql .= " WHERE DATE(appo.invoice_date_due) = %s AND usu.ID = appo.invoice_client_id AND  appo.invoice_status = '".$status."' ";
		
		$sql = $wpdb->prepare($sql,array($date));	
		$appointments = $wpdb->get_results($sql );
		
		foreach ( $appointments as $appointment )
		{
				$total= $appointment->total;			
			
		}					
		
		return $total;
	}

	public function get_total_earnings_status ($status){
		global $wpdb;		
		$sql = 'SELECT SUM(invoice_amount ) as total FROM ' . $wpdb->prefix . 'plaid_int_invoices   ' ;			
		$sql .= " WHERE invoice_status =  %d ";		
		$sql = $wpdb->prepare($sql,array($status));			
		$rows = $wpdb->get_results($sql );	

		foreach ( $rows as $item ){
			return $item->total	;	
		
		}
				
	}

	

    public function get_with_key ($key)	{
		global $wpdb;		
		
		$sql =  'SELECT  usu.*, invo.*  FROM ' . $wpdb->prefix . 'plaid_int_invoices invo  ' ;			
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = invo.invoice_client_id)";			
		$sql .= " WHERE usu.ID = invo.invoice_client_id  AND  invo.invoice_key  = %s ";		

		$sql = $wpdb->prepare($sql,array($key));
		$rows = $wpdb->get_results( $sql );	
		
        if ( empty( $rows ) ){		
		
		}else{			
			
			foreach ( $rows as $item ){
				return $item;			
			
			}
			
		}		
				
	}

	public function get_with_id ($key)	{
		global $wpdb;		
		
		$sql =  'SELECT  usu.*, invo.*  FROM ' . $wpdb->prefix . 'plaid_int_invoices invo  ' ;			
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = invo.invoice_client_id)";			
		$sql .= " WHERE usu.ID = invo.invoice_client_id  AND  invo.invoice_id  = %s ";		

		$sql = $wpdb->prepare($sql,array($key));
		$rows = $wpdb->get_results( $sql );	
		
        if ( empty( $rows ) ){		
		
		}else{			
			
			foreach ( $rows as $item ){
				return $item;			
			
			}
			
		}				
	}	
}
?>