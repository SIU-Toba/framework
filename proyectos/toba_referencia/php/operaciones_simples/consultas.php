<?php
php_referencia::instancia()->agregar(__FILE__);

class consultas
{

	static function get_deportes_por_persona($where='true')
	{
		$sql = "
			SELECT
				dep.id												as id_deporte,
				per.nombre											as nombre_persona,
				dep.nombre											as nombre_deporte,
				per_dep.dia_semana									as dia_semana,
				per_dep.hora_inicio || ' a ' || per_dep.hora_fin	as horario
			FROM
				ref_persona_deportes per_dep
					INNER JOIN ref_persona per ON (per_dep.persona = per.id)
					INNER JOIN ref_deportes dep ON (per_dep.deporte = dep.id)
			WHERE
				$where
		";
		return toba::db()->consultar($sql);
	}

	/**
		Retorna la lista de juegos
	*/
	static function get_juegos()
	{
		$sql = 'SELECT id, nombre, descripcion FROM ref_juegos;';
		return toba::db()->consultar($sql);
	}
	
	/**
		Retorna la lista de deportes
	*/
	static function get_deportes($filtro=null)
	{
		$where = '';
		if(isset($filtro)){
			if (isset($filtro['nombre'])) {
				$nombre = quote("%{$filtro['nombre']}%");
				$where = " WHERE nombre ILIKE $nombre";
			}
		}
		$sql = "SELECT id, nombre, descripcion FROM ref_deportes $where";
		return consultar_fuente($sql);
	}
	
	/**
		Retorna la lista de personas
	*/
	static function get_personas($filtro=null)
	{
		$where = '';
		if(isset($filtro)){
			if(isset($filtro['nombre'])){
				$nombre = quote("%{$filtro['nombre']}%");
				$where = " WHERE nombre ILIKE $nombre";
			}
		}
		$sql = "SELECT id, nombre, fecha_nac FROM ref_persona $where ORDER BY nombre";
		$sql = toba::perfil_de_datos()->filtrar($sql);
		return consultar_fuente($sql);
	}
	
	static function get_personas_con_deporte($deporte)
	{
		$deporte = toba::db()->quote($deporte);
		$sql = "SELECT p.id, p.nombre, p.fecha_nac
				FROM 
					ref_persona p,
					ref_persona_deportes d
				WHERE 
					p.id = d.persona AND
					d.deporte = $deporte
				ORDER BY p.nombre
					
		";
		return consultar_fuente($sql);
	}	
	
	static function get_persona_datos($id_persona)
	{
		$sql = "SELECT
			id,
			nombre,
			fecha_nac,
			planilla_pdf_firmada,
			(imagen IS NOT NULL) as tiene_imagen
		FROM ref_persona WHERE id = ".quote($id_persona);
		return toba::db()->consultar_fila($sql);
	}
	
	static function get_persona_datos_zona($persona)
	{
		return self::get_persona_datos($persona['id']);
	}
	
	
	static function get_persona_nombre($id_persona)
	{
		$datos = self::get_persona_datos($id_persona);
		
		if ($datos !== false) {
			return $datos['nombre'];
		} else {
			return '';	
		}
	}

	/**
		Retorna los dias de la semana
	*/
	static function get_dias_semana()
	{
		$dias[0]['id'] = '0';
		$dias[0]['desc'] = 'Lunes';
		$dias[1]['id'] = '1';
		$dias[1]['desc'] = 'Martes';
		$dias[2]['id'] = '2';
		$dias[2]['desc'] = 'Miercoles';
		$dias[3]['id'] = '3';
		$dias[3]['desc'] = 'Jueves';
		$dias[4]['id'] = '4';
		$dias[4]['desc'] = 'Viernes';
		$dias[5]['id'] = '5';
		$dias[5]['desc'] = 'Sabado';
		$dias[6]['id'] = '6';
		$dias[6]['desc'] = 'Domingo';
		return $dias;
	}

	/**
		Devuelve un dia con el formato que necesita el DAO
	*/
	static function get_dia_semana($dia)
	{
		$dias = self::get_dias_semana();
		$d[0]['desc_dia_semana'] = $dias[$dia]['desc'];
		return $d;
	}

	static function traeme_mis_dias($dias_mios)
	{
		$resultado = array();
		$dias = self::get_dias_semana();
		foreach($dias as $dia) {
			if (in_array($dia['id'], $dias_mios)) {
				$resultado[] =array('dia_semana' => $dia['id'], 'desc_dia_semana' => $dia['desc']);
			}
		}
		return $resultado;
	}


	/**
		Retorna las horas del dia
	*/
	static function get_horas_dia()
	{
		for($a=0;$a<24;$a++){
			$horas[$a]['id'] = $a+1;	
			$horas[$a]['desc'] = str_pad($a+1,2,0,STR_PAD_LEFT);	
		}
		return $horas;
	}
	
	//--------------------------------------------------
	//------------ PAISES 
	//--------------------------------------------------
	
	static function get_paises($filtro=null, $locale=null)
	{
		if (! isset($filtro) || ($filtro == null) || trim($filtro) == '') {
			return array();
		}
		$where = '';
		if (isset($locale)) {
			$locale = quote($locale);
			$where = "AND locale=$locale";
		}
		$filtro = quote("{$filtro}%");
		$sql = "SELECT 
					rowId, 
					countryName 
				FROM 
					iso_countries
				WHERE
					countryName ILIKE $filtro 
					$where
				LIMIT 20
		";
		return consultar_fuente($sql);		
	}
	
	static function get_pais($id=null)
	{
		if (! isset($id)) {
			return array();
		}
		$id = quote($id);
		$sql = "SELECT 
					rowId, 
					countryName
				FROM 
					iso_countries
				WHERE
					rowId = $id";
		$result = consultar_fuente($sql);	
		if (! empty($result)) {
			return $result[0]['countryname'];
		}
	}	

	static function get_locales()
	{
		$sql = 'SELECT distinct locale
				FROM
					iso_countries
		';
		return consultar_fuente($sql);
	}
	
	static function get_prefijo_telefonico($pais)
	{
		$pais = quote($pais);
		$sql = "SELECT phoneprefix
				FROM
					iso_countries
				WHERE
					rowId = $pais
		";
		$datos = toba::db()->consultar_fila($sql);
		return $datos['phoneprefix'];
	}
}
?>
