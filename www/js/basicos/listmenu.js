
//menu object constructor
function simpleMenu(navid, orient)
{
	//if the dom is not supported, or this is opera 5 or 6, don't continue
	var version = parseInt(navigator.appVersion, 10);
	var opera5 = /opera[\/ ][56]/i.test(navigator.userAgent);
	if(typeof document.getElementById == 'undefined' || opera5) { return; }
	
	//identify konqueror
	this.iskde = navigator.vendor == 'KDE';

	//identify internet explorer [but both opera and konqueror recognise the .all collection]
	this.isie = typeof document.all != 'undefined' && typeof window.opera == 'undefined' && !this.iskde;
	//identify old safari [< 1.2]
	this.isoldsaf = navigator.vendor == 'Apple Computer, Inc.' && typeof XMLHttpRequest == 'undefined';

	//ul tree
	this.tree = document.getElementById(navid);	

	//if it exists
	if(this.tree !== null)
	{
		//get trigger elements
		this.items = this.tree.getElementsByTagName('li');
		this.itemsLen = this.items.length;

		//initialise each trigger, using do .. while because it's faster
		var i = 0; 
		do
		{
			this.init(this.items[i], this.isie, this.isoldsaf, this.iskde, navid, orient);
		}
		while (++i < this.itemsLen);
	}
}


//trigger initialiser
simpleMenu.prototype.init = function(trigger, isie, isoldsaf, iskde, navid, ishoriz)
{
	//store menu object, or null if there isn't one
	//extend it as a property of the trigger argument
	//so it's global within [and unique to] the scope of this instantiation
	//which is the same trick as going "var self = this"
	trigger.menu = 
	  trigger.getElementsByTagName('ul').length ? 
	  trigger.getElementsByTagName('ul')[0] : null;

	//store link object
	trigger.link = trigger.getElementsByTagName('a')[0];

	//store whther this is a submenu or child menu
	//a submenu's parent node will have the navbar id
	trigger.issub = trigger.parentNode.id == navid;
	
	//whether this is a horizontal navbar
	trigger.ishoriz = ishoriz == 'horizontal';
	
	//menu opening events
	//onfocus doesn't bubble in ie, but its proprietary 'onactivate' event does
	//which works in win/ie5.5+
	this.openers = { 'm' : 'onmouseover', 'k' : (isie ? 'onactivate' : 'onfocus') };

	//bind menu openers
	for(var i in this.openers)
	{
		trigger[this.openers[i]] = function(e)
		{
			//set rollover persistence classname -- we have to check for an existing value first
			//because some opera builds don't allow the class attribute to have a leading space
			//don't do persistent rollovers for konqueror, because they stick in kde <= 3.0.4
			if(!iskde) { this.link.className += (this.link.className === '' ? '' : ' ') + 'rollover'; }

			//if trigger has a menu
			if(this.menu !== null)
			{
				//show the menu by positioning it back on the screen
				//we can use the same positions as in pure CSS for most browsers ['css']
				//but we have to compute the positions for ie ['compute'] 
				//because it uses position:relative on <a> 
				//whereas the others have position:relative on <li>
				//we also need to use those values for old safari builds ['compute']
				//because the regular positioning doesn't work 
				
				//if this is a horizontal navbar
				//set the left position to auto [css] or compute a position [compute]
				if(this.ishoriz) { this.menu.style.left = (ie6omenor || isoldsaf) ? this.offsetLeft + 'px' : 'auto'; }
				
				//if this is a horizontal navbar and a first-level submenu 
				//set the top position to auto [css] or compute a position [compute]
				//otherwise set it to 0 [css] or compute a position [compute]
				this.menu.style.top = (this.ishoriz && this.issub) ? 
				  (ie6omenor || (this.ishoriz && isoldsaf)) ? 
				    this.link.offsetHeight + 'px' : 'auto' : 
				  (ie6omenor || (this.ishoriz && isoldsaf)) ? 
				    this.offsetTop + 'px' : '0';
			}
		};
	}


	//menu closing events
	//'ondeactivate' is the equivalent blur handler for 'onactivate'
	this.closers = { 'm' : 'onmouseout', 'k' : (isie ? 'ondeactivate' : 'onblur') };

	//bind menu closers
	for(i in this.closers)
	{
		trigger[this.closers[i]] = function(e)
		{
			//store event-related-target property
			this.related = (!e) ? window.event.toElement : e.relatedTarget;

			//if event came from outside current trigger branch
			if(!this.contains(this.related))
			{
				//reset rollover persistence classname; not for konqueror
				if(!iskde) { this.link.className = this.link.className.replace(/[ ]?rollover/g, ''); }
				
				//if trigger has a menu
				if(this.menu !== null)
				{
					//hide menu using left for a horizontal menu, or top for a vertical
					this.menu.style[(this.ishoriz ? 'left' : 'top')] = 
					  this.ishoriz ? '-10000px' : '-100em';
				}
			}
		};
	}


	//contains method by jkd -- http://www.jasonkarldavis.com/
	//not necessary for ie because we're re-creating in ie-proprietary method
	//and it would throw errors in mac/ie5 anyway
	//not actually necessary for opera 7 either, because it's already implemented
	//but it won't do any harm, so spoofing doesn't matter
	if(!isie)
	{
		trigger.contains = function(node)
		{
			if (node === null) { return false; }
			if (typeof node == 'undefined') { return false; }
			if (node == this) { return true; }
			else { return this.contains(node.parentNode); }
		};
	}
	// stop IE memory leak
	trigger = null;
};


toba.confirmar_inclusion('basicos/listamenu');