<?php

class toba_mc_gene__grupo_externo
{
	static function get_items_menu()
	{
		return array (
);
	}

	static function get_items_accesibles()
	{
		return array (
  'toba_editor-3280' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3280',
  ),
  'toba_editor-/admin/objetos/clonador' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/clonador',
  ),
);
	}

	static function get_lista_permisos()
	{
		return array (
);
	}

	static function get_items_zona__zona_item()
	{
		return array (
);
	}

	static function get_items_zona__zona_objeto()
	{
		return array (
  'toba_editor-/admin/objetos/clonador' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/objetos/clonador',
    'orden' => '29',
    'imagen' => 'objetos/clonar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Clonar Componente',
    'descripcion' => 'Clonar el componente seleccionado',
  ),
);
	}

	static function get_items_zona__zona_carpeta()
	{
		return array (
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

}

?>