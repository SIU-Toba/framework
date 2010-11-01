<?php
/**
 * Genera un cliente para mapas GIS
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_mapa ei_mapa
 * @wiki Referencia/Objetos/ei_mapa
 
	- A este le falta el disparar_eventos, que busque en el get o post lo que el toolbar envio y llame al reportar_eventos del ci
 */
class toba_ei_mapa extends toba_ei
{
	//Variables que mantienen la info inicial
	protected $_mapa;
	protected $_extent;

	//Variables modificables en el conf o cuando vuelven los parametros
	protected $_alto_viewport = '';
	protected $_ancho_viewport = '';
	protected $_layers_activos = array();
	protected $_extent_activo;

	protected $_info_eventos = array('conf');

	final function __construct($id)
	{
		if (! extension_loaded('php_mapscript') && ! extension_loaded('MapScript')) {
			throw new toba_error('La extensión php_mapscript no se encuentra cargada, verifique la instalación.');
		}
		parent::__construct($id);
		//TODO: Hack para navegacion ajax con windows*/
		toba_ci::set_navegacion_ajax(false);
	}

	protected function preparar_componente()
	{
		if (isset($this->_info_mapa['mapfile_path'])) {
			$ruta = toba::proyecto()->get_path_php(). '/'. $this->_info_mapa['mapfile_path'];
			$this->_mapa = ms_newMapObj($ruta);
		} else {
			toba::logger()->error('El componente '. $this->_id[1] . ' no posee un archivo map definido.');
			throw new toba_error_def('Falta especificar un map file para el componente');
		}
		$this->analizar_layers();
		parent::preparar_componente();
	}

