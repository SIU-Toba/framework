<?

class consultas
{
	/**
		Retorna la lista de juegos
	*/
	function get_juegos()
	{
		$sql = "SELECT id, nombre, descripcion FROM ref_juegos;";
		return consultar_fuente($sql);
	}
	
	/**
		Retorna la lista de deportes
	*/
	function get_deportes($filtro=null)
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
	function get_personas($filtro=null)
	{
		$where = '';
		if(isset($filtro)){
			if(isset($filtro['nombre'])){
				$where = " WHERE nombre ILIKE '%{$filtro['nombre']}%'";
			}
		}
		$sql = "SELECT id, nombre, fecha_nac FROM ref_persona $where;";
		return consultar_fuente($sql);
	}

	/**
		Retorna los dias de la semana
	*/
	function get_dias_semana()
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
	function get_dia_semana($dia)
	{
		$dias = self::get_dias_semana();
		$d[0]['desc_dia_semana'] = $dias[$dia]['desc'];
		return $d;
	}

	/**
		Retorna las horas del dia
	*/
	function get_horas_dia()
	{
		for($a=0;$a<24;$a++){
			$horas[$a]['id'] = $a+1;	
			$horas[$a]['desc'] = str_pad($a+1,2,0,STR_PAD_LEFT);	
		}
		return $horas;
	}
}
?>