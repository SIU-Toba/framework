<?php

class toba_mc_gene__grupo_usuario
{
	static function get_items_menu()
	{
		return array (
  0 => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/catalogo_unificado',
    'nombre' => 'Catálogo',
    'imagen' => 'objetos/arbol.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/editor_items',
    'nombre' => 'Editor de Items',
    'imagen' => 'objetos/editar.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
    'nombre' => 'CARPETA - Editor',
    'imagen' => 'nucleo/carpeta.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  3 => 
  array (
    'padre' => '__raiz__',
    'carpeta' => 1,
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba',
    'nombre' => 'Componentes',
    'imagen' => NULL,
    'imagen_recurso_origen' => 'proyecto',
  ),
);
	}

	static function get_items_accesibles()
	{
		return array (
  0 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000021',
  ),
  1 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000045',
  ),
  2 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1240',
  ),
  3 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3280',
  ),
  4 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
  ),
  5 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/catalogo_unificado',
  ),
  6 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/editor_items',
  ),
  7 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/clonador',
  ),
  8 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/editores/editor_estilos',
  ),
  9 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/selector_archivo',
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
  0 => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '1000021',
    'orden' => '2',
    'imagen' => 'objetos/clonar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Clonador de Items',
    'descripcion' => NULL,
  ),
  1 => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/items/editor_items',
    'orden' => NULL,
    'imagen' => 'objetos/editar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Editor de Items',
    'descripcion' => 'Un [wiki:Referencia/Item ítem] es la definición de una operación.',
  ),
);
	}

	static function get_items_zona__zona_objeto()
	{
		return array (
  0 => 
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
  0 => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
    'orden' => '4',
    'imagen' => 'nucleo/carpeta.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'CARPETA - Editor',
    'descripcion' => 'Propiedades de la Carpera',
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

}

?>