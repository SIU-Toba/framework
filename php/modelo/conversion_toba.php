<?
/*
	Hay que pensar un par de tablas para manejar los logs de cambios, versiones, revisiones SVN, etc.
	Estas tablas deberian ser la base de la administracion de conversiones.
*/

class conversion
/*
	Esta clase representa una conversion entre dos versiones del toba
*/
{
	protected $version;
	protected $log;
	protected $db;
	protected $proyecto;			//Proyecto sobre el que se corren las conversiones
	protected $reg_afectados = 0;	//Cantidad de registros afectados por la conversion

	//-------- STATICAS
	
	static function existe_conversion($version)
	{
		$conversiones = self::conversiones_posibles();
		return in_array($version, $conversiones);
	}
	
	static function conversiones_posibles($proyecto=null)
	{
		$conversiones = array();
		$dir = opendir(dirname(__FILE__));
		while(($archivo = readdir($dir)) !== false)  
		{  
			if (ereg("conversion_(.+).php", $archivo, $version)) {
				if ($version[1] != 'toba') {
					$conversiones[] = str_replace("_", ".", $version[1]);
				}
			}
		}		
		//Si se pide un proyecto, filtra las ya aplicadas 
		if (isset($proyecto)) {
			foreach ($conversiones as $id => $version) {
				if (self::ejecutada_anteriormente($proyecto, $version)) {
					unset($conversiones[$id]);
				}
			}
		}
		return $conversiones;
	}
	
	
	static function ejecutada_anteriormente($proyecto, $version)
	{
		$sql = "SELECT fecha FROM apex_conversion WHERE
						proyecto = '$proyecto' AND
						conversion_aplicada = '$version'
		";
		$rs = toba::get_db('instancia')->consultar($sql);
		if (empty($rs)) {
			return false;	
		} else { 
			return $rs[0]['fecha'];
		}
	}	
	
	//--- DINAMICAS
	
	function __construct()
	{
		$this->version = $this->get_version();
		$this->db = toba::get_db('instancia');
//		$this->db->debug = true;
	}
		
	
	public function info()
	{
		echo $this->info_cambio(new ReflectionClass(get_class($this)))."\n";
		$cambios = $this->get_lista_cambios();
		foreach ($cambios as $cambio) {
			$com = $this->info_cambio($cambio);
			if ($com != "")
				echo $cambio->getName().": ".$com."\n\n";	
		}
	}
	
	protected function info_cambio($cambio)
	{
		return parsear_doc_comment( $cambio->getDocComment() );
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
			if ($es_prueba) {
				$this->ejecutar_sql("ROLLBACK TRANSACTION");
			} else {
				$this->cerrar_conversion();
				$this->ejecutar_sql("COMMIT TRANSACTION");
			}
			echo "Registros afectados: {$this->reg_afectados}\n";			
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
	*	Marca la conversion como ejecutada para el proyecto actual
	*	Evitando que se corra dos veces
	*/
	protected function cerrar_conversion()
	{
		$sql = "INSERT INTO apex_conversion
					(proyecto, conversion_aplicada, fecha) VALUES
					('{$this->proyecto}', '{$this->version}', CURRENT_TIMESTAMP)";
		$this->ejecutar_sql($sql);
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
		$aff =  $this->db->ejecutar($sql);
		$this->reg_afectados += $aff;
	}
}
?>