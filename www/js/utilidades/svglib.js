
function aviso_instalacion_svg()
{
	//es IE?
	if ((navigator.mimeTypes == null || navigator.mimeTypes.length == 0)) {
		    try{
		        var asv = new ActiveXObject("Adobe.SVGCtl");
		        return true;
		    }
		    catch(e){
		    }
		    alert("Para visualizar el esquema SVG se necesita instalar el plugin de Acrobat " +
		    		"(http://www.adobe.com/svg/viewer/install/auto)");
	} else {
		if (navigator.mimeTypes["image/svg+xml"] == null) {
		    alert("Para visualizar el esquema SVG se necesita instalar Firefox 1.5 o superior.");
    	}
	}

}

if (typeof toba != 'undefined') {
	toba.confirmar_inclusion('utilidades/svglib');
}