<?
require_once("db.php");

class db_postgres7 extends db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "postgres7";
		parent::__construct($profile, $usuario, $clave, $base);
	}
	
	function retrazar_constraints()
	{
		$this->ejecutar("SET CONSTRAINTS ALL DEFERRED");
		logger::instancia()->debug("************ Se retraza el chequeo de constraints ****************", 'toba');		
	}

	//------------------------------------------------------------------------
	//-- INSPECCION del MODELO de DATOS
	//------------------------------------------------------------------------

	/**
	*	Busca la definicion de un TABLA. Falta terminar
	*/
	function get_definicion_columnas($tabla)
	{
		//1) Busco definicion
		$sql = "SELECT 	a.attname as 			nombre,
						t.typname as 			tipo,
						a.attlen as 			tipo_longitud,
						a.atttypmod as 			longitud,
						a.attnotnull as 		not_null,
						a.atthasdef as 			tiene_predeterminado,
						d.adsrc as 				valor_predeterminado,
						ic.relname AS 			nombre_indice,
						i.indisunique AS 		uk,
						i.indisprimary AS 		pk,
						'' as					secuencia,
						a.attnum as 			orden
				FROM 	pg_class c,
						pg_type t,
						pg_attribute a 	
							LEFT OUTER JOIN pg_attrdef d
								ON ( d.adrelid = a.attrelid AND d.adnum = a.attnum)
							LEFT OUTER JOIN ( pg_index i INNER JOIN pg_class ic ON ic.oid = i.indexrelid ) 
								ON ( a.attrelid = i.indrelid 
									AND (i.indkey[0] = a.attnum 
										OR i.indkey[1] = a.attnum 
										OR i.indkey[2] = a.attnum 
										OR i.indkey[3] = a.attnum 
										OR i.indkey[4] = a.attnum 
										OR i.indkey[5] = a.attnum 
										OR i.indkey[6] = a.attnum 
										OR i.indkey[7] = a.attnum) )
				WHERE c.relkind in ('r','v') 
				AND c.relname='$tabla'
				AND a.attname not like '....%%'
				AND a.attnum > 0 
				AND a.atttypid = t.oid 
				AND a.attrelid = c.oid 
				ORDER BY a.attnum;";
		$columnas = $this->consultar($sql);
		if(!$columnas){
			throw new excepcion_toba("La tabla '$tabla' no existe");	
		}
		//2) Normalizo VALORES
		$columnas_booleanas = array('uk','pk','not_null','tiene_predeterminado');
		foreach(array_keys($columnas) as $id) {
			//Estas columnas manejan string en vez de booleanos
			foreach($columnas_booleanas as $x) {
				if($columnas[$id][$x]=='t'){
					$columnas[$id][$x] = true;
				}else{
					$columnas[$id][$x] = false;
				}
			}
			//Tipo de datos generico
			$columnas[$id]['tipo'] = $this->get_tipo_datos_generico($columnas[$id]['tipo']);
			//longitudes
			if($columnas[$id]['tipo_longitud'] <= 0){
				$columnas[$id]['longitud'] = $columnas[$id]['longitud'] - 4;
			}
			//Secuencias
			if($columnas[$id]['tiene_predeterminado']){
				$match = array();
				if(preg_match("&nextval.*?(\'|\")(.*?[.]|)(.*)(\'|\")&",$columnas[$id]['valor_predeterminado'],$match)){
					$columnas[$id]['secuencia'] = $match[3];
				}			
			}
		}
		return $columnas;
	}

	//-----------------------------------------------------------------------------------
	//-- GENERACION de MENSAJES de ERROR (Esto necesita adecuacion al esquema actual)
	//-----------------------------------------------------------------------------------

	/**
	*	Esta funcion mapea el error de la base al modulo de mensajes del toba
	*	Basicamente deduce el SQLSTATE de la descripcion
	*	Para que esto funcione necesito saber la version, y el idioma del motor
	*	(VERSION POSTGRESQL 7.4.3)
	*/
	function obtener_error_toba($codigo, $descripcion)
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
   //Si corresponde a un indice alternativo retorna un mensaje gen?rico.
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
?>