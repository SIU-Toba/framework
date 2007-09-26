<?php 

class odr_datos_tabla extends toba_datos_relacion
{
	/**
	 * Recorre los metadatos de la tabla y actualiza la definicion:
	 *   - Si hay alguna nueva columna la agrega
	 */
	function actualizar_campos()
	{
		//-- Obtengo datos
		$tabla = $this->tabla("prop_basicas")->get_fila_columna(0,"tabla");
		$reg = $this->tabla("base")->get();
		$proyecto = $reg['fuente_datos_proyecto'];
		$id_fuente = $reg['fuente_datos'];
		$fuente = toba::db($id_fuente, toba_editor::get_proyecto_cargado());
		$columnas = $fuente->get_definicion_columnas($tabla);
		foreach(array_keys($columnas) as $id){
			$columnas[$id]['columna'] = $columnas[$id]['nombre'];	
			$columnas[$id]['no_nulo_db'] = $columnas[$id]['not_null'];
			if ($columnas[$id]['tipo'] == 'C' && $columnas[$id]['longitud'] > 0) {
				$columnas[$id]['largo'] = $columnas[$id]['longitud'];
			}			
		}
		$dbr = $this->tabla("columnas");
		$actuales = $dbr->get_filas(null, true);
		for($a=0;$a<count($columnas);$a++){
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
			}catch(toba_error $e){
				toba::notificacion()->agregar("Error agregando la COLUMNA '{$columnas[$a]['columna']}' al datos_tabla de laa tabla $tabla. " . $e->getMessage());
			}
		}
	}

}

?>