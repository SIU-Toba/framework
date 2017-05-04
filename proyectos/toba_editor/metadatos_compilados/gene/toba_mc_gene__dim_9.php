<?php

class toba_mc_gene__dim_9
{
	static function get_info()
	{
		return array (
  'proyecto' => 'toba_editor',
  'dimension' => 9,
  'nombre' => 'deportes',
  'descripcion' => NULL,
  'schema' => NULL,
  'tabla' => 'ref_deportes',
  'col_id' => 'id',
  'col_desc' => 'nombre',
  'col_desc_separador' => NULL,
  'multitabla_col_tabla' => NULL,
  'multitabla_id_tabla' => NULL,
  'fuente_datos_proyecto' => 'toba_editor',
  'fuente_datos' => 'test',
  'gatillos' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_editor',
      'dimension' => 9,
      'gatillo' => 1,
      'tipo' => 'directo',
      'orden' => 1,
      'tabla_rel_dim' => 'ref_deportes',
      'columnas_rel_dim' => 'id',
      'tabla_gatillo' => NULL,
      'ruta_tabla_rel_dim' => NULL,
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'dimension' => 9,
      'gatillo' => 4,
      'tipo' => 'directo',
      'orden' => 2,
      'tabla_rel_dim' => 'ref_persona_deportes',
      'columnas_rel_dim' => 'deporte',
      'tabla_gatillo' => NULL,
      'ruta_tabla_rel_dim' => NULL,
    ),
    2 => 
    array (
      'proyecto' => 'toba_editor',
      'dimension' => 9,
      'gatillo' => 14,
      'tipo' => 'indirecto',
      'orden' => 1,
      'tabla_rel_dim' => 'ref_persona',
      'columnas_rel_dim' => NULL,
      'tabla_gatillo' => 'ref_persona_deportes',
      'ruta_tabla_rel_dim' => NULL,
    ),
  ),
);
	}

}

?>