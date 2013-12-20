<?php
/**
*	Sincroniza el arbol de archivos manejado con la generacion
*	automatica de archivos basados en la base.
*		( Cuando algo se elimina en la base, un archivo se deja de
*			generar, esta clase se encarga de que una baja en la base
*			este sincronizada con una baja en el sistema de archivos (fs o svn)
* @package Varios
*/
class toba_sincronizador_archivos
{
	
	private $tipo_manejo;
	private $dir;
	private $patron_archivos;
	private $archivos_originales;
	private $archivos_utilizados;
	private $archivos_agregados = array();
	private $archivos_eliminados = array();
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
	
	function info()
	{
		return $this->archivos_originales;	
	}
	
	private function cargar_archivos_originales()
	{
		$this->archivos_originales = toba_manejador_archivos::get_archivos_directorio( 	$this->dir, 
																					$this->patron_archivos, 
																					true );
	}
	
	function resolver_tipo()
	{
		if (is_dir($this->dir) && toba_svn::es_copia_trabajo($this->dir)) {
			return 'svn';
		}else{
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
		if (is_array($this->archivos_utilizados) && is_array($this->archivos_originales)) {
			$this->archivos_agregados = array_diff( $this->archivos_utilizados, $this->archivos_originales );
			$this->archivos_eliminados = array_diff( $this->archivos_originales, $this->archivos_utilizados );
		}
		return $this->persistir_cambios();
	}

	function sincronizar_agregados()
	{
		if( ! $this->activado ) {
			return array("El directorio '$this->dir' no existe.");
		}
		$this->archivos_agregados = array_diff( $this->archivos_utilizados, $this->archivos_originales );
		return $this->persistir_cambios();
	}

	function sincronizar_eliminados()
	{
		if( ! $this->activado ) {
			return array("El directorio '$this->dir' no existe.");
		}
		$this->archivos_eliminados = array_diff( $this->archivos_originales, $this->archivos_utilizados );
		return $this->persistir_cambios();
	}
	
	private function persistir_cambios()
	{
		if ( $this->tipo_manejo == 'svn' ) {
			return $this->sincro_svn();
			//print_r( $this->archivos_originales );
		} elseif ( $this->tipo_manejo == 'fs' ) {
			return $this->sincro_fs();
		}
	}
	
	function sincro_svn()
	{
		$obs = array();
		foreach ( $this->archivos_eliminados as $archivo ) {
			$cmd = "svn delete --force \"$archivo\"";
			system($cmd);
			toba_logger::instancia()->info("Sincronizacion SVN. Comando: $cmd");
			$obs[] = "SVN DELETE '$archivo'";
		}
		foreach ( $this->archivos_agregados as $archivo ) {
			/*
				Falta agregar las carpetas SVN padre
				Por ahora contempla solo un nivel
			*/
			//--Agrega el directorio padre
			$directorio = dirname($archivo);
			system("svn add $directorio -N -q");


			$cmd = "svn add \"$archivo\"";
			toba_logger::instancia()->info("Sincronizacion SVN. Comando: $cmd");			
			system($cmd);
			$obs[] = "SVN ADD '$archivo'";
		}
		return $obs;
	}
	
	function sincro_fs()
	{
		$obs = array();
		foreach ( $this->archivos_eliminados as $archivo ) {
			unlink( $archivo );
			toba_logger::instancia()->info("Sincronizacion: Eliminando archivo $archivo");			
			$obs[] = "SINCRO: eliminar '$archivo'";
		}
		return $obs;
	}
}
?>