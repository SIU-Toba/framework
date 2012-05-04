<?php
class toba_pers_xml_generador_componentes extends toba_pers_xml_generador
{

	/**
	 *
	 * @param string $path path del directorio de componentes
	 * @param array $data componentes a exportar
	 */
	function generar_componentes_nuevas($path, &$data)
	{
		if (empty($data)) return;
		
		$path_nuevos = toba_personalizacion::dir_nuevos;
		$this->plan->abrir_elemento(toba_pers_xml_elementos::nuevas);
		
		foreach (array_keys($data) as $tipo) {
			foreach ($data[$tipo] as $comp) {
				$path_componente = $this->get_path_componente($path_nuevos
															  , $comp['tipo']
															  , $comp['id']['componente']);

				$this->agregar_al_plan($comp['id']['componente'], $path_componente);

				$path_absoluto = $this->get_path_componente($path . $path_nuevos
															  , $comp['tipo']
															  , $comp['id']['componente']);
				
				$this->generar_componente_nueva($comp, $path_absoluto);
			}
		}

		$this->plan->cerrar_elemento();
	}

	/**
	 *
	 * @param string $path path del directorio de componentes
	 * @param array $data componentes a exportar
	 */
	function generar_componentes_modificadas($path, &$data)
	{
		if (empty($data)) return;

		$path_modificados = toba_personalizacion::dir_modificados;
		$this->plan->abrir_elemento(toba_pers_xml_elementos::modificadas);

		foreach (array_keys($data) as $tipo) {
			foreach ($data[$tipo] as $comp) {
				$path_componente = $this->get_path_componente($path_modificados
															  , $comp['tipo']
															  , $comp['id']['componente']);

				$this->agregar_al_plan($comp['id']['componente'], $path_componente);

				$path_absoluto = $this->get_path_componente($path . $path_modificados
															  , $comp['tipo']
															  , $comp['id']['componente']);
				$this->generar_componente_modificada($comp, $path_absoluto);
			}
		}

		$this->plan->cerrar_elemento();
	}

	function generar_componentes_borradas($path, &$data)
	{
		if (empty($data)) return;
		
		$path_borrados = toba_personalizacion::dir_borrados;
		$this->plan->abrir_elemento(toba_pers_xml_elementos::borradas);

		foreach (array_keys($data) as $tipo) {
			foreach ($data[$tipo] as $comp) {
				$path_componente = $this->get_path_componente($path_borrados
															  , $comp['tipo']
															  , $comp['id']['componente']);				
				$this->agregar_al_plan($comp['id']['componente'], $path_componente);
				
				$path_absoluto = $this->get_path_componente($path . $path_borrados
															  , $comp['tipo']
															  , $comp['id']['componente']);								
				$this->generar_componente_borrada($comp, $path_absoluto);
			}
		}

		$this->plan->cerrar_elemento();
	}

	protected function get_path_componente($path_inicial, $tipo, $id)
	{
		$candidato = str_replace('%id%', $id , toba_personalizacion::template_archivo_componente);
		$valido = toba_manejador_archivos::nombre_valido($candidato);
		
		return $path_inicial.$tipo.'/'.$valido;
	}

	/**
	 * @param <type> $componente
	 * @param <type> $path
	 */
	private function generar_componente_borrada(&$componente, $path)
	{
		toba_manejador_archivos::crear_arbol_directorios(dirname($path));		
		$xml =  new toba_xml($path);
		$xml->abrir_elemento(toba_pers_xml_elementos::componente);
		$xml->add_atributo(toba_pers_xml_atributos::id, $componente['id']['componente'], true);
		$xml->add_atributo(toba_pers_xml_atributos::descripcion, $componente['tipo'], true);		
		
		//La eliminacion se hace en el orden inverso.
		$datos_componente = array_reverse($componente['metadata'], true);		
		foreach ($datos_componente as $key_tabla => $contenido) {
			if (empty($contenido)) continue;
			$xml->abrir_elemento(toba_pers_xml_elementos::tabla);
			$xml->add_atributo(toba_pers_xml_atributos::nombre, $key_tabla, true);
			
			foreach ($contenido as $clave => $registro) {
				$estado = toba_personalizacion::registro_deleted;
				$xml->abrir_elemento(toba_pers_xml_elementos::registro);
				$xml->add_atributo(toba_pers_xml_atributos::estado, $estado, true);

				if ($this->grabo_clave($estado)) {
					$xml->add_atributo(toba_pers_xml_atributos::clave, $clave, true);
				}

				$xml->cerrar_elemento();
			}
			$xml->cerrar_elemento();
		}

		$xml->cerrar_elemento();
		$xml->cerrar_documento();
	}
	
	/**
	 * @param <type> $componente
	 * @param <type> $path
	 */
	private function generar_componente_modificada(&$componente, $path)
	{
		toba_manejador_archivos::crear_arbol_directorios(dirname($path));	//Creo el directorio si aun no existe
		$xml =  new toba_xml($path);
		$xml->abrir_elemento(toba_pers_xml_elementos::componente);
		$xml->add_atributo(toba_pers_xml_atributos::id, $componente['id']['componente'], true);
		$xml->add_atributo(toba_pers_xml_atributos::descripcion, $componente['tipo'], true);		

		foreach ($componente['metadata'] as $key_tabla => $contenido) {
			if (empty($contenido)) continue;
			$xml->abrir_elemento(toba_pers_xml_elementos::tabla);
			$xml->add_atributo(toba_pers_xml_atributos::nombre, $key_tabla, true);
			
			foreach ($contenido as $registro) {
				$estado = $registro['estado'];

				$xml->abrir_elemento(toba_pers_xml_elementos::registro);
				$xml->add_atributo(toba_pers_xml_atributos::estado, $estado, true);

				if ($this->grabo_clave($estado)) {
					$xml->add_atributo(toba_pers_xml_atributos::clave, $registro['clave'], true);
				}

				foreach (array_eliminar_nulls($registro['data']) as $columna => $valor) {
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
		}

		$xml->cerrar_elemento();
		$xml->cerrar_documento();
	}

	private function generar_componente_nueva(&$componente, $path)
	{
		toba_manejador_archivos::crear_arbol_directorios(dirname($path));	//Creo el directorio si aun no existe		
		$xml =  new toba_xml($path);
		$xml->abrir_elemento(toba_pers_xml_elementos::componente);
		$xml->add_atributo(toba_pers_xml_atributos::id, $componente['id']['componente'], true);
		$xml->add_atributo(toba_pers_xml_atributos::descripcion, $componente['tipo'], true);
		
		foreach ($componente['metadata'] as $key_tabla => $contenido) {
			if (empty($contenido)) continue;

			$xml->abrir_elemento(toba_pers_xml_elementos::tabla);
			$xml->add_atributo(toba_pers_xml_atributos::nombre, $key_tabla, true);

			foreach ($contenido as $registro) {
				$xml->abrir_elemento(toba_pers_xml_elementos::registro);
				foreach (array_eliminar_nulls($registro) as $columna => $valor) {
					$xml->abrir_elemento(toba_pers_xml_elementos::columna);
					$xml->add_atributo(toba_pers_xml_atributos::nombre, $columna, true);
					$xml->add_atributo(toba_pers_xml_atributos::valor, $valor, true);
					$xml->cerrar_elemento();
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
