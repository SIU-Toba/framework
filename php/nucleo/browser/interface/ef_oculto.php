<?php
include_once("nucleo/browser/interface/ef.php");// Elementos de interface
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

	function ef_oculto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		global $solicitud;
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		//Estado directo
		if(isset($parametros["estado"])){
			$this->estado = $parametros["estado"];
		}		
		$this->cargar_estado();
	}

	function clave_memoria()
	{
		return "obj_" . $this->padre[1] . "_ef_" . $this->id_form;
	}

	function cargar_estado($estado=null)
	//Desabilito la carga via POST y utilizo memoria
	{
		global $solicitud;
		if(isset($estado)) {	//Carga el estado a partir del parametro
			$this->estado = $estado;
			$solicitud->hilo->persistir_dato_sincronizado($this->clave_memoria(), $this->estado);
		} else { //Intenta cargar el estado a partir del hilo
			$temp = $solicitud->hilo->recuperar_dato($this->clave_memoria());
			if(isset($temp)){
				$this->estado = $temp;
				//Tengo que memorizar el estado para la proxima instanciacion
				$solicitud->hilo->persistir_dato_sincronizado($this->clave_memoria(), $this->estado);
			}
		}
		return true;
	}

	function resetear_estado()
	//Devuelve el estado interno
	{
		global $solicitud;
		$solicitud->hilo->eliminar_dato_sincronizado($this->clave_memoria());
		if(isset($this->estado)){
			unset($this->estado);
		}
	}

	function obtener_input()
	{
		return null;
	}

	function obtener_interface()
	{
		return null;;
	}
	
	function javascript_master_notificar()
	{
		return null;
	}
	
	function javascript_master_get_estado()
	{
		return " function master_get_estado_{$this->id_form}() { return '".apex_ef_valor_oculto."' }\n";		
	}
	
	function javascript_master_cargado()
	{
		return " function master_cargado_{$this->id_form}() { return true; }\n";
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
	
	function ef_oculto_proyecto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
        global $solicitud;
        $parametros["estado"]=$solicitud->hilo->obtener_proyecto();
		parent::ef_oculto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
	}
    
	function cargar_estado($estado="")
	//Desabilito la carga via POST
	{
        global $solicitud;
        if($estado!=""){
    		$this->estado = $estado;
        }else{
            $this->estado = $solicitud->hilo->obtener_proyecto();
        }
		return true;
	}

	function resetear_estado()
	//Resetea el estado INTERNO
	{
		global $solicitud;
		$this->estado = $solicitud->hilo->obtener_proyecto();
	}	
	
    function validar_estado()
    //Controla que el proyecto ACTUAL sea el mismo que el proyecto SETEADO
    {
        global $solicitud;
        $proyecto_actual = $solicitud->hilo->obtener_proyecto();
        if($this->estado == $proyecto_actual ){
			$this->validacion = true;
            return array(true,"(A: $proyecto_actual S:{$this->estado})");
        }else{
			$this->validacion = false;
            return array(false,"No se puede cargar un ELEMENTO fuera de su PROYECTO\n(A: $proyecto_actual S:{$this->estado})");
        }
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
	
	function ef_oculto_secuencia($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::ef_oculto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
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
	
	function ef_oculto_usuario($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		global $solicitud;
		parent::ef_oculto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		$this->estado = $solicitud->hilo->obtener_usuario();
	}

	function resetear_estado()
	//Devuelve el estado interno
	{
		global $solicitud;
		$this->estado = $solicitud->hilo->obtener_usuario();
	}	

	function cargar_estado($estado=null)
	//Desabilito la carga via POST y utilizo memoria
	{
		global $solicitud;
		$this->estado = $solicitud->hilo->obtener_usuario();
		return true;
	}

}
//########################################################################################################
//########################################################################################################
?>