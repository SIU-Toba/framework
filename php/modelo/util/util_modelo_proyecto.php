<?php
/**
 * Algunas funcionalidades utiles para manipular algunas características de los
 * proyectos
 */
class util_modelo_proyecto
{
    /**
	 * Extiende las clases de componentes de toba o de las componentes extendidas del
	 * proyecto.
	 *
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $path
	 * @param string $tipo valores posibles: 'toba' | 'proyecto'
	 */
	static function extender_componentes(toba_modelo_proyecto $proyecto, $path, $tipo = 'toba')
	{
		$id_proyecto = $proyecto->get_id();

		if (is_dir($path)) {
			toba_manejador_archivos::eliminar_directorio($path);
		}
		toba_manejador_archivos::crear_arbol_directorios($path);
		
		$componentes = self::get_componentes_toba($proyecto);
		if ($tipo == 'toba') {
			$prefijo = '';
			$clase_a_extender = 'toba_%comp%';
		} else {
			$prefijo = 'pers_';
			$clase_a_extender = $id_proyecto.'_%comp%';
		}

		foreach ($componentes as $componente) {
			$nombre_clase = $id_proyecto.'_'.$prefijo.$componente;
			$clase = new toba_codigo_clase($nombre_clase, str_replace('%comp%', $componente, $clase_a_extender));
			$clase->guardar($path.'/'.$nombre_clase.'.php');
		}
	}

	static function get_componentes_toba(toba_modelo_proyecto $proyecto)
	{
		$res = self::get_clases_componentes_toba($proyecto);

		foreach (array_keys($res) as $key) {
			$res[$key] = substr($res[$key], strlen('toba_'));
		}

		return $res;
	}

	/**
	 * Cambia los extends de las clases que extienden de las componentes de $de
	 * a extends de las componentes de $a.
	 * Combinaciones válidas:
	 *	$de = toba		| $a = proyecto
	 *	$de = toba		| $a = personalizacion
	 *	$de = proyecto	| $a = personalizacion
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $de valores posibles: toba | proyecto
	 * @param string $a valores posibles: proyecto | personalizacion
	 */
	static function revincular_componentes(toba_modelo_proyecto $proyecto, $de = 'toba', $a = 'proyecto')
	{
		if (!self::chequear_combinaciones($de, $a)) {
			throw new toba_error("No se puede revincular de $de a $a");
		}

		if ($de == 'toba' && $a == 'personalizacion') {
			self::revincular_componentes($proyecto, $de, 'proyecto');
			self::revincular_componentes($proyecto, 'proyecto', $a);
			return;
		}

		$id_proyecto = $proyecto->get_id();

		$clases_de	= array();
		$clases_a	= array();
		self::get_clases($proyecto, $de, $a, $clases_de, $clases_a);
		
		$editor = new toba_editor_archivos();

		foreach ($clases_de as $key => $clase) {
			$clase_nueva = $clases_a[$key];
			$texto_buscado = "|(?:[\t\r\n ]+extends[\t\r\n ]+$clase)|i";
			$texto_reemplazo = " extends $clase_nueva";
			$editor->agregar_sustitucion($texto_buscado, $texto_reemplazo);
		}

		$path = $proyecto->get_dir().'/php';
		$dirs_excluidos = array(
			$path.'/extension_toba/componentes'
		);
		$archivos = toba_manejador_archivos::get_archivos_directorio($path, '|.php|', true, $dirs_excluidos);
		$editor->procesar_archivos($archivos);
	}

	private static function chequear_combinaciones($de, $a)
	{
		return 
			($de == 'toba'		&& $a == 'proyecto')		||
			($de == 'toba'		&& $a == 'personalizacion')	||
			($de == 'proyecto'	&& $a == 'personalizacion');
	}

