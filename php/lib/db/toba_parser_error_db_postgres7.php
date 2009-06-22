<?php

/**
 * Clase que parsea mensajes de error en lenguaje natural generados por un motor postgres, e intenta armar un mensaje entendible para el usuario
 * Utiliza los comentarios de tablas y campos para no mostrar los identificadores de la base
 * Usar así: toba::db()->set_parser_errores(new toba_parser_error_db_postgres7());
 * @package Fuentes
 */
class toba_parser_error_db_postgres7 extends toba_parser_error_db
{
	protected $id_db_original;
	protected $proyecto_original;
	protected $conexion_extra;
	protected $sep_ini = '"'; //Para lc_messages = 'esp' poner el caracter '«';
	protected $sep_fin = '"'; //Para lc_messages = 'esp' poner el caracter '»';
	protected $error_pk;		//En caso que el error sea de pk, guarda cual es su id
	protected $error_fk;		//En caso que el error sea de fk, guarda cual es su id
	protected $error_not_null;	//En caso de error not null guarda cual es el campo
	protected $mostrar_nombres_campos = true;	//En caso que no encuentre el comentario del campo, usa su nombre
	
	
	/**
	 * En caso que no encuentre el comentario del campo del error, usa su nombre
	 */
	function set_mostrar_nombres_campos($mostrar)
	{
		$this->mostrar_nombres_campos = $mostrar;
	}
	
