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
			echo ei_mensaje('No hay logs registrados para el proyecto '.
							"<strong>{$this->controlador->get_proyecto()}</strong>");
			return;
		}			
		$seleccion = $this->controlador->s__seleccion;
		$niveles = toba::logger()->get_niveles();
		$niveles = array_reverse($niveles);		

		$res = $this->controlador->get_analizador()->get_pedido($seleccion);
		$encabezado = $this->controlador->get_analizador()->analizar_encabezado($res);			///CON ESTO PUEDO SACAR OPERACION, PROYECTO, IP, USUARIO ETC

		//--- Opciones
		$selec = ($seleccion == 'ultima') ? 'Última solicitud' : "Solicitud {$seleccion}";
		echo '<div>';
		echo "<span class='logger-proyecto' title='{$this->controlador->get_analizador()->get_archivo_nombre()}' style='text-align:right;'>";
		
		echo ucfirst($this->controlador->get_proyecto());


		echo "<span class='logger-selec'>$selec</span>";
		
		//--- Botones anterior/siguiente
		if ($seleccion != 1) {
			$this->generar_boton('anterior');			
		}
		if ($seleccion != 'ultima') {
			$this->generar_boton('siguiente');
			$this->generar_boton('ultima');
		}
		echo '</span>';
		echo "<br><div id='logger_info_operacion'>";
		echo $this->generar_html_info_operacion($res);
		echo '</div>';

		$valor_check = 0;
		if ($this->controlador->get_estado_encabezados() === true) {
			$valor_check = 1;
		}
		
		$check = toba_form::checkbox('con_encabezados', $valor_check, 1, 'ef-checkbox', " onclick=\"{$this->objeto_js}.evt__con_encabezados__click(this)\" ");
		echo "<label>$check Ver Encabezados</label><br>";
		echo "</div><hr style='clear:both' />";

		echo "<div style='clear:both;width:100%;overflow:auto;'>\n";
		list($detalle, $cant_por_nivel) = $this->generar_html_detalles($res);

		$display_encabezados = 'none';
		if ($this->controlador->get_estado_encabezados() === true) {
			$display_encabezados = '';
		}

		//--- Encabezado 
		echo "<ul id='logger_encabezados' style='display:$display_encabezados;list-style-type: none;padding: 0;margin: 0'>";
		echo $this->generar_html_encabezado($res);
		echo '</ul>';

		//---- Niveles
		echo "<div style='clear:both;float:right;margin-left:10px;text-align:center;'>";
		echo '<strong>Niveles</strong>';
		echo "<ul class='logger-opciones'>";
		foreach ($niveles as $nivel) {
			$img = toba_recurso::imagen_proyecto('logger/'.strtolower($nivel).'.gif', true, null, null, "Filtrar el nivel: $nivel");
			$cant = ($cant_por_nivel[$nivel] != 0) ? "[{$cant_por_nivel[$nivel]}]" : '';
			echo "<li id='nivel_$nivel'><a href='#' onclick='mostrar_nivel(\"$nivel\")'>$img</a> ";
			echo "<span id='nivel_cant_$nivel'>$cant</span></li>\n";	
		}
		echo '</ul>';
		echo '</div>';

		$proyecto_actual = $this->controlador->get_proyecto();
		$mostrar = $this->controlador->get_seleccion_modo_detalle();
		$lista_valida = array($proyecto_actual => ucfirst($proyecto_actual), 'toba' => 'Nucleo', 'no_seteado' => 'Todos');
 		//echo toba_recurso::imagen_proyecto('logger/ver_texto.gif', true, 16, 16, "Ver el texto original del log");
		echo "<div style='clear:both;float:right;margin-left:10px;text-align:center;'><br>";		
		echo '<strong>Mostrar mensajes</strong>';
		echo "<ul id='logger_proyectos' class='logger-opciones'>";
		echo '<li>'.toba_form::select('opciones_proyectos', $mostrar, $lista_valida,  null, "onchange='{$this->objeto_js}.mostrar_proyecto()'").'</li>';
		echo '</ul>';		
		echo '</div>';
		
		//--- Detalles
		echo "<ol id='logger_detalle' style='list-style-type:none;padding:0;margin:0;margin-top:10px;'>";		
		echo $detalle;
		echo "</ol>\n";		
		echo '</div>';
	}	
	
	function generar_html_info_operacion($res)
	{
		$encabezado = $this->controlador->get_analizador()->analizar_encabezado($res);
		$string = '';
		if (isset($encabezado['operacion'])) {			
			$string .= "<span id='div_lapso' style='font-weight:bold;font-size:18px;'>{$encabezado['operacion']}</span><br>";
		}
		
		if (isset($encabezado['fecha'])) {
			$fecha_ref = new toba_fecha();
			$fecha_log = new toba_fecha();
			$fecha_log->set_timestamp(strtotime($encabezado['fecha']));
			
			$fecha = $fecha_log->get_timestamp_pantalla(); 
			if ($fecha_ref->es_igual_que($fecha_log)) {
				$fecha = 'Hoy  ' . date('H:i:s', strtotime($encabezado['fecha']));
			}		
			$string .= "<span id='div_lapso' style='font-weight:bold;font-size:12px;'>$fecha</span><br>";
		}

		return $string;
	}

	function generar_html_encabezado($res)
	{
		$encabezado = $this->controlador->get_analizador()->analizar_encabezado($res);
		$enc = '';
		//--- Encabezado		
		foreach ($encabezado as $clave => $valor) {
			$enc .= '<li><strong>'.ucfirst($clave)."</strong>: $valor</li>\n";
		}
		$enc .= '<li><hr></li>';
		return $enc;
	}
	
	function generar_html_detalles($res)
	{
		$niveles = toba::logger()->get_niveles();
		$cuerpo = $this->controlador->get_analizador()->analizar_cuerpo($res);
		$cant_por_nivel = array();
		foreach ($niveles as $nivel) {
			$cant_por_nivel[$nivel] = 0;
		}
		$detalle = '';
		foreach ($cuerpo as $linea) {
			//¿Es una sección?
			if (substr($linea['mensaje'], 0, 10) == '[SECCION] ') {
				$linea['mensaje'] = substr($linea['mensaje'], 10);
				$img = '';
				$clase = 'logger-seccion';			
			} else {	//Es normal
				$img = toba_recurso::imagen_proyecto('logger/'.strtolower($linea['nivel']).'.gif', true, null, null);
				$clase = 'logger-normal';	
			}
			$detalle .= "<li class='$clase' nivel='{$linea['nivel']}' proyecto='{$linea['proyecto']}'>";
			$detalle .= "$img ";
			$detalle .= $this->txt2html($linea['mensaje']);
			$detalle .= '</li>';	
			$cant_por_nivel[$linea['nivel']]++;
		}
		return array($detalle, $cant_por_nivel);
	}

	
	function txt2html($txt)	
	{
		$txt = trim($txt);
		$texto_traza = '[TRAZA]';
		$pos_traza = strpos($txt, $texto_traza);
		$salto = strpos($txt, "\n", 0);		
		
		//¿Contiene una traza?		
		if ($pos_traza !== false) {
			$txt_anterior = htmlspecialchars(substr($txt, 0, $pos_traza));
			$txt_traza = trim(substr($txt, $pos_traza + strlen($texto_traza)));
			$txt = "$txt_anterior <span class='logger-traza' onclick='toggle_nodo(this.nextSibling)'>$texto_traza</span><span class='logger-traza-detalle' style='display:none;'>$txt_traza</span>";
		} elseif ($salto !== false) {
			//Los saltos (\n) dentro del mensaje se considera que viene un dump de algo			
			$txt = substr($txt, 0, $salto).'<pre>'.substr($txt, $salto).'</pre>';
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
?>
			var ultima_mod ='<?php echo $this->controlador->timestamp_archivo(); ?>';
			var niveles = <?php echo toba_js::arreglo($niveles); ?>;
			var niveles_actuales = {length: 0};

			<?php echo $this->objeto_js; ?>.evt__refrescar = function() {
				this.ajax('get_datos_logger', ultima_mod, this, this.respuesta_refresco);
				return false;
			}
			
			<?php echo $this->objeto_js; ?>.respuesta_refresco = function(resp)
			{
				if (resp != null) {
					toba.inicio_aguardar();				
					ultima_mod = resp['ultima_mod'];
					document.getElementById('logger_encabezados').innerHTML = resp['encabezado'];
					document.getElementById('logger_detalle').innerHTML = resp['detalle'];
					document.getElementById('logger_info_operacion').innerHTML = resp['info_op'];
					refrescar_cantidad_niveles(resp['cant_por_nivel']);		
					refrescar_detalle();
					this.mostrar_proyecto(true);
					setTimeout("toba.fin_aguardar()", 200);					
				}
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

			<?php echo $this->objeto_js; ?>.mostrar_proyecto = function (inicial)
			{
				obj_combo = document.getElementById('opciones_proyectos');
				valor = obj_combo.options[obj_combo.selectedIndex].value;

				//Informo el modo de seleccion para que sea recordado entre pedidos de pagina
				if (!inicial){
					this.ajax('set_modo_detalle_seleccionado', valor, this, this.dump_response);
				}

				//Refresco la visualizacion del detalle
				var mostrar_todos = (valor == 'no_seteado');
				var detalle = document.getElementById('logger_detalle');
				for (var i=0; i < detalle.childNodes.length; i++) {
					var nodo = detalle.childNodes[i];
					var pr = nodo.attributes['proyecto'].value;
					var debe_mostrar = (mostrar_todos || (pr == valor));
					if (debe_mostrar && nodo.style.display == 'none') {
						nodo.style.display = '';
					}
					if (!debe_mostrar && nodo.style.display == '') {
						nodo.style.display = 'none';
					}
				}
			}

			function refrescar_niveles()
			{
				var mostrar_todos = (niveles_actuales.length == 0);			
				for (var i=0; i < niveles.length; i++) {
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

			<?php echo $this->objeto_js; ?>.dump_response = function(resp){
					//Esta funcion esta para desechar la respuesta, la cual no existe
			}

			<?php echo $this->objeto_js; ?>.evt__con_encabezados__click = function(obj){
				toggle_nodo(document.getElementById('logger_encabezados'));
				this.ajax('set_estado_encabezados', obj.checked, this, this.dump_response);
				return false;
			}

			<?php echo $this->objeto_js; ?>.mostrar_proyecto(true);
<?php
	}
	
}

?>