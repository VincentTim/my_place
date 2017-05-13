/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    function initItem(){
        $('.item').each(function(){
            var elt = $(this);
            var imageHeight = elt.width();
            elt.css('height', imageHeight + 'px');
        })
    }


    function init(){
        initItem();
    }

    return {
        ready : init
    }

};