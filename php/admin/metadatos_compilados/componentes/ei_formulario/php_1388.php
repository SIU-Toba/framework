<?
//Generador: compilador_proyecto.php

class php_1388
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1388',
    'anterior' => NULL,
    'reflexivo' => '1',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'form_prop_basicas',
    'subclase_archivo' => 'admin/objetos_toba/ei_formulario_ml/form_prop_basicas.php',
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor FORM ML - Prop. basicas',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'En esta interface se definen las caracteristicas centrales del ABM',
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
      'identificador' => 'ancho',
      'columnas' => 'ancho',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 10;',
      'etiqueta' => 'Ancho',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Ancho de la tabla',
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'filas',
      'columnas' => 'filas',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 3;',
      'etiqueta' => 'Lineas',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Cantidad de lineas a presentar. Si es 0 toma la cantidad de lineas fijas pasadas en el cargar datos.',
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'filas_agregar',
      'columnas' => 'filas_agregar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_info: SI;',
      'etiqueta' => 'Agregar/Quitar lineas',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indica si la interface permite agregar y quitar más lineas. También se va a permitir deshacer estas operaciones.',
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'filas_agregar_online',
      'columnas' => 'filas_agregar_online',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;
estado: 1;',
      'etiqueta' => 'Agregado en línea',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Si es online, la operación se hace en el cliente (usando javascript), sino se lanza al server como un evento.',
      'orden' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'filas_ordenar',
      'columnas' => 'filas_ordenar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor:1;',
      'etiqueta' => 'Ordenar líneas',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indica si la interfaz permite subi y bajar las posiciones de las distintas líneas.',
      'orden' => '5',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'columna_orden',
      'columnas' => 'columna_orden',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => NULL,
      'etiqueta' => 'Ordenar en columna',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Tanto en la carga como en la modificacion se puede emplear una columna extra en los datos de la cual se cargan y notifica el orden numérico de cada registro. Si no se utiliza columna el orden en que se envien y reciben los datos es el orden definitivo.',
      'orden' => '6',
      'colapsado' => NULL,
    ),
    6 => 
    array (
      'identificador' => 'filas_numerar',
      'columnas' => 'filas_numerar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor:1;',
      'etiqueta' => 'Numerar filas',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Numera las filas dinámicamente.',
      'orden' => '7',
      'colapsado' => NULL,
    ),
    7 => 
    array (
      'identificador' => 'scroll',
      'columnas' => 'scroll',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_info: SI;',
      'etiqueta' => 'SCROLL',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'El formulario tiene SCROLL?',
      'orden' => '8',
      'colapsado' => NULL,
    ),
    8 => 
    array (
      'identificador' => 'alto',
      'columnas' => 'alto',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 10;',
      'etiqueta' => 'SCROLL - Alto',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Alto de la zona de SCROLL',
      'orden' => '9',
      'colapsado' => NULL,
    ),
    9 => 
    array (
      'identificador' => 'analisis_cambios',
      'columnas' => 'analisis_cambios',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_lista_c',
      'inicializacion' => 'lista: NO,Sin análisis/LINEA, En línea con los registros/EVENTOS, Lanzados como eventos;',
      'etiqueta' => 'Análisis de cambios',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'El formulario puede analizar los cambios realizado por el cliente y notificarlos.

En la notificación en línea, a los datos enviados en el evento modifcación se incluye un id interno como llave asociativo de los registros, y en cada uno de ellos se agrega una columna indicando el tipo de cambio (A, B o M), la columna se accede como $registro[apex_ei_analisis_fila].

En la notificación por eventos, se lanzan los eventos registro_alta, registro_modificacion o registro_baja con el id y los datos de cada registro como parámetros.',
      'orden' => '10',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>