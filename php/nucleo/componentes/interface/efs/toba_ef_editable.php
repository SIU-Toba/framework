<?php

interface toba_callback_errores_validacion {
	
	/**
	 * Atrapa la validacion de tamaño maximo de un campo
	 *
	 * @param toba_ef_editable $ef campo en cuestión
	 * @param int $maximo Tamaño maximo definido
	 * @param string $estado Estado actual a validado
	 * @return boolean/String True para descartar el error, o un string para mostrar un mensaje personalizado
	 */
	public function editable_maximo(toba_ef_editable $ef, $maximo, $estado);
}

/**
 * Elemento editable equivalente a un <input type='text'>
 * Puede manejar una mascara.
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable ef_editable
 */
class toba_ef_editable extends toba_ef
{
	protected $tamano = 20;
	protected $maximo;
	protected $mascara;
	protected $expreg;
	protected $unidad;
	protected $clase_css = 'ef-input';
	protected static $callback_errores_validacion = null;
	protected static $ratio_pixel;
	protected static $limite_minimo = 5;
	protected $placeholder='';
	
	
	static function get_lista_parametros_carga()
	{
		$parametros = toba_ef::get_lista_parametros_carga_basico();    
		array_borrar_valor($parametros, 'carga_lista');
		array_borrar_valor($parametros, 'carga_col_clave');
		array_borrar_valor($parametros, 'carga_col_desc');
		return $parametros;    	
	}

	static function get_lista_parametros()
	{
		$param[] = 'edit_tamano';
		$param[] = 'edit_maximo';
		$param[] = 'edit_mascara';
		$param[] = 'edit_unidad';
		$param[] = 'edit_expreg';
		$param[] = 'edit_placeholder';
		return $param;    	
	}

	static function set_callback_errores_validacion(toba_callback_errores_validacion $callback) 
	{
		self::$callback_errores_validacion = $callback;	
	}
    	
	/**
	 * Permite hacer que todos los efs traduzcan su tamaño visual a pixeles con un porcentaje dado
	 * @param int $porcentaja 
	 */
	static function set_tamano_multiplicado_pixels($porcentaje = 1)
	{
		self::$ratio_pixel = $porcentaje;
	}
	
