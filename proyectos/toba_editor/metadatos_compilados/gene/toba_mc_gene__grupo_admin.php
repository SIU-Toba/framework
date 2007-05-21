<?php

class toba_mc_gene__grupo_admin
{
	static function get_items_menu()
	{
		return array (
  'toba_editor-3276' => 
  array (
    'padre' => '/admin/usuarios',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '3276',
    'nombre' => 'Derechos Globales',
    'orden' => '55',
    'imagen' => NULL,
    'imagen_recurso_origen' => NULL,
  ),
  'toba_editor-/admin/datos/fuente' => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/datos/fuente',
    'nombre' => 'Fuente de Datos - Editor',
    'orden' => '0',
    'imagen' => 'fuente.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  'toba_editor-/admin/proyectos/propiedades' => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/proyectos/propiedades',
    'nombre' => 'Proyecto - Parámetros Basicos',
    'orden' => '0',
    'imagen' => 'nucleo/proyecto.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  'toba_editor-3287' => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '3287',
    'nombre' => 'Param. Previsualizacion',
    'orden' => '1',
    'imagen' => 'config_previsualizacion.gif',
    'imagen_recurso_origen' => 'proyecto',
  ),
  'toba_editor-/admin/apex/elementos/pagina_tipo' => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/pagina_tipo',
    'nombre' => 'Tipo de PAGINA',
    'orden' => '10',
    'imagen' => 'tipo_pagina.gif',
    'imagen_recurso_origen' => 'proyecto',
  ),
  'toba_editor-/admin/apex/elementos/zona' => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/zona',
    'nombre' => 'ZONA',
    'orden' => '10',
    'imagen' => 'zona.gif',
    'imagen_recurso_origen' => 'proyecto',
  ),
  'toba_editor-/admin/apex/elementos/error' => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/error',
    'nombre' => 'MENSAJES',
    'orden' => '12',
    'imagen' => 'mensaje.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  'toba_editor-1000020' => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '1000020',
    'nombre' => 'Elementos de Formulario (efs)',
    'orden' => NULL,
    'imagen' => 'objetos/abms_ef.gif',
    'imagen_recurso_origen' => 'apex',
  ),
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
  'toba_editor-/admin/menu_principal' => 
  array (
    'padre' => '__raiz__',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/menu_principal',
    'nombre' => 'Menu',
    'orden' => '2',
    'imagen' => NULL,
    'imagen_recurso_origen' => NULL,
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
  'toba_editor-/inicio' => 
  array (
    'padre' => '__raiz__',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/inicio',
    'nombre' => 'Inicio',
    'orden' => '40',
    'imagen' => NULL,
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_items_accesibles()
	{
		return array (
  'toba_editor-10000019' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '10000019',
  ),
  'toba_editor-1000003' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000003',
  ),
  'toba_editor-1000020' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000020',
  ),
  'toba_editor-1000021' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000021',
  ),
  'toba_editor-1000043' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000043',
  ),
  'toba_editor-1000045' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000045',
  ),
  'toba_editor-1000058' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000058',
  ),
  'toba_editor-1000104' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000104',
  ),
  'toba_editor-1240' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1240',
  ),
  'toba_editor-1241' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1241',
  ),
  'toba_editor-1242' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1242',
  ),
  'toba_editor-2045' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '2045',
  ),
  'toba_editor-2447' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '2447',
  ),
  'toba_editor-2865' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '2865',
  ),
  'toba_editor-3276' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3276',
  ),
  'toba_editor-3278' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3278',
  ),
  'toba_editor-3280' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3280',
  ),
  'toba_editor-3287' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3287',
  ),
  'toba_editor-3288' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3288',
  ),
  'toba_editor-3316' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3316',
  ),
  'toba_editor-3357' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3357',
  ),
  'toba_editor-3359' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3359',
  ),
  'toba_editor-/admin/acceso' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/acceso',
  ),
  'toba_editor-/admin/apex/elementos/ef' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/ef',
  ),
  'toba_editor-/admin/apex/elementos/error' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/error',
  ),
  'toba_editor-/admin/apex/elementos/observaciones_solicitud' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/observaciones_solicitud',
  ),
  'toba_editor-/admin/apex/elementos/pagina_tipo' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/pagina_tipo',
  ),
  'toba_editor-/admin/apex/elementos/zona' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/zona',
  ),
  'toba_editor-/admin/datos/fuente' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/datos/fuente',
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
  'toba_editor-/admin/menu_principal' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/menu_principal',
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
  'toba_editor-/admin/objetos/mensajes' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/mensajes',
  ),
  'toba_editor-/admin/objetos/php' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/php',
  ),
  'toba_editor-/admin/objetos_toba/crear' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/crear',
  ),
  'toba_editor-/admin/objetos_toba/editores/ci' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ci',
  ),
  'toba_editor-/admin/objetos_toba/editores/db_registros' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/db_registros',
  ),
  'toba_editor-/admin/objetos_toba/editores/db_tablas' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/db_tablas',
  ),
  'toba_editor-/admin/objetos_toba/editores/ei_archivos' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_archivos',
  ),
  'toba_editor-/admin/objetos_toba/editores/ei_cuadro' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_cuadro',
  ),
  'toba_editor-/admin/objetos_toba/editores/ei_filtro' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_filtro',
  ),
  'toba_editor-/admin/objetos_toba/editores/ei_formulario' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_formulario',
  ),
  'toba_editor-/admin/objetos_toba/editores/ei_formulario_ml' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_formulario_ml',
  ),
  'toba_editor-/admin/objetos_toba/selector_archivo' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/selector_archivo',
  ),
  'toba_editor-/admin/proyectos/organizador' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/proyectos/organizador',
  ),
  'toba_editor-/admin/proyectos/propiedades' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/proyectos/propiedades',
  ),
  'toba_editor-/admin/usuarios/grupo' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/usuarios/grupo',
  ),
  'toba_editor-/basicos/cronometro' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/basicos/cronometro',
  ),
  'toba_editor-/inicio' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/inicio',
  ),
  'toba_editor-/pruebas/testing_automatico_consola' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/pruebas/testing_automatico_consola',
  ),
  'toba_editor-/pruebas/testing_automatico_web' => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/pruebas/testing_automatico_web',
  ),
);
	}

	static function get_lista_permisos()
	{
		return array (
  'prueba1-' => 
  array (
    'nombre' => 'prueba1',
  ),
  'prueba10-' => 
  array (
    'nombre' => 'prueba10',
  ),
  'prueba11-' => 
  array (
    'nombre' => 'prueba11',
  ),
  'prueba2-' => 
  array (
    'nombre' => 'prueba2',
  ),
  'prueba3-' => 
  array (
    'nombre' => 'prueba3',
  ),
  'prueba4-' => 
  array (
    'nombre' => 'prueba4',
  ),
  'prueba54-' => 
  array (
    'nombre' => 'prueba54',
  ),
  'prueba6-' => 
  array (
    'nombre' => 'prueba6',
  ),
  'prueba7-' => 
  array (
    'nombre' => 'prueba7',
  ),
  'prueba84-' => 
  array (
    'nombre' => 'prueba84',
  ),
  'prueba9-' => 
  array (
    'nombre' => 'prueba9',
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
  'toba_editor-/admin/objetos/mensajes' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/objetos/mensajes',
    'orden' => '0',
    'imagen' => 'mensaje.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Propiedades - Mensajes',
    'descripcion' => 'Mensajes asociados al componente. Forma parte del esquema de [wiki:Referencia/Mensajes Mensajes y Notificaciones]',
  ),
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
  'toba_editor-1000104' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '1000104',
    'orden' => '3',
    'imagen' => 'ordenar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Ordenar Items',
    'descripcion' => NULL,
  ),
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
  'toba_editor-/admin/datos/fuente' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/datos/fuente',
    'orden' => '4',
    'imagen' => 'fuente.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Fuente de Datos - Editor',
    'descripcion' => 'Las [wiki:Referencia/FuenteDatos fuentes de datos] permiten conectar componentes y código propio a distintas bases de datos.',
  ),
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
  'toba_editor-/admin/usuarios/grupo' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/usuarios/grupo',
    'orden' => '1',
    'imagen' => 'objetos/editar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Grupo de Acceso - Propiedades',
    'descripcion' => NULL,
  ),
  'toba_editor-3288' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '3288',
    'orden' => '2',
    'imagen' => 'usuarios/grupo.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Grupo de Acceso - Items',
    'descripcion' => NULL,
  ),
  'toba_editor-3278' => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '3278',
    'orden' => '10',
    'imagen' => 'usuarios/permisos.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Grupo de Acceso - Asignar Derechos Globales',
    'descripcion' => 'Edición de los permisos globales habilitados para este grupo',
  ),
);
	}

}

?>