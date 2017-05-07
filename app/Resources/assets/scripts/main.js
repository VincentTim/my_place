(function(context){

	"use strict";

	var modules = [
		'_toggleContent',
        '_collection',
		'_dropzone',
		'_cropImage'
	];

	var modulesLoaded = [];

	/**
	 * Vendors calling
	 */
	var debounce = require('throttle-debounce').debounce;

	function init() {

		/**
		 * require once each module listed in modules array
		 */
		$.each(modules, function (i, module) {
	        modulesLoaded.push(require('./modules/'+ module)(context));
	    });

		/**
		 * fire domReady events
		 */
		$(function () {
	        $.each(modulesLoaded, function (i, module) {
	            if (typeof module.ready !== 'undefined') {
	                module.ready(context);
	            }
	        });
	    });

	    /**
		 * fire resize events with some debounce
		 */
	    $(context).on('resize', debounce(250, function () {
	        $.each(modulesLoaded, function (i, module) {
	            if (typeof module.resize !== 'undefined') {
	                module.resize(context);
	            }
	        })
	    }));
	}

	init();

})(window);
