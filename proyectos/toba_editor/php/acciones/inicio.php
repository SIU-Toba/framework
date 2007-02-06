<?php
require_once('modelo/instalacion.php');
require_once('admin_util.php');

echo "<div style='margin-top: 30%;margin-bottom: 30%;'>";
echo toba_recurso::imagen_proyecto('logo.gif', true);
echo "</div>";

//ei_arbol($_SESSION, 'SESION', null, true);

//--- VERSION

$url_trac = get_url_desarrollos();
$url_login = $url_trac.'/trac/toba/login';

$version = instalacion::get_version_actual();
$cambios = "$url_trac/trac/toba/wiki/Versiones/".$version->__toString();
echo "<div style='position: fixed; _position:absolute;right: 0; bottom:0; padding: 4px;background-color:white;border: 1px solid gray'>";
//echo "<span style='font-size:10px;font-weight:bold;'>toba</span> ";
$ayuda = toba_recurso::ayuda(null, "Ver log de cambios introducidos en esta versión");
echo "<a target='wiki' style='text-decoration:none' href='$cambios' $ayuda>Versión ";
echo $version->__toString()."</a>";
echo "</div>";

	if (! isset($_POST['migracion'])) {
		echo toba_form::abrir('toba', toba::vinculador()->crear_autovinculo());
		echo "<div style='position:fixed;left:0;bottom:0;'>";
		echo toba_form::submit('migracion', "Chequear compatibilidad extensiones");
		echo "</div>";
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
		
		$dir = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado());
		$archivos = toba_manejador_archivos::get_archivos_directorio( $dir, '/\.php$/', true);
		echo "<h2>Métodos obsoletos</h2> (no busca por extender_objeto_js de los cis)";
		echo "<ul style='list-style-type:none; text-align:left;'>";
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
						$icono = toba_recurso::imagen_toba('descripcion.gif', true, null, null, $ayuda);
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
