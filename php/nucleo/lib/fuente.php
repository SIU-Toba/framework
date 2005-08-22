<?

class fuente_datos
{
	var $motor;
	var $conexion;
	
	function fuente_datos($motor, &$conexion)
	{
		$this->motor = $motor;
		$this->conexion =& $conexion;
	}
	
	function obtener_error_toba($codigo, $descripcion)
	//Esta funcion mapea el error de la base al modulo de mensajes del toba
	{
		return array();		
	}

	function obtener_sql_paginacion($sql)
	{
		return $sql;	
	}

	function get_tipo_datos_generico($tipo)
	/*
		Adaptado de ADOdb.
	*/
	{
		$tipo=strtoupper($tipo);
	static $typeMap = array(
		'VARCHAR' => 'C',
		'VARCHAR2' => 'C',
		'CHAR' => 'C',
		'C' => 'C',
		'STRING' => 'C',
		'NCHAR' => 'C',
		'NVARCHAR' => 'C',
		'VARYING' => 'C',
		'BPCHAR' => 'C',
		'CHARACTER' => 'C',
		'INTERVAL' => 'C',  # Postgres
		##
		'LONGCHAR' => 'X',
		'TEXT' => 'X',
		'NTEXT' => 'X',
		'M' => 'X',
		'X' => 'X',
		'CLOB' => 'X',
		'NCLOB' => 'X',
		'LVARCHAR' => 'X',
		##
		'BLOB' => 'B',
		'IMAGE' => 'B',
		'BINARY' => 'B',
		'VARBINARY' => 'B',
		'LONGBINARY' => 'B',
		'B' => 'B',
		##
		'YEAR' => 'D', // mysql
		'DATE' => 'D',
		'D' => 'D',
		##
		'TIME' => 'T',
		'TIMESTAMP' => 'T',
		'DATETIME' => 'T',
		'TIMESTAMPTZ' => 'T',
		'T' => 'T',
		##
		'BOOL' => 'L',
		'BOOLEAN' => 'L', 
		'BIT' => 'L',
		'L' => 'L',
		# SERIAL... se tratan como enteros#
		'COUNTER' => 'E',
		'E' => 'E',
		'SERIAL' => 'E', // ifx
		'INT IDENTITY' => 'E',
		##
		'INT' => 'E',
		'INT2' => 'E',
		'INT4' => 'E',
		'INT8' => 'E',
		'INTEGER' => 'E',
		'INTEGER UNSIGNED' => 'E',
		'SHORT' => 'E',
		'TINYINT' => 'E',
		'SMALLINT' => 'E',
		'E' => 'E',
		##
		'LONG' => 'N', // interbase is numeric, oci8 is blob
		'BIGINT' => 'N', // this is bigger than PHP 32-bit integers
		'DECIMAL' => 'N',
		'DEC' => 'N',
		'REAL' => 'N',
		'DOUBLE' => 'N',
		'DOUBLE PRECISION' => 'N',
		'SMALLFLOAT' => 'N',
		'FLOAT' => 'N',
		'NUMBER' => 'N',
		'NUM' => 'N',
		'NUMERIC' => 'N',
		'MONEY' => 'N',
		
		## informix 9.2
		'SQLINT' => 'E', 
		'SQLSERIAL' => 'E', 
		'SQLSMINT' => 'E', 
		'SQLSMFLOAT' => 'N', 
		'SQLFLOAT' => 'N', 
		'SQLMONEY' => 'N', 
		'SQLDECIMAL' => 'N', 
		'SQLDATE' => 'D', 
		'SQLVCHAR' => 'C', 
		'SQLCHAR' => 'C', 
		'SQLDTIME' => 'T', 
		'SQLINTERVAL' => 'N', 
		'SQLBYTES' => 'B', 
		'SQLTEXT' => 'X' 
		);
		if(isset($typeMap[$tipo])) 
			return $typeMap[$tipo];
		return null;
	}
}
//------------------------------------------------------------------------

class fuente_datos_postgres7 extends fuente_datos
{
	function fuente_datos_postgres7(&$conexion)
	{
		parent::fuente_datos("postgres7",$conexion);
	}
	//------------------------------------------------------------------------