	static function set_limite_minimo_caracteres($cantidad)
	{
		self::$limite_minimo = $cantidad;
	}
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		//VAlor FIJO
		if(isset($parametros['estado_defecto'])){
			$this->estado_defecto = $parametros['estado_defecto'];
			$this->estado = $this->estado_defecto;
		}
		//Tamaño del editable
		if (isset($parametros['edit_tamano'])) {
			$this->tamano = $parametros['edit_tamano'];	
		}
		//Maximo de caracteres
		if(isset($parametros['edit_maximo']) && $parametros['edit_maximo']!=""){
			$this->maximo = $parametros['edit_maximo'];
		}
		//Mascara
		if(isset($parametros['edit_mascara'])) {
			$this->mascara = $parametros['edit_mascara'];		
		}
		if (isset($parametros['edit_unidad'])) {
			$this->unidad = $parametros['edit_unidad'];
			unset($parametros['edit_unidad']);	
		}		
		if (isset($parametros['edit_expreg'])) {
			$this->expreg = $parametros['edit_expreg'];
			unset($parametros['expreg']);	
		}
		if (isset($parametros['placeholder'])) {
			$this->set_placeholder($parametros['placeholder']);
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
	
	function set_expreg($expreg)   
	{  
		$this->expreg = $expreg;      
	} 
	 
	/**
	 * En el caso del editable las opciones representa su estado por defecto
	 * @param string $opciones
	 */
	function set_opciones($opciones, $maestros_cargados=true)
	{
		if (! $maestros_cargados) {
			$this->solo_lectura = true;
		}
		if (!isset($this->estado)) {
			$this->estado = $opciones;
		}		
	}
	
	function set_estado($estado)
	{
		if(isset($estado)){								
			$this->estado=trim($estado);
		} else {
			$this->estado = null;	
		}
	}
	
	function set_placeholder($msj)
	{
		$this->placeholder = $msj;
	}
	
	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])) {
			$this->estado = trim($_POST[$this->id_form]);
		} else {
			$this->estado = null;
		}
	}	

	function tiene_estado()
	{
		if (isset($this->estado)) {
			return ($this->estado != "");
		} else{
			return false;
		}
	}

	function get_estilo_visualizacion_pixeles()
	{		
		if (isset(self::$ratio_pixel) && ($this->tamano > self::$limite_minimo)) {
			$en_pixels = floor($this->tamano * self::$ratio_pixel);
			return ' style=\'width: '.$en_pixels.'px;\' ';
		}
		return '';
	}
	
	protected function get_info_placeholder()
	{		
		if (trim($this->placeholder) != '') {
			$ph  = texto_plano($this->placeholder);
			return " placeholder='$ph' ";
		}
		return '';
	}
	
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
		//Si el ef tiene estado realizo chequeos
		if ($this->tiene_estado() && $this->estado != '') {
			//Hago el chequeo x expresion regular si existiera
			if (isset($this->expreg) && !preg_match($this->expreg, $this->estado)) {
				return 'No es válido';
			}

			//Evaluo si se supera el maximo de caracteres permitido
			if (isset($this->maximo) && !is_null($this->maximo) && (strlen($this->estado) > $this->maximo)) {
				if (! isset(self::$callback_errores_validacion)) {
					return "Supera el ancho máximo {$this->maximo}";
				} else {
					return self::$callback_errores_validacion->editable_maximo($this, $this->maximo, $this->estado);
				}
			}
		}
		return true;
	}	
		
	function get_input()
	{
		$this->input_extra .= $this->get_estilo_visualizacion_pixeles();
		$this->input_extra .= $this->get_info_placeholder();		
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';
		$input = toba_form::text($this->id_form, $this->estado,$this->es_solo_lectura(),$this->maximo,$this->tamano, $this->clase_css, $this->javascript.' '.$this->input_extra.$tab);
		if (isset($this->unidad)) {
			$input = "<span class='ef-editable-unidad'>".$input .' '.$this->unidad.'</span>';
		}
		$input .= $this->get_html_iconos_utilerias();
		return $input;
	}
	
	function get_consumo_javascript()
	{
		$consumos = array('efs/mascaras', 'efs/ef', 'efs/ef_editable');
		return $consumos;
	}

	function parametros_js()
	{
		$exp = isset($this->expreg) ? addslashes($this->expreg) : '';
		return parent::parametros_js().", '{$this->mascara}', '$exp'";
	}
	
	function crear_objeto_js()
	{
		return "new ef_editable({$this->parametros_js()})";
	}	
	
}
//########################################################################################################
//########################################################################################################

/**
 * Elemento editable que sólo permite ingresar números
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable_numero ef_editable_numero
 */
class toba_ef_editable_numero extends toba_ef_editable
{
	protected $rango_inferior = array('limite' => '*', 'incluido' => 1);
	protected $rango_superior = array('limite' => '*', 'incluido' => 1);
	protected $cambio_rango = false;
	protected $tamano = 10;
	protected $mensaje_defecto;
	protected $clase_css = 'ef-numero';

