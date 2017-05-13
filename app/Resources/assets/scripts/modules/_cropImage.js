/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    require('../vendors/jquery.jcrop');

    function crop(){
        $("#cropbox").Jcrop({
            onSelect: checkCoords,
            onChange: updateCoords,
            maxSize: [640,640],
            setSelect: [0,0,640,640],
            aspectRatio: 1

        });
    }

    function updateCoords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    };

    function checkCoords()
    {
        if (parseInt($('#w').val())) return true;
        //alert('Please select a crop region then press submit.');
        return false;
    };


    function init(){
        crop();
    }

    return {
        ready : init
    }

};