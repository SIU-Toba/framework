<?php

class pantalla_visor extends toba_ei_pantalla 
{
	public $analizador;
	
	function generar_layout()
	{
		parent::generar_layout();
?>
		<style type="text/css">
		.cuerpo, .ci-cuerpo {
			margin-top: 0px;
			margin-bottom: 0px;
		}
		pre {
			margin: 0;
			padding:0;
			margin-left:20px;
		}
		</style>
<?php
		if ($this->controlador->debe_mostrar_visor()) {
			$this->generar_html_fs();
		}
	}	
	
	function generar_html_fs()
	{
		if (! $this->controlador->existe_archivo_log()) {
			echo ei_mensaje("No hay logs registrados para el proyecto ".
							"<strong>{$this->controlador->get_proyecto()}</strong>");
			return;
		}			
		$seleccion = $this->controlador->seleccion;
		$niveles = toba::logger()->get_niveles();
		$niveles = array_reverse($niveles);		
		
		$res = $this->analizador->get_pedido($seleccion);
		$encabezado = $this->analizador->analizar_encabezado($res);
		
		//--- Opciones
		$selec = ($seleccion == 'ultima') ? "Última solicitud" : "Solicitud {$seleccion}";
		echo "<div>";
		echo "<span class='logger-proyecto' title='{$this->analizador->get_archivo_nombre()}'>";
		echo ucfirst($this->controlador->get_proyecto());
		echo "<span class='logger-selec'>$selec</span>";
		
		//--- Botones anterior/siguiente
		if ($seleccion != 1) {
			$this->generar_boton('anterior');			
		}
		if ($seleccion != 'ultima') {
			$this->generar_boton('siguiente');
		}
		echo "</span>";
		$check = toba_form::checkbox("con_encabezados", 0, 1, "ef-checkbox", " onclick=\"toggle_nodo(document.getElementById('logger_encabezados'))\"");
		echo "<label>$check Ver Encabezados</label><br>";

		$check = toba_form::checkbox("refresco_automatico", 0, 1, "ef-checkbox", " onclick=\"set_refresco_automatico(this.checked);\"");
		$edit = toba_form::text("refresco_lapso", 2000, false, 6, 6);
		echo "<label>$check Refresco Automático</label> <span id='div_lapso'>".$edit."ms</span><br>";
		echo "</div><hr style='clear:both' />";		
		
		echo "<div style='clear:both;width:100%;height:100%;overflow:auto;'>\n";
		list($detalle, $cant_por_nivel) = $this->generar_html_detalles($res);

		//--- Encabezado 
		echo "<ul id='logger_encabezados' style='display:none;list-style-type: none;padding: 0;margin: 0'>";		
		echo $this->generar_html_encabezado($res);
		echo "</ul>";

		//---- Niveles
		echo "<div style='clear:both;float:right;margin-left:10px;text-align:center;'>";
		echo "<strong>Niveles</strong>";
		echo "<ul class='logger-opciones'>";
		foreach ($niveles as $nivel) {
			$img = toba_recurso::imagen_proyecto('logger/'.strtolower($nivel).'.gif', true, null, null, "Filtrar el nivel: $nivel");
			$cant = ($cant_por_nivel[$nivel] != 0) ? "[{$cant_por_nivel[$nivel]}]" : "";
			echo "<li id='nivel_$nivel'><a href='#' onclick='mostrar_nivel(\"$nivel\")'>$img</a> ";
			echo "<span id='nivel_cant_$nivel'>$cant</span></li>\n";	
		}
		echo "</ul>";
		echo "</div>";
/*****	MOCKUP de la eleccion de un proyecto especifico		
 		echo toba_recurso::imagen_proyecto('logger/ver_texto.gif', true, 16, 16, "Ver el texto original del log");* 
		echo "<div style='clear:both;float:right;margin-left:10px;text-align:center;'><br>";		
		echo "<strong>Proyectos</strong>";
		echo "<ul id='logger_proyectos' class='logger-opciones'>";
		echo "<li>".toba_form::multi_select("opciones_proyectos", array(), array('referencia','toba'), 2)."</li>";
		echo "</ul>";		
		echo "</div>";*/
		
		//--- Detalles
		echo "<ol id='logger_detalle' style='list-style-type:none;padding:0;margin:0;margin-top:10px;'>";		
		echo $detalle;
		echo "</ol>\n";
		
		echo "</div>";
	}	
	

	function generar_html_encabezado($res)
	{
		$encabezado = $this->analizador->analizar_encabezado($res);
		$enc = "";
		//--- Encabezado		
		foreach ($encabezado as $clave => $valor) {
			$enc .= "<li><strong>".ucfirst($clave)."</strong>: $valor</li>\n";
		}
		$enc .= "<li><hr></li>";
		return $enc;
	}
	
