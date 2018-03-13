/**
 * Decred payment method model
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'Decred_Payments/js/view/payment/method-renderer/decred-method'
    ],
    function (
        Component,
        rendererList,
        DecredMethod
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'decred_payments',
                component: 'Decred_Payments/js/view/payment/method-renderer/decred-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
