/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

    "use strict";

    require('../vendors/bin/materialize');

    function initModal(){
        $('.modal').modal({
                dismissible: false, // Modal can be dismissed by clicking outside of the modal
                opacity: .7, // Opacity of modal background
                inDuration: 300, // Transition in duration
                outDuration: 200, // Transition out duration
                startingTop: '4%', // Starting top style attribute
                endingTop: '10%', // Ending top style attribute
                ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
                    console.log('oj');
                },
                complete: function() {
                    window.history.pushState('obj', 'newtitle', '/');
                } // Callback for Modal close
            }
        );
    }

    function eventStart(){
        $(document).ajaxStart(function(){
            $('.loader').toggleClass('loader--visible');
        });
        $(document).ajaxComplete(function(){
            $('.loader').toggleClass('loader--visible');
        });
    }

    function closeModal(){
        $('.modal-close').click(function(){
            $('#modal1 .modal-content').empty();
        })
    }

    function openModal(){
        $('.open-modal').click(function(e){
            e.preventDefault();

            if($(window).width() > 361){
              $('#modal1').modal('open');

              window.history.pushState('obj', 'newtitle', $('#modal1').data('uri')+'/'+$(this).data('id'));

              $.ajax({
                  url: $('#modal1').data('uri')+'/'+$(this).data('id'),
                  type: 'post',
                  success: function(html){
                      $('#modal1 .modal-content').html(html);
                  }
              })
            }


        })
    }


    function init(){
        initModal();
        openModal();
        closeModal();
        eventStart();
    }

    return {
        ready : init
    }

};
