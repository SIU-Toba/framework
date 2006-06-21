<?php
require_once("nucleo/componentes/runtime/interface/efs/ef.php");// Elementos de interface

class ef_editable extends ef
{
	protected $tamano;
	protected $maximo;
	protected $estilo="ef-input";
	protected $mascara;

	static function get_parametros()
	{
		$parametros = parent::get_parametros_carga();
		unset($parametros['lista']);
		$parametros["tamano"]["descripcion"]="Cantidad de caracteres visibles.";
		$parametros["tamano"]["opcional"]=1;	
		$parametros["tamano"]["etiqueta"]="Tamaño Campo";
		$parametros["maximo"]["descripcion"]="Cantidad maxima de caracteres (Por defecto igual a [tamano]).";
		$parametros["maximo"]["opcional"]=1;	
		$parametros["maximo"]["etiqueta"]="Max. Caract.";
		$parametros["mascara"]["descripcion"]="Mascara del elemento.";
		$parametros["mascara"]["opcional"]=1;	
		$parametros["mascara"]["etiqueta"]="Máscara";
		$parametros["estado"]["descripcion"]="Indica un valor predeterminado para el campo";
		$parametros["estado"]["opcional"]=1;	
		$parametros["estado"]["etiqueta"]="Valor defecto";
		$parametros["solo_lectura"]["descripcion"]="Establece el elemento como solo lectura.";
		$parametros["solo_lectura"]["opcional"]=1;	
		$parametros["solo_lectura"]["etiqueta"]="Solo lectura";
		$parametros["estilo"]["descripcion"]="Clase css a aplicar al elemento";
		$parametros["estilo"]["opcional"]=1;	
		$parametros["estilo"]["etiqueta"]="Estilo CSS";		
		return $parametros;
	}

	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		//VAlor FIJO
		if(isset($parametros["estado"])){
			$this->estado_defecto = $parametros["estado"];
			$this->estado = $this->estado_defecto;
		}
		//Tamaño del editable
		$this->tamano = (isset($parametros["tamano"]))? $parametros["tamano"] : 20;
		//Maximo de caracteres
		if(isset($parametros["maximo"])){
			if($parametros["maximo"]!=""){
				$this->maximo = $parametros["maximo"];
			}else{
				$this->maximo = $this->tamano;
			}
		}else{
			$this->maximo = $this->tamano;
		}
		//Mascara
		if(isset($parametros["mascara"])) {
			$this->mascara = $parametros["mascara"];		
		}
        //Determino la ESTILO
        if((isset($parametros["estilo"]))&&(trim($parametros["estilo"])!="")){
            $this->estilo = $parametros["estilo"];
            unset($parametros["estilo"]);
    	}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
	
	function cargar_valores($valores)
	{
		if ($valores === null) {
			$this->solo_lectura = true;
		}
		if (!isset($this->estado)) {
			$this->estado = $valores;
		}
	}
	
	function set_estado($estado)
	{
   		if(isset($estado)){								
    		$this->estado=trim($estado);
			return true;
	    } else {
	    	$this->estado = null;	
	    }
	}
	
	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])) {
			$this->estado = trim($_POST[$this->id_form]);
    	} else {
    		$this->estado = null;
    	}
	}	

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		if (isset($this->estado)) {
			return ($this->estado != "");
		} else{
			return false;
		}
	}
    
	function get_input()
	{
		return form::text($this->id_form, $this->estado,$this->solo_lectura,$this->maximo,$this->tamano,$this->estilo, $this->javascript.' '.$this->input_extra);
	}

	function get_consumo_javascript()
	{
		$consumos = array('interface/mascaras', 'interface/ef', 'interface/ef_editable');
		return $consumos;
	}

	function parametros_js()
	{
		return parent::parametros_js().", '{$this->mascara}'";
	}	
	
	function crear_objeto_js()
	{
		return "new ef_editable({$this->parametros_js()})";
	}	
	
}
//########################################################################################################
//########################################################################################################

