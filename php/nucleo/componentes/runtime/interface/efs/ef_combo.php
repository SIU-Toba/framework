<?php
require_once("nucleo/componentes/runtime/interface/efs/ef.php");

abstract class ef_seleccion extends ef
{
	protected $valores;				//Array con valores de la lista
	protected $predeterminado;		//Si el combo tiene predeterminados, tengo que inicializarlo
	protected $no_seteado;
	protected $categorias;
	protected $estado_nulo = null;
	protected $estado_defecto;

	static function get_parametros()
	{
		$parametros = parent::get_parametros_carga();
		$parametros["no_seteado"]["descripcion"]="Descripcion que representa la NO-SELECCION del combo";
		$parametros["no_seteado"]["opcional"]=1;	
		$parametros["no_seteado"]["etiqueta"]="Desc. No seleccion";	

		$parametros["predeterminado"]["descripcion"]="Valor predeterminado, si tiene varias claves separar con barra (/)";
		$parametros["predeterminado"]["opcional"]=1;	
		$parametros["predeterminado"]["etiqueta"]="Valor predeterminado";
		
		
		$parametros["dependencias"]["descripcion"]="El estado dependende de otro EF (CASCADA). Lista de EFs separada por comas";
		$parametros["dependencias"]["opcional"]=1;	
		$parametros["dependencias"]["etiqueta"]="Dependencias";	
		$parametros["dependencias_opcionales"]["descripcion"]="(1 o 0) Indica si las dependencias deben estar todas con valores para recargar sus valores";
		$parametros["dependencias_opcionales"]["opcional"]=1;
		$parametros["dependencias_opcionales"]["etiqueta"]="Dependencias son opcional";		
		
		$parametros["agrupador_dao"]["descripcion"]="Método de donde se obtienen las distintas categorias para agrupar.";
		$parametros["agrupador_dao"]["opcional"]=0;	
		$parametros["agrupador_dao"]["etiqueta"]="Agrupador - Método";	
		$parametros["agrupador_clase"]["descripcion"]="Nombre de la clase";
		$parametros["agrupador_clase"]["opcional"]=1;	
		$parametros["agrupador_clase"]["etiqueta"]="Agrupador - Clase";	
		$parametros["agrupador_include"]["descripcion"]="Archivo donde se encuentra definida la clase";
		$parametros["agrupador_include"]["opcional"]=1;	
		$parametros["agrupador_include"]["etiqueta"]="Agrupador - Include";	
		$parametros["agrupador_clave"]["descripcion"]="Indica que INDICES de la matriz de grupos se utilizaran como CLAVE (Si son varios separar con comas). ".
													  "Estas claves tienen que estar presentes tanto en el dao como en el agrupador";
		$parametros["agrupador_clave"]["opcional"]=0;	
		$parametros["agrupador_clave"]["etiqueta"]="Agrupador - resultado: CLAVE";	
		$parametros["agrupador_valor"]["descripcion"]="Indica que INDICE de la matriz de grupos se utilizara como DESCRIPCION";
		$parametros["agrupador_valor"]["opcional"]=0;	
		$parametros["agrupador_valor"]["etiqueta"]="Agrupador - resultado: DESC.";
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		
		//---------------------- Manejo de Estado por defecto  ------------------		
		if (isset($parametros["predeterminado"]) && $parametros["predeterminado"]!="") {
			if (is_array($this->dato)) {
				$this->estado_defecto = array();
				$param = explode(',', $parametros["predeterminado"]);
				for ($i=0; $i < count($this->dato); $i++) {
					$this->estado_defecto[$this->dato[$i]] = trim($param[$i]);
				}
			} else {
				$this->estado_defecto = trim($parametros["predeterminado"]);	
			}
		}		
		
		if (! isset($this->estado_defecto)) {
			$this->estado_defecto = $this->estado_nulo;
		}	
		$this->set_estado($this->estado_defecto);

	}

	function es_seleccionable()
	{
		return true;
	}
	
	function cargar_valores($datos)
	{
		if ($datos == null) {
			$this->input_extra = " disabled ";
		}
		$this->valores = $datos;
	}

	function get_estado()
	{
		return $this->estado;
	}	
	
	function get_descripcion_estado()
	{
		if ( isset( $this->estado ) && isset( $this->valores[ $this->estado ] ) ) {
			return $this->valores[ $this->estado ];
		} else {
			return null;	
		}
	}

