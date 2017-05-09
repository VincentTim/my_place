/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    function displayTag(){
        console.log($('.post_container .item').length);

        $('.post_container').find('.item').each(function(){
            var elt = $(this);
            var arrayTags = elt.data('tags').split(','); /*'test, test, test'*/
            console.log(elt.data('tags').split(','));

            elt.mouseenter(function(){
                var number = Math.floor((Math.random() * arrayTags.length -1 ) + 1);
                elt.append('<p class="array">'+arrayTags[number]+'</p>');
            });
            elt.mouseleave(function(){
                console.log('ouit');
                elt.find('.array').remove();
            })
        })
    }


    function init(){
        displayTag();
    }

    return {
        ready : init
    }

};