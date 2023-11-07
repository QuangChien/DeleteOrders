define([
    "jquery",
    'uiGridColumnsActions'
], function($, actions){
    'use strict';

    return actions.extend({
        defaults: {
            draggable: true
        }
    });
});