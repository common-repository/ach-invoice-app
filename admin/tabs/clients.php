<?php
global $plaidplugin;
$auxObj= new PlaidClient();

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$rowClients = $auxObj->get_all();

        
?>

<div class="plaidintegra-welcome-panel">

<h1 class="plaidintegra-extended">Clients</h1>

 <p class="plaidintegra-extended-p">Manage your clients.</p> 


 <div class="getbwp-sect getbwp-welcome-panel">

 <!-- start: options -->
 <div class="rownoflex palid-button-bart">
    <button type="button" id='plaidint-create-client-btn' class="button button-primary button-large"><span style="margin-right:5px"><i class="fa fa-user"></i></span>Create Client</button>

 </div>
        
        <?php
           
           
               
               if (!empty($rowClients)){
               
               
               ?>
      
          <table width="100%" class="wp-list-table widefat fixed posts table-generic">
           <thead>
               <tr>
                   <th width="2%"><?php _e('#', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Name', 'plaid-integration-frz'); ?></th>                
                   <th><?php _e('Email', 'plaid-integration-frz'); ?></th>                    
                   <th><?php _e('Actions', 'plaid-integration-frz'); ?></th>
                   
               </tr>
           </thead>
           
           <tbody>
           
           <?php 
           $i = 1;
           foreach($rowClients as $client) {                                     			
              
           ?>
             

               <tr>
                   <td><?php echo  $i; ?></td>
                   <td><?php echo  esc_attr($client->display_name); ?></td>                   
                   <td><?php echo  esc_attr($client->user_email); ?></td>                
                   <td><a href="#" class="edit-invoice-btn" u-id="<?php echo esc_attr($client->ID)?>"  title="<?php _e('See','plaid-integration-frz'); ?>"><i class="fa fa-edit"></i>See</a>
       
                </td>                    
                  
               </tr>
               
               
               <?php

                $i++;
             }
                   
                   } else {
           ?>
           <p><?php _e('There are no invoices yet.','get-bookings-wp'); ?></p>
           <?php	} ?>

           </tbody>
       </table>
       
       
       </div>
   	
</div>


<div id="plaidint-edit-invoice-box" class="plaid-popup-box" title="<?php _e('Invoice Details','get-bookings-wp')?>"></div>
<div id="plaidint-create-client-box" class="plaid-popup-box" title="<?php _e('Add New Client','get-bookings-wp')?>"></div>

<div id="plaidint-create-invoice-box" class="plaid-popup-box" title="<?php _e('Create Invoice','get-bookings-wp')?>"></div>

     
