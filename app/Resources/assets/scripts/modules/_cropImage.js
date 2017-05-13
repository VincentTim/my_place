/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";


    require('cropper');

    function crop(){
        $('#cropbox').cropper({
            aspectRatio: 1,
            crop: function(e) {
                // Output the result data for cropping image.
                console.log(e.x);
                console.log(e.y);
                console.log(e.width);
                console.log(e.height);
                console.log(e.rotate);
                console.log(e.scaleX);
                console.log(e.scaleY);
                updateCoords(e);
            }
        });
    }

    // require('../vendors/jquery.jcrop');
    // function crop(){
    //     $("#cropbox").Jcrop({
    //         onSelect: checkCoords,
    //         onChange: updateCoords,
    //         maxSize: [640,640],
    //         setSelect: [0,0,640,640],
    //         aspectRatio: 1
    //
    //     });
    // }
    //
    function updateCoords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.width);
        $('#h').val(c.height);
    };
    //
    // function checkCoords()
    // {
    //     if (parseInt($('#w').val())) return true;
    //     //alert('Please select a crop region then press submit.');
    //     return false;
    // };


    function init(){
        crop();
    }

    return {
        ready : init
    }

};