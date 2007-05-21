<?php

class toba_mc_item___objetos_ei_cuadro
{
	static function get_metadatos()
	{
		return array (
  'basica' => 
  array (
    'item_proyecto' => 'toba_referencia',
    'item' => '/objetos/ei_cuadro',
    'item_nombre' => 'Paginado',
    'item_descripcion' => NULL,
    'item_act_buffer_proyecto' => 'toba',
    'item_act_buffer' => 0,
    'item_act_patron_proyecto' => 'toba',
    'item_act_patron' => 'CI',
    'item_act_accion_script' => '',
    'item_solic_tipo' => 'web',
    'item_solic_registrar' => 0,
    'item_solic_obs_tipo_proyecto' => NULL,
    'item_solic_obs_tipo' => NULL,
    'item_solic_observacion' => NULL,
    'item_solic_cronometrar' => 0,
    'item_parametro_a' => NULL,
    'item_parametro_b' => NULL,
    'item_parametro_c' => NULL,
    'item_imagen_recurso_origen' => NULL,
    'item_imagen' => NULL,
    'tipo_pagina_clase' => 'tp_referencia',
    'tipo_pagina_archivo' => 'tp_referencia.php',
    'item_include_arriba' => NULL,
    'item_include_abajo' => NULL,
    'item_zona_proyecto' => NULL,
    'item_zona' => NULL,
    'item_zona_archivo' => NULL,
    'zona_cons_archivo' => NULL,
    'zona_cons_clase' => NULL,
    'zona_cons_metodo' => NULL,
    'item_publico' => 0,
    'item_existe_ayuda' => NULL,
    'carpeta' => 0,
    'menu' => 1,
    'orden' => '5',
    'publico' => 0,
    'redirecciona' => 0,
    'crono' => 0,
    'solicitud_tipo' => 'web',
    'item_padre' => '/objetos/cuadro',
    'cant_dependencias' => '1',
    'cant_items_hijos' => '0',
  ),
  'objetos' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1554,
      'objeto_nombre' => 'Ejemplo de ei_cuadro - paginado',
      'objeto_subclase' => 'extension_ci',
      'objeto_subclase_archivo' => 'componentes/ei_cuadro - paginado/extension_ci.php',
      'orden' => 1,
      'clase_proyecto' => 'toba',
      'clase' => 'objeto_ci',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
      'fuente_proyecto' => NULL,
      'fuente' => NULL,
      'fuente_motor' => NULL,
      'fuente_host' => NULL,
      'fuente_usuario' => NULL,
      'fuente_clave' => NULL,
      'fuente_base' => NULL,
    ),
  ),
);
	}

}

class toba_mc_comp__1554
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1554,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ci',
    'subclase' => 'extension_ci',
    'subclase_archivo' => 'componentes/ei_cuadro - paginado/extension_ci.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Ejemplo de ei_cuadro - paginado',
    'titulo' => NULL,
    'colapsable' => 0,
    'descripcion' => NULL,
    'fuente_proyecto' => NULL,
    'fuente' => NULL,
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
    'creacion' => '2005-09-06 10:57:08',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/ci',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ci',
    'clase_icono' => 'objetos/multi_etapa.gif',
    'clase_descripcion_corta' => 'ci',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1642',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '2',
  ),
  '_info_eventos' => 
  array (
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => '400px',
    'alto' => '300px',
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => 'tab_h',
    'con_toc' => 0,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1000027,
      'identificador' => '1',
      'etiqueta' => 'Paginado por Cuadro',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'cuadro_auto',
      'eventos' => NULL,
      'orden' => 1,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
    1 => 
    array (
      'pantalla' => 466,
      'identificador' => '0',
      'etiqueta' => 'Paginado por CI',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'cuadro',
      'eventos' => NULL,
      'orden' => 2,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'cuadro',
      'proyecto' => 'toba_referencia',
      'objeto' => 1553,
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'cuadro_auto',
      'proyecto' => 'toba_referencia',
      'objeto' => 1000157,
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}

class toba_mc_comp__1553
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1553,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_cuadro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Ejemplo de ei_cuadro con paginado',
    'titulo' => NULL,
    'colapsable' => 0,
    'descripcion' => NULL,
    'fuente_proyecto' => NULL,
    'fuente' => NULL,
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
    'creacion' => '2005-09-06 10:53:40',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_cuadro',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_cuadro',
    'clase_icono' => 'objetos/cuadro_array.gif',
    'clase_descripcion_corta' => 'ei_cuadro',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1843',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '0',
  ),
  '_info_eventos' => 
  array (
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_cuadro' => 
  array (
    'titulo' => NULL,
    'subtitulo' => NULL,
    'sql' => NULL,
    'columnas_clave' => 'fecha',
    'clave_datos_tabla' => 0,
    'archivos_callbacks' => NULL,
    'ancho' => NULL,
    'ordenar' => 1,
    'exportar_xls' => 0,
    'exportar_pdf' => NULL,
    'paginar' => 1,
    'tamano_pagina' => 10,
    'tipo_paginado' => 'C',
    'scroll' => 0,
    'alto' => NULL,
    'eof_invisible' => 0,
    'eof_customizado' => NULL,
    'pdf_respetar_paginacion' => NULL,
    'pdf_propiedades' => NULL,
    'asociacion_columnas' => NULL,
    'dao_nucleo_proyecto' => NULL,
    'dao_clase' => NULL,
    'dao_metodo' => NULL,
    'dao_parametros' => NULL,
    'dao_archivo' => '',
    'cc_modo' => NULL,
    'cc_modo_anidado_colap' => NULL,
    'cc_modo_anidado_totcol' => NULL,
    'cc_modo_anidado_totcua' => NULL,
  ),
  '_info_cuadro_columna' => 
  array (
    0 => 
    array (
      'orden' => '1',
      'titulo' => 'Fecha',
      'estilo_titulo' => NULL,
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'fecha',
      'formateo' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
      'vinculo_indice' => NULL,
      'usar_vinculo' => NULL,
      'vinculo_carpeta' => NULL,
      'vinculo_item' => NULL,
      'total_cc' => NULL,
      'vinculo_target' => NULL,
      'vinculo_celda' => NULL,
      'vinculo_popup' => NULL,
      'vinculo_popup_param' => NULL,
    ),
    1 => 
    array (
      'orden' => '2',
      'titulo' => 'Importe',
      'estilo_titulo' => NULL,
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'importe',
      'formateo' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
      'vinculo_indice' => NULL,
      'usar_vinculo' => NULL,
      'vinculo_carpeta' => NULL,
      'vinculo_item' => NULL,
      'total_cc' => NULL,
      'vinculo_target' => NULL,
      'vinculo_celda' => NULL,
      'vinculo_popup' => NULL,
      'vinculo_popup_param' => NULL,
    ),
  ),
  '_info_cuadro_cortes' => 
  array (
  ),
);
	}

}

