<?
/*
	Hay que pensar un par de tablas para manejar los logs de cambios, versiones, revisiones SVN, etc.
	Estas tablas deberian ser la base de la administracion de conversiones.
*/

class conversion_toba
/*
	Esta clase representa una conversion entre dos versiones del toba
*/
{
	protected $version;
	protected $log;
	protected $db;
	protected $proyecto;		//Proyecto sobre el que se corren las conversiones

	function __construct()
	{
		$this->version = $this->get_version();
		$this->db = toba::get_db('instancia');
//		$this->db->debug = true;
	}
	
	static function existe_conversion($version)
	{
		$conversiones = self::conversiones_posibles();
		return in_array($version, $conversiones);
	}
	
	static function conversiones_posibles()
	{
		$conversiones = array();
		$dir = opendir(dirname(__FILE__));
		while(($archivo = readdir($dir)) !== false)  
		{  
			if (ereg("conversion_(.+).php", $archivo, $version)) {
				if ($version[1] != 'toba') {
					$conversiones[] = $version[1];
				}
			}
		}		
		return $conversiones;
	}
	
	public function info()
	{
		$cambios = $this->get_lista_cambios();
		foreach ($cambios as $cambio) {
			$com = $this->info_cambio($cambio);
			if ($com != "")
				echo $cambio->getName().": ".$com."\n\n";	
		}
	}
	
	protected function info_cambio($cambio)
	{
		$com = $cambio->getDocComment();
	    $com = preg_replace("/(^[\\s]*\\/\\*\\*)
	                                 |(^[\\s]\\*\\/)
	                                 |(^[\\s]*\\*?\\s)
	                                 |(^[\\s]*)
	                                 |(^[\\t]*)/ixm", "", $com);
	
	    $com = str_replace("\r", "", $com);
	    $com = trim(preg_replace("/([\\t])+/", "\t", $com));
		return $com;
	}
	
	/**
		Dispara los metodos que empiezan con "cambio_" dentro de una transaccion
		Si todo sale ok, deja un log de que los cambios impactaron en el sistema
	*/
	public function procesar($proyecto=null, $es_prueba = false)
	{
		$this->proyecto = $proyecto;
		$logger = toba::get_logger();
		$cambios = $this->get_lista_cambios();
		try {
			$this->ejecutar_sql("BEGIN TRANSACTION");
			$this->pre_cambios();
			foreach ($cambios as $cambio) {
				$cambio->invoke($this);
				$logger->info($cambio->getName()."...OK");
			}
			$this->post_cambios();
			if ($es_prueba)
				$this->ejecutar_sql("ROLLBACK TRANSACTION");
			else
				$this->ejecutar_sql("COMMIT TRANSACTION");			
		} catch (excepcion_toba $e) {
			$this->ejecutar_sql("ROLLBACK TRANSACTION");
			$e->agregar_mensaje($cambio->getName()."...ERROR\n");
			$logger->error($this->info_cambio($cambio));
			throw $e;
		}
		$logger->guardar_en_archivo("conversion_{$this->version}.log");
	}
	
	protected function pre_cambios()
	{
	}
	
	protected function post_cambios()
	{
	}
	
	/**
		Busca todos los metodos que empiezan con "cambio_"
	*/	
	protected function get_lista_cambios()
	{
		$cambios = array();
		$clase = new ReflectionClass(get_class($this));
		foreach ($clase->getMethods() as $metodo) {
			if (substr($metodo->getName(), 0, 7) == 'cambio_')
				$cambios[] = $metodo;
		}
		return $cambios;
	}
	
	/**
		Ejecuta el SQL y arma un LOG
	*/	
	protected function ejecutar_sql($sql)
	{
		$res = $this->db->Execute($sql);
		if (!$res)
			throw new excepcion_toba("Error al ejecutar la conversion: $sql \n ".$this->db->ErrorMsg());
	}
}
?>