	static function get_lista_parametros()
	{
		$param = parent::get_lista_parametros();
		$param[] = 'edit_rango';
		array_borrar_valor($param, 'edit_placeholder');
		return $param;
	}
    
  	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (isset($parametros['edit_rango'])) {
			$this->cambiar_rango($parametros['edit_rango']);
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	/**
	 * Permite modificar el rango de numeros permitido y el mensaje de error
	 * @param string $rango ej: "[0..100), Número positivo"
	 */
	function cambiar_rango($rango)
	{
		//Parseo del rango
		$limitadores = array('[', ']', '(', ')');
		$partes = explode(',', $rango, 2);

		//Determinación de límites
		$rango = trim($partes[0]);
		list($inferior, $superior) = explode('..',  str_replace($limitadores, '', $rango));
		$this->rango_inferior['limite'] = trim($inferior);
		if (strpos($rango, '(') !== false)
			$this->rango_inferior['incluido'] = 0;
		$this->rango_superior['limite']	= trim($superior);
		if (strpos($rango, ')') !== false)
			$this->rango_superior['incluido'] = 0;

		//Descripción
		if (isset($partes[1])) {
			$this->mensaje_defecto = $partes[1];
		}
		$this->cambio_rango = true;
	}
	
	protected function mensaje_validacion_rango()
	{
		if (isset($this->mensaje_defecto)) {
			return $this->mensaje_defecto;
		}
		$inferior = "";
		$superior = "";
		if ($this->rango_inferior['limite'] != '*') {
			$inferior .= (($this->rango_inferior['incluido']) ? " mayor o igual a " : " mayor a ").$this->rango_inferior['limite'];
		}
		if ($this->rango_superior['limite'] != '*') {
			$superior .= (($this->rango_superior['incluido']) ? " menor o igual a " : " menor a ").$this->rango_superior['limite'];
		}
		$nexo = ($inferior != "" && $superior != "") ? " y" : "";
		return " debe ser$inferior$nexo$superior.";
	}
	
	protected function validar_rango()
	{
		$ok = true;
		if ($this->rango_inferior['limite'] != '*') {
			if ($this->rango_inferior['incluido'])
				$ok = ($this->estado >= $this->rango_inferior['limite']);
			else
				$ok = ($this->estado > $this->rango_inferior['limite']);			
		}
		if ($ok && $this->rango_superior['limite'] != '*') {
			if ($this->rango_superior['incluido'])
				$ok = ($this->estado <= $this->rango_superior['limite']);
			else
				$ok = ($this->estado < $this->rango_superior['limite']);			
		}
		if (! $ok ) {
			return $this->mensaje_validacion_rango();
		}
		return true;
	}

	/**
	 * Valida que el número cumpla con el rango preestablecido (si lo hay)
	 */
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
		if ($this->tiene_estado()) {
			if ($this->confirma_excepcion_validacion()) {
				return true;
			}
			if (! is_numeric($this->estado)) {
				return "El campo es numérico";
			}
			return $this->validar_rango();
		}
		return true;
	}
	
	function get_descripcion_estado($tipo_salida)
	{
		$formato = new toba_formateo($tipo_salida);
		$desc =  $formato->formato_numero($this->get_estado());		
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$desc</div>";
			break;
			case 'xml':
			case 'pdf':
				return $desc;	
			case 'excel':
				return $formato->formato_millares($this->get_estado());
			break;
		}
	}		

	function get_input()
	{	
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';
		$input = toba_form::text($this->id_form, $this->estado,$this->es_solo_lectura(),$this->maximo,$this->tamano, $this->clase_css, $this->javascript.' '.$this->input_extra.$tab);
		if (isset($this->unidad)) {
			$input = "<span class='ef-editable-unidad'>".$input .' '.$this->unidad.'</span>';
		}
		$input .= $this->get_html_iconos_utilerias();
		return $input;
	}
	
	function parametros_js()
	{
		$inferior = "new Array('{$this->rango_inferior['limite']}', {$this->rango_inferior['incluido']})";
		$superior = "new Array('{$this->rango_superior['limite']}', {$this->rango_superior['incluido']})";
		return parent::parametros_js().", [$inferior, $superior], '{$this->mensaje_validacion_rango()}'";
	}		
	
	function crear_objeto_js()
	{
		return "new ef_editable_numero({$this->parametros_js()})";
	}	
}
//########################################################################################################
//########################################################################################################

/**
 * Elemento editable que sólo permite ingresar números que representan un valor monetario
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable_moneda ef_editable_moneda
 */
class toba_ef_editable_moneda extends toba_ef_editable_numero
{
	protected $rango_inferior = array('limite' => '0', 'incluido' => 1);
	protected $tamano = 12;	
	
	function crear_objeto_js()
	{
		return "new ef_editable_moneda({$this->parametros_js()})";
	}	
	
	protected function mensaje_validacion_rango()
	{
		if (! $this->cambio_rango) {
			return ' debe ser un importe positivo.';
		}
		return parent::mensaje_validacion_rango();
	}	
	
	function get_descripcion_estado($tipo_salida)
	{
		$formato = new toba_formateo($tipo_salida);
		$desc =  $formato->formato_moneda($this->get_estado());
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$desc</div>";
			break;
			case 'xml':
			case 'pdf':
				return $desc;	
			break;
			case 'excel':
				return $formato->formato_moneda($this->get_estado());
		}
	}
}

//########################################################################################################
//########################################################################################################

/**
 * Elemento editable que sólo permite ingresar números que representan un porcentaje
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable_porcentaje ef_editable_porcentaje
 */
