<?php
class PlaidClient extends PlaidIntCommon {
		
		
	public function __construct()	{       	
		
    }

	
   /*Get Latest*/
	public function get_all (){
		global $wpdb;		
		$query = new WP_User_Query(array(
            'role' => 'Subscriber',           
            'orderby' => 'user_registered',
            'order' => 'DESC'
        ));
        $users = $query->get_results();	     
        return $users;	
	}   
	
	
}
?>