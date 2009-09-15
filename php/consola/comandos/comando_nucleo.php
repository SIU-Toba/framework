<?php
require_once('comando_toba.php');
/**
*	Publica los servicios de la clase NUCLEO a la consola toba
*/
class comando_nucleo extends comando_toba
{
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba nucleo OPCION");
		$this->consola->enter();
	}
		
	static function get_info()
	{
		return 'Administracion de la informacion perteneciente al nucleo del sistema';
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	* Genera la metadata necesaria para los exportadores.
	* @gtk_icono exportar.png 
	*/
	function opcion__parsear_ddl()
	{
		$this->get_nucleo()->parsear_ddl();
	}

	/**
	* Migra la DDL de toba a la sintaxis de otros motores.
	* @gtk_icono exportar.png 
	*/
	function opcion__migrar_ddl()
	{
		$this->get_nucleo()->migrar_ddl();
	}
	
	/**
	* Exporta las tablas maestras del sistema. 
	* @consola_parametros [-i id_instancia] o variable 'toba_instancia'
	* @gtk_icono exportar.png
	*/
	function opcion__exportar_datos()
	{
		//Tomo la referencia a la instancia
		$instancia = $this->get_instancia();
		$this->get_nucleo()->exportar( $instancia );
	}
	
	/**
	 * Actualiza los objetos info en base a los editores de los objetos
	 * @gtk_icono exportar.png 
	 */
	function opcion__parsear_editores()
	{
		//Tomo la referencia a la instancia
		$instancia = $this->get_instancia();
		$this->get_nucleo()->parsear_editores( $instancia );		
	}
	
	/**
	 * Comprime el codigo js
	* @gtk_icono extension_zip.png 
	 */
	function opcion__comprimir_js()
	{
		//Tomo la referencia a la instancia
		$this->get_nucleo()->comprimir_js();
	}
	
	/**
	 * Valida el javascript utilizando jslint
	 * @consola_parametros [-a patron inclusion] [-b patron de exclusion]
	 * @gtk_icono tilde.gif
	 */
	function opcion__validar_js()
	{
		$param = $this->get_parametros();		
		$patron_incl = null;
		$patron_excl = null;
		if ( isset($param['-a']) &&  (trim($param['-a']) != '') ) {
			$patron_incl = $param['-a'];
		}
		if ( isset($param['-b']) &&  (trim($param['-b']) != '') ) {
			$patron_excl = $param['-b'];
		}
		
		//Tomo la referencia a la instancia
		$this->get_nucleo()->validar_js($patron_incl, $patron_excl);
	}		

	/**
	 * Reune las definiciones de los componentes en un solo archivo
	* @gtk_icono compilar.png 
	 */
	function opcion__compilar()
	{
		$this->get_nucleo()->compilar();		
	}
	
	/**
	 * Dado un release en trunk_versiones genera una version en versiones
	 * @consola_parametros -r release -u usuario_svn [-s url_svn] [-t path temporal] [-d path destino] 
	 */
	function opcion__versionar()
	{
		$error = null; $salida = null;		
		$url_svn = 'http://localhost/svn/toba';
		$dir_temp = '/tmp';
		$destino = '/var/www/downloads/toba';		
		$rama_branch = 'trunk_versiones';
		$rama_versiones = 'versiones';
		$mensaje_commit = 'Rama %s: Preparacion lanzamiento version %s';
		$mensaje_copy = "Lanzamiento Version %s:";
		$param = $this->get_parametros();		
		if ( !isset($param['-r']) ||  (trim($param['-r']) == '') ) {
			throw new toba_error("Es necesario indicar el release con '-r'");
		}
		if ( !isset($param['-u']) ||  (trim($param['-u']) == '') ) {
			throw new toba_error("Es necesario indicar el usuario svn con '-u'");
		}
		if (isset($param['-s']) && (trim($param['-s']) != '') ) {
			$url_svn = $param['-s'];
		}
		if (isset($param['-t']) && (trim($param['-t']) != '') ) {
			$dir_temp = $param['t'];
		}
		if (isset($param['-d']) && (trim($param['-d']) != '') ) {
			$destino = $param['d'];
		}		
		$release = $param['-r'];		
		$usuario = $param['-u'];
	
		//-- Averiguo cual es el siguiente numero
		$versiones = explode("\n", trim(`svn ls $url_svn/$rama_versiones`));
		$siguiente = new toba_version($release.'.0');
		foreach ($versiones as $numero) {
			$version = new toba_version(str_replace('/', '', $numero));
			if ($version->es_cambio_menor_version($siguiente) && $version->es_mayor_igual($siguiente)) {
				$siguiente = $version->get_siguiente_menor();
			}
		}
		if (! $this->consola->dialogo_simple('Lanzando version '.$siguiente->__toString(). " a partir del release $release", 's')) {
			return;
		}
	
		//-- Pongo ese numero en el archivo VERSION (de todos los proyectos)
		$this->consola->mensaje("Checkout y commit del archivo VERSION de $rama_branch/$release .", false);
		if (is_writable($dir_temp) === false) {
			throw new toba_error("El usuario actual no tiene permisos de escritura sobre '$dir_temp'");
		}
		$co_temp = $dir_temp.'/'.uniqid('toba_');
		`svn co --username $usuario --non-recursive $url_svn/$rama_branch/$release $co_temp`;
		$this->consola->progreso_avanzar();
		file_put_contents($co_temp.'/VERSION', $siguiente->__toString());

		//-- Arma mensaje de commit
		$mensaje_commit = utf8_encode(sprintf($mensaje_commit, $release, $siguiente->__toString()));
		$cmd = "svn ci $co_temp -m '$mensaje_commit'";
		exec($cmd, $salida, $error);
		if ($error) {
			throw new toba_error("No fue posible hacer el commit. Comando:\n$cmd");
		}
		$this->consola->progreso_fin();
		
		//-- Hago el copy a versiones
		$this->consola->mensaje("Haciendo copy a $rama_versiones.", false);
		$mensaje_copy = sprintf($mensaje_copy, $siguiente->__toString());
		if (file_exists($co_temp.'/notas_version.txt')) {
			//-- Incluye en el commit el changelog de la versión
			$notas_version = file_get_contents($co_temp.'/notas_version.txt');
			$resultado = preg_split('/===\s*(\d+\.\d+\.\d+)\s*===/i', $notas_version, null, 2);			
			if (isset($resultado) && is_array($resultado)) {
				foreach ($resultado as $i => $nota) {
					if ($nota == $siguiente->__toString()) {
						$mensaje_copy .= $resultado[$i + 1];
						break;
					}
				}
			}
		}
		$archivo_msg = tempnam($dir_temp, 'log_svn');
		file_put_contents($archivo_msg, $mensaje_copy);
		$cmd = "svn cp --username $usuario $url_svn/$rama_branch/$release $url_svn/$rama_versiones/$siguiente -F $archivo_msg";
		exec($cmd, $salida, $error);
		toba_manejador_archivos::eliminar_directorio($co_temp);
		unlink($archivo_msg);
		if ($error) {
			throw new toba_error("No fue posible hacer el copy. Comando:\n$cmd");
		}
		$this->consola->progreso_fin();
		
		//-- Hago el export a una carpeta
		$this->consola->mensaje("Export a carpeta temporal.", false);
		$export_dir = $dir_temp."/toba_$siguiente"; 
		if (file_exists($export_dir)) {
			toba_manejador_archivos::eliminar_directorio($export_dir);
		}
		$cmd = "svn export $url_svn/$rama_versiones/$siguiente $export_dir";
		exec($cmd, $salida, $error);
		if ($error) {
			toba_manejador_archivos::eliminar_directorio($export_dir);
			throw new toba_error("No fue posible hacer el export. Comando:\n$cmd");
		}		
		$this->consola->progreso_fin();
		
		//-- Armo el .zip 
		$this->consola->mensaje("Creando ZIP.", false);
		if (file_exists("$destino/toba_$siguiente.zip")) {
			unlink("$destino/toba_$siguiente.zip");
		}
		$cmd = "cd $dir_temp; zip -r $destino/toba_$siguiente.zip toba_$siguiente";
		exec($cmd, $salida, $error);
		if ($error) {
			toba_manejador_archivos::eliminar_directorio($export_dir);
			throw new toba_error("Error armando el .zip. Comando:\n$cmd");
		}
		$this->consola->progreso_fin();
		
		//-- Armo el .tar.gz
		$this->consola->mensaje("Creando TAR.GZ.", false);
		if (file_exists("$destino/toba_$siguiente.tar.gz")) {
			unlink("$destino/toba_$siguiente.tar.gz");
		}
		$cmd = "cd $dir_temp; tar -czvf $destino/toba_$siguiente.tar.gz toba_$siguiente";
		exec($cmd, $salida, $error);
		if ($error) {
			toba_manejador_archivos::eliminar_directorio($export_dir);
			throw new toba_error("Error armando el .tar.gz. Comando:\n$cmd");
		}
		$this->consola->progreso_fin();
		
		//-- Borro temporales
		$this->consola->mensaje("Borrando archivos temporales.", false);
		toba_manejador_archivos::eliminar_directorio($export_dir);
		$this->consola->progreso_fin();
	}
	
	/**
	 * Arma los .tar.gz y .zip de todas las versiones lanzadas
	 */
	function opcion__comprimir_versiones()
	{
		$error = null; $salida = null;		
		$url_svn = 'http://localhost/svn/toba';
		$dir_temp = '/tmp';
		$destino = '/var/www/downloads/toba';		
		$rama_branch = 'trunk_versiones';
		$rama_versiones = 'versiones';
		
		//-- Averiguo cual es el siguiente numero
		$versiones = explode("\n", trim(`svn ls $url_svn/$rama_versiones`));
		foreach ($versiones as $numero) {
			$siguiente = new toba_version(str_replace('/', '', $numero));
			$this->consola->mensaje("Comprimiendo version ".$siguiente->__toString(), false);
			
			//-- Hago el export a una carpeta
			$this->consola->mensaje("Export a carpeta temporal.", false);
			$export_dir = $dir_temp."/toba_$siguiente"; 
			if (file_exists($export_dir)) {
				toba_manejador_archivos::eliminar_directorio($export_dir);
			}
			$cmd = "svn export $url_svn/$rama_versiones/$siguiente $export_dir";
			exec($cmd, $salida, $error);
			if ($error) {
				toba_manejador_archivos::eliminar_directorio($export_dir);
				throw new toba_error("No fue posible hacer el export. Comando:\n$cmd");
			}		
			$this->consola->progreso_fin();
			
			//-- Armo el .zip 
			$this->consola->mensaje("Creando ZIP.", false);
			if (file_exists("$destino/toba_$siguiente.zip")) {
				unlink("$destino/toba_$siguiente.zip");
			}
			$cmd = "cd $dir_temp; zip -r $destino/toba_$siguiente.zip toba_$siguiente";
			exec($cmd, $salida, $error);
			if ($error) {
				toba_manejador_archivos::eliminar_directorio($export_dir);
				throw new toba_error("Error armando el .zip. Comando:\n$cmd");
			}
			$this->consola->progreso_fin();
			
			//-- Armo el .tar.gz
			$this->consola->mensaje("Creando TAR.GZ.", false);
			if (file_exists("$destino/toba_$siguiente.tar.gz")) {
				unlink("$destino/toba_$siguiente.tar.gz");
			}
			$cmd = "cd $dir_temp; tar -czvf $destino/toba_$siguiente.tar.gz toba_$siguiente";
			exec($cmd, $salida, $error);
			if ($error) {
				toba_manejador_archivos::eliminar_directorio($export_dir);
				throw new toba_error("Error armando el .tar.gz. Comando:\n$cmd");
			}
			$this->consola->progreso_fin();
			
			//-- Borro temporales
			$this->consola->mensaje("Borrando archivos temporales.", false);
			toba_manejador_archivos::eliminar_directorio($export_dir);
			$this->consola->progreso_fin();			
		}
	}	
	
}
?>