define([
  'jquery',
  'mage/validation'
], function ($) {
  'use strict';

  return {
    /**
     * Validate checkout agreements
     *
     * @returns {Boolean}
     */
    validate: function () {
      var $input = $('input[name=refund_address]');
      var $error = $('#decred-refund-address-error');
      var val = $input.val();

      if (typeof val === 'undefined') {
        return true;
      }

      if (val.length === 0 && !$input.prop('required')) {
        $error.hide();
        return true;
      }

      if (val.length === 35) {
        var type = val.slice(0, 2);
        if (type === 'Ds' || type === 'Ts') {
          $error.hide();
          return true;
        }
      }

      $error.show();
      return false;
    }
  };
});
