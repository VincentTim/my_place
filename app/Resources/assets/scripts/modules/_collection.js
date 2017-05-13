/**
 * Structure par défaut de tout nouveau module
 */

module.exports = function(context){

	"use strict";

    function addTagCollection(bloc) {
        
        var $collectionHolder;
        var label;

        //if(bloc == 'caption'){ label = 'Ajouter une description'; }

        // setup an "add a tag" link
        var $addTagLink = $('<a href="#" class="btn btn-info add_'+bloc+'_link">'+label+'</a>');
        var $newLinkLi = $('<div></div>').append($addTagLink);
        
        // Get the ul that holds the collection of tags
        $collectionHolder = $('#post_'+bloc);

        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.append($newLinkLi);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addTagLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addTagForm($collectionHolder, $newLinkLi);
        });
    };
    
    function addTagForm($collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<div></div>').append(newForm);
        $newLinkLi.before($newFormLi);
    }
	

	function init(){
        addTagCollection('tags');
        addTagCollection('files');
	}

	return {
		ready : init
	}

};