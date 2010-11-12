<?php

class consultas
{
	/**
	*	Lista de Jurisdicciones
	*/
	function get_jurisdicciones()
	{
		$sql = "SELECT id_jurisdiccion, nombre
				FROM jurisdiccion
				ORDER by nombre";
		return toba::db()->consultar($sql);
	}
	
	/**
	*	Lista de Unidades Académicas
	*/
	function get_ua($institucion)
	{
		$sql = "SELECT 	id_ua, nombre
				FROM ua
				WHERE 
					id_institucion = ".quote($institucion).
				" ORDER by nombre";
		return toba::db()->consultar($sql);
	}

	/**
	*	Lista de tipos de Unidad Académica
	*/
	function get_ua_tipos()
	{
		$sql = "SELECT 	id_ua_tipo, nombre
				FROM ua_tipo
				ORDER BY nombre";
		return toba::db()->consultar($sql);
	}
	
	//---Pais, provincia, localidad -----------------------------------------

	/**
	*	Lista de Paises
	*/
	function get_paises()
	{
		$sql = "SELECT 	id_pais, nombre
				FROM pais 
				ORDER BY nombre";
		return toba::db()->consultar($sql);
	}
	
	/**
	*	Lista de Provincias
	*/
	function get_provincias($pais=null)
	{
		$where = "";
		if (isset($pais) && !is_null($pais)) {
			$where .= " AND p.id_pais = ". quote($pais);
		}
		$sql = "SELECT 	p.id_provincia,
						p.nombre,
						pp.nombre as			pais_nombre
				FROM 	provincia p,
						pais pp
				WHERE 	pp.id_pais = p.id_pais
					$where
				ORDER BY nombre";
		return toba::db()->consultar($sql);
	}

	/**
	*	Lista de Localidades
	*/
	function get_localidades($pais, $provincia)
	{
		$sql = "SELECT 	cp, nombre
				FROM localidad
				WHERE 
						id_provincia = ". quote($provincia)."
					AND	id_pais = ". quote($pais).
				"ORDER by 2";
		return toba::db()->consultar($sql);
	}

	//-- Carga de columnas externas ---------------------------------

	/**
	*	Devuelve el pais correspondiente a una localidad
	*/
	function get_pais_localidad($localidad)
	{
		$sql = "SELECT 	id_pais 
				FROM localidad
				WHERE cp = ".quote($localidad);
		$salida = toba::db()->consultar_fila($sql);
		return $salida['id_pais'];
	}
	
	/**
	*	Devuelve la provincia correspondiente a una localidad
	*/
	function get_provincia_localidad($localidad)
	{
		$sql = "SELECT 	id_provincia
				FROM localidad
				WHERE cp = ".quote($localidad);
		$salida = toba::db()->consultar_fila($sql);
		return $salida['id_provincia'];
	}

	/**
	*	Lista de Instituciones
	*/
	function get_instituciones($where=null)
	{
		$where = isset($where) ? " WHERE $where " : '';
		$sql = "SELECT 	id_institucion, 
						nombre,
						sigla
				FROM institucion				
				$where
				ORDER BY nombre";
		return toba::db()->consultar($sql);
	}

	/**
	*	Lista de Sedes
	*/
	function get_sedes($where=null)
	{
		$where = isset($where) ? " WHERE $where " : '';
		$sql = "SELECT 	id_institucion, 
						id_sede,
						nombre,
						cp
				FROM sede				
				$where
				ORDER BY nombre";
		return toba::db()->consultar($sql);
	}

	function reporte_instituciones($where='true')
	{
		$sql = "SELECT 	ua.id_ua,
						ua.nombre 			as ua_nombre,
						ua_tipo.nombre 		as ua_tipo_nombre,
						inst.id_institucion,
						inst.nombre			as inst_nombre,
						inst.sigla			as inst_sigla
				FROM	institucion as inst,
						ua as ua,
						ua_tipo	as ua_tipo
				WHERE
						inst.id_institucion = ua.id_institucion
					AND	ua.id_ua_tipo = ua_tipo.id_ua_tipo 
					AND		
						$where
				ORDER BY inst.nombre";
		toba::logger()->debug($sql);
		return toba::db()->consultar($sql);
	}
}
?>