class ef_editable_numero extends ef_editable
{
	protected $rango_inferior = array('limite' => '*', 'incluido' => 1);
	protected $rango_superior = array('limite' => '*', 'incluido' => 1);
	protected $mensaje_defecto;

	static function get_parametros()
	{
		$parametros = ef_editable::get_parametros();
		unset($parametros['tamano']);
		unset($parametros['maximo']);
		$mas = '<br>Ver [wiki:Referencia/efs/numero documentación de los parametros]';
		$parametros["maximo"]["descripcion"]="Pone un limite en la cantidad de caracteres que es posible ingresar al editbox incluyendo simbolos de puntuación, comas, decimales, etc. $mas";
		$parametros["maximo"]["etiqueta"]="Cant. Max. Caract.";
		$parametros["maximo"]["opcional"]=1;
		$parametros["cifras"]["descripcion"]="Determina la cantidad de caracteres que son visibles sin scrollear. ".$mas;
		$parametros["cifras"]["opcional"]=1;	
		$parametros["cifras"]["etiqueta"]="Tamaño visual";
		$parametros["rango"]["descripcion"]="Intervalo de números permitidos. Los corchetes incluyen el límite, los paréntesis no, por defecto [0..*]. ".$mas;
		$parametros["rango"]["opcional"]=1;	
		$parametros["rango"]["etiqueta"]="Rango de valores permitidos";
		$parametros["mascara"]["descripcion"]="Máscara aplicada al número, por ejemplo ###.###,00".$mas;
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->estilo = "ef-input-numero";
        $parametros["tamano"] = (isset($parametros["cifras"])) ? $parametros["cifras"] : 5;
        unset($parametros["cifras"]);
		if (isset($parametros['rango'])) {
			$this->cambiar_rango($parametros['rango']);
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

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
	}
	
	function mensaje_validacion_rango()
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
	
	function validar_rango()
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

    function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
        if ($this->activado()) {
			if (! is_numeric($this->estado)) {
				return "El campo es numérico";
			}
			return $this->validar_rango();
		}
		return true;
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

class ef_editable_moneda extends ef_editable_numero
{
	protected $rango_inferior = array('limite' => '0', 'incluido' => 1);
	protected $mensaje_defecto = ' debe ser un importe positivo.';
	
	static function get_parametros()
	{
		$parametros = ef_editable_numero::get_parametros();
		return $parametros;
	}

	function crear_objeto_js()
	{
		return "new ef_editable_moneda({$this->parametros_js()})";
	}	
}

//########################################################################################################
//########################################################################################################


class ef_editable_numero_porcentaje extends ef_editable_numero
{
	protected $rango_inferior = array('limite' => '0', 'incluido' => 1);
	protected $rango_superior = array('limite' => '100', 'incluido' => 1);
	protected $mensaje_defecto = ' debe estar entre 0% y 100%.';
	
	static function get_parametros()
	{
		$parametros = ef_editable_numero::get_parametros();
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (! isset($parametros["cifras"]))
			$parametros["cifras"]= 4;
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function get_input()
	{
		return parent::get_input()." %";
	}	
	
	function crear_objeto_js()
	{
		return "new ef_editable_porcentaje({$this->parametros_js()})";
	}	
}

//########################################################################################################
//########################################################################################################

class ef_editable_clave extends ef_editable
{
	static function get_parametros()
	{
		$parametros["tamano"]["descripcion"]="Cantidad de caracteres.";
		$parametros["tamano"]["opcional"]=1;	
		$parametros["tamano"]["etiqueta"]="Tamaño Campo";
		$parametros["maximo"]["descripcion"]="Cantidad maxima de caracteres (Por defecto igual a [tamano]).";
		$parametros["maximo"]["opcional"]=1;	
		$parametros["maximo"]["etiqueta"]="Max. Caract.";
		return $parametros;
	}

	function get_input()
	{
		$estado = isset($this->estado)? $this->estado : "";
		$html = form::password($this->id_form,$estado)."<br>";
		$html .= form::password($this->id_form ."_test",$estado);
		return $html;
	}
	
	function crear_objeto_js()
	{
		return "new ef_editable_clave({$this->parametros_js()})";
	}
}
//########################################################################################################
//########################################################################################################

class ef_editable_fecha extends ef_editable
//Campo que maneja fechas
{
	static function get_parametros()
	{
		$parametros = ef_editable::get_parametros();
		unset($parametros['tamano']);
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$parametros['tamano'] = "10";//Esto deberia depender del tipo de fecha
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function cambiar_fecha($fecha,$sep_actual,$sep_nuevo){
		$f = explode($sep_actual,$fecha);
		$dia = str_pad($f[0],2,0,STR_PAD_LEFT);
		$mes = str_pad($f[1],2,0,STR_PAD_LEFT);
		return $f[2] . $sep_nuevo . $mes . $sep_nuevo .$dia;
 	}

	function set_estado($estado="")
	{
  		if($estado!="") {
    		$this->estado = $this->cambiar_fecha($estado,'-','/');
	    } else {
	    	$this->estado = null;	
	    }
	}

	function get_estado()
	{
		// En este punto se formatea la fecha
		if($this->activado()){
			return $this->cambiar_fecha($this->estado,'/','-');
		}else{
			return null;
		}
	}	
	
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = "fecha";
		return $consumo;
	}
	
	function get_input()
	{
		$html = "<span class='ef-fecha'>";
		$html .= form::text($this->id_form,$this->estado,$this->solo_lectura,$this->tamano,$this->tamano, $this->estilo);
		if (! $this->solo_lectura) {
			$html .= "<a id='link_". $this->id_form . "' ";
			$html .= " onclick='calendario.select(document.getElementById(\"{$this->id_form}\"),\"link_".$this->id_form."\",\"dd/MM/yyyy\");return false;' ";
			$html .= " href='#' name='link_". $this->id_form . "'>".recurso::imagen_apl('cal.gif',true,16,16,"Seleccione la fecha")."</a>\n";
		}
		$html .= "</span>\n";
		return $html;
	}
    
    function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
		if ($this->activado()) {
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
		}
		return true;
   }
   
	function crear_objeto_js()
	{
		return "new ef_editable_fecha({$this->parametros_js()})";
	}		   
}
//########################################################################################################
//########################################################################################################

class ef_editable_multilinea extends ef_editable
{
	protected $lineas;
	protected $resaltar;
	protected $wrap;
	protected $clase;
	protected $no_margen;
	protected $ajustable;
	protected $maximo;
	
	static function get_parametros()
	{
		$parametros["filas"]["descripcion"]="Cantidad de lineas";
		$parametros["filas"]["opcional"]=1;	
		$parametros["filas"]["etiqueta"]="Alto";	
		$parametros["columnas"]["descripcion"]="Cantidad de carcteres por linea";
		$parametros["columnas"]["opcional"]=1;	
		$parametros["columnas"]["etiqueta"]="Ancho";
		$parametros["maximo"]["descripcion"]="Cantidad máxima de caracteres.";
		$parametros["maximo"]["opcional"]=1;	
		$parametros["maximo"]["etiqueta"]="Max. Caract.";
		$parametros["wrap"]["descripcion"]="";
		$parametros["wrap"]["opcional"]=1;	
		$parametros["wrap"]["etiqueta"]="Wrap";	
		$parametros["resaltar"]["descripcion"]="Se incorpora un boton para resaltar el texto";
		$parametros["resaltar"]["opcional"]=1;	
		$parametros["resaltar"]["etiqueta"]="Seleccionable";	
		$parametros["ajustable"]["descripcion"]="El tamaño gráfico es ajustable";
		$parametros["ajustable"]["opcional"]=1;	
		$parametros["ajustable"]["etiqueta"]="Ajustable";	
		$parametros["sql"]["descripcion"]="Cargar el valor en base a una sentencia SQL.";
		$parametros["sql"]["opcional"]=1;	
		$parametros["sql"]["etiqueta"]="Carga SQL: select";
		$parametros["fuente"]["descripcion"]="(Util solo si existe [sql]) Fuente a utilizar para ejecutar el SQL.";
		$parametros["fuente"]["opcional"]=1;	
		$parametros["fuente"]["etiqueta"]="Carga SQL: fuente";
		$parametros["dao"]["descripcion"]="Cargar el valor de un metodo.";
		$parametros["dao"]["opcional"]=1;	
		$parametros["dao"]["etiqueta"]="Carga Dao: metodo";
		$parametros["clase"]["descripcion"]="(Util solo si existe [dao]) Nombre de la clase que posee el metodo.";
		$parametros["clase"]["opcional"]=1;	
		$parametros["clase"]["etiqueta"]="Carga Dao: clase";
		$parametros["include"]["descripcion"]="(Util solo si existe [dao]) Archivo que posee la definicion de la clase.";
		$parametros["include"]["opcional"]=1;	
		$parametros["include"]["etiqueta"]="Carga Dao: include";
		$parametros["estado"]["descripcion"]="Indica un valor predeterminado para el campo";
		$parametros["estado"]["opcional"]=1;	
		$parametros["estado"]["etiqueta"]="Valor defecto";
		$parametros["solo_lectura"]["descripcion"]="Establece el elemento como solo lectura.";
		$parametros["solo_lectura"]["opcional"]=1;	
		$parametros["solo_lectura"]["etiqueta"]="Solo lectura";
		$parametros["no_margen"]["descripcion"]="Indica que no se utilice etiqueta";
		$parametros["no_margen"]["opcional"]=1;	
		$parametros["no_margen"]["etiqueta"]="No margen";	
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		//Esta conversion es para no modificar ahora las definiciones, CAMBIAR!
		$this->no_margen = isset($parametros["no_margen"]) ? $parametros["no_margen"] : 0;
		$this->lineas = isset($parametros["filas"]) ? $parametros["filas"] : 6;
		$this->wrap = isset($parametros["wrap"]) ? $parametros["wrap"] : "";
		$this->clase = isset($parametros["clase"]) ? $parametros["clase"] : "ef-textarea";
		if(isset($parametros["resaltar"])){
			if($parametros["resaltar"]==1){
				$this->resaltar = 1;
			}
		}else{
			$this->resaltar = 0;
		}
		$parametros["tamano"] = isset($parametros["columnas"]) ? $parametros["columnas"] : 40;
		
		if(isset($parametros["maximo"]) && $parametros["maximo"]!="") {
			$maximo = $parametros["maximo"];
			unset($parametros['maximo']);
		}
		$this->ajustable = isset($parametros["ajustable"]) ? $parametros["ajustable"] : false;
		unset($parametros["filas"]);
		unset($parametros["columnas"]);
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
			$consumo[] = "interface/resizeTa";
		return $consumo;
	}

	function get_input()
	{	
		if (!isset($this->estado)) {
			$this->estado = '';	
		}
		$html = "";
		if($this->solo_lectura){
			$html .= form::textarea( $this->id_form, $this->estado, $this->lineas, $this->tamano, $this->clase, $this->wrap, " readonly");
		}else{
			if($this->resaltar){
				$javascript = " onclick='javascript: document.getElementById('{$this->id_form}').select()'";
				$html .= form::button($this->id_form . "_res", "Seleccionar", $javascript );
			}
			if ($this->maximo) {
				$obj = $this->objeto_js();
				$this->javascript .= "onkeydown=\"$obj.validar()\" onkeyup=\"$obj.validar()\"";
			}
			$html .= form::textarea( $this->id_form, $this->estado,$this->lineas,$this->tamano,$this->clase,$this->wrap,$this->javascript);
		}
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
		return "new ef_editable_multilinea({$this->parametros_js()})";
	}			
	
	function parametros_js()
	{
		$maximo = isset($this->maximo) ? "'{$this->maximo}'" : 'null';
		$ajustable = ($this->ajustable) ? "true" : "false";
		return parent::parametros_js().", $maximo, $ajustable";	
	}
}
//########################################################################################################
//########################################################################################################
         
?>
