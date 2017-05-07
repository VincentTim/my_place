/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    require('dropzone');

    function initDropzone(){
        $("#dropZone").dropzone({ url: "/" });
    }


    function init(){
        initDropzone();
    }

    return {
        ready : init
    }

};