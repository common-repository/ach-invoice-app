<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $plaidplugin;

?>

<h3><?php _e('Plugin Settings','plaid-integra'); ?></h3>
<form method="post" action="">
<input type="hidden" name="plaidintegra_update_settings" />


<div id="tabs-bupro-settings" class="plaidintegra-multi-tab-options">

<ul class="nav-tab-wrapper bup-nav-pro-features">



<li class="nav-tab bup-pro-li"><a href="#tabs-plaidintegra-recaptcha" title="<?php _e('Settings','plaid-integra'); ?>"><?php _e('Common','plaid-integra'); ?> </a></li>




</ul>




<div id="tabs-plaidintegra-recaptcha">


<div class="plaidintegra-sect  plaidintegra-welcome-panel">
  <h3><?php _e('Plaid Credentials','plaid-integra'); ?></h3>
  
    
    <p><?php _e("You can get the Client ID and Secret key at Plaid Dashboard",'plaid-integra'); ?>. <a href="https://dashboard.plaid.com/team/keys" target="_blank"> <?php _e("Click here",'plaid-integra'); ?> </a> </p>
        
  
  <table class="form-table">
<?php


	$this->create_plugin_setting(
			'input',
			'plaid_client_id',
			__('Client ID:','plaid-integra'),array(),
			__('Enter your Client ID here.','plaid-integra'),
			__('Enter your Client ID here.','plaid-integra')
	);
	
	$this->create_plugin_setting(
			'input',
			'plaid_secret',
			__('Secret Key:','plaid-integra'),array(),
			__('Enter your Plaid secret here.','plaid-integra'),
			__('Enter your Plaid secret here.','plaid-integra')
	);

  $this->create_plugin_setting(
    'input',
    'plaid_redirect',
    __('Redirect URL:','plaid-integra'),array(),
    __('Enter your Plaid redirect URL here.','plaid-integra'),
    __('Enter your Plaid redirect URL here.','plaid-integra')
);

$this->create_plugin_setting(
  'input',
  'plaid_webhook',
  __('WebHook URL:','plaid-integra'),array(),
  __('Enter your WebHook URL here.','plaid-integra'),
  __('Enter your WebHook URL here.','plaid-integra')
);

$this->create_plugin_setting(
                'select',
                'plaid_enviroment',
                __('Enviroment:','plaid-integra'),
                array(
                        'sandbox' => __('Sandobx','plaid-integra'),
                        'development' => __('Development','plaid-integra'), 
                        'production' => __('Production','plaid-integra'), 
                        
                        ),
                        
                __('','users-control'),
          __('.','users-control')
               );

               $this->create_plugin_setting(
                'input',
                'plaid_company_name',
                __('Input your Company Name:','plaid-integra'),array(),
                __('Input your Company Name here.','plaid-integra'),
                __('Input your Company Name here.','plaid-integra')
            );
            

               $this->create_plugin_setting(
                'textarea',
                'plaid_company_address_data',
                __('Input Company Billing Address Data:','plaid-integra'),array(),
                __('Input your company address data. This information will be displayed in your invoice.','plaid-integra'),
                __('Input your company address data. This information will be displayed in your invoice','plaid-integra')
              );

          $this->create_plugin_setting(
                'textarea',
                'plaid_invoice_terms',
                __('Terms:','plaid-integra'),array(),
                __('Enter your predefined terms and notes for your invoice','plaid-integra'),
                __('Enter your predefined terms and notes for your invoice','plaid-integra')
              );


          $this->create_plugin_setting(
                'select',
                'invoice_page_id',
                __('Invoice Page','plaid-integra'),
                $this->get_all_sytem_pages(),
                __('Make sure you have the <code>[plaidint_invoice]</code> shortcode on this page.','plaid-integra'),
                __('This page will display the invoice.','plaid-integra')
          );



?>
</table>
</div>





<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','plaid-integra'); ?>"  />
</p>

  
</div>



</div>




</form>