	function parsear($sql, $sqlstate, $mensaje)
	{
		//-- Intenta determinar el separador
		try {
			$sql_alt = "SHOW lc_messages";
			$datos = $this->get_conexion_extra()->consultar_fila($sql_alt);
			if (stristr($datos['lc_messages'], 'es') !== false) {
				 $this->sep_ini = '«';
				 $this->sep_fin = '»';
			}
		} catch (toba_error $e) {
			
		}
		
		$accion = $this->get_accion($sql);
		$mensaje = str_replace("\n", '', $mensaje);
		$metodos = reflexion_buscar_metodos($this, 'parsear_sqlstate_');
		$metodo = "parsear_sqlstate_$sqlstate";
		if (in_array($metodo, $metodos)) {
			return $this->$metodo($accion, $sql, $mensaje);
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
		$tabla_sana = $db->quote($tabla);
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
							(c.relname= $tabla_sana OR c.relname = lower($tabla_sana))
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
				} elseif ($this->mostrar_nombres_campos) {
					$salida[$campo['campo']] = $campo['campo'];
				}
			}
		}
		return $salida;
	}

	//-------------------------------------------------------------------------------------
	//----- PRIMARY KEY
	//-------------------------------------------------------------------------------------
	
	function parsear_sqlstate_23505($accion, $sql, $mensaje)
	{
		if (preg_match("/.*{$this->sep_ini}(.*){$this->sep_fin}.*/", $mensaje, $partes)){
			$this->pk = $partes[1];
			return $this->get_mensaje_pk($accion, $partes[1]);
		}		
	}
	
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
		//-- Primero prueba si es una constraint
		$db = $this->get_conexion_extra();
		$pk_sana = $db->quote($pk);
		$sql = "SELECT COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as nombre_tabla,
		                  t.conkey as clave,
		                  c.relname as tabla
		           FROM pg_class c, pg_constraint t
		           WHERE
		           	   c.oid = t.conrelid
		           AND t.conname = $pk_sana";
		$rs = $db->consultar_fila($sql);
		if (! empty($rs)) {
			if (! is_null($rs['clave'])) {
				$claves = explode(',', substr($rs['clave'], 1, strlen($rs['clave']) - 2));
				$rs['campos'] = $this->get_comentario_campos($rs['tabla'], $claves);
			}
			return $rs;
		}			

		//-- Si no es una constraint, puede ser un indice
		$sql = "
				SELECT 
					COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as nombre_tabla,
				        x.indkey as clave,
					c.relname as tabla
				FROM 
					pg_index x,
					pg_class c,
					pg_class i
				WHERE
						c.oid = x.indrelid
					AND	i.oid = x.indexrelid
					AND	i.relname = $pk_sana";
		$rs = $db->consultar_fila($sql);
		if (! empty($rs)) {
			if (! is_null($rs['clave'])) {
				$claves = explode(' ', $rs['clave']);
				$rs['campos'] = $this->get_comentario_campos($rs['tabla'], $claves);
			}
			return $rs;
		}		
	}
	
	//-------------------------------------------------------------------------------------
	//----- FOREIGN KEY
	//-------------------------------------------------------------------------------------	
	
	function parsear_sqlstate_23503($accion, $sql, $mensaje)
	{
		if(preg_match("/.*{$this->sep_ini}(.*){$this->sep_fin}.*{$this->sep_ini}(.*){$this->sep_fin}.*{$this->sep_ini}(.*){$this->sep_fin}.*{$this->sep_ini}(.*){$this->sep_fin}.*/", $mensaje, $partes)){
			//delete y updates envian mas datos		
			$this->error_fk = $partes[2];
			return $this->get_mensaje_fk(false, $accion, $partes[1], $partes[2], $partes[3]);
		} elseif (preg_match("/.*{$this->sep_ini}(.*){$this->sep_fin}.*{$this->sep_ini}(.*){$this->sep_fin}.*{$this->sep_ini}(.*){$this->sep_fin}.*/", $mensaje, $partes)) {
			//inserts envian menos, y es un 'not present'
			$this->error_fk = $partes[2];
			return $this->get_mensaje_fk(true, $accion, $partes[1], $partes[2]);
		}		
	}	
	
	function get_mensaje_fk($es_alta, $accion, $tabla_local, $fk, $tabla_foranea=null)
	{
		$datos = $this->get_datos_fk($tabla_local, $fk, $tabla_foranea);
		if (! isset($datos['nombre_tabla'])) {
			$mensaje = "Error $accion. ";				
		} else {
			$mensaje = "Error $accion en <strong>{$datos['nombre_tabla']}</strong>. ";				
		}		
		if (! isset($datos['campos_locales']) || empty($datos['campos_locales'])) {
			if ($es_alta) {
				$mensaje .= 'El registro tiene valores que no están presentes en las tablas referenciadas.';
			} else {
				$mensaje .= 'Al menos un registro continúa siendo utilizado por otra tabla.';
			}
		} else {
			$mensaje .= (count($datos['campos_locales']) == 1 ) ?
							"El valor del campo " :
							"Los valores de los campos ";
			$mensaje .= '<em>'.implode('</em>, <em>', $datos['campos_locales']).'</em> ';
			if ($es_alta) {
				$mensaje .= (count($datos['campos_locales']) == 1 ) ?
								"no está presente en " :
								"no están presentes en ";		
			} else {
				$mensaje .= (count($datos['campos_locales']) == 1 ) ?
								"aún sigue siendo utilizado en " :
								"aún siguen siendo utilizados en ";					
			}
			$mensaje .= '<strong>'.$datos['nombre_tabla_foranea'].'</strong>';
		}
		return $mensaje;
	}	
	
	function get_datos_fk($tabla_local, $fk, $tabla_foranea)
	{
		//-- La foreing key que falla es de la tabla local?
		$rs = $this->get_datos_consulta_fk($tabla_local, $fk);
		if (! empty($rs)) {
			if (! is_null($rs['clave_local']) && ! is_null($rs['clave_foranea'])) {
				$clave_local = explode(',', substr($rs['clave_local'], 1, strlen($rs['clave_local']) - 2));
				$clave_foranea = explode(',', substr($rs['clave_foranea'], 1, strlen($rs['clave_foranea']) - 2));
				$rs['campos_locales'] = $this->get_comentario_campos($tabla_local, $clave_local);
				$rs['campos_foraneos'] = $this->get_comentario_campos($rs['ftabla'], $clave_foranea);
			}
			return $rs;
		}
		
		//-- La foreing key que falla es de la tabla foranea? Si es así­ hay que dar vuelta todo
		$rs = $this->get_datos_consulta_fk($tabla_foranea, $fk);
		if (! empty($rs)) {
			if (! is_null($rs['clave_local']) && ! is_null($rs['clave_foranea'])) {
				//-- Se intercambian los nombres de tablas y  claves entre locales y foraneas
				$temp = $rs['nombre_tabla'];
				$rs['nombre_tabla'] = $rs['nombre_tabla_foranea'];
				$rs['nombre_tabla_foranea'] = $temp;
				$clave_local = explode(',', substr($rs['clave_foranea'], 1, strlen($rs['clave_foranea']) - 2));
				$clave_foranea = explode(',', substr($rs['clave_local'], 1, strlen($rs['clave_local']) - 2));
				$rs['campos_locales'] = $this->get_comentario_campos($tabla_local, $clave_local);
				$rs['campos_foraneos'] = $this->get_comentario_campos($tabla_foranea, $clave_foranea);
			}
			return $rs;
		}
	}	
	
	protected function get_datos_consulta_fk($tabla, $fk)
	{
		$db = $this->get_conexion_extra();
		$fk_sana = $db->quote($fk);
		$tabla_sana = $db->quote($tabla);
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
						lower(c.relname) = lower($tabla_sana)
					AND c.oid = t.conrelid
					AND t.conname = $fk_sana
					AND r.oid = t.confrelid";
		return $db->consultar_fila($sql);		
	}
	
	//-------------------------------------------------------------------------------------
	//----- NOT NULL
	//-------------------------------------------------------------------------------------		
	
	function parsear_sqlstate_23502($accion, $sql, $mensaje)
	{
		if(preg_match("/.*{$this->sep_ini}(.*){$this->sep_fin}.*/", $mensaje, $partes)){
			$this->error_not_null = $partes[1];
			return $this->get_mensaje_not_null($accion, $partes[1]);
		}		
	}		
	
	function get_mensaje_not_null($accion, $campo)
	{
		$mensaje = "Error $accion. ";
		$mensaje .= "El campo <em>$campo</em> no debe quedar vacío.";
		return $mensaje;
	}
	
}

?>