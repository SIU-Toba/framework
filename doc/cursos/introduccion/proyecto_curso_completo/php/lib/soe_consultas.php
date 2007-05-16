<?php

class soe_consultas
{
	/**
	*	Lista de Instituciones
	*/
	function get_instituciones($filtro=null)
	{
		$where = '';
		if($filtro['nombre']){
			$where = "WHERE nombre_completo ILIKE '%{$filtro['nombre']}%'"; 	
		}
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
	function get_sedes($filtro=null)
	{
		$sql_where = '';
		$where = array();
		if($filtro['nombre']){
			$where[] = "nombre ILIKE '%{$filtro['nombre']}%'"; 	
		}
		if($filtro['institucion']){
			$where[] = "institucion = '{$filtro['institucion']}'"; 	
		}
		if(count($where)>0) {
			$sql_where = "WHERE " . implode('AND ',$where);
		}
		$sql = "SELECT 	institucion as 			institucion, 
						sede as					sede,
						nombre as				nombre,
						codigopostal
				FROM soe_sedes
				$sql_where
				ORDER by 2";
		return consultar_fuente($sql);
	}

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
				WHERE 	institucion = '$institucion'
				ORDER by 2";
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
		$where = isset($pais) ? "AND		p.idpais = '$pais'" : '';
		$sql = "SELECT 	p.idprovincia as 		id,
						p.nombre as				nombre,
						p.idprovincia as 		idprovincia,
						pp.nombre as			pais
				FROM 	ona_provincia p,
						ona_pais pp
				WHERE 	pp.idpais = p.idpais
				$where
				ORDER by 2";
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
				WHERE idprovincia = '$provincia'
				ORDER by 2";
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
				WHERE codigopostal = '$localidad'";
		return consultar_fuente($sql);
	}
	
	/**
	*	Devuelve la provincia correspondiente a una localidad
	*/
	function get_provincia_localidad($localidad)
	{
		$sql = "SELECT 	idprovincia
				FROM ona_localidad
				WHERE codigopostal = '$localidad'";
		return consultar_fuente($sql);
	}
}
?>