<?
//Generador: compilador_proyecto.php

class php_1722
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1722',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - ci - Eventos',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => NULL,
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
    'creacion' => '2005-11-09 13:43:43',
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
      'identificador' => 'cancelar',
      'etiqueta' => '&Cancelar',
      'maneja_datos' => '0',
      'sobre_fila' => '0',
      'confirmacion' => '',
      'estilo' => 'abm-input',
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => '',
      'ci_predep' => NULL,
      'implicito' => '0',
      'grupo' => 'cargado',
    ),
    1 => 
    array (
      'identificador' => 'aceptar',
      'etiqueta' => '&Aceptar',
      'maneja_datos' => '1',
      'sobre_fila' => '0',
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => '0',
      'grupo' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'modificacion',
      'etiqueta' => NULL,
      'maneja_datos' => NULL,
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
    'ancho' => '500',
    'ancho_etiqueta' => '150px',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'estilo',
      'columnas' => 'estilo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;',
      'etiqueta' => 'Estilo',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'confirmacion',
      'columnas' => 'confirmacion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 4;
columnas: 60;',
      'etiqueta' => 'Confirmacion',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Texto de confirmacion a mostrar antes de disparar el evento.',
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'ayuda',
      'columnas' => 'ayuda',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 4;
columnas: 60;',
      'etiqueta' => 'Ayuda',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Texto orientativo a mostrar cuando se posiciona el mouse sobre el elemento grafico que dispara el evento.',
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'grupo',
      'columnas' => 'grupo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 80;',
      'etiqueta' => 'Grupos',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Este identificador permite catalogar los eventos en grupos. Hay que ingresar la lista de grupos a los que el evento pertenece seperados por comas. Existen primitivas en los EI que permiten definir que grupo mostrar.',
      'orden' => '6',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'pantallas',
      'columnas' => 'pantallas',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_multi_seleccion_lista',
      'inicializacion' => 'dao: get_pantallas_posibles;
clave: identificador;
valor: nombre;
mostrar_utilidades: 1;',
      'etiqueta' => 'Pantallas',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Pantallas en las que se incluye el evento.',
      'orden' => '7',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>