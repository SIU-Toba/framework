<?php

class consulta
{
	static function get_instituciones($filtro=null)
	{
		$sql = "SELECT 	institucion as 			institucion, 
						nombre_abreviado as		nombre,
						sigla as				sigla
				FROM soe_instituciones
				ORDER by 2";
		return consultar_fuente($sql);
	}

	static function get_sedes($filtro=null)
	{
		$sql = "SELECT 	institucion as 			institucion, 
						sede as					sede,
						nombre as				nombre
				FROM soe_sedes
				ORDER by 2";
		return consultar_fuente($sql);
	}

	static function get_unidadacad($institucion)
	{
		$sql = "SELECT 	unidadacad as			unidadacad, 
						nombre as				nombre
				FROM soe_unidadesacad
				ORDER by 2";
		return consultar_fuente($sql);
	}

	static function get_jurisdicciones()
	{
		$sql = "SELECT 	jurisdiccion as			id, 
						descripcion as			nombre
				FROM soe_jurisdicciones
				ORDER by 2";
		return consultar_fuente($sql);
	}

	static function get_jurisdicciones_filtro($filtro)
	{
		
		$sql = "SELECT 	jurisdiccion as			id, 
						descripcion as			nombre
				FROM soe_jurisdicciones
			WHERE descripcion like '%$filtro%'
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	static function get_paises()
	{
		$sql = "SELECT 	idpais as 				id, 
						nombre as				nombre
				FROM ona_pais 
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	static function get_provincias($pais)
	{
		$sql = "SELECT 	idprovincia as 			id,
						nombre as				nombre 
				FROM ona_provincia
				WHERE idpais = '$pais'
				ORDER by 2";
		return consultar_fuente($sql);
	}

	static function get_localidades($provincia)
	{
		$sql = "SELECT 	codigopostal as 		id,
						nombre as				nombre
				FROM ona_localidad
				WHERE idprovincia = '$provincia'
				ORDER by 2";
		return consultar_fuente($sql);
	}
	
	static function get_tipounidad($filtro = null) 
	{
		$sql = "SELECT tipoua as id,
					   descripcion as nombre,
					   detalle as detalle,
					   estado as estado
				FROM soe_tiposua";
		if (isset($filtro)) {
			$sql = $sql . " WHERE estado LIKE '%".$filtro["estado"]."%' ";
	    }
		return consultar_fuente($sql);
	}


	static function get_sedes_filtrado($filtro = null) 
	{
		//echo(print_r($filtro,true));
		$sql = "SELECT nombre as nombre,
					   tiposede as tiposede,
					   institucion as institucion,
					   sede as sede	
				FROM soe_sedes";
		if (isset($filtro)) {
			$sql = $sql . " WHERE institucion LIKE '%".$filtro["instituciones"]."%' ";
	    }
		return consultar_fuente($sql);
	}

}
?>