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
		echo "
			Es muy similar a crear un �tem. Dentro del editor del proyecto:
			<ol>
				<li>Desde el men� principal ir a la vista de �tems: 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/boton_items.png', true)."</span></li>
				<li>Expandir las utiler�as de una carpeta donde estar� contenido el �tem (por ejemplo la raiz): 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/nuevo_item_expandir.png', true)."</span></li>
				<li>Presionar el �cono de creaci�n de �tem: 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/nuevo_item_icono.png', true)."</span></li>
				<li>En las propiedadese b�sicas indicar el nombre del �tem (por ejemplo <em>Pago a terceros</em>) e indicar que forma parte del men�: <span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/nuevo_item_basicas.png', true)."</span></li>
				<li>Presionar <strong>Guardar</strong>: 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/nuevo_item_guardar.png', true)."</span></li>
				<li>En el �rbol de la izquierda se puede visualizar el �tem creado. Ahora es posible previsualizar el �tem en la aplicaci�n: 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/nuevo_item_creado.png', true)."</span></li>
			</ol>
		";
	}
}


class pant_carpeta extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			Dentro del editor del proyecto:
			<ol>
				<li>Desde el men� principal ir a la vista de �tems: 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/boton_items.png', true)."</span></li>
				<li>Expandir las utiler�as de una carpeta donde estar� contenida la carpeta (por ejemplo la raiz): 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/nuevo_item_expandir.png', true)."</span></li>
				<li>Presionar el �cono de creaci�n de carpeta: 
						<span class='screenshot'>".toba_recurso::imagen_proyecto('tutorial/nuevo_item_icono.png', true)."</span></li>
				<li>El resto de los pasos son iguales a los de un �tem com�n</li>
			</ol>
		";
	}	
}

?>