class toba_ef_editable_numero_porcentaje extends toba_ef_editable_numero
{
	protected $rango_inferior = array('limite' => '0', 'incluido' => 1);
	protected $rango_superior = array('limite' => '100', 'incluido' => 1);
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (! isset($parametros['edit_tamano']))
			$parametros['edit_tamano']= 4;
		if (!isset($parametros['edit_unidad'])) {
			$parametros['edit_unidad'] = '%';
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function crear_objeto_js()
	{
		return "new ef_editable_porcentaje({$this->parametros_js()})";
	}	
	
	protected function mensaje_validacion_rango()
	{
		if (! $this->cambio_rango) {
			return ' debe estar entre 0% y 100%.';
		}
		return parent::mensaje_validacion_rango();
	}		
	
	function get_descripcion_estado($tipo_salida)
	{
		$formato = new toba_formateo($tipo_salida);
		$desc =  $formato->formato_porcentaje($this->get_estado());
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$desc</div>";
			break;
			case 'xml':
			case 'pdf':
				return $desc;	
			case 'excel':
				return $formato->formato_porcentaje($estado);
			break;
		}
	}	
}

//########################################################################################################
//########################################################################################################

/**
 * Elemento editable que permite ingresar contraseñas, con o sin campo de confirmación
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable_clave ef_editable_clave
 */
class toba_ef_editable_clave extends toba_ef_editable
{
	protected $confirmar_clave = false;
	
	static function get_lista_parametros()
	{
		$param[] = 'edit_tamano';
		$param[] = 'edit_maximo';
		$param[] = 'edit_confirmar_clave';
		return $param;
	}
    
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if ( isset($parametros['edit_confirmar_clave'])) {
			$this->confirmar_clave = $parametros['edit_confirmar_clave'];
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
    
	function get_input()
	{
		$this->input_extra .= $this->get_estilo_visualizacion_pixeles();
		$tab = ' tabindex="'.$this->padre->get_tab_index(2).'"';
		$estado = isset($this->estado)? $this->estado : "";
		
		$opciones_extra = $this->input_extra . $tab;
		$estilo_extra = ' style="display:block;" ';
		$js = " onKeyUp=\"{$this->objeto_js()}.runPassword(this.value,'{$this->id_form}');\" ";
		$html = toba_form::password($this->id_form,$estado, $this->maximo, $this->tamano, 'ef-input',$js. $opciones_extra );
		if ($this->confirmar_clave) {										//Agrego div para mostrar la 'fortaleza' del pwd y tambien ef para confirmacion
			$html .= '<div  class="ef-editable-clave-barra-info">					
					<div id="'.$this->id_form.'_text" style="font-size: 10px;"></div>
					<div id="'.$this->id_form.'_bar" class="ef-editable-clave-fortaleza"></div></div>';			
			$html .= toba_form::password($this->id_form ."_test", $estado, $this->maximo, $this->tamano, 'ef-input', $opciones_extra . $estilo_extra);			
		}
		$html .= $this->get_html_iconos_utilerias();
		return $html;
	}
	
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}		
		if ($this->estado != '' && $this->confirmar_clave) {
			$test = trim($_POST[$this->id_form.'_test']);
			if ($this->estado != $test) {
				return 'No coinciden las claves';
			}
		}
		return true;
	}	
	
	function crear_objeto_js()
	{
		return "new ef_editable_clave({$this->parametros_js()})";
	}
}

//########################################################################################################
//########################################################################################################

/**
 * Elemento editable que permite ingresar fechas
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable_fecha ef_editable_fecha
 */
class toba_ef_editable_fecha extends toba_ef_editable
{
	static protected $rango_fechas_global;
	protected $rango_fechas;
	
	static function get_lista_parametros()
	{
		$param = toba_ef_editable::get_lista_parametros();
		array_borrar_valor($param, 'edit_unidad');
		array_borrar_valor($param, 'edit_placeholder');
		return $param;
	}	

