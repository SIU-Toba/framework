<?php
class toba_ei_cuadro_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "Cuadro";		
	}


	function get_nombre_instancia_abreviado()
	{
		return "cuadro";	
	}		
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar_bloque( $this->get_molde_eventos_js() );		
		$molde->agregar_bloque( $this->get_molde_eventos_sobre_fila() );
		// Cortes de control
		$molde->agregar( new toba_codigo_separador_php('Configuracion de Pantallas','Pantallas') );
		$datos_cortes = rs_ordenar_por_columna($this->datos['_info_cuadro_cortes'],'orden');
		foreach($datos_cortes as $corte) {
			$molde->agregar( new toba_codigo_metodo_php('sumarizar_cc__' . $corte['identificador'] . '__IDENTIFICADOR', array('$filas') ) );
			$molde->ultimo_elemento()->set_contenido('return 0;');
			$molde->agregar( new toba_codigo_metodo_php('html_cabecera_cc_contenido__' . $corte['identificador'], array('&$nodo') ) );
			$molde->ultimo_elemento()->set_contenido('echo \'descripcion\';');
			$molde->agregar( new toba_codigo_metodo_php('html_pie_cc_contenido__' . $corte['identificador'], array('&$nodo') ) );
			$molde->ultimo_elemento()->set_contenido('echo \'descripcion\';');
			$molde->agregar( new toba_codigo_metodo_php('html_pie_cc_cabecera__' . $corte['identificador'], array('&$nodo') ) );
			$molde->ultimo_elemento()->set_contenido('return \'descripcion\';');
		}		
		return $molde;
	}

	//--- Primitivas sobre eventos -------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		if ($this->ordenable()){
			$eventos['ordenar']['parametros'] = array('columna','sentido');
			$eventos['ordenar']['comentarios'] = array("\$sentido puede ser \"des\" o \"asc\"");
		}
		return $eventos;
	}

	function ordenable() {
		return $this->datos['_info_cuadro']['ordenar'];
	}

	function get_comentario_carga()
	{
		return "El formato del retorno debe ser array( array('columna' => valor, ...), ...)";
	}

	//-- Generacion de metadatos

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'seleccion';
		$modelo[0]['nombre'] = 'Seleccion';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'seleccion':
				$evento[0]['identificador'] = "seleccion";
				$evento[0]['etiqueta'] = "";
				$evento[0]['orden'] = 1;
				$evento[0]['sobre_fila'] = 1;
				$evento[0]['en_botonera'] = 0;
				$evento[0]['imagen_recurso_origen'] = "apex";
				$evento[0]['imagen'] = "doc.gif";	
				break;
		}
		return $evento;
	}
	
	static function get_eventos_internos(toba_datos_relacion $dr)
	{
		$eventos = array();
		if ($dr->tabla('prop_basicas')->get_columna('paginar') ) {
			$eventos['cambiar_pagina'] = "Se recibe como parámetro el número de página a la que se cambio.";
		}
		if ($dr->tabla('prop_basicas')->get_columna('ordenar') ) {
			$eventos['ordenar'] = "Se recibe como primer parámetro el sentido del ordenamiento ('asc' o 'des') y como segundo la columna a ordenar. ".
			 						"Si no se atrapa el evento, el ordenamiento lo asume el propio cuadro.";
		}				
		return $eventos;
	}	
}
?>