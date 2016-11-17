<?php

/**
 * Centraliza la generación de código e includes javacript:
 *  - Include centralizado de recursos js
 *  - Conversión de estructuras de datos entre php y js
 * 
 * @package SalidaGrafica
 */
class toba_js
{
	private static $instancia;
	private static $cargados = array();
	private static $comprimido_cargado = false;
	protected $nivel_identado = 0;
	private static $basicos_cargados = false;
	private static $consumos_compr = array('componentes/', 'efs/', 'basicos/');
	private static $consumos_basicos = array(
						'basicos/basico', 'basicos/toba',  
						'utilidades/datadumper', 'basicos/yahoo',
						'basicos/comunicacion_server', 'basicos/notificacion',
						'basicos/vinculador');
	
	/**
	 * @return toba_js
	 */
	static function instancia() 
	{
		if (! isset(self::$instancia)) {
			self::$instancia = new toba_js();			
		}
		return self::$instancia;
	}
	

	/**
	*	Retorna el string de identado actual para el código JS
	*/
	function identado()
	{
		$tabs = '';
		for ($i=0; $i<$this->nivel_identado; $i++) {
			$tabs .= "\t";
		}
		return $tabs;
	}
	
	/**
	*	Cambia el nivel de identado agregando $nivel
	*/	
	function identar($nivel)
	{
		$this->nivel_identado += $nivel;
		return $this->identado();
	}
	
	//--- SERVICIOS ESTATICOS
	static function version()
	{
		return "1.4";
	}

	/**
	 * Incluye en tag <SCRIPT> con los atributos por defecto usados en el framework
	 */
	static function abrir()
	{
		return "<SCRIPT  language='JavaScript".toba_js::version()."' type='text/javascript'>\n";
	}

	/**
	 * Cierra el tag <SCRIPT>
	 */
	static function cerrar()
	{
		return "\n</SCRIPT>\n";
	}

	/**
	 * Incluye un tag <SCRIPT> con el include del archivo definido
	 * @param string $archivo URL del recurso js a incluir
	 */
	static function incluir($archivo) 
	{
		$version = toba::memoria()->get_dato_instancia('toba_revision_recursos_cliente'); 
		if (! is_null($version)) {
			$archivo = $archivo . "?av=". toba::escaper()->escapeUrl($version);
		}
		return "<SCRIPT language='JavaScript".toba_js::version()."' type='text/javascript' src='$archivo'></SCRIPT>\n";		
	}

	/**
	 * Incluye el código js suministrado dentro de un tag <SCRIPT>
	 */
	static function ejecutar($codigo) 
	{
		return toba_js::abrir().$codigo.toba_js::cerrar();
	}

	/**
	 * Permite agregar consumos basicos en runtime, normalmente se debe llamar
	 * antes de enviar la cabecera HTML.
	 * @param array $consumos Arreglo con consumos basicos a incluir
	 */
	function agregar_consumos_basicos($consumos = array())
	{
		self::$consumos_basicos = array_merge(self::$consumos_basicos, $consumos);
	}

