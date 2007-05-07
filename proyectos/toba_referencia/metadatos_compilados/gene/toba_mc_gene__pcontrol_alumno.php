<?php

class toba_mc_gene__pcontrol_alumno
{
	static function get_info()
	{
		return array (
  'cabecera' => 
  array (
    'proyecto' => 'toba_referencia',
    'pto_control' => 'alumno',
    'descripcion' => 'Se selecciona un alumno.',
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
      'parametro' => 'nro_inscripcion',
    ),
  ),
  'controles' => 
  array (
    0 => 
    array (
      'archivo' => 'puntos_de_control/controles/ctrl_alumno.php',
      'clase' => 'ctrl_alumno',
      'actua_como' => 'M',
    ),
  ),
);
	}

}

?>