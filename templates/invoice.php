<?php
global $plaidplugin;
?>
<div class="plaid-invoice-cont" id="plaid-invoice-cont">
    <input type="hidden" id="plaid_invoice_key" value="<?php echo $invoice->invoice_key?>">
   <div class="container mt-5">
        <div class="d-flex justify-content-center row">
            <div class="col-md-8">
                <div class="p-3 bg-white rounded">
                    <div class="row">
                    <h1 class="text-uppercase">Invoice</h1>
                        <div class="col-md-6-plaid">
                           
                            <div class="billed"><span class="font-weight-bold text-uppercase">Billed: </span><span class="ml-1"><?php echo $invoice->display_name?></span></div>
                            <div class="billed"><span class="font-weight-bold text-uppercase">Issue Date: </span><span class="ml-1"><?php echo date('m/d/Y', strtotime($invoice->invoice_date))?></span></div>
                            <div class="billed"><span class="font-weight-bold text-uppercase">Due Date: </span><span class="ml-1"><?php echo date('m/d/Y', strtotime($invoice->invoice_date_due))?></span></div>
                            <div class="billed"><span class="font-weight-bold text-uppercase">Invoice ID: </span><span class="ml-1">#<?php echo $invoice->invoice_id?></span></div>
                        </div>
                        <div class="col-md-6-plaid text-right mt-3">
                            <h4 class="text-danger mb-0"><?php echo  $plaidplugin->get_option('plaid_company_name');?></h4><span></span>

                            <p><?php echo  nl2br($plaidplugin->get_option('plaid_company_address_data'));?></p>
                        
                        </div>
                       </div>
                    <div class="mt-3">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php
                                $items = $this->get_all_invoice_items($invoice->invoice_id);
                                $total = 0;
                                foreach($items as $item) {

                                   
                                ?>
                                   
                                    <tr>
                                        <td><?php echo $item->item_product?></td>
                                        <td><?php echo $item->item_qty?></td>
                                        <td>$<?php echo $item->item_unit_price?></td>
                                        <td>$<?php echo $item->item_total?></td>
                                    </tr>

                                <?php  

                                    $total = $total+$item->item_total;
                            
                                 }

                                 $total = number_format($total,2);
                                 
                                 ?>


                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="plaidint-amount-due">Amount Due (USD)</td>
                                        <td class="plaidint-amount-total"><strong>$<?php echo $total?></strong></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-right mb-3 " id="plaid-btn-box">
                        <?php if($invoice->invoice_status==0){ ?>
                        <button  id="link-button-plaid" class="btn pay-invoice-btn btn-custom-color btn-danger btn-sm mr-5" type="button">Pay Now</button>
                        <?php }else{?>

                            <div class="plaid-invoice-paid-message">INVOICE PAID</div>

                        <?php }?>
                    </div>

                    <div class="text-right mb-3 print-pdf-block print-cont-box" id="plaid-btn-box">

                    <a id="print_button" href="#">Print Invoice</a>  <a id="pdf_creation_button" href="#">Download PDF</a>

                    </div>

                    <div id="editor"></div>


                </div>
            </div>
        </div>
    </div>

    </div>