	/**
	* Cambia el rango de fechas aceptado por todas las instancias del ef_fecha
	*/
	static function set_rango_valido_global($desde, $hasta)
	{
		self::$rango_fechas_global = array($desde, $hasta);
	}
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (! isset($parametros['edit_tamano'])) {
			$parametros['edit_tamano'] = "10";//Esto deberia depender del tipo de fecha
		}
		if (isset(self::$rango_fechas_global)) {
			$this->rango_fechas = self::$rango_fechas_global;
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function set_estado($estado="")
	{
		if($estado!="") {
			$this->estado = cambiar_fecha($estado,'-','/');
		} else {
			$this->estado = null;	
		}
	}
	
	function normalizar_parametro_cascada($parametro) 
	{ 
		if (isset($parametro)) { 
			return cambiar_fecha($parametro,'/','-'); 
		} 
	} 

	/**
	 * Valida que las fechas ingresadas estén dentro del rango de fechas
	 *
	 * @param string $desde aaaa-mm-dd
	 * @param string $hasta aaaa-mm-dd
	 */
	function set_rango_valido($desde, $hasta)
	{
		$this->rango_fechas = array($desde, $hasta);
	}

	function get_estado()
	{
		// En este punto se formatea la fecha
		if($this->tiene_estado()){
			return cambiar_fecha($this->estado,'/','-');
		}else{
			return null;
		}
	}	
	
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = "efs/fecha";
		return $consumo;
	}
	
	function get_input()
	{		
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';
		$html = "<span class='ef-fecha'>";
		$html .= toba_form::text($this->id_form,$this->estado, $this->es_solo_lectura(),$this->tamano,
								$this->tamano, $this->clase_css, $this->input_extra.$tab);
		if (! $this->es_solo_lectura()) {
			$html .= "<a id='link_". $this->id_form . "' ";
			$html .= " onclick='calendario.select(document.getElementById(\"{$this->id_form}\"),\"link_".$this->id_form."\",\"dd/MM/yyyy\");return false;' ";
			$html .= " href='#' name='link_". $this->id_form . "'>".toba_recurso::imagen_toba('calendario.gif',true,16,16,"Seleccione la fecha")."</a>\n";
		}
		$html .= $this->get_html_iconos_utilerias();
		$html .= "</span>\n";
		return $html;
	}
    
	/**
	 * Valida que sea una fecha válida con la funcion php checkdate
	 */
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
		if ($this->tiene_estado()) {
			if ($this->confirma_excepcion_validacion()) {
				return true;
			}			
			$fecha = explode('/',$this->estado); 
			if (count($fecha) != 3) {
				return "El campo no es una fecha valida (3).";
			}
			if ( ! is_numeric($fecha[0]) || !is_numeric($fecha[1]) || !is_numeric($fecha[2]) ) {
				return "El campo no es una fecha valida (2).";
			}
			if (! checkdate($fecha[1],$fecha[0],$fecha[2])) {
				return "El campo no es una fecha valida (1).";
			}
			if (isset($this->rango_fechas)) {
				//TODO: Falta validación en el servidor
			}
		}
		return true;
	}
	
	function parametros_js()
	{
		if (isset($this->rango_fechas)) {
			$desde = explode('-', $this->rango_fechas[0]);
			$hasta = explode('-', $this->rango_fechas[1]);
			$desde[1]--;
			$hasta[1]--;
			$rango = "[new Date('{$desde[0]}','{$desde[1]}','{$desde[2]}'), new Date('{$hasta[0]}','{$hasta[1]}','{$hasta[2]}')]";
		} else {
			$rango = 'null';
		}
		return parent::parametros_js().", $rango";
	}	   
   
	function crear_objeto_js()
	{
		return "new ef_editable_fecha({$this->parametros_js()})";
	}		   
	
	function get_descripcion_estado($tipo_salida)
	{
		$formato = new toba_formateo($tipo_salida);
		$estado = $this->get_estado();
		$desc = ($estado != '') ? $formato->formato_fecha($estado) : '';
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$desc</div>";
			break;
			case 'xml':
			case 'pdf':
				return $desc;
			case 'excel':
				return $formato->formato_fecha($estado);
			break;
		}
	}	
}

//########################################################################################################
//########################################################################################################
/**
 * Elemento editable que permite ingresar fechas con horario
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable_fecha_hora ef_editable_fecha_hora
 */
