<? include_once("lib/html_cabecera.php")?>
<SCRIPT language='JavaScript'>
<?	if($this->mostrar_contenido) echo $this->contenido->generar_array_js_graf_fc() ?>

	function obtenerXLS(grupo,tabla){
		alert("Se solicito un XLS\n GRUPO:"+grupo+"\nTABLA: "+tabla);
	}
	
	function ordenar(sentido,columna){
		alert(sentido+" | "+columna);
	}

	function popup_grafico(sentido,indice,clave){
		var qs,pagina;
		pagina= graf_fc[sentido][indice] + "&clave=" + clave;
		//prompt("URL",pagina);
    	if (!window.window2) {
	        // has not yet been defined
    	    window2 = window.open(pagina ,'grafico','scrollbars=no,dependent=yes');
	    }
    	else {
	        // has been defined
    	    if (!window2.closed) {
        	    // still open
				window2.location.href=pagina;
	            window2.focus();
    	    }
        	else {
	            window2 = window.open(pagina ,'grafico','scrollbars=no,dependent=yes');
    	    }
	    }	
	}
	
	function invertir_grafico(grafico,tabla){
		var sentido;
		sentido = 0;
		alert("apl_graficador.php?" + graf_fc[sentido][tabla] + " -- GRAFICO " + grafico );
	}

//----------------------------< NAVEGACION >------------------------------------

<? echo $this->generar_array_vinculos() ?>
var parametro = "vacio";
var par_id = "desconocido";

function drillDown(par){
	parametro = par;//Dejo guardado el parametro seleccionado
	toggleBox('navegar',1);
}
	
function toggleBox(szDivID, iState) // 1 visible, 0 hidden
{
    if(document.layers)	   //NN4+
    {
       document.layers[szDivID].visibility = iState ? "show" : "hide";
    }
    else if(document.getElementById)	  //gecko(NN6) + IE 5+
    {
        var obj = document.getElementById(szDivID);
        obj.style.visibility = iState ? "visible" : "hidden";
    }
    else if(document.all)	// IE 4
    {
        document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
    }
}

function navegar_zoom(indice){
	toggleBox('navegar',0);
	alert("ZOOM: " + array_vinculos[indice]+"&"+par_id+"="+parametro);
}

function navegar_popup(indice){
	toggleBox('navegar',0);
	alert("POPUP: " + array_vinculos[indice]+"&"+par_id+"="+parametro);
}

//-----------------------------------------------------------------------------
</SCRIPT>
<? 
	if(inspector::posee_habilidad("editar-hoja")){
	global $canal;
?>
			<table border="0" cellspacing="0" cellpadding="4">
          	<tr><td>
			<table border="0" cellspacing="0" cellpadding="2" class='form-2'>
          	<tr> 
				<td class='opcion-1'>
					<a href='<? echo $canal->generar_vinculo("siu_adm_hoja-ed",array("hoja"=>$this->identificador)) ?>'><img src="<? echo img_global ?>e.gif" alt="Editar HOJA de DATOS" border="0"></a>
				</td>
        		<td class='columna-titulo-2-b' >
					<a href='<? echo $canal->generar_vinculo("siu_adm_hoja-cargar",array("hoja"=>$this->identificador,"formato"=>"wddx")) ?>'><img src="<? echo img_global ?>x.gif" alt="Exportar definicion en formato WDDX <? echo "\n"?>(Web Distributed Data Exchange)" border="0"></a>
				</td>
		    </tr>
    		</table>		
				</td>
		    </tr>
    		</table>		
<?
	}
?>
<table width="100%" border="0" cellspacing="7" cellpadding="0">
  <tr>
    <td align="center"><img src="img/nulo.gif" width="1" height="1"></td>
  </tr>
  <tr> 
    <td>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
   	<tr> 
        <td width="10%" class="hoja-titulo"><? echo $this->c_definicion['titulo'] ?></td>
   	</tr> 
    </table>
	</td>
  </tr>
  <tr> 
    <td align="center"><img src="img/nulo.gif" width="1" height="1"></td>
  </tr>
<form name='formulario' method='post' action=''>
  <tr> 
    <td align="center"><? echo $this->generar_filtro(); ?></td>
  </tr>
</from>
  <tr> 
    <td align="center"><img src="img/nulo.gif" width="1" height="1"></td>
  </tr>
<? if ($this->mensaje<>""){ ?>
  <tr> 
    <td align="center"><? echo $this->mensaje; ?></td>
  </tr>
<? }
if ($this->mostrar_contenido){ ?>
  <tr> 
    <td align="center"><? echo $this->html; ?></td>
  </tr>
<? } ?>
  <tr>
    <td align="center"><img src="img/nulo.gif" width="1" height="1"></td>
  </tr>
</table>
<? echo $this->generar_popup_vinculos() ?>
<? include_once("lib/html_pie.php")?>