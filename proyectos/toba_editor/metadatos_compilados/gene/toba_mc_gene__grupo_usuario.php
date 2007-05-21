<?php

class toba_mc_gene__grupo_usuario
{
	static function get_items_menu()
	{
		return array (
  'toba_editor-/admin/items/catalogo_unificado' => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/catalogo_unificado',
    'nombre' => 'Catálogo',
    'orden' => '5',
    'imagen' => 'objetos/arbol.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  'toba_editor-/admin/items/editor_items' => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/editor_items',
    'nombre' => 'Editor de Items',
    'orden' => '6',
    'imagen' => 'objetos/editar.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  'toba_editor-/admin/items/carpeta_propiedades' => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
    'nombre' => 'CARPETA - Editor',
    'orden' => '7',
    'imagen' => 'nucleo/carpeta.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  'toba_editor-/admin/objetos_toba' => 
  array (
    'padre' => '__raiz__',
    'carpeta' => 1,
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba',
    'nombre' => 'Componentes',
    'orden' => '2',
    'imagen' => NULL,
    'imagen_recurso_origen' => 'proyecto',
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
  'toba_editor-1000045' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000045',
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
  'toba_editor-/admin/items/carpeta_propiedades' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
  ),
  'toba_editor-/admin/items/catalogo_unificado' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/catalogo_unificado',
  ),
  'toba_editor-/admin/items/editor_items' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/editor_items',
  ),
  'toba_editor-/admin/objetos/clonador' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/clonador',
  ),
  'toba_editor-/admin/objetos/editores/editor_estilos' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/editores/editor_estilos',
  ),
  'toba_editor-/admin/objetos_toba/selector_archivo' => 
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
  'toba_editor-/admin/items/editor_items' => 
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
  'toba_editor-/admin/items/carpeta_propiedades' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
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

}

?>