	function obtener_definicion_columnas($tabla)
	{
		$a=0;
		$columnas = $this->conexion->MetaColumns($tabla,false);
		if(!$columnas){
			throw new excepcion_toba("La tabla '$tabla' no existe");	
		}
		//echo "<pre>"; print_r($columnas);
		foreach( $columnas as $col ){
			$definicion[$a]['columna'] = $col->name;
			$definicion[$a]['tipo'] = $this->get_tipo_datos_generico($col->type);
			if(($definicion[$a]['tipo'])=="C")
				if(isset($col->max_length)) 
					$definicion[$a]['largo'] = $col->max_length;
			if(isset($col->not_null)) $definicion[$a]['no_nulo_db'] = $col->not_null;
			if(isset($col->primary_key)) $definicion[$a]['pk'] = $col->primary_key;
			//Secuencias
			if(isset($col->default_value)){
				if(preg_match("/nextval/",$col->default_value)){
					$seq = true;
					$temp = preg_split("|\"|", $col->default_value);
					$definicion[$a]['secuencia'] = $temp[1];
				}			
			}
			$a++;
		}
		return $definicion;
	}

	function obtener_metadatos($tabla)
	{
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = " SELECT 	c.attnum as			num_col,
						c.attname as		columna, 
						c.attnotnull as		not_null,
						d.adsrc as			default
				FROM 	pg_attribute c 
						LEFT OUTER JOIN pg_attrdef d
							ON  adrelid = c.attrelid
							AND adnum = c.attnum
				WHERE 	c.attrelid = (SELECT oid FROM pg_class 
										WHERE relname = '$tabla')
				AND 	c.attnum > 0
				ORDER BY 1;";
		$rs =& $this->conexion->Execute($sql);
		if((!$rs)){
			monitor::evento("bug", "Error consultando METADATOS (columnas)". $this->conexion->ErrorMsg() );
		}
		if($rs->EOF){
			$metadatos['columnas'] = array();
		}else{
			$metadatos['columnas'] =& $rs->getArray();
		}
		$sql = "SELECT conname, contype, conkey
				FROM pg_constraint
				WHERE conrelid = (SELECT oid FROM pg_class 
								WHERE relname = '$tabla');";
		$rs = $this->conexion->Execute($sql);
		if((!$rs)){
			monitor::evento("bug", "Error consultando METADATOS (constraints)". $this->conexion->ErrorMsg() );
		}
		if($rs->EOF){
			$metadatos['constraints'] = array();
		}else{
			$metadatos['constraints'] =& $rs->getArray();
		}
		return $metadatos;
	}
	//------------------------------------------------------------------------

	function obtener_tablas_prefijo($prefijo="")
	//Buscar tablas con un prefijo determinado
	{
		//Admito que el valor del argumento -t sea opcional
		if ($prefijo == "VACIO") $prefijo = ".*";
		
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = " SELECT relname 
				FROM pg_class 
				WHERE relname ~* '$prefijo'
				AND relkind = 'r'
				ORDER BY relname;";
		//echo $sql;
		$rs = $this->conexion->Execute($sql);
		if((!$rs)){
			monitor::evento("bug", "Error consultando METADATOS (tablas)". $this->conexion->ErrorMsg() );
		}
		if($rs->EOF){
			$tablas = array();
		}else{
			$tablas =& $rs->getArray();
		}
		return $tablas;
	}
	//------------------------------------------------------------------------

	function obtener_error_toba($codigo, $descripcion)
	//Esta funcion mapea el error de la base al modulo de mensajes del toba
	//Basicamente deduce el SQLSTATE de la descripcion
	//Para que esto funcione necesito saber la version, y el idioma del motor
   //-------------VERSION POSTGRESQL 7.4.3----------------------------------     
	{
      global $db;
      
      //Se crea una conexión nueva, ya que la actual queda trabada después del error, 
      //recuperar los comentarios de tablas y campos.
      $conexion_local =& ADONewConnection('postgres7');
      $conexion_local->SetFetchMode(ADODB_FETCH_ASSOC);
      if (! $conexion_local->Connect($this->conexion->host, $this->conexion->user, $this->conexion->password, $this->conexion->database))
      {
         echo ei_mensaje('No se pudo conectar a la base de datos del item.', 'error');
      }
     
      $this->conexion->Execute('ROLLBACK TRANSACTION'); //para que no genere error en la version 3.40 de ADOdb
      $mensaje = '';
      
		//-- PRIMARY KEY ------------------
		if(strpos($descripcion, "duplicate key")){
			$respuesta["descripcion"] = $descripcion;
			$respuesta["indice"] = "pg_23505";
			if(preg_match("/[^\"]*\"(.*)\".*/",$descripcion,$temp)){
				$respuesta["parametro_tipo"] = "constraint";				
				$respuesta["parametro"] = $temp[1];
            $mensaje = 'Registro duplicado.<br><br>' . 
                       $this->comentario_pk($conexion_local, $temp[1]);
			}
		//-- NOT NULL ---------------------
		}elseif(strpos($descripcion,"null value")){
			$respuesta["descripcion"] = $descripcion;
			$respuesta["indice"] = "pg_23502";
			if(preg_match("/[^\"]*\"(.*)\".*/",$descripcion,$temp)){
				$respuesta["parametro_tipo"] = "columna";
				$respuesta["parametro"] = $temp[1];
            $mensaje = "La columna '{$temp[1]}' no debe quedar vacía.";
			}
		//-- FOREIGN KEY ---------------------
		}elseif(strpos($descripcion,"violates foreign key constraint")){
			$respuesta["descripcion"] = $descripcion;
			$respuesta["indice"] = "pg_23503";
			if(preg_match("/.*\"(.*)\".*\"(.*)\".*\"(.*)\".*/",$descripcion,$temp)){
				//ei_arbol($constraint);
				$respuesta["parametro_tipo"] = "constraint";
				$respuesta["parametro"] = $temp[1];
            $mensaje = 'Este registro está siendo utilizado.<br><br>' . 
                       $this->comentario_fk($conexion_local, $temp[3], $temp[2]);
			}
		//-- Respuesta GENERICA ---------------------
		}else{
			$respuesta["descripcion"] = $descripcion;
			$respuesta["indice"] = "db_error";
         $mensaje = $descripcion;
		}
      return $mensaje;
      //echo ei_mensaje($mensaje, 'error', (isset($subtitulo)? $subtitulo: ''));
		//ei_arbol($respuesta);
		//return $respuesta;
	}
   