class toba_ef_editable_fecha_hora extends toba_ef_editable
{
	static protected $rango_fechas_global;
	protected $rango_fechas;

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (! isset($parametros['edit_tamano'])) {
			$parametros['edit_tamano'] = "10";//Esto deberia depender del tipo de fecha
		}
		if (isset(self::$rango_fechas_global)) {
			$this->rango_fechas = self::$rango_fechas_global;
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	static function get_lista_parametros()
	{
		$param = toba_ef_editable::get_lista_parametros();
		array_borrar_valor($param, 'edit_unidad');
		array_borrar_valor($param, 'edit_placeholder');		
		return $param;
	}

	/**
	* Cambia el rango de fechas aceptado por todas las instancias del ef_fecha_hora
	*/
	static function set_rango_valido_global($desde, $hasta)
	{
		self::$rango_fechas_global = array($desde, $hasta);
	}

	function set_estado($estado="")
	{
		toba::logger()->var_dump($estado);
		if(is_array($estado) && isset($estado['0']) && isset($estado['1'])) {
			$this->estado = array('fecha' => cambiar_fecha($estado['0'],'-','/') , 'hora' => $estado['1']);
		} else {
			$this->estado = null;
		}
	}

	function get_estado()
	{
		// En este punto se formatea la fecha
		if($this->tiene_estado()){
			return array(cambiar_fecha($this->estado['fecha'],'/','-') , $this->estado['hora']);
		}else{
			return null;
		}
	}

	function tiene_estado()
	{
		//Verifico que sea distinto de null y que ambas componenetes esten seteadas.
		$hay_fecha = $hay_hora = true;
		if (! is_null($this->estado)) {
			$hay_fecha = (isset($this->estado['fecha']) && trim($this->estado['fecha'] != ''));
			$hay_hora = (isset($this->estado['hora']) && trim($this->estado['hora'] != ''));
		}
		return ($hay_fecha && $hay_hora);
	}

	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form. '_fecha'])) {
			$this->estado = array( 'fecha' => trim($_POST[$this->id_form .'_fecha']), 'hora' => trim($_POST[$this->id_form . '_hora']));
		} else {
			$this->estado = null;
		}
	}

	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = "efs/fecha";
		return $consumo;
	}

	function get_input()
	{		
		$estado_fecha = (! is_null($this->estado)) ? $this->estado['fecha']: '';
		$estado_hora = (! is_null($this->estado))? $this->estado['hora'] : '';
		
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';
		$id_form_fecha = $this->id_form . '_fecha';
		$id_form_hora = $this->id_form . '_hora';
		$html = "<span class='ef-fecha-hora'>";
		$html .= toba_form::text($id_form_fecha ,$estado_fecha, $this->es_solo_lectura(),$this->tamano, $this->tamano, $this->clase_css, $this->input_extra.$tab);
		$visibilidad = "style= 'visibility:hidden;'";
		if (! $this->es_solo_lectura()) {	//Hay que ver si es solo lectura por la cascada o que?
			$visibilidad = "style= 'visibility:visible;'";
		}
		$html .= "<a id='link_". $this->id_form . "' ";
		$html .= " onclick='calendario.select(document.getElementById(\"$id_form_fecha\"),\"link_".$this->id_form."\",\"dd/MM/yyyy\");return false;' ";
		$html .= " href='#' name='link_". $this->id_form . "' $visibilidad>".toba_recurso::imagen_toba('calendario.gif',true,16,16,"Seleccione la fecha")."</a>\n";

		$html .= toba_form::text($id_form_hora, $estado_hora, $this->es_solo_lectura(), 5,  5, $this->clase_css . '  ef-numero ', $this->input_extra. $tab);
		$html .= $this->get_html_iconos_utilerias();
		$html .= "</span>\n";
		return $html;
	}

	/**
	* Valida que sea una fecha válida con la funcion php checkdate
	*/
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;
		}
		if ($this->tiene_estado()) {
			$fecha = explode('/',$this->estado['fecha']);
			if ($this->confirma_excepcion_validacion()) {
				return true;
			}			
			if (count($fecha) != 3) {
				return "El campo no es una fecha valida (3).";
			}
			if ( ! is_numeric($fecha[0]) || !is_numeric($fecha[1]) || !is_numeric($fecha[2]) ) {
				return "El campo no es una fecha valida (2).";
			}
			if (! checkdate($fecha[1],$fecha[0],$fecha[2])) {
				return "El campo no es una fecha valida (1).";
			}
			if (isset($this->rango_fechas)) {
				//TODO: Falta validación en el servidor
			}

			$hora = explode(':', $this->estado['hora']);
			if (! is_numeric($hora[0]) || ! is_numeric($hora[1])) {
				return "El campo no es una hora valida (4).";
			}

			if (! checktime($hora[0], $hora[1])) {
				return "El campo no es una hora valida (5).";
			}
		}
		return true;
	}

	function parametros_js()
	{
		if (isset($this->rango_fechas)) {
			$desde = explode('-', $this->rango_fechas[0]);
			$hasta = explode('-', $this->rango_fechas[1]);
			$desde[1]--;
			$hasta[1]--;
			$rango = "[new Date('{$desde[0]}','{$desde[1]}','{$desde[2]}'), new Date('{$hasta[0]}','{$hasta[1]}','{$hasta[2]}')]";
		} else {
			$rango = 'null';
		}
		return parent::parametros_js().", $rango";
	}

	function crear_objeto_js()
	{
		return "new ef_editable_fecha_hora({$this->parametros_js()})";
	}

	function get_descripcion_estado($tipo_salida)
	{
		$formato = new toba_formateo($tipo_salida);
		$estado = $this->get_estado();
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				$desc = (! is_null($estado)) ? $formato->formato_fecha($estado[0]) . " $estado[1] " : '';
				$desc = "<div class='{$this->clase_css}'>$desc</div>";
				break;				
			case 'excel':
				$desc = $formato->formato_fecha_hora("{$estado[0]} {$estado[1]}");
				break;
			case 'xml':
			case 'pdf':
				break;		//Retorna la descripcion actual
		}
		return $desc;
	}
}

