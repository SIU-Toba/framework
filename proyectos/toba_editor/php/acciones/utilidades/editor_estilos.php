<?php
	$plantilla = toba::memoria()->get_parametro('plantilla');
	$escapador = toba::escaper();
	if (isset($_POST['elem_nueva_plantilla'])) {
		$archivo_css = toba::instalacion()->get_path().'/www/css/'.apex_proyecto_estilo.'.css';
		$texto_css = $_POST['elem_nueva_plantilla'];
		$texto_css = $escapador->escapeCss(str_replace(';', ";\n\t", $texto_css));
		file_put_contents($archivo_css, $texto_css);
	}
?>
<form name="editor_estilos" method="post" action="">	
<div style='padding-left: 5px;font-size: 10px;'>
	<fieldset style='padding: 5px'>
	<legend>Opciones</legend>
		<label for="elem_solo_item"><input id='elem_solo_item' type="checkbox" class="ef-checkbox" checked disabled> Aplicar a todo el proyecto </label><br>
		<label for="elem_sin_form"><input id='elem_sin_form' type="checkbox" class="ef-checkbox"  onClick="cambiar_formularios();"> Deshabilitar formularios</label><br>
		<label for="elem_sin_links"><input id='elem_sin_links' type="checkbox" class="ef-checkbox" disabled> Deshabilitar links</label>	<br>
	</fieldset>

	<fieldset style='padding: 5px; text-align:center;'>
		<legend>Editor</legend>
		<div id='elem_nombre_clase' style='text-align:center; font-weight: bold;'></div>
			<textarea id="elem_editor" cols="50" rows="15" class='ef-textarea' disabled>
			</textarea>
		<br>
	<input id='elem_aplicar' type='button' value='Vista Previa' onClick='aplicar()' class='ef-boton' disabled>
	&nbsp;	&nbsp;	&nbsp;
	<input id='elem_aceptar' type='button' value='Aceptar' onClick='aplicar(); aceptar();' class='ef-boton' disabled>	
	<input id='elem_cancelar' type='button' value='Cancelar' onClick='cancelar()' class='ef-boton' disabled>	
	&nbsp;	&nbsp;	&nbsp;
	<input id='elem_refrescar' type='button' value='Refrescar' onClick='refrescar()' class='ef-boton'>		
	</fieldset>
	<br>
	<fieldset  style='padding: 5px'>
	<legend>Estilos modificados</legend>
		<div id="elem_lista_modificadas"></div><br>
		<div style="text-align:center">
			<input  name="elem_nueva_plantilla" id="elem_nueva_plantilla" type="hidden" value="holasaa">
			<input id='elem_aplicar_todo' type='button' value='Aplicar Todos' onClick='aplicar_todo()' class='ef-boton' disabled>			
			<input id='elem_borrar_todo' type='button' value='Borrar Todos' onClick='borrar_todo()' class='ef-boton' disabled>
			&nbsp;	&nbsp;	&nbsp;
			<input id='elem_guardar' type='button' value='Guardar...' onClick='guardar();' class='ef-boton' disabled>	
		</div>		
	</fieldset>
	<fieldset  style='padding: 5px'><br>
	<legend>Ayuda</legend>	
	<ul>
		<li>Sólo funciona para IE (por ahora).</li>
		<li>Hacer click en el sector donde se quiere cambiar los estilos.</li>
		<li>Los sectores que se permiten editar son sólo aquellos que contienen estilos de la plantilla predeterminada del proyecto.</li>
		<li>Se puede seguir navegando por los items sin perder los cambios realizados (hay que 'Aplicar Todos' para cada página nueva).</li>
		<li>Una vez guardada la nueva plantilla debe refrescar el frame principal.</li>
		<li>Para desechar los cambios una vez guardada la plantilla, usar SVN:Revert sobre el archivo css del proyecto.</li>
	</ul>
	En <strong>BETA</strong>, usar bajo propio riesgo :P
	</fieldset>	
</div>
<!-- <link rel="stylesheet" href="csseditor/style/css.css" type="text/css" />
	 <script type="text/javascript" src="csseditor/javascript/css.js"></script> -->
