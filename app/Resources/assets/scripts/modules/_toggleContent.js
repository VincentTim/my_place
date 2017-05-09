/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    function toggleContent(){

        $('.item').each(function(){
            var elt = $(this);
            var currentHeight = elt.width();
            var btnExpand = elt.find('.toggle__btn--expand');
            var btnReduce = elt.find('.toggle__btn--reduce');
            var wrapper = elt.find('.content__wrapper');

            btnExpand.click(function(){
                wrapper.toggleClass('content__wrapper--expand');
                elt.toggleClass('item--expand');
                elt.find('footer').toggleClass('footer--expand');
                elt.find('header').toggleClass('header--expand');
                elt.find('.content__overlay').fadeOut(500, function(){
                    btnReduce.fadeTo(200, 0.6);
                });
            });

            btnReduce.click(function(){

                $(this).fadeOut(600, function(){
                    elt.find('header').toggleClass('header--expand');
                    elt.find('.content__overlay').fadeIn(500);

                    elt.find('footer').toggleClass('footer--expand');
                    elt.toggleClass('item--expand');
                    wrapper.toggleClass('content__wrapper--expand');
                })

            });

        })


    }


    function init(){
        toggleContent();
    }

    return {
        ready : init
    }

};