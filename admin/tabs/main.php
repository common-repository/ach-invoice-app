<?php
global $plaidplugin, $wp_locale;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$auxInvoice = new PlaidInvoice();


$today =0;
$tomorrow = 0;
$week = 0;

$pending = $auxInvoice->get_total_invoice_status(0);
$paid = $auxInvoice->get_total_invoice_status(1);
$earnings = $auxInvoice->get_total_earnings_status(1);
$outstanding = $auxInvoice->get_total_earnings_status(0);
$cancelled = 0;        
?>

<div class="plaidintegra-welcome-panel">

<h1 class="plaidintegra-extended">ACH Invoicing Plugin Dashboard</h1>

 <div class="getbwp-main-sales-summary" >


        <ul>
                   <li>                    
                     
                      <p style=""> <?php echo esc_attr($today)?></p>  
                       <small><?php _e('Overdue','ach-invoice-app')?> </small>                  
                    </li>
                    
                    <li>                   
                     
                      <p style="color:"> <?php echo esc_attr($pending)?></p> 
                       <small><?php _e('Unpaid','ach-invoice-app')?> </small>                   
                    </li>
                
                	<li>                   
                     
                      <p style="color:"> <?php echo esc_attr($paid)?></p> 
                       <small><?php _e('Paid','ach-invoice-app')?> </small>                   
                    </li>
                   
                    <li>     
                        
                         <a href="#" class="getbwp-adm-see-appoint-list-quick" getbwp-status='3' getbwp-type='byunpaid'>              
                         
                          <p style="color: #f2776e;font-size: 2.4rem;">$<?php echo esc_attr($outstanding)?></p> 
                          <small><?php _e('Outstanding','ach-invoice-app')?> </small>
                          
                           </a>                     
                    </li>
                   
                     <li>     
                        
                         <a href="#" class="getbwp-adm-see-appoint-list-quick" getbwp-status='3' getbwp-type='byunpaid'>              
                         
                          <p style="color: #8BC34A;font-size: 2.4rem;">$<?php echo esc_attr($earnings)?></p> 
                          <small><?php _e('Earnings','ach-invoice-app')?> </small>
                          
                           </a>                     
                    </li>
                   
                   
              </ul>

 </div>

<div class="achinvo-main-dashcol" >

        <div class="dashcol-graph-1" >           
            <div id='achplaidhome-gcharthome' style="width: 100%; height: 180px;"> </div>
        </div>

        <div class="dashcol-graph-2" >          
            <div id='achplaidhome-grossmonthly' style="width: 100%; height: 180px;"> </div>
        </div>
</div>

<h3><?php _e('Latest 20 Invoices','ach-invoice-app')?></h3>

<div class="achinvo-main-dashcol" >

<?php

$invoices = $auxInvoice->get_all_invoices(20);
           
           
               
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
               <td><?php echo  date('m/d/Y', strtotime($account->invoice_date_due)); ?></td>
               <td><?php echo  esc_attr($account->invoice_amount); ?></td>
               <td><?php echo  $status ?></td>
               <td><a href="#" class="edit-invoice-btn" invoice-id="<?php echo esc_attr($account->invoice_id)?>"  title="<?php _e('See','plaid-integration-frz'); ?>"><i class="fa fa-edit"></i><span class="achico-responsive">See</span></a>
               
              
               &nbsp;<a href="#" class="plaid-int-delete-invoice" invoice-id="<?php echo esc_attr($account->invoice_id)?>" title="<?php _e('Delete','plaid-integration-frz'); ?>"><i class="fa fa-trash-o "></i><span class="achico-responsive">Delete</span></a>
               &nbsp;<a href="<?php echo $invoice_link;?>" target= '_blank' title="<?php _e('See Invoice Link','plaid-integration-frz'); ?>"><i class="fa fa-link"></i><span class="achico-responsive">Link</span></a>
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

<div id="plaidint-edit-invoice-box" class="plaid-popup-box" title="<?php _e('Invoice Details','ach-invoice-app')?>"></div>
<div id="plaidint-create-client-box" class="plaid-popup-box" title="<?php _e('Add New Client','ach-invoice-app')?>"></div>

<div id="plaidint-create-invoice-box" class="plaid-popup-box" title="<?php _e('Create Invoice','ach-invoice-app')?>"></div>

<?php

$sales_val= $auxInvoice->get_graph_total_monthly(0);
$sales_gross_monthly_val= $auxInvoice->get_graph_total_gross_by_month();
$sales_val_paid= $auxInvoice->get_graph_total_monthly(1);
$months_array = array_values( $wp_locale->month );
$current_month = date("m");
$current_month_legend = $months_array[$current_month -1];

?>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
		  
        var data = google.visualization.arrayToDataTable([

          ["<?php _e('Day','ach-invoice-app')?>", "<?php _e('Unpaid','ach-invoice-app')?>"],
         <?php echo wp_kses($sales_val,  $plaidplugin->allowed_html)?>


        ]  
        
        );

        var options = {
                title: "<?php _e('Current Month Unpaid Invoices','ach-invoice-app')?>",          
                 hAxis: {title: '<?php printf(__( 'Month: %s', 'ach-invoice-app' ),
                 $current_month_legend);?> ',  titleTextStyle: {color: '#333'},  textStyle: {fontSize: '9'}},
          
                vAxis: {minValue: 0},		 
                series: {
                        0: {
                            // set options for the first data series
                            color: '#57c1e2'
                        }
                        
                    },
		            legend: { position: "none" }
        };

        var chart_1 = new google.visualization.AreaChart(document.getElementById('achplaidhome-gcharthome'));
        chart_1.draw(data, options);
        
        //gross montlhly sales
		 var data = google.visualization.arrayToDataTable([
          ["<?php _e('Day','ach-invoice-app')?>", "<?php _e('Sales','ach-invoice-app')?>"],
         <?php echo wp_kses($sales_gross_monthly_val,  $plaidplugin->allowed_html)?>
        ]);

        var options = {
		  title: "<?php _e('Current Year Gross Sales','ach-invoice-app')?>",        
          hAxis: {title: '<?php printf(__( 'Year: %s', 'ach-invoice-app' ),
    date("Y"));?> ',  titleTextStyle: {color: '#333'},  textStyle: {fontSize: '9'}},
          vAxis: {minValue: 0},	
          series: {
                        0: {
                            // set options for the first data series
                            color: '#8BC34A'
                        }
                        
                    },

		  legend: { position: "none" }
        };

        var chart_2 = new google.visualization.AreaChart(document.getElementById('achplaidhome-grossmonthly'));
        chart_2.draw(data, options);
		
		
      }
    </script>    

     
