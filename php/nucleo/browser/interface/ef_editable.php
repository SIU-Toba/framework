<?php
require_once("nucleo/browser/interface/ef.php");// Elementos de interface
/*
* 			ef <abstracta>
* 			|
* 			+----> ef_editable
* 					|
* 					+----> ef_editable_numero
* 					|	|
* 					|	+----> ef_editable_numero_porcentaje
*					|
* 					+----> ef_editable_clave
*					|
* 					+----> ef_editable_fecha (faltan validaciones en el cliente y el servidor)
*					| 
* 					+----> ef_editable_multilinea
* 
*/
class ef_editable extends ef
// Editbox de texto
//PARAMETROS ADICIONALES:
//"tamano": Cantidad de caracteres
{
	var $tamano;
	var $maximo;
	var $estado;
    var $fuente;
	var $estilo="ef-input";
	var $mascara;
	private $requiere_instancia = false;	

	static function get_parametros()
	{
		$parametros["tamano"]["descripcion"]="Cantidad de caracteres.";
		$parametros["tamano"]["opcional"]=1;	
		$parametros["tamano"]["etiqueta"]="Tamaño Campo";
		$parametros["maximo"]["descripcion"]="Cantidad maxima de caracteres (Por defecto igual a [tamano]).";
		$parametros["maximo"]["opcional"]=1;	
		$parametros["maximo"]["etiqueta"]="Max. Caract.";
		$parametros["mascara"]["descripcion"]="Mascara del elemento.";
		$parametros["mascara"]["opcional"]=1;	
		$parametros["mascara"]["etiqueta"]="Máscara";
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
		$parametros["dependencias"]["descripcion"]="El estado dependende de otro EF (CASCADA). Lista de EFs separada por comas";
		$parametros["dependencias"]["opcional"]=1;	
		$parametros["dependencias"]["etiqueta"]="Dependencias";
		$parametros["estilo"]["descripcion"]="Clase css a aplicar al elemento";
		$parametros["estilo"]["opcional"]=1;	
		$parametros["estilo"]["etiqueta"]="Estilo CSS";		
		return $parametros;
	}

	function ef_editable($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		//Solo Lectura
		if((isset($parametros["solo_lectura"]))&&($parametros["solo_lectura"]==1)){
			$this->solo_lectura = true;
		}else{
			$this->solo_lectura = false;
		}
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
        //Determino la FUENTE
        if((isset($parametros["fuente"]))&&(trim($parametros["fuente"])!="")){
            $this->fuente = $parametros["fuente"];
            unset($parametros["fuente"]);
    	}else{
    	    $this->fuente = "instancia"; //La instancia por defecto es la CENTRAL
        }
        //Determino la ESTILO
        if((isset($parametros["estilo"]))&&(trim($parametros["estilo"])!="")){
            $this->estilo = $parametros["estilo"];
            unset($parametros["estilo"]);
    	}
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);

		//Carga a partir de SQL
		if(isset($parametros["sql"])){
			if($parametros["sql"]!=""){
				$this->sql = stripslashes($parametros["sql"]);
				if(is_array($this->dependencias)){
					$this->valores = array();
				}else{
					$this->cargar_datos_db();
				}
			}
		}
		
		//Carga a partir de DAO
		if(isset($parametros["dao"])){
			$this->dao = $parametros["dao"];
		}
		if(isset($parametros["include"])){
			$this->include = $parametros["include"];
		}
		if(isset($parametros["clase"])){
			$this->clase = $parametros["clase"];
		}
		if(isset($parametros["instanciable"])){
			if( $parametros["instanciable"] == "1" ){
				$this->requiere_instancia = true;
			}
		}
		if(isset($this->include) && isset($this->clase) )
		{
			$this->modo = "estatico";
		}else{
			$this->modo = "cn";	
		}
		unset($parametros["dao"]);
		unset($parametros["clase"]);
		unset($parametros["include"]);		
		//Si el elemento no posee dependencias lo puedo cargar ahora...
		//Sino hay que esperar que la carga se llame explicitamente una vez que el padre se encuentre cargado
		//El modo estatico puede funcionar con cascadas
		if($this->modo == "estatico"){
			if(is_array($this->dependencias)){
				//$this->establecer_solo_lectura();
				//$this->valores = array();
				$this->input_extra = " readonly ";
			}else{
				$this->cargar_datos_dao();
			}
		}		
	}

	function obtener_dao()
	/*
		De esta manera, el COMBO avisa que necesita informacion del CN
		indicandole cual es el metodo del mismo que hay que ejecutar para recibir la informacion
		Esto se desactiva si el dao depende de una clase estatica.
	*/
	{
		if( $this->modo == "estatico" ) {
			return null;
		}else{
			return $this->dao;		
		}
	}
	
	function recuperar_datos_dao($param=null)
	//ATENCION: los parametros son codigo PHP a evaluar, no son un array...
	{
		include_once($this->include);
		if($this->requiere_instancia){
			$sentencia = "\$c = new {$this->clase}();
							\$valor = \$c->{$this->dao}($param);";
		}else{
			$sentencia = "\$valor = " .  $this->clase . "::" . $this->dao ."($param);";
		}
		eval($sentencia);//echo $sentencia;
		return $valor;
	}	
	
	function cargar_datos_dao()
	/*
		Si el DAO esta a a cargo del CN, el CN lo carga a travez de este metodo.
		Si el DAO se carga a travez de una clase estatica, el mismo obtiene los
		datos directamente de la misma, obviando los parametros
		Esto hay que pensarlo un poco mejor
	*/
	{
		if($this->modo =="estatico" ){
			$valor = $this->recuperar_datos_dao();
		}
		$this->estado = $valor;
	}		
	
	function cargar_datos_db()
	{
		$rs = toba::get_db($this->fuente)->consultar($this->sql, apex_db_numerico);
		if(empty($rs)){
			echo ei_mensaje("EF etiquetado '" . $this->etiqueta . "'<br> No se obtuvieron registros: ". $this->sql);
		}
		$this->estado = $rs[0][0];
	}

	function cargar_datos_master_ok()
	//Si el master esta cargado, el EF procede a cargar sus registros
	{
		if(isset($this->sql)){
			//1) Reescribo el SQL con los datos de las dependencias	
			foreach($this->dependencias_datos as $dep => $valor){
				$this->sql = ereg_replace(apex_ef_dependenca.$dep.apex_ef_dependenca,$valor,$this->sql);
			}
			//echo $this->id . " - " . $this->sql;
			//2) Regenero la consulta a la base
			$this->cargar_datos_db();
		}
		if (isset($this->dao)) {
			$parametros = array();
			for($a=0;$a<count($this->dependencias);$a++){
				$parametros[] = "'" . $this->dependencias_datos[$this->dependencias[$a]] . "'";
			}
			$param = implode(", ", $parametros);
			if($this->modo =="estatico" )
			{
				$valor = $this->recuperar_datos_dao($param);
				$this->cargar_estado($valor);
			}else{
				echo ei_mensaje("Las cascadas de DAO no estan preparadas para metodos no estaticos");
			}		
		}
	}	
	
	function cargar_estado($estado=null)
	//Carga el estado interno
	{
   		if(isset($estado)){								
    		$this->estado=trim($estado);
			return true;
	    }elseif(isset($_POST[$this->id_form])){
	    	
			if( get_magic_quotes_gpc() ){
				$this->estado = stripslashes(trim($_POST[$this->id_form]));
			}else{
   				$this->estado = trim($_POST[$this->id_form]);
			}
			return true;
    	}
		return false;
	}

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		if(isset($this->estado)){
			return ( !(($this->estado == "") OR ($this->estado=="NULL")) );
		}else{
			return false;
		}
	}
    
    function validar_estado()
    //Controla que el campo no este vacio
    {
        if($this->obligatorio){
            if( $this->activado() ){
				$this->validacion = true;
                return array(true,"");
            }else{
				$this->validacion = false;
                return array(false,"El campo es obligatorio!");
            }
        }else{
			$this->validacion = true;
			return array(true,"");
		}
    }

	function establecer_solo_lectura()
	{
		$this->solo_lectura = true;
	}

	function obtener_input()
	{
		if(!isset($this->estado) || $this->estado=="NULL") $this->estado="";
		$html = form::text($this->id_form, $this->estado,$this->solo_lectura,$this->maximo,$this->tamano,$this->estilo, $this->javascript);
		$html .= $this->obtener_javascript_general() . "\n\n";
		return $html;
	}

	function obtener_consumo_javascript()
	{
		$consumos = array('interface/mascaras', 'interface/ef', 'interface/ef_editable');
		return $consumos;
	}

	function obtener_javascript()
    {
        //Si el campo es obligatorio, en el form hay que llenarlo si o si
        if($this->obligatorio){
            return "
if( ereg_nulo.test(formulario.". $this->id_form .".value) ){
	alert(\"El campo '". $this->etiqueta ."' es obligatorio.\");
	formulario.". $this->id_form .".focus();
    return false;
}";
        }
    }

	function parametros_js()
	{
		return parent::parametros_js().", '{$this->mascara}'";
	}	
	
	function crear_objeto_js()
	{
		return "new ef_editable({$this->parametros_js()})";
	}	
	
	//-----------------------------------------------
	//-------------- DEPENDENCIAS -------------------
	//-----------------------------------------------
	
	function obtener_valores()
	{
		$estado = $this->obtener_estado();
		return ($estado !== 'NULL') ? $estado : null;
	}
	
	function javascript_slave_recargar_datos()
	{
		$solo_lect = "";
		if (! $this->solo_lectura) {
			$solo_lect = "s_.readOnly = false;";
		}
		return "
		function recargar_slave_{$this->id_form}(respuesta)
		{
			var dato = eval('(' + respuesta.responseText + ')');
			s_ = document.{$this->nombre_formulario}.{$this->id_form};
			s_.value = dato;
			$solo_lect
			s_.focus();
			atender_proxima_consulta();
		}
		";	
	}
	//-----------------------------------------------

	function javascript_slave_reset()
	{		
		$js = "
		function reset_{$this->id_form}()
		{
			s_ = document.{$this->nombre_formulario}.{$this->id_form};
			s_.readOnly = true;
			s_.value = '';\n";
			
		//Reseteo las dependencias	
		if(isset($this->dependientes)){
			foreach($this->dependientes as $dependiente){
				$js .= " reset_{$dependiente}();\n";
			}
		}
		$js .= "}\n";
		//Hay que resetear a los DEPENDIENTES
		return $js;
	}
	
	function javascript_master_get_estado()
	{
		return "
		function master_get_estado_{$this->id_form}()
		{
			s_ = document.{$this->nombre_formulario}.{$this->id_form};
			return(s_.value);
		}
		";		
	}
	
	function javascript_master_cargado()
	{
		return "
		function master_cargado_{$this->id_form}()
		{
			return (trim(master_get_estado_{$this->id_form}()) != '');
		}
		";		
	}	
		
}
//########################################################################################################
//########################################################################################################

class ef_editable_numero extends ef_editable
// Solo acepta numeros... NO TERMINADO!
//PARAMETROS ADICIONALES:
//"cifras": Cantidad de caracteres
//"rango: Intervalo de números permitidos (ej [0..100))": 
{
	protected $rango_inferior = array('limite' => '*', 'incluido' => 1);
	protected $rango_superior = array('limite' => '*', 'incluido' => 1);
	protected $mensaje_defecto;

	static function get_parametros()
	{
		$parametros = ef_editable::get_parametros();
		unset($parametros['tamano']);
		$parametros["cifras"]["descripcion"]="Cantidad maxima de caracteres.";
		$parametros["cifras"]["opcional"]=1;	
		$parametros["cifras"]["etiqueta"]="Cantidad de cifras";
		$parametros["rango"]["descripcion"]="Intervalo de números permitidos. Los corchetes incluyen el límite, los paréntesis no, por defecto [0..*]";
		$parametros["rango"]["opcional"]=1;	
		$parametros["rango"]["etiqueta"]="Rango de valores";
		$parametros["mascara"]["descripcion"]="Máscara aplicada al número, por ejemplo ###.###,00";
		return $parametros;
	}

	function ef_editable_numero($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->estilo = "ef-input-numero";
        $parametros["tamano"] = (isset($parametros["cifras"])) ? $parametros["cifras"] : 5;
        unset($parametros["cifras"]);
		if (isset($parametros['rango'])) {
			$this->cambiar_rango($parametros['rango']);
		}
		parent::ef_editable($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

    function obtener_javascript()
	//Validacion en el cliente. El campo es numerico? 
    {
        //Valido que los campos solo sean numeros
		$javascript = parent::obtener_javascript();//					--> DESACTIVADO!
		$javascript .= "
if( !(ereg_numero.test(formulario.". $this->id_form .".value)) ){
	alert(\"ATENCION: El campo '". $this->etiqueta ."' es numerico.\");
	//formulario.". $this->id_form .".value = '';
	formulario.". $this->id_form .".focus();
    return false;
}";
		//Le concateno lo generado en el padre (control obligatorio)
		return $javascript;
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
		if (isset($partes[1]))
			$this->mensaje_defecto = $partes[1];	
	}
	
	function mensaje_validacion_rango()
	{
		if (isset($this->mensaje_defecto))
			return $this->mensaje_defecto;
		$inferior = "";
		$superior = "";
		if ($this->rango_inferior['limite'] != '*')
			$inferior .= (($this->rango_inferior['incluido']) ? " mayor o igual a " : " mayor a ").$this->rango_inferior['limite'];
		if ($this->rango_superior['limite'] != '*')
			$superior .= (($this->rango_superior['incluido']) ? " menor o igual a " : " menor a ").$this->rango_superior['limite'];
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
		if ($ok || trim($this->estado)=="") {
			$this->validacion = true;
			return array(true, '');
		} else {
			$this->validacion = false;
			return array(false, $this->mensaje_validacion_rango());
		}
	}

    function validar_estado()
	//Validacion en el servidor. El campo es numerico?
	{
		$val_padre = parent::validar_estado();//Obligatorio nulo?
        if ($val_padre[0] &&  isset($this->estado)) {
			if((is_numeric($this->estado))||(trim($this->estado)=="")) {//Numerico?
				return $this->validar_rango();
			} else {
				$this->validacion = false;
                return array(false,"El campo es numerico.");
			}
		} else {
			return $val_padre;
		}
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

	function ef_editable_numero_porcentaje($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (! isset($parametros["cifras"]))
			$parametros["cifras"]= 4;
		parent::ef_editable_numero($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

    function obtener_javascript()
	//Validacion en el cliente. El campo esta en el rango correcto?
    {
        //Valido que los campos esten en el campo correcto
		$javascript = parent::obtener_javascript();
		$javascript .= "
if( !( (formulario.". $this->id_form .".value >= 0) &&  (formulario.". $this->id_form .".value <= 100) ) ){
	alert(\"ATENCION: El campo '". $this->etiqueta ."' posee un porcentaje fuera de rango.\");
	//formulario.". $this->id_form .".value = '';
	formulario.". $this->id_form .".focus();
    return false;
}";
		//Validacion del padre (numero)
		return $javascript;
    }
	
	function obtener_input()
	{
		return "<table class='tabla-0'><tr><td>".parent::obtener_input()."</td><td>%</td></tr></table>";
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

	function ef_editable_clave($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

    function obtener_javascript()
	//Validacion en el cliente. El campo es numerico? 
    {
        //Valido que los campos solo sean numeros
		$javascript = "
if( formulario.". $this->id_form .".value != formulario.". $this->id_form ."_test.value){
	alert(\"ATENCION: Las contraseñas no coinciden\");
	formulario.". $this->id_form .".value = '';
	formulario.". $this->id_form ."_test.value = '';
	formulario.". $this->id_form .".focus();
    return false;
}";
		//Le concateno lo generado en el padre (control obligatorio)
		$javascript .= parent::obtener_javascript();//					--> DESACTIVADO!
		return $javascript;
    }

	function obtener_input()
	{
		$estado = isset($this->estado)? $this->estado : "";
		$html = "<table class='tabla-0' width='100%'>\n";
		$html .= "<tr><td >\n";
		$html .= form::password($this->id_form,$estado);
		$html .= "</td></tr>\n";
		$html .= "<tr><td >\n";
		$html .= form::password($this->id_form ."_test",$estado);
		$html .= "</td></tr>\n";
		$html .= "</table>\n";
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

	function ef_editable_fecha($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$parametros['tamano'] = "10";//Esto deberia depender del tipo de fecha
		parent::ef_editable($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function cambiar_fecha($fecha,$sep_actual,$sep_nuevo){
		$f = explode($sep_actual,$fecha);
		$dia = str_pad($f[0],2,0,STR_PAD_LEFT);
		$mes = str_pad($f[1],2,0,STR_PAD_LEFT);
		return $f[2] . $sep_nuevo . $mes . $sep_nuevo .$dia;
 	}

	function cargar_estado($estado="")
	//Carga el estado interno
	{
//		$this->estado = "NULL";
  		if($estado!="" && $estado != "NULL"){									
    		$this->estado=$this->cambiar_fecha($estado,'-','/');
			return true;
	    }elseif(isset($_POST[$this->id_form])){
				if(get_magic_quotes_gpc()){
					$this->estado = stripslashes(trim($_POST[$this->id_form]));
				}else{
	   			$this->estado = trim($_POST[$this->id_form]);
				}
			return true;
    	}
		return false;
	}

	function obtener_estado()
	//Devuelve el estado interno
	{
		// En este punto se formatea la fecha
		if($this->activado()){
			return $this->cambiar_fecha($this->estado,'/','-');
		}else{
			return 'NULL';
		}
	}	

	function obtener_consumo_javascript()
	{
		$consumo = parent::obtener_consumo_javascript();
		$consumo[] = "fecha";
		return $consumo;
	}
	
	function obtener_input()
	{
		if(!isset($this->estado) || $this->estado=="NULL") $this->estado="";  
		$html = "<table class='tabla-0'>";
		$html .= "<tr><td>\n";
		$html .= form::text($this->id_form,$this->estado,$this->solo_lectura,$this->tamano,$this->tamano, $this->estilo);
		$html .= "</td>\n";
		if (! $this->solo_lectura) {
			$html .= "<td><a id='link_". $this->id_form . "' ";
			$html .= " onclick='calendario.select(document.".$this->nombre_formulario.".".$this->id_form.",\"link_".$this->id_form."\",\"dd/MM/yyyy\");return false;' ";
			$html .= " href='#' name='link_". $this->id_form . "'>".recurso::imagen_apl('cal.gif',true,16,16,"Seleccione la fecha")."</A> ";
			$html .= "</td>\n";
		}
		$html .= "</tr></table>\n";
		return $html;
	}

    function obtener_javascript()
	//Validacion en el cliente. El campo es fecha valida? 
    {
        $javascript = "          
        if (! validar_fecha(formulario.". $this->id_form .".value))
        {
        	formulario.". $this->id_form .".focus();
            return false;
        }";
        
		//Le concateno lo generado en el padre (control obligatorio)
		$javascript .= parent::obtener_javascript();//					--> DESACTIVADO!
		return $javascript;
    }
    
    function validar_estado()
	//Validacion en el servidor. El campo es fecha?
	{
		$val_padre = parent::validar_estado();//Obligatorio nulo?
        if($val_padre[0]){
			if(isset($this->estado) and (trim($this->estado) != "")  and (trim($this->estado) != "NULL")){
	            $fecha = explode('/',$this->estado); 
	            if (count($fecha) == 3){
					if( is_numeric($fecha[0]) && is_numeric($fecha[1]) && is_numeric($fecha[2]) )
					{
						if(checkdate($fecha[1],$fecha[0],$fecha[2])){
							$this->validacion = true;
							return array(true,"");
						}else{
							$this->validacion = false;
			                return array(false,"El campo no es una fecha valida (1).");
						}
					}else{
						$this->validacion = false;
		                return array(false,"El campo no es una fecha valida (2).");
					}
				}else{
					$this->validacion = false;
	                return array(false,"El campo no es una fecha valida (3).");
				}
			}else{
				return $val_padre;
			}
		}else{
			return $val_padre;
		}
   }
   
	function crear_objeto_js()
	{
		return "new ef_editable_fecha({$this->parametros_js()})";
	}		   
}
//########################################################################################################
//########################################################################################################

class ef_editable_multilinea extends ef_editable
// Editbox de texto
//PARAMETROS ADICIONALES:
//"filas"		--> lineas
//"columnas" 	--> tamano
{
	var $lineas;
	var $resaltar;
	var $wrap;
	var $clase;
	var $no_margen;
	var $ajustable;
	
	static function get_parametros()
	{
/*
		ATENCIOooooooN! colapsan la clase PHP y la clase CSS
	
		$parametros["css"]["descripcion"]="Clase CSS a aplicar sobre el EF.";
		$parametros["css"]["opcional"]=1;	
*/
		$parametros["filas"]["descripcion"]="Cantidad de lineas";
		$parametros["filas"]["opcional"]=1;	
		$parametros["filas"]["etiqueta"]="Lineas";	
		$parametros["columnas"]["descripcion"]="Cantidad de carcteres por linea";
		$parametros["columnas"]["opcional"]=1;	
		$parametros["columnas"]["etiqueta"]="Largo";	
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

	function ef_editable_multilinea($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
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
		$this->ajustable = isset($parametros["ajustable"]) ? $parametros["ajustable"] : false;
		unset($parametros["filas"]);
		unset($parametros["columnas"]);
		parent::ef_editable($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
	//---------------------------------------------------------

	function obtener_interface_ut()
	{
		if($this->no_margen=="1"){
			echo $this->obtener_input();
		}else{
			parent::obtener_interface_ut();
		}
	}
	//---------------------------------------------------------

	function obtener_consumo_javascript()
	{
		$consumo = parent::obtener_consumo_javascript();
		//Consumo la clase para hacer resize de los textarea
		if ($this->ajustable)
			$consumo[] = "interface/resizeTa";
		return $consumo;
	}	
	//---------------------------------------------------------

	function obtener_input()
	{	
		if(!isset($this->estado) || $this->estado=="NULL") $this->estado="";
		$html = "";
		if($this->solo_lectura){
			$html .= form::textarea( $this->id_form, $this->estado, $this->lineas, $this->tamano, $this->clase, $this->wrap, " readonly");
		}else{
			if($this->resaltar){
				$javascript = " onclick='javascript: document.".$this->nombre_formulario . "." . $this->id_form.".select()'";
				$html .= form::button($this->id_form . "_res", "Seleccionar", $javascript );
			}
			$html .= form::textarea( $this->id_form, $this->estado,$this->lineas,$this->tamano,$this->clase,$this->wrap,$this->javascript );
			if ($this->ajustable) {
				$html .= js::abrir();
				$html .= "resizeTa.agregar_elemento(document.getElementById('{$this->id_form}'));";
				$html .= js::cerrar();
			}
		}
		return $html;
	}
}
//########################################################################################################
//########################################################################################################
         
?>