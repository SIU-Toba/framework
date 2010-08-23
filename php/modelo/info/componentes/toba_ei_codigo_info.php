<?php
class toba_ei_codigo_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "C�digo";
	}
		

	function get_nombre_instancia_abreviado()
	{
		return "codigo";
	}	
		
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
	
	function get_comentario_carga()
	{
		return array(
			"Permite cambiar la configuraci�n del grafico previo a la generaci�n de la salida"
		);
	}

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'basico';
		$modelo[0]['nombre'] = 'Basico';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'basico':
				$evento[0]['identificador'] = "modificacion";
				$evento[0]['etiqueta'] = "&Modificar";
				$evento[0]['maneja_datos'] = 1;
				$evento[0]['implicito'] = true;
				$evento[0]['orden'] = 3;
				$evento[0]['en_botonera'] = 0;
				break;
		}
		return $evento;
	}
}
?>