	function generar_html_detalles($res)
	{
		$niveles = toba::logger()->get_niveles();
		$cuerpo = $this->analizador->analizar_cuerpo($res);
		$cant_por_nivel = array();
		foreach ($niveles as $nivel) {
			$cant_por_nivel[$nivel] = 0;
		}
		$detalle = '';
		foreach ($cuerpo as $linea) {
			//¿Es una sección?
			if (substr($linea['mensaje'], 0,10) == "[SECCION] ") {
				$linea['mensaje'] = substr($linea['mensaje'], 10);
				$img ='';
				$clase = "logger-seccion";
			//Es normal
			} else {
				$img = toba_recurso::imagen_proyecto('logger/'.strtolower($linea['nivel']).'.gif', true, null, null);
				$clase = "logger-normal";	
			}
			$detalle .= "<li class='$clase' nivel='{$linea['nivel']}' proyecto='{$linea['proyecto']}'>";
			$detalle .= "$img ";
			$detalle .= $this->txt2html($linea['mensaje']);
			$detalle .= "</li>";	
			$cant_por_nivel[$linea['nivel']]++;
		}
		return array($detalle, $cant_por_nivel);
	}

	
	function txt2html($txt)	
	{
		$txt = trim($txt);
		$texto_traza = "[TRAZA]";
		$pos_traza = strpos($txt, $texto_traza);
		$salto = strpos($txt, "\n", 0);		
		
		//¿Contiene una traza?		
		if ($pos_traza !== false) {
			$txt_anterior = htmlspecialchars(substr($txt, 0, $pos_traza));
			$txt_traza = trim(substr($txt, $pos_traza+strlen($texto_traza)));
			$txt = "$txt_anterior <span class='logger-traza' onclick='toggle_nodo(this.nextSibling)'>$texto_traza</span>$txt_traza";
		} elseif ($salto !== false) {
			//Los saltos (\n) dentro del mensaje se considera que viene un dump de algo			
			$txt = substr($txt,0,$salto)."<pre>".substr($txt, $salto)."</pre>";
		}
		return $txt;
	}		
	
	function extender_objeto_js() 
	{
		if (!$this->controlador->debe_mostrar_visor() || ! $this->controlador->existe_archivo_log()) {
			return;	
		}
		$niveles = toba::logger()->get_niveles();		
		$parametros = array();
//		$vinculo = toba::vinculador()->crear_autovinculo($parametros, array('servicio' => 'ejecutar'));
?>
			var ultima_mod ='<?php echo $this->controlador->timestamp_archivo();?>';
			var niveles = <?php echo toba_js::arreglo($niveles)?>;
			var niveles_actuales = {length: 0};
			var refresco_automatico = true;
			var consultando = false;

			<?php echo $this->objeto_js?>.evt__refrescar = function() {
				var callback =
				{
				  success: this.respuesta_refresco ,
				  failure: toba.error_comunicacion,
				  scope: this
				}
				var parametros = {'mtime': ultima_mod};
				var vinculo = vinculador.crear_autovinculo('ejecutar', parametros);
				conexion.asyncRequest('GET', vinculo, callback, null);
				return false;
			}
			
			<?php echo $this->objeto_js?>.respuesta_refresco = function(resp)
			{
				try {
					var partes = toba.analizar_respuesta_servicio(resp);
					//Se actualizo el logger?
					if (partes.length > 0) {
						toba.inicio_aguardar();
						ultima_mod = partes[0];
						document.getElementById('logger_encabezados').innerHTML = partes[1];
						document.getElementById('logger_detalle').innerHTML = partes[2];
						var cant = eval('(' + partes[3] + ')');
						refrescar_cantidad_niveles(cant);
						refrescar_detalle();
						setTimeout("toba.fin_aguardar()", 200);
					}
				} catch (e) {
					//alert(e);
				}
				consultando = false;				
			}
			
			function mostrar_nivel(nivel)
			{
				var li_nivel = document.getElementById('nivel_' + nivel);
				if (! niveles_actuales[nivel]) {
					niveles_actuales[nivel] = true;
					niveles_actuales.length++;
				} else {
					delete(niveles_actuales[nivel]);
					niveles_actuales.length--;
				}
				refrescar_niveles();
				refrescar_detalle();
			}
	
			function refrescar_niveles()
			{
				var mostrar_todos = (niveles_actuales.length == 0);			
				for (var i=0; i< niveles.length; i++) {
					var nivel_min = niveles[i].toLowerCase();
					var li_nivel = document.getElementById('nivel_' + niveles[i]);
					var src_actual = li_nivel.childNodes[0].childNodes[0].src;
					var diff = (mostrar_todos || niveles_actuales[niveles[i]]) ? '' : '_des';
					var src_nuevo = toba_proyecto_alias + '/img/logger/' + nivel_min + diff + '.gif';
					if (src_actual != src_nuevo) {
						li_nivel.childNodes[0].childNodes[0].src = src_nuevo;
					}
				}
			}
			
			function refrescar_detalle()
			{
				var mostrar_todos = (niveles_actuales.length == 0);
				var detalle = document.getElementById('logger_detalle');
				for (var i=0; i < detalle.childNodes.length; i++) {
					var nodo = detalle.childNodes[i];
					var nivel = nodo.attributes['nivel'].value;
					var debe_mostrar = (mostrar_todos || niveles_actuales[nivel]);
					if (debe_mostrar && nodo.style.display == 'none') {
						nodo.style.display = '';
					}
					if (!debe_mostrar && nodo.style.display == '') {
						nodo.style.display = 'none';	
					}
				}
			}			
			
			function refrescar_cantidad_niveles(cantidades)
			{
				for (var nivel in cantidades) {
					var cant = (cantidades[nivel] > 0) ? '[' + cantidades[nivel] + ']' : '';
					document.getElementById('nivel_cant_' + nivel).innerHTML = cant;
				}
			}
			
			function set_refresco_automatico(activar)
			{
				refresco_automatico = activar;
				document.getElementById('div_lapso').style.display = (activar) ? "" :"none";
			}
			
			function chequear_refresco()
			{
				if (refresco_automatico && !consultando) {
					consultando = true;
					toba.set_aguardar(false);
					<?php echo $this->objeto_js?>.evt__refrescar();
				}
				timer_refresco();
			}
			
			function timer_refresco()
			{
				var lapso = parseFloat(document.getElementById('refresco_lapso').value);
				var lapso = (lapso>0) ? lapso : 2000;
				setTimeout("chequear_refresco()", lapso);
			}
			set_refresco_automatico(document.getElementById('refresco_automatico').checked);
			timer_refresco();
<?php
	}
	
}

?>