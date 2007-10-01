<?php
require_once("tutorial/pant_tutorial.php");

class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		$arbol = toba_recurso::imagen_proyecto('tutorial/abm-mt-arbol.png');		
		echo "
			<p>
				Cuando la entidad a editar en el ABM se compone de m�s de un tabla, la operaci�n
				se puede dividir en dos grandes etapas:
			</p>
				<ol>
					<li>Elecci�n entre editar un entidad existente o crear una nueva.
					<li>Edici�n de la entidad
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
		$vinculo = toba::vinculador()->get_url(null, 2658, array(), array('celda_memoria'=>'ejemplo'));
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
		$eventos1 = toba_recurso::imagen_proyecto('tutorial/abm-mt-navegacion.png');				
		$eventos2 = toba_recurso::imagen_proyecto('tutorial/abm-mt-edicion.png');
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
		$tab1 = toba_recurso::imagen_proyecto('tutorial/abm-mt-tab1.png');
		$tab2 = toba_recurso::imagen_proyecto('tutorial/abm-mt-tab2.png');
		$tab3 = toba_recurso::imagen_proyecto('tutorial/abm-mt-tab3.png');
		$codigo1 =	
'<?php
function conf__form_persona()
{
  return $this->get_relacion()->tabla("persona")->get();
}

function evt__form_persona__modificacion($registro)
{
	$this->get_relacion()->tabla("persona")->set($registro);
}
?>
';
		$codigo2 = 
'<?php
function conf__form_juegos()	
{
	return $this->get_relacion()->tabla("juegos")->get_filas(null,true);	
}

function evt__form_juegos__modificacion($datos)
{
	$this->get_relacion()->tabla("juegos")->procesar_filas($datos);	
}
?>';
		$codigo3 = 
'<?php
protected $s__deporte;

function conf__cuadro_deportes()	
{
	return $this->get_relacion()->tabla("deportes")->get_filas();	
}

function evt__cuadro_deportes__seleccion($seleccion) {	
	$this->s__deporte = $seleccion;
}

function conf__form_deportes()
{
	if(isset($this->s__deporte)) {	
		return $this->get_relacion()->tabla("deportes")->get_fila($this->s__deporte);	
	}
}

function evt__form_deportes__modificacion($registro)
{
	if(isset($this->s__deporte)){
		$this->get_relacion()->tabla("deportes")->modificar_fila($this->s__deporte, $registro);	
		$this->evt__form_deportes__cancelar();	
	}
}

function evt__form_deportes__baja()
{
	if(isset($this->s__deporte)){
		$this->get_relacion()->tabla("deportes")->eliminar_fila( $this->s__deporte );	
		$this->evt__form_deportes__cancelar();	
	}
}

function evt__form_deportes__alta($registro)
{
	$this->get_relacion()->tabla("deportes")->nueva_fila($registro);
}

function evt__form_deportes__cancelar()
{
	unset($this->s__deporte);
}
?>
';
	$codigo4 =
'<?php
function conf__cuadro_deportes()	
{
	return $this->get_relacion()->tabla("deportes")->get_filas();	
}

function evt__cuadro_deportes__seleccion($seleccion) {	
	$this->get_relacion()->tabla("deportes")->set_cursor($seleccion);
}

function conf__form_deportes()
{
	if ($this->get_relacion()->tabla("deportes")->hay_cursor()) {
		return $this->get_relacion()->tabla("deportes")->get();
	}
}

function evt__form_deportes__modificacion($registro)
{
	$this->get_relacion()->tabla("deportes")->set($registro);
	$this->evt__form_deportes__cancelar();
}

function evt__form_deportes__baja()
{
	$this->get_relacion()->tabla("deportes")->set(null);
	$this->evt__form_deportes__cancelar();
}

function evt__form_deportes__alta($registro)
{
	$this->get_relacion()->tabla("deportes")->nueva_fila($registro);
}

