<?php
  
class ef_upload extends ef
{

	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
	{
		parent :: __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
	}
    
	function obtener_info()
	{
		if($this->activado()){
			return "{$this->etiqueta}: {$this->estado}";
		}
	}
    
	function obtener_input()
	{
		$salida = form::archivo($this->id_form);
		return $salida;
	}

	function cargar_estado($estado=null)
	//Carga el estado interno
	{
		if(isset($estado)){								
			$this->estado=$estado;
			return true;
		}
		elseif(isset($_FILES[$this->id_form]))
		{
			$this->controlar_estado($_FILES[$this->id_form]['error']);
			$this->estado = $_FILES[$this->id_form];
			return true;
		}
		return false;
	}

	function controlar_estado($id)
	{
		switch($id){
			case UPLOAD_ERR_NO_FILE:
				if (isset($this->obligatorio) AND $this->obligatorio == 1)
					throw new excepcion_toba("No se envio un archivo");
				break;
			case UPLOAD_ERR_INI_SIZE:
				throw new excepcion_toba("Se supero el limite seteado en PHP.INI");
				break;
			case UPLOAD_ERR_FORM_SIZE:
				throw new excepcion_toba("Se supero el limite expresado en el FORM");
				break;
			case UPLOAD_ERR_PARTIAL:
				throw new excepcion_toba("Ha ocurrido un error cargando el archivo");
				break;
		}
	}
	
	function obtener_consumo_javascript()
	{
		$consumos = array('interface/ef','interface/ef_upload');
		return $consumos;
	}
	
	function crear_objeto_js()
	{
		return "new ef_upload({$this->parametros_js()})";
	}	
	
}

?>