<script language="javascript" type="text/javascript">
	var frame_editado = top.<?php echo apex_frame_centro; ?>; 
	
	//----Recorrido del DOM
	function recorrer_nodo(nodo)
	{
		if (nodo.className) 
			nodo.onclick = elemento_cambiar_comportamiento;
		for (var i=0; i < nodo.childNodes.length; i++) {
			recorrer_nodo(nodo.childNodes[i])
		}
	}		
	function elemento_cambiar_comportamiento(e)
	{
		frame_editado.window.event.cancelBubble = true;
		var elemento = frame_editado.window.event.srcElement;
		var clase = elemento.className;
		
		if (puede_modificar() && clase != '')
		{		
			editado_recuperar_estado_anterior();
			elemento_editado = elemento;
			editado_guardar_estado();
			elemento.style.border = "solid red 4px";
			cargar_clase(clase);
		}
	}
	
	//----EDITADO
	var elemento_editado;
	var borde_anterior;	
	function elemento_estilo(el,styleProp)
	{
		var x = el;
		if (frame_editado.window.getComputedStyle)
			var y = frame_editado.window.getComputedStyle(x,null).getPropertyValue(styleProp);
		else if (x.currentStyle)
			var y = eval('x.currentStyle.' + styleProp);
		return y;
	}
	function editado_guardar_estado()
	{
		borde_anterior = new Object();
		borde_anterior['borderStyle'] = elemento_estilo(elemento_editado, 'borderStyle');
		borde_anterior['borderWidth'] = elemento_estilo(elemento_editado, 'borderWidth');
		borde_anterior['borderColor'] = elemento_estilo(elemento_editado, 'borderColor');
	}
	function editado_recuperar_estado_anterior()
	{
		if (elemento_editado) {
			elemento_editado.style.borderStyle = borde_anterior['borderStyle'];
			elemento_editado.style.borderWidth = borde_anterior['borderWidth'];
			elemento_editado.style.borderColor = borde_anterior['borderColor'];								
		}
	}
	
	//----EDITOR	
	var clases_modificadas = new Array();
	var url_editada = '';
	var plantilla;	
	var estado_formularios = new Array();	
	var clase_cargada;
	
	function cambiar_formularios()
	{
		refrescar();
		var desactivado = document.getElementById('elem_sin_form').checked;
		if (!desactivado) {
			for (var i = 0; i<frame_editado.document.forms.length; i++)	{
				if (estado_formularios[i])
					frame_editado.document.forms[i].onsubmit = estado_formularios[i];
			}
		}
		else {  //Desactivar
			for (var i = 0; i<frame_editado.document.forms.length; i++)	{
				estado_formularios[i] = frame_editado.document.forms[i].onsubmit;
				frame_editado.document.forms[i].onsubmit = function() {return false};
			}
		}
	}	 
		
	function cargar_plantilla() {
		for (i =0; i < frame_editado.document.styleSheets.length; i++) {
			if (frame_editado.document.styleSheets[i].href == '<?php echo $escapador->escapeJs($plantilla); ?>')
				plantilla = frame_editado.document.styleSheets[i];
		}
	}
	
	function puede_modificar()
	{
		return true;
	}
	function modificar_clase() {
	    var values = getStyle();
	    var editor = document.all["editor"]; 
	    
	    nuevo_valor = "";
	    for (i = 0; i < values.length; i++) {
	        var splitted = values[i].split("=");
	        nuevo_valor += splitted[0] + ": " + splitted[1] + ";\n";
	    }
	    editor.value = nuevo_valor;
	}
	function cargar_clase(clase)
	{
		refrescar();	
		clase_cargada = clase;
		if (clases_modificadas[clase]) {
			texto_css = clases_modificadas[clase]; 
		}
		else { //Hay que salir a buscarla
			for(j = 0; plantilla.rules.length > j; j++) {
				if(plantilla.rules[j].selectorText.toLowerCase().indexOf(clase) > -1) {
					var texto_css = plantilla.rules[j].style.cssText.toLowerCase();
/*					var reglas_texto = texto_css.split(";");
					var nuevas_reglas = new Array()
					for (i = 0; i < reglas_texto.length; i++)
					{
						var regla = reglas_texto[i].split(': ');
						nuevas_reglas.push(trim(regla[0]) + '=' + trim(regla[1]));
					}
					setStyle(nuevas_reglas);*/
				}
		   } 
		 }
		document.getElementById('elem_nombre_clase').innerHTML = clase;
		document.getElementById('elem_editor').value = texto_css.reemplazar('; ', ';\n');
		document.getElementById('elem_aplicar').disabled = false;
		document.getElementById('elem_aceptar').disabled = false;		
		document.getElementById('elem_cancelar').disabled = false;
		document.getElementById('elem_editor').disabled = false;
	   
	}
	function aplicar_clase(clase, valor)
	{
			for(j = 0; plantilla.rules.length > j; j++)
			{
				if(plantilla.rules[j].selectorText.toLowerCase().indexOf(clase) > -1)
					plantilla.rules[j].style.cssText = valor;
		   } 	
	}
	function aplicar()
	{
		refrescar();	
	    var editor = document.getElementById('elem_editor');
	    if (clase_cargada)
			aplicar_clase(clase_cargada, editor.value);
	}
	
	function cancelar()
	{
		document.getElementById('elem_aplicar').disabled = true;
		document.getElementById('elem_aceptar').disabled = true;		
		document.getElementById('elem_cancelar').disabled = true;
		document.getElementById('elem_editor').value = '';
		document.getElementById('elem_editor').disabled = true;
		editado_recuperar_estado_anterior();
		document.getElementById('elem_nombre_clase').innerHTML ='';
		clase_cargada = null;
	}
	
	function aceptar()
	{
		refrescar();	
	    var editor = document.getElementById('elem_editor');
	    if (clase_cargada)
	    {
			clases_modificadas[clase_cargada] = editor.value;
	    }
		mostrar_clases_modificadas();
	}
	
	function borrar_clase(clase)
	{
		delete(clases_modificadas[clase]);
		mostrar_clases_modificadas();
	}

	function mostrar_clases_modificadas()
	{
		var img_ver = '<img src="<?php echo toba_recurso::imagen_toba('doc.gif'); ?>" border=0/>';
		var img_borrar = '<img src="<?php echo toba_recurso::imagen_toba('borrar.gif'); ?>" border=0/>';
		var html = '<ul>';
		var hay_uno = false;
		for (clase in clases_modificadas)
		{
			hay_uno = true;
			ver = ' <a href="javascript: cargar_clase(\'' + clase + '\');">' +  clase + '</a> ';
			borrar = ' <a href="javascript: borrar_clase(\'' + clase + '\');">' + img_borrar + '</a> ';			
			html += '<li>' +  borrar + ver +'</li>';
		}
		html += '</ul>';
		document.getElementById('elem_lista_modificadas').innerHTML = html;
		document.getElementById('elem_guardar').disabled = !hay_uno;
		document.getElementById('elem_borrar_todo').disabled = !hay_uno;
		document.getElementById('elem_aplicar_todo').disabled = !hay_uno;						
	}
	function mostrar_ayuda()
	{

	}
	
	function refrescar()
	{
		if (url_editada != frame_editado.window.location.href)
		{
			url_editada = frame_editado.window.location.href;
			elemento_editado = null;
			cargar_plantilla();
			recorrer_nodo(frame_editado.document.body);
			cancelar();
			mostrar_ayuda();
		}			
	}
	function borrar_todo()
	{
		for (clase in clases_modificadas) {
			borrar_clase(clase);
		}
	}
	function aplicar_todo()
	{
		for (clase in clases_modificadas) {
			aplicar_clase(clase, clases_modificadas[clase]);
		}
	}
	function guardar()
	{
		document.getElementById('elem_nueva_plantilla').value = plantilla.cssText;
		document.forms['editor_estilos'].submit();
	}
	//--- INICIO
	refrescar();	
</script>
<!-- <div id="css" class="editor">Cargando el editor...</div> 
<script language="javascript" type="text/javascript">
	addChangesListener(modificar_clase);
	insertEditor(css)
</script>
-->
</form>	