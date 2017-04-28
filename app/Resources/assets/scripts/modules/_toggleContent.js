/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    function toggleContent(){

        $('.toggle_btn').click(function(){
            console.log('test')
            //Todo: toggle icon btn
            //Todo: slide image 70px top
            //Todo: slide description 100vw - 20px
        });

    }


    function init(){
        toggleContent();
    }

    return {
        ready : init
    }

};