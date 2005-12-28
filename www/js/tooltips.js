/***********************************************
* Cool DHTML tooltip script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

var offsetxpoint=-120 //Customize x offset of tooltip
var offsetypoint=10 //Customize y offset of tooltip
var ie=document.all
var ns6=document.getElementById && !document.all
var enabletip=false
if (ie||ns6)
var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : ""
function ietruebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function ddrivetip(thetext, thecolor, thewidth){
if (ns6||ie){
if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
tipobj.innerHTML=thetext
enabletip=true
return false
}
}

function positiontip(e){
if (enabletip){
var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;
//Find out how close the mouse is to the corner of the window
var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<tipobj.offsetWidth)
//move the horizontal position of the menu to the left by it's width
tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px"
else if (curX<leftedge)
tipobj.style.left="5px"
else
//position the horizontal position of the menu where the mouse is positioned
tipobj.style.left=curX+offsetxpoint+"px"

//same concept with the vertical position
if (bottomedge<tipobj.offsetHeight)
tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px"
else
tipobj.style.top=curY+offsetypoint+"px"
tipobj.style.visibility="visible"
}
}

function hideddrivetip(){
if (ns6||ie){
enabletip=false
tipobj.style.visibility="hidden"
tipobj.style.left="-1000px"
tipobj.style.backgroundColor=''
tipobj.style.width=''
}
}

document.onmousemove=positiontip;

 /***********************************************
 * Fixed ToolTip script- © Dynamic Drive (www.dynamicdrive.com)
 * This notice MUST stay intact for legal use
 * Visit http://www.dynamicdrive.com/ for full source code
 ***********************************************/
 
 var tipwidth='280px' //default tooltip width
 var tipbgcolor='lightyellow' //tooltip bgcolor
 var disappeardelay=250 //tooltip disappear speed onMouseout (in miliseconds)
 var vertical_offset="0px" //horizontal offset of tooltip from anchor link
 var horizontal_offset="-3px" //horizontal offset of tooltip from anchor link
 
 /////No further editting needed
 
 var es_ie4=document.all
 var ns6=document.getElementById&&!document.all
 
 if (es_ie4||ns6)
 document.write('<div id="fixedtipdiv" onmouseover="tooltip_continuar()" onmouseout="tooltip_terminar()" style="visibility:hidden;width:'+tipwidth+';background-color:'+tipbgcolor+'" ></div>')
 
 function getposOffset(what, offsettype){
 var totaloffset=(offsettype=="left")? what.offsetLeft : what.offsetTop;
 var parentEl=what.offsetParent;
 while (parentEl!=null){
 totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
 parentEl=parentEl.offsetParent;
 }
 return totaloffset;
 }
 
 
 function showhide(obj, e, visible, hidden, tipwidth){
 if (es_ie4||ns6)
 dropmenuobj.style.left=dropmenuobj.style.top=-500
 if (tipwidth!=""){
 dropmenuobj.widthobj=dropmenuobj.style
 dropmenuobj.widthobj.width=tipwidth
 }
 if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover")
 obj.visibility=visible
 else if (e.type=="click")
 obj.visibility=hidden
 }
 
 function iecompattest(){
 return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
 }
 
 function clearbrowseredge(obj, whichedge){
 var edgeoffset=(whichedge=="rightedge")? parseInt(horizontal_offset)*-1 : parseInt(vertical_offset)*-1
 if (whichedge=="rightedge"){
 var windowedge=es_ie4 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset+window.innerWidth-15
 dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
 if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
 edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth
 }
 else{
 var windowedge=es_ie4 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18
 dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
 if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure)
 edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight
 }
 return edgeoffset
 }
 
 function fixedtooltip(menucontents, obj, e, tipwidth){
 if (window.event) event.cancelBubble=true
 else if (e.stopPropagation) e.stopPropagation()
 clearhidetip()
 dropmenuobj=document.getElementById? document.getElementById("fixedtipdiv") : fixedtipdiv
 dropmenuobj.innerHTML=menucontents
  if (es_ie4||ns6){
  showhide(dropmenuobj.style, e, "visible", "hidden", tipwidth)
 dropmenuobj.x=getposOffset(obj, "left")
 dropmenuobj.y=getposOffset(obj, "top")
 dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px"
 dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight+"px"
 }
 }
 
 function hidetip(e){
 if (typeof dropmenuobj!="undefined"){
 if ((es_ie4||ns6) && tooltip_ocultar)
 dropmenuobj.style.visibility="hidden"
 }
 }
 
 function delayhidetip(){
 if (es_ie4||ns6)
 delayhide=setTimeout("hidetip()",disappeardelay)
 }
 
 function clearhidetip(){
 if (typeof delayhide!="undefined")
 clearTimeout(delayhide)
 }
 
 var tooltip_ocultar = true;
 
 function tooltip_continuar(){
 	tooltip_ocultar=false;
 }
 
 function tooltip_terminar(){
 	tooltip_ocultar=true;
 	delayhidetip()
 }