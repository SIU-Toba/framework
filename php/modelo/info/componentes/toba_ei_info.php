<?php
abstract class toba_ei_info extends toba_componente_info
{

	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	protected function hay_evento($nombre)
	{
		foreach ($this->datos['_info_eventos'] as $evento) {
			if ($evento['identificador'] == $nombre) {
				return true;
			}
		}
		return false;	
	}	

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();
		foreach ($this->datos['_info_eventos'] as $evt_db) {
			//ei_arbol($evt_db);
			$id = $evt_db['identificador'];
			$parametros = array();
			$doc = array("Atrapa la interacción del usuario con el botón asociado");
			if( $evt_db['sobre_fila'] ){				
				if ($evt_db['es_seleccion_multiple'] == '1') {
						$parametros[] = '$datos';
						$doc = array('Atrapa la interacción del usuario con el cuadro mediante los checks',
						'@param array $datos Ids. correspondientes a las filas chequeadas.',
						"El formato es de tipo recordset array(array('clave1' =>'valor', 'clave2' => 'valor'), array(....))");
				}else{
						$parametros[] = '$seleccion';
						$doc[] = '@param array $seleccion Id. de la fila seleccionada';
				}
			}else{
				if($evt_db['maneja_datos']) {
					$parametros[] = '$datos';
					$doc[] = '@param array $datos Estado del componente al momento de ejecutar el evento. El formato es el mismo que en la carga de la configuración';
				}
			}
			$eventos[$id]['parametros'] = $parametros;
			$eventos[$id]['comentarios'] = $doc;
			$eventos[$id]['info'] = $evt_db;
		}
		//ei_arbol($eventos);
		return $eventos;
	}

	function eventos_sobre_fila()
	{
		$eventos_sobre_fila = array();
		foreach ($this->eventos_predefinidos() as $evento => $info) {
			if( isset($info['info']) && !$info['info']['implicito'] && $info['info']['sobre_fila']) {
				$eventos_sobre_fila[$evento] = $info;
			}
		}				
		return $eventos_sobre_fila;
	}
	
	function get_comentario_carga()
	{
		return array("");
	}

	static function get_modelos_evento()
	{
		$modelo = array();
		return $modelo;
	}
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		return $evento;
	}
	
	function get_molde_eventos_js()
	{
		$bloque_molde[] = new toba_codigo_separador_js('Eventos');
		foreach ($this->eventos_predefinidos() as $evento => $info) {
			//$info['info'] no esta seteado en los eventos predefinidos agregados a mano
			if( isset($info['info']) && !$info['info']['implicito'] ) {	//Excluyo los implicitos
				// Atrapar evento en JS
				if ($info['info']['accion'] == 'V') { //Vinculo
					$metodo = new toba_codigo_metodo_js('modificar_vinculo__' . $evento, array('id_vinculo'));
					$metodo->set_doc("Permite modificar el destino o parámetros de un vínculo en javascript. [wiki:Referencia/Eventos/Vinculo#ExtensionenJavascript Ver doc]");
					$bloque_molde[] = $metodo;
				} else {
					$metodo = new toba_codigo_metodo_js('evt__' . $evento);
					$metodo->set_doc("Atrapa en javascript la interacción del usuario con el evento. 
										Se puede parar la propagación del evento retornando <strong>false</strong> en la extensión.
										[wiki:Referencia/Eventos#Listeners Ver más]");
					$bloque_molde[] = $metodo;
				}
			}
		}
		return $bloque_molde;
	}	

	function get_molde_eventos_sobre_fila()
	{
		$bloque_molde[] = new toba_codigo_separador_php('Config. EVENTOS sobre fila');
		foreach ($this->eventos_sobre_fila() as $evento => $info) {
			$ayuda = "Permite configurar el evento sobre una fila especifica para modificarlo o anularlo";
			$doc = array(
				$ayuda,
				"@param toba_evento_usuario \$evento Evento diparado",
				"@param array \$fila Clave de la fila"
			);			
			$metodo = new toba_codigo_metodo_php('conf_evt__' . $evento, array('$evento', '$fila'));
			$metodo->set_doc($ayuda);
			$bloque_molde[] = $metodo;
		}
		return $bloque_molde;
	}
}
?>