//########################################################################################################
//########################################################################################################
/**
 * Elemento editable que permite ingresar fechas con horario
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_editable_fecha_hora ef_editable_fecha_hora
 */
class toba_ef_editable_hora extends toba_ef_editable
{
	protected $rango_horas;

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (! isset($parametros['edit_tamano'])) {
			$parametros['edit_tamano'] = "5";//Esto deberia depender del tipo de fecha
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	static function get_lista_parametros()
	{
		$param = toba_ef_editable::get_lista_parametros();
		array_borrar_valor($param, 'edit_unidad');
		array_borrar_valor($param, 'edit_placeholder');		
		return $param;
	}

	function cargar_estado_post()
	{
		$this->estado = null;		
		if (isset($_POST[$this->id_form])) {
			$this->estado = trim($_POST[$this->id_form]);			
		}
	}

	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = "efs/fecha";
		return $consumo;
	}

	function get_input()
	{
		$estado_hora = (! is_null($this->estado))? $this->estado : '';

		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';
		$html = "<span class='ef-fecha-hora'>";
		$visibilidad = "style= 'visibility:hidden;'";
		if (! $this->es_solo_lectura()) {	//Hay que ver si es solo lectura por la cascada o que?
			$visibilidad = "style= 'visibility:visible;'";
		}
		$html .= toba_form::text($this->id_form, $estado_hora, $this->es_solo_lectura(), 5,  5, $this->clase_css . '  ef-numero ', $this->input_extra. $tab);
		$html .= $this->get_html_iconos_utilerias();
		$html .= "</span>\n";
		return $html;
	}

	function set_estado($estado)
	{
		if(isset($estado)) {	
			$value = explode(':', trim($estado));		//Lo separo por los dos puntos
			$this->estado = $value[0] .':'. $value[1];	// Y tomo los dos primeros componentes hh:mm
		} else {
			$this->estado = null;	
		}
	}

	/**
	* Valida que sea una fecha válida con la funcion php checkdate
	*/
	function validar_estado()
	{
		$padre = parent::validar_estado();		
		if ($padre !== true) {
			return $padre;
		}		
		if ($this->tiene_estado()) {
			if ($this->confirma_excepcion_validacion()) {
				return true;
			}
			$hora = explode(':', $this->estado);
			if (! is_numeric($hora[0]) || ! is_numeric($hora[1])) {
				return "El campo no es una hora valida (1).";
			}

			if (! checktime($hora[0], $hora[1])) {
				return "El campo no es una hora valida (2).";
			}
		}
		return true;
	}

	function parametros_js()
	{
		if (isset($this->rango_horas)) {
			$desde = explode(':', $this->rango_horas[0]);
			$hasta = explode(':', $this->rango_horas[1]);
			$rango = "[new Date(0,0,0,'{$desde[0]}','{$desde[1]}'), new Date(0,0,0,'{$hasta[0]}','{$hasta[1]}')]";
		} else {
			$rango = 'null';
		}
		return parent::parametros_js().", $rango";
	}

	function crear_objeto_js()
	{
		return "new ef_editable_hora({$this->parametros_js()})";
	}

