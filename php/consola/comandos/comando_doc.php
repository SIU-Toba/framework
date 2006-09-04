<?
require_once('comando_toba.php');

class comando_doc extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de la documentación de Toba';
	}
	
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba doc OPCION");
		$this->consola->enter();
	}

	/**
	 * Descarga la documentación online del wiki desde desarrollos2.siu.edu.ar utilizando httracker
	 */
	function opcion__wiki()
	{
		$destino = toba_dir().'/www/doc/wiki';
		$comando = 'httrack "https://desarrollos2.siu.edu.ar/trac/toba/wiki" -v  -%h -%F "" -I0 -N100 -x %P -O "'.$destino.'" \
					+*.png +*.gif +*.jpg +*.css +*.js  -*login* -*changeset* -*timeline* -*browse* -*roadmap* \
					-*report* -*search* -*history* -*format* -*settings*  -*about* -*ticket* -*query* -*milestone* \
					-*WikiMacros* -*RecentChanges*';
		system($comando);

		$this->consola->mensaje("Convirtiendo codificacion de UTF-8 a ISO88591:");
		//Se buscan los archivos .html del arbol de directorios
		$archivos = manejador_archivos::get_archivos_directorio($destino, "/\\.html/", true);
		foreach ($archivos as $archivo) {
			$this->convertir_codificacion($archivo);
		}
		$this->consola->mensaje("Fin conversión");
	}
	
	/**
	 * Genera la documentación del API en base a los tags phpdoc del código
	 */
	function opcion__api()
	{
		//--- Se incluye a phpdocumentor en el path
		$dir = toba_dir()."/php/3ros";
		$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
		ini_set("include_path", ini_get("include_path"). $separador . $dir);
		
		global $_phpDocumentor_setting;
		$_phpDocumentor_setting['title'] = "Toba API";
		$_phpDocumentor_setting['directory'] = toba_dir().'/php/nucleo/componentes';
		$_phpDocumentor_setting['target'] = toba_dir().'/proyectos/toba_editor/www/doc/api';
//		$_phpDocumentor_setting['output'] = "HTML:Smarty:HandS";
		$_phpDocumentor_setting['ignore'] = 'componente*.php';
		require_once("PhpDocumentor/phpDocumentor/phpdoc.inc");
	}


	protected function convertir_codificacion($archivo)
	{	
		$this->consola->mensaje("\t".$archivo);
		$utf8 = file_get_contents($archivo);
		$iso = iconv("UTF-8", "ISO-8859-1", $utf8);
		file_put_contents($archivo, $iso);
	}
		
}
?>