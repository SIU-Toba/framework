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

		//-- Vista previa		
		//echo '<link rel="stylesheet" href="'. toba_recurso::url_proyecto('toba_editor') . '/js/packages/highlight.js/styles/default.css"'. '>';
		//echo '<script src="' .toba_recurso::url_proyecto('toba_editor') . '/js/packages/highlight.js/highlight.pack.js"'. '></script>';
		
		$codigo = $this->controlador()->get_previsualizacion();
		$escapador = toba::escaper();		
		echo "<div class='editor-vista-previa-codigo'>";
		echo '<pre><code class="php">'. $escapador->escapeHTML($codigo) . '</pre></code>';
		echo '</div>';		
	}

	function extender_objeto_js()
	{
		echo "
			var nodos = document.getElementsByTagName('code');				
			for (i = 0; i < nodos.length; i++) {
				hljs.highlightBlock(nodos[i]);					
			}
		"; 
		
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