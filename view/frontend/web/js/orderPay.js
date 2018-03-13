define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Catalog/js/price-utils',
    'qrcode',
    'domReady!'
  ],
  function(ko, $, Component, priceUtils, qrcode) {
    'use strict';

    var displayDecredAmountBig = ko.observable();
    var displayDecredAmountSmall = ko.observable();
    var componentClasses = ko.observable('decred-pay');

    var sizeClasses = {
      960: 'decred-pay__big',
      589: 'decred-pay__medium',
      0: 'decred-pay__small'
    };

    var updateComponentResponsiveClasses = function() {
      var $decredPay = $('.decred-pay');
      var decredClass = sizeClasses[0];
      Object.keys(sizeClasses).forEach(function(size) {
        if ($decredPay.width() >= size) {
          decredClass = sizeClasses[size];
        }
      });

      componentClasses('decred-pay ' + decredClass);
    };

    function copyTextToClipboard(text) {
      var textArea = document.createElement("textarea");

      // Place in top-left corner of screen regardless of scroll position.
      textArea.style.position = 'fixed';
      textArea.style.top = 0;
      textArea.style.left = 0;

      // Ensure it has a small width and height. Setting to 1px / 1em
      // doesn't work as this gives a negative w/h on some browsers.
      textArea.style.width = '2em';
      textArea.style.height = '2em';

      // We don't need padding, reducing the size if it does flash render.
      textArea.style.padding = 0;

      // Clean up any borders.
      textArea.style.border = 'none';
      textArea.style.outline = 'none';
      textArea.style.boxShadow = 'none';

      // Avoid flash of white box if rendered for any reason.
      textArea.style.background = 'transparent';


      textArea.value = text;

      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
    }

    var decredFormat = {
      decimalSymbol: '.',
      groupLength: 3,
      groupSymbol: ",",
      integerRequired: 1,
      pattern: "%s",
      precision: 4,
      requiredPrecision: 4
    };

    return Component.extend({
      defaults: {
        template: 'Decred_Payments/payment/order-pay',
        txid: null,
        status: null,
        confirmations: null,
        orderId: null,
        address: 'Unknown',
        amount: null,
        waitConfirmations: 0
      },

      displayDecredAmountBig: displayDecredAmountBig,
      displayDecredAmountSmall: displayDecredAmountSmall,

      copyAmount: function() {
        copyTextToClipboard(this.amount);
      },

      copyAddress: function() {
        copyTextToClipboard(this.address);
      },

      copyTxid: function() {
        copyTextToClipboard(this.txid);
      },

      data: ko.observable(),

      componentClasses: componentClasses,

      elementClasses: function() {
        return 'decred-pay ' + decredClass;
      },

      initialize: function() {
        this._super();
        var element = this;
        var address = this.address;

        var amount = Number(this.amount).toFixed(8);
        displayDecredAmountBig(priceUtils.formatPrice(amount.substring(0, amount.indexOf('.') + 3), decredFormat));
        displayDecredAmountSmall(amount.substring(amount.indexOf('.') + 3).replace(/0+$/, ''));

        /**
         * Add responsive classes to the component.
         */
        $(window).resize(updateComponentResponsiveClasses);

        this.data({
          txid: this.txid,
          status: this.status,
          confirmations: this.confirmations
        });

        var waitForEl = function(id, callback) {
          if (document.getElementById(id)) {
            callback();
          } else {
            setTimeout(function() {
              waitForEl(id, callback);
            }, 100);
          }
        };

        waitForEl('decred-qrcode', function() {
          updateComponentResponsiveClasses();
          new QRCode(document.getElementById('decred-qrcode'), {
            text: address,
            width: 300,
            height: 300,
            correctLevel : QRCode.CorrectLevel.M
          });
        });

        var updateOrderStatusRequest = function() {
          $.ajax('/decred/order/status/order_id/' + element.orderId)
            .done(function (response) {
              if (response) {
                if ('txid' in response) {
                  element.data(response);
                }
              }
            });
        };

        var updateOrder = function() {
          if (element.data().status !== 1) {
            updateOrderStatusRequest();
            setTimeout(updateOrder, 5000);
          }
        };

        updateOrder();
      }
    });
  });
