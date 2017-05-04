<?php

class toba_mc_gene__grupo_usuario
{
	static function get_items_menu()
	{
		return array (
  'toba_editor-1000239' => 
  array (
    'padre' => '1000266',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '1000239',
    'nombre' => 'Catálogo',
    'orden' => '5',
    'imagen' => 'objetos/arbol.gif',
    'imagen_recurso_origen' => 'apex',
    'es_primer_nivel' => false,
  ),
  'toba_editor-1000240' => 
  array (
    'padre' => '1000266',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '1000240',
    'nombre' => 'Editor de Operaciones',
    'orden' => '6',
    'imagen' => 'objetos/editar.gif',
    'imagen_recurso_origen' => 'apex',
    'es_primer_nivel' => false,
  ),
  'toba_editor-1000238' => 
  array (
    'padre' => '1000266',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '1000238',
    'nombre' => 'CARPETA - Editor',
    'orden' => '7',
    'imagen' => 'nucleo/carpeta.gif',
    'imagen_recurso_origen' => 'apex',
    'es_primer_nivel' => false,
  ),
  'toba_editor-1000246' => 
  array (
    'padre' => '1000271',
    'carpeta' => 1,
    'proyecto' => 'toba_editor',
    'item' => '1000246',
    'nombre' => 'Componentes',
    'orden' => '2',
    'imagen' => NULL,
    'imagen_recurso_origen' => 'proyecto',
    'es_primer_nivel' => true,
  ),
);
	}

	static function get_items_accesibles()
	{
		return array (
  'toba_editor-1000021' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000021',
  ),
  'toba_editor-1000042' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000042',
  ),
  'toba_editor-1000045' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000045',
  ),
  'toba_editor-1000238' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000238',
  ),
  'toba_editor-1000239' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000239',
  ),
  'toba_editor-1000240' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000240',
  ),
  'toba_editor-1000242' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000242',
  ),
  'toba_editor-1000243' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000243',
  ),
  'toba_editor-1000257' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000257',
  ),
  'toba_editor-1000269' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000269',
  ),
  'toba_editor-1240' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1240',
  ),
  'toba_editor-3280' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3280',
  ),
  'toba_editor-33000019' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '33000019',
  ),
  'toba_editor-33000040' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '33000040',
  ),
  'toba_editor-3359' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3359',
  ),
);
	}

	static function get_lista_permisos()
	{
		return array (
);
	}

	static function get_items_zona__zona_consulta_php()
	{
		return array (
);
	}

	static function get_items_zona__zona_dimension()
	{
		return array (
);
	}

	static function get_items_zona__zona_objeto()
	{
		return array (
  'toba_editor-1000242' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '1000242',
    'orden' => '29',
    'imagen' => 'objetos/clonar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Clonar Componente',
    'descripcion' => 'Clonar el componente seleccionado',
  ),
);
	}

	static function get_items_zona__zona_item()
	{
		return array (
  'toba_editor-1000021' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '1000021',
    'orden' => '2',
    'imagen' => 'objetos/clonar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Clonador de Items',
    'descripcion' => NULL,
  ),
  'toba_editor-1000240' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '1000240',
    'orden' => NULL,
    'imagen' => 'objetos/editar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Editor de Operaciones',
    'descripcion' => 'Una [wiki:Referencia/Operacion operación] es la unidad accesible por el usuario.',
  ),
);
	}

	static function get_items_zona__zona_carpeta()
	{
		return array (
  'toba_editor-1000238' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '1000238',
    'orden' => '4',
    'imagen' => 'nucleo/carpeta.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'CARPETA - Editor',
    'descripcion' => NULL,
  ),
);
	}

	static function get_items_zona__zona_fuente()
	{
		return array (
);
	}

	static function get_items_zona__zona_usuario()
	{
		return array (
);
	}

	static function get_items_zona__zona_grupo_acceso()
	{
		return array (
);
	}

	static function get_items_zona__zona_servicio_web()
	{
		return array (
);
	}

	static function get_membresia()
	{
		return array (
);
	}

}

?>