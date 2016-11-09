<?php 
class pant_vista_previa extends toba_ei_pantalla
{
	function generar_layout()
	{
		//--- Barra SVN
		echo "<div class='editor-vista-previa'>".$this->controlador()->get_info_archivo();
		echo '<div>';
		$this->generar_botones_eventos(true);
		echo '</div></div>';

		//--- Barra de convenciones
		$mensajes_linea = array();
		$modelo = toba_editor::get_modelo_proyecto();
		$estandar = $modelo->get_estandar_convenciones();
		$path = $this->controlador()->get_path_archivo();
		if (file_exists($path)) {
			$resultado = $estandar->validar(array($path));
			$titulo = '<strong>Convenciones</strong>';
			$cant_errores = $resultado['totals']['errors'];
			$cant_warnings = $resultado['totals']['warnings'];
			$extra = '';
			if ($cant_errores === 0 && $cant_warnings === 0) {
				$nivel = 'info';
				$salida = 'Ok!';
			} else {
				$nivel = ($cant_errores !== 0) ? 'error' : 'warning';
				$salida = "$cant_errores ";
				$salida .= ($cant_errores !== 1) ? 'errores' : 'error';
				$salida .= " / $cant_warnings ";
				$salida .= ($cant_warnings !== 1) ? 'avisos' : 'aviso';
				foreach ($resultado['files'][$path]['messages'] as $linea => $columnas) {
					$textos = array();
					foreach ($columnas as $column => $mensajes) {
						foreach ($mensajes as $mensaje) {
							//$extra .= "<strong>$linea</strong>:{$mensaje['message']}";
							$imagen = ($mensaje['type'] == 'ERROR') ? 'error.gif' : 'warning.gif';
							$textos[] = $estandar->parsear_mensaje($mensaje['message']);	//Cambia el id de la convencion por una url
						}
					}
					$ayuda = implode('<br><br>', $textos);
					$ayuda = str_replace("'", "`", $ayuda);
					$mensajes_linea[$linea] = toba_recurso::imagen_toba($imagen, true, null, null, $ayuda);
				}
			}
			$this->generar_html_descripcion("$titulo: $salida $extra", $nivel);
		}

		//-- Vista previa
		echo "<div class='editor-vista-previa-codigo'>";
		$codigo = $this->controlador()->get_previsualizacion();
		require_once(toba_dir().'/php/3ros/PHP_Highlight.php');
		$h = new PHP_Highlight(false);
		$h->loadString($codigo);
		$formato_linea = "<span class='editor-linea-codigo'>%02d</span>&nbsp;&nbsp;";
		echo @$h->toHtml(true, true, $formato_linea, true, $mensajes_linea);
		echo '</div>';
	}

	function extender_objeto_js()
	{
		if ($this->existe_evento('trac_ver')) {
			$escapador = toba::escaper();
			$path = $this->controlador()->get_path_archivo();
			$svn = new toba_svn();
			$url = $svn->get_url($path);
			$proyecto = toba_editor::get_proyecto_cargado();
			$url = preg_replace('/svn\/(\w+)/i', 'trac/$1/browser', $url);
			echo $escapador->escapeJs($this->objeto_js)
				.".evt__trac_ver = function() {
					var opciones = {'scrollbars' : 1, 'resizable': 1};
					abrir_popup('trac', '". $escapador->escapeJs($url)."', opciones);
					return false;
				}
			";
		}
	}
}

?>