	/**
	 * Incluye los consumos globales básicos para utilizar el framework
	 */
	static function cargar_consumos_basicos()
	{
		if (! self::$basicos_cargados) {
			$escapador = toba::escaper();
			self::$consumos_basicos[] = 'basicos/jquery-1.9.1.min';			
			if (toba::proyecto()->get_parametro('es_css3')) {
				self::$consumos_basicos[] = 'formalize/javascripts/jquery.formalize.min';
			}
			$item = toba::memoria()->get_item_solicitado() ;
			$imagenes = array(	'error' => toba_recurso::imagen_toba('error.gif', false), 
							'info' => toba_recurso::imagen_toba('info_chico.gif', false),
							'warning' => toba_recurso::imagen_toba('warning.gif', false),  
							'maximizar' => toba_recurso::imagen_toba('nucleo/sentido_des_sel.gif', false), 
							'minimizar' => toba_recurso::imagen_toba('nucleo/sentido_asc_sel.gif', false),
							'expandir'  => toba_recurso::imagen_skin('expandir_vert.gif', false),
							'contraer'  => toba_recurso::imagen_skin('contraer_vert.gif', false),
							'expandir_nodo' => toba_recurso::imagen_toba('nucleo/expandir.gif', false),
							'contraer_nodo' => toba_recurso::imagen_toba('nucleo/contraer.gif', false),
							'esperar' => toba_recurso::imagen_toba('wait.gif', false),
							'cerrar' => toba_recurso::imagen_toba('nucleo/cerrar_ventana.gif', false),
							);
			$script = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']): 'aplicacion.php';
			
			echo toba_js::abrir();
			echo "var toba_alias='".$escapador->escapeJs(toba_recurso::url_toba())."';\n";
			echo "var toba_proyecto_alias='".$escapador->escapeJs(toba_recurso::url_proyecto())."';\n";
			if (toba_editor::activado()) {
				echo 'var toba_proyecto_editado_alias = "'.$escapador->escapeJs(toba_editor::get_url_previsualizacion())."\";\n";
			}			
			echo "var toba_hilo_qs='".apex_hilo_qs_item."';\n";
			echo "var toba_hilo_separador='".apex_qs_separador."';\n";
			echo "var toba_hilo_separador_interno='". apex_qs_sep_interno. "';\n";
			echo "var toba_hilo_qs_servicio='".apex_hilo_qs_servicio."';\n";
			echo "var toba_hilo_qs_menu='".apex_hilo_qs_menu."';\n";
			echo "var apex_hilo_qs_celda_memoria='".apex_hilo_qs_celda_memoria."';\n";
			echo "var toba_hilo_qs_objetos_destino='".apex_hilo_qs_objetos_destino."';\n";
			echo "var lista_imagenes=".toba_js::arreglo($imagenes, true).",";
			echo " toba_prefijo_vinculo=toba_proyecto_alias + '/". $escapador->escapeJs($script) .'?' . apex_hilo_qs_id."='+'".  $escapador->escapeJs(toba::memoria()->get_id())
				. "&'+ toba_hilo_qs + '=". $escapador->escapeJs($item[0]). "'+toba_hilo_separador+'". $escapador->escapeJs($item[1]) .
				"' + '&'+ apex_hilo_qs_celda_memoria + '='  +'".$escapador->escapeJs(toba::memoria()->get_celda_memoria_actual_id())."';\n";
			echo "var apex_solicitud_tipo='".$escapador->escapeJs(toba::solicitud()->get_tipo())."';\n";			
			
			$espera = toba::proyecto()->get_parametro('tiempo_espera_ms');		
			if (! isset($espera)) {
				$espera = 0;	//No hay espera
			}
			echo "var toba_espera=".$escapador->escapeJs($espera).";\n";

			//-------------- Incluyo funcionalidad para la respuesta del popup  ---------------
			$ef_popup = toba::memoria()->get_parametro('ef_popup');
			if (is_null($ef_popup)) {
				$ef_popup = toba::memoria()->get_dato_sincronizado('ef_popup');
			}
			if (! is_null($ef_popup)){
				toba::memoria()->set_dato_sincronizado('ef_popup', $ef_popup);
				echo "
				function seleccionar(clave, descripcion) {
					window.opener.popup_callback('". $escapador->escapeJs($ef_popup) ."', clave, descripcion);
					window.close();
				}
				function respuesta_ef_popup(parametros) {
					var seleccion = parametros.split('||');
					seleccionar(seleccion[0], seleccion[1]);
				}

				function iniciar_respuesta_popup(objeto, parametros)
				{					
					var posicion = objeto.id.ultima_ocurrencia('_');
					var nombre = objeto.id.substr(0, posicion) + '_descripcion';
					var descripcion = $$(nombre).value;
					seleccionar(parametros, descripcion);
				}";
			}
			//-----------------------------------------------------------------------------------------------------
			echo toba_js::cerrar();		
			//Incluyo el javascript STANDART	
			
			self::cargar_consumos_globales(self::$consumos_basicos);
			
			if (toba::instalacion()->arreglo_png_ie()) {
				///---Arreglo PNGs IE
				$url = toba_recurso::js("utilidades/pngbehavior.htc");
				echo "<!--[if lt IE 7]>
					<style type='text/css'>". $escapador->escapeCss("
						img {
							behavior: url('$url');
						}")."
					</style>
					<![endif]-->\n";
			}
			$url = toba_recurso::js('basicos/html5shiv.js');
			echo "	<!--[if lt IE 9]>
						<script src='$url'></script>
					<![endif]-->\n";
			self::$basicos_cargados = true;
		}
	}
	
	static function cargar_definiciones_runtime()
	{		
		echo "window.toba_prefijo_vinculo =  toba_prefijo_vinculo;\n";	
		echo "window.toba_hilo_item = ".toba_js::arreglo(toba::memoria()->get_item_solicitado(), false)."\n";
		echo "window.toba_qs_zona = '".toba::vinculador()->get_qs_zona()."';\n";
	}
	
