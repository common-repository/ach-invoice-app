<?php
global $plaidplugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$accounts = $plaidplugin->get_all_accounts();
        
?>

<div class="plaidintegra-welcome-panel">

<h1 class="plaidintegra-extended">Linked Accounts</h1>

 <p class="plaidintegra-extended-p">Here you will see a list of all linked Plaid accounts.</p> 


 <div class="getbwp-sect getbwp-welcome-panel">
        
        <?php
           
           
               
               if (!empty($accounts)){
               
               
               ?>
      
          <table width="100%" class="wp-list-table widefat fixed posts table-generic">
           <thead>
               <tr>
                   <th width="2%"><?php _e('#', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Client', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Token', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Inst. Name', 'plaid-integration-frz'); ?></th>  
                   <th><?php _e('Inst. Id', 'plaid-integration-frz'); ?></th>                
                  
                   <th><?php _e('Account #', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Account Routing', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Account Name', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Account Type', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Balance', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Date', 'plaid-integration-frz'); ?></th>
                   <th><?php _e('Actions', 'plaid-integration-frz'); ?></th>

                   
                   
               </tr>
           </thead>
           
           <tbody>
           
           <?php 
           $i = 1;
           foreach($accounts as $account) {        
              
           ?>
             

               <tr id="acc-row-<?php echo $account->account_id?>">
                   <td><?php echo  $i; ?></td>
                   <td><?php echo  esc_attr($account->display_name); ?></td>
                   <td><?php echo  esc_attr($account->plaid_access_token); ?></td>
                   <td><?php echo  esc_attr($account->institution_name); ?></td>
                   <td><?php echo  esc_attr($account->institution_id); ?></td>
                   <td><?php echo  esc_attr($account->plaid_account_number); ?></td>
                   <td><?php echo  esc_attr($account->plaid_account_routing); ?></td>
                   <td><?php echo  esc_attr($account->plaid_account_name); ?></td>
                   <td><?php echo  esc_attr($account->plaid_account_type); ?></td>   
                   <td><?php echo  esc_attr($account->plaid_account_balance); ?></td>                
                   <td><?php echo  esc_attr(date("m/d/Y", strtotime($account->plaid_account_creation_date))); ?></td>
                   <td>&nbsp;<a href="#" class="plaid-int-delete-acc" acc-id="<?php echo esc_attr($account->account_id)?>" title="<?php _e('Delete','plaid-integration-frz'); ?>"><i class="fa fa-trash-o"></i>Delete</a></td>
                  
               </tr>
               
               
               <?php

                $i++;
             }
                   
                   } else {
           ?>
           <p><?php _e('There are no linked accounts yet.','plaid-integration-frz'); ?></p>
           <?php	} ?>

           </tbody>
       </table>
       
       
       </div>
   	
</div>

     