	static function get_clases_componentes_toba(toba_modelo_proyecto $proyecto)
	{
		$db = $proyecto->get_db();
		$sql = "SELECT clase FROM apex_clase WHERE clase_tipo <> 10";
		$res = $db->consultar($sql);
		
		foreach (array_keys($res) as $key) {
			$res[$key] = $res[$key]['clase'];
		}
		
		return $res;
	}

	static private function get_clases(toba_modelo_proyecto $proyecto, $de, $a, &$clases_de, &$clases_a)
	{
		$comp_de_toba = self::get_componentes_toba($proyecto);
		
		if ($de == 'toba') {
			$clases_de = self::get_clases_componentes_toba($proyecto);
		} else {
			$clases_de = $proyecto->get_clases_componentes_proyecto();
		}

		if ($a == 'proyecto') {
			$clases_a = $proyecto->get_clases_componentes_proyecto();
		} else {
			$clases_a = $proyecto->get_clases_componentes_personalizacion();
		}
	}

	/**
	 * Marca las clases como extendidas del tipo $tipo
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $tipo valores posibles: toba | proyecto
	 */
	static function marcar_clases_extendidas(toba_modelo_proyecto $proyecto, $tipo = 'toba')
	{
		$db	= $proyecto->get_db();
		$id_proyecto = $db->quote($proyecto->get_id());
		$set = ($tipo == 'toba') ? 'extension_toba=true' : 'extension_proyecto=true';
		$sql = "UPDATE apex_proyecto SET $set WHERE proyecto=$id_proyecto";
		$db->ejecutar($sql);
	}

	static function crear_arbol_personalizacion($path_proyecto)
	{
		$path_pers =  $path_proyecto.'/'.toba_personalizacion::dir_personalizacion;
		$path_logs	= $path_pers.'/'.toba_personalizacion::dir_logs;
		$path_php	= $path_pers.'/'.toba_personalizacion::dir_php;
		$path_www	= $path_pers.'/'.toba_personalizacion::dir_www;
		$path_img	= $path_pers.'/'.toba_personalizacion::dir_www.'/img';
		$path_css	= $path_pers.'/'.toba_personalizacion::dir_www.'/css';
    	$path_ext	= $path_pers.'/php/extension_toba/componentes';
		$archivo_css	= $path_pers.'/'.toba_personalizacion::dir_www.'/css/toba.css';
        $archivo_ini	= $path_pers.'/'.toba_personalizacion::archivo_ini;

		toba_manejador_archivos::crear_arbol_directorios($path_pers);
		toba_manejador_archivos::crear_arbol_directorios($path_logs);
		toba_manejador_archivos::crear_arbol_directorios($path_php);
		toba_manejador_archivos::crear_arbol_directorios($path_www);
		toba_manejador_archivos::crear_arbol_directorios($path_img);
		toba_manejador_archivos::crear_arbol_directorios($path_css);
		toba_manejador_archivos::crear_arbol_directorios($path_ext);

        toba_manejador_archivos::crear_archivo_con_datos($archivo_ini, toba_personalizacion::iniciada.' = no');
        toba_manejador_archivos::crear_archivo_con_datos($archivo_css, "/*\nIncluir aquí las reglas css personalizadas del proyecto\n*/");
	}

	static function extender_clases(toba_modelo_proyecto $proyecto, $consola, $de)
	{
		$seguir = true;

		if ($proyecto->tiene_clases_extendidas($de)) {
			$mensaje  = "Las clases ya están extendidas. Si las reextiende se ";
			$mensaje .= "perderán todos los cambios que se introdujeron en las ";
			$mensaje .= "mismas. Desea reextender de cualquier manera?";
			$seguir = $consola->dialogo_simple($mensaje);
		}

		if ($seguir) {
			$prefijo = ($de == 'proyecto') ? '/personalizacion' : '';
			$path = $proyecto->get_dir().$prefijo.'/php/extension_toba/componentes';
			util_modelo_proyecto::extender_componentes($proyecto, $path, $de);
			util_modelo_proyecto::marcar_clases_extendidas($proyecto, $de);
		}
		
		return $seguir;
	}
}
?>
