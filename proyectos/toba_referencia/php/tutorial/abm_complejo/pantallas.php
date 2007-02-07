<?php
require_once("tutorial/pant_tutorial.php");

class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		$arbol = toba_recurso::imagen_proyecto('tutorial/abmc-arbol.png');		
		echo "
			<p>
				Cuando la entidad a editar en el ABM se compone de m�s de un tabla, la operaci�n
				se puede dividir en dos grandes etapas:
			</p>
				<ol>
					<li>Selecci�n de una entidad existente o desici�n de crear una nueva
					<li>Edici�n de la entidad (puede ser una modificaci�n o un alta)
				</ol>
			<img src='$arbol'>				
			<p>
				Estas dos etapas se modelan como dos <strong>pantallas</strong> distintas del 
				<strong>CI</strong> principal de la operaci�n. La primer pantalla
				(la de <em>Navegaci�n</em>) contiene s�lo un <strong>filtro</strong> y un <strong>cuadro</strong> que 
				permite al usuario seleccionar una entidad existente, pasando inmediatamente a modo edici�n. 
				Tambi�n se incluye un evento en esta pantalla que permite avanzar hacia el alta (Bot�n Agregar en el
				ejemplo).
			</p>
			<p>
				A la segunda pantalla se le dice de <em>edici�n</em> y contiene en composici�n otro <strong>CI</strong> que 
				tiene generalmente una pantalla por tabla involucrada en la entidad. Estas pantallas se muestran como
				solapas o tabs permitiendo al usuario navegar entre ellas e ir editando las distintas tablas que componen la
				entidad.
			</p>

			<p>
				Adem�s de la cantidad de componentes, la diferencia principal en el armado de esta operaci�n
				es que no se transacciona con la base de datos hasta que el usuario en la pantalla de edici�n presiona
				el bot�n <strong>Guardar</strong>. Para soportar este requisito se va a usar 
				una <strong>Transacci�n a nivel operaci�n</strong>, vista en el cap�tulo de Persistencia. 
				Las modificaciones, altas y bajas son mantenidas en memoria (sesi�n) hasta que el usuario presiona
				Guardar, donde se sincronizan con la base de datos.
			</p>
		";
		$vinculo = toba::vinculador()->crear_vinculo(null, 2658, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ejecutar Operaci�n</a></p>";
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
...(parte de la extensi�n del CI principal)...

//---------- Pantalla seleccion --------------//

function evt__filtro_personas__filtrar($datos)
{
	
	$this->s__filtro = $datos;			//Guardar las condiciones en una variable de sesion
										//para poder usarla en la configuracion del cuadro
}

function evt__cuadro_personas__seleccion($id)
{
	$this->dep("datos")->cargar($id);	//Carga el datos_relacion
	$this->set_pantalla("edicion");		//Cambia a la pantalla de edici�n
}

function evt__agregar()
{
	$this->set_pantalla("edicion");		//Cambia a la pantalla de edici�n
}

//---------- Pantalla edicion --------------//

function evt__eliminar()
{
	$this->dep("datos")->eliminar();	//Elimina TODOS los datos de la relaci�n y sincroniza
	$this->set_pantalla("seleccion");   //Cambia a la pantalla de selecci�n o navegaci�n
}

function evt__cancelar()
{
	$this->dep("editor")->disparar_limpieza_memoria();	//Limpia al CI anidado de edici�n
	$this->dep("datos")->resetear();					//Descarta los cambios en el datos_relacion
	$this->set_pantalla("seleccion");					//Cambia a la pantalla de selecci�n o navegaci�n
}
	
function evt__procesar()
{
	$this->dep("editor")->disparar_limpieza_memoria(); 	//Limpia al CI anidado de edici�n
	$this->dep("datos")->sincronizar();					//Sincroniza los cambios del datos_relacion con la base
	$this->dep("datos")->resetear();				
	$this->set_pantalla("seleccion");					//Cambia a la pantalla de selecci�n o navegaci�n
}
	
?>
';
		echo "
			<p>Una vez definidos los componentes se necesita programar la l�gica del CI
			principal, es decir el que maneja la navegaci�n y la transacci�n a alto nivel. 
			Lo m�s interesante en este CI es atrapar los distintos eventos:
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
				 	<li>Ingresar una condici�n al filtro, reduciendo el conjunto de datos que muestra el cuadro
				 	<li>Seleccionar un elemento del cuadro, pasando a editar el elemento seleccionado
				 	<li>Decidir Agregar un nuevo elemento, pasando a editar un elemento vac�o inicialmente
				 	</ul>
				 </td>
				 <td>Posibles eventos:
				 	<ul>
				 		<li>Eliminar completo la entidad
				 		<li>Cancelar la edici�n y volver a la pantalla anterior
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