define([
    'jquery',
    'jquery/ui',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';
    $.widget('mage.autocomplete', {
        options: {
            url: 'mystore/autocomplete/index',
            method: 'POST'
        },
        _create: function () {
            var self = this;

            $('input').on('input', function () {
                var sku = jQuery('#sku').val();
                console.log(sku);
                $.ajax({
                    type: self.options.method,
                    dataType: 'json',
                    url: self.options.url,
                    data: {sku: sku},
                }).done(function (data) {
                    $(".skuList").empty();
                    jQuery.each(data, function (i, val) {
                        $(".skuList").append("<option>" + val + "</option>");
                    });
                });
            });
        }
    })
    return $.mage.autocomplete;
});