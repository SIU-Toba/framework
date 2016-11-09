<?php

function cuadro_ayuda($titulo, $iconos, $origen_proyecto=false)
{
	$escapador = toba::escaper();
	foreach ($iconos as $icono) {
		echo '<li>';
		if (count($icono['url']) == 1) {
			foreach ($icono['url'] as $ayuda => $url) {
				echo "<a href='". $escapador->escapeHtmlAttr($url)."' target='". $escapador->escapeHtmlAttr($icono['frame'])."'>";
				if ($origen_proyecto) {
					echo toba_recurso::imagen_proyecto($icono['img'], true, null, null, $ayuda);
				} else {
					echo toba_recurso::imagen_toba($icono['img'], true, null, null, $ayuda);
				}
				echo '</a>';
			}
		} else {
			$ayuda = '<ul>';
			foreach ($icono['url'] as $desc => $url) {
				$ayuda .= "<li><a href=". $escapador->escapeHtmlAttr($url)." target=". $escapador->escapeHtmlAttr($icono['frame']).">". $escapador->escapeHtml($desc);
				$ayuda .= '</a></li>';
			}
			$ayuda .= '</ul>';
			if ($origen_proyecto) {
				echo toba_recurso::imagen_proyecto($icono['img'], true, null, null, $ayuda);
			} else {
				echo toba_recurso::imagen_toba($icono['img'], true, null, null, $ayuda);
			}
		}
		echo "</li>\n";
	}
}
	
$url_trac = get_url_desarrollos();
$url_login = $url_trac.'/trac/toba/login';
$url_referencia = toba_recurso::url_proyecto('toba_referencia');
$escapador = toba::escaper();
echo "
	<style type='text/css'>
		a {
			text-decoration:none;
			color: white;	
		}
		a:hover {
			text-decoration:underline;
		}
		ul { text-align: left; margin-left:30%;
			
		}
		h2 {
		    background-color: white;
		    color: black;
		    padding-left: 10px;
		    margin-top: 20px;
		    border: 1px solid black;
			font-size: 16px;
		}		
		h2 div {
			font-size: 12px;
		}
		li {  
			list-style-type:none;	
			margin-top: 10px;
			font-size: 12px;
		}
		li img {
			vertical-align: middle;
			border: 0;
		}
	</style>
";




/**
 * OFFLINE
 */
echo "
<h2>Offline<div>Contenido correspondiente a esta versión</div></h2>
		<ul>
			<li>
				<a target='api' href='".toba_recurso::url_proyecto()."/doc/api/index.html' title='Documentación de referencia de la API PHP disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/php-small.png'>
						API PHP</a>
				
			</li>
			<li>
				<a target='api_js' href='".toba_recurso::url_proyecto()."/doc/api_js/index.html' title='Documentación de referencia de la API Javascript disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/javascript-small.png'>
						API Javascript</a>
			</li>
			<li>
				<a target='toba_referencia' href='". $escapador->escapeHtmlAttr($url_referencia)."/'>
					<img src='".toba_recurso::imagen_proyecto('referencia_chico.png')."'>			
						Proyecto Referencia</a>
			</li>
		</ul>";

/**
 * ONLINE
 */
echo '
<h2>Online<div>Contenido más actualizado</div></h2>';


	
echo "
	<table border=0 width=100%>
	<tr>
	<td width=50%>
		<ul>
			<li>
				<a target='desarrollos' href='". $escapador->escapeHtmlAttr("$url_trac/trac/toba/newticket*$url_login")."' title='Lugar central de la documentación'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/wiki-small.png'>
						Reportar un bug o mejora</a>
			</li>					
			<li>
				<a target='desarrollos' href='". $escapador->escapeHtmlAttr("$url_trac/trac/toba/wiki*$url_login")."' title='Lugar central de la documentación'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/wiki-small.png'>
						Wiki</a>
			</li>
			<li>
				<a target='desarrollos' href='". $escapador->escapeHtmlAttr("$url_trac/toba_editor_trunk/doc/api/index.html")."' title='Documentación de referencia de la API PHP disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/php-small.png'>
						API PHP</a>
				
			</li>
			<li>
				<a target='desarrollos' href='". $escapador->escapeHtmlAttr("$url_trac/toba_editor_trunk/doc/api_js/index.html")."' title='Documentación de referencia de la API Javascript disponible'>
					<img src='".toba_recurso::url_proyecto()."/doc/api/media/javascript-small.png'>
						API Javascript</a>
			</li>
			<li>
				<a target='desarrollos' href='". $escapador->escapeHtmlAttr("$url_trac/toba_referencia_trunk/?ai=toba_referencia||1000073")."' title='Tutoriales con explicaciones guiadas y videos introductorios'>			
					<img src='".toba_recurso::imagen_proyecto('referencia_chico.png')."'>
					Tutorial</a>
			</li>			
			<li>
				<a target='desarrollos' href='". $escapador->escapeHtmlAttr("$url_trac/toba_referencia_trunk")."/'>
					<img src='".toba_recurso::imagen_proyecto('referencia_chico.png')."'>			
					Proyecto Referencia</a>
			</li>
		<ul>
	</td>
	<td>
		<ul>		
	";

	//--- LINKS
	$online = array();
	$online[] = array('url' => array('Especificación 4.01 (esp)' => 'http://html.conclase.net/w3c/html401-es/cover.html#minitoc'
								),
					'img' => 'botones/html80.png', 'frame' => 'html');
	
	$online[] = array('url' => array('Manual Introductorio (esp)' => 'http://www.sidar.org/recur/desdi/mcss/manual/indice.php',
										'Especificación 2.0 (esp)' => 'http://www.sidar.org/recur/desdi/traduc/es/css/cover.html',
										'Compatibilidad entre navegadores (eng)' => 'http://www.westciv.com/style_master/academy/browser_support/index.html',
										'Tutoriales (eng)' => 'http://css.maxdesign.com.au/',
										'Posicionamiento (eng)' => 'http://www.brainjar.com/css/positioning/default.asp',
										'Soporte CSS en Email (eng)' => 'http://www.campaignmonitor.com/blog/archives/2006/03/a_guide_to_css_1.html',
								),
					'img' => 'botones/css80.png', 'frame' => 'html');
					
	$online[] = array('url' => array('Manual (esp)' => 'http://www.php.net/manual/es/',
								),
					'img' => 'botones/php80.png', 'frame' => 'php');
					
	$online[] = array('url' => array('Manual 8.1 (eng)' => 'http://www.postgresql.org/docs/8.1/interactive/index.html',
									),
					'img' => 'botones/postgres80x15.gif', 'frame' => 'postgres');
					
	$online[] = array('url' => array('Manual 2.2 (esp)' => 'http://httpd.apache.org/docs/2.2/es/',
								),
					'img' => 'botones/apache80.png', 'frame' => 'apache');
					
	$online[] = array('url' => array('Libro (eng)' => 'http://svnbook.red-bean.com/nightly/en/index.html',
								),
					'img' => 'botones/svn80.png', 'frame' => 'svn');
					
					
	cuadro_ayuda('Otros', $online, true);
	
	echo '</ul></td></tr></table>';
?>