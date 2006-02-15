<?
require_once('nucleo/lib/manejador_archivos.php');
/*
*	Sincroniza el arbol de archivos manejado con la generacion
*	automatica de archivos basados en la base.
*		( Cuando algo se elimina en la base, un archivo se deja de
*			generar, esta clase se encarga de que una baja en la base
*			este sincronizada con una baja en el sistema de archivos (fs o svn)
*/
class sincronizador_archivos
{
	
	private $tipo_manejo;
	private $dir;
	private $patron_archivos;
	private $archivos_originales;
	private $archivos_utilizados;
	private $archivos_agregados;
	private $archivos_eliminados;
	private $activado = true;
	
	function __construct( $dir, $patron_archivos = null )
	{
		$this->dir = $dir;
		$this->patron_archivos = $patron_archivos;
		$this->tipo_manejo = $this->resolver_tipo();
		//echo "SINCRONIZADOR ** DIR: $dir - TIPO: " . $this->tipo_manejo ." **\n" ;
		if ( ! is_dir( $this->dir ) ) {
			$this->activado = false;		
		} else {
			$this->cargar_archivos_originales();			
		}
	}
	
	private function cargar_archivos_originales()
	{
		$this->archivos_originales = manejador_archivos::get_archivos_directorio( 	$this->dir, 
																					$this->patron_archivos, 
																					true );
	}
	
	function resolver_tipo()
	{
		$dir_svn = $this->dir . '/.svn';
		if ( is_dir( $dir_svn ) ) {
			return 'svn';	
		} else {
			return 'fs';	
		}
	}
		
	/*
	*	Indica la generacion de un archivo
	*/
	function agregar_archivo( $archivo )
	{
		$this->archivos_utilizados[] = $archivo;
	}
	
	function sincronizar()
	{	
		if( ! $this->activado ) {
			return array("El directorio '$this->dir' no existe.");
		}
		$this->archivos_agregados = array_diff( $this->archivos_utilizados, $this->archivos_originales );
		$this->archivos_eliminados = array_diff( $this->archivos_originales, $this->archivos_utilizados );
		if ( $this->tipo_manejo == 'svn' ) {
			return $this->sincro_svn();
			//print_r( $this->archivos_originales );
		} elseif ( $this->tipo_manejo == 'fs' ) {
			return $this->sincro_fs();
		}
	}
	
	function sincro_svn()
	{
		/*
			Faltan los controles de que el SVN exista en el path
		*/
		$obs = array();
		foreach ( $this->archivos_eliminados as $archivo ) {
			system("svn delete --force $archivo");
			$obs[] = "SVN DELETE '$archivo'";
		}
		foreach ( $this->archivos_agregados as $archivo ) {
			/*
				Falta agregar los padres al SVN
				en el caso de que se exporte un componente que nunca se exporto,
				se crea la carpeta del componente
			*/
			system("svn add $archivo");
			$obs[] = "SVN ADD '$archivo'";
		}
		return $obs;
	}
	
	function sincro_fs()
	{
		$obs = array();
		foreach ( $this->archivos_eliminados as $archivo ) {
			unlink( $archivo );
			$obs[] = "SINCRO: eliminar '$archivo'";
		}
		return $obs;
	}
}
?>