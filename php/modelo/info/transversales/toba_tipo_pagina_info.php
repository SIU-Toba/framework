<?php
class toba_tipo_pagina_info extends toba_elemento_transversal_info
{
	function ini()
	{
		$proyecto = quote($this->_id['proyecto']);
		$tipo_pagina = quote($this->_id['id']);
		$sql = "SELECT clase_nombre,
						clase_archivo, punto_montaje
					FROM apex_pagina_tipo
					WHERE	proyecto = $proyecto
					AND pagina_tipo = $tipo_pagina;";

		$this->_datos['_info'] = toba::db()->consultar_fila($sql);
		toba::logger()->debug($sql);
	}

	function set_subclase($nombre, $archivo, $pm)
	{
		$proyecto = quote($this->_id['proyecto']);
		$tipo_pagina = quote($this->_id['id']);
		$nombre = quote($nombre);
		$archivo = quote($archivo);
		$pm = quote($pm);
		
		$sql = "UPDATE  apex_pagina_tipo
					SET
							clase_nombre = $nombre, 
							clase_archivo = $archivo,
							punto_montaje = $pm
					WHERE	proyecto = $proyecto
					AND pagina_tipo = $tipo_pagina;";

		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}
	
	//-----------------------------------------------------------------------------------
	function get_nombre_instancia_abreviado()
	{
		return $this->_tipo_elemento;
	}
	
	//-----------------------------------------------------------------------------------	
	function get_clase_nombre()
	{
		return 'toba_tp_basico';
	}

	function get_clase_archivo()
	{
		return 'nucleo/tipo_pagina/toba_tp_basico.php';
	}

	function get_subclase_nombre()
	{
		return $this->_datos['_info']['clase_nombre'];
	}

	function get_subclase_archivo()
	{
		return $this->_datos['_info']['clase_archivo'];
	}
	

	function get_punto_montaje()
	{
		return $this->_datos['_info']['punto_montaje'];
	}	

	function get_molde_vacio()
	{
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );
		return $molde;
	}

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar_bloque($this->get_bloque_encabezado());
		$molde->agregar_bloque($this->get_bloque_cuerpo());
		$molde->agregar_bloque($this->get_bloque_pie());
	 	$molde->agregar_bloque($this->get_bloque_css());

		return $molde;
	}

	function get_bloque_encabezado()
	{
		$bloque = array();

		$doc[] = 'Saca el encabezado de la pagina';
		$doc[] = 'No se cierra el div para hacer lugar para la zona';
		$metodo = new toba_codigo_metodo_php('encabezado', array(), $doc);
		$metodo->set_contenido(	"\$this->cabecera_html();\n".
														"\$this->comienzo_cuerpo();\n".
														"\$this->barra_superior();");
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('pre_contenido', array(), $doc);
		$bloque[] = $metodo;

		$doc = array('Se encarga de sacar toda la información del header HTML');
		$doc[] = 'Plantillas de estilo y consumos JS basicos';
		$metodo = new toba_codigo_metodo_php('cabecera_html', array(), $doc);
		$metodo->set_contenido('parent::cabecera_html();');
		$bloque[] = $metodo;

		$doc = 'Devuelve el titulo de la página';
		$metodo = new toba_codigo_metodo_php('titulo_pagina', array(), array($doc));
		$metodo->set_contenido('return parent::titulo_pagina();');
		$bloque[] = $metodo;

		return $bloque;
	}

	function get_bloque_cuerpo()
	{
		$bloque = array();

		$doc[] = 'Crea el <body> y toba_recursos basicos. ';
		$doc[] = 'Incluye un <div> que se propaga hasta el fin de la zona parte sup. ';
		$metodo = new toba_codigo_metodo_php('comienzo_cuerpo', array(), $doc);
		$metodo->set_contenido('parent::comienzo_cuerpo();');
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('comienzo_cuerpo_basico', array(), $doc);
		$metodo->set_contenido('parent::comienzo_cuerpo_basico();');
		$bloque[] = $metodo;
		
		return $bloque;
	}

	function get_bloque_pie()
	{
		$bloque = array();
		$doc = array();
		$metodo = new toba_codigo_metodo_php('post_contenido', array(), $doc);
		$bloque[] = $metodo;

		$doc = array('Cierra el documento');
		$metodo = new toba_codigo_metodo_php('pie', array(), $doc);
		$metodo->set_contenido('parent::pie();');
		$bloque[] = $metodo;

		return $bloque;		
	}

	function get_bloque_css()
	{
		$bloque = array();
		$doc = array();
		$metodo = new toba_codigo_metodo_php('plantillas_css', array(), $doc);
		$metodo->set_contenido('parent::plantillas_css();');
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('estilos_css', array(), $doc);
		$metodo->set_contenido('parent::estilos_css();');
		$bloque[] = $metodo;

		return $bloque;
	}
}
?>
