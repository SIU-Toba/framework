<?php
/**
 * Driver de conexión con postgres
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_postgres7 extends toba_db
{
	function __construct($profile, $usuario, $clave, $base, $puerto)
	{
		$this->motor = "postgres7";
		parent::__construct($profile, $usuario, $clave, $base, $puerto);
	}

	function get_dsn()
	{
		$puerto = ($this->puerto != '') ? "port={$this->puerto}": '';
		return "pgsql:host=$this->profile;dbname=$this->base;$puerto";	
	}

	/**
	*	Recupera el valor actual de una secuencia
	*	@param string $secuencia Nombre de la secuencia
	*	@return string Siguiente numero de la secuencia
	*/	
	function recuperar_secuencia($secuencia)
	{
		$sql = "SELECT currval('$secuencia') as seq;";
		$datos = $this->consultar($sql);
		return $datos[0]['seq'];
	}
		
	function retrazar_constraints()
	{
		$this->ejecutar("SET CONSTRAINTS ALL DEFERRED");
		toba_logger::instancia()->debug("************ Se retraza el chequeo de constraints ****************", 'toba');		
	}

	function abrir_transaccion()
	{
		$sql = 'BEGIN TRANSACTION';
		$this->ejecutar($sql);
		toba_logger::instancia()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK TRANSACTION';
		$this->ejecutar($sql);		
		toba_logger::instancia()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}
	
	function cerrar_transaccion()
	{
		$sql = "COMMIT TRANSACTION";
		$this->ejecutar($sql);		
		toba_logger::instancia()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
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
			throw new toba_error("La tabla '$tabla' no existe");	
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
}
?>