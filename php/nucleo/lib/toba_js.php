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
						'basicos/basico', 'basicos/toba', 'utilidades/datadumper', 
						'basicos/comunicacion_server', 'basicos/notificacion',
						'basicos/vinculador');
	
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
	 * Incluye los consumos globales básicos para utilizar el framework
	 */
	static function cargar_consumos_basicos()
	{
		if (! self::$basicos_cargados) {
			$imagenes = array(	'error' => toba_recurso::imagen_toba('error.gif', false), 
								'info' => toba_recurso::imagen_toba('info_chico.gif', false), 
								'maximizar' => toba_recurso::imagen_toba('nucleo/sentido_des_sel.gif', false), 
								'minimizar' => toba_recurso::imagen_toba('nucleo/sentido_asc_sel.gif', false),
								'expandir'  => toba_recurso::imagen_skin('expandir_vert.gif', false),
								'contraer'  => toba_recurso::imagen_skin('contraer_vert.gif', false),
								'expandir_nodo' => toba_recurso::imagen_toba('nucleo/expandir.gif', false),
								'contraer_nodo' => toba_recurso::imagen_toba('nucleo/contraer.gif', false),
								'esperar' => toba_recurso::imagen_toba('wait.gif', false),
								'cerrar' => toba_recurso::imagen_toba('nucleo/cerrar_ventana.gif', false),
								);
			echo toba_js::abrir();
			echo "var toba_alias='".toba_recurso::url_toba()."';\n";
			echo "var toba_proyecto_alias='".toba_recurso::url_proyecto()."';\n";
			if (toba_editor::activado()) {
				echo 'var toba_proyecto_editado_alias = "'.toba_editor::get_url_previsualizacion()."\";\n";	
			}
			
			echo "var toba_prefijo_vinculo=\"".toba::vinculador()->crear_autovinculo()."\";\n";
			echo "var toba_hilo_qs='".apex_hilo_qs_item."'\n";
			echo "var toba_hilo_separador='".apex_qs_separador."'\n";
			echo "var toba_hilo_qs_servicio='".apex_hilo_qs_servicio."'\n";
			echo "var toba_hilo_qs_menu='".apex_hilo_qs_menu."'\n";
			echo "var apex_hilo_qs_celda_memoria='".apex_hilo_qs_celda_memoria."'\n";
			echo "var toba_hilo_qs_objetos_destino='".apex_hilo_qs_objetos_destino."'\n";
			echo "var toba_hilo_item=".toba_js::arreglo(toba::memoria()->get_item_solicitado(), false)."\n";
			echo "var lista_imagenes=".toba_js::arreglo($imagenes, true).";";
			echo "var apex_solicitud_tipo='".toba::solicitud()->get_tipo()."'\n";		
			echo toba_js::cerrar();		
			//Incluyo el javascript STANDART	
			
			self::cargar_consumos_globales(self::$consumos_basicos);
			
			///---Arreglo PNGs IE
			$url = toba_recurso::js("utilidades/pngbehavior.htc");			
			echo "<!--[if lt IE 7]>
				<style type='text/css'>
					img {
						behavior: url('$url');
					}
				</style>
				<![endif]-->\n";
			self::$basicos_cargados = true;
		}
	}
	
	/**
	 * Incluye una serie de librerías o consumos javascript
	 * @param array $consumos Lista de consumos, un consumo es el path relativo a www, sin la ext. js
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
					//--> Codigo necesario para el EDITOR HTML embebido
					case 'fck_editor':
						echo toba_js::incluir(toba_recurso::js("fckeditor/fckeditor.js"));
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
		return ($bool) ? "true" : "false";
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
		$js = "";
		if ($es_assoc) {
			if (count($arreglo) > 0) {
				$js .= "{";
				foreach($arreglo as $id => $valor) {
					if (is_array($valor)) { 
						//RECURSIVIDAD
						$js .= "$id: ".self::arreglo($valor, $seg_nivel_assoc)." ,";
					} else {
						$js .= "$id: '$valor', ";
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
					$js .= "$valor,";
				} elseif (is_array($valor)) {
					//RECURSIVIDAD
					$js .= self::arreglo($valor, $seg_nivel_assoc).",";
				} else {
					$js .= "'$valor',";
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

	/**
	 * Retorna el codigo necesario para crear un evento en js
	 *
	 * @param string $id Id. del evento
	 * @param string $evento Datos del evento, en forma de arreglo
	 */
	static function evento($id, $evento, $parametros = null)
	{
		$js_confirm = isset( $evento['confirmacion'] ) ? "'{$evento['confirmacion']}'" : "''";
		$js_validar = isset( $evento['maneja_datos'] ) ? toba_js::bool($evento['maneja_datos']) : "true";
		if (is_array($parametros))
			$param = ", ".toba_js::arreglo($parametros, true);
		else		
			$param = (isset($parametros)) ? ", '$parametros'" : '';
		return "new evento_ei('$id', $js_validar, $js_confirm $param)";
	}	

}
?>