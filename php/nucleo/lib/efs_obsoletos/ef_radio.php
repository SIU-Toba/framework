<?php
include_once("nnucleo/lib/efs_obsoletos/ef.php");// Elementos de interface
/*
* 			ef <abstracta>
* 			|
*			+----> ef_radio <abstracta> (recibe un ARRAY) 
* 					|		FALTA: poder decir cual es el valor por defecto cuando no hay estado!!!
* 					|
* 					+----> ef_radio_lista (recibe los elementos en un STRING separado por ",")
* 					|
* 					+----> ef_radio_lista_c (recibe los elementos en un STRING separado por "/"
* 					|							y su clave-valor separado por ",")
* 					|
* 					+----> ef_radio_db (recibe un SQL)
* 				       	   	|
* 				   	        +----> ef_radio_proyecto (recibe un SQL, agrega un WHERE para el proyecto [+toba?]
* 							|							Este EF tendria que ser el hijo (usar una ventana de 
* 							|							reescritura de SQL) de un multiclave generico...
*			                +----> ef_radio_db_cascada (recibe n SQLs, uno para combo. Muestra combos que se encuentran relacionados entre si)
*							|
*			                +----> ef_radio_db_ayuda (recibe un SQL con tres columnas : id, valor del combo, ayuda)
*/

class ef_radio extends ef
//PARAMETROS ADICIONALES:
// "valores": Array con valores a mostrar en los radio buttons
// "no_seteado": Nombre del valor NULO
{
	var $valores;				//Array con valores de la lista
	var $predeterminado;		//Si el combo tiene predeterminados, tengo que inicializarlo
	
	static function get_parametros()
	{
		$parametros[""]["descripcion"]="";
		$parametros[""]["opcional"]=1;	
		return $parametros;
	}

	function ef_combo($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
        $this->valores = array();
		//Manejo del valor NO SETEADO
		if(isset($parametros["no_seteado"])){
    		if($parametros["no_seteado"]!=""){
	    		$this->estado = apex_ef_no_seteado;
		    	$this->valores[apex_ef_no_seteado] = $parametros["no_seteado"];
    		}
        }
		//Esto se hace de esta manera para que el valor NO SETEADO se vea primero
		if(is_array($parametros["valores"])) $this->valores = $this->valores + $parametros["valores"];
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

	function obtener_info()
	{
		//Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
		if($this->activado()){
			return "{$this->etiqueta}: {$this->valores[$this->estado]}";
		}
	}

	function obtener_input()
	{
		return form::select($this->id_form,$this->estado,$this->valores);	
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

}
//########################################################################################################
//########################################################################################################
?>