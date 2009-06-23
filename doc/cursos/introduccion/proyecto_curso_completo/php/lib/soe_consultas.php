<?php

class soe_consultas
{
	/**
	*	Lista de Jurisdicciones
	*/
	function get_jurisdicciones()
	{
		$sql = "SELECT 	jurisdiccion as			id, 
						descripcion as			nombre
				FROM soe_jurisdicciones
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	/**
	*	Lista de Unidades Académicas
	*/
	function get_unidadacad($institucion)
	{
		$sql = "SELECT 	unidadacad as			id, 
						nombre as				nombre
				FROM soe_unidadesacad
				WHERE 	institucion = ".quote($institucion).
				"ORDER by 2";
		return consultar_fuente($sql);
	}

	/**
	*	Lista de tipos de Unidad Académica
	*/
	function get_tiposua()
	{
		$sql = "SELECT 	tipoua		 as			id, 
						descripcion as			nombre
				FROM soe_tiposua
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	//---Pais, provincia, localidad -----------------------------------------

	/**
	*	Lista de Paises
	*/
	function get_paises()
	{
		$sql = "SELECT 	idpais as 				id, 
						nombre as				nombre
				FROM ona_pais 
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	/**
	*	Lista de Provincias
	*/
	function get_provincias($pais=null)
	{
		$where = array();
		if (isset($pais) && ! is_null($pais)){
			$where[] = " p.idpais = ". quote($pais);
		}
		$sql = "SELECT 	p.idprovincia as 		id,
						p.nombre as				nombre,
						p.idprovincia as 		idprovincia,
						pp.nombre as			pais
				FROM 	ona_provincia p,
						ona_pais pp
				WHERE 	pp.idpais = p.idpais
				ORDER by 2";
		if(count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}				
		toba::logger()->debug($sql);
		return consultar_fuente($sql);
	}

	/**
	*	Lista de Localidades
	*/
	function get_localidades($provincia)
	{
		$sql = "SELECT 	codigopostal as 		id,
						nombre as				nombre
				FROM ona_localidad
				WHERE idprovincia = ". quote($provincia).
				"ORDER by 2";
		toba::logger()->debug($sql);
		return consultar_fuente($sql);
	}

	//-- Carga de columnas externas ---------------------------------

	/**
	*	Devuelve el pais correspondiente a una localidad
	*/
	function get_pais_localidad($localidad)
	{
		$sql = "SELECT 	idpais 
				FROM ona_localidad
				WHERE codigopostal = ".quote($localidad);
		return consultar_fuente($sql);
	}
	
	/**
	*	Devuelve la provincia correspondiente a una localidad
	*/
	function get_provincia_localidad($localidad)
	{
		$sql = "SELECT 	idprovincia
				FROM ona_localidad
				WHERE codigopostal = ".quote($localidad);
		return consultar_fuente($sql);
	}

	/**
	*	Lista de Instituciones
	*/
	function get_instituciones($where=null)
	{
		$where = isset($where) ? " WHERE $where " : '';
		$sql = "SELECT 	institucion as 			institucion, 
						nombre_completo as		nombre,
						nombre_abreviado as		nombre_corto,
						sigla as				sigla
				FROM soe_instituciones				
				$where
				ORDER by 2";
		return consultar_fuente($sql);
	}

	/**
	*	Lista de Sedes
	*/
	function get_sedes($where=null)
	{
		$where = isset($where) ? " WHERE $where " : '';
		$sql = "SELECT 	institucion as 			institucion, 
						sede as					sede,
						nombre as				nombre,
						codigopostal
				FROM soe_sedes				
				$where
				ORDER by 2";
		return consultar_fuente($sql);
	}

	function reporte_instituciones($where=null)
	{
		$where = isset($where) ? " WHERE $where " : '';
		$sql = "SELECT 	su.unidadacad as ua, 
						su.nombre as ua_nombre,
						st.descripcion as ua_tipo,
						si.institucion,
						si.nombre_abreviado as institucion_nombre
				FROM	soe_instituciones as si
				JOIN soe_unidadesacad as su ON si.institucion = su.institucion
				JOIN soe_tiposua as st ON su.tipoua = st.tipoua
				$where
				ORDER BY	si.institucion";
		toba::logger()->debug($sql);
		return consultar_fuente($sql);
	}
}
?>