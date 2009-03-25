<?php 
require_once('tutorial/pant_tutorial.php');

class ci_items extends toba_ci
{

}

//----------------------------------------------------------

class pant_definicion extends pant_tutorial
{

	function generar_layout()
	{
		echo "
			<p>
			Si se piensa la aplicación como un <em>Catálogo de operaciones</em>, cada una de estas operaciones
			se la puede pensar como un <strong>ítem</strong> de este catálogo. Para una mejor organización de estos ítems se los incluye en <em>carpetas</em>, conformando
			un árbol. Por ejemplo se puede definir el siguiente árbol de ítems en el editor:
			</p>
		";
		echo toba_recurso::imagen_proyecto('tutorial/item-arbol.png', true);
		echo ' 
			<p>
			Y luego se puede ver el mismo árbol sólo que horizontalmente, formando el <strong>menú de la aplicación</strong>:
			</p>
		';
		echo toba_recurso::imagen_proyecto('tutorial/menu.png', true);
		echo '
			<p>Ahora veremos cómo armar este árbol a partir de dos acciones:
				<ul><li>Creación de Carpetas</li><li>Creación de Items</li></ul>
			</p>
		';
	}	
	
}

//----------------------------------------------------------

class pant_creacion extends pant_tutorial
{
	function generar_layout()
	{
		echo mostrar_video('items-crear');
	}
}

//----------------------------------------------------------


class pant_php_plano extends pant_tutorial
{
	function generar_layout()
	{
		echo "<p>
				Una vez creado un ítem podemos asociarle comportamientos:
				<ul>
				 <li><em>Con Componentes</em>: Es la forma recomendada para la mayoría de las operaciones, se ve más adelante en el tutorial.</li>
				 <li><em>Programando su comportamiento</em>: Para casos de operaciones de consola, en lote o que tengan una salida gráfica totalmente irregular
				 		 en donde el esquema de componentes no alcanza.</li>
				 </ul>
			</p>
			<p>
			La <strong>ejecución de PHP Plano</strong> cubre el caso de comportamiento totalmente programado o <em>ad-hoc</em>. 
			En este caso se asocia al ítem un archivo en el sistema de archivos y en él se programa la operación de la forma tradicional en PHP. 
			</p>
			
			<p>Se puede definir el archivo en las propiedades básica de la operación
			</p>
			";
			echo "<div style='text-align: center'>";
			echo toba_recurso::imagen_proyecto('tutorial/item-php-plano.png', true);
			echo "</div>";
			$vinculo = toba::vinculador()->get_url(null, 1000077);
			echo "<p>
				El código puede contener referencias a todo el API de toba, exceptuando a los componentes.
				<a href='$vinculo' target='_blank'>Ver ejemplo</a>
			</p>";

	}	
}

//--------------------------------------------------------

class pant_masinfo extends pant_tutorial 
{
	function generar_layout()
	{
		$wiki1 = toba_parser_ayuda::parsear_wiki('Referencia/Operacion', 
													'Documentación de una operación',
													'toba_editor');
		echo "
			<ul>
				<li>$wiki1
			</ul>
		";
	}
}

?>