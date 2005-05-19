<?php

class js
//Clase para funciones javascript.
{
	//--- SINGLETON
	private static $instancia;
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
			switch ($consumo) {
				//--> Expresion regular que machea NULOS
				case 'ereg_nulo':
					echo js::ejecutar(" ereg_nulo = /^\s*$/;");
					break;
				//--> Expresion regular que machea NUMEROS
				case 'ereg_numero':
					echo js::ejecutar(" ereg_numero = /^[1234567890,.-]*$/;"); 
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
					echo "<img id='ef_warning' src='$warn' style='margin: 0px 0px 0px 0px; display:none; position: absolute;'>";
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
				case 'clases/toba': 
					echo js::incluir(recurso::js("$consumo.js"));
					$img_error = recurso::imagen_apl('error.gif', false);
					$img_info = recurso::imagen_apl('info_chico.gif', false);
					$img_max = recurso::imagen_apl('sentido_des_sel.gif', false);
					$img_min = recurso::imagen_apl('sentido_asc_sel.gif', false);
					$imagenes = array('error' => $img_error, 'info' => $img_info, 'maximizar' => $img_max, 'minimizar' => $img_min);
					echo js::abrir();
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
	//----------------------------------------------------------------------------------
	//						CONVERSION DE TIPOS
	//----------------------------------------------------------------------------------	
	static function bool($bool)
	{
		return ($bool) ? "true" : "false";
	}
}
?>