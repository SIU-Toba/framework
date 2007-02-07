<?php
require_once("tutorial/pant_tutorial.php");

class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		$arbol = toba_recurso::imagen_proyecto('tutorial/abmc-arbol.png');		
		echo "
			<p>
				Cuando la entidad a editar en el ABM se compone de más de un tabla, la operación
				se puede dividir en dos grandes etapas:
			</p>
				<ol>
					<li>Selección de una entidad existente o desición de crear una nueva
					<li>Edición de la entidad (puede ser una modificación o un alta)
				</ol>
			<img src='$arbol'>				
			<p>
				Estas dos etapas se modelan como dos <strong>pantallas</strong> distintas del 
				<strong>CI</strong> principal de la operación. La primer pantalla
				(la de <em>Navegación</em>) contiene sólo un <strong>filtro</strong> y un <strong>cuadro</strong> que 
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
				una <strong>Transacción a nivel operación</strong>, vista en el capítulo de Persistencia. 
				Las modificaciones, altas y bajas son mantenidas en memoria (sesión) hasta que el usuario presiona
				Guardar, donde se sincronizan con la base de datos.
			</p>
		";
		$vinculo = toba::vinculador()->crear_vinculo(null, 2658, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ejecutar Operación</a></p>";
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
		$eventos1 = toba_recurso::imagen_proyecto('tutorial/abmc-navegacion.png');				
		$eventos2 = toba_recurso::imagen_proyecto('tutorial/abmc-edicion.png');
		$codigo = 
'<?php
...(parte de la extensión del CI principal)...

//---------- Pantalla seleccion --------------//

function evt__filtro_personas__filtrar($datos)
{
	
	$this->s__filtro = $datos;			//Guardar las condiciones en una variable de sesion
										//para poder usarla en la configuracion del cuadro
}

function evt__cuadro_personas__seleccion($id)
{
	$this->dep("datos")->cargar($id);	//Carga el datos_relacion
	$this->set_pantalla("edicion");		//Cambia a la pantalla de edición
}

function evt__agregar()
{
	$this->set_pantalla("edicion");		//Cambia a la pantalla de edición
}

//---------- Pantalla edicion --------------//

function evt__eliminar()
{
	$this->dep("datos")->eliminar();	//Elimina TODOS los datos de la relación y sincroniza
	$this->set_pantalla("seleccion");   //Cambia a la pantalla de selección o navegación
}

function evt__cancelar()
{
	$this->dep("editor")->disparar_limpieza_memoria();	//Limpia al CI anidado de edición
	$this->dep("datos")->resetear();					//Descarta los cambios en el datos_relacion
	$this->set_pantalla("seleccion");					//Cambia a la pantalla de selección o navegación
}
	
function evt__procesar()
{
	$this->dep("editor")->disparar_limpieza_memoria(); 	//Limpia al CI anidado de edición
	$this->dep("datos")->sincronizar();					//Sincroniza los cambios del datos_relacion con la base
	$this->dep("datos")->resetear();				
	$this->set_pantalla("seleccion");					//Cambia a la pantalla de selección o navegación
}
	
?>
';
		echo "
			<p>Una vez definidos los componentes se necesita programar la lógica del CI
			principal, es decir el que maneja la navegación y la transacción a alto nivel. 
			Lo más interesante en este CI es atrapar los distintos eventos:
			<table>
			<tr style='text-align: center; font-weight: bold;'>
				<td style='border-bottom: 1px solid black;'>Primera Pantalla</td>
				<td style='border-bottom: 1px solid black;'>Segunda Pantalla</td></tr>
			<tr><td><img src='$eventos1'></td>
				<td valign=top><img src='$eventos2'></td>
			</tr>
			<tr valign=top>
				<td>Posibles eventos:
					<ul>
				 	<li>Ingresar una condición al filtro, reduciendo el conjunto de datos que muestra el cuadro
				 	<li>Seleccionar un elemento del cuadro, pasando a editar el elemento seleccionado
				 	<li>Decidir Agregar un nuevo elemento, pasando a editar un elemento vacío inicialmente
				 	</ul>
				 </td>
				 <td>Posibles eventos:
				 	<ul>
				 		<li>Eliminar completo la entidad
				 		<li>Cancelar la edición y volver a la pantalla anterior
				 		<li>Guardar los cambios a la base de datos
				 		<li>Cambiar de solapas y cambiar los datos, esto se delega
				 			a un CI anidado que se ve luego.
				 </td>
			</tr>
			</table>
			</p><br style='clear:both'>
			".mostrar_php($codigo)."
		";
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