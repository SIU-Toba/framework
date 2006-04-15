<?php

function cuadro_ayuda($titulo, $iconos)
{
	echo "<div style='background-color:white;border:1px solid black;float:right;margin: 5px;'>";
	echo "<div style='text-align:center;background-color:#524883;color:white;font-weight:bold'>$titulo</div>";
	echo "<ul style='list-style-type: none;padding: 4px;margin: 0; '>";
	foreach ($iconos as $icono) {
		echo "<li>";
		if (count($icono['url']) == 1) {
			foreach ($icono['url'] as $ayuda => $url) {
				echo "<a href='$url' target='{$icono['frame']}'>";
				echo recurso::imagen_apl($icono['img'], true, null, null, $ayuda);
				echo "</a>";
			}
		} else {
			$ayuda = "<ul>";
			foreach ($icono['url'] as $desc => $url) {
				$ayuda .= "<li><a href=$url target={$icono['frame']}>$desc";
				$ayuda .= "</a></li>";
			}
			$ayuda .= "</ul>";
			echo recurso::imagen_apl($icono['img'], true, null, null, $ayuda);
		}
		echo "</li>\n";
	}
	echo "</ul>";
	echo "</div>";
}

							
echo "<div style='display: block;'>";
$online = array();
$online[] = array('url' => array("Manual Introductorio (esp)" => 'http://www.sidar.org/recur/desdi/mcss/manual/indice.php',
									"Especificación 2.0 (esp)" => 'http://www.sidar.org/recur/desdi/traduc/es/css/cover.html'
							),
				'img' => 'admin/botones/css80.png', 'frame' => 'html');
				
$online[] = array('url' => array("Especificación 4.01 (esp)" => "http://html.conclase.net/w3c/html401-es/cover.html#minitoc"
							),
				'img' => 'admin/botones/html80.png', 'frame' => 'html');
				
$online[] = array('url' => array('Manual (esp)' => 'http://www.php.net/manual/es/',
							),
				'img' => 'admin/botones/php80.png', 'frame' => 'php');
				
$online[] = array('url' => array('Manual 8.1 (eng)' => 'http://www.postgresql.org/docs/8.1/interactive/index.html',
								),
				'img' => 'admin/botones/postgres80x15.gif', 'frame' => 'postgres');
				
$online[] = array('url' => array("Manual 2.2 (esp)" => 'http://httpd.apache.org/docs/2.2/es/',
							),
				'img' => 'admin/botones/apache80.png', 'frame' => 'apache');
				
$online[] = array('url' => array("Libro (eng)" => 'http://svnbook.red-bean.com/nightly/en/index.html',
							),
				'img' => 'admin/botones/svn80.png', 'frame' => 'svn');
				
cuadro_ayuda("Otros", $online);

$trac = array();
$trac[] = array('url' => array('Documentación online de la última versión' => 'http://desarrollos2.siu.edu.ar/trac/toba/login',
							),
				'img' => 'admin/botones/wiki80.png', 'frame' => 'trac');
$trac[] = array('url' => array('Planificación de versiones futuras' => 'https://desarrollos2.siu.edu.ar/trac/toba/roadmap',
							),
				'img' => 'admin/botones/roadmap80.png', 'frame' => 'trac');
$trac[] = array('url' => array('Bugs y mejoras pendientes' => 'https://desarrollos2.siu.edu.ar/trac/toba/report/3',
							),
				'img' => 'admin/botones/tickets80.png', 'frame' => 'trac');
$trac[] = array('url' => array('Línea de tiempo del proyecto' =>'https://desarrollos2.siu.edu.ar/trac/toba/login',
							),
						'img' => 'admin/botones/timeline80.png', 'frame' => 'trac');
cuadro_ayuda("Sitio Web", $trac);


$offline = array();
$offline[] = array('url' => array(recurso::path_apl()."/doc/api/index.html",
							),
					'img' => 'admin/botones/apioffline80.png', 'frame' => 'trac');
$offline[] = array('url' => array(recurso::path_apl()."/doc/wiki/trac/toba/wiki.html",
							),
					'img' => 'admin/botones/wikioffline80.png', 'frame' => 'trac');					
cuadro_ayuda("Ayuda Local", $offline);

echo "</div>";




//ei_arbol( dba::get_info_db_instancia(), "Parametros de conexion de la instancia");
		
		//---------------------------------//
			
	//Mostrar la revision utilizada
	echo "<pre>";
		$proyecto  = $this->hilo->obtener_proyecto();
		if( $proyecto != "toba" ){
			echo "		revision SVN toba: " . revision_svn(  $this->hilo->obtener_path() ) . "
		revision SVN $proyecto: " . revision_svn($this->hilo->obtener_proyecto_path() );
		}
   echo "</pre>";


?>