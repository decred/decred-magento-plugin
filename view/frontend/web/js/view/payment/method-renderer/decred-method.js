/**
 * Decred method renderer
 */
 /*browser:true*/
 /*global define*/
 define(
 [
   'jquery',
    'ko',
   'Magento_Checkout/js/view/payment/default',
   'Magento_Catalog/js/price-utils',
   'Magento_Checkout/js/action/select-payment-method',
   'Magento_Checkout/js/model/payment/additional-validators',
   'Magento_Checkout/js/model/quote',
   'Magento_Checkout/js/model/totals',
   'Magento_Customer/js/model/customer',
   'Magento_Checkout/js/checkout-data',
   'Decred_Payments/js/model/decred-address-validator',
 ],
 function (
   $,
   ko,
   Component,
   priceUtils,
   selectPaymentMethodAction,
   additionalValidators,
   quote,
   totals,
   customer,
   checkoutData,
   decredAddressValidator) {
   'use strict';

   var decredFormat = {
     decimalSymbol: '.',
     groupLength: 3,
     groupSymbol: ",",
     integerRequired: 1,
     pattern: "%s",
     precision: 4,
     requiredPrecision: 4
   };

   var displayDecredAmountBig = ko.observable();
   var displayDecredAmountSmall = ko.observable();
   var displayDecredTotalPrice = ko.observable();


   additionalValidators.registerValidator(decredAddressValidator);

   quote.getTotals().subscribe(function() {
     var segment = totals.getSegment('decred_amount');
     if (segment && 'value' in segment) {
       var amount = Number(segment.value).toFixed(8);

       displayDecredAmountBig(priceUtils.formatPrice(amount.substring(0, amount.indexOf('.') + 3), decredFormat));
       displayDecredAmountSmall(amount.substring(amount.indexOf('.') + 3).replace(/0+$/, ''));
       displayDecredTotalPrice(segment.value);
     }
   });

   return Component.extend({
     defaults: {
         template: 'Decred_Payments/payment/decred-form'
     },

     displayDecredAmountBig: displayDecredAmountBig,
     displayDecredAmountSmall: displayDecredAmountSmall,
     displayDecredTotalPrice: displayDecredTotalPrice,

     isShowRefundAddress: function() {
       var checkoutConfig = window.checkoutConfig;

       if (checkoutConfig && checkoutConfig.payment && checkoutConfig.payment.decred_payments) {
         return !!checkoutConfig.payment.decred_payments.isShowRefundAddress;
       }

       return true;
     },

     isRefundAddressOptional: function() {
       var checkoutConfig = window.checkoutConfig;
       if (checkoutConfig && checkoutConfig.payment && checkoutConfig.payment.decred_payments) {
         return !!checkoutConfig.payment.decred_payments.isRefundAddressOptional;
       }

       return true;
     },

     getData: function() {

       return {
         'method': this.getCode(),
         'additional_data': {
           'refund_address': $('input[name=refund_address]').val(),
         }
       }
     },
   });
 });
