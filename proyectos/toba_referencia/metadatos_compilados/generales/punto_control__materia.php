<?php

class punto_control__materia
{
	static function get_info()
	{
		return array (
  'cabecera' => 
  array (
    'proyecto' => 'toba_referencia',
    'pto_control' => 'materia',
    'descripcion' => 'Se selecciona una materia',
  ),
  'parametros' => 
  array (
    0 => 
    array (
      'parametro' => 'carrera',
    ),
    1 => 
    array (
      'parametro' => 'legajo',
    ),
    2 => 
    array (
      'parametro' => 'materia',
    ),
    3 => 
    array (
      'parametro' => 'nro_inscripcion',
    ),
  ),
  'controles' => 
  array (
    0 => 
    array (
      'archivo' => 'puntos_de_control/controles/ctrl_materia.php',
      'clase' => 'ctrl_materia',
      'actua_como' => 'M',
    ),
  ),
);
	}

}
?>