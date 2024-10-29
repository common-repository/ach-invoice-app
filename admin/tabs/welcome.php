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

<h1 class="plaidintegra-extended">Welcome to ACH Invoicing Plugin Dashboard</h1>
 <p class="plaidintegra-extended-p">We would like to thank you for installing <strong>ACH Invoice</strong> plugin. With ACH Plaid WordPress plugin, you can easily add Plaid's functionality to your site, allowing your clients to securely connect their bank accounts and make payments directly from your site. This can be especially useful for your e-commerce site or a site that require users to make payments for goods or services.</p> 



    <div class="plaidintegra-welcome-steps">

        <div class="step-a">
          <h3 >1. Get Plaid Credentials</h3>
          <p class="plaidintegra-extended-p">Login to your Plaid account to obtain your API credentials.</p> 

        </div>

        <div class="step-a">
        <h3 >2. Set Invoice Page</h3>
        <p class="plaidintegra-extended-p">Add the invoice shortcode in the public Invoice Page.</p> 
        </div>

        <div class="step-a">
         <h3 >3. Create your Invoice</h3>
         <p class="plaidintegra-extended-p">Easily create an invoice and send it to your client.</p> 
        </div>

        <div class="step-a">
         <h3>4. Retreive Accounts</h3>
         <p class="plaidintegra-extended-p">All done!. You will get the client's accounts as well the account balances.</p> 
        </div>

    </div>

    <h1 class="plaidintegra-extended">Check the Getting Started Guide</h1>
    <p class="plaidintegra-extended-p">If you're new with Plaid API, it's always a good idea to start with the <a href="?page=plaidplugin&tab=help">getting started guide</a>. This guide is typically a quick and easy way to learn the basics of this plugin, and can help you get up and running quickly.</p>
    <p class="plaidintegra-extended-p">    Remember, the getting started guide is just the beginning. Once you've completed it, you can start billing your clients. </p>

</div>



     
