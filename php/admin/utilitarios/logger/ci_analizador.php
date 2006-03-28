<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//--------------------------------------------------------------------

class ci_analizador extends objeto_ci
{
	protected $opciones;
	protected $seleccion;
	protected $archivo;
	protected $cambiar_pantalla = false;
	
	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = 'opciones';
		$estado[] = 'archivo';
		$estado[] = 'seleccion';
		return $estado;	
	}
	
	protected function get_pantalla_inicial()
	{
		if (isset($this->seleccion)) {
			return "visor";
		}
		return parent::get_pantalla_inicial();		
	}	
	
	function get_pantalla_actual()
	{
		if ($this->cambiar_pantalla) {
			return "visor";	
		}
		return parent::get_pantalla_actual();
	}
	
	/**
	 * @todo Se desactiva el logger porque no corre como proyecto toba sino como el de la aplicacion
	 * 		Cuando el admin sea un proyecto hay que sacar la desactivación
	 */
	function evt__inicializar()
	{
		toba::get_logger()->desactivar();	
		if (!isset($this->opciones)) {
			$this->opciones['proyecto'] = toba::get_hilo()->obtener_proyecto();	
			$this->opciones['fuente'] = 'fs';
			$this->archivo = $this->get_logger()->directorio_logs()."/sistema.log";
			$this->seleccion = 'ultima';
		}
	}
	
	function obtener_html_dependencias()
	{
		parent::obtener_html_dependencias();
?>
		<style type="text/css">
		.cuerpo, .ci-cuerpo {
			margin-top: 0px;
			margin-bottom: 0px;
		}
		</style>
<?php
		if ($this->get_pantalla_actual() == 'visor' && isset($this->seleccion)) {
			if (isset($this->opciones['proyecto']) && isset($this->opciones['fuente'])) {
				if ($this->opciones['fuente'] == 'db') {
					$this->obtener_html_db();
				} elseif ($this->opciones['fuente'] == 'fs') {
					$this->obtener_html_fs();
				}
			}
		}
	}
	
	function obtener_html_db()
	{
		echo ei_mensaje("El logger por base de datos no está implementado");
	}
	
	function servicio__ejecutar()
	{
		$res = $this->obtener_pedido($this->seleccion);
		$encabezado = $this->obtener_html_encabezado($res);
		list($detalle, $cant_por_nivel) = $this->obtener_html_detalles($res);
		$anterior_mod = toba::get_hilo()->obtener_parametro('mtime');
		$ultima_mod = filemtime($this->archivo);
		if ($anterior_mod != $ultima_mod) {
			echo filemtime($this->archivo);			
			echo "<--toba-->";			
			echo $encabezado;
			echo "<--toba-->";		
			echo $detalle;
			echo "<--toba-->";
			echo js::arreglo($cant_por_nivel, true);
		}
	}
	
	function extender_objeto_js() 
	{
		if ($this->get_pantalla_actual() != 'visor' || !isset($this->seleccion)) {
			return;
		}
		$niveles = toba::get_logger()->get_niveles();		
		$parametros = array();
//		$vinculo = toba::get_vinculador()->crear_autovinculo($parametros, array('servicio' => 'ejecutar'));
?>
			var ultima_mod ='<?=filemtime($this->archivo)?>';
			var niveles = <?=js::arreglo($niveles)?>;
			var niveles_actuales = {length: 0};
			var refresco_automatico = true;
			var consultando = false;

			<?=$this->objeto_js?>.evt__refrescar = function() {
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
			
			<?=$this->objeto_js?>.respuesta_refresco = function(resp)
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
					var src_nuevo = toba_alias + '/img/logger/' + nivel_min + diff + '.png';
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
					<?=$this->objeto_js?>.evt__refrescar();
				}
				timer_refresco();
			}
			
			function timer_refresco()
			{
				var lapso = parseFloat(document.getElementById('refresco_lapso').value);
				var lapso = (lapso>0) ? lapso : 2000;
				setTimeout("chequear_refresco()", lapso);
			}
			timer_refresco();
