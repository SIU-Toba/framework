<?php
class toba_ei_filtro_info extends toba_ei_formulario_info
{
	static function get_tipo_abreviado()
	{
		return "Filtro";		
	}	

	function get_nombre_instancia_abreviado()
	{
		return "filtro";	
	}		
	
	function get_molde_subclase($multilinea=false)
	{
		$molde = $this->get_molde_vacio();
		// Redefinicion del LAYOUT
		$ayuda = "Permite modificar la forma en que se grafica el formulario, por defecto un ef sobre el otro";
		$doc = array(
			$ayuda
		);
		$metodo = new toba_codigo_metodo_php('generar_layout', array(), $doc);
		$metodo->set_doc($ayuda);
		$molde->agregar($metodo);
		$php = array();
		foreach ($this->datos['_info_filtro_col'] as $ef => $info) {
			$php[] = '$this->generar_html_ef(\''.$info['nombre'].'\');';
		}
		$molde->ultimo_elemento()->set_contenido($php);
				
		//--Javascript
		$molde->agregar_bloque( $this->get_molde_eventos_js() );	
		$molde->agregar( new toba_codigo_separador_js('Validacion general') );

		//Validar datos
		$doc = array("Validación general del formulario",
			"Retornar true/false para controlar la validación",
			"Usar notificacion.agregar() para mostrar mensajes al usuario"
		);
		$metodo = new toba_codigo_metodo_js('evt__validar_datos', array(), $doc);
		$metodo->set_doc("Validación general del formulario");
		$molde->agregar($metodo);	

		if(count($this->datos['_info_filtro_col'])) {
			//-- Procesamiento de EFs
			$doc = array("Método que se invoca al cambiar el valor del ef en el cliente",
				"Se dispara inicialmente al graficar la pantalla, enviando en true el primer parámetro",
			);			
			$molde->agregar( new toba_codigo_separador_js('Procesamiento de EFs') );
			foreach ($this->datos['_info_filtro_col'] as $ef => $info) {
				$parametros = array('es_inicial');
				if($multilinea) $parametros[] = 'fila';
				$metodo = new toba_codigo_metodo_js('evt__' . $info['nombre'] . '__procesar', $parametros, $doc);
				$metodo->set_doc("Método que se invoca al cambiar el valor del ef en el cliente");
				$molde->agregar($metodo);
			}
			//-- Validacion de EFs
			$molde->agregar( new toba_codigo_separador_js('Validacion de EFs') );		
			$doc = array("Validación puntual de un ef en el cliente",
				"Retornar true/false para controlar la validación",
				"Usar this.ef(id).set_error(mensaje) para mostrar un error contextual al campo"
			);						
			foreach ($this->datos['_info_filtro_col'] as $ef => $info) {
				$parametros = $multilinea ? array('fila') : array();
				$metodo =  new toba_codigo_metodo_js('evt__' . $info['nombre'] . '__validar', $parametros, $doc) ;
				$metodo->set_doc("Validación puntual de un ef en el cliente");
				$molde->agregar($metodo);		
			}
		}
		return $molde;
	}	
	
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	//-- Generacion de metadatos

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'basico';
		$modelo[0]['nombre'] = 'Filtrar - Limpiar';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'basico':
				$evento[0]['identificador'] = "filtrar";
				$evento[0]['etiqueta'] = "&Filtrar";
				$evento[0]['estilo'] = "ei-boton-filtrar";
				$evento[0]['orden'] = 1;
				$evento[0]['en_botonera'] = 1;		
				$evento[0]['maneja_datos'] = 1;
				$evento[0]['grupo'] = 'cargado,no_cargado';
				$evento[0]['imagen_recurso_origen'] = 'apex';
				$evento[0]['imagen'] = 'filtrar.png';				
		
				$evento[1]['identificador'] = "cancelar";
				$evento[1]['etiqueta'] = "&Limpiar";
				$evento[1]['estilo'] = "ei-boton-limpiar";
				$evento[1]['orden'] = 2;
				$evento[1]['en_botonera'] = 1;		
				$evento[1]['grupo'] = 'cargado';
				$evento[1]['imagen_recurso_origen'] = 'apex';
				$evento[1]['imagen'] = 'limpiar.png';				
				break;
		}
		return $evento;
	}
}
?>