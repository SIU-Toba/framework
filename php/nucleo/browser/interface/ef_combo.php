<?php
include_once("nucleo/browser/interface/ef.php");// Elementos de interface
/*
*	ef <abstracta>
*	|
*	+----> ef_combo <abstracta> (recibe un ARRAY) 
*				|		FALTA: poder decir cual es el valor por defecto cuando no hay estado!!!
*				|
*				+----> ef_combo_lista (recibe los elementos en un STRING separado por ",")
*				|
*				+----> ef_combo_lista_c (recibe los elementos en un STRING separado por "/"
*				|							y su clave-valor separado por ",")
*				|
*				+----> ef_combo_db (recibe un SQL)
*			       	   	|
*			   	        +----> ef_combo_proyecto (recibe un SQL, agrega un WHERE para el proyecto [+toba?]
*					|							Este EF tendria que ser el hijo (usar una ventana de 
*					|							reescritura de SQL) de un multiclave generico...
*	        		        +----> ef_combo_db_ayuda (recibe un SQL con tres columnas : id, valor del combo, ayuda)
*/

class ef_combo extends ef
//PARAMETROS ADICIONALES:
// "valores": Array con valores a mostrar en el combo
// "no_seteado": Nombre del valor NULO
{
	var $valores;				//Array con valores de la lista
	var $predeterminado;		//Si el combo tiene predeterminados, tengo que inicializarlo
	var $no_seteado;
	protected $categorias;
	
	static function get_parametros()
	{
		$parametros["no_seteado"]["descripcion"]="Descripcion que representa la NO-SELECCION del combo";
		$parametros["no_seteado"]["opcional"]=1;	
		$parametros["no_seteado"]["etiqueta"]="Desc. No seleccion";	
		$parametros['valores']['descripcion'] = 'Lista de valores estáticos a incluir';
		$parametros['valores']['opcional'] = 1;
		$parametros['valores']['etiqueta'] = 'Valores fijos';
		$parametros["predeterminado"]["descripcion"]="Valor predeterminado";
		$parametros["predeterminado"]["opcional"]=1;	
		$parametros["predeterminado"]["etiqueta"]="Valor predeterminado";	
		return $parametros;
	}

	function ef_combo($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
        $this->valores = array();
		//Manejo del valor NO SETEADO
		if(isset($parametros["no_seteado"])){
    		if($parametros["no_seteado"]!=""){
	    		$this->no_seteado = $parametros["no_seteado"];
	    		$this->estado = apex_ef_no_seteado;
		    	$this->valores[apex_ef_no_seteado] = $parametros["no_seteado"];
    		}else{
    			$this->no_seteado = null;
    		}
        }else{
   			$this->no_seteado = null;
    	}
		//Esto se hace de esta manera para que el valor NO SETEADO se vea primero
		if(isset($parametros["valores"])){
			if(is_array($parametros["valores"])){
				$this->valores = $this->valores + $parametros["valores"];
			}
		}
		//Manejo de VALORES predeterminados
		$this->predeterminado = null;
		if(isset($parametros["predeterminado"])){
    		if($parametros["predeterminado"]!=""){
	    		$this->estado = $parametros["predeterminado"];
   			$this->predeterminado = $parametros["predeterminado"];
    		}
		}
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function cargar_datos($datos)
	{
		$this->valores = $datos;
	}

	function establecer_solo_lectura()
	{
		//Elimino los valores distintos al seleccionado
		/* 
			Esto genera errores cuando se deshabilita el combo antes de
			de establecer el valor
		if(isset($this->estado)){
			foreach(array_keys($this->valores) as $valor){
				if($valor != $this->estado){
					unset($this->valores[$valor]);
				}	
			}
		}
		*/
        $this->solo_lectura = true;
	}

	function obtener_info()
	{
		//Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
		if($this->activado()){
			return "{$this->etiqueta}: {$this->valores[$this->estado]}";
		}
	}

	function obtener_valores()
	{
		return $this->valores;	
	}

	function obtener_descripcion_estado()
	{
		return $this->valores[ $this->estado ];
	}

	//-----------------------------------------------
	//-------------- DEPENDENCIAS -------------------
	//-----------------------------------------------
	
	function javascript_slave_recargar_datos()
	{
		return "
		function recargar_slave_{$this->id_form}(datos)
		{
			s_ = document.{$this->nombre_formulario}.{$this->id_form};
			s_.options.length = 0;//Borro las opciones que existan
			//Creo los OPTIONS recuperados
			var hay_datos = false
			for (id in datos){
				if (id !=  '".apex_ef_no_seteado."')
					hay_datos = true;
				s_.options[s_.options.length] = new Option(datos[id], id);
			}
			if (hay_datos)
			{
				s_.disabled = false;
				s_.focus();
			}
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
			s_.disabled = true;
			s_.options.length = 0;\n";
		if(isset($this->no_seteado)){
			$js .= "s_.options[0] = new Option('{$this->no_seteado}', '".apex_ef_no_seteado."');\n";
		}else{
			$js .= "s_.options[0] = new Option('', '".apex_ef_no_seteado."');\n";
		}
		//Reseteo las dependencias	
		if(isset($this->dependientes)){
			foreach($this->dependientes as $dependiente){
				$js .= " reset_{$dependiente}{$this->agregado_form}();\n";
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
			return ( master_get_estado_{$this->id_form}() != '".apex_ef_no_seteado."');
		}
		";		
	}	
	//-----------------------------------------------
	//-----------------------------------------------
	//-----------------------------------------------	

	function obtener_input($input_extra=null)
	{
		if (isset($input_extra)) {
			$this->input_extra .= $input_extra;	
		}
		$estado = $this->obtener_estado_input();
		//ei_arbol($this->valores);
        if ($this->solo_lectura) {
			if (count($this->valores) > 0){
				$valores = $this->valores;
			}else{
				$valores = array($this->no_seteado);
			}
        	$input = form::select("",$estado, $valores, "ef-combo", "disabled");	
			if ($estado == "")
				$estado = apex_ef_no_seteado;
			$input .= form::hidden($this->id_form, $estado);
            return $input;
        } else {
        	$html = $this->obtener_javascript_general() . "\n\n";
			$html .= form::select($this->id_form, $estado ,$this->valores, 'ef-combo', $this->obtener_javascript_input() . $this->input_extra, $this->categorias);
			return $html;
        }
	}

	function obtener_estado_input()
	{
        if (isset($this->estado)) {
            return $this->estado;
        }else{
            return "";
        }
	}

	function resetear_estado()
	//Devuelve el estado interno
	{
		if($this->activado()){
			if(isset($this->predeterminado)){
				$this->estado = $this->predeterminado;
			}else{
				unset($this->estado);
			}
		}
	}

    function validar_estado()
    //Si el campo es obligatorio, el combo no puede tener el valor no_seteado
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
    
	function obtener_consumo_javascript()
	{
		$consumos = array('interface/ef','interface/ef_combo');
		return $consumos;
	}
	
	function crear_objeto_js()
	{
		return "new ef_combo({$this->parametros_js()})";
	}	
	
    function obtener_javascript()
    {
    //Si el campo es obligatorio, el combo no puede tener el valor no_seteado
        if($this->obligatorio){
            $no_seteado = apex_ef_no_seteado;
            return "
    if (formulario.". $this->id_form .".value == '$no_seteado')
    {
    	alert(\"El campo '". $this->etiqueta ."' es obligatorio.\");            
    	formulario.". $this->id_form .".focus();
        return false;
    }
            ";
        }
    }
}
//########################################################################################################
//########################################################################################################

class ef_combo_dao extends ef_combo
/*
	El DAO estatico no deberia ser una clase separada?
	ATENCION: falta el valor predeterminado en el caso de claves compuestas
*/
{
	private $dao;
	private $include;
	private $clase;
	private $requiere_instancia = false;
	private $modo; 		//Carga estatica o a travez del CN
	private $clave;
	private $valor;
	private $opcion_seleccionada;
	private $estado_nulo;
	private $cantidad_claves;
	
	private $agrupador_clave;
	private $agrupador_valor;
	private $agrupador_dao;
	private $agrupador_clase;
	private $agrupador_include;

	static function get_parametros()
	{
		$parametros["dao"]["descripcion"]="Metodo a ejecutar para recuperar datos.";
		$parametros["dao"]["opcional"]=0;	
		$parametros["dao"]["etiqueta"]="DAO - Metodo";	
		$parametros["clase"]["descripcion"]="Nombre de la clase";
		$parametros["clase"]["opcional"]=1;	
		$parametros["clase"]["etiqueta"]="DAO - Clase";	
		$parametros["include"]["descripcion"]="Archivo donde se encuentra definida la clase";
		$parametros["include"]["opcional"]=1;	
		$parametros["include"]["etiqueta"]="DAO - Include";	
		$parametros["clave"]["descripcion"]="Indica que INDICES de la matriz recuperada se utilizaran como CLAVE (Si son varios separar con comas)";
		$parametros["clave"]["opcional"]=0;	
		$parametros["clave"]["etiqueta"]="DAO - resultado: CLAVE";	
		$parametros["valor"]["descripcion"]="Indica que INDICE de la matriz recuperada se utilizara como DESCRIPCION";
		$parametros["valor"]["opcional"]=0;	
		$parametros["valor"]["etiqueta"]="DAO - resultado: DESC.";	
		$parametros["no_seteado"]["descripcion"]="Descripcion que representa la NO-SELECCION del combo.";
		$parametros["no_seteado"]["opcional"]=1;	
		$parametros["no_seteado"]["etiqueta"]="Desc. No seleccion";	
		$parametros["predeterminado"]["descripcion"]="Valor predeterminado";
		$parametros["predeterminado"]["opcional"]=1;	
		$parametros["predeterminado"]["etiqueta"]="Valor predeterminado";
		$parametros["dependencias"]["descripcion"]="El estado dependende de otro EF (CASCADA). Lista de EFs separada por comas";
		$parametros["dependencias"]["opcional"]=1;	
		$parametros["dependencias"]["etiqueta"]="Dependencias";	

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

	function ef_combo_dao($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$parametros['valores'] = array();
	
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
		
		//--- AGRUPADOR
		if(isset($parametros["agrupador_clave"])){
			$this->agrupador_clave = explode(',',$parametros["agrupador_clave"]);
			$this->agrupador_clave = array_map("trim",$this->agrupador_clave);			
			unset($parametros["agrupador_clave"]);
		}
		if(isset($parametros["agrupador_valor"])){
			$this->agrupador_valor = $parametros["agrupador_valor"];
			unset($parametros["agrupador_valor"]);
		}
		if(isset($parametros["agrupador_dao"])){
			$this->agrupador_dao = $parametros["agrupador_dao"];
			unset($parametros["agrupador_dao"]);
		}
		if(isset($parametros["agrupador_include"])){
			$this->agrupador_include = $parametros["agrupador_include"];
			unset($parametros["agrupador_include"]);
		}
		if(isset($parametros["agrupador_clase"])){
			$this->agrupador_clase = $parametros["agrupador_clase"];
			unset($parametros["agrupador_clase"]);
		}		
		
		parent::ef_combo($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		
		//---------------------- Manejo de CLAVES compuestas ------------------
		//Clave de los datos a recibir
		if(isset($parametros["clave"])){
			$this->clave = explode(",",$parametros["clave"]);
			$this->clave = array_map("trim",$this->clave);
		}else{
			//SI no esta definido esto tiene que tirar una excepcion
		}
		//**** CC!
		$this->cantidad_claves = count($this->clave);
		//Representacion a nivel datos de lo no_seteado
		if( $this->cantidad_claves > 1){
			if(!is_array($this->dato)){
				toba::get_cola_mensajes()->agregar("COMBO: {$this->etiqueta}. La cantidad de claves ({$this->cantidad_claves})
															tiene que corresponderse con la cantidad de datos manejados por el EF (1)","error");
			}else{
				foreach($this->dato as $dato){
					$this->estado_nulo[$dato] = 'NULL';
				}
			}
	        if(isset($parametros["no_seteado"])){
	    		if($parametros["no_seteado"]!=""){
					$this->opcion_seleccionada = apex_ef_no_seteado;
					$this->estado = $this->estado_nulo;
	    		}
	        }
		}
		//Clave de los datos a recibir
		if(isset($parametros["valor"])){
			$this->valor = $parametros["valor"];
		}else{
			//SI no esta definido esto tiene que tirar una excepcion
		}
		//----------------------------------------------------------------------

		//Si el elemento no posee dependencias lo puedo cargar ahora...
		//Sino hay que esperar que la carga se llame explicitamente una vez que el padre se encuentre cargado
		//El modo estatico puede funcionar con cascadas
		if($this->modo == "estatico"){
			if(is_array($this->dependencias)){
				//$this->establecer_solo_lectura();
				//$this->valores = array();
				$this->input_extra = " disabled ";
			}else{
				$this->cargar_datos();
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
		if( $this->modo == "estatico" )
		{
			return null;
		}else{
			return $this->dao;		
		}
	}

	function cargar_datos($valores=null)
	/*
		Si el DAO esta a a cargo del CN, el CN lo carga a travez de este metodo.
		Si el DAO se carga a travez de una clase estatica, el mismo obtiene los
		datos directamente de la misma, obviando los parametros
		Esto hay que pensarlo un poco mejor
	*/
	{
		if($this->modo =="estatico" ){
			$valores = $this->recuperar_datos_dao();
		}
		$this->adjuntar_datos($valores);
	}

	function cargar_datos_master_ok()
	//Si el master esta cargado, el EF procede a cargar sus registros
	{
		/*
			ATENCION: Esto que sigue es extraño, al DAO hay que pasarle en array
			de	"$this->dependencias_datos". No se hace ahora para soportar 
			compatibilidad con el comechingones...
		*/
		$parametros = array();
		for($a=0;$a<count($this->dependencias);$a++){
			$parametros[] = "'" . $this->dependencias_datos[$this->dependencias[$a]] . "'";
		}
		$param = implode(", ", $parametros);
		if($this->modo =="estatico" )
		{
			$valores = $this->recuperar_datos_dao($param);
			if(isset($valores)){
				$this->adjuntar_datos($valores);	
				$this->input_extra = "";
			}else{
				$this->valores[apex_ef_no_seteado] = $this->no_seteado;
			}
		}else{
			throw new excepcion_toba("Las cascadas de DAO no estan preparadas para metodos no estaticos");
		}
	}

	function recuperar_datos_dao($param=null)
	//ATENCION: los parametros son codigo PHP a evaluar, no son un array...
	{
		include_once($this->include);
		if($this->requiere_instancia){
			$sentencia = "\$c = new {$this->clase}();
							\$valores = \$c->{$this->dao}($param);";
		}else{
			$sentencia = "\$valores = " .  $this->clase . "::" . $this->dao ."($param);";
		}
		eval($sentencia);//echo $sentencia;
		return $valores;
	}

	function adjuntar_datos($valores)
	//ATENCION: Las claves de los combos, hay que encriptarlas?
	{
		if (isset($this->agrupador_dao)) {
			//Creo las categorias
			require_once($this->agrupador_include);
			$grupos = call_user_func(array($this->agrupador_clase, $this->agrupador_dao));
			$carpetas = dao_editores::get_carpetas_posibles();
			$this->categorias = array();			
			foreach ($grupos as $grupo) {
				$this->categorias[$grupo[$this->agrupador_valor]] = array();
			}
		}
		//-[ 0 ]- Incluyo el valor no seteado
		if(isset($this->no_seteado)){
			$this->valores[apex_ef_no_seteado] = $this->no_seteado;
		}
		//ei_arbol($valores, $this->id);
		//-[ 1 ]- Armo los valores...
		if( $this->cantidad_claves > 1){
			//**** CC!
			//La clave es COMPUESTA
			for($a=0;$a<count($valores);$a++){
				//Determino la clave
				//Este algoritmo podria ser mejor...
				/*
					ATENCION, este es un punto en el que puede aparecer un error de tiempo de
					definicion... hay que pensar un esquema en el cual puedan ponerse controles
					sin afectar el tiempo de ejecucion cuando el sistema este en produccion...	
				*/
				$id = "";
				for($c=0;$c<count($this->clave);$c++){
					$id .= $valores[$a][$this->clave[$c]] . apex_ef_separador;
				}
				$id = substr($id,0,strlen($id)-strlen(apex_ef_separador));
				//Adjunto los DATOS
				$this->valores[ $id ] = $valores[$a][$this->valor];
				
				//Busco la categoria donde encaja
				if (isset($grupos)) {
					foreach ($grupos as $grupo) {
						//Hace el macheo de claves
						$igual = true;
						for($c=0;$c<count($this->agrupador_clave);$c++) {
							//Si el item pertenece a este grupo lo agrega
							if ($grupo[$this->agrupador_clave[$c]] != $valores[$a][$this->agrupador_clave[$c]]) {
								$igual = false;
							}
						}
						if ($igual) {
							$this->categorias[$grupo[$this->agrupador_valor]][] = $id;
						}
					}
				}
			}
		}else{
			//La clave es SIMPLE
			$id = $this->clave[0];
			for($a=0;$a<count($valores);$a++){
				//Adjunto los DATOS
				$this->valores[ $valores[$a][$id] ] = $valores[$a][$this->valor];
			}
		}
	}

	function cargar_estado($estado=null)
	//Carga el estado interno. Es un array asociativo del tipo dato:valor
	{
		if( $this->cantidad_claves > 1){
		//**** CC!
	   		if(isset($estado) && ( count($estado) > 0 ))
	   		{								
					//El estado tiene el formato adecuado?
					if(count($estado) <> $this->cantidad_claves){
						throw new excepcion_toba("Ha intentado cargar el combo '{$this->id}' con un array que posee un formato inadecuado " .
										" se esperaban {$this->cantidad_claves} claves, pero se utilizaron: ". count($estado) . ".");
					}
					//Si el estado es nulo tengo que manejarlo de una forma especial
					$valores = "";
					foreach($estado as $valor){
						$valores .= $valor;
					}
					if(trim($valores)==""){									//Valor NULO
						$this->estado = $this->estado_nulo;
						$this->opcion_seleccionada = apex_ef_no_seteado;
					}else{													//Valor seteado
			    		$this->estado=$estado;
						//Deduzco la opcion seleccionada del estado
						$opcion = "";
		    	        foreach($this->dato as $dato){//Sigo el orden de las columnas
		        	        $opcion .= $this->estado[$dato] . apex_ef_separador;
			            }
		    	       //Saca el ultimo apex_ef_separador
						$opcion = substr($opcion, 0, -1 * strlen(apex_ef_separador));
						$this->opcion_seleccionada = $opcion;
					}
					return true;
			}elseif(isset($_POST[$this->id_form])){
	            //Deduzco el estado de la opcion seleccionada
	   			$this->opcion_seleccionada=$_POST[$this->id_form];
				//echo $this->id . " - " . $this->opcion_seleccionada. "<br>";
				if($this->opcion_seleccionada == apex_ef_no_seteado){	//Valor nulo
					$this->estado = $this->estado_nulo;
				}else{													//Valor seteado
		            $temp = explode(apex_ef_separador, $this->opcion_seleccionada);
	    	        $temp_ind = 0;
					unset($this->estado);
	        	    foreach($this->dato as $dato){//Sigo el orden de las columnas
	            	    $this->estado[$dato] = $temp[$temp_ind];
	                	$temp_ind++;
		            }
				}
				//ei_arbol($this->estado,$this->id);
				return true;
	    	}
			return false;
		}else{
			return parent::cargar_estado($estado);
		}
	}

	function obtener_estado()
	//Devuelve el estado interno
	{
		if( $this->cantidad_claves > 1){
		//**** CC!
			if($this->activado()){
				return $this->estado;
			}else{
				return $this->estado_nulo;
			}
		}else{
			return parent::obtener_estado();
		}
	}

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		if( $this->cantidad_claves > 1){
		//**** CC!
			return isset($this->estado) && ($this->estado !==  $this->estado_nulo);
		}else{
			return parent::activado();
		}
	}
	
	function tiene_carga_dao() 
	{
		return true;	
	}

	function obtener_estado_input()
	{
		if( $this->cantidad_claves > 1){
		//**** CC!
			return $this->opcion_seleccionada;
		}else{
			return parent::obtener_estado_input();
		}
	}

	function establecer_solo_lectura()
	{
		if( $this->cantidad_claves > 1){
		//**** CC!
			//Elimino los valores distintos al seleccionado
			if(isset($this->estado)){
				foreach(array_keys($this->valores) as $valor){
					if($valor != $this->opcion_seleccionada){
						unset($this->valores[$valor]);
					}	
				}
			}
		}
		parent::establecer_solo_lectura();		
	}

}
//########################################################################################################
//########################################################################################################

class ef_combo_lista extends ef_combo
//PARAMETROS ADICIONALES:
// "lista": La lista representada como un STRING con los elementos separados por COMAS
// "no_seteado": Valor que representa el estado de NO activado
{
	static function get_parametros()
	{
		$parametros["lista"]["descripcion"]="a lista representada como un STRING con los elementos separados por COMAS";
		$parametros["lista"]["opcional"]=1;	
		$parametros["lista"]["etiqueta"]="Lista";	
		return $parametros;
	}

	function ef_combo_lista($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if(isset($parametros["lista"])){
			$temp = explode(",",$parametros["lista"]);
			foreach($temp as $t){
				$t = trim($t);
				$parametros["valores"][$t] = $t;
			}
		}else{
			$parametros["valores"] = array();
		}
		unset($parametros["lista"]);//Este valor no significa nada para el padre
		parent::ef_combo($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
}

//########################################################################################################
//########################################################################################################

class ef_combo_lista_c extends ef_combo
//PARAMETROS ADICIONALES:
// "lista": La lista representada como un STRING con los elementos separados por "/" y
// 			la clave y el valor separados por ","
// "no_seteado": Valor que representa el estado de NO activado
{
	static function get_parametros()
	{
		$parametros = ef_combo::get_parametros();
		$parametros['lista']['descripcion'] = "La clave/valor se separa por ',' y los pares por '/'";
		$parametros['lista']['opcional'] = 0;
		$parametros['lista']['etiqueta'] = "Lista de valores";
		return $parametros;
	}

	function ef_combo_lista_c($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$elementos = explode("/",$parametros["lista"]);
		foreach($elementos as $elemento){
			$opcion = explode(",",$elemento);
			$parametros["valores"][trim($opcion[0])] = trim($opcion[1]);
		}
         unset($parametros["lista"]);//Este valor no significa nada para el padre
		parent::ef_combo($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
}

//########################################################################################################
//########################################################################################################

class ef_combo_db extends ef_combo
// Este elemento de formulario consiste en una lista extraida de una tabla.
//PARAMETROS ADICIONALES:
// "sql": SQL que genera la lista (EL sql debe devolver dos columnas: clave, descripcion)
// "no_seteado": Valor que representa el estado de NO activado
{
	var $sql;
	var $fuente;
	protected $dependencias_opcionales = false;

	static function get_parametros()
	{
		$parametros = ef_combo::get_parametros();
		$parametros["sql"]["descripcion"]="Query que carga al combo";
		$parametros["sql"]["opcional"]=0;	
		$parametros["sql"]["etiqueta"]="SQL";	
		$parametros["dependencias"]["descripcion"]="El estado dependende de otro EF (CASCADA). Lista de EFs separada por comas";
		$parametros["dependencias"]["opcional"]=1;	
		$parametros["dependencias"]["etiqueta"]="Dependencias";			
		$parametros["dependencias_opcionales"]["descripcion"]="(1 o 0) Indica si las dependencias deben estar todas con valores para recargar sus valores";
		$parametros["dependencias_opcionales"]["opcional"]=1;	
		$parametros["dependencias_opcionales"]["etiqueta"]="Dependencias son opcional";
		return $parametros;
	}

	function ef_combo_db($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if(!($this->sql = stripslashes($parametros["sql"]) )){
			monitor::evento("bug","COMBO DB: SQL Vacio.");
		}
        if((isset($parametros["fuente"]))&&(trim($parametros["fuente"])!="")){
    		$this->fuente = $parametros["fuente"];
            unset($parametros["fuente"]);
        }else{
            $this->fuente = "instancia"; //La instancia por defecto es la CENTRAL
        }
        if (isset($parametros['dependencias_opcionales'])) {
        	$this->dependencias_opcionales = $parametros['dependencias_opcionales'];
        }
//		echo $this->sql . "<br>";
//     	echo $this->fuente;
        unset($parametros["sql"]);

		parent::ef_combo($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		if(is_array($this->dependencias)){
			$this->valores = array();
		}else{
			$this->cargar_datos_db();
		}
		
	}

	function cargar_datos_db()
	{
		$this->valores = array();//Limpio la lista de valores
		if(isset($this->no_seteado)){
    		if(trim($this->estado)==""){
    			$this->estado = apex_ef_no_seteado;
	   		}
	    	$this->valores[apex_ef_no_seteado] = $this->no_seteado;
        }
		$rs = toba::get_db($this->fuente)->consultar($this->sql, apex_db_numerico);
		$temp = $this->preparar_valores($rs);
		if(is_array($temp)){
			$this->valores = $this->valores + $temp;
		}
		//ei_arbol($this->valores);
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
	}

    function preparar_valores($datos_recordset)
    {
		$valores = null;
		foreach ($datos_recordset as $fila){
            $valores[$fila[0]] = $fila[1];
		}
        return $valores;
    }
}
//########################################################################################################
//########################################################################################################
//Este elemento es COMPLEJO (maneja mas de una columna)
/* Los elementos complejos manejan mas de un DATO de la tabla que su padre (el ABM) administra
* por eso la propiedad $this->dato es un ARRAY que indica cuales son los subelementos que se manejan
*/

class ef_combo_db_proyecto extends ef_combo_db
//Este elemento de formulario restringe los registros mostrados a los del PROYECTO ACTUAL o
//PARAMETROS ADICIONALES:
//"sql":    1) usuar %w% para ver donde se concatena el WHERE
//          2) El QUERY tiene que devolver 3 columnas: $this->dato[0], $this->dato[1] y descripcion.
//          Es ABSOLUTANMENTE NECESARIO que orden de estas columnas y el de %this->dato coincidan
//"columna_proyecto": Que columna de la tabla consulatada indica el proyecto al que pertenecen los registros?
//"incluir_toba": Hay que incluir el proyecto TOBA?
//"no_seteado":
{
   var $opcion_seleccionada;
	var $estado_nulo;

	static function get_parametros()
	{
		$parametros = ef_combo_db::get_parametros();
		$parametros["sql"]["descripcion"]="Query que carga al combo. Tiene que devolver 3 columnas (proyecto, id, valor legible)";
		$parametros["columna_proyecto"]["descripcion"]= "Columna de la tabla que representa el proyecto";
		$parametros["columna_proyecto"]["opcional"]=0;	
		$parametros["columna_proyecto"]["etiqueta"]= "Columna del proyecto";
		$parametros["incluir_toba"]["descripcion"]= "¿Hay que listar a toba entre los proyectos?";
		$parametros["incluir_toba"]["opcional"]=0;	
		$parametros["incluir_toba"]["etiqueta"]= "Incluir Toba";		
		return $parametros;
	}

	function ef_combo_db_proyecto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
        //Armo la sentencia que limita al proyecto
        $sql_where =  $parametros["columna_proyecto"] . " = '".toba::get_hilo()->obtener_proyecto()."' ";
		if(isset($parametros["incluir_toba"])){
	        if($parametros["incluir_toba"]) $sql_where .= " OR ".$parametros["columna_proyecto"]." = 'toba'";
		}
        $where[] = "(" . $sql_where .")";
        $parametros["sql"] =  stripslashes(sql_agregar_clausulas_where($parametros["sql"],$where));
        //echo $parametros["sql"] . "<br>";
		//parent::ef_combo_db($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$sql_modificado,$fuente,$no_seteado);
        unset($parametros["columna_proyecto"]);
        unset($parametros["incluir_toba"]);
		//------> ATENCION!! el manejo de NULOS no funciona!! 
		//unset($parametros["no_seteado"]);//----------> ARREGLAR!!!
		parent::ef_combo_db($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		if(count($dato)<>2){
			echo ei_mensaje("ef_combo_proyecto: Error en el elemento '{$this->id}'. El elemento debe manejar 2 datos!");
		}else{
			//Array que representa el estado NULO. 'null' para cada dato.
			foreach($this->dato as $dato){
				$this->estado_nulo[$dato] = 'NULL';
			}
		}
		//Si existe un valor no seteado, es el valor por defecto
        if(isset($parametros["no_seteado"])){
    		if($parametros["no_seteado"]!=""){
				$this->opcion_seleccionada = apex_ef_no_seteado;
				$this->estado = $this->estado_nulo;
    		}
        }
		//SI existe un valor predeterminado, lo utilizo
		if(isset($parametros["predeterminado"])){
    		if($parametros["predeterminado"]!=""){


	    		//Seteo el estado
	    		$estado = explode(",",$parametros["predeterminado"]);
	    		//ei_arbol($estado);
				$x = 0;
				unset($this->estado);
				foreach($this->dato as $dato){
					$estado_ok[$dato] = trim($estado[$x]);
					$x++;
				}
				//$this->estado = $estado_ok;
				$this->predeterminado = $estado_ok;

				//Seteo la opcion seleccionada
				$opcion = "";
    	        foreach($this->dato as $dato){//Sigo el orden de las columnas
        	        $opcion .= $estado_ok[$dato] . apex_ef_separador;
	            }
    	        //Saca el ultimo apex_ef_separador
				$this->opcion_seleccionada = substr($opcion,0,strlen($opcion)-strlen(apex_ef_separador));

    		}
    		//ei_arbol($this->estado);
      }
	}

	function establecer_solo_lectura()
	{
		//Elimino los valores distintos al seleccionado
		if(isset($this->estado)){
			foreach(array_keys($this->valores) as $valor){
				if($valor != $this->opcion_seleccionada){
					unset($this->valores[$valor]);
				}	
			}
		}
	}

    function preparar_valores($recordset)
    {
		$valores = array();
		foreach ($recordset as $fila){
            $valores[$fila[0].apex_ef_separador.$fila[1]] = $fila[2];
		}
        return $valores;
    }

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		return isset($this->estado) && ($this->estado !==  $this->estado_nulo);
	}

	function cargar_estado($estado=null)
	//Carga el estado interno. Es un array asociativo del tipo dato:valor
	{
   		if(isset($estado)){								
			//El estado tiene el formato adecuado?
			if(count($estado)<>2){
				echo ei_mensaje("ef_combo_proyecto: Error en el elemento '{$this->id}'. Se esperaba un array con 2 subindices!");
			}
			//Si el estado es nulo tengo que manejarlo de una forma especial
			$valores = "";
			foreach($estado as $valor){
				$valores .= $valor;
			}
			if(trim($valores)==""){									//Valor NULO
				$this->estado = $this->estado_nulo;
				$this->opcion_seleccionada = apex_ef_no_seteado;
			}else{													//Valor seteado
	    		$this->estado=$estado;
				//Deduzco la opcion seleccionada del estado
				$opcion = "";
    	        foreach($this->dato as $dato){//Sigo el orden de las columnas
        	        $opcion .= $this->estado[$dato] . apex_ef_separador;
	            }
    	        //Saca el ultimo apex_ef_separador
				$this->opcion_seleccionada = substr($opcion,0,strlen($opcion)-strlen(apex_ef_separador));
			}
			return true;
		}elseif(isset($_POST[$this->id_form])){
            //Deduzco el estado de la opcion seleccionada
   			$this->opcion_seleccionada=$_POST[$this->id_form];
			//echo $this->id . " - " . $this->opcion_seleccionada. "<br>";
			if($this->opcion_seleccionada == apex_ef_no_seteado){	//Valor nulo
				$this->estado = $this->estado_nulo;
			}else{													//Valor seteado
	            $temp = explode(apex_ef_separador, $this->opcion_seleccionada);
    	        $temp_ind = 0;
				unset($this->estado);
        	    foreach($this->dato as $dato){//Sigo el orden de las columnas
            	    $this->estado[$dato] = $temp[$temp_ind];
                	$temp_ind++;
	            }
			}
			//ei_arbol($this->estado,$this->id);
			return true;
    	}
		return false;
	}

	function obtener_estado()
	//Devuelve el estado interno
	{
		if($this->activado()){
			return $this->estado;
		}else{
			return $this->estado_nulo;
		}
	}
    
	function obtener_input()
    //COmo este es un elemento complejo, su estado no es el valor del ID del select
	{
		return form::select($this->id_form,$this->opcion_seleccionada,$this->valores);	
	}

}
//########################################################################################################
//########################################################################################################

class ef_combo_db_ayuda extends ef_combo_db
// Este elemento de formulario consiste en una lista extraida de una tabla con una ayuda por elemento.
//PARAMETROS ADICIONALES:
// "sql": SQL que genera la lista (EL sql debe devolver tres columnas: clave, descripcion, ayuda)
// "no_seteado": Valor que representa el estado de NO activado
{
	var $sql;
	var $fuente;
	var $ayuda;

	static function get_parametros()
	{
		$parametros = ef_combo_db::get_parametros();
		$parametros["sql"]["descripcion"]="Query que carga al combo. Tiene que devolver 3 columnas (claves, descripcion, ayuda)";
		return $parametros;	}

	function ef_combo_db_ayuda($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::ef_combo_db($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

    function preparar_valores($recordset)
    {
		$valores = null;
		foreach ($recordset as $fila){
            //Guardo valor del combo y ayuda
			$valores[$fila[0]] = $fila[1];
			$this->ayuda[$fila[0]] = $fila[2];
		}
        return $valores;
    }

	function obtener_estado()
	//Devuelve el estado interno
	{
		if($this->activado()){
			return $this->estado;
		}else{
			return $this->estado_nulo;
		}
	}
	
	function cargar_estado($estado=null)
	//Carga el estado interno
	{
   		if(isset($estado)){								
    		$this->estado=$estado;
			return true;
	    }elseif(isset($_POST[$this->id_form])){
				if(get_magic_quotes_gpc()){
					$this->estado = stripslashes($_POST[$this->id_form]);
				}else{
	   				$this->estado = $_POST[$this->id_form];
				}
			return true;
    	}
		return false;
	}
    
	function obtener_input()
    //Como este es un elemento complejo, su estado no es el valor del ID del select
	{
		$html = "<script language='javascript'>
				function mostrar_ayuda_{$this->id_form}(){
				ef = document.{$this->nombre_formulario}.{$this->id_form}.value;
				switch(ef){\n";
					foreach ($this->ayuda as $proy=>$ayuda){
						$html .= "\t\tcase '$proy':\n\t\t\talert('".addslashes($ayuda)."');\n\t\t\tbreak;\n";
					}			
		$html .= "		}
				}
				</script>\n";		
		$html .= "<table class='tabla-0'>\n";
		$html .= "<tr><td>\n";
        $html .= parent::obtener_input();
    	$html .= "</td><td>\n";
		$html .= "<a href='#' onclick='javascript:mostrar_ayuda_{$this->id_form}();return false'>". recurso::imagen_apl("ayuda.jpg",true,null,null,"Descripcion del ELEMENTO") ."</a>";
		$html .= "</td></tr>\n";
		$html .= "</table>\n";
		return $html;
	}
}
//########################################################################################################
//########################################################################################################
?>
