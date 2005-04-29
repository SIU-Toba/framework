/*

  Resize Textarea 0.1
  Original coding by Raik Juergens
  Contact: borstel33@web.de

*/
var resizeTa =
{
  loaded: null,
  TAlength: 0,
  elem: new Array(),

  init: function (){
    if (resizeTa.loaded)
      return;
    else{
        resizeTa.loaded = true;
		resizeTa.pageload();		 
	} 
  },

agregar_elemento: function (elemento) {
	resizeTa.elem[elemento.id] = true;
	resizeTa.elem.length++;
},

pageload: function (aEvent){
	mediciones.marcar('Inicio ResizeTA');
	resizeTa.doc = document;
	resizeTa.TA = document.getElementsByTagName('TEXTAREA');
    resizeTa.TAlength = resizeTa.TA.length;
    if(resizeTa.TAlength == 0 || resizeTa.elem.length == 0){
        return;
    }else{
    resizeTa.rootElem = document.body;
    var i = resizeTa.TAlength;
        while(i--){
			if (resizeTa.elem[resizeTa.TA[i].id]) {
	           resizeTa.newdiv('5' ,'1' ,'gripH_',i,'w');
	           resizeTa.newdiv('2' ,'5' ,'gripV_',i,'n');
	           resizeTa.newdiv('10','10','gripX_',i,'se');
			}
		}
		mediciones.marcar('Flechas cargadas');		
        resizeTa.newdiv('0','0','showCursor','','w');
		mediciones.marcar('Cursor cargado');
        CursorDiv = resizeTa.doc.getElementById('showCursor');
		CursorDiv.mousedown = '';
        CursorDiv.style.left = '0px';
        CursorDiv.style.top  = '0px';
        resizeTa.posdivs();
		window.resize = resizeTa.posdivs;
	}
	mediciones.marcar('Fin ResizeTA');	
	logger(mediciones.resultados());
},

newdiv: function (w,h,id,nr,cu){
	mediciones.marcar('Crear DIV');		
	if (ie) {	//ATENCION: CAMBIAR!!
		var grip = document.createElement("<div id='" + id+nr + "' style='position:absolute;width:"+w+"px;height:"+h+"px;cursor:"+cu+"-resize' onmousedown='return resizeTa.activate()'></div>");	
	}
	else {		
	    var grip = resizeTa.doc.createElement("div");
		grip.setAttribute("ID", id+nr);
		grip.id = id+nr;
		grip.setAttribute("STYLE", "position:absolute;width:"+w+"px;height:"+h+"px;cursor:"+cu+"-resize");
		grip.onmousedown = resizeTa.activate;
    }
	resizeTa.rootElem.appendChild(grip);
},

getposition: function (i){
	var curElem = resizeTa.TA[i];
	var curX = curElem.offsetLeft;
	while (curElem.offsetParent) {
        curX += curElem.offsetParent.offsetLeft;
        curElem = curElem.offsetParent;
	}
	curElem = resizeTa.TA[i];
	var curY = curElem.offsetTop;
	while (curElem.offsetParent) {
        curY += curElem.offsetParent.offsetTop;
        curElem = curElem.offsetParent;
	}
    return [curX,curY]
},

posdivs: function (){
    var k = resizeTa.TAlength;
    while(k--){
		if (resizeTa.elem[resizeTa.TA[k].id]) {
	        curPos = resizeTa.getposition(k);
	        resizeTa.doc.getElementById('gripH_'+k).style.left   = curPos[0]+resizeTa.TA[k].offsetWidth -3 + "px";
	        resizeTa.doc.getElementById('gripH_'+k).style.top    = curPos[1]                               + "px";
	        resizeTa.doc.getElementById('gripH_'+k).style.height =           resizeTa.TA[k].offsetHeight-8 + "px";
	        resizeTa.doc.getElementById('gripV_'+k).style.left   = curPos[0]                               + "px";
	        resizeTa.doc.getElementById('gripV_'+k).style.top    = curPos[1]+resizeTa.TA[k].offsetHeight-3 + "px";
	        resizeTa.doc.getElementById('gripV_'+k).style.width  =           resizeTa.TA[k].offsetWidth -8 + "px";
	        resizeTa.doc.getElementById('gripX_'+k).style.left   = curPos[0]+resizeTa.TA[k].offsetWidth -8 + "px";
	        resizeTa.doc.getElementById('gripX_'+k).style.top    = curPos[1]+resizeTa.TA[k].offsetHeight-8 + "px";
		}
	}
},

activate: function (e){
	if (!e) var e = window.event
	if (!e.target) e.target = e.srcElement;

	resizeTa.doc = e.target.ownerDocument;
	resizeTa.TA = resizeTa.doc.getElementsByTagName('TEXTAREA');
	CursorDiv = resizeTa.doc.getElementById('showCursor');
	resizeTa.TAlength = resizeTa.TA.length;
	var curTargetId = e.target.getAttribute('ID').split("_");
    curTarget = curTargetId[0];
    curTA_Nr = parseInt(curTargetId[1]);
	resizeTa.doc.onmouseup = resizeTa.deactivate;
    switch(curTarget){
        case "gripH": resizeTa.doc.onmousemove = resizeTa.resizeta_h; break;
        case "gripV": resizeTa.doc.onmousemove = resizeTa.resizeta_v; break;
        case "gripX": resizeTa.doc.onmousemove = resizeTa.resizeta_x; break;
	}
    CursorDiv.style.width = resizeTa.rootElem.offsetWidth + "px";
	CursorDiv.style.height = resizeTa.rootElem.offsetHeight + "px";
	CursorDiv.style.cursor = e.target.style.cursor;
},

deactivate: function (){
    resizeTa.doc.onmouseup = '';
   	resizeTa.doc.onmousemove=''; 
    CursorDiv.style.width = '0px';
    CursorDiv.style.height = '0px';
    resizeTa.posdivs();
},

resizeta_h: function (e){
	try {
		if (!e) var e = window.event
		curPos = resizeTa.getposition(curTA_Nr);
		pos = resizeTa.positions(e);	
		resizeTa.TA[curTA_Nr].style.width = pos[0] - curPos[0] + "px";
	} catch(exc) {
	}		
},

resizeta_v: function (e){
	try {
		if (!e) var e = window.event
		curPos = resizeTa.getposition(curTA_Nr);
		pos = resizeTa.positions(e);	
		resizeTa.TA[curTA_Nr].style.height = pos[1] - curPos[1] + "px";
	} catch(exc) {
	}
},

resizeta_x: function (e){
	try {
		if (!e) var e = window.event
		curPos = resizeTa.getposition(curTA_Nr);
		pos = resizeTa.positions(e);
		resizeTa.TA[curTA_Nr].style.width = pos[0] - curPos[0] + 2 + "px";
		resizeTa.TA[curTA_Nr].style.height = pos[1] - curPos[1] + 2 + "px";
	} catch(exc) {
	}
},

positions: function(e) {
		if (e.pageX || e.pageY)
			return [e.pageX, e.pageY];
		if (e.clientX || e.clientY)
			return [e.clientX + document.body.scrollLeft, e.clientY + document.body.scrollTop];
	}	
}
addEvent(window, 'onload', 'resizeTa.init()');
