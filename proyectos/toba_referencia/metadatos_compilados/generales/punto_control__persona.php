<?php

class punto_control__persona
{
	static function get_info()
	{
		return array (
  'cabecera' => 
  array (
    'proyecto' => 'toba_referencia',
    'pto_control' => 'persona',
    'descripcion' => 'En la ejecucion del item se selecciona una persona',
  ),
  'parametros' => 
  array (
    0 => 
    array (
      'parametro' => 'nro_inscripcion',
    ),
  ),
  'controles' => 
  array (
    0 => 
    array (
      'archivo' => 'puntos_de_control/controles/ctrl_persona.php',
      'clase' => 'ctrl_persona',
      'actua_como' => 'M',
    ),
    1 => 
    array (
      'archivo' => 'puntos_de_control/controles/ctrl_persona.php',
      'clase' => 'ctrl_persona_1',
      'actua_como' => 'M',
    ),
    2 => 
    array (
      'archivo' => 'puntos_de_control/controles/ctrl_persona.php',
      'clase' => 'ctrl_persona_2',
      'actua_como' => 'M',
    ),
  ),
);
	}

}
?>