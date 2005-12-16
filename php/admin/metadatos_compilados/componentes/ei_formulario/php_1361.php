<?
//Generador: compilador_proyecto.php

class php_1361
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1361',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'eiform_cuadro_prop_basicas',
    'subclase_archivo' => 'admin/objetos_toba/ei_cuadro/eiform_cuadro_prop_basicas.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor CUADRO - Prop. basicas',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'Editor del cuadro',
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
      'identificador' => 'titulo',
      'columnas' => 'titulo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano:30',
      'etiqueta' => 'Título',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define el titulo del cuadro',
      'orden' => '1',
      'colapsado' => '1',
    ),
    1 => 
    array (
      'identificador' => 'subtitulo',
      'columnas' => 'subtitulo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano:30',
      'etiqueta' => 'Subtítulo',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Permite ingresar el subtitulo del cuadro',
      'orden' => '2',
      'colapsado' => '1',
    ),
    2 => 
    array (
      'identificador' => 'clave_dbr',
      'columnas' => 'clave_dbr',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'CLAVE datos_tabla',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indica que se utiliza la clave interna de los objeto_datos_tabla.',
      'orden' => '4',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'columnas_clave',
      'columnas' => 'columnas_clave',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 50;
maximo: 150;',
      'etiqueta' => 'Clave',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indica la clave de las filas del cuadro (el valor a utilizar en los eventos que el cuadro dispare).
Hay que escribir una lista de columnas validas, separadas por comas.',
      'orden' => '5',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'ancho',
      'columnas' => 'ancho',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 5;',
      'etiqueta' => 'Ancho',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define el ancho del cuadro',
      'orden' => '6',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'ordenar',
      'columnas' => 'ordenar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor:1',
      'etiqueta' => 'Ordenar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define si el cuadro es ordenable',
      'orden' => '7',
      'colapsado' => '1',
    ),
    6 => 
    array (
      'identificador' => 'exportar',
      'columnas' => 'exportar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor:1',
      'etiqueta' => 'Exportar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define si el cuadro es exportable',
      'orden' => '8',
      'colapsado' => '1',
    ),
    7 => 
    array (
      'identificador' => 'scroll',
      'columnas' => 'scroll',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_info: SI;',
      'etiqueta' => 'Scroll',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '9',
      'colapsado' => NULL,
    ),
    8 => 
    array (
      'identificador' => 'scroll_alto',
      'columnas' => 'scroll_alto',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 10;',
      'etiqueta' => 'Scroll alto',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '10',
      'colapsado' => NULL,
    ),
    9 => 
    array (
      'identificador' => 'paginar',
      'columnas' => 'paginar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'Paginar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define si el cuadro es paginable',
      'orden' => '11',
      'colapsado' => NULL,
    ),
    10 => 
    array (
      'identificador' => 'tamano_pagina',
      'columnas' => 'tamano_pagina',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 5;',
      'etiqueta' => 'Tamaño de página',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define el tamaño de la pagina',
      'orden' => '12',
      'colapsado' => NULL,
    ),
    11 => 
    array (
      'identificador' => 'tipo_paginado',
      'columnas' => 'tipo_paginado',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_lista_c',
      'inicializacion' => 'no_seteado: ---Seleccione---;
lista: P,Propio/C,A cargo del CI;',
      'etiqueta' => 'Tipo de Paginado',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define si el paginado es a cargo del propio cuadro o del CI que lo contiene',
      'orden' => '13',
      'colapsado' => NULL,
    ),
    12 => 
    array (
      'identificador' => 'eof_invisible',
      'columnas' => 'eof_invisible',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'EOF - Invisible',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Si la consulta no devuelve registros, no mostrar nada',
      'orden' => '14',
      'colapsado' => NULL,
    ),
    13 => 
    array (
      'identificador' => 'eof_customizado',
      'columnas' => 'eof_customizado',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 2;
columnas: 60;',
      'etiqueta' => 'EOF - Customizado',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '15',
      'colapsado' => NULL,
    ),
    14 => 
    array (
      'identificador' => 'dao_nucleo',
      'columnas' => 'dao_nucleo_proyecto, dao_nucleo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db_proyecto',
      'inicializacion' => 'sql: SELECT proyecto, nucleo, nucleo 
FROM apex_nucleo
%w%
ORDER BY 2 ASC;
columna_proyecto: proyecto;
no_seteado: No utiliza;',
      'etiqueta' => 'DAO (nucleo)',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Elemento del nucleo utilizado como DAO',
      'orden' => '16',
      'colapsado' => '1',
    ),
    15 => 
    array (
      'identificador' => 'dao_metodo',
      'columnas' => 'dao_metodo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 80;',
      'etiqueta' => 'DAO (metodo)',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Metodo a ejecutar en el DAO.',
      'orden' => '17',
      'colapsado' => '1',
    ),
  ),
);
	}

}
?>