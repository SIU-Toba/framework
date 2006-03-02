<?
require_once('comando_toba.php');

class comando_doc extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de la documentaci�n de Toba';
	}
	
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba doc OPCION");
		$this->consola->enter();
	}

	/**
	 * Descarga la documentaci�n online del wiki desde desarrollos2.siu.edu.ar utilizando httracker
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
		$this->consola->mensaje("Fin conversi�n");
	}
	
	/**
	 * Genera la documentaci�n del API en base a los tags phpdoc del c�digo
	 */
	function opcion__api()
	{
		global $_phpDocumentor_setting;
		$_phpDocumentor_setting['title'] = "Toba API";
		$_phpDocumentor_setting['directory'] = toba_dir().'/php/nucleo/componentes/runtime';
		$_phpDocumentor_setting['target'] = toba_dir().'/www/doc/api';
		$_phpDocumentor_setting['output'] = "HTML:Smarty:HandS";
		$_phpDocumentor_setting['ignore'] = '__*.php,eventos.php,objeto_ci_abm.php,objeto_cuadro*.php,objeto_esquema*.php,objeto_filtro.php,objeto_grafico.php,objeto_hoja*.php,objeto_mt*.php,objeto_ut*.php,objeto_plan.php,objeto_lista.php,objeto_mapa.php,objeto_ei_multicheq.php,objeto_html.php';
		require_once("3ros/phpdocumentor/phpDocumentor/phpdoc.inc");
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