<?php
require_once("tutorial/pant_tutorial.php");

class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		$arbol = toba_recurso::imagen_proyecto('tutorial/abmc-arbol.png');		
		$arbol = toba_recurso::imagen_proyecto('tutorial/abmc-arbol.png');		
		echo "
			<p>
				Cuando la entidad a editar en el ABM se compone de más de un tabla la operación
				se puede dividir en dos grandes etapas:
			</p>
				<ol>
					<li>Selección de una entidad existente o desición de crear una nueva
					<li>Edición de la entidad (puede ser una modificación o un alta)
				</ol>
			<p>
				Estas dos etapas se modelan como dos <strong>pantallas</strong> distintas del 
				<strong>CI</strong> principal de la operación. La primer pantalla
				(la de <em>selección</em>) contiene sólo un <strong>filtro</strong> y un <strong>cuadro</strong> que 
				permite al usuario seleccionar una entidad existente, pasando inmediatamente a modo edición. 
				También se incluye un evento en esta pantalla que permite avanzar hacia el alta (Botón Agregar en el
				ejemplo).
			</p>
			<p>
				A la segunda pantalla se le dice de <em>edición</em> y contiene en composición otro <strong>CI</strong> que 
				tiene generalmente una pantalla por tabla involucrada en la entidad. Estas pantallas se muestran como
				solapas o tabs permitiendo al usuario navegar entre ellas e ir editando las distintas tablas que componen la
				entidad.
			</p>
			<p>
				Además de la cantidad de componentes, la diferencia principal en el armado de esta operación
				es que no se transacciona con la base de datos hasta que el usuario en la pantalla de edición presiona
				el botón <strong>Guardar</strong>. Para soportar este requisito se va a usar 
			</p>
			<img src='$arbol'>
		";
		$vinculo = toba::vinculador()->crear_vinculo(null, 2658, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ejecutar Operación</a></p>";
	}
}

//--------------------------------------------------------------

class pant_def_tablas extends pant_tutorial 
{
	function generar_layout()
	{
		
	}
}

//--------------------------------------------------------------

class pant_def_relacion extends pant_tutorial 
{
	function generar_layout()
	{
		
	}
}

//--------------------------------------------------------------

class pant_ci_seleccion extends pant_tutorial 
{
	function generar_layout()
	{
		
	}
}

//--------------------------------------------------------------

class pant_ci_edicion extends pant_tutorial 
{
	function generar_layout()
	{
		
	}
}

//--------------------------------------------------------------


?>