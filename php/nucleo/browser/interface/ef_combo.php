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
		if(isset($this->estado)){
			foreach(array_keys($this->valores) as $valor){
				if($valor != $this->estado){
					unset($this->valores[$valor]);
				}	
			}
		}
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
			return ( master_get_estado_{$this->id_form}() != '".apex_ef_no_seteado."');
		}
		";		
	}	
	//-----------------------------------------------
	//-----------------------------------------------
	//-----------------------------------------------	

	function obtener_input()
	{
		$estado = $this->obtener_estado_input();
		//ei_arbol($this->valores);
        if ($this->solo_lectura)
        {
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
        }else{
				$html = $this->obtener_javascript_general() . "\n\n";
				$html .= form::select($this->id_form, $estado ,$this->valores, 'ef-combo', $this->obtener_javascript_input() . $this->input_extra );
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
		//Clave de los datos a recibir
		if(isset($parametros["clave"])){
			$this->clave = explode(",",$parametros["clave"]);
			$this->clave = array_map("trim",$this->clave);
		}else{
			//SI no esta definido esto tiene que tirar una excepcion
		}
		//Clave de los datos a recibir
		if(isset($parametros["valor"])){
			$this->valor = $parametros["valor"];
		}else{
			//SI no esta definido esto tiene que tirar una excepcion
		}
		unset($parametros["dao"]);
		unset($parametros["clase"]);
		unset($parametros["include"]);
		parent::ef_combo($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
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
		//**** CC!
		$this->cantidad_claves = count($this->clave);
		//Representacion a nivel datos de lo no_seteado
		if( $this->cantidad_claves > 1){
			foreach($this->dato as $dato){
				$this->estado_nulo[$dato] = 'NULL';
			}
	        if(isset($parametros["no_seteado"])){
	    		if($parametros["no_seteado"]!=""){
					$this->opcion_seleccionada = apex_ef_no_seteado;
					$this->estado = $this->estado_nulo;
	    		}
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
			echo ei_mensaje("Las cascadas de DAO no estan preparadas para metodos no estaticos");
		}
	}

	function recuperar_datos_dao($param=null)
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
	{
		//-[ 0 ]- Incluyo el valor no seteado
		if(isset($this->no_seteado)){
			$this->valores[apex_ef_no_seteado] = $this->no_seteado;
		}
		//ei_arbol($valores);
		//-[ 1 ]- Armo los valores...
		if( $this->cantidad_claves > 1){
			//**** CC!
			//La clave es COMPUESTA
			for($a=0;$a<count($valores);$a++){
				//Determino la clave
				//Este algoritmo podria ser mejor...
				$id = "";
				for($c=0;$c<count($this->clave);$c++){
					$id .= $valores[$a][$this->clave[$c]] . apex_ef_separador;
				}
				$id = substr($id,0,strlen($id)-strlen(apex_ef_separador));
				//Adjunto los DATOS
				$this->valores[ $id ] = $valores[$a][$this->valor];
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
	   		if(isset($estado)){								
				//El estado tiene el formato adecuado?
				if(count($estado)<>$this->cantidad_claves){
					echo ei_mensaje("ERROR: la cantidad de claves no coinciden");
					return false;
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
		}else{
			parent::establecer_solo_lectura();
		}
	}

}
//########################################################################################################
//########################################################################################################

class ef_combo_lista extends ef_combo
//PARAMETROS ADICIONALES:
// "lista": La lista representada como un STRING con los elementos separados por COMAS
// "no_seteado": Valor que representa el estado de NO activado
{
	function ef_combo_lista($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if(isset($parametros["lista"])){
			$temp = explode(",",$parametros["lista"]);
			foreach($temp as $t){
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
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$rs = $db[$this->fuente][apex_db_con]->Execute($this->sql);
		if(!$rs){
			monitor::evento("bug","COMBO DB: No se genero el recordset. ". $db[$this->fuente][apex_db_con]->ErrorMsg()." -- SQL: {$this->sql} -- ");
		}
		if($rs->EOF){
			//echo ei_mensaje("EF etiquetado '$etiqueta'<br> No se obtuvieron registros: ". $this->sql);
		}
		$temp = $this->preparar_valores($rs->getArray());
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
	
	function ef_combo_db_proyecto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
        global $solicitud;
        //Armo la sentencia que limita al proyecto
        $sql_where =  $parametros["columna_proyecto"] . " = '".$solicitud->hilo->obtener_proyecto()."' ";
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
				return false;
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
	
	function crear_objeto_js()
	{
		return "new ef_combo_ayuda({$this->parametros_js()})";
	}	
}
//########################################################################################################
//########################################################################################################
?>