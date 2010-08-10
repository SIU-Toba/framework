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

		$path_nuevos = $path . toba_personalizacion::dir_nuevos;
		$this->plan->abrir_elemento(toba_pers_xml_elementos::nuevas);
		
		foreach (array_keys($data) as $tipo) {
			foreach ($data[$tipo] as $comp) {
				$path_componente = $this->get_path_componente($path_nuevos
															  , $comp['tipo']
															  , $comp['id']['componente']);

				$this->agregar_al_plan($comp['id']['componente'], $path_componente);

				$this->generar_componente_nueva($comp, $path_componente);
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

		$path_modificados = $path . toba_personalizacion::dir_modificados;
		$this->plan->abrir_elemento(toba_pers_xml_elementos::modificadas);

		foreach (array_keys($data) as $tipo) {
			foreach ($data[$tipo] as $comp) {
				$path_componente = $this->get_path_componente($path_modificados
															  , $comp['tipo']
															  , $comp['id']['componente']);

				$this->agregar_al_plan($comp['id']['componente'], $path_componente);

				$this->generar_componente_modificada($comp, $path_componente);
			}
		}

		$this->plan->cerrar_elemento();
	}

	function generar_componentes_borradas($path, &$data)
	{
		if (empty($data)) return;

		$path_borrados	= $path . toba_personalizacion::dir_borrados;

		$this->plan->abrir_elemento(toba_pers_xml_elementos::borradas);

		foreach (array_keys($data) as $tipo) {
			foreach ($data[$tipo] as $comp) {
				$this->agregar_al_plan($comp['id']['componente']);
			}
		}

		$this->plan->cerrar_elemento();
	}

	protected function get_path_componente($path_inicial, $tipo, $id)
	{
		$path_tipo = $path_inicial.$tipo.'/';
		toba_manejador_archivos::crear_arbol_directorios($path_tipo);

		$candidato = str_replace('%id%', $id
								 , toba_personalizacion::template_archivo_componente);

		$valido = toba_manejador_archivos::nombre_valido($candidato);
		return $path_tipo.$valido;
	}

	/**
	 * @param <type> $componente
	 * @param <type> $path
	 */
	private function generar_componente_modificada(&$componente, $path)
	{
		$xml =  new toba_xml($path);
		$xml->abrir_elemento(toba_pers_xml_elementos::componente);
		$xml->add_atributo(toba_pers_xml_atributos::id, $componente['id']['componente']);

		foreach ($componente['metadata'] as $key_tabla => $contenido) {
			if (empty($contenido)) continue;
			$xml->abrir_elemento(toba_pers_xml_elementos::tabla);
			$xml->add_atributo(toba_pers_xml_atributos::nombre, $key_tabla);
			
			foreach ($contenido as $registro) {
				$estado = $registro['estado'];

				$xml->abrir_elemento(toba_pers_xml_elementos::registro);
				$xml->add_atributo(toba_pers_xml_atributos::estado, $estado);

				if ($this->grabo_clave($estado)) {
					$xml->add_atributo(toba_pers_xml_atributos::clave, $registro['clave']);
				}

				foreach (array_eliminar_nulls($registro['data']) as $columna => $valor) {
					$xml->abrir_elemento(toba_pers_xml_elementos::columna);
					$xml->add_atributo(toba_pers_xml_atributos::nombre, $columna);
					if ($estado == toba_personalizacion::registro_updated) {
						$xml->add_atributo(toba_pers_xml_atributos::valor, $valor['actual']);
						$xml->add_atributo(toba_pers_xml_atributos::valor_original, $valor['original']);
					} else {	// es un registro nuevo
						$xml->add_atributo(toba_pers_xml_atributos::valor, $valor);
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
		$xml =  new toba_xml($path);
		$xml->abrir_elemento(toba_pers_xml_elementos::componente);
		$xml->add_atributo(toba_pers_xml_atributos::id, $componente['id']['componente']);

		foreach ($componente['metadata'] as $key_tabla => $contenido) {
			if (empty($contenido)) continue;

			$xml->abrir_elemento(toba_pers_xml_elementos::tabla);
			$xml->add_atributo(toba_pers_xml_atributos::nombre, $key_tabla);

			foreach ($contenido as $registro) {
				$xml->abrir_elemento(toba_pers_xml_elementos::registro);
				foreach (array_eliminar_nulls($registro) as $columna => $valor) {
					$xml->abrir_elemento(toba_pers_xml_elementos::columna);
					$xml->add_atributo(toba_pers_xml_atributos::nombre, $columna);
					$xml->add_atributo(toba_pers_xml_atributos::valor, $valor);
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
