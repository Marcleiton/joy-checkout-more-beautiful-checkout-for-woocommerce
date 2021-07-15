$(document).ready(function(){
    $("#billing_phone").before("<span class=\"step-1-ccod\">+55</span>");
});
        
$("input[type=radio][name=payment_method]").change(function() {
    if ($(this).value == "woo-mercado-pago-custom") {
        $(".checkout-img-card").show();
    }
    else{
       $(".checkout-img-card").hide(); 
    }
});
            