class toba_mc_comp__1000157
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1000157,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_cuadro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Cuadro auto-paginado',
    'titulo' => NULL,
    'colapsable' => 0,
    'descripcion' => NULL,
    'fuente_proyecto' => NULL,
    'fuente' => NULL,
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
    'creacion' => '2006-07-11 10:42:23',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_cuadro',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_cuadro',
    'clase_icono' => 'objetos/cuadro_array.gif',
    'clase_descripcion_corta' => 'ei_cuadro',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1843',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '0',
  ),
  '_info_eventos' => 
  array (
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_cuadro' => 
  array (
    'titulo' => NULL,
    'subtitulo' => NULL,
    'sql' => NULL,
    'columnas_clave' => 'fecha',
    'clave_datos_tabla' => 0,
    'archivos_callbacks' => NULL,
    'ancho' => NULL,
    'ordenar' => 1,
    'exportar_xls' => 0,
    'exportar_pdf' => NULL,
    'paginar' => 1,
    'tamano_pagina' => 10,
    'tipo_paginado' => 'P',
    'scroll' => 0,
    'alto' => NULL,
    'eof_invisible' => 0,
    'eof_customizado' => NULL,
    'pdf_respetar_paginacion' => NULL,
    'pdf_propiedades' => NULL,
    'asociacion_columnas' => NULL,
    'dao_nucleo_proyecto' => NULL,
    'dao_clase' => NULL,
    'dao_metodo' => NULL,
    'dao_parametros' => NULL,
    'dao_archivo' => '',
    'cc_modo' => NULL,
    'cc_modo_anidado_colap' => NULL,
    'cc_modo_anidado_totcol' => NULL,
    'cc_modo_anidado_totcua' => NULL,
  ),
  '_info_cuadro_columna' => 
  array (
    0 => 
    array (
      'orden' => '1',
      'titulo' => 'Fecha',
      'estilo_titulo' => NULL,
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'fecha',
      'formateo' => NULL,
      'no_ordenar' => 0,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => 0,
      'vinculo_indice' => NULL,
      'usar_vinculo' => NULL,
      'vinculo_carpeta' => NULL,
      'vinculo_item' => NULL,
      'total_cc' => NULL,
      'vinculo_target' => NULL,
      'vinculo_celda' => NULL,
      'vinculo_popup' => NULL,
      'vinculo_popup_param' => NULL,
    ),
    1 => 
    array (
      'orden' => '2',
      'titulo' => 'Importe',
      'estilo_titulo' => NULL,
      'estilo' => 'col-num-p1',
      'ancho' => NULL,
      'clave' => 'importe',
      'formateo' => NULL,
      'no_ordenar' => 0,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => 0,
      'vinculo_indice' => NULL,
      'usar_vinculo' => NULL,
      'vinculo_carpeta' => NULL,
      'vinculo_item' => NULL,
      'total_cc' => NULL,
      'vinculo_target' => NULL,
      'vinculo_celda' => NULL,
      'vinculo_popup' => NULL,
      'vinculo_popup_param' => NULL,
    ),
  ),
  '_info_cuadro_cortes' => 
  array (
  ),
);
	}

}

?>