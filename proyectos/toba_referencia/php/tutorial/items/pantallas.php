<?php 
require_once("modelo/lib/catalogo_items.php");
class ci_items extends toba_ci
{
	function conf__arbol(toba_ei_arbol $arbol)
	{
		contexto_info::set_proyecto('toba_referencia');
		contexto_info::set_db(toba_instancia::get_db());
		$catalogador = new catalogo_items('toba_referencia');
		$catalogador->cargar_todo();
		$arbol->set_mostrar_utilerias(false);
		//$arbol->set_mostrar_propiedades_nodos(false);
		$arbol->set_datos(array($catalogador->buscar_carpeta_inicial()));
	}
}

class pant_1 extends toba_ei_pantalla
{
	function generar_layout()
	{
		echo "
			<p>
			Si se ve a la aplicación como un <em>Catálogo de operaciones</em>, cada una de estas
			se puede pensar como un <strong>ítem</strong> de este catálogo. Para una mejor organización de estos ítems se los organiza en <strong>carpetas</strong>, conformando
			un árbol de operaciones. Por ejemplo:
			</p>
		";
		echo "<div style='width: 400px'>";
		$this->dep('arbol')->generar_html();
		echo "</div>";
	}	
	
}



?>