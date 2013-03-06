<?php 

class odr_datos_tabla extends toba_datos_relacion
{
	/**
	 * Recorre los metadatos de la tabla y actualiza la definicion:
	 *   - Si hay alguna nueva columna la agrega
	 */
	function actualizar_campos($schema=null)
	{
		$basicos = $this->tabla('base')->get();
		$id_fuente = $basicos['fuente_datos'];
		
		//-- Obtengo datos
		$reg = $this->tabla('prop_basicas')->get();
		$tabla = $reg['tabla'];
		$fuente = toba::db($id_fuente, toba_editor::get_proyecto_cargado());
		if (is_null($schema)) {
			$schema = $fuente->get_schema();
		}		
		$columnas = $fuente->get_definicion_columnas($tabla, $schema);
		$tabla_ext = '';
		if ($reg['ap'] == toba_ap_tabla_db_mt::id_ap_mt) {	// Si es un ap multitabla
			$tabla_ext = $reg['tabla_ext'];
			$columnas = array_merge($columnas, $fuente->get_definicion_columnas($tabla_ext));
		}
		
		foreach (array_keys($columnas) as $id) {
			$columnas[$id]['columna'] = $columnas[$id]['nombre'];	
			$columnas[$id]['no_nulo_db'] = $columnas[$id]['not_null'];
			if ($columnas[$id]['tipo'] == 'C' && $columnas[$id]['longitud'] > 0) {
				$columnas[$id]['largo'] = $columnas[$id]['longitud'];
			}			
		}
		$dbr = $this->tabla('columnas');
		$actuales = $dbr->get_filas(null, true);
		for ($a = 0; $a < count($columnas); $a++) {
			try{
				//--- Evita incluir dos veces el mismo nombre
				$nueva = true;
				foreach ($actuales as $id => $actual) {
					if ($columnas[$a]['columna'] == $actual['columna']) {
						$nueva = false;
					}
				}
				if ($nueva) {
					$dbr->nueva_fila($columnas[$a]);
				}
			}catch(toba_error $e) {
				if ($columnas[$a]['tabla'] == $tabla) {
					toba::notificacion()->agregar("Error agregando la COLUMNA '{$columnas[$a]['columna']}' al datos_tabla de laa tabla $tabla. " . $e->getMessage());
				} else {
					toba::notificacion()->agregar("Error agregando la COLUMNA '{$columnas[$a]['columna']}' al datos_tabla de laa tabla $tabla_ext. " . $e->getMessage());
				}
				
			}
		}
	}

}

?>