<?php
	}
	
	function obtener_html_fs()
	{
		if (!file_exists($this->archivo)) {
			echo ei_mensaje("No hay logs registrados para el proyecto ".
							"<strong>{$this->opciones['proyecto']}</strong>");
			return;
		}			
		$niveles = toba::get_logger()->get_niveles();
		$niveles = array_reverse($niveles);		
		
		$res = $this->obtener_pedido($this->seleccion);
		$encabezado = $this->analizar_encabezado($res);
		
		echo "<div style='clear:both;width:100%;height:100%;overflow:auto;'>\n";
		
		//--- Opciones
		echo "<div>";
		$check = form::checkbox("con_encabezados", 0, 1, "ef-checkbox", " onclick=\"toggle_nodo(document.getElementById('logger_encabezados'))\"");
		echo "<label>$check Ver Encabezados</label><br>";
		$check = form::checkbox("refresco_automatico", 1, 1, "ef-checkbox", " onclick=\"set_refresco_automatico(this.checked);\"");		
		$edit = form::text("refresco_lapso", 2000, false, 6, 6);
		echo "<label>$check Refresco Automático</label> <span id='div_lapso'>".$edit."ms</span><br>";
		echo "</div><hr>";
				
		list($detalle, $cant_por_nivel) = $this->obtener_html_detalles($res);

		//--- Encabezado 
		echo "<ul id='logger_encabezados' style='display:none;list-style-type: none;padding: 0;margin: 0'>";		
		echo $this->obtener_html_encabezado($res);
		echo "</ul>";

		//---- Niveles
		echo "<div style='clear:both;float:right;margin-left:10px;text-align:center;'><strong>Niveles</strong>";
		echo "<ul style='border:1px solid black; text-align: center;list-style-type: none;padding: 4px;margin: 0; background-color:white;'>";
		foreach ($niveles as $nivel) {
			$img = recurso::imagen_apl('logger/'.strtolower($nivel).'.png', true, null, null, "Filtrar el nivel: $nivel");
			$cant = ($cant_por_nivel[$nivel] != 0) ? "[{$cant_por_nivel[$nivel]}]" : "";
			echo "<li id='nivel_$nivel' style='text-align:left;'><a href='#' onclick='mostrar_nivel(\"$nivel\")'>$img</a> ";
			echo "<span id='nivel_cant_$nivel'>$cant</span></li>\n";	
		}
		echo "</ul>";
		echo "</div>";
		
		//--- Detalles
		echo "<ul id='logger_detalle' style='list-style-type: none;padding: 0;margin: 0;margin-top:10px;'>";		
		echo $detalle;
		echo "</ul>\n";
		
		echo "</div>";
	}
	
	function obtener_html_encabezado($res)
	{
		$encabezado = $this->analizar_encabezado($res);
		$enc = "";
		//--- Encabezado		
		foreach ($encabezado as $clave => $valor) {
			$enc .= "<li><strong>".ucfirst($clave)."</strong>: $valor</li>\n";
		}
		$enc .= "<li><hr></li>";
		return $enc;
	}
	
	function obtener_html_detalles($res)
	{
		$niveles = toba::get_logger()->get_niveles();
		$cuerpo = $this->analizar_cuerpo($res);
		$cant_por_nivel = array();
		foreach ($niveles as $nivel) {
			$cant_por_nivel[$nivel] = 0;
		}
		$detalle = '';
		foreach ($cuerpo as $linea) {
			$img = recurso::imagen_apl('logger/'.strtolower($linea['nivel']).'.png', true, null, null);
			$detalle .= "<li nivel='{$linea['nivel']}'>";
			$detalle .= "$img ".$linea['mensaje'];
			$detalle .= "</li>";	
			$cant_por_nivel[$linea['nivel']]++;
		}
		return array($detalle, $cant_por_nivel);
	}
	
	protected function analizar_encabezado($log)
	{
		$encabezado = substr($log, 0, strpos($log, logger::fin_encabezado));
		$pares = explode("\r\n", trim($encabezado));
		$basicos = array();
		foreach ($pares as $texto) {
			$pos = strpos($texto, ":");
			$clave = substr($texto,0, $pos);
			$valor = substr($texto, $pos+1, strlen($texto));
			$basicos[strtolower(trim($clave))] = trim($valor);
		}
		return $basicos;
	}	
	
	protected function analizar_cuerpo($log)
	{
		$cuerpo = array();
		$niveles = toba::get_logger()->get_niveles();
		$texto = trim(substr($log, strpos($log, logger::fin_encabezado) + strlen(logger::fin_encabezado), strlen($log)));
		$patron = "/\[(";
		$patron .= implode("|", $niveles);
		$patron .= ")\]/";
		
		$res = preg_split($patron, $texto, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		//Se mezclan el nivel y el mensaje en un arreglo
		for ($i = 0; $i < count($res); $i+=2) {
			$cuerpo[] = array('nivel' => $res[$i], 'mensaje' => $res[$i+1]);
		}
		return $cuerpo;
	}

	protected function obtener_pedido($seleccion)
	{
		//Pedir el ultimo es un caso especial porque se trata con mas eficiencia
		if ($seleccion == 'ultima') {
			return $this->obtener_ultimo_pedido();	
		}
		//Trata de encontrar el n-esimo pedido en el archivo
		//Este metodo es mucha mas ineficiente que obtener el ultimo
		$logs = $this->get_logs_archivo();
		return $logs[$seleccion-1];
	}
	
	/**
	 * Recorre en inversa el archivo tratando de encontrar el limite de la ultima seccion
	 * @return array Texto del ultimo pedido, ¿Queda algo antes?
	 */
	protected function obtener_ultimo_pedido()
	{
		$total = filesize($this->archivo);
		$fp = fopen($this->archivo, "rb");
		$franja = 50 * 1024; //Se leen los ultimos 50 KB en reversa
		$franja_acum = $franja;
		$pos = 0;
		$encontrado = false;
		$hay_algo_antes = false;
		do {
			$pos = (abs($pos - $franja) > $total) ? -$total : $pos-$franja;
			fseek($fp, $pos, SEEK_END);
			$hay_mas_para_leer = (abs($pos) < $total);
			$acumulado = fread($fp, $franja_acum);
			$ocurrencia = strrpos($acumulado, logger::separador);
			if ($ocurrencia !== false) {
				//Se encontro el separador, una parte del acumulado pertenece a este pedido
				$encontrado = true;
				$acumulado = substr($acumulado, $ocurrencia + strlen(logger::separador));
				$hay_algo_antes =  $hay_mas_para_leer || ($ocurrencia !== 0);
			}
			$franja_acum += $franja;
		} while (!$encontrado && $hay_mas_para_leer);
		
		fclose($fp);
		return $acumulado;
	}
	
	protected function get_logs_archivo()
	{
		if (!file_exists($this->archivo)) {
			return array();	
		}
		$texto = trim(file_get_contents($this->archivo));
		$logs = explode(logger::separador , $texto);
		if (count($logs) > 0) {
			//Borra el primer elemento que siempre esta vacio
			array_splice($logs, 0 ,1);
		}
		return $logs;
	}
	
	function get_logger()
	{
		return logger::instancia($this->opciones['proyecto']);
	}
	

	function evt__pre_cargar_datos_dependencias()
	{
		if (isset($this->opciones)) {
			$this->dependencia('filtro')->colapsar();
		}
	}
	
	//---- Eventos CI -------------------------------------------------------
	
	function evt__refrescar()
	{
	}
	
	function evt__borrar()
	{
		$this->get_logger()->borrar_archivos_logs();	
	}
	
	//---- Eventos Filtro -------------------------------------------------------
	
	function evt__filtro__filtrar($opciones)
	{
		$this->opciones = $opciones;		
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->opciones);	
	}
	
	function evt__filtro__carga()
	{
		if (isset($this->opciones)) {
			return $this->opciones;	
		}
	}
	
	//---- Eventos Cuadro -------------------------------------------------------
	
	function evt__pedidos__carga()
	{
		$logs = $this->get_logs_archivo();
		$logs = array_reverse($logs);		
		$pedidos = array();
		$numero = count($logs);
		foreach ($logs as $log) {
			$log = trim($log);
			$basicos = $this->analizar_encabezado($log);
			$basicos['numero'] = $numero;
			$pedidos[] = $basicos; 
			$numero--;
		}
		return $pedidos;
	}
	
	function evt__pedidos__seleccion($id)
	{
		$this->seleccion = $id['numero'];
		$this->cambiar_pantalla = true;
	}
	
	function evt__pedidos__ultima()
	{
		$this->seleccion = 'ultima';
		$this->cambiar_pantalla = true;		
	}
		
}

?>