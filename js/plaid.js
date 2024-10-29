var $ = jQuery;

jQuery(document).ready(function($) {
    $('#link-button-plaid').on('click', function(e) {       
        createLinkToken();
    });  

}); //jquery ready

function createLinkToken() {

      const data = new FormData();
	  data.append( 'action', 'plaid_in_create_link_token' );						
	  const params = new URLSearchParams(data);
      var plaid_invoice_key = jQuery( "#plaid_invoice_key" ).val();

      jQuery.ajax({
       
        url: ajaxurl,      
        data: {"action": "plaid_in_create_link_token",
               "plaid_invoice_key": plaid_invoice_key},
        type: "POST",       
        success: function (response) {
            var data =jQuery.parseJSON(response);	           
            console.log('Link Token: ' + data.link_token);
            linkPlaidAccount(data.link_token);
        },
        error: function (err) {
            console.log('Error creating link token.');
            const errMsg = JSON.parse(err);
            alert(err.error_message);
            console.error("Error creating link token: ", err);
        }
    });
}

function linkPlaidAccount(linkToken) {
  var linkHandler = Plaid.create({
      token: linkToken,
      onSuccess: function (public_token, metadata) {

          var plaid_invoice_key = jQuery( "#plaid_invoice_key" ).val();
          var body = {
              action: 'plaid_in_exchange_token',
              public_token: public_token,
              accounts: metadata.accounts,
              institution: metadata.institution,
              link_session_id: metadata.link_session_id,
              link_token: linkToken,
              plaid_invoice_key: plaid_invoice_key
          };

          jQuery.ajax({              
              url: ajaxurl,
              type: "POST",
              data: body,             
              success: function (data) {                   
                var res = JSON.parse(data);
                plaidSubmitForm(res);
                 
              },
              error: function (err) {
                alert("error");
                  console.log('Error linking Plaid account.');
                  const errMsg = JSON.parse(err);
                  console.error("Error linking Plaid account: ", err);
              }
          });
      },
      onExit: function (err, metadata) {
          console.log("linkBankAccount error=", err, metadata);
          const errMsg = JSON.parse(err);
                  console.error("Error linking Plaid account: ", err);

          linkHandler.destroy();
          if (metadata.link_session_id == null && metadata.status == "requires_credentials") {
              createLinkToken();
          }
      }
  });
  linkHandler.open();
}

function plaidSubmitForm(res){            
    jQuery( "#plaid-btn-box" ).html( '<div class="plaid-invoice-paid-message">INVOICE PAID</div>' );

}

jQuery(document).on("click", "#print_button", function() {
    jQuery( ".print-cont-box" ).hide();
    window.print();
    setInterval(function() {
        jQuery( ".print-cont-box" ).show();
    }, 2500);
});

window.jsPDF = window.jspdf.jsPDF;
var doc = new jsPDF();
var docPDF = new jsPDF();
jQuery(document).on("click", "#pdf_creation_button", function() {

    jQuery( ".print-cont-box" ).hide();
    var elementHTML = document.querySelector("#plaid-invoice-cont");
    docPDF.html(elementHTML, {
    callback: function(docPDF) {
    docPDF.save('PlaidInvoice.pdf');
    },
    x: 15,
    y: 15,
    width: 170,
    windowWidth: 650
    });   

});