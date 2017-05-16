/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    function initItem(){
        $('.item').each(function(){
            var elt = $(this);
            var imageHeight = elt.width();
            console.log(elt.parent().data('id')+','+imageHeight);
            elt.css('height', imageHeight + 'px');
            $('.item-collection').css('height', imageHeight + 'px');
        })
    }


    function init(){
        //initItem();
    }

    return {
        ready : init
    }

};
