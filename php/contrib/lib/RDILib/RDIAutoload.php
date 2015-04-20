<?php

class RDIAutoload
{
	static function registrar()
	{
        spl_autoload_register(array('RDIAutoload', 'cargar'), true, false);		
	}
	
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]);
		}
	}

	static $clases = array(
		'RDICliente'								=> 'RDICliente.php',
		'RDIServicios'								=> 'RDIServicios.php',
		'RDIServicioRecurso'						=> 'servicios/RDIServicioRecurso.php',
		'RDIServicioRecursoPersonal'				=> 'servicios/RDIServicioRecursoPersonal.php',
		'RDIServicioRecursoPersonalFoto'			=> 'servicios/RDIServicioRecursoPersonalFoto.php',
		'RDIServicioRecursoPersonalReciboSueldo'	=> 'servicios/RDIServicioRecursoPersonalReciboSueldo.php',
		'RDIConector'								=> 'conectores/RDIConector.php',
		'RDIConectorCMIS'							=> 'conectores/RDIConectorCMIS.php',
		'RDIConectorCMIS_ATOM'						=> 'conectores/RDIConectorCMIS_ATOM.php',
		'RDIExcepcion'								=> 'RDIExcepcion.php',
		'RDIExcepcionObjetoNoEncontrado'			=> 'RDIExcepcion.php',
		'RDILog'									=> 'RDILog.php',
		'RDITipos'									=> 'RDITipos.php',
		'RDIServicioBasico'							=> 'servicios/RDIServicioBasico.php',
	);
}
?>