function evt__form_deportes__cancelar()
{
	$this->get_relacion()->tabla("deportes")->resetear_cursor();
}
?>
';
		echo "
			<h2>Primer Tab: Formulario simple</h2>
			<img src='$tab1'>
			<p>En esta solapa se encuentra un formulario con un evento impl�cito <em>modificacion</em>.
			Ya que se est� editando la tabla cabecera de la relaci�n (en este caso persona) s�lo
			es posible que exista un �nico registro de esta tabla en la relaci�n.</p>
			<p>
			El m�todo <em>set</em> del datos_tabla est� preparado para estos casos, 
			si no existe el registro lo crea y si existe lo modifica. La carga en la configuraci�n tambi�n
			es sencilla, con el m�todo <em>get</em> se piden los datos del �nico registro, en caso de no 
			existir este m�todo retorna <em>null</em> mostrando el formulario vac�o.
			</p>
			
			".mostrar_php($codigo1)."
			
			<h2>Segundo Tab: Formulario ml</h2>
			<img src='$tab2'>
			<p>En esta solapa, un formulario ML maneja las tres acciones (ABM) sobre una tabla
			de la relaci�n (juegos de una persona). Lo interesante del formulario ML es que las acciones
			se realizan en javascript, informandolas al servidor como un bloque. El formato de la informaci�n
			que recibe el servidor es una matriz, donde por cada fila se informa su estado (A, B o M) junto con
			su nuevo valor (exceptuando la baja).</p>
			<p>En lugar de recorrer esta estructura manualmente y con un case derivar cada acci�n de una fila 
			a un m�todo del datos_tabla, esta clase contiene un m�todo <em>procesar_filas</em> que lo hace internamente.
			Para la carga se utiliza el m�todo <em>get_filas</em> con su segundo par�metro en verdadero, indicando que 
			las filas se retornen en una matriz asociativa cuya clave sea la clave interna de la fila. Esto permite
			que el ML y el datos_tabla mantengan los mismos valores de claves de las filas.
			".mostrar_php($codigo2)."
			
			<h2>Tercer Tab: Cuadro y Formulario</h2>
			<img src='$tab3'>
			<p>
			La �ltima solapa tiene un cuadro y un formulario que maneja las acciones sobre una tabla de la relaci�n
			(deportes de una persona). La estrategia aqu� es manejar la interface de una forma cl�sica, en donde
			en el cuadro se muestran las filas disponibles y al seleccionarlas se pueden editar o borrar.
			</p>
			<p>
			La primera forma de encararlo es mantener en sesi�n la fila seleccionada, si no existe tal fila
			indica que el formulario se debe mostrar vac�o dando lugar a una alta. Cuando un atributo de la 
			clase comienza con <em>s__</em> indica que ser� mantenido en sesi�n, en este caso ese atributo es
			<em>\$s__deporte</em>. Las operaciones de baja y modificaci�n utilizan esta fila seleccionada
			como par�metro para los m�todos del datos_tabla.
			</p>
			".mostrar_php($codigo3)."
		
			<h2>Tercer Tab: Forma altenativa</h2>
			<p>La alternativa a mantener la selecci�n en una variable de sesi�n y luego
			usar la API del datos_tabla sobre esta fila (para obtener sus valores, modificarla o borrarla) 
			es usar una utilidad del datos_tabla llamada <strong>cursor</strong>. El cursor
			permite apuntar a una fila particular, haciendo que ciertas operaciones utilizen esa fila
			como predeterminada. Por ejemplo si se fija el cursor en la fila 8 y luego, sea en el mismo u otro
			pedido de p�gina, se hace un set(\$datos) en esa tabla, los datos afectan a esta fila.
			<p>
			
			<p>El cursor es de suma utilidad cuando se trabaja con relaciones m�s complejas ya que permite
			fijar valores en ciertas tablas y operar en forma reiterada sobre registros relacionados de otras
			tablas, esto se ver� m�s adelante.</p>
			
			
			".mostrar_php($codigo4)."
		";
	}
}

//--------------------------------------------------------------


?>