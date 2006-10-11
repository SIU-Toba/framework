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
		$destino = toba_dir().'/proyectos/toba_editor/www/doc/wiki';		
		
		//--- Se borra lo viejo (para que se de cuenta svn)
		$lista = toba_manejador_archivos::get_archivos_directorio($destino, "/\\.html/", true);
		foreach ($lista as $arch) {
			unlink($arch);
		}

		//--- Se baja de la web		
		$comando = 'httrack "https://localhost/trac/toba/wiki" -v  -%h -%F "" -I0 -N100 -x %P -O "'.$destino.'" \
					+*.png +*.gif +*.jpg +*.css +*.js  -*login* -*changeset* -*timeline* -*browse* -*roadmap* \
					-*report* -*search* -*history* -*format* -*settings*  -*about* -*ticket* -*query* -*milestone* \
					-*WikiMacros* -*RecentChanges* -*/Desarrollo* +*png?format=raw* -*sandbox* -*reuniones*';
		system($comando);

		//-- Busca el archivo css del wik y modifica algunos estilos
		$cambios = "
			#altlinks, #search, #header, #metanav, #ctxtnav, #mainnav {
				display: none;
			}
			#footer {
				display: none;
			}
			td.header-menu{ background-color: #BD0000; font-size: 8pt; padding: 2px; padding-right: 5px; text-align: right; 
				border: 1px solid #333366; color: white;
			}
			td.header-top-left{font-size: 16pt; font-weight: bold; padding: 10px; text-align: left; }
			td.header-top-center{ font-size: 16pt; font-weight: bold; padding: 10px; text-align: right;
				padding-top: 40px;
			 }
			td.header-top-right{ font-size: 16pt; font-weight: bold; padding: 10px; text-align: right; }
			
			.menu { color:white;}
			body { margin: 0; padding: 0; }
			div.wiki { padding-left: 10px; }			
		";
		$archivo_css = $destino."/trac/toba/chrome/common/css/trac.css";
		file_put_contents($archivo_css, $cambios, FILE_APPEND);
		
		//--- Busca los html y les agrega la 'barra' de navegacion
		$html_inicio = "
			<body>
			<table border='0' cellspacing='0' cellpadding='0' height='48' width='100%'>
			  <tr>
			
				<td class='header-top-left'><img src='{BASE}api/media/logo.png' border='0'/></td>
			    <td class='header-top-right'>
			    		<img border='0' style='vertical-align: middle' src='{BASE}api/media/wiki.png' />
			    	<a href='{BASE}api/index.html'  title='Navegar hacia la documentaciÃ³n PHP'>
			    		<img border='0' style='vertical-align: middle' src='{BASE}api/media/php-small.png' /></a> 
			    	<a href='{BASE}api_js/index.html'  title='Navegar hacia la documentaciÃ³n Javascript'>    		
			    	<img border='0' style='vertical-align: middle' src='{BASE}api/media/javascript-small.png' /></a>
			  </tr>
			
			  <tr>
			    <td colspan='2' class='header-menu'>
			  		  [ <a href='http://desarrollos2.siu.edu.ar{ACTUAL}' title='El sitio online contiene contenidos mas detallados y actualizados'
						class='menu'>Ver online</a> ]
			    </td>
			
			  </tr>
			  <tr><td colspan='2' class='header-line'><img src='{BASE}api/media/empty.png' width='1' height='1' border='0' alt=''  /></td></tr>
			</table>
		";
		/*$html_fin = '
			<div class="credit">
    			    <hr class="separator" />Desarrollado por <a href="http://www.siu.edu.ar">SIU</a>. <br />        
		            Documentación generada con <a href="http://trac.edgewall.org/">Trac</a>
		         </div>
			 </body>';*/
		 
		$archivos = toba_manejador_archivos::get_archivos_directorio($destino, "/\\.html/", true);
		$cant = count($archivos);
		$this->consola->mensaje("Convirtiendo $cant archivos");		
		foreach ($archivos as $archivo) {
			$contenido = file_get_contents($archivo);
			
			//--- Convierte un nombre de archivo en una url para navegar online
			$url = str_replace($destino, '', $archivo);
			$url = str_replace('.html', '', $url);
			$reemplazo = str_replace('{ACTUAL}', $url, $html_inicio);
			
			//--- Busca la cantidad de barras en el archivo actual y hace la misma cantidad de retrocesos (../)
			$base = str_repeat('../', substr_count($url, '/'));
			$reemplazo = str_replace('{BASE}', $base, $reemplazo);
			
			//--- Escribe los reemplazos
			$contenido = str_replace('<body>', $reemplazo, $contenido);
			//$contenido = str_replace('</body>', $html_fin, $contenido);
			file_put_contents($archivo, $contenido);
		}
	}
	
	/**
	 * Genera la documentación del API en base a los tags phpdoc del código
	 */
	function opcion__api()
	{
		
		$dest = toba_dir().'/proyectos/toba_editor/www/doc/api';

		$lista = toba_manejador_archivos::get_archivos_directorio($dest, "/\\.html/", true);
		foreach ($lista as $arch) {
			unlink($arch);
		}
		
		//--- Se incluye a phpdocumentor en el path
		$dir = toba_dir()."/php/3ros";
		$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
		ini_set("include_path", ini_get("include_path"). $separador . $dir);
		
		global $_phpDocumentor_setting;
		$_phpDocumentor_setting['title'] = "API PHP";
		//$_phpDocumentor_setting['directory'] = toba_dir().'/php/nucleo/,'.toba_dir().'/php/lib/,';
		$_phpDocumentor_setting['directory'] = toba_dir().'/php/nucleo/componentes/';
		$_phpDocumentor_setting['target'] = $dest;
		$_phpDocumentor_setting['output'] = "HTML:Smarty:toba_hands";
		$_phpDocumentor_setting['defaultpackagename'] = 'Centrales';
		$_phpDocumentor_setting['customtags'] = 'jsdoc,wiki,ventana';
		//$_phpDocumentor_setting['output'] = "HTML:frames:DOM/toba";
		$_phpDocumentor_setting['ignore'] = 'componente*.php';
		require_once("PhpDocumentor/phpDocumentor/phpdoc.inc");

		//-- La clase toba es la clase inicial (como esta un nivel mas adentro hay que bajar un nivel menos)
		$indice = file_get_contents($dest.'/Centrales/toba.html');
		$indice = str_replace('../../', '#BASE#', $indice);
		$indice = str_replace('../', '', $indice);
		$indice = str_replace('#BASE#', '../', $indice);
		file_put_contents($dest.'/index.html', $indice);
		
		$this->convertir_codificacion_dir($dest, "ISO-8859-1", "UTF-8");		
	}

	/**
	 * Genera la documentación del API Javascript
	 * Utiliza jsdoc (http://jsdoc.sourceforge.net/)
	 */	
	function opcion__api_js()
	{
		$destino = toba_dir().'/proyectos/toba_editor/www/doc/api_js';
		$lista = toba_manejador_archivos::get_archivos_directorio($destino, "/\\.html/", true);
		foreach ($lista as $arch) {
			unlink($arch);
		}
			
		$directorios = toba_dir().'/www/js/basicos ';
		$directorios .= toba_dir().'/www/js/componentes ';
		$directorios .= toba_dir().'/www/js/efs/ef* ';
		
		$cmd = "perl ".toba_dir().
				"/bin/herramientas/JSDoc/jsdoc.pl --globals-name GLOBALES ".
				"--recursive --directory $destino --no-sources ".
				"--project-name \"SIU-Toba\" $directorios ";
		system($cmd);

		//-- La clase toba es la clase inicial
		copy($destino.'/toba.html', $destino.'/index.html');
		$this->convertir_codificacion_dir($destino, "ISO-8859-1", "UTF-8");
	}
	

	protected function convertir_codificacion($archivo, $desde, $hasta)
	{	
		$this->consola->mensaje_directo(".");
		$utf8 = file_get_contents($archivo);
		$iso = iconv($desde, $hasta, $utf8);
		file_put_contents($archivo, $iso);
	}

	
	protected function convertir_codificacion_dir($destino, $desde="UTF-8", $hasta="ISO-8859-1")
	{
		//Se buscan los archivos .html del arbol de directorios
		$archivos = toba_manejador_archivos::get_archivos_directorio($destino, "/\\.html/", true);
		$cant = count($archivos);
		$this->consola->mensaje("Convirtiendo $cant archivos de codificacion $desde a $hasta:");		
		foreach ($archivos as $archivo) {
			$this->convertir_codificacion($archivo, $desde, $hasta);
		}
		$this->consola->mensaje("Fin conversión");
	}
}
?>