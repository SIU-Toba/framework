<?php
class dimension_combo_db_restric extends ef_combo_db
{
//---> Hay que persistir los restringidos en la memoria

	var $tabla_ref;
	var $tabla_ref_clave;
	var $tabla_ref_des;
	var $tabla_restric;

	function dimension_combo_db_restric($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		$this->tab_ref = $parametros["tab_ref"];
		$this->tab_ref_clave = $parametros["tab_ref_clave"];
		$this->tab_ref_des = $parametros["tab_ref_des"];
		$this->tab_restric = $parametros["tab_restric"];
		$this->perfil = $parametros["perfil"];
		$this->fuente = $parametros["fuente"];	
		//Armo el SELECT
		if($this->perfil != ""){
			//Si el usuario tiene restringida la dimension, 
			//muestro el combo limitado por la tabla de restricciones.
			$sql = " SELECT dim." .	$this->tab_ref_clave .", dim.".
								$this->tab_ref_des .
					" FROM " . $parametros["tab_ref"]. " dim ,". 
                            $this->tab_restric . " res 
					WHERE (dim." . $this->tab_ref_clave ." = 
							res.". $this->tab_ref_clave .")
					AND (res.usuario_perfil_datos = '". $this->perfil ."')
					ORDER BY 2;";
			//$this->solo_lectura = true;
		}else{
			//Si el usuario no tiene restringida la dimension, muestro
			//todos los registros de la dimension.
			$sql = " SELECT ". 	$this->tab_ref_clave .", ".
										$this->tab_ref_des .
					" FROM " . $this->tab_ref ."
					ORDER BY 2;";
			if(isset($parametros["no_seteado"])){
				$parametros_padre["no_seteado"] = $parametros["no_seteado"];
			}
		}
		$parametros_padre["fuente"] = $this->fuente;
		$parametros_padre["sql"] = $sql;
		parent::ef_combo_db($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio,$parametros_padre);
 	}

	function obtener_where()
	//Devuelve la porcion de WHERE del SQL correspondiente a esta dimension.
	{
		global $solicitud;
		$sql = "";
		if($this->perfil != ""){
			//Si el usuario tiene restringida la dimension le agrego la clausula WHERE correspondiente.
			$sql .= " ( ({$this->dato} = {$this->tab_restric}.{$this->tab_ref_clave}) 
                    AND ( {$this->tab_restric}.usuario_perfil_datos = '". $solicitud->hilo->obtener_usuario_perfil_datos() ."' ) )";
			//Si el perfil de datos es compuesto, se puede elegir un elemento individual del mismo
			if($this->activado()){
				//Si ademas se utiliza como parametro interactivo...
				$sql .= " AND ({$this->dato} = '{$this->estado}') ";
			}
		}else{
			if($this->activado()){
				//Si ademas se utiliza como parametro interactivo...
				$sql .= " ({$this->dato} = '{$this->estado}') ";
			}
		}
		return $sql;
	}

	function obtener_from()
	//Devuelve la porcion del FROM del SQL correspondiente a esta dimension.
	//Como manejo los perfiles por tablas asociadas (JOINs!) necesito que la
	//tabla de restricciones entre en el FROM...
	{
		if($this->perfil != ""){
			//Si el usuario tiene restringida la dimension...
			return $this->tab_restric;
		}
	}

	function obtener_interface()
	{
		return $this->envoltura_filtro($this->obtener_input());
	}

	function validar_estado(){
		//Si es INTERACTIVO, REQUERIDO y esta DESACTIVADO, tiene que gritar...
		if( (!($this->no_interactivo)) && ($this->requerido) && ($this->activado()===false) ){
			return $this->nombre . ": El parametro es requerido y se encuentra desactivado";
		}else{
			return ""; 
		}
	}

//########################################################################################################
//########################################################################################################
}


?>