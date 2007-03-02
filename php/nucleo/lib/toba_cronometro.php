<?php
//-------------> Armar niveles de ejecucion
define("apex_nivel_nucleo","nucleo");
define("apex_nivel_objeto","objeto");
define("apex_nivel_item","item");
define("apex_nivel_lib","lib");

/**
 * Permite medir el tiempo consumido entre marcas y opcionalmente registrarlo en la base de la instancia
 * @package Debug
 */
class toba_cronometro
{
	protected $marcas;
	protected $indice;
	static private $instancia;
	
	private function __construct() {
		$this->indice = 0;
		global $cronometro;
		$cronometro = $this;
		$this->marcar("Creacion cronometro.");
	}
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_cronometro();	
		}
		return self::$instancia;
	}	

	function marcar($nombre,$nivel="item"){
		$microtime = explode(' ', microtime());
		$this->marcas[$this->indice]["t"] = $microtime[1] . substr($microtime[0], 1);
		$this->marcas[$this->indice]["n"] = $nombre;
		$this->marcas[$this->indice]["niv"] = $nivel;
		$this->indice++;
	}

    function tiempo_acumulado() {
		$ultimo = (count($this->marcas)-1);
		return (($this->marcas[$ultimo]['t']) - ($this->marcas[0]['t']));
    }

	function get_marcas()
	{
		$marcas = array();
		$marca_anterior = $this->marcas[0]['t'];
		for($f=0;$f<count($this->marcas);$f++)
		{
			$marca_actual = $this->marcas[$f]['t'];
			$marcas[$f]['texto'] = $this->marcas[$f]['n'];
			$marcas[$f]['tiempo'] = number_format(($marca_actual - $marca_anterior),3,'.','');
			//$marcas[$f]['nivel'] = $this->marcas[$f]['niv'];
			$marca_anterior = $marca_actual;
		}
		return $marcas;
	}

	/**
	 * 	Guardar el la base las marcas del CRONOMETRO
	 */
	function registrar($proyecto, $solicitud)
	{
		$this->marcar("Fin del cronómetro");
		//dump_arbol($this->marcas);
		$temp = $this->marcas[0]['t'];
		for($f=0;$f<count($this->marcas);$f++)
		{
			//echo "$f-1 TEMP: $temp<br>";
			$momento = $this->marcas[$f]['t'];
			//echo "$f-2 MOME: $momento<br>";
			$tiempo = number_format(($momento - $temp),3,'.','');
			//echo "$f-3 TIEM: $tiempo<br>";
			if($this->marcas[$f]['n']!="INICIO"){
				toba::instancia()->registrar_marca_cronometro($proyecto, $solicitud, $f, $this->marcas[$f]['niv'], $this->marcas[$f]['n'], $tiempo);
			}
			$temp = $momento;
		}
	}
}
?>