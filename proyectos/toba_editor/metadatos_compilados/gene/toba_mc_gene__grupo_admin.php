<?php

class toba_mc_gene__grupo_admin
{
	static function get_items_menu()
	{
		return array (
  0 => 
  array (
    'padre' => '/admin/usuarios',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '3276',
    'nombre' => 'Derechos Globales',
    'imagen' => NULL,
    'imagen_recurso_origen' => NULL,
  ),
  1 => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/datos/fuente',
    'nombre' => 'Fuente de Datos - Editor',
    'imagen' => 'fuente.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/proyectos/propiedades',
    'nombre' => 'Proyecto - Parámetros Basicos',
    'imagen' => 'nucleo/proyecto.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  3 => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '3287',
    'nombre' => 'Param. Previsualizacion',
    'imagen' => 'config_previsualizacion.gif',
    'imagen_recurso_origen' => 'proyecto',
  ),
  4 => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/pagina_tipo',
    'nombre' => 'Tipo de PAGINA',
    'imagen' => 'tipo_pagina.gif',
    'imagen_recurso_origen' => 'proyecto',
  ),
  5 => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/zona',
    'nombre' => 'ZONA',
    'imagen' => 'zona.gif',
    'imagen_recurso_origen' => 'proyecto',
  ),
  6 => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/error',
    'nombre' => 'MENSAJES',
    'imagen' => 'mensaje.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  7 => 
  array (
    'padre' => '/configuracion',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '1000020',
    'nombre' => 'Elementos de Formulario (efs)',
    'imagen' => 'objetos/abms_ef.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  8 => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/catalogo_unificado',
    'nombre' => 'Catálogo',
    'imagen' => 'objetos/arbol.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  9 => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/editor_items',
    'nombre' => 'Editor de Items',
    'imagen' => 'objetos/editar.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  10 => 
  array (
    'padre' => '/items',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
    'nombre' => 'CARPETA - Editor',
    'imagen' => 'nucleo/carpeta.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  11 => 
  array (
    'padre' => '__raiz__',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/admin/menu_principal',
    'nombre' => 'Menu',
    'imagen' => NULL,
    'imagen_recurso_origen' => NULL,
  ),
  12 => 
  array (
    'padre' => '__raiz__',
    'carpeta' => 1,
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba',
    'nombre' => 'Componentes',
    'imagen' => NULL,
    'imagen_recurso_origen' => 'proyecto',
  ),
  13 => 
  array (
    'padre' => '__raiz__',
    'carpeta' => 0,
    'proyecto' => 'toba_editor',
    'item' => '/inicio',
    'nombre' => 'Inicio',
    'imagen' => NULL,
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_items_accesibles()
	{
		return array (
  0 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/acceso',
  ),
  1 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/ef',
  ),
  2 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/error',
  ),
  3 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/observaciones_solicitud',
  ),
  4 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/pagina_tipo',
  ),
  5 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/apex/elementos/zona',
  ),
  6 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/datos/fuente',
  ),
  7 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/carpeta_propiedades',
  ),
  8 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/catalogo_unificado',
  ),
  9 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/items/editor_items',
  ),
  10 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/menu_principal',
  ),
  11 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/clonador',
  ),
  12 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/editores/editor_estilos',
  ),
  13 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/mensajes',
  ),
  14 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos/php',
  ),
  15 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/crear',
  ),
  16 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ci',
  ),
  17 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/db_registros',
  ),
  18 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/db_tablas',
  ),
  19 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_archivos',
  ),
  20 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_cuadro',
  ),
  21 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_filtro',
  ),
  22 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_formulario',
  ),
  23 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/editores/ei_formulario_ml',
  ),
  24 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/objetos_toba/selector_archivo',
  ),
  25 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/proyectos/organizador',
  ),
  26 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/proyectos/propiedades',
  ),
  27 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/admin/usuarios/grupo',
  ),
  28 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/basicos/cronometro',
  ),
  29 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/inicio',
  ),
  30 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/pruebas/testing_automatico_consola',
  ),
  31 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '/pruebas/testing_automatico_web',
  ),
  32 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1240',
  ),
  33 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1241',
  ),
  34 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1242',
  ),
  35 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '2045',
  ),
  36 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '2447',
  ),
  37 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '2865',
  ),
  38 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3276',
  ),
  39 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3278',
  ),
  40 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3280',
  ),
  41 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3287',
  ),
  42 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3288',
  ),
  43 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3316',
  ),
  44 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3357',
  ),
  45 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '3359',
  ),
  46 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000003',
  ),
  47 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000020',
  ),
  48 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000021',
  ),
  49 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000043',
  ),
  50 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000045',
  ),
  51 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '1000058',
  ),
  52 => 
  array (
    'proyecto' => 'toba_editor',
    'item' => '10000019',
  ),
);
	}

	static function get_lista_permisos()
	{
		return array (
  0 => 
  array (
    'nombre' => 'prueba1',
  ),
  1 => 
  array (
    'nombre' => 'prueba2',
  ),
  2 => 
  array (
    'nombre' => 'prueba3',
  ),
  3 => 
  array (
    'nombre' => 'prueba4',
  ),
  4 => 
  array (
    'nombre' => 'prueba54',
  ),
  5 => 
  array (
    'nombre' => 'prueba6',
  ),
  6 => 
  array (
    'nombre' => 'prueba7',
  ),
  7 => 
  array (
    'nombre' => 'prueba84',
  ),
  8 => 
  array (
    'nombre' => 'prueba9',
  ),
  9 => 
  array (
    'nombre' => 'prueba10',
  ),
  10 => 
  array (
    'nombre' => 'prueba11',
  ),
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
    'item' => '/admin/objetos/mensajes',
    'orden' => '0',
    'imagen' => 'mensaje.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Propiedades - Mensajes',
    'descripcion' => 'Mensajes asociados al componente. Forma parte del esquema de [wiki:Referencia/Mensajes Mensajes y Notificaciones]',
  ),
  1 => 
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
  0 => 
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
  0 => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '/admin/usuarios/grupo',
    'orden' => '1',
    'imagen' => 'objetos/editar.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Grupo de Acceso - Propiedades',
    'descripcion' => NULL,
  ),
  1 => 
  array (
    'item_proyecto' => 'toba_editor',
    'item' => '3288',
    'orden' => '2',
    'imagen' => 'usuarios/grupo.gif',
    'imagen_origen' => 'apex',
    'nombre' => 'Grupo de Acceso - Items',
    'descripcion' => NULL,
  ),
  2 => 
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