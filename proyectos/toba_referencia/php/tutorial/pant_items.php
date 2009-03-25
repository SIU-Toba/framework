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
			Si se piensa la aplicaci�n como un <em>Cat�logo de operaciones</em>, cada una de estas operaciones
			se la puede pensar como un <strong>�tem</strong> de este cat�logo. Para una mejor organizaci�n de estos �tems se los incluye en <em>carpetas</em>, conformando
			un �rbol. Por ejemplo se puede definir el siguiente �rbol de �tems en el editor:
			</p>
		";
		echo toba_recurso::imagen_proyecto('tutorial/item-arbol.png', true);
		echo ' 
			<p>
			Y luego se puede ver el mismo �rbol s�lo que horizontalmente, formando el <strong>men� de la aplicaci�n</strong>:
			</p>
		';
		echo toba_recurso::imagen_proyecto('tutorial/menu.png', true);
		echo '
			<p>Ahora veremos c�mo armar este �rbol a partir de dos acciones:
				<ul><li>Creaci�n de Carpetas</li><li>Creaci�n de Items</li></ul>
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
				Una vez creado un �tem podemos asociarle comportamientos:
				<ul>
				 <li><em>Con Componentes</em>: Es la forma recomendada para la mayor�a de las operaciones, se ve m�s adelante en el tutorial.</li>
				 <li><em>Programando su comportamiento</em>: Para casos de operaciones de consola, en lote o que tengan una salida gr�fica totalmente irregular
				 		 en donde el esquema de componentes no alcanza.</li>
				 </ul>
			</p>
			<p>
			La <strong>ejecuci�n de PHP Plano</strong> cubre el caso de comportamiento totalmente programado o <em>ad-hoc</em>. 
			En este caso se asocia al �tem un archivo en el sistema de archivos y en �l se programa la operaci�n de la forma tradicional en PHP. 
			</p>
			
			<p>Se puede definir el archivo en las propiedades b�sica de la operaci�n
			</p>
			";
			echo "<div style='text-align: center'>";
			echo toba_recurso::imagen_proyecto('tutorial/item-php-plano.png', true);
			echo "</div>";
			$vinculo = toba::vinculador()->get_url(null, 1000077);
			echo "<p>
				El c�digo puede contener referencias a todo el API de toba, exceptuando a los componentes.
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
													'Documentaci�n de una operaci�n',
													'toba_editor');
		echo "
			<ul>
				<li>$wiki1
			</ul>
		";
	}
}

?>