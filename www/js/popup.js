var popup_elementos = new Array();
var popup_elementos_cant = 0;

function popup_abrir_item(url, indice, elemento_cod, elemento_desc, parametros_ventana)
{
	popup_elementos[popup_elementos_cant] = new Array(indice, elemento_cod, elemento_desc);
	//-- Seteo parametros de la ventana
	if(parametros_ventana !== null){
		vars = "width=" + parametros_ventana[0] + ",height=" + parametros_ventana[1] + ",scrollbars=" + parametros_ventana[2] +  ",dependent=yes";
	}else{
		vars = ""
	}
	//-- Abro la ventana
	if (!window.popup_hija){
		// No fue definida.
		popup_hija = window.open( url , 'popup_hija', vars);
		popup_hija.opener = window;
		popup_hija.focus();
	} else {
		// Ya fue definida.
		if(!popup_hija.closed){
			//Todavia esta abierta
			popup_hija.opener = window;
			popup_hija.location.href = url;
			popup_hija.focus();
		}else{
			popup_hija = window.open( url , 'popup_hija', vars);
		}
	}
	popup_elementos_cant++;
}

function popup_callback(indice, clave, desc)
{
	var i=0;
	var encontrado=false;

	while (i < popup_elementos_cant && !encontrado)
	{
		if (popup_elementos[i][0] == indice)
		{
			encontrado=true;
			popup_elementos[i][1].value = clave;
			if (popup_elementos[i][1].onchange)
				popup_elementos[i][1].onchange();
			popup_elementos[i][2].value = desc;	
		}			
		i++;
	}		

}
