<?

class php_1815
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1815',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Usuario - Proyecto',
    'titulo' => 'Propiedades dentro de este proyecto',
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
    'creacion' => '2006-02-17 04:43:45',
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
      'etiqueta' => '&Modificacion',
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
    'ancho_etiqueta' => '150px',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'proyecto',
      'columnas' => 'proyecto',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_oculto_proyecto',
      'inicializacion' => '',
      'etiqueta' => 'proyecto',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'usuario_grupo_acc',
      'columnas' => 'usuario_grupo_acc',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_grupos_acceso;
clase: dao_permisos;
include: admin/db/dao_permisos.php;
clave: usuario_grupo_acc;
valor: nombre;
no_seteado: --Seleccione--;',
      'etiqueta' => 'Grupo Acceso',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'usuario_perfil_datos',
      'columnas' => 'usuario_perfil_datos',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_perfiles_datos;
clase: dao_permisos;
include: admin/db/dao_permisos.php;
clave: usuario_perfil_datos;
valor: nombre;',
      'etiqueta' => 'Perfil de Datos',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>