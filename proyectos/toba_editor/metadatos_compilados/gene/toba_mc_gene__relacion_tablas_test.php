<?php

class toba_mc_gene__relacion_tablas_test
{
	static function get_info()
	{
		return array (
  'ref_juegos' => 
  array (
    'ref_juegos_oferta' => 
    array (
      'cols_1' => 
      array (
        0 => 'id',
      ),
      'cols_2' => 
      array (
        0 => 'juego',
      ),
    ),
    'log_juegos' => 
    array (
      'cols_1' => 
      array (
        0 => 'id',
      ),
      'cols_2' => 
      array (
        0 => 'juego',
      ),
    ),
  ),
  'ref_persona' => 
  array (
    'ref_persona_deportes' => 
    array (
      'cols_1' => 
      array (
        0 => 'id',
      ),
      'cols_2' => 
      array (
        0 => 'persona',
      ),
    ),
    'ref_persona_juegos' => 
    array (
      'cols_1' => 
      array (
        0 => 'id',
      ),
      'cols_2' => 
      array (
        0 => 'persona',
      ),
    ),
    'log_persona' => 
    array (
      'cols_1' => 
      array (
        0 => 'id',
      ),
      'cols_2' => 
      array (
        0 => 'persona',
      ),
    ),
  ),
  'ref_persona_juegos' => 
  array (
    'ref_juegos' => 
    array (
      'cols_1' => 
      array (
        0 => 'juego',
      ),
      'cols_2' => 
      array (
        0 => 'id',
      ),
    ),
  ),
  'ref_deportes' => 
  array (
    'ref_persona_deportes' => 
    array (
      'cols_1' => 
      array (
        0 => 'id',
      ),
      'cols_2' => 
      array (
        0 => 'deporte',
      ),
    ),
  ),
);
	}

}

?>