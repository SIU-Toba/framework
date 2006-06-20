<?

class php_1519
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1519',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'form_prop_basicas',
    'subclase_archivo' => 'admin/editores/editor_item/form_prop_basicas.php',
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'ITEM - Propiedades Básicas',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'Propiedades del ITEM',
    'fuente_proyecto' => 'toba',
    'fuente' => 'instancia',
    'solicitud_registrar' => NULL,
    'solicitud_obj_obs_tipo' => NULL,
    'solicitud_obj_observacion' => NULL,
    'parametro_a' => NULL,
    'parametro_b' => NULL,
    'parametro_c' => NULL,
    'parametro_d' => NULL,
    'parametro_e' => NULL,
    'parametro_f' => NULL,
    'usuario' => NULL,
    'creacion' => NULL,
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_formulario',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_formulario',
    'clase_icono' => 'objetos/ut_formulario.gif',
    'clase_descripcion_corta' => 'Formulario',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '1842',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'modificacion',
      'etiqueta' => 'Modificacion',
      'maneja_datos' => '1',
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '0',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => '1',
      'grupo' => NULL,
    ),
  ),
  'info_formulario' => 
  array (
    'auto_reset' => NULL,
    'ancho' => NULL,
    'ancho_etiqueta' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'proyecto',
      'columnas' => 'proyecto',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_oculto_proyecto',
      'inicializacion' => NULL,
      'etiqueta' => NULL,
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'item',
      'columnas' => 'item',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_fijo',
      'inicializacion' => 'tamano: 60;',
      'etiqueta' => 'Identificador',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'padre',
      'columnas' => 'padre_proyecto, padre',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_carpetas_posibles;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: proyecto,id;
valor: nombre;',
      'etiqueta' => 'Carpeta Padre',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'nombre',
      'columnas' => 'nombre',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 60;',
      'etiqueta' => 'Nombre',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'solicitud_tipo',
      'columnas' => 'solicitud_tipo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'predeterminado: web;
sql: SELECT solicitud_tipo, descripcion_corta 
FROM apex_solicitud_tipo 
WHERE solicitud_tipo <> \'fantasma\'
ORDER BY 1;',
      'etiqueta' => 'Tipo de solicitud',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'nivel_acceso',
      'columnas' => 'nivel_acceso',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_oculto',
      'inicializacion' => 'estado: 0;',
      'etiqueta' => NULL,
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '6',
      'colapsado' => NULL,
    ),
    6 => 
    array (
      'identificador' => 'pagina_tipo',
      'columnas' => 'pagina_tipo_proyecto, pagina_tipo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db_proyecto',
      'inicializacion' => 'predeterminado: toba,normal;
sql: SELECT proyecto, pagina_tipo, descripcion FROM apex_pagina_tipo %w% ORDER BY 3;
columna_proyecto: proyecto;
incluir_toba: 1;',
      'etiqueta' => 'Modelo Pagina',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Modelo para manejar templates de HTML (include de PHP que se aplica antes y despues de la ACTIVIDAD)',
      'orden' => '7',
      'colapsado' => NULL,
    ),
    7 => 
    array (
      'identificador' => 'descripcion',
      'columnas' => 'descripcion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 4;
columnas: 55;',
      'etiqueta' => 'Descripcion',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '8',
      'colapsado' => NULL,
    ),
    8 => 
    array (
      'identificador' => 'comportamiento',
      'columnas' => 'comportamiento',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_lista_c',
      'inicializacion' => 'lista: patron,Predefinido/accion,Script PHP en sistema de archivos/buffer,Script PHP en fuente de datos;',
      'etiqueta' => 'Tipo Comportamiento',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'El comportamiento determina la ejecución principal de la operación. Generalmente instancia los objetos involucrados y les asigna un rol específico.',
      'orden' => '9',
      'colapsado' => NULL,
    ),
    9 => 
    array (
      'identificador' => 'buffer',
      'columnas' => 'actividad_buffer_proyecto, actividad_buffer',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db_proyecto',
      'inicializacion' => 'sql: SELECT proyecto, buffer, descripcion_corta FROM apex_buffer WHERE buffer !=0 %w% ORDER BY 2;
columna_proyecto: proyecto;
incluir_toba: 1;',
      'etiqueta' => 'Script PHP',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'El comportamiento se encuentra almacenado en la base y no en el sistema de archivos.',
      'orden' => '10',
      'colapsado' => NULL,
    ),
    10 => 
    array (
      'identificador' => 'patron',
      'columnas' => 'actividad_patron_proyecto, actividad_patron',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db_proyecto',
      'inicializacion' => 'predeterminado: toba,CI;
sql: SELECT proyecto, patron, descripcion_corta FROM apex_patron 
WHERE 
 patron != \'especifico\' %w%
 ORDER BY 3;
columna_proyecto: proyecto;
incluir_toba: 1;',
      'etiqueta' => 'Comportamiento',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'También llamado Patrones, estos comportamientos son los predefinidos en el sistema, tomando desiciones de qué hacer con los objetos involucrados.',
      'orden' => '11',
      'colapsado' => NULL,
    ),
    11 => 
    array (
      'identificador' => 'accion',
      'columnas' => 'actividad_accion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 60;
maximo: 80;',
      'etiqueta' => 'Archivo PHP',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '12',
      'colapsado' => NULL,
    ),
    12 => 
    array (
      'identificador' => 'parametro_a',
      'columnas' => 'parametro_a',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 100;',
      'etiqueta' => 'PATRON (param A)',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '13',
      'colapsado' => '1',
    ),
    13 => 
    array (
      'identificador' => 'parametro_b',
      'columnas' => 'parametro_b',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 100;',
      'etiqueta' => 'PATRON (param B)',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '14',
      'colapsado' => '1',
    ),
    14 => 
    array (
      'identificador' => 'parametro_c',
      'columnas' => 'parametro_c',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 100;',
      'etiqueta' => 'PATRON (param C)',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '15',
      'colapsado' => '1',
    ),
    15 => 
    array (
      'identificador' => 'menu',
      'columnas' => 'menu',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'Mostrar en el menú',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '16',
      'colapsado' => NULL,
    ),
    16 => 
    array (
      'identificador' => 'orden',
      'columnas' => 'orden',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'tamano: 2;',
      'etiqueta' => 'Orden en el menú',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '17',
      'colapsado' => NULL,
    ),
    17 => 
    array (
      'identificador' => 'imagen_recurso_orige',
      'columnas' => 'imagen_recurso_origen',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'no_seteado: Ninguno;
sql: SELECT recurso_origen, descripcion FROM apex_recurso_origen ORDER BY descripcion;',
      'etiqueta' => 'Imagen - origen',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Procedencia de la imagen',
      'orden' => '18',
      'colapsado' => '1',
    ),
    18 => 
    array (
      'identificador' => 'imagen',
      'columnas' => 'imagen',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 60;',
      'etiqueta' => 'Imagen',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Imagen que representa al item',
      'orden' => '19',
      'colapsado' => '1',
    ),
    19 => 
    array (
      'identificador' => 'zona',
      'columnas' => 'zona_proyecto, zona',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db_proyecto',
      'inicializacion' => 'no_seteado: Ninguna;
sql: SELECT proyecto, zona, nombre
FROM apex_item_zona %w%
ORDER BY descripcion;
columna_proyecto: proyecto;
incluir_toba: 0;',
      'etiqueta' => 'ZONA',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Zona de la que el ITEM forma parte',
      'orden' => '20',
      'colapsado' => NULL,
    ),
    20 => 
    array (
      'identificador' => 'zona_listar',
      'columnas' => 'zona_listar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'Zona - listar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Listar el ITEM como vecino de la ZONA?',
      'orden' => '21',
      'colapsado' => NULL,
    ),
    21 => 
    array (
      'identificador' => 'zona_orden',
      'columnas' => 'zona_orden',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => '',
      'etiqueta' => 'ZONA - Orden',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Orden que ocupa el item en la zona',
      'orden' => '22',
      'colapsado' => NULL,
    ),
    22 => 
    array (
      'identificador' => 'solicitud_registrar',
      'columnas' => 'solicitud_registrar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'ACCESO - Registrar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Registrar el acceso a este ITEM',
      'orden' => '23',
      'colapsado' => '1',
    ),
    23 => 
    array (
      'identificador' => 'solicitud_obs_tipo',
      'columnas' => 'solicitud_obs_tipo_proyecto, solicitud_obs_tipo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db_proyecto',
      'inicializacion' => 'no_seteado: NO clasificar;
sql: SELECT proyecto, solicitud_obs_tipo, 
descripcion 
FROM apex_solicitud_obs_tipo 
WHERE (criterio = \'item\' OR criterio=\'sistema\')%w%;
columna_proyecto: proyecto;
incluir_toba: 1;',
      'etiqueta' => 'ACCESO - Clasificar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Categorizacion pensada para navegar facilmente los LOGS.',
      'orden' => '24',
      'colapsado' => '1',
    ),
    24 => 
    array (
      'identificador' => 'solicitud_observacio',
      'columnas' => 'solicitud_observacion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 4;
columnas: 55;',
      'etiqueta' => 'ACC. - Obs.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Observacion sobre el acceso a este item.',
      'orden' => '25',
      'colapsado' => '1',
    ),
    25 => 
    array (
      'identificador' => 'solicitud_registrar_',
      'columnas' => 'solicitud_registrar_cron',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'ACC - Cronom.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Cronometrar el acceso al ITEM',
      'orden' => '26',
      'colapsado' => '1',
    ),
    26 => 
    array (
      'identificador' => 'publico',
      'columnas' => 'publico',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'Publico',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'El ITEM puede ser accedido por cualquier USUARIO, sin considerar el GRUPO de ACCESO al que pertenece.',
      'orden' => '27',
      'colapsado' => '1',
    ),
    27 => 
    array (
      'identificador' => 'carpeta',
      'columnas' => 'carpeta',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_oculto',
      'inicializacion' => 'estado: 0',
      'etiqueta' => 'carpeta',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '28',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>