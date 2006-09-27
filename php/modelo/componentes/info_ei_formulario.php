<?php
require_once('info_ei.php');

class info_ei_formulario extends info_ei
{
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function get_molde_subclase($multilinea=false)
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar_bloque( $this->get_molde_eventos_js() );	
		$molde->agregar( new toba_molde_separador_js('Validacion general') );		
		$molde->agregar( new toba_molde_metodo_js('evt__validar_datos') );		
		if(count($this->datos['info_formulario_ef'])) {
			//-- Procesamiento de EFs
			$molde->agregar( new toba_molde_separador_js('Procesamiento de EFs') );		
			$molde->agregar( new toba_molde_metodo_js('evt__' . $info['identificador'] . '__procesar', $parametros) );
			foreach ($this->datos['info_formulario_ef'] as $ef => $info) {
				$parametros = array('es_inicial');
				if($multilinea) $parametros[] = 'fila';
				$molde->agregar( new toba_molde_metodo_js('evt__' . $info['identificador'] . '__procesar', $parametros) );		
			}
			//-- Validacion de EFs
			$molde->agregar( new toba_molde_separador_js('Validacion de EFs') );		
			foreach ($this->datos['info_formulario_ef'] as $ef => $info) {
				$parametros = $multilinea ? array('fila') : array();
				$molde->agregar( new toba_molde_metodo_js('evt__' . $info['identificador'] . '__validar', $parametros) );		
			}
		}
		return $molde;
	}

	function get_comentario_carga()
	{
		return "El formato del retorno debe ser array('id_ef' => \$valor, ...)";
	}

	//-- Generacion de metadatos

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'basico';
		$modelo[0]['nombre'] = 'Basico';
		$modelo[1]['id'] = 'abm';
		$modelo[1]['nombre'] = 'ABM';
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
			case 'abm':
				$evento[0]['identificador'] = "alta";
				$evento[0]['etiqueta'] = "&Agregar";
				$evento[0]['maneja_datos'] = 1;
				$evento[0]['estilo'] = "ei-boton-alta";
				$evento[0]['orden'] = 1;
				$evento[0]['en_botonera'] = 1;		
				$evento[0]['grupo'] = 'no_cargado';

				$evento[1]['identificador'] = "baja";
				$evento[1]['etiqueta'] = "&Eliminar";
				$evento[1]['estilo'] = "ei-boton-baja";
				$evento[1]['imagen_recurso_origen'] = 'apex';
				$evento[1]['imagen'] = 'borrar.gif';
				$evento[1]['confirmacion'] = "¿Desea ELIMINAR el registro?";
				$evento[1]['orden'] = 2;
				$evento[1]['en_botonera'] = 1;		
				$evento[1]['grupo'] = 'cargado';

				$evento[2]['identificador'] = "modificacion";
				$evento[2]['etiqueta'] = "&Modificar";
				$evento[2]['maneja_datos'] = 1;
				$evento[2]['estilo'] = "ei-boton-mod";
				$evento[2]['orden'] = 3;
				$evento[2]['en_botonera'] = 1;		
				$evento[2]['grupo'] = 'cargado';
				
				$evento[3]['identificador'] = "cancelar";
				$evento[3]['maneja_datos'] = 0;
				$evento[3]['etiqueta'] = "Ca&ncelar";
				$evento[3]['estilo'] = "ei-boton-canc";		
				$evento[3]['orden'] = 4;		
				$evento[3]['en_botonera'] = 1;		
				$evento[3]['grupo'] = 'cargado';
				break;
		}
		return $evento;
	}
}
?>