   function comentario_tabla(&$conexion, $tabla = '')
   //Recupera el comentario agregado a una tabla mediante el 
   //comando "COMMENT ON TABLE tabla_x IS 'comentario';"
   //Si no tiene comentario retorna el nombre de la tabla.
   {
      $consulta = "SELECT COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as com_tabla
                   FROM pg_class c
                   WHERE c.relkind = 'E'
                   AND (c.relname='$tabla' OR c.relname = lower('$tabla'))";
      $rs = $conexion->Execute($consulta);
      if(! $rs->EOF)
      {
         return $rs->fields['com_tabla'];
      }
      return '';
   }
   
   function comentario_campos(&$conexion, $tabla = '', $campo = '')
   //Recupera los comentarios agregados a los campos a una tabla mediante el 
   //comando "COMMENT ON COLUMN tabla_x.campo_x IS 'comentario';"
   //Si un campo no tiene comentario retorna el nombre del mismo.
   {
      $consulta = "SELECT a.attname as campo, t.typname as tipo, a.attnum as orden, 
                         COALESCE(col_description(c.oid, a.attnum), a.attname) as com_campo
                   FROM pg_class c, pg_attribute a, pg_type t
                   WHERE relkind = 'E'
                   AND (c.relname='$tabla' OR c.relname = lower('$tabla')) " .
                   ($campo == ''? '': "AND a.attname = '$campo' ") . 
                  "AND a.attnum > 0
                   AND a.atttypid = t.oid
                   AND a.attrelid = c.oid
                   ORDER BY a.attnum";
      $rs = $conexion->Execute($consulta);
      $retorno = array();
      while(! $rs->EOF)
      {
         $retorno[$rs->fields['orden']] = array();
         $retorno[$rs->fields['orden']]['campo'] = $rs->fields['campo'];
         $retorno[$rs->fields['orden']]['tipo'] = $rs->fields['tipo'];
         $retorno[$rs->fields['orden']]['com_campo'] = $rs->fields['com_campo'];                  
         $rs->MoveNext();
      }
      return $retorno;
   }

