var $ = jQuery;

jQuery(document).ready(function($) {

    "use strict";

    jQuery('.plaid-popup-box').on('change keyup keypress blur','.nuva_numbers_comma_only',function(e)	{

       var currentInput = $(this).val();
       var fixedInput = currentInput.replace(/[A-Za-z!@#$%^&*()]/g, '');
       $(this).val(fixedInput);

       invoice_calculate();
    });

    function invoice_calculate(){
        var total = jQuery("#invoice_item_price").val() *  jQuery("#invoice_qty").val() ;
        jQuery("#invoice_total").val(total);
    }

    $('#invoice_qty').change(function() {   

      //  alert('test');
 
        invoice_calculate();
     });
    

    jQuery( "#plaidint-create-client-box" ).dialog({
        autoOpen: false,																							
        width: 500,
        modal: true,
        buttons: {
        "Create Client": function() {    

            var client_name =   jQuery("#client_name").val();   
            var client_lname =   jQuery("#client_lname").val();           
            var client_email =   jQuery("#client_email").val();        
            
            if(client_name==''){alert('Please, input a name'); return;}
            if(client_lname==''){alert('Please, input a last name'); return;}
            if(client_email==''){alert('Please, input an e-mail address'); return;}
            
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "plaid_client_add_confirm",
                "client_name": client_name,
                "client_lname": client_lname,
                "client_email": client_email},
                success: function(data){   
                    
                    var res =jQuery.parseJSON(data);						
					if(res.response=='OK'){
                        alert('The new client has been created');                    
                        window.location.href='admin.php?page=plaidplugin&tab=invoices&confclient=ok';   
						
					}else{

                        jQuery("#getbwp-err-message").html(res.content);
                    }
                }
            });           
            
        
        },
        
        "Cancel": function() {            
            jQuery( this ).dialog( "close" );
        },        
        
        },
        close: function() {        
        
        }
    });

    /* open category form */	
	jQuery( "#plaidint-create-invoice-box" ).dialog({
        autoOpen: false,																							
        width: 800,
        modal: true,
        buttons: {
        "Create Invoice": function() {    

            var invoice_client=   jQuery("#invoice_client").val();
            var invoice_due_date =   jQuery("#invoice_due_date").val();
            var invoice_item_price =   jQuery("#invoice_item_price").val();
            var invoice_qty =   jQuery("#invoice_qty").val();
            var invoice_total =   jQuery("#invoice_total").val();   
            var invoice_item_name =   jQuery("#invoice_item_name").val();  
            var invoice_terms =   jQuery("#invoice_terms").val();       
            
            if(invoice_client==''){alert('Please, choose a client'); return;}
            if(invoice_item_name==''){alert('Please, input a name for the service'); return;}
            
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "plaid_create_invoice_conf",
                "invoice_client": invoice_client,
                "invoice_item_name": invoice_item_name,
                "invoice_due_date": invoice_due_date,
                "invoice_item_price": invoice_item_price,
                "invoice_qty": invoice_qty,
                "invoice_total": invoice_total,
                "invoice_terms": invoice_terms},
                
                success: function(data){                       
                   alert('The new invoice has been created');                    
                   window.location.href='admin.php?page=plaidplugin&tab=invoices&conf=ok';
                    
                }
            });           
            
        
        },
        
        "Cancel": function() {            
            jQuery( this ).dialog( "close" );
        },        
        
        },
        close: function() {
        
        
        }
    });

    /* open category form */	
	jQuery( "#plaidint-edit-invoice-box" ).dialog({
        autoOpen: false,																							
        width: 990,
        modal: true,
        buttons: {
        "MARK AS PAID": function() {    

            var invoice_client=   jQuery("#invoice_client").val();
            var invoice_due_date =   jQuery("#invoice_due_date").val();
            var invoice_item_price =   jQuery("#invoice_item_price").val();
            var invoice_qty =   jQuery("#invoice_qty").val();
            var invoice_total =   jQuery("#invoice_total").val();   
            var invoice_item_name =   jQuery("#invoice_item_name").val();        
            
            if(invoice_client==''){alert('Please, choose a client'); return;}
            if(invoice_item_name==''){alert('Please, input a name for the service'); return;}
            
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "plaid_create_invoice_conf",
                "invoice_client": invoice_client,
                "invoice_item_name": invoice_item_name,
                "invoice_due_date": invoice_due_date,
                "invoice_item_price": invoice_item_price,
                "invoice_qty": invoice_qty,
                "invoice_total": invoice_total},
                
                success: function(data){                  
                   
                   			
                               
                                                              
                    
                }
            });           
            
        
        },
        
        "CLOSE": function() {            
            jQuery( this ).dialog( "close" );
        },        
        
        },
        close: function() {
        
        
        }
    });


    jQuery(document).on("click", "#plaidint-create-client-btn", function(e) {
			
        e.preventDefault();          
        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "plaid_create_client_form"},
                success: function(data){			
                    jQuery("#plaidint-create-client-box" ).html( data);	                    
                    jQuery("#plaidint-create-client-box" ).dialog( "open" );	
                    
                    }
            });        
        
        e.preventDefault();         
            
    });

    jQuery(document).on("click", ".plaid-int-delete-acc", function(e) {
        e.preventDefault();	 
        var doIt = false;
        var acc_id = jQuery(this).attr("acc-id"); 
        doIt=confirm("Are you totally sure?");
		if(doIt){                    
            jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {"action": "plaid_delete_account",
                           "acc_id": acc_id},
                    success: function(data){	
                        jQuery("#acc-row-"+ acc_id).slideUp();
                    }
            }); 
        }
        e.preventDefault();         
    });

    jQuery(document).on("click", ".plaid-int-delete-invoice", function(e) {
        e.preventDefault();	 
        var doIt = false;
        var invoice_id = jQuery(this).attr("invoice-id"); 
        doIt=confirm("Are you totally sure that you want to delete this invoice?");
		if(doIt){                    
            jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {"action": "plaid_delete_invoice",
                           "invoice_id": invoice_id},
                    success: function(data){	
                        jQuery("#acc-row-"+ acc_id).slideUp();
                    }
            }); 
        }           
    });
	
	
	jQuery(document).on("click", "#plaidint-create-invoice-btn", function(e) {
			
        e.preventDefault();    
        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "plaid_create_invoice_form"},
                success: function(data){			
                    jQuery("#plaidint-create-invoice-box" ).html( data);	
                    
                    var date = new Date();
					date.setDate(date.getDate());              

                    $('#invoice_due_date').datepicker({
                        dateFormat:'m/d/y',
                        todayBtn: true,
                        numberOfMonths:2,                      
                        autoclose: true,
                        startDate: date,
                        todayHighlight: true
                    })

                    jQuery("#plaidint-create-invoice-box" ).dialog( "open" );	
                    
                    }
            });        
        
        e.preventDefault();         
            
    });

    jQuery(document).on("click", ".edit-invoice-btn", function(e) {
			
        e.preventDefault();    
        var invoice_id = jQuery(this).attr("invoice-id");
        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "plaid_edit_invoice_form",
                        "invoice_id": invoice_id},
                
                success: function(data){				
                                            
                    jQuery("#plaidint-edit-invoice-box" ).html( data);              
                    jQuery("#plaidint-edit-invoice-box" ).dialog( "open" );
                                            
                    
                    }
            });        
        
        e.preventDefault();         
            
    });
	
});

