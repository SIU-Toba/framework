<?php
php_referencia::instancia()->agregar(__FILE__);

class consultas
{
	/**
		Retorna la lista de juegos
	*/
	static function get_juegos()
	{
		$sql = "SELECT id, nombre, descripcion FROM ref_juegos;";
		return toba::db()->consultar($sql);
	}
	
	/**
		Retorna la lista de deportes
	*/
	static function get_deportes($filtro=null)
	{
		$where = '';
		if(isset($filtro)){
			if(isset($filtro['nombre'])){
				$where = " WHERE nombre LIKE '%{$filtro['nombre']}%'";
			}
		}
		$sql = "SELECT id, nombre, descripcion FROM ref_deportes $where;";
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
				$where = " WHERE nombre ILIKE '%{$filtro['nombre']}%'";
			}
		}
		$sql = "SELECT id, nombre, fecha_nac FROM ref_persona $where ORDER BY nombre";
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
	
	static function get_persona_datos($persona)
	{
		$sql = "SELECT id, nombre, fecha_nac FROM ref_persona WHERE id='{$persona['id']}'";
		$rs = consultar_fuente($sql);
		if (! empty($rs)) {
			return current($rs);
		}
		return $rs;
	}
	
	static function get_persona_nombre($persona)
	{
		if (isset($persona)) {
			$datos = self::get_persona_datos($persona);
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
}
?>