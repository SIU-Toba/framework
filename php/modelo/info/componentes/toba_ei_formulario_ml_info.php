<?php
class toba_ei_formulario_ml_info extends toba_ei_formulario_info
{
	static function get_tipo_abreviado()
	{
		return "Form. ML";		
	}
	

	function get_nombre_instancia_abreviado()
	{
		return "form_ml";	
	}		
	
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function get_molde_subclase()
	{
		$molde = parent::get_molde_subclase(true);
		$molde->agregar_bloque( $this->get_molde_eventos_sobre_fila() );
		return $molde;
	}
	
	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		if ($this->tipo_analisis() == 'EVENTOS') {
			$eventos['registro_alta']['parametros'] = array('datos', 'id_fila');
			$eventos['registro_alta']['comentarios'] = array("El \$id_fila es la clave de la fila en el arreglo asociativo retornado en la modificación");
			$eventos['registro_baja']['parametros'] = array('id_fila');
			$eventos['registro_baja']['comentarios'] = array("El \$id_fila es la clave de la fila en el arreglo asociativo retornado en la modificación");
			$eventos['registro_modificacion']['parametros'] = array('datos', 'id_fila');
			$eventos['registro_modificacion']['comentarios'] = array("El \$id_fila es la clave de la fila en el arreglo asociativo retornado en la modificación");
		}
		return $eventos;
	}
	
	function agregar_online()
	{
		return ($this->datos['_info_formulario']['filas_agregar_online'] == true);
	}
	
	function tipo_analisis() {
		return $this->datos['_info_formulario']['analisis_cambios'];
	}

	function get_comentario_carga()
	{
		return array(
			"Permite cambiar la configuración del ML previo a la generación de la salida",		
			"El formato debe ser una matriz array('id_fila' => array('id_ef' => valor, ...), ...)"
		);
	}

	//-- Generacion de metadatos

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
				$evento[0]['etiqueta'] = "&Modificacion";
				$evento[0]['maneja_datos'] = 1;
				$evento[0]['implicito'] = true;
				$evento[0]['orden'] = 3;
				$evento[0]['en_botonera'] = 0;		
				break;
		}
		return $evento;
	}
	
	static function get_eventos_internos(toba_datos_relacion $dr)
	{
		$eventos = array();
		if (! $dr->tabla('prop_basicas')->get_columna('filas_agregar_online') ) {
			$eventos['pedido_registro_nuevo'] = "El usuario notifica que quiere dar de alta un nuevo registro y necesita suministrarle un registro en blanco".
												" para comenzar a editar. Escuchando este evento se puede cancelar el pedido de alta o brindar una fila inicial con datos usando ".
												" el método <em>set_registro_nuevo</em> del ML";
		}
		if ("EVENTOS" == $dr->tabla('prop_basicas')->get_columna('analisis_cambios') ) {
			$eventos['registro_alta'] = "El usuario crea una nuevo registro. Como primer parámetro recibe los datos y como segundo el id de la fila.";
			$eventos['registro_modificacion'] = "El usuario modifica un registro existente. Como primer parámetro recibe los datos y como segundo el id de la fila.";			
			$eventos['registro_baja'] = "El usuario borra un registro existente. Como primer parámetro recibe el id de la fila eliminada.";
		}
		return $eventos;
	}	
}
?>