   function comentario_fk(&$conexion, $tabla = '', $fk = '')
   //Retorna un mensaje compuesto a partir de las tablas y campos 
   //involucrados en un error de clave foránea.
   {
      $consulta = "SELECT COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as com_tabla,
                          COALESCE(obj_description(t.oid, 'pg_constraint'), t.conname) as com_fk,
                          r.relname as ftabla,
                          COALESCE(obj_description(r.oid, 'pg_class'), r.relname) as com_ftabla,                          
                          t.conkey as clave_local,
                          t.confkey as clave_foranea
                   FROM pg_class c, pg_constraint t, pg_class r
                   WHERE c.relkind = 'E'
                   AND (c.relname='$tabla' OR 
                        c.relname = lower('$tabla'))
                   AND c.oid = t.conrelid
                   AND t.conname = '$fk'
                   AND r.relkind = 'E'
                   AND r.oid = t.confrelid";
      $rs = $conexion->Execute($consulta);
      if(! $rs->EOF)
      {
         $com_tabla = $rs->fields['com_tabla'];
         $com_fk = $rs->fields['com_fk'];
         $ftabla = $rs->fields['ftabla'];
         $com_ftabla = $rs->fields['com_ftabla'];
         $clave_local = $rs->fields['clave_local'];
         $clave_foranea = $rs->fields['clave_foranea'];
         if (! is_null($clave_local) && ! is_null($clave_foranea))
         {
            $arr_clave_local = explode(',', substr($clave_local, 1, strlen($clave_local) - 2));
            $arr_clave_foranea = explode(',', substr($clave_foranea, 1, strlen($clave_foranea) - 2));
            $arr_comentarios_local = $this->comentario_campos($conexion, $tabla);
            $arr_comentarios_foranea = $this->comentario_campos($conexion, $ftabla);
            
            $campos_local = '(';
            foreach ($arr_clave_local as $local)
            {
               $campos_local .= $arr_comentarios_local[$local]['com_campo'] . ', ';
            }
            $campos_local = substr($campos_local, 0, strlen($campos_local) - 2) . ')';
            $campos_foranea = '(';
            foreach ($arr_clave_foranea as $foranea)
            {
               $campos_foranea .= $arr_comentarios_foranea[$foranea]['com_campo'] . ', ';
            }
            $campos_foranea = substr($campos_foranea, 0, strlen($campos_foranea) - 2) . ')';
            
            // string con el mensaje a retornar.
            $retorno = (count($arr_clave_foranea) > 1? 
                        "Los valores de los campos ": "El valor del campo ") . $campos_foranea;
            $retorno .= " de la tabla '" . $com_ftabla . "' ";
            $retorno .= (count($arr_clave_foranea) > 1? 
                        "aun están siendo utilizados por ": "aun está siendo utilizado por ");
            $retorno .= (count($arr_clave_local) > 1? 
                        "los campos ": "el campo ") . $campos_local;
            $retorno .= " de la tabla '" . $com_tabla . "'.";                        
            return $retorno;
         }
         else
         {
            return 'Uno o varios campos de esta tabla están siendo referenciados en otra.';
         }
      }
      else
      {
         return 'Uno o varios campos de esta tabla están siendo referenciados en otra.';
      }
      return $retorno;
   }

   function comentario_pk(&$conexion, $pk = '')
   //Retorna un mensaje compuesto a partir de la tabla y los campos 
   //involucrados en un error de clave duplicada.
   //Si corresponde a un indice alternativo retorna un mensaje genérico.
   {
      $consulta = "SELECT COALESCE(obj_description(c.oid, 'pg_class'), c.relname) as com_tabla,
                          t.conkey as clave_local,
                          c.relname as tabla
                   FROM pg_class c, pg_constraint t
                   WHERE c.relkind = 'E'
                   AND c.oid = t.conrelid
                   AND t.conname = '$pk'
                   AND t.contype = 'p'";
      $rs = $conexion->Execute($consulta);
      if(! $rs->EOF)
      {
         $com_tabla = $rs->fields['com_tabla'];
         $clave_local = $rs->fields['clave_local'];
         $tabla = $rs->fields['tabla'];
         if (! is_null($clave_local))
         {
            $arr_clave_local = explode(',', substr($clave_local, 1, strlen($clave_local) - 2));
            $arr_comentarios_local = $this->comentario_campos($conexion, $tabla);
            
            $campos_local = '(';
            foreach ($arr_clave_local as $local)
            {
               $campos_local .= $arr_comentarios_local[$local]['com_campo'] . ', ';
            }
            $campos_local = substr($campos_local, 0, strlen($campos_local) - 2) . ')';
            
            // string con el mensaje a retornar.
            $retorno = "Ya existe un registro con ";
            $retorno .= (count($arr_clave_local) > 1? 
                        "los mismos valores en los campos ": "el mismo valor en el campo ") . $campos_local;
            $retorno .= " de la tabla '" . $com_tabla . "'.";                        
            return $retorno;
         }
         else
         {
            return 'Ya existe un registro con la misma clave o descripción.';
         }
      }
      else
      {
         //Mensaje genérico.
         return 'Ya existe un registro con la misma clave o descripción.';
      }
      return $retorno;
   }
}
//------------------------------------------------------------------------

class fuente_datos_mysql extends fuente_datos
{
	function fuente_datos_mysql($con)
	{
		parent::fuente_datos("mysql", $con);
	}
}

//------------------------------------------------------------------------
class fuente_datos_odbc extends fuente_datos
{
	function fuente_datos_odbc()
	{
		parent::fuente_datos("odbc");
	}
}
//------------------------------------------------------------------------
?>