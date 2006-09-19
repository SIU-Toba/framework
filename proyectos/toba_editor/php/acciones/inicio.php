<?php
echo '<div style="text-align:left">';
	require_once('modelo/instalacion.php');
	require_once('admin_util.php');
	
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
					echo toba_recurso::imagen_apl($icono['img'], true, null, null, $ayuda);
					echo "</a>";
				}
			} else {
				$ayuda = "<ul>";
				foreach ($icono['url'] as $desc => $url) {
					$ayuda .= "<li><a href=$url target={$icono['frame']}>$desc";
					$ayuda .= "</a></li>";
				}
				$ayuda .= "</ul>";
				echo toba_recurso::imagen_apl($icono['img'], true, null, null, $ayuda);
			}
			echo "</li>\n";
		}
		echo "</ul>";
		echo "</div>";
	}
	
	//--- LINKS
	echo "<div style='display: block;clear:both;'>";
	$online = array();
	$online[] = array('url' => array("Especificación 4.01 (esp)" => "http://html.conclase.net/w3c/html401-es/cover.html#minitoc"
								),
					'img' => 'admin/botones/html80.png', 'frame' => 'html');
	
	$online[] = array('url' => array("Manual Introductorio (esp)" => 'http://www.sidar.org/recur/desdi/mcss/manual/indice.php',
										"Especificación 2.0 (esp)" => 'http://www.sidar.org/recur/desdi/traduc/es/css/cover.html',
										"Compatibilidad entre navegadores (eng)" => 'http://www.westciv.com/style_master/academy/browser_support/index.html',
										"Tutoriales (eng)" => 'http://css.maxdesign.com.au/',
										"Posicionamiento (eng)" => 'http://www.brainjar.com/css/positioning/default.asp',
										"Soporte CSS en Email (eng)" => 'http://www.campaignmonitor.com/blog/archives/2006/03/a_guide_to_css_1.html',
								),
					'img' => 'admin/botones/css80.png', 'frame' => 'html');
					
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
	
	
	$url_trac = admin_util::get_url_desarrollos();
	$url_login = $url_trac.'/trac/toba/login';
	$trac = array();
	$trac[] = array('url' => array('Documentación online de la última versión' => $url_trac.'/trac/toba/wiki/*'.$url_login,
								),
					'img' => 'admin/botones/wiki80.png', 'frame' => 'trac');
	$trac[] = array('url' => array('Planificación de versiones futuras' => $url_trac.'/trac/toba/roadmap/*'.$url_login,
								),
					'img' => 'admin/botones/roadmap80.png', 'frame' => 'trac');
	$trac[] = array('url' => array('Bugs y mejoras pendientes' => $url_trac.'/trac/toba/report/3/*'.$url_login,
								),
					'img' => 'admin/botones/tickets80.png', 'frame' => 'trac');
	$trac[] = array('url' => array('Línea de tiempo del proyecto' =>$url_trac.'/trac/toba/timeline/*'.$url_login,
								),
							'img' => 'admin/botones/timeline80.png', 'frame' => 'trac');
	$trac[] = array('url' => array('Documentación de la API de la última versión' => $url_trac.'/toba_trunk/doc/api/index.html',
								),
					'img' => 'admin/botones/api80.png', 'frame' => 'api');					
	cuadro_ayuda("Sitio Web", $trac);
	
	
	$offline = array();
	$offline[] = array('url' => array(toba_recurso::path_pro()."/doc/api/index.html",
								),
						'img' => 'admin/botones/apioffline80.png', 'frame' => 'api');
	$offline[] = array('url' => array(toba_recurso::path_pro()."/doc/wiki/trac/toba/wiki.html",
								),
						'img' => 'admin/botones/wikioffline80.png', 'frame' => 'wiki');					
	cuadro_ayuda("Ayuda Local", $offline);
	
	echo "</div>";
	
	//--- VERSION
	$version = instalacion::get_version_actual();
	$cambios = "$url_trac/trac/toba/wiki/Versiones/".$version->__toString();
	echo "<div style='position: fixed; _position:absolute;right: 0; bottom:0; padding: 4px;background-color:white;border: 1px solid gray'>";
	//echo "<span style='font-size:10px;font-weight:bold;'>toba</span> ";
	$ayuda = toba_recurso::ayuda(null, "Ver log de cambios introducidos en esta versión");
	echo "<a target='wiki' style='text-decoration:none' href='$cambios' $ayuda>Versión ";
	echo $version->__toString()."</a>";
	echo "</div>";
	
	echo "<div style='padding:4px; background-color: white;border: 1px solid gray'>";
	echo  toba_parser_ayuda::parsear("Conectado a la [wiki:Referencia/Instancia Instancia]  <strong>".toba_instancia::get_id()."</strong> :");
	echo "<ul style='margin-top: 0px;'>";
	
	foreach (toba::db()->get_parametros() as $clave => $valor) {
		echo "<li>".ucfirst($clave).": $valor</li>";	
	}
	echo "</ul>";
	echo "</div>";

	
	if (! isset($_POST['migracion'])) {
		echo toba_form::abrir('toba', toba::vinculador()->crear_autovinculo());
		echo "<ul><li>";
		echo toba_form::submit('migracion', "Chequear compatibilidad extensiones entre versiones 0.9 y 1.0");
		echo "</li></ul>";
		echo toba_form::cerrar();
	} else {
		echo "<hr style='clear:both'><h1 style='text-align:center'>Chequeo de compatibilidad de extensiones</h1>";		
		//------------------ ID de PANTALLAS e EIS  -----------------
		$sql = "
			SELECT
				pant.identificador		as id,
				pant.objeto_ci			as padre
			FROM
				apex_objeto_ci_pantalla pant,
				apex_objeto_dependencias dep
			WHERE
					pant.identificador = dep.identificador		-- Mismo id
				AND	pant.objeto_ci_proyecto = dep.proyecto		-- Mismo proy.
				AND pant.objeto_ci = dep.objeto_consumidor		-- Mismo CI padre
				AND dep.proyecto = '".toba_editor::get_proyecto_cargado()."'
		";
		$rs = contexto_info::get_db()->consultar($sql);
		if (! empty($rs)) {
			echo "<h2>Pantallas y eis que comparten el mismo id</h2><ul>";
			foreach ($rs as $conflicto) {
				echo "<li>CI {$conflicto['padre']}: {$conflicto['id']}</li>";
			}
			echo "</ul>";
		}
		
		//------------------ METODOS OBSOLETOS -----------------
		//--- Busca archivos sin migrar 
		$prohibidos['get_lista_ei'] = 'Usar $this->pantalla()->agregar_dep o eliminar_dep en la configuración.';
		$prohibidos['get_lista_eventos(']= 'Usar $this->pantalla()->agregar_evento, modificar_evento o eliminar_evento en la configuración.';
		$prohibidos['get_lista_eventos (']= $prohibidos['get_lista_eventos('];
		$prohibidos['get_pantalla_actual']= 'Usar $this->set_pantalla() en la configuración.';
		$prohibidos['get_lista_tabs']= 'Si se usaba solo para obtener información ahora se puede hacer con $this->pantalla()->get_lista_tabs, 
										si se usaba para redefinir usar agregar_tab o eliminar_tab de $this->pantalla';
		$prohibidos['evt__post_recuperar_interaccion']= 'Definir post_eventos()';
		$prohibidos['evt__pre_cargar_datos_dependencias']= 'Definir el metodo conf() del ci';
		$prohibidos['evt__post_cargar_datos_dependencias']= 'Ya no es necesario ya que las dependencias se pueden cargar con un metodo';
		$prohibidos['obtener_html']= 'Se cambio por generar_html. Si era de un ci el método se delego a las pantalla asoaciada';
		$prohibidos['obtener_html_contenido']= 'Se cambio por generar_html_contenido. Si era de un ci el método se delego a las pantalla asoaciada';
		$prohibidos['get_etapa_actual']= 'Usar $this->set_pantalla() en la configuración.';
		$prohibidos['__cant_reg']= 'El paginado del cuadro a cargo del CI no se hace mas con el evento cant_reg sino configurandolo explicitamente con el metodo del cuadro set_total_registros.';
		$prohibidos['cargar_editable']= 'Usar set_editable';
		$prohibidos['inicializar(']= 'Si es un ci, definir el metodo ini()';
		$prohibidos['persistir_dato_global'] = 'Usar set_dato_operacion o set_dato_aplicacion según corresponda';
		$prohibidos['filtrar_evt__'] = '';
		$prohibidos['modificar_vinculo__'] = '';
		$prohibidos['__puede_mostrar_pantalla'] = 'Setear la pantalla correcta en el conf() del ci. Ver el ejemplo del proyecto referencia.';
		$prohibidos['eventos::'] = 'Definir los eventos en el administrador. Luego es posible manipularlos ya que es una clase toba_evento_usuario (ej. en el ci $this->pantalla()->evento(\'tal\')->)';
		
		$dir = toba_instancia::get_path_proyecto(toba_editor::get_proyecto_cargado());
		$archivos = manejador_archivos::get_archivos_directorio( $dir, '/\.php$/', true);
		echo "<h2>Métodos obsoletos</h2> (no busca por extender_objeto_js de los cis)";
		echo "<ul style='list-style-type:none'>";
		foreach ($archivos as $archivo ) {
			if ($archivo !== __FILE__) {
				$contenido = file_get_contents($archivo);
				$encontrados = array();
				foreach (array_keys($prohibidos) as $prohibido) {
					if (strpos($contenido, $prohibido) !== false) {
						$encontrados[] = $prohibido;
					}
				}
				
				$encontrados = array_unique($encontrados);
				$path = substr($archivo, strpos($archivo, 'php')+4);
				if (! empty($encontrados)) {
					$icono = admin_util::get_icono_abrir_php($path);
					echo "<li>$icono <strong>$path</strong>:<ul style='list-style-type:none'>";
					foreach ($encontrados as $metodo) {
						$ayuda = $prohibidos[$metodo];
						$icono = toba_recurso::imagen_apl('descripcion.gif', true, null, null, $ayuda);
						echo "<li>$icono $metodo</li>";
					}
					echo "</ul></li>";
				}
			}
		}
		echo "</ul>";
		echo '</div>';
		
	}
?>
