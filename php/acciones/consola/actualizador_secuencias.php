<?php

/**
 * Actualiza las secuencias en base a un número particular que posee cada grupo de desarrollo
 * Este número evita que dos o más grupos trabajando sobre los mismos metadatos pero en bases de datos
 * separadas tengan conflictos a la hora de unificar sus trabajos debido a una duplicación en los números de secuencia
 */

define('apex_cantidad_seq_grupo', 1000000); //Cantidad de numeros por secuencia que tiene un grupo de desarrollo a su disposición

//Se leen las secuencias de la configuración
global $secuencias;
require_once(toba_dir()."/sql/secuencias.php");

echo "Actualizando secuencias:\n";
if (defined('apex_id_grupo_desarrollo')) {
	echo "(Se actualizan utilizando el grupo '".apex_id_grupo_desarrollo."')\n";
}

foreach ($secuencias as $seq => $datos) {
	if (! defined('apex_id_grupo_desarrollo')) {
		//Si no hay definido un grupo la secuencia se toma en forma normal
		$sql = "SELECT setval('$seq', max({$datos['campo']})) as nuevo FROM {$datos['tabla']}"; 
		$res = consultar_fuente($sql, 'instancia', null, true);		
		$nuevo = $res[0]['nuevo'];
	} else {
		//Sino se toma utilizando los límites según el ID del grupo
		$lim_inf = apex_cantidad_seq_grupo * apex_id_grupo_desarrollo;
		$lim_sup = apex_cantidad_seq_grupo * (apex_id_grupo_desarrollo + 1);
		$sql_nuevo = "SELECT max({$datos['campo']}) as nuevo
					  FROM {$datos['tabla']}
					  WHERE	{$datos['campo']} BETWEEN $lim_inf AND $lim_sup";
		$res = consultar_fuente($sql_nuevo, 'instancia', null, true);
		$nuevo = $res[0]['nuevo'];
		//Si no hay un maximo, es el primero del grupo
		if ($nuevo == NULL) {
			$nuevo = $lim_inf;
		}
		$sql = "SELECT setval('$seq', $nuevo)
					FROM {$datos['tabla']}";
		consultar_fuente($sql, 'instancia');		
	}
	echo "\t$seq: ". $nuevo ."\n" ;
}


?>