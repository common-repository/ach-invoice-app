<?php
global $plaidplugin;
$auxInvoice = new PlaidInvoice();

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$invoices = $auxInvoice->get_all_invoices();

//$plaidplugin->delete_all_accounts();
        
?>

<div class="plaidintegra-welcome-panel">

<h1 class="plaidintegra-extended">Invoices</h1>

 <p class="plaidintegra-extended-p">Manage your invoices.</p> 


 <div class="getbwp-sect getbwp-welcome-panel">

 <!-- start: options -->
 <div class="rownoflex palid-button-bart">
    <button type="button" id='plaidint-create-client-btn' class="button button-primary button-large"><span style="margin-right:5px"><i class="fa fa-user"></i></span>Create Client</button>
    <button type="button" id='plaidint-create-invoice-btn' class="button button-primary button-large"><span style="margin-right:5px"><i class="fa fa-dollar"></i></span>Create Invoice</button>
 </div>
        
        <?php
           
           
               
               if (!empty($invoices)){
               
               
               ?>
      
          <table width="100%" class="wp-list-table widefat fixed posts table-generic">
           <thead>
               <tr>
                   <th width="2%"><?php _e('#', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Client', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Date', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Due Date', 'plaid-integration-frz'); ?></th>  
                   <th><?php _e('Amount', 'plaid-integration-frz'); ?></th>            
                   <th><?php _e('Status', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Actions', 'plaid-integration-frz'); ?></th>
                   
               </tr>
           </thead>
           
           <tbody>
           
           <?php 
           $i = 1;
           foreach($invoices as $account) {

            if($account->invoice_status==0){
                $status = '<span class="plaid-pending-invoice">UNPAID</span>'; 
            }elseif($account->invoice_status==1){
                $status = '<span class="plaid-paid-invoice">PAID</span>';
            }
            $invoice_link = $auxInvoice->get_invoice_page_url($account->invoice_key);
         
                           			
              
           ?>
             

               <tr id="acc-row-<?php echo $account->invoice_id?>">
                   <td><?php echo  $i; ?></td>
                   <td><?php echo  esc_attr($account->display_name); ?></td> 
                   <td><?php echo  date('m/d/Y', strtotime($account->invoice_date)); ?></td>
                   <td><?php echo date('m/d/Y', strtotime($account->invoice_date_due)); ?></td>
                   <td><?php echo  esc_attr($account->invoice_amount); ?></td>
                   <td><?php echo  $status ?></td>
                   <td><a href="#" class="edit-invoice-btn" invoice-id="<?php echo esc_attr($account->invoice_id)?>"  title="<?php _e('See','plaid-integration-frz'); ?>"><i class="fa fa-edit"></i>See</a>
                   
                  
                   &nbsp;<a href="#" class="plaid-int-delete-invoice" invoice-id="<?php echo esc_attr($account->invoice_id)?>" title="<?php _e('Delete','plaid-integration-frz'); ?>"><i class="fa fa-trash-o"></i>Delete</a>
                   &nbsp;<a href="<?php echo $invoice_link;?>" target= '_blank' title="<?php _e('See Invoice Link','plaid-integration-frz'); ?>"><i class="fa fa-link"></i>Link</a>
                </td>
                     
                  
               </tr>
               
               
               <?php

                $i++;
             }
                   
                   } else {
           ?>
           <p><?php _e('There are no invoices yet.','ge'); ?></p>
           <?php	} ?>

           </tbody>
       </table>
       
       
       </div>
   	
</div>


<div id="plaidint-edit-invoice-box" class="plaid-popup-box" title="<?php _e('Invoice Details','get-bookings-wp')?>"></div>
<div id="plaidint-create-client-box" class="plaid-popup-box" title="<?php _e('Add New Client','get-bookings-wp')?>"></div>

<div id="plaidint-create-invoice-box" class="plaid-popup-box" title="<?php _e('Create Invoice','get-bookings-wp')?>"></div>

     
