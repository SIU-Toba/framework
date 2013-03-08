<?php

class toba_zona_info extends toba_elemento_transversal_info
{
	function ini()
	{
		$proyecto = quote($this->_id['proyecto']);
		$zona = quote($this->_id['id']);
		$sql = "SELECT zona,
					  punto_montaje, 
					  archivo
					FROM apex_item_zona
					WHERE	proyecto = $proyecto
					AND zona = $zona;";

		$this->_datos['_info'] = toba::db()->consultar_fila($sql);
		toba::logger()->debug($sql);
	}
	
	function set_subclase($nombre, $archivo, $pm)
	{
		$db = toba_contexto_info::get_db();
		$nombre = $db->quote($nombre);
		$archivo = $db->quote($archivo);
		$pm = $db->quote($pm);
		$proyecto = $db->quote($this->_id['proyecto']);
		$zona = $db->quote($this->_id['id']);
		$sql = "
			UPDATE apex_item_zona
			SET
				archivo = $archivo,
				punto_montaje = $pm
			WHERE
					proyecto = $proyecto
			AND zona = $zona;";
		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}
	//-----------------------------------------------------------------------------------	
	function get_clase_nombre()
	{
		return 'toba_zona'; 
	}

	function get_clase_archivo()
	{
		return 'nucleo/lib/toba_zona.php';
	}

	function get_punto_montaje()
	{
		return $this->_datos['_info']['punto_montaje'];
	}
	
	function get_subclase_nombre()
	{
		return $this->_datos['_info']['zona'];
	}

	function get_subclase_archivo()
	{
		return $this->_datos['_info']['archivo'];
	}

	function get_molde_vacio()
	{
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );
		return $molde;
	}

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar_bloque($this->get_bloque_inicial());
		$molde->agregar_bloque($this->get_bloque_cuerpo());
		return $molde;
	}

	function get_bloque_inicial()
	{
		$bloque = array();

		$doc = array();
		$metodo = new toba_codigo_metodo_php('cargada', array(), $doc);
		$metodo->set_contenido(	'return (isset($this->editable_id) && ($this->editable_id != \'\'));');
		$bloque[] = $metodo;

		$doc = array('Carga la informacion asociada al id en la zona');
		$metodo = new toba_codigo_metodo_php('cargar', array('$id'), $doc);
		$metodo->set_contenido(	"if (!isset(\$this->editable_info) || !isset(\$this->editable_id) || \$id !== \$this->editable_id) { \n"
			."	toba::logger()->debug(\"Cargando la zona '{\$this->id}' con el editable '\".var_export(\$id, true).\"'\"); \n"
			."	\$this->editable_id = \$id; \n"
			."	\$this->cargar_info();\n}");
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('get_info', array(), $doc);
		$metodo->set_contenido("if (! isset(\$clave)) {\n".
			"	return \$this->editable_info; \n".
			"} else {\n".
			"	return \$this->editable_info[\$clave]; \n}");
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('editable_nombre', array(), $doc);
		$metodo->set_contenido("if (is_scalar(\$this->editable_info)) {\n".
			"	return \$this->editable_info;\n".
			"}\n".
			"\$candidatos = array('nombre', 'descripcion_corta', 'descripcion');\n".
			"foreach (\$candidatos as \$candidato) {\n".
			"	if (isset(\$this->editable_info[\$candidato])) {\n".
			"		return \$this->editable_info[\$candidato];\n".
			"	}\n".
			"}\nreturn '';	");
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('get_editable_id', array(), $doc);
		$metodo->set_contenido("if (is_array(\$this->editable_id)) {\n".
			"	return implode(' - ', \$this->editable_id);\n".
			"} else {\n".
			"	return \$this->editable_id;\n}");
		$bloque[] = $metodo;

		$doc = array('Ventana de configuracion de la zona');
		$metodo = new toba_codigo_metodo_php('conf', array(), $doc);
		$metodo->set_contenido('return parent::conf();');
		$bloque[] = $metodo;

		return $bloque;
	}

	function get_bloque_cuerpo()
	{
		$bloque = array();

		$doc = array();
		$metodo = new toba_codigo_metodo_php('get_items_vecinos', array(), $doc);
		$metodo->set_contenido("\$items = array();\n".
				"foreach(\$this->items_vecinos as \$item) {\n".
				"	\$items[] = array('item_proyecto' => \$item['objeto_proyecto'], 'item' => \$item['item'], 'orden' => \$item['orden']);\n".
				"}\nreturn \$items;");
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('desactivar_items', array('$condiciones'), $doc);
		$metodo->set_contenido("\$items_restantes = \$this->items_vecinos;\n".
				"foreach (\$condiciones as \$condicion){\n".
				"	foreach(\$items_restantes as \$key => \$valor) {\n".
				"		\$coincide = array_intersect_assoc(\$valor, \$condicion);\n".
				"		if (! empty(\$coincide)){\n".
				"			unset(\$items_restantes[\$key]);\n".
				"		}\n".
				"	}\n".
				"}\n\$this->items_vecinos = \$items_restantes;");
		$bloque[] = $metodo;
		
		return $bloque;
	}

	function get_bloque_html()
	{
		$bloque = array();
		$doc = array();
		$metodo = new toba_codigo_metodo_php('generar_html_barra_especifico', array(), $doc);
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('generar_html_barra_inferior', array(), $doc);
		$bloque[] = $metodo;

		return $bloque;		
	}
}
?>
