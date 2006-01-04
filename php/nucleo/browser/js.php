<?php

class js
//Clase para funciones javascript.
{
	//--- SINGLETON
	private static $instancia;
	private static $cargados = array();
	protected $nivel_identado = 0;
	
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
	//-------------------------------------------------------------------------------------
	static function abrir()
	{
		return "<SCRIPT  language='JavaScript".js::version()."' type='text/javascript'>\n";
	}
	//-------------------------------------------------------------------------------------
	static function cerrar()
	{
		return "\n</SCRIPT>\n";
	}
	//-------------------------------------------------------------------------------------	
	static function incluir($archivo) 
	{
		return "\n<SCRIPT language='JavaScript".js::version()."' type='text/javascript' src='$archivo'></SCRIPT>\n";
	}
	//-------------------------------------------------------------------------------------
	static function ejecutar($codigo) 
	{
		return js::abrir().$codigo.js::cerrar();
	}
	//-------------------------------------------------------------------------------------
	static function cargar_consumos_globales($consumos)
	{
		$consumos = array_unique($consumos);
		foreach ($consumos as $consumo)	{
			//Esto asegura que sólo se puede cargar una vez
			if (! in_array($consumo, self::$cargados)) {
				self::$cargados[] = $consumo;
				switch ($consumo) {
					//--> Expresion regular que machea NULOS
					case 'ereg_nulo':
						echo js::ejecutar(" ereg_nulo = /^\s*$/;");
						break;
					//--> Expresion regular que machea NUMEROS
					case 'ereg_numero':
						echo js::ejecutar(" ereg_numero = /^[1234567890,.-]*$/;"); 
						break;
					//--> Tooltips HTML
					case 'tooltips':
						echo "<div id='dhtmltooltip'></div>";
						echo js::incluir(recurso::js("$consumo.js"));
						break;
					//--> Codigo necesario para los ef_fecha
					case 'fecha':
						echo js::incluir(recurso::js("calendario_es.js"));
						echo js::incluir(recurso::js("validacion_fecha.js"));
						echo js::ejecutar("document.write(getCalendarStyles());" .
							 "\nvar calendario = new CalendarPopup('div_calendario');calendario.showYearNavigation();calendario.showYearNavigationInput();");
						echo "<DIV id='div_calendario' style='VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white'></DIV>\n";
						break;
					//--> Codigo necesario para el EDITOR HTML embebido
					case 'fck_editor':
						echo js::incluir(recurso::js("fckeditor/fckeditor.js"));
						break;
					case 'interface/ef':
						$warn = recurso::imagen_apl('error.gif', false);
						echo "<img id='ef_warning' src='$warn' style='left: 0px;margin: 0px 0px 0px 0px; display:none; position: absolute;'>";
						echo js::incluir(recurso::js("$consumo.js"));
						break;
					case 'subModal':
						echo "
							<div id='popupMask'>&nbsp;</div>
							<div id='popupContainer'>
								<div id='popupInner'>
									<div id='popupTitleBar'>
										<div id='popupTitle'></div>
										<div id='popupControls'>
										<button class='abm-input' onclick='hidePopWin(false)'>$img_cerrar</button>
										</div>
									</div>
									<div style='width:100%;height:80%;background-color:transparent;' scrolling='auto' allowtransparency='true' id='popupFrame' name='popupFrame'></div>
									<div style='width:100%;height:20%;text-align:center;' id='popupBotonera'></div>
								</div>
							</div>
						";
						echo js::incluir(recurso::js("$consumo.js"));					
						break;
					case 'comunicacion_server':
						echo js::abrir();
						echo "var apex_frame_com='".apex_frame_com."'\n";
						echo js::cerrar();
						echo js::incluir(recurso::js("$consumo.js"));
						break;
					case 'clases/toba': 
						echo js::incluir(recurso::js("$consumo.js"));
						$imagenes = array(	'error' => recurso::imagen_apl('error.gif', false), 
											'info' => recurso::imagen_apl('info_chico.gif', false), 
											'maximizar' => recurso::imagen_apl('sentido_des_sel.gif', false), 
											'minimizar' => recurso::imagen_apl('sentido_asc_sel.gif', false),
											'expandir'  => recurso::imagen_apl('expandir_vert.gif', false),
											'contraer'  => recurso::imagen_apl('contraer_vert.gif', false),
											'expandir_nodo' => recurso::imagen_apl('arbol/expandir.gif', false),
											'contraer_nodo' => recurso::imagen_apl('arbol/contraer.gif', false)
											);
						echo js::abrir();
						echo "var toba_prefijo_vinculo=\"".toba::get_hilo()->prefijo_vinculo()."\";\n";
						echo "var toba_hilo_qs='".apex_hilo_qs_item."'\n";
						echo "var toba_hilo_separador='".apex_qs_separador."'\n";
						echo dump_array_javascript($imagenes, 'lista_imagenes');
						echo js::cerrar();
						break;
					break;					
					//--> Por defecto carga el archivo del consumo
					default:
						echo js::incluir(recurso::js("$consumo.js"));
		        }
			}
		}
	}
	//----------------------------------------------------------------------------------
	//						CONVERSION DE TIPOS
	//----------------------------------------------------------------------------------	
	static function bool($bool)
	{
		return ($bool) ? "true" : "false";
	}
	
	static function arreglo($arreglo, $es_assoc = false)
	{
		$js = "";
		if ($es_assoc) {
			if (count($arreglo) > 0) {
				$js .= "{";
				foreach($arreglo as $id => $valor) {
					if (is_array($valor)) { 
						//RECURSIVIDAD
						$js .= "$id: ".self::arreglo($valor, true)." ,";
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
				if (is_numeric($valor))
					$js .= "$valor,";
				elseif (is_array($valor)) {
					//RECURSIVIDAD
					$js .= self::arreglo($valor, true).",";
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