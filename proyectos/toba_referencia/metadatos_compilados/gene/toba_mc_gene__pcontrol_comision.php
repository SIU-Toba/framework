<?php

class toba_mc_gene__pcontrol_comision
{
	static function get_info()
	{
		return array (
  'cabecera' => 
  array (
    'proyecto' => 'toba_referencia',
    'pto_control' => 'comision',
    'descripcion' => 'Se selecciona una comision',
  ),
  'parametros' => 
  array (
    0 => 
    array (
      'parametro' => 'carrera',
    ),
    1 => 
    array (
      'parametro' => 'comision',
    ),
    2 => 
    array (
      'parametro' => 'legajo',
    ),
    3 => 
    array (
      'parametro' => 'materia',
    ),
    4 => 
    array (
      'parametro' => 'nro_inscripcion',
    ),
  ),
  'controles' => 
  array (
    0 => 
    array (
      'archivo' => 'puntos_de_control/controles/ctrl_comision.php',
      'clase' => 'ctrl_comision',
      'actua_como' => 'M',
    ),
  ),
);
	}

}

?>