	function get_descripcion_estado($tipo_salida)
	{
		$formato = new toba_formateo($tipo_salida);
		$estado = $this->get_estado();
		$desc = (! is_null($estado)) ? $formato->formato_hora($estado) : '';
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$desc</div>";
				break;
			case 'xml':
			case 'pdf':
				return $desc;
			case 'excel':
				return $formato->formato_hora($estado);
				break;
		}
	}
}

//########################################################################################################
//########################################################################################################

/**
 * Elemento editable que permite ingresar textos largos, equivalene a un tag <textarea>
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_textarea ef_textarea
 */
class toba_ef_editable_textarea extends toba_ef_editable
{
	protected $lineas;
	protected $resaltar;
	protected $wrap;
	protected $clase='ef-textarea';
	protected $no_margen;
	protected $ajustable;
	protected $maximo;
	
	static function get_lista_parametros()
	{
		$param[] = 'edit_filas';
		$param[] = 'edit_columnas';
		$param[] = 'edit_wrap';
		$param[] = 'edit_maximo';
		$param[] = 'edit_resaltar';
		$param[] = 'edit_ajustable';
		$param[] = 'edit_placeholder';
		return $param;
	}		
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		//Esta conversion es para no modificar ahora las definiciones, CAMBIAR!
		$this->lineas = isset($parametros['edit_filas']) ? $parametros['edit_filas'] : 6;
		$this->wrap = isset($parametros['edit_wrap']) ? $parametros['edit_wrap'] : "";
		if (isset($parametros['edit_resaltar'])){
			if($parametros['edit_resaltar']==1){
				$this->resaltar = 1;
			}
		}else{
			$this->resaltar = 0;
		}
		$parametros['edit_tamano'] = isset($parametros["edit_columnas"]) ? $parametros["edit_columnas"] : 40;

		if(isset($parametros['edit_maximo']) && $parametros['edit_maximo']!="") {
			$maximo = $parametros['edit_maximo'];
			unset($parametros['edit_maximo']);
		}
		$this->ajustable = isset($parametros['edit_ajustable']) ? $parametros['edit_ajustable'] : false;
		unset($parametros['edit_filas']);
		unset($parametros['edit_columnas']);
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);

		if (isset($maximo)) {
			$this->maximo = $maximo;	
		} else {
			$this->maximo = null;	
		}
	}

	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		//Consumo la clase para hacer resize de los textarea
		if ($this->ajustable)
			$consumo[] = "efs/resizeTa";
		return $consumo;
	}

	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])) {
			$this->estado = trim($_POST[$this->id_form]);
			$this->estado = str_replace("\r\n", "\n", $this->estado);
		} else {
			$this->estado = null;
		}
	}

	function get_input()
	{	
		if (!isset($this->estado)) {
			$this->estado = '';	
		}
		$html = "";
		if($this->es_solo_lectura()){
			$clase = $this->clase.' ef-input-solo-lectura';
			$html .= toba_form::textarea( $this->id_form, $this->estado, $this->lineas, $this->tamano, $clase, $this->wrap, " readonly");
		}else{
			$this->input_extra .= $this->get_info_placeholder();
			if($this->resaltar){
				$javascript = " onclick='javascript: document.getElementById('{$this->id_form}').select()'";
				$html .= toba_form::button($this->id_form . "_res", "Seleccionar", $javascript );
			}
			if ($this->maximo) {
				$obj = $this->objeto_js();
				$this->javascript .= "onkeydown=\"$obj.validar()\" onkeyup=\"$obj.validar()\"";
			}
			$tab = ' tabindex="'.$this->padre->get_tab_index().'"';	
			$html .= toba_form::textarea( $this->id_form, $this->estado,$this->lineas,$this->tamano,$this->clase,$this->wrap,$this->javascript.' '.$this->input_extra.$tab);
		}
		$html .= $this->get_html_iconos_utilerias();
		return $html;
	}

	function set_estado($estado)
	{
		parent::set_estado($estado);
		if ($this->maximo) {
			if (strlen($this->estado) > $this->maximo) {
				$this->estado = substr($this->estado, 0, $this->maximo);
			}
		}
	}

	function crear_objeto_js()
	{
		return "new ef_textarea({$this->parametros_js()})";
	}			

	function parametros_js()
	{
		$maximo = isset($this->maximo) ? "'{$this->maximo}'" : 'null';
		$ajustable = ($this->ajustable) ? "true" : "false";
		return parent::parametros_js().", $maximo, $ajustable";	
	}
}
   
?>
