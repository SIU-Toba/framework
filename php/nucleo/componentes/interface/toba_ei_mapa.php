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
	protected $_param_mapext = 'mapext';
	protected $_param_extra = 'map_extra';

	final function __construct($id)
	{
		if (! extension_loaded('php_mapscript') && ! extension_loaded('MapScript')) {
			throw new toba_error('La extensión php_mapscript no se encuentra cargada, verifique la instalación.');
		}
		parent::__construct($id);
		//TODO: Hack para navegacion ajax con windows*/
		toba_ci::set_navegacion_ajax(false);
		$this->_param_mapext .= $this->_id[1];
		$this->_param_extra .= $this->_id[1];
	}

	/**
	 * @ignore
	 */
	protected function preparar_componente()
	{
		if (!isset($this->_info_mapa['mapfile_path'])) {
			toba::logger()->error('El componente '. $this->_id[1] . ' no posee un archivo map definido.');
			throw new toba_error_def('Falta especificar un map file para el componente');			
		}
				
		$ruta = toba::proyecto()->get_path_php(). '/'. $this->_info_mapa['mapfile_path'];
		try {
			$this->_mapa = new MapObj($ruta);
		} catch (Exception $e) {
			toba::logger()->error($e->getMessage());
			throw new toba_error('No se pudo crear el objeto Mapserver');
		}
		$this->analizar_layers();
		parent::preparar_componente();
	}

	/**
	 *  @ignore
	 */
	protected function analizar_layers()
	{
		//Primero busco todos los layers del  mapa
		$this->_extent = $this->_mapa->extent;			//Desgraciadamente no hay Api para este objeto
		$this->_layers_activos = $this->get_nombre_layers();	//Inicializo los layers activos en los que devolvio el mapa.
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																										ETAPA DE EVENTOS																										  //
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Devuelve el evento que se disparo en el cliente
	 * @return string
	 */
	function get_evento_interaccion()
	{
		$evento = null;
		if (isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];
		}		
		return $evento;
	}

	/**
	 * Determina si hubo un evento en el cliente o no.
	 * @return boolean
	 */
	function hay_evento_interaccion()
	{
		return (! is_null($this->get_evento_interaccion()));
	}

	/**
	 * Dispara los eventos del componente
	 */
	function disparar_eventos()
	{
		$this->pre_eventos();
		$datos = $this->get_datos();
		$evento = $this->get_evento_interaccion();

		//Recupero el tamaño del viewport desde el GET
		if (! empty($datos['size'])) {
			$this->set_viewport($datos['size'][0], $datos['size'][1]);
		}

		//Recupero el extent nuevo a usar tambien
		if (! empty($datos['extent'])) {
			list($xmin, $ymin, $xmax, $ymax) = $datos['extent'];
			$this->set_extent_activo($xmin, $ymin, $xmax, $ymax);
		}

		if (! empty($datos['layers'])) {
				$this->set_layers_activos($datos['layers']);
		}		

		//Si existe un evento puntual entonces lo disparo, sino va por el camino de la generacion de la imagen nomas
		if (isset($evento) && isset($this->_memoria['eventos'][$evento])) {
				$this->reportar_evento($evento, $datos);
		} 
		$this->post_eventos();
	}

	/**
	 * Recupera los datos que fueron enviados por el mapa, ya sea por post o get.
	 * @return array
	 * @ignore
	 */
	function get_datos()
	{
		$datos = array();
		$size = null;
		$layers = null;
		$extra = null;

		 //Se proceso un evento del mapa solo viene el extent y el parametro extra
		if ($this->hay_evento_interaccion()) {
			//Recupero el extent a usar			
			$extent =  (isset($_POST[$this->_param_mapext]) && ($_POST[$this->_param_mapext] != '')) ? $_POST[$this->_param_mapext] : null;
			//Recupero los datos extra que pudo enviar el evento
			$extra = (isset($_POST[$this->_param_extra]) && ($_POST[$this->_param_extra] != '')) ? $_POST[$this->_param_extra] : null;
		} else {								//Hace llamada ajax el cliente del mapa, viene todo por GET no hay evento
			//Recupero el tamaño del viewport desde el GET
			$size = toba::memoria()->get_parametro('mapsize');
			//Recupero el extent nuevo a usar tambien
			$extent = toba::memoria()->get_parametro('mapext');
			//Recupero los layers activos
			$layers = toba::memoria()->get_parametro('layers');
		}
		//Paso los datos al arreglo que se retorna
		$datos['size'] = (! is_null($size)) ? explode(' ', $size) : array();
		$datos['extent'] = (! is_null($extent)) ? explode(' ' , $extent) : array();
		$datos['layers'] =  (! is_null($layers)) ?  explode(' ' , $layers) : array();
		$datos['extra'] = $extra;

		return $datos;
	}

	function pre_eventos()
	{
	}

	function post_eventos()
	{
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																								METODOS DE CONFIGURACION																							//
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Permite fijar el ancho y alto con que se vera el mapa en pantalla
	 * @param integer $ancho
	 * @param integer $alto
	 */
	function set_viewport($ancho, $alto)
	{
		if (!is_numeric($ancho) || !is_numeric($alto)) {
			throw new toba_error_usuario('El tamaño del viewport no es válido', "Se eligio ('$ancho', '$alto') como tamaño para el viewport y no es válido");
		}
		$this->_ancho_viewport = $ancho;
		$this->_alto_viewport = $alto;	
	}

	/**
	 * Setea los layers que estan activos en el mapa
	 * @param array $layers Arreglo conteniendo los nombres de los layers activos
	 */
	function set_layers_activos($layers = array())
	{
		$this->verificar_layers_validos($layers);
		$this->_layers_activos = $layers;
	}

	/**
	 * Setea la porcion del mapa actualmente visible
	 * @param float $xmin
	 * @param float $ymin
	 * @param float $xmax
	 * @param float $ymax
	 */
	function set_extent_activo($xmin, $ymin, $xmax, $ymax)
	{
		$this->verificar_extent_valido($xmin, $ymin, $xmax, $ymax);
		$this->_extent_activo = array('xmin' => $xmin, 'ymin' => $ymin,'xmax' => $xmax, 'ymax' => $ymax);
	}

	/**
	 * @ignore
	 * @param <type> $datos
	 */
	function set_datos($datos)
	{
		//Aca no se que voy a poner por ahora
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Devuelve una lista de los layers activos
	 * @return array
	 */
	function get_layers_activos()
	{
		if (isset($this->_layers_activos)) {
			return $this->_layers_activos;
		}
	}

	/**
	 * Devuelve una lista de los grupos que existen en el mapa
	 * @return array
	 */
	function get_grupos()
	{
		return $this->_mapa->getAllGroupNames();
	}

	/**
	 * Devuelve una referencia al objeto mapscript
	 * @return obj
	 */
	function get_mapa()
	{
		if (isset($this->_mapa)) {
			return $this->_mapa;
		}
	}

	/**
	 * Devuelve una lista con los nombres de los layers
	 * @return array
	 */
	function get_nombre_layers()
	{
		return $this->_mapa->getAllLayerNames();
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																													VALIDACIONES																									    //
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Verifica que los layers recibidos esten entre los validos para el mapa
	 * @param array $layers
	 * @ignore
	 */
	protected function verificar_layers_validos($layers)
	{
		$layers_referencia = $this->get_nombre_layers();
		foreach($layers as $layer) {
			if (! in_array($layer, $layers_referencia)) {
				toba::logger()->error("El layer $layer no es valido para el mapa {$this->_info_mapa['mapfile_path']}");
				throw new toba_error_validacion("El layer $layer no es válido");
			}
		}
		return true;
	}

	/**
	 * Verifica que los limites visuales sean validos
	 * @param float $xmin
	 * @param float $ymin
	 * @param float $xmax
	 * @param float $ymax
	 * @return boolean
	 */
	protected function verificar_extent_valido($xmin, $ymin, $xmax, $ymax)
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
	/**
	 * Genera el html del mapa
	 */
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

	/**
	 *  Genera el HTML para la porcion que visualiza el mapa propiamente dicha
	 */
	protected function generar_viewport()
	{
		$ancho = ''; $alto = '';
		if (isset($this->_ancho_viewport)) {
			$ancho = 'width: '.$this->_ancho_viewport.'px;';
		}
		if (isset($this->_alto_viewport)) {
			$alto = 'height: '.$this->_alto_viewport .'px;';
		}
		//Campos de sincronizacion con JS
		$this->generar_parametros_post();

		//Div donde se mostrara el mapa
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "style='display:none'" : "";
		echo "<div style=\"$ancho $alto\"  id='cuerpo__{$this->objeto_js}' > </div>";
	}

	/**
	 * @ignore
	 */
	protected function generar_parametros_post()
	{
		//Campos de sincronizacion con JS
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_param_extra , '');
		echo toba_form::hidden($this->_param_mapext , '');
	}

	/**
	 *  Recorre la lista de layers y arma el HTML para el selector
	 */
	protected function generar_control_de_layers()
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

	/**
	 * Arma el div selector para el layer, con el evento JS propiamente dicho
	 * @param string $id_ef
	 * @param string $nombre_layer
	 * @ignore
	 */
	protected function get_selector_layer($id_ef, $nombre_layer)
	{
		$actual = $nombre_layer;
		$layer_obj = $this->_mapa->getLayerByName($actual);
		if (is_null($layer_obj)) {
			throw new toba_error('El mapa no contiene la capa '. $actual);			
		}		
		$status = $layer_obj	->getMetadata('status');		
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
		echo "<label style='width: $ancho;' for='$id_ef' class='$estilo'>$nombre_layer</label>\n";
		echo "</div>";
	}

	/**
	 * Coloca un separador de grupos de layers
	 * @param string $id_grupo
	 */
	protected function get_separador_grupo($id_grupo)
	{
		echo "<br><div class='layer-grupo'> $id_grupo </div><br>";
	}

	/**
	 *@ignore
	 */
	function generar_botones($clase = '', $extra='')
	{
		//Redefino para anular comportamiento debido a que los botones se agregan en JS
	}	

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																										PROCESAMIENTO DEL MAPA																									//
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Devuelve el vinculo con la direccion a la que se le pedira el servicio para el grafico del mapa.
	 * @return <type>
	 */
	protected function get_url_mapa()
	{
		$this->_memoria['parametros'] = array('layers' => $this->get_nombre_layers());
		$destino = array($this->_id);
		$url = toba::vinculador()->get_url(null, null, array(),array('servicio' => 'ejecutar',
																											 'objetos_destino' => $destino));
		return $url;
	}

	/**
	 * Servicio que se ejecuta cuando el cliente pide el mapa por URL
	 * @param <type> $parametros
	 * @ignore
	 */
	function servicio__ejecutar($parametros = null)
	{
		toba::memoria()->desactivar_reciclado();
		$archivo = $this->generar_archivo();
		$this->enviar_archivo($archivo);
	}

	/**
	 * Utiliza el objeto mapscript para generar el archivo con la imagen que representa el mapa
	 * @return string
	 * @ignore
	 */
	protected function generar_archivo()
	{
		$formato = $this->_mapa->outputformat;
		$nombre_archivo = mt_rand() . '.' . $formato->getOption('name');
		$dir_temp = toba::instalacion()->get_path_temp();
		$salida = toba_manejador_archivos::path_a_unix( $dir_temp . "/" . $nombre_archivo );

		//Aca le digo cuales son los layers activos
		$this->mapa_setear_estado_layers();

		//Aca le digo cual es el extent activo
		$this->mapa_setear_extent_activo();

		//Dibujo el mapa y envio la salida a archivo.
		$this->generar_salida($salida);

		return $nombre_archivo;
	}

	/**
	 * @ignore
	 */
	function mapa_setear_estado_layers ()
	{
		if (! empty($this->_layers_activos)) {
			$layers_disp = $this->get_nombre_layers();
			foreach($layers_disp as $layer) {
				$lay_obj = $this->_mapa->getLayerByName($layer);
				if (is_null($lay_obj)) {
					throw new toba_error('En el mapa no se encuentra cargada la capa ' .$layer);
				}
				//De acuerdo a si esta o no seleccionado, muestro u oculto el layer.
				if (in_array($layer, $this->_layers_activos)) {
					$lay_obj->set('status', MS_ON);
				}else{
					$lay_obj->set('status', MS_OFF);
				}
			}
		}
	}

	/**
	 * @ignore
	 */
	function mapa_setear_extent_activo()
	{
		if (isset($this->_extent_activo)) {
			$this->_mapa->setExtent($this->_extent_activo['xmin'], $this->_extent_activo['ymin'], $this->_extent_activo['xmax'], $this->_extent_activo['ymax']);
		}
	}

	/**
	 * Dibuja el mapa utilizando una funcion especifica
	 * @param string $salida Nombre del archivo de salida
	 */
	function generar_salida($salida)
	{
		$img = $this->_mapa->draw();
		if (is_null($img)) {
			throw new toba_error('No se pudo graficar el mapa');
		}
		$img->saveImage($salida,$this->_mapa);
	}

	/**
	 * Envia el archivo que contiene la imagen hacia el cliente.
	 * @param file $archivo
	 * @ignore
	 */
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
	/**
	 * Devuelve la lista de scripts JS utilizados por este componente
	 * @ignore
	 * @return <type>
	 */
	function get_consumo_javascript()
	{
		return array('componentes/ei_mapa', 'utilidades/varios','utilidades/ms_tools', 'utilidades/ms_map', 'utilidades/point_overlay');
	}

	/**
	 * Envia al cliente la sentencia que genera el objeto JS para este componente
	 *@ignore
	 */
	function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$id = toba_js::arreglo($this->_id, false);
		echo $identado . "window.{$this->objeto_js} = new ei_mapa($id,'{$this->objeto_js}', '{$this->_submit}'); \n";

		$this->generar_funciones_eventos_js();
	}

	/**
	*  Por cada evento se genera una funcion que lo atiende
	* @ignore
	*/
	protected function generar_funciones_eventos_js()
	{
		$identado = toba_js::instancia()->identado();
		//Si existen eventos definidos entonces genero una funcion js x cada evento que se dispara desde la toolbar
		echo $identado. "//---------------- INICIO EVENTOS MAPA {$this->_id[1]} --------------\n";
		if ($this->hay_botones()) {
			foreach($this->_eventos_usuario_utilizados as $evento )	{
				if ( $evento->esta_en_botonera() ) {
					if( !in_array($evento->get_id(), $this->_botones_graficados_ad_hoc ) ) {
						$this->generar_funcion_evento($evento->get_id());
					}
				}
			}
		}
	}
	
	/**
	 * Genera la botonera a partir de los eventos del componente
	 *  y  agrega un icono en el toolbar del mapa
	 *@ignore
	 */
	protected function generar_botones_eventos_js()
	{
		$identado = toba_js::instancia()->identado();
		//Si existen eventos definidos entonces genero una funcion js x cada evento que se dispara desde la toolbar

		echo $identado. '//---------------- CONFIG. BOTONERA DEL MAPA ----------------'. "\n";
		//Agrego lo que seria el grupo basico de eventos del mapa
		echo $identado . "{$this->objeto_js}.configurar_toolbar_eventos = function() \n";
		echo $identado."{";

		$this->generar_botones_eventos_estandar();

		//Vuelvo a ciclar nuevamente por los eventos esta vez para agregar el boton a la toolbar
		if ($this->hay_botones()) { 
			foreach($this->_eventos_usuario_utilizados as $evento )	{
				if ( $evento->esta_en_botonera() ) {
					if( !in_array($evento->get_id(), $this->_botones_graficados_ad_hoc ) ) {
						$this->generar_boton_js($evento->get_id());
					}
				}
			}
		}

		echo $identado. "} \n";
		echo $identado. "//---------------- FIN ZONA EVENTOS MAPA {$this->_id[1]} ------------- \n \n";
	}

	/**
	 * Genera la botonera basica del componente
	 *@ignore
	 */
	protected function generar_botones_eventos_estandar()
	{
		echo '
			this._toolbar.addTool(new msTool(\'Full Extent\', \'resetear_posicion\', _iconFullExtentButton));
			this._toolbar.addTool(new msTool(\'Zoom In\',  \'acercarse\', _iconZoominButton));
			this._toolbar.addTool(new msTool(\'Zoom Out\', \'alejarse\', _iconZoomoutButton));
			this._toolbar.addTool(new msTool(\'Pan\', \'desplazar\', _iconPanButton, true));
			this._toolbar.addTool(new msTool(\'Zoom\', \'area\', _iconZoomboxButton, true));
		';
	}

	/**
	 * Genera una funcion standard para el disparo del evento
	 * @ignore
	 */
	protected function generar_funcion_evento($evento_id)
	{
		$identado = toba_js::instancia()->identado();
		$evento = $this->evento($evento_id)->get_evt_javascript();
		//Genero la funcion en js que procesara el evento
		echo $identado. "{$this->objeto_js}.evt__mapa__disparador_$evento_id = function(evento) \n".
		$identado ."{\n".
		$identado. "	if (evento != undefined) { \n".
		$identado. "		this.set_evento($evento); \n" .
		$identado."	} \n	} \n";

		//Declaro como variable el icono que utilizara dicho boton
		$icono = '_icon'.$evento_id;
		$imagen = $this->evento($evento_id)->get_imagen_url_rel();
		echo $identado. "var $icono =  imgDir + '$imagen'; \n";
	}

	/**
	 * Agrega un boton al toolbar para el evento en cuestion, indicando ademas cual es la funcion
	 * que debe dispararse.
	 * @ignore
	 */
	protected function generar_boton_js($evento_id )
	{
			$identado = toba_js::instancia()->identado();
			$icono = '_icon'.$evento_id;

			$etiqueta = $this->evento($evento_id)->get_etiqueta();			
			echo $identado. "this._toolbar.addTool(new msTool('$etiqueta', 'evt__mapa__disparador_$evento_id', $icono, true)); \n";
	}

	/**
	 * Genera el codigo para inicializar el objeto JS
	 * @ignore
	 */
	protected function iniciar_objeto_js()
	{
		$url = $this->get_url_mapa();
		$identado = toba_js::instancia()->identado();

		//Obtengo el Full Extent del mapa, los zooms intermedios los maneja el cliente
		$extent_full = "'{$this->_extent->minx}' ,' {$this->_extent->maxx}', '{$this->_extent->miny}', '{$this->_extent->maxy}'";

		//Porcion actualmente visible, si no hay valores tomo el extent full como referencia
		if (isset($this->_extent_activo)) {
			$extent = "'".$this->_extent_activo['xmin']."' ,'". $this->_extent_activo['xmax'] . "', '". $this->_extent_activo['ymin']. "', '". $this->_extent_activo['ymax']. "'";
		} else {
			$extent = $extent_full;
		}

		//Obtengo la lista de Layers original del mapa
		$layers = implode(' ' ,$this->get_nombre_layers());

		//Genero los eventos en JS
		$this->generar_botones_eventos_js();

		//Se agrega al objeto al singleton toba
		echo $identado."toba.agregar_objeto(window.{$this->objeto_js});\n";

		//Envio todas las variables necesarias en el cliente
		echo $identado. "{$this->objeto_js}.set_url('$url');\n";
		echo $identado. "{$this->objeto_js}.set_full_extent($extent_full); \n";
		echo $identado. "{$this->objeto_js}.set_extent($extent);\n";
		echo $identado. "{$this->objeto_js}.set_layers('$layers');\n";		
		echo $identado."{$this->objeto_js}.set_layers_activos(".toba_js::arreglo(array_fill_keys($this->_layers_activos,1),true)."); \n";

		echo $identado. "{$this->objeto_js}.iniciar();\n";
		echo $identado. "{$this->objeto_js}.render();\n";

		//-- EVENTO implicito --
/*		if (isset($this->_evento_implicito) && is_object($this->_evento_implicito)){
			$evento_js = $this->_evento_implicito->get_evt_javascript();
			echo toba_js::instancia()->identado()."{$this->objeto_js}.set_evento_implicito($evento_js);\n";
		}*/
		if ($this->_colapsado) {
			echo $identado."window.{$this->objeto_js}.colapsar();\n";
		}
	}
}
?>
