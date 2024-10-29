<?php
global $plaidplugin;

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

<h1 class="plaidintegra-extended">Getting Started Guide</h1>
 <p class="plaidintegra-extended-p">Welcome to the Getting Started Guide for the ACH Plaid WordPress plugin! This guide is designed to help you set up and configure the plugin so that you can start integrating Plaid's powerful financial data APIs into your WordPress site.</p> 

 
 <h4>Obtaining Plaid Secret Key and Client ID</h4>

 <p class="plaidintegra-extended-p">1. First, create an account on the Plaid website and log in to the dashboard. <a href="https://dashboard.plaid.com/team/keys" target="_blank"> <?php _e("Create account",'plaid-integra'); ?> </a></p>
 <p class="plaidintegra-extended-p">2. From the dashboard, click on the "API keys" option in the left-hand menu.</p>
 <p class="plaidintegra-extended-p">3. Click on the "Add API Key" button, and you will be prompted to enter a name for your new API key.</p>
 <p class="plaidintegra-extended-p">4. Copy your Client ID and Secret Key and store them in a secure location. You will need to use these credentials to authenticate your requests to the Plaid API from your WordPress plugin or other application.</p>


 <p class="plaidintegra-extended-p">It's important to note that you should never share your Client ID or Secret Key with anyone who is not authorized to access your Plaid account. These credentials are sensitive and should be protected like any other password or authentication token.</p>

 <p class="plaidintegra-extended-p">That's it! With your Client ID and Secret Key in hand, you are ready to start integrating the Plaid API with your WordPress plugin or other application.</p>

 <h4>Displaying Public Side Invoice</h4>
 <p class="plaidintegra-extended-p">1. First, create a new WordPress Page.</p>
 <p class="plaidintegra-extended-p">2. Make sure you include the following shortcode on it <code>[plaidint_invoice]</code> </p>

 <p class="plaidintegra-extended-p">Important: Make sure that the shortcode is not wrapped with "code" tag.</p>

</div>



     
