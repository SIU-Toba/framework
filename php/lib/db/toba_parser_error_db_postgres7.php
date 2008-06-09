<?php

class toba_parser_error_db_postgres7 extends toba_parser_error_db
{
	protected $id_db_original;
	protected $proyecto_original;
	protected $conexion_extra;
	

	function parsear($sql, $sqlstate, $mensaje)
	{
		$accion = $this->get_accion($sql);
		$mensaje = str_replace("\n", '', $mensaje);
		switch ($sqlstate) {
			
			//Clave Duplicada
			case '23505':
				if(preg_match("/[^\"]*\"(.*)\".*/", $mensaje, $partes)){
					return $this->get_mensaje_pk($accion, $partes[1]);
				}
				break;
			
				
			//FK
			case '23503':
				if(preg_match("/.*\"(.*)\".*\"(.*)\".*\"(.*)\".*/", $mensaje, $partes)){
					return $this->get_mensaje_fk($accion, $partes[1], $partes[2]);
				}
				break;
				
			default:
				break;
		}
	}
	
	
	/**
	 * Recupera los comentarios agregados a los campos a una tabla mediante el 
	 * comando "COMMENT ON COLUMN tabla_x.campo_x IS 'comentario';"
	 * Si un campo no tiene comentario retorna el nombre del mismo.
	 */
	function get_comentario_campos($tabla, $posiciones=null)
	{
		$db = $this->get_conexion_extra();		
		$sql = "SELECT 
						a.attname as campo, 
						t.typname as tipo, 
						a.attnum as orden, 
	                 	col_description(c.oid, a.attnum) as com_campo
		           FROM
		           		pg_class c, 
		           		pg_attribute a, 
		           		pg_type t
		           WHERE 
							(c.relname='$tabla' OR c.relname = lower('$tabla')) 
			           AND a.attnum > 0
			           AND a.atttypid = t.oid
			           AND a.attrelid = c.oid
		           ORDER BY a.attnum";
		$rs = $db->consultar($sql);
		$salida = array();
		foreach ($rs as $campo) {
			if (! isset($posiciones) || in_array($campo['orden'], $posiciones)) {
				if (isset($campo['com_campo'])) {
					$salida[$campo['campo']] = $campo['com_campo'];
				}
			}
		}
		return $salida;
	}

	//-------------------------------------------------------------------------------------
	//----- PRIMARY KEY
	//-------------------------------------------------------------------------------------
	
	function get_mensaje_pk($accion, $pk)
	{
		$datos = $this->get_datos_pk($pk);
		if (! isset($datos['nombre_tabla'])) {
			$mensaje = "Error $accion. ";				
		} else {
			$mensaje = "Error $accion en <strong>{$datos['nombre_tabla']}</strong>. ";				
		}
		if (! isset($datos['campos']) || empty($datos['campos'])) {
			$mensaje .= 'Ya existe un registro con la misma clave o descripción.';
		} else {
			
			$mensaje .= 'Ya existe un registro con ';
			$mensaje .= (count($datos['campos']) == 1 ) ?
							"el mismo valor en el campo " :
							"los mismos valores en los campos ";
			$mensaje .= '<em>'.implode('</em>, <em>', $datos['campos']).'</em>';
		}
		return $mensaje;
	}
	
	function get_datos_pk($pk)
	{
		$db = $this->get_conexion_extra();
		$sql = "SELECT COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as nombre_tabla,
		                  t.conkey as clave,
		                  c.relname as tabla
		           FROM pg_class c, pg_constraint t
		           WHERE
		           	   c.oid = t.conrelid
		           AND t.conname = '$pk'
		           AND t.contype = 'p'";
      
		$rs = $db->consultar_fila($sql);
		if (! empty($rs)) {
			if (! is_null($rs['clave'])) {
				$claves = explode(',', substr($rs['clave'], 1, strlen($rs['clave']) - 2));
				$rs['campos'] = $this->get_comentario_campos($rs['tabla'], $claves);
			}
			return $rs;
		}
	}
	
	//-------------------------------------------------------------------------------------
	//----- FOREIGN KEY
	//-------------------------------------------------------------------------------------	
	
	
	function get_mensaje_fk($accion, $tabla, $fk)
	{
		$datos = $this->get_datos_fk($tabla, $fk);
		if (! isset($datos['nombre_tabla'])) {
			$mensaje = "Error $accion. ";				
		} else {
			$mensaje = "Error $accion en <strong>{$datos['nombre_tabla']}</strong>. ";				
		}		
		if (! isset($datos['campos_locales']) || empty($datos['campos_locales'])) {
			$mensaje .= 'Al menos un registro continúa siendo utilizado por otra tabla.';
		} else {
			$mensaje .= (count($datos['campos_locales']) == 1 ) ?
							"El valor del campo " :
							"Los valores de los campos ";
			$mensaje .= '<em>'.implode('</em>, <em>', $datos['campos_locales']).'</em> ';
			$mensaje .= (count($datos['campos_locales']) == 1 ) ?
							"aún sigue siendo utilizado en " :
							"aún siguen siendo utilizados en ";		
			$mensaje .= '<strong>'.$datos['nombre_tabla_foranea'].'</strong>';
		}
		return $mensaje;
	}	
	
	function get_datos_fk($tabla, $fk)
	{
		$db = $this->get_conexion_extra();
		$sql = "SELECT 
					COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as nombre_tabla,
					COALESCE(obj_description(t.oid, 'pg_constraint'), t.conname) as com_fk,
					r.relname as ftabla,
					COALESCE(obj_description(r.oid, 'pg_class'), r.relname) as nombre_tabla_foranea,                          
					t.conkey as clave_local,
					t.confkey as clave_foranea
				FROM 
					pg_class c, 
					pg_constraint t, 
					pg_class r
				WHERE
						(c.relname='$tabla' OR  c.relname = lower('$tabla'))
					AND c.oid = t.conrelid
					AND t.conname = '$fk'
					AND r.oid = t.confrelid
		";	
		$rs = $db->consultar_fila($sql);
		if (! empty($rs)) {
			if (! is_null($rs['clave_local']) && ! is_null($rs['clave_foranea'])) {
				$clave_local = explode(',', substr($rs['clave_local'], 1, strlen($rs['clave_local']) - 2));
				$clave_foranea = explode(',', substr($rs['clave_foranea'], 1, strlen($rs['clave_foranea']) - 2));
				$rs['campos_locales'] = $this->get_comentario_campos($tabla, $clave_local);
				$rs['campos_foraneos'] = $this->get_comentario_campos($rs['ftabla'], $clave_foranea);
			}
			return $rs;
		}
	}	
	
}

?>