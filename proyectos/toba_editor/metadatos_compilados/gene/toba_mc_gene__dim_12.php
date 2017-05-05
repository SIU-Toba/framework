<?php

class toba_mc_gene__dim_12
{
	static function get_info()
	{
		return array (
  'proyecto' => 'toba_editor',
  'dimension' => 12,
  'nombre' => 'juegos',
  'descripcion' => NULL,
  'schema' => NULL,
  'tabla' => 'ref_juegos',
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
      'dimension' => 12,
      'gatillo' => 16,
      'tipo' => 'directo',
      'orden' => 1,
      'tabla_rel_dim' => 'ref_juegos',
      'columnas_rel_dim' => 'id',
      'tabla_gatillo' => NULL,
      'ruta_tabla_rel_dim' => NULL,
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'dimension' => 12,
      'gatillo' => 17,
      'tipo' => 'directo',
      'orden' => 2,
      'tabla_rel_dim' => 'ref_juegos_oferta',
      'columnas_rel_dim' => 'juego',
      'tabla_gatillo' => NULL,
      'ruta_tabla_rel_dim' => NULL,
    ),
    2 => 
    array (
      'proyecto' => 'toba_editor',
      'dimension' => 12,
      'gatillo' => 18,
      'tipo' => 'directo',
      'orden' => 3,
      'tabla_rel_dim' => 'ref_persona_juegos',
      'columnas_rel_dim' => 'juego',
      'tabla_gatillo' => NULL,
      'ruta_tabla_rel_dim' => NULL,
    ),
  ),
);
	}

}

?>