	/**
	 * Incluye una serie de librerías o consumos javascript
	 * @param array $consumos Lista de consumos, un consumo es el path relativo a www/js, sin la ext. js
	 */
	static function cargar_consumos_globales($consumos)
	{
		$consumos = array_unique($consumos);
		foreach ($consumos as $consumo)	{
			//Esto asegura que sólo se puede cargar una vez
			if (! in_array($consumo, self::$cargados)) {
				self::$cargados[] = $consumo;
				switch ($consumo) {
					//--> Expresiones regulares movidas a basico.js
					case 'ereg_nulo':
					case 'ereg_numero':
						break;
					///--> Excepciones a la validacion del cuit, al ser dinamicas no se pueden meter en un .js						
					case 'ef_cuit_excepciones':
						$excepciones = toba_ef_cuit::get_excepciones();
						echo toba_js::abrir();
						echo 'var ef_cuit_excepciones ='.toba_js::arreglo($excepciones, false);
						echo toba_js::cerrar();
						break;
					//--> Por defecto carga el archivo del consumo
					default:
						$instalacion = toba_instalacion::instancia();
						if (! $instalacion->es_js_comprimido()) {
							echo toba_js::incluir(toba_recurso::js("$consumo.js"));
						} else {
							//-- ¿Es algo comprimido?
							$comprimido = false;
							foreach (self::$consumos_compr as $compr) {
								if (strpos($consumo, $compr) !== false) {
									$comprimido = true;
									break;
								}
							}
							if (!$comprimido) {
								echo toba_js::incluir(toba_recurso::js("$consumo.js"));
							} elseif (! self::$comprimido_cargado) {
								//--- Es el comprimido y nunca se cargo, entonces cargarlo
								$archivo = 'toba_'.$instalacion->get_numero_version().'.js';
								echo toba_js::incluir(toba_recurso::js($archivo));
								self::$comprimido_cargado = true;
							}
						}
						break;
		        }
			}
		}
	}
	
	static function finalizar()
	{
		//echo toba_js::ejecutar('toba.confirmar_inclusion('. toba_js::arreglo(self::$cargados) .')');	
	}
	
	//----------------------------------------------------------------------------------
	//						CONVERSION DE TIPOS
	//----------------------------------------------------------------------------------	

	/**
	 * Conversion de una variable booleana a javascript
	 */
	static function bool($bool)
	{
		return ($bool) ? 'true' : 'false';
	}

	/**
	 * Conversion de un arreglo a jasvascript
	 *
	 * @param array $arreglo Variable a convertir
	 * @param boolean $es_assoc El primer nivel del arreglo es asociativo ?
	 * @param boolean $seg_nivel_assoc El segundo nivel del arreglo es asociativo ?
	 */
	static function arreglo($arreglo, $es_assoc = false, $seg_nivel_assoc=true)
	{
		$js = ""; $escapador = toba::escaper();
		if ($es_assoc) {
			if (count($arreglo) > 0) {
				$js .= "{";
				foreach($arreglo as $id => $valor) {
					$id_js = $escapador->escapeJs($id);
					if (is_array($valor)) { 
						//RECURSIVIDAD
						$js .= "'$id_js': ".self::arreglo($valor, $seg_nivel_assoc)." ,";
					} elseif (is_bool($valor)) {
						$js .= "'$id_js': ". self::bool($valor) . ' ,';
					} else {
						$valor = addslashes($valor);				
						$js .= "'$id_js': '". $escapador->escapeJs($valor)."', ";
					}
				}
				$js = substr($js, 0, -2);
				$js .= "}";
			} else {
				$js = 'new Object()';
			}
		} else {	//No asociativo
			$js .="[ ";
			foreach($arreglo as $valor) {
				if (!isset($valor)) {
					$js .= "null,";
				} elseif (is_numeric($valor)) {
					$js .= $escapador->escapeJs("$valor,");
				} elseif (is_bool($valor)) {
					$js .= self::bool($valor) . ' ,';
				} elseif (is_array($valor)) {
					//RECURSIVIDAD
					$js .= self::arreglo($valor, $seg_nivel_assoc).",";
				} else {
					//$valor = addslashes($valor);				
					$js .= "'". $escapador->escapeJs($valor)."',";
				}
			}
			$js = substr($js, 0, -1);
			$js .= " ]";
		}
		return $js;		
	}	
	
	/**
	 * Reemplaza los strings multilinea por cadenas válidas en JS
	 */	
	static function string($cadena)
	{
		return pasar_a_unica_linea($cadena);
	}

	static function sanear_string($cadena)
	{
		$unica_linea = self::string($cadena);
		return toba::escaper()->escapeJs($unica_linea);
	}
	
	/**
	 * Retorna el codigo necesario para crear un evento en js
	 *
	 * @param string $id Id. del evento
	 * @param string $evento Datos del evento, en forma de arreglo
	 */
	static function evento($id, $evento, $parametros = null, $es_implicito = false)
	{
		$escapador = toba::escaper();
		$js_confirm = isset( $evento['confirmacion'] ) ? "'". $escapador->escapeJs($evento['confirmacion'])."'" : "''";
		$js_validar = isset( $evento['maneja_datos'] ) ? toba_js::bool($evento['maneja_datos']) : "true";
		if (is_array($parametros)) {
			$param = ", ".toba_js::arreglo($parametros, true);
		} else {
			$param = (isset($parametros)) ? ", '".str_replace('"',"'", $escapador->escapeJs($parametros))."'" : '';			
		}

		$implicito = '';
		if ($es_implicito) { 				
			$implicito =  ($param == '')? ",''" : '';			
			$implicito .= ', '. toba_js::bool(true);
		}
		$id_js = $escapador->escapeJs($id);
		return "new evento_ei('$id_js', $js_validar, $js_confirm $param $implicito)";
	}	
	
	//----------------------------------------------------------------------------------
	//						UTILIDADES
	//----------------------------------------------------------------------------------		

}
?>