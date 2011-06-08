<?php

class toba_pers_xml_generador_tablas extends toba_pers_xml_generador {

	function generar_tablas($path, &$data)
	{
		// Se agrega por uniformidad de los planes de tablas y componentes
		$this->plan->abrir_elemento(toba_pers_xml_elementos::modificadas);

		foreach (array_keys($data) as $tabla) {
			$path_tabla = $this->get_path_tabla('', $tabla);
			$this->agregar_al_plan($tabla, $path_tabla);
			$this->generar_tabla($this->get_path_tabla($path, $tabla), $tabla, $data[$tabla]);
		}
		
		$this->plan->cerrar_elemento();
	}

	protected function get_path_tabla($path_inicial, $tabla)
	{
		$candidato = str_replace('%id%', $tabla
								 , toba_personalizacion::template_archivo_tabla);

		$valido = toba_manejador_archivos::nombre_valido($candidato);
		return $path_inicial.$valido;
	}

	protected function generar_tabla($path, $nombre_tabla, &$tabla)
	{
		$xml =  new toba_xml($path);
		$xml->abrir_elemento(toba_pers_xml_elementos::tabla);
		$xml->add_atributo(toba_pers_xml_atributos::id, $nombre_tabla, true);

		// el contenido es el array con metadata de estado, clave y data
		foreach (array_keys($tabla) as $key_contenido) {	
			$estado = $tabla[$key_contenido]['estado'];

			$xml->abrir_elemento(toba_pers_xml_elementos::registro);
			$xml->add_atributo(toba_pers_xml_atributos::estado, $estado, true);
			
			if ($this->grabo_clave($estado)) {
				$xml->add_atributo(toba_pers_xml_atributos::clave, $tabla[$key_contenido]['clave'], true);
			}

			foreach (array_eliminar_nulls($tabla[$key_contenido]['data']) as $columna => $valor) {
				$xml->abrir_elemento(toba_pers_xml_elementos::columna);
				$xml->add_atributo(toba_pers_xml_atributos::nombre, $columna, true);

				if ($estado == toba_personalizacion::registro_updated) {
					$xml->add_atributo(toba_pers_xml_atributos::valor, $valor['actual'], true);
					$xml->add_atributo(toba_pers_xml_atributos::valor_original, $valor['original'], true);
				} else {	// es un registro nuevo
					$xml->add_atributo(toba_pers_xml_atributos::valor, $valor, true);
				}

				$xml->cerrar_elemento();
			}

			$xml->cerrar_elemento();
		}
		
		$xml->cerrar_elemento();
		$xml->cerrar_documento();
	}


}
?>
