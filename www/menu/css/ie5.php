/* this makes the float position consistent with others */
ul.navmeister {
	display:inline;
	}




/* this prevents ~some~ of the excessive whitespace-margin between list items */
ul.navmeister li {
	margin:0;
	margin-bottom:-3;
	}



/* this stops events from passing through the gaps between list items (!) */
ul.navmeister ul {
	/*	background-color:#ffffff;	*/		/*  FONDO DEL MENU	*/
	}



/* add list item behaviour */
ul.navmeister li {
	behavior:url(<? print $_GET['path'] . "hover.htc"; ?>);
	/* not until click behaviour works okay
	cursor:pointer;
	*/
	}
	
	
	
/* tweak sub list container left position */
ul.navmeister ul {
	margin-right:-2em;
}
	
	

	
