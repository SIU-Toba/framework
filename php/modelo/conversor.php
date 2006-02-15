<?
require_once('modelo/lib/elemento_modelo.php');
/*
	Esta clase MANEJA la conversion entre dos versiones del toba

	Hay que pensar un par de tablas para manejar los logs de cambios, versiones, revisiones SVN, etc.
	Estas tablas deberian ser la base de la administracion de conversiones.
*/
class conversor extends elemento_modelo
{
	const dir_conversiones = 'modelo/conversiones';
	protected $instancia;
	protected $db;

	function __construct( $instancia ) 
	{
		$this->instancia = $instancia;	
		$this->db = $this->instancia->get_db();
	}

	private function get_conversion( $version_usuario )
	{
		$version = str_replace( ".", "_", $version_usuario );
		if( self::existe_conversion( $version_usuario ) ) {
			require_once( self::dir_conversiones . "/conversion_$version.php" );
			$clase = "conversion_$version";
			return new $clase( $this->db );
		} else {
			throw new excepcion_toba("La conversion '$version_usuario' no existe.");
		}	
	}

	static function existe_conversion( $version )
	{
		$conversiones = self::get_conversiones_posibles();
		return in_array( $version, $conversiones );
	}

	function get_conversiones_posibles($proyecto=null)
	{
		$conversiones = array();
		$dir = opendir( toba_dir() . '/php/' .  self::dir_conversiones );
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
	
	function ejecutada_anteriormente($proyecto, $version)
	{
		$sql = "SELECT fecha FROM apex_conversion WHERE
						proyecto = '$proyecto' AND
						conversion_aplicada = '$version'
		";
		$rs = $this->db->consultar($sql);
		if (empty($rs)) {
			return false;	
		} else { 
			return $rs[0]['fecha'];
		}
	}	

	function ver_contenido_conversion( $version )
	{
		$conversion = $this->get_conversion( $version );
		$conversion->info();
	}
		
	function procesar( $version, $proyecto, $es_prueba=false )
	{
		$anterior = self::ejecutada_anteriormente( $proyecto, $version );
		if ($anterior) {
			throw new excepcion_toba("CONVERSOR: La conversion ya fue ejecutada en fecha: $anterior\n");
		} else {
			$conversion = $this->get_conversion( $version );
			$obs = $conversion->procesar($proyecto, $es_prueba);
		}
	}
}
?>