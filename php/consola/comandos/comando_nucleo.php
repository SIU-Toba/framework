<?php
require_once('comando_toba.php');
require_once('3ros/diff.php');
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
	 * Dado un release en trunk_versiones genera los archivos zip y tar.gz correspondientes a la version
	 * @consola_parametros -r release -u usuario_svn [-s url_svn] [-t path temporal] [-d path destino] 
	 */
	function opcion__zipversion()
	{
		$url_svn = 'https://repositorio.siu.edu.ar/svn/toba';
		$dir_temp = '/tmp';
		$destino = '/tmp/toba';
		$rama_versiones = 'versiones';
		$rama_branch = 'trunk_versiones';
		

		if (! file_exists($destino)) {
			mkdir($destino);
		}
		
		//Obtengo los parametros de entrada
		$param = $this->get_parametros();		
		if ( !isset($param['-r']) ||  (trim($param['-r']) == '') ) {
			throw new toba_error("Es necesario indicar el release con '-r'");
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
		
		//-- Averiguo cual es el siguiente numero
		$versiones = explode("\n", trim(`svn ls $url_svn/$rama_versiones`));
		$siguiente = new toba_version($release);
		if (! $this->consola->dialogo_simple('Generando archivos de version '.$siguiente->__toString(). " a partir del release $release", 's')) {
			return;
		}

		//Chequeo si el directorio destino es accesible
		if (is_writable($dir_temp) === false) {
			throw new toba_error("El usuario actual no tiene permisos de escritura sobre '$dir_temp'");
		}
	
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
	 * Dado un release en trunk_versiones genera una version en versiones
	 * @consola_parametros -r release -u usuario_svn [-s url_svn] [-t path temporal] [-d path destino] 
	 */
	function opcion__versionar()
	{
		$error = null; $salida = null;		
		$url_svn = 'https://repositorio.siu.edu.ar/svn/toba';
		$dir_temp = '/tmp';
		$destino = '/tmp/toba';		
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
	
		if (! file_exists($destino)) {
			mkdir($destino);
		}
		
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

	function opcion__autoload()
	{
		include_once 'lib/toba_extractor_clases.php';
		$punto_de_montaje = toba_dir().'/php';
		$param = array(
	 		$punto_de_montaje => array(
	 			'archivo_salida' => 'toba_autoload.php',
	 			'dirs_excluidos' => array(
					'3ros',
					'/modelo/template_proyecto',	// Es lo mismo poner o no la barra del principio
					'modelo/migraciones',
					'instalacion',
					'consola'
				),
				'extras' => array(
					'Numbers_Words_es_Ar' => '3ros/Numbers_Words/Words/lang.es_AR.php',
					'toba_migracion' => 'modelo/migraciones/toba_migracion.php',
				)
//	 			'extends_excluidos' => array() <-- opcional. Las clases que extienden de las clases enumeradas acá no serán incluidas
	 		)
		);
		$extractor = new toba_extractor_clases($param);
		$extractor->generar();
//		$extractor->comparar();
	}

	/********************************PERSONALIZACION******************************/

	protected $exclude_fields;
	
	static function get_path_comparacion()
	{
		return toba_dir().'/temp/sql/resultado.txt';
	}

	function opcion__foto_schema($parametro=null)
	{
		$base   = $this->consola->dialogo_ingresar_texto('Introduzca la base donde se encuentra el schema');
		$schema = $this->consola->dialogo_ingresar_texto('Introduzca el schema a sacar la foto');

		$path = toba_dir().'/temp/sql';
		toba_manejador_archivos::crear_arbol_directorios($path);

		$db = $this->get_instalacion()->conectar_base($base);

  		$tablas = $db->get_lista_tablas();

  		foreach($tablas as $tabla) {
  			// Exporto los datos
  			$archivo = toba_dir().'/temp/sql/'.$tabla['nombre'].'.old';
  			$this->exportar_tabla_inserts($base, $schema, $tabla['nombre'], $archivo);
			$this->consola->progreso_avanzar();
  		}
		$this->consola->progreso_fin();
		$this->consola->mensaje("La foto de la base se guardó en $path");
	}

	private function exportar_tabla_inserts($base, $schema, $tabla, $archivo = null)
	{
		$puerto='';
		$params = $this->get_parametros();
		if (isset($params['puerto'])) {
			$puerto= "-p{$params['puerto']} ";
		}
		$comando = "pg_dump -a -i -D -t $schema.$tabla -h 127.0.0.1 -U postgres $base";

		putenv("PGPASSWORD=postgres");

		$salida = array();
		exec($comando, $salida, $exito);

		// chequea si hubo error
		if ($exito > 0) {
			throw new toba_error('No se pudo exportar correctamente los datos');
		}

		// guarda en el archivo si vino como parámetro
		if($archivo) {
			$arch = fopen($archivo,"w");
			fputs($arch,implode("\n",$salida));
			fclose($arch);
		}

		return $salida;
	}

	/**
	 * Compara el schema actual con la última foto que se sacó. 
	 */
	function opcion__compara_foto_schema()
	{
		$base   = $this->consola->dialogo_ingresar_texto('Introduzca la base donde se encuentra el schema');
		$schema = $this->consola->dialogo_ingresar_texto('Introduzca el schema');
		$db = $this->get_instalacion()->conectar_base($base);
		$tablas = $db->get_lista_tablas();

		// abro el achivo donde van los resultados
		$archivo_salida = self::get_path_comparacion();
		$salida = fopen($archivo_salida, 'w');

		// Para cada tabla hago la comparación
		foreach($tablas as $tabla){
			// datos viejos: levanto del archivo y ordeno
			$archivo_old = toba_dir().'/temp/sql/'.$tabla['nombre'].'.old';
			$cont = file_get_contents($archivo_old);
			$datos_old = explode("\n", $cont);
			sort($datos_old);

			// datos nuevos, recupero y ordeno
			$datos = $this->exportar_tabla_inserts($base, $schema, $tabla['nombre']);
			sort($datos);

			// Calculo la diferencia y muestro
			$diff = diff::PHPDiff($datos_old,$datos);

			if(count($diff['I'])||count($diff['D'])||count($diff['U']))
				fputs($salida,"==============================================\nTabla: {$tabla['nombre']}\n");
			if(count($diff['I']))
				fputs($salida,"----------------------------------------------\nInserts:\n");

			foreach($diff['I'] as $s){
				fputs($salida,$s."\n");
			}

			if(count($diff['D']))
				fputs($salida,"----------------------------------------------\nDeletes:\n");

			foreach($diff['D'] as $s){
				fputs($salida,$s."\n");
			}

			if(count($diff['U']))
				fputs($salida,"----------------------------------------------\nUpdates:\n");

			foreach($diff['U'] as $s){
				fputs($salida,"_____\n".$s['D']."\n".$s['I']."\n_____\n");
			}

			$this->consola->progreso_avanzar();
	//}
		}
		fclose($salida);
		$this->consola->progreso_fin();
	}

	/**
	 * Crea un caso de test de personalización a partir de la salida de compara_foto_schema
	 */
	function opcion__crear_caso_test()
	{
		$nombre = $this->consola->dialogo_ingresar_texto('Ingrese el nombre del caso de test');
		$archivo_fuente = self::get_path_comparacion();

		$array = $this->salida_como_array($archivo_fuente);

		$clase	= new toba_codigo_clase("$nombre", "toba_pers_caso_test");
		$prop	= new toba_codigo_propiedad_php('$sql', 'protected', '', $array);
		$metodo = new toba_codigo_metodo_php('get_descripcion', array(), array('Descripción del caso de test'));
		$metodo->set_contenido("return '';");
		$clase->agregar($prop);
		$clase->agregar($metodo);

		$path = toba_dir()."/proyectos/toba_testing/personalizacion/tests/$nombre.php";
		$clase->guardar($path);
		$this->consola->mensaje("El caso de test se guardó en $path");
	}

	private function salida_como_array($salida_path)
	{
		$salida_path = toba_dir().'/temp/sql/resultado.txt';

		$cont = file_get_contents($salida_path);
		$datos = explode("\n", $cont);

		$this->init_exclude_fields();
		$estado = 'pasar';
		$salida = "array(";
		foreach ($datos as $key => $linea) {
			if (comienza_con($linea, 'Tabla: ')) {	// tenemos que sacar el nombre de la tabla
				$tabla = str_replace('Tabla: ', '', $linea);
				$estado = 'nueva_tabla';

			} elseif (comienza_con($linea, 'Inserts:')) {
				$estado = 'leyendo_inserts';
				$salida .= "\n\t\t// tabla: $tabla\n";

			} elseif (comienza_con($linea, 'Updates:')) {
				$estado = 'leyendo_updates';
				$salida .= "\n\t\t// tabla: $tabla";

			} elseif (comienza_con($linea, 'INSERT INTO')) {
				if ($estado == 'leyendo_inserts') {
					$insert = $this->crear_insert($tabla, $linea);
					$salida .= "\n\t\t\"$insert\",\n";
					
				} elseif ($estado == 'leyendo_updates') {
					$siguiente_linea = $datos[$key + 1];	// tiene que estar seteado por el formato que tiene la salida
					$update = $this->crear_update($tabla, $linea, $siguiente_linea);
					$salida .= "\n\t\t\"$update\",\n";
					$estado = 'pasar';
				}
			}
		}
		$salida .= "\n\t)";

		return $salida;
	}

	private function crear_insert($tabla, $raw_insert)
	{
		$col_string = '(';
		$index_col = strpos($raw_insert, $col_string) + strlen($col_string);
		$end_col_string = ') VALUES';
		$pos_end_col_string = strpos($raw_insert, $end_col_string) - $index_col;	// la posición se calcula con respecto a la posición de arranque de las columnas
		$cols = explode(',', substr($raw_insert, $index_col, $pos_end_col_string));

		$val_string = 'VALUES (';
		$index_val = strpos($raw_insert, $val_string) + strlen($val_string);
		$values = explode(',', substr($raw_insert, $index_val, -2));	// con -2 sacamos el paréntesis que cierra y el ;

		// Limpiamos los null
		foreach ($values as $key => $value) {
			if (trim($value) == 'NULL') {
				unset($cols[$key]);
				unset($values[$key]);
			}
		}

		// Borramos los campos autoincrement
		foreach ($cols as $key => $value) {
			if ($this->excluir_campo($tabla, $value)) {
				unset($cols[$key]);
				unset($values[$key]);
			}
		}

		// generamos el sql
		$cols_imp = implode(', ', $cols);
		$vals_imp = implode(', ', $values);
		$sql = "INSERT INTO $tabla ($cols_imp) VALUES ($vals_imp);";
		return $sql;
	}

	/**
	 * Este método es muy específico ya que depende de un formato especial. No recomiendo su lectura
	 * @ignore
	 */
	private function crear_update($tabla, $old, $new)
	{
		$col_string = '(';
		$index_col = strpos($old, $col_string) + strlen($col_string);
		$end_col_string = ') VALUES';
		$pos_end_col_string = strpos($old, $end_col_string) - $index_col;	// la posición se calcula con respecto a la posición de arranque de las columnas
		$cols = explode(', ', substr($old, $index_col, $pos_end_col_string));

		$val_string = 'VALUES (';
		$old_index_val = strpos($old, $val_string) + strlen($val_string);
		$new_index_val = strpos($new, $val_string) + strlen($val_string);
		$old_values = explode(',', substr($old, $old_index_val, -2));	// con -2 sacamos el paréntesis que cierra y el ;
		$new_values = explode(',', substr($new, $new_index_val, -2));	// con -2 sacamos el paréntesis que cierra y el ;

		$set	= array();
		$where	= array();
		foreach ($cols as $key => $col) {
			$new_value = trim($new_values[$key]);
			$old_value = trim($old_values[$key]);

			if ($new_value != $old_value) {
				$set[] = "$col = $new_value";
			}

			if ($old_value != 'NULL') {
				$where[] = "$col = $old_value";
			}
		}

		$set_string		= implode(', ', $set);
		$where_string	= implode(' AND ', $where);
		$sql = "UPDATE $tabla SET $set_string WHERE $where_string;";

		return $sql;
	}

	protected function init_exclude_fields()
//	function opcion__obtener_campos_seq()
	{
		$sql = "
			SELECT table_name, column_name, column_default
			FROM information_schema.columns
			WHERE table_schema = 'desarrollo' AND ((column_default LIKE '%seq\"''::text)::regclass)')
				OR (column_default LIKE '%seq''::text)::regclass)'))
		";

		$db = $this->get_instalacion()->conectar_base('toba_trunk');
		$this->exclude_fields = $db->consultar($sql);
	}

	protected function excluir_campo($tabla, $nombre_campo)
	{
		foreach ($this->exclude_fields as $campo) {
			if ($campo['table_name'] == $tabla && $campo['column_name'] == $nombre_campo) {
				return true;
			}
		}

		return false;
	}

}
?>