	protected function es_estado_nulo($estado)
	{
		if (is_array($this->dato)) {
			//Si el estado es nulo tengo que manejarlo de una forma especial
			$valores = "";
			foreach ($estado as $valor) {
				$valores .= $valor;
			}		
			return trim($valores) === '';
		} else {
			return $estado === null;	
		}
	}

	protected function get_estado_para_input()
	{
		if ($this->es_estado_nulo($this->estado)) {
			return apex_ef_no_seteado;	
		}
		if (is_array($this->dato)) {
			//Maneja muchos datos
			$opcion = "";
		    foreach ($this->dato as $dato) { //Sigo el orden de las columnas
	        	$opcion .= $this->estado[$dato] . apex_ef_separador;
		    }
		   //Saca el ultimo apex_ef_separador
			$opcion = substr($opcion, 0, -1 * strlen(apex_ef_separador));
			return $opcion;
		} else {
			return $this->estado;				
		}
	}
	
	function set_estado($estado)
	{
		if (isset($estado) && is_array($this->dato)) {
			//Maneja multiples datos
			//El estado tiene el formato adecuado?
			$cant_datos = count($this->dato);
			if (count($estado) <> $cant_datos) {
				throw new excepcion_toba("Ha intentado cargar el combo '{$this->id}' con un array que posee un formato inadecuado " .
								" se esperaban {$cant_datos} claves, pero se utilizaron: ". count($estado) . ".");
			}
		}
		if ($this->es_estado_nulo($estado)) {
			$this->estado = $this->estado_nulo;
		} else {
    		$this->estado = $estado;
		}
	}
	
	function cargar_estado_post()
	{
		if (! isset($_POST[$this->id_form])) {
			return false;
		}
		if (is_array($this->dato)) {
            //Deduzco el estado de la opcion seleccionada
   			$seleccion = $_POST[$this->id_form];
			if ($seleccion == apex_ef_no_seteado){
				$this->estado = $this->estado_nulo;
			} else {
				$cant_datos = count($this->dato);
	            $valores = explode(apex_ef_separador, $seleccion);
				if (count($valores) <> $cant_datos) {
					throw new excepcion_toba("Ha intentado cargar el combo '{$this->id}' con un array que posee un formato inadecuado " .
									" se esperaban {$cant_datos} claves, pero se utilizaron: ". count($valores) . ".");
				}
				$this->estado = array();
				for ($i=0; $i < count($this->dato); $i++) {
				   	$this->estado[$this->dato[$i]] = $valores[$i];
				}
			}
		} else {
			$estado = $_POST[$this->id_form];
			if ($estado == apex_ef_no_seteado) {
				$estado = null;	
			}
			$this->set_estado($estado);
		}
		return true;
	}
	
	function activado()
	{
		return isset($this->estado) && !$this->es_estado_nulo($this->estado);
	}

	function get_consumo_javascript()
	{
		$consumos = array('interface/ef','interface/ef_combo');
		return $consumos;
	}	
	
	
}
//########################################################################################################
//########################################################################################################

class ef_combo extends ef_seleccion
{

	function get_input()
	{
		$html = "";

		//El estado que puede contener muchos datos debe ir en un unico string
		$estado = $this->get_estado_para_input();
        if ($this->solo_lectura) {
        	$input = form::select("",$estado, $this->valores, "ef-combo", "disabled");	
			$input .= form::hidden($this->id_form, $estado);
            return $input;
		} else {
			$js = '';
			if ($this->cuando_cambia_valor != '') {
				$js = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
			}
			$html .= form::select($this->id_form, $estado ,$this->valores, 'ef-combo', $js . $this->input_extra, $this->categorias);
			return $html;
		}
	}	

	function crear_objeto_js()
	{
		return "new ef_combo({$this->parametros_js()})";
	}
}

class ef_radio extends ef_seleccion 
{
	function get_input()
	{
		$estado = $this->get_estado_para_input();
		$html = "";		
		if ($this->solo_lectura) {
			foreach ($this->valores as $id => $descripcion) {
				$html .= "<label class='ef-radio'>";
				if ($id == $estado) {
					$html .= recurso::imagen_apl('radio_checked.gif',true,16,16);
				} else  {
					$html .= recurso::imagen_apl('radio_unchecked.gif',true,16,16);
				}
				$html .= "$descripcion</label>\n";
			}
		} else {
			$html .= form::radio($this->id_form, $estado, $this->valores);
		}
		return $html;
	}
	
	function parametros_js()
	{
		return parent::parametros_js().", \"{$this->get_cuando_cambia_valor()}\"";	
	}
	
	function crear_objeto_js()
	{
		return "new ef_radio({$this->parametros_js()})";
	}	
}

?>