	function analizar_layers()
	{
		//Primero busco todos los layers del  mapa
		$this->_extent = $this->_mapa->extent;			//Desgraciadamente no hay Api para este objeto
		$this->_layers_activos = $this->get_nombre_layers();	//Inicializo los layers activos en los que devolvio el mapa.
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																										ETAPA DE EVENTOS																										  //
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	function disparar_eventos()
	{
		//$this->pre_eventos();
		//$datos = $this->get_datos();
		/*if (isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];
		}*/
		
		//Recupero el tamaño del viewport desde el GET
		$size = toba::memoria()->get_parametro('mapsize');
		if (! is_null($size)) {
			list($this->_ancho_viewport, $this->_alto_viewport) = explode(' ', $size);
		}

		//Recupero el extent nuevo a usar tambien
		$extent = toba::memoria()->get_parametro('mapext');
		if (! is_null($extent)) {
			list($xmin, $ymin, $xmax, $ymax) = explode(' ', $extent);
			$this->set_extent_activo($xmin, $ymin, $xmax, $ymax);
		}

		$layers = toba::memoria()->get_parametro('layers');
		if (! is_null($layers)) {
				$layers_act = explode(' ' , $layers);
				$this->set_layers_activos($layers_act);
		}		
		
		//Aca decido que hacer con los distintos eventos que me van llegando y con la info que necesita para procesarlos
		//aunque aparentemente los maneja todos el cliente ahorita
		//$this->post_eventos();
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																								METODOS DE CONFIGURACION																							//
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	function set_viewport($ancho, $alto)
	{
			$this->_ancho_viewport = $ancho;
			$this->_alto_viewport = $alto;	
	}
	
	function set_layers_activos($layers = array())
	{
		$this->verificar_layers_validos($layers);
		$this->_layers_activos = $layers;
	}

	function set_extent_activo($xmin, $ymin, $xmax, $ymax)
	{
		$this->verificar_extent_valido($xmin, $ymin, $xmax, $ymax);
		$this->_extent_activo = array('xmin' => $xmin, 'ymin' => $ymin,'xmax' => $xmax, 'ymax' => $ymax);
	}
	
	function set_datos($datos)
	{
		//Aca no se que voy a poner por ahora
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------//
	function get_layers_activos()
	{
		if (isset($this->_layers_activos)) {
			return $this->_layers_activos;
		}
	}

	function get_grupos()
	{
		return $this->_mapa->getAllGroupNames();
	}

	function get_mapa()
	{
		if (isset($this->_mapa)) {
			return $this->_mapa;
		}
	}

	function get_nombre_layers()
	{
		return $this->_mapa->getAllLayerNames();
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																													VALIDACIONES																									    //
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	private function verificar_layers_validos($layers)
	{
		$layers_referencia = $this->get_nombre_layers();
		foreach($layers as $layer) {
			if (! in_array($layer, $layers_referencia)) {
				toba::logger()->error("El layer $layer no es valido para el mapa {$this->_info_mapa['mapfile_path']}");
				throw new toba_error_validacion("El layer $layer no es válido");
			}
		}
	}

	private function verificar_extent_valido($xmin, $ymin, $xmax, $ymax)
	{
		//Deberia  verificar que son todos nros reales.
		//TODO: verificar que los boundaries no se excedan
		if (! is_numeric($xmin) || ! is_numeric($ymin) || ! is_numeric($xmax)  || ! is_numeric($ymax)) {
			toba::logger()->error("El extent seleccionado para el mapa {$this->_info_mapa['mapfile_path']} no es válido: ($xmin, $ymin, $xmax, $ymax)");
			throw new toba_error_validacion('El extent seleccionado no es válido');
		}
		return true;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																													SALIDA HTML																											  //
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//	
	function generar_html()
	{
        echo "\n<table class='ei-base' >\n";
		echo"<tr><td style='padding:0'>\n";
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-esquema-barra-sup");
		$this->generar_viewport();
		echo "</td>\n";
		echo "<td>\n";
		$this->generar_control_de_layers();
		echo "</td>\n";
		echo "</tr></table>\n";
	}

	function generar_viewport()
	{
        $ancho = ''; $alto = '';
        if (isset($this->_ancho_viewport)) {
        	$ancho = 'width: '.$this->_ancho_viewport.'px;';
        }
		if (isset($this->_alto_viewport)) {
			$alto = 'height: '.$this->_alto_viewport .'px;';
		}
		//Campo de sincronizacion con JS
		echo toba_form::hidden($this->_submit, '');
		//Div donde se mostrara el mapa
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "style='display:none'" : "";
		echo "<div style=\"$ancho $alto\"  id='cuerpo__{$this->objeto_js}' > </div>";
	}

	function generar_control_de_layers()
	{
		echo "<div id = 'control_layers_{$this->objeto_js}'  class='layer-ctrl'>";
		//Recorro los grupos que pueda haber
		$grupos = $this->get_grupos();
		if (! empty($grupos)) {
				foreach( $grupos as $grupo) {
					//Recupero los layers que esten dentro del grupo por indice
					$this->get_separador_grupo($grupo);
					$layer_idxs = $this->_mapa->getLayerIndexByGroup($grupo);
					foreach($layer_idxs as $idx) {
						$layer = $this->_mapa->getLayer($lay_idx);
						$nombre_layer = $layer->getMetadata('NAME');
						$id_ef = $this->objeto_js. '_chck_'. $layer;
						$this->get_selector_layer($id_ef, $layer);
					}
				}
		} else {				//Si no hay grupos en el Mapfile entonces muestro los mapas
				echo "<p><span style='font-weight: bold; font-variant:small-caps;'> Layers Disponibles </span></p><hr/>";
				$layers_disp = $this->get_nombre_layers();
				foreach ($layers_disp as $layer) {
					$id_ef = $this->objeto_js. '_chck_'. $layer;
					$this->get_selector_layer($id_ef, $layer);
				}
		}
		echo '</div>';
	}

	function get_selector_layer($id_ef, $nombre_layer)
	{
		$actual = $nombre_layer;
		$status = $this->_mapa->getLayerByName($nombre_layer)->getMetadata('status');		
		if ($status === MS_OFF) {					//Si el layer no esta activo en el mapfile
			$actual = '';
		} elseif (! empty($this->_layers_activos) && ! in_array($nombre_layer, $this->_layers_activos)) {  //Si no viene en la lista de layers activos actualmente
			$actual = '';
		}

		$estilo = '';									//Habria que crear un estilo para el checkbox
		$ancho = '100px';						//Ancho de las etiquetas, se deberia poder configurar
		$js = "onclick='{$this->objeto_js}.change_layers(this);'";		//Js que realiza la llamada

		//Saco el label y el checkbox
		echo "<div class = 'layer-selector'>";
		echo toba_form::checkbox($id_ef, $actual, $nombre_layer, null, $js);
		echo "<label style='_width:$ancho;' for='$id_ef' class='$estilo'>$nombre_layer</label>\n";
		echo "</div>";
	}

	function get_separador_grupo($id_grupo)
	{
		echo "<br><div class='layer-grupo'> $id_grupo </div><br>";
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																										PROCESAMIENTO DEL MAPA																									//
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
/*
	protected function generar_mapa($formato, $ancho = null, $alto = null)
	{
		$url = $this->get_url_mapa();
		$this->generar_sentencia_incrustacion($url, $formato, $ancho, $alto);
	}

	static function generar_sentencia_incrustacion($url, $formato, $ancho=null, $alto=null)
	{
		$ancho = isset($ancho) ? "width='$ancho'" : "";
		$alto = isset($alto) ? "height='$alto'" : "";
		echo "<img src='$url' $ancho $alto border='0'>";				
		switch ($formato) {
			case 'png':
			case 'gif':
				echo "<img src='$url' $ancho $alto border='0'>";				
			break;
			case 'svg':
				/*toba_js::cargar_consumos_globales(array("utilidades/svglib"));
				echo toba_js::abrir();
				echo "//aviso_instalacion_svg()";
				echo toba_js::cerrar();	*/
			/*	echo "<embed src='$url' type='image/svg+xml' $ancho $alto palette='foreground' pluginspage='http://www.adobe.com/svg/viewer/install/auto'>\n";
				echo "</embed>"; 
			break;
		}
	}*/


	protected function get_url_mapa()
	{
		$this->_memoria['parametros'] = array('layers' => $this->get_nombre_layers());
		$destino = array($this->_id);
		$url = toba::vinculador()->get_url(null, null, array(),array('servicio' => 'ejecutar',
																											 'objetos_destino' => $destino));
		return $url;
	}

	function servicio__ejecutar($parametros = null)
	{
		toba::memoria()->desactivar_reciclado();
		$archivo = $this->generar_archivo();
		$this->enviar_archivo($archivo);
	}

	protected function generar_archivo()
	{
		$formato = $this->_mapa->outputformat;
		$nombre_archivo = mt_rand() . '.' . $formato->getOption('name');
		$dir_temp = toba::instalacion()->get_path_temp();
		$salida = toba_manejador_archivos::path_a_unix( $dir_temp . "/" . $nombre_archivo );

		//Aca le digo cuales son los layers activos
		if (! empty($this->_layers_activos)) {
			$layers_disp = $this->get_nombre_layers();
			foreach($layers_disp as $layer) {
				if (in_array($layer, $this->_layers_activos)) {
					$this->_mapa->getLayerByName($layer)->set('status', MS_ON);
				}else{
					$this->_mapa->getLayerByName($layer)->set('status', MS_OFF);
				}
			}
		}

		//Aca le digo cual es el extent activo
		if (isset($this->_extent_activo)) {
			$this->_mapa->setExtent($this->_extent_activo['xmin'], $this->_extent_activo['ymin'], $this->_extent_activo['xmax'], $this->_extent_activo['ymax']);
		}

		//Aca deberia ir algo para extender la consulta o algo por el estilo

		//Dibujo el mapa y envio la salida a archivo.
		$img = $this->_mapa->draw();
		$img->saveImage($salida,$this->_mapa);

		return $nombre_archivo;
	}

	protected function enviar_archivo($archivo)
	{
		//Primero averiguo el mimetype del mapfile
		$formato = $this->_mapa->outputformat;
		$tipo_salida = $formato->getOption('mimetype');

		//Luego abro el archivo y veo que paso
		$dir_temp = toba::instalacion()->get_path_temp();
		$path_completo = $dir_temp . "/" . $archivo;
		if (file_exists($path_completo)) {
			$fp = fopen($path_completo, 'rb');
			if (isset($tipo_salida)) {
				header("Content-type: $tipo_salida");
			}
			header("Content-Length: " . filesize($path_completo));
			fpassthru($fp);
			fclose($fp);
			unlink($path_completo);
		} else {
			toba::logger()->error("El archivo $path_completo no se encuentra");
		}
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																													SALIDA JS																												  //
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	function get_consumo_javascript()
	{
		return array('componentes/ei_mapa', 'utilidades/varios','utilidades/ms_tools', 'utilidades/ms_map', 'utilidades/point_overlay');
	}

	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$id = toba_js::arreglo($this->_id, false);
		echo $identado . "window.{$this->objeto_js} = new ei_mapa($id,'{$this->objeto_js}', '{$this->_submit}');";
		//echo $identado. "window.{$this->objeto_js} = new msMap(document.getElementById('cuerpo_{$this->objeto_js}'), 'standardRight');\n";
		//echo $this->get_selector_layer_js();
	}

	protected function iniciar_objeto_js()
	{
		$url = $this->get_url_mapa();
		$identado = toba_js::instancia()->identado();

		//Obtengo el Full Extent del mapa, los zooms intermedios los maneja el cliente
		$extent = "'{$this->_extent->minx}' ,' {$this->_extent->maxx}', '{$this->_extent->miny}', '{$this->_extent->maxy}'";
		//Obtengo la lista de Layers original del mapa
		$layers = implode(' ' ,$this->get_nombre_layers());

		//Se agrega al objeto al singleton toba
		echo $identado."toba.agregar_objeto(window.{$this->objeto_js});\n";

		//Envio todas las variables necesarias en el cliente
		echo $identado. "{$this->objeto_js}.set_url('$url');\n";
		echo $identado. "{$this->objeto_js}.set_extent($extent);\n";
		echo $identado. "{$this->objeto_js}.set_layers('$layers');\n";		
		echo $identado."{$this->objeto_js}.set_layers_activos(".toba_js::arreglo(array_fill_keys($this->_layers_activos,1),true)."); \n";

		echo $identado. "{$this->objeto_js}.iniciar();\n";
		echo $identado. "{$this->objeto_js}.render();\n";

		//-- EVENTO implicito --
/*		if (isset($this->_evento_implicito) && is_object($this->_evento_implicito)){
			$evento_js = $this->_evento_implicito->get_evt_javascript();
			echo toba_js::instancia()->identado()."{$this->objeto_js}.set_evento_implicito($evento_js);\n";
		}
		if ($this->_colapsado) {
			echo $identado."window.{$this->objeto_js}.colapsar();\n";
		}*/
	}

/*	protected function get_selector_layer_js()
	{
		//envio una lista de los layers del mapa y voy seleccionando los activos con los checks
		//Luego que selecciono seteo los activos y disparo un pedido de actualizacion
		$identado = toba_js::instancia()->identado();
		echo $identado. "var lista_layers_{$this->objeto_js} = " . toba_js::arreglo(array_fill_keys($this->_layers_activos,1),true). ';';
		echo "
			{$this->objeto_js}.change_layers = function(obj)
			{
				var layer_actual = obj.value;
				var status = obj.checked;
				if (status) {
					lista_layers_{$this->objeto_js}[layer_actual] = 1;
				} else {
					lista_layers_{$this->objeto_js}[layer_actual] = 0;
				}

				var layers_resultado = [];
				for (var layer  in  lista_layers_{$this->objeto_js}) {
					if (lista_layers_{$this->objeto_js}[layer] == 1) {
						layers_resultado.push(layer);
					}
				}
				window.{$this->objeto_js}.setLayers(layers_resultado.join(' '));
				window.{$this->objeto_js}.redraw();
			};";
	}*/
}
?>
