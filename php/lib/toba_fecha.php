<?php
/**
 * @deprecated
 */
class toba_fecha
{
	protected $timestamp;
	
	/**
	 * Crea un nuevo toba_fecha en base a una fecha d/m/a
	 * @return toba_fecha
	 */
	static function desde_pantalla($fecha)
	{
		$fecha = cambiar_fecha($fecha, '/', '-');		
		$salida = new toba_fecha();
		$salida->set_fecha($fecha);
		return $salida;
	}
	
	function __construct()
	{
		if (!func_num_args() ){
			$this->timestamp = strtotime(date("Y-m-d H:i:s"));
		}else{
			list($arg) = func_get_args();
			$this->set_fecha( $arg );
		}	
	}	
	
	//Metodos para setear la variable interna.
	function set_fecha($fecha)
	{
		if (isset($fecha))
			$this->timestamp = strtotime($fecha);	
	}
	
	function set_timestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}
	
	//Metodos para obtener una fecha desplazada en dias, meses o años. Se debe incluir el signo en el parametro.
	function get_fecha_desplazada($dias)
	{
		$aux = strtotime("$dias day", $this->timestamp);
		return $aux;
	}
	
	function get_fecha_desplazada_meses($meses)
	{
		$aux = strtotime("$meses month", $this->timestamp);		
		return $aux;		
	}
	
	function get_fecha_desplazada_años($anios)
	{
		$aux = strtotime("$anios year", $this->timestamp);
		return $aux;		
	}
	
	//Metodos de comparacion de fechas, siempre se compara contra la fecha cargada en la variable interna.
	function es_menor_que($fecha2)
	{
		if ($this->get_diferencia($fecha2) > 0)
						return TRUE;
			
		return FALSE;	 
	}
	
	function es_mayor_que($fecha2)
	{
		if ($this->get_diferencia($fecha2) < 0)
						return TRUE;
			
		return FALSE;	 	
	}
	
	function es_igual_que($fecha2)
	{
		if ($this->get_diferencia($fecha2) == 0)
						return TRUE;
			
		return FALSE;	 	
	}
	
	//Metodo que calcula la diferencia de dias entre dos fechas.
	function diferencia_dias($fecha2)
	{
		return (abs($this->get_diferencia($fecha2)));
	}
	
	//Metodos para obtener la fecha en distintos formatos, se utiliza para recuperar la fecha interna.
	function get_timestamp_db()
	{
		$aux = date("Y-m-d H:i:s",$this->timestamp);
		return $aux;
	}
	
	function get_fecha_db()
	{
		$aux = date("Y-m-d",$this->timestamp);
		return $aux;
	}
	
	function get_timestamp_pantalla()
	{
		$aux = date("d/m/Y H:i:s",$this->timestamp);
		return $aux;		
	}
		
	function get_fecha_pantalla()
	{
		$aux = date("d/m/Y",$this->timestamp);
		return $aux;		
	}	
	
	//Metodos estaticos para convertir fechas
	function convertir_fecha_a_timestamp($fecha)
	{
		$timestamp =  strtotime($fecha);
		$aux = date("Y-m-d H:i:s",$timestamp);
		return $aux;
	}
	
	function convertir_timestamp_a_fecha($timestamp)
	{
		$aux = date("Y-m-d",strtotime($timestamp));
		return $aux;
	}
	
	//Metodos para obtener la hora apartir de un timestamp
	function convertir_timestamp_a_hora($timestamp)
	{
		$aux = date("H:i:s",strtotime($timestamp));
		return $aux;
	}
	
	//Metodo que devuelve si el dia es sabado o domingo.	
	function es_dia_habil()
	{
		$aux = $this->get_parte('dia_semana');		//0 es para Domingo y 6 es para Sabado
		if (($aux > '0') AND ($aux < '6'))
					return TRUE;
			
		return FALSE;	
	}
	
	//Metodo que devuelve una parte especifica de la fecha.
	function get_parte($parte)
	{
		switch($parte)
        {
            case 'dia':
                $parte_fecha = 'mday';
                break;
                
            case 'mes':
                $parte_fecha = 'mon';
                break;
                
            case 'año':
                $parte_fecha = 'year';
                break;                

            case 'dia_semana':
                $parte_fecha = 'wday';
                break;                
                                
            default:
                $parte_fecha = 'mday';

        } // switch
		        
        $aux = $this->separar_fecha_en_partes();
        return ($aux[$parte_fecha]);
	}

	function separar_fecha_en_partes()
	{
		return getdate($this->timestamp);
	}	
	
	function get_diferencia($fecha2)
	{
		if ($fecha2 instanceof toba_fecha) {
			$fecha2 = $fecha2->get_fecha_db();
		}
		if(! is_null($fecha2)){
			$timestamp2 = strtotime($fecha2);
			$diff_segs = $timestamp2 - $this->timestamp;
			if ($diff_segs < 0)
				$resultado = ceil($diff_segs / 86400);
			else
			 	$resultado = floor($diff_segs / 86400);
		 	
			return $resultado;		
		}
	}
	
	function get_meses_anio()
	{
		//El dia que windows cumpla con el RFC 1766 esto va a funcar correctamente.
		/*$i = 0;		
		$meses = array();
		setlocale(LC_TIME, "es-ES");		
		$next_fecha = strtotime(date("Y-m-d H:i:s"));
		while ($i < 12){
				$mes_loco = strftime('%B-%m', $next_fecha);
				list($mes_letra, $mes_nro) = explode('-', $mes_loco);
				$meses[$mes_nro - 1] = array('id'=> $mes_nro, 'mes' => ucfirst($mes_letra));				
				$next_fecha = strtotime("+1 month", $next_fecha);				
				$i++;		
		}//while		*/

		//Por ahora lo hacemos asi mas croto.
		$meses[0]['id'] = 1; 
		$meses[0]['mes'] = "Enero";
		$meses[1]['id'] = 2; 
		$meses[1]['mes'] = "Febrero";
		$meses[2]['id'] = 3; 
		$meses[2]['mes'] = "Marzo";
		$meses[3]['id'] = 4; 
		$meses[3]['mes'] = "Abril";
		$meses[4]['id'] = 5; 
		$meses[4]['mes'] = "Mayo";
		$meses[5]['id'] = 6; 
		$meses[5]['mes'] = "Junio";
		$meses[6]['id'] = 7; 
		$meses[6]['mes'] = "Julio";
		$meses[7]['id'] = 8; 
		$meses[7]['mes'] = "Agosto";
		$meses[8]['id'] = 9; 
		$meses[8]['mes'] = "Septiembre";
		$meses[9]['id'] = 10; 
		$meses[9]['mes'] = "Octubre";
		$meses[10]['id'] = 11; 
		$meses[10]['mes'] = "Noviembre";
		$meses[11]['id'] = 12; 
		$meses[11]['mes'] = "Diciembre";

		return $meses;
	}
}

?>