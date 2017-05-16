/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    function displayTag(){
        console.log($('.post_container .item').length);

        if($(window).width() > 361){
          $('.post_container').find('.item').each(function(){
              var elt = $(this);
              var arrayTags = elt.data('tags').split(','); /*'test, test, test'*/

              elt.mouseenter(function(){
                  var number = Math.floor((Math.random() * arrayTags.length -1 ) + 1);
                  elt.append('<p class="array">#'+arrayTags[number]+'</p>');
              });
              elt.mouseleave(function(){
                  elt.find('.array').remove();
              })
          })
        }
    }


    function init(){
        displayTag();
    }

    return {
        ready : init
    }

};
