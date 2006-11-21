<?php 
require_once("modelo/lib/catalogo_items.php");
require_once("tutorial/pant_tutorial.php");

class ci_items extends toba_ci
{
	function conf__arbol(toba_ei_arbol $arbol)
	{
		if (class_exists('contexto_info')) {
			contexto_info::set_proyecto('toba_referencia');
			contexto_info::set_db(toba_instancia::get_db());
			$catalogador = new catalogo_items('toba_referencia');
			$catalogador->cargar(array('menu' => 'SI'));
			$arbol->set_mostrar_utilerias(false);
			//$arbol->set_mostrar_propiedades_nodos(false);
			$arbol->set_datos(array($catalogador->buscar_carpeta_inicial()));
		}
	}
}

class pant_definicion extends pant_tutorial
{

	function generar_layout()
	{
		echo "
			<p>
			Si se piensa la aplicaci�n como un <em>Cat�logo de operaciones</em>, cada una de estas operaciones
			se puede pensar como un <strong>�tem</strong> de este cat�logo. Para una mejor organizaci�n de estos �tems se los incluye en <em>carpetas</em>, conformando
			un �rbol. Por ejemplo se puede definir en el editor:
			</p>
		";
		echo "<div style='width: 400px'>";
		$this->dep('arbol')->generar_html();
		echo "</div>";
		
		echo ' 
			<p>
			Y luego se puede ver el mismo �rbol s�lo que horizontalmente, formando el <strong>men� de la aplicaci�n</strong>:
			</p>
		';
		echo toba_recurso::imagen_proyecto('tutorial/menu.png', true);
	}	
	
}

class pant_creacion extends pant_tutorial
{
	function generar_layout()
	{
		echo mostrar_video('items/items-crear');
	}
}



?>