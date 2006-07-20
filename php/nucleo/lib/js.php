<?php

class js
//Clase para funciones javascript.
{
	//--- SINGLETON
	private static $instancia;
	private static $cargados = array();
	private static $comprimido_cargado = false;
	protected $nivel_identado = 0;
	private static $consumos_compr = array('componentes/', 'efs/', 'basicos/');
	private static $consumos_basicos = array(
						'basicos/basico', 'basicos/toba', 'utilidades/datadumper', 
						'basicos/comunicacion_server', 'basicos/cola_mensajes',
						'basicos/vinculador');
	
	static function instancia() 
	{
		if (! isset(self::$instancia)) {
			self::$instancia = new js();
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

	static function abrir()
	{
		return "<SCRIPT  language='JavaScript".js::version()."' type='text/javascript'>\n";
	}

	static function cerrar()
	{
		return "\n</SCRIPT>\n";
	}

	static function incluir($archivo) 
	{
		return "<SCRIPT language='JavaScript".js::version()."' type='text/javascript' src='$archivo'></SCRIPT>\n";		
	}

	static function ejecutar($codigo) 
	{
		return js::abrir().$codigo.js::cerrar();
	}
	
	static function cargar_consumos_basicos()
	{
		$imagenes = array(	'error' => recurso::imagen_apl('error.gif', false), 
							'info' => recurso::imagen_apl('info_chico.gif', false), 
							'maximizar' => recurso::imagen_apl('sentido_des_sel.gif', false), 
							'minimizar' => recurso::imagen_apl('sentido_asc_sel.gif', false),
							'expandir'  => recurso::imagen_apl('expandir_vert.gif', false),
							'contraer'  => recurso::imagen_apl('contraer_vert.gif', false),
							'expandir_nodo' => recurso::imagen_apl('arbol/expandir.gif', false),
							'contraer_nodo' => recurso::imagen_apl('arbol/contraer.gif', false),
							'esperar' => recurso::imagen_apl('wait.gif', false)
							);
		echo js::abrir();
		echo "var toba_alias='".recurso::path_apl()."';\n";
		echo "var toba_prefijo_vinculo=\"".toba::get_vinculador()->crear_autovinculo()."\";\n";
		echo "var toba_hilo_qs='".apex_hilo_qs_item."'\n";
		echo "var toba_hilo_separador='".apex_qs_separador."'\n";
		echo "var toba_hilo_qs_servicio='".apex_hilo_qs_servicio."'\n";
		echo "var toba_hilo_qs_menu='".apex_hilo_qs_menu."'\n";
		echo "var apex_hilo_qs_celda_memoria='".apex_hilo_qs_celda_memoria."'\n";
		echo "var toba_hilo_qs_objetos_destino='".apex_hilo_qs_objetos_destino."'\n";
		echo "var toba_hilo_item=".js::arreglo(toba::get_hilo()->obtener_item_solicitado(), false)."\n";
		echo "var lista_imagenes=".js::arreglo($imagenes, true).";";
		echo "var apex_solicitud_tipo='".toba::get_solicitud()->get_tipo()."'\n";		
		echo js::cerrar();		
		
		//Incluyo el javascript STANDART	
		self::cargar_consumos_globales(self::$consumos_basicos);
	}
	
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
						echo js::incluir(recurso::js("fckeditor/fckeditor.js"));
						break;
					//--> Por defecto carga el archivo del consumo
					default:
						$instalacion = info_instalacion::instancia();
						if (! $instalacion->es_js_comprimido()) {
							echo js::incluir(recurso::js("$consumo.js"));
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
								echo js::incluir(recurso::js("$consumo.js"));
							} elseif (! self::$comprimido_cargado) {
								//--- Es el comprimido y nunca se cargo, entonces cargarlo
								$archivo = 'toba_'.$instalacion->get_numero_version().'.js';
								echo js::incluir(recurso::js($archivo));
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
		//echo js::ejecutar('toba.confirmar_inclusion('. js::arreglo(self::$cargados) .')');	
	}
	
	//----------------------------------------------------------------------------------
	//						CONVERSION DE TIPOS
	//----------------------------------------------------------------------------------	
	static function bool($bool)
	{
		return ($bool) ? "true" : "false";
	}
	
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
	
	static function string($cadena)
	//Reemplaza los strings multilinea por cadenas válidas en JS
	{
		return pasar_a_unica_linea($cadena);
	}

}
?>