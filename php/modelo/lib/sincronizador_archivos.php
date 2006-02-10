<?
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
	private $archivos;
	
	function __construct( $dir, $tipo_manejo='svn' )
	{
		$this->dir = $dir;
		if ( $tipo_manejo != 'fs' && $tipo_manejo != 'svn' ) {
			throw new excepcion_toba("SINCRONIZADOR: Los tipos de manejo posibles son: svn, fs. (Tipo solicitado: '$tipo_manejo')");
		}
		$this->tipo_manejo = $tipo_manejo;
	}
	
	/*
	*	Indica la generacion de un archivo
	*/
	function agregar_archivo( $archivo )
	{
		$this->archivos = $archivo;
	}
	
	function procesar()
	{
		
	}
}

?>