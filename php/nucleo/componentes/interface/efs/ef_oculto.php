<?php
include_once("ef.php");// Elementos de interface
/*
*			ef <abstracta>
* 			|
* 			+----> ef_oculto
*       			|
*		        	+----> ef_oculto_proyecto (Maneja el dato del registro que representa el proyecto)
*       			|
*		        	+----> ef_oculto_secuencia
*       			|
*		        	+----> ef_oculto_usuario (Usuario que realizo la SOLICITUD)
*/

//PARAMETROS ADICIONALES:
//"estado": Valor que tiene que tomar el elemento

class ef_oculto extends ef
{
//Atencion: los elementos ocultos no se propagan a traves del formulario porque no tienen interface.
//La propagacion de su estado a travez de solicitudes se lleva a cabo impementado MEMORIA

	static function get_parametros()
	{
		$parametros = array();
		$parametros['estado']['descripcion'] = "Valor fijo que toma el ef";
		$parametros['estado']['opcional'] = 1;
		$parametros['estado']['etiqueta'] = "Estado";
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		//Estado directo
		if(isset($parametros["estado"])){
			$this->estado_defecto = $parametros["estado"];
			$this->set_estado($this->estado_defecto);
		}		
	}

	function clave_memoria()
	{
		return "obj_" . $this->id_padre[1] . "_ef_" . $this->id_form;
	}

	function set_estado($estado)
	{
		$this->estado = $estado;
		toba::get_hilo()->persistir_dato_sincronizado($this->clave_memoria(), $this->estado);
		return true;
	}

	function cargar_estado_post()
	{
		//Intenta cargar el estado a partir del hilo
		$temp = toba::get_hilo()->recuperar_dato($this->clave_memoria());
		if(isset($temp)) {
			$this->estado = $temp;
			//Tengo que memorizar el estado para la proxima instanciacion
			toba::get_hilo()->persistir_dato_sincronizado($this->clave_memoria(), $this->estado);
			return true;
		}
		return false;
	}
	
	function resetear_estado()
	//Devuelve el estado interno
	{
		toba::get_hilo()->eliminar_dato_sincronizado($this->clave_memoria());
		if(isset($this->estado)){
			unset($this->estado);
		}
		if (isset($this->estado_defecto)) {
			$this->estado = $this->estado_defecto;	
		}
	}

	function get_input()
	{
		return null;
	}

	function obtener_interface()
	{
		return null;;
	}

}
//########################################################################################################
//########################################################################################################

class ef_oculto_proyecto extends ef_oculto
//Este elemento de formulario tienen propositos de seguridad:
//HAce que no se pueda editar un elemento de un proyecto cuando uno se encuentra logueado en otro
{
	static function get_parametros()
	{
		return array();
	}
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
        $parametros["estado"]=toba::get_hilo()->obtener_proyecto();
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
	}
    
	function set_estado($estado="")
	//Desabilito la carga via POST
	{
        if($estado!=""){
    		$this->estado = $estado;
        }else{
            $this->estado = toba::get_hilo()->obtener_proyecto();
        }
		return true;
	}

	function resetear_estado()
	//Resetea el estado INTERNO
	{
		$this->estado = toba::get_hilo()->obtener_proyecto();
	}	
	
    function validar_estado()
    //Controla que el proyecto ACTUAL sea el mismo que el proyecto SETEADO
    {
        $proyecto_actual = toba::get_hilo()->obtener_proyecto();
        if ($this->estado != $proyecto_actual ){
			return "No se puede cargar un ELEMENTO fuera de su PROYECTO\n(A: $proyecto_actual S:{$this->estado})";
        }
        return true;
    }
}
//########################################################################################################
//########################################################################################################

class ef_oculto_secuencia extends ef_oculto
//Este elemento maneja un campo ques una secuencia
{
	static function get_parametros()
	{
		return array();
	}
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
	}
}
//########################################################################################################
//########################################################################################################

class ef_oculto_usuario extends ef_oculto
//Este elemento maneja un campo ques una secuencia
{
	static function get_parametros()
	{
		return array();
	}
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		$this->estado = toba::get_hilo()->obtener_usuario();
	}

	function resetear_estado()
	//Devuelve el estado interno
	{
		$this->estado = toba::get_hilo()->obtener_usuario();
	}	

	function set_estado($estado=null)
	//Desabilito la carga via POST y utilizo memoria
	{
		$this->estado = toba::get_hilo()->obtener_usuario();
		return true;
	}

}
//########################################################################################################
//########################################################################################################
?>