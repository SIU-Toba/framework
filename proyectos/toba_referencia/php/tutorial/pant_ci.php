<?php
require_once("tutorial/pant_tutorial.php");

class pant_definicion extends pant_tutorial 
{
	function generar_layout()
	{
		$icono = toba_recurso::imagen_toba('objetos/multi_etapa.gif', true);
		$icono_pant = toba_recurso::imagen_toba('objetos/pantalla.gif', true);
		$arbol = toba_recurso::imagen_proyecto('tutorial/ci-arbol.png');
		$tabs = toba_recurso::imagen_proyecto('tutorial/ci-tabs.png');
		echo "
			<div style='float:right;padding: 10px;width: 310px;'>
			<img src='$arbol'><br>
			<span class='caption'>Definición en el editor web de una operación con un CI de dos pantallas.
			</span>
			</div>
	
			<p>
			El Controlador de interface $icono  o CI es el componente raiz que necesitamos definir en nuestra
			operación, ya que tiene la capacidad de contener otros componentes, formando las 
			distintas ramas del árbol de una operación.
			</p>
			<p>
			Para organizar la operación, el CI tiene la capacidad de definir
			 <span style='white-space:nowrap;'>$icono_pant <strong>Pantallas</strong> </span>
			siendo responsable de la lógica de navegación entre las mismas y de los componentes que utiliza	cada una.
			 La forma más usual de navegación entre estas pantallas es usar solapas o tabs horizontales.
			</p>
			
			<div style='padding: 10px;width: 360px;clear:both;'>
			<img src='$tabs'><br>
			<span class='caption'>Ejecución de la operación, las pantallas se ven como solapas horizontales</span>
			</div>				


		";
	}
}

//--------------------------------------------------------------

class pant_ejemplo extends pant_tutorial 
{
	function generar_layout()
	{
		$vinculo = toba::vinculador()->get_url(null, 1000089, array(), array('celda_memoria'=>'ejemplo'));		
		$arbol = toba_recurso::imagen_proyecto('tutorial/ci-arbol.png');
		$tabs = toba_recurso::imagen_proyecto('tutorial/ci-tabs.png');
		echo "

			<p>
				En el resto de este capítulo se trabajará con un ejemplo simple de 
				un ABM de direcciones de correo (un poco raro... por el bien del ejemplo). Puede verse la operación terminada
				<strong><a href='$vinculo' target='_blank'>aquí</a></strong>.
			</p>
			<h3>ABM de direcciones de correo</h3>
			<p>
			La idea de la operación es mostrar por un lado el listado de direcciones de correo actual con un <strong>cuadro</strong>
			y por otro poder modificar este listado con un <strong>formulario</strong>. Los datos que se usan no se persisten
			en una base, eso lo vemos más adelante.
			</p>
			</p>
			Desde el listado es posible seleccionar una dirección. En este caso se cambia a la pantalla de edición
			y se presenta un formulario con la opción de modificar o dar de baja esa dirección.
			También esta la posibilidad de dar de alta una nueva dirección, navegando por la solapa
			hacia la pantalla de edición.
			</p>
			
			<h3>Definición de los componentes</h3>
			<div style='float:right;padding: 10px;width: 310px;'>
			<img src='$arbol'><br>
			<span class='caption'>Definición en el editor de la operación con un CI de dos pantallas.
			</span>
			</div>					
			El primer paso es definir los distintos componentes que componen la operación:
			<ol>
				<li>Un ítem
				<li>Un CI con dos pantallas
				<li>En la primer pantalla se crea un cuadro
				<li>En la segunda pantalla se crea un formulario
			</ol>
			
			<h3 style='clear:both'>Programación</h3>
			<p>
			Una vez definidos los componentes el resto del capítulo vamos a dedicarlo
			a programar el comportamiento de la operación, siempre dentro de la extensión
			del CI de la operación.
			</p>
		";
	}	
}

//--------------------------------------------------------------

class pant_video extends pant_tutorial 
{
	function generar_layout()
	{
		echo mostrar_video('ci');		
	}	
}

//--------------------------------------------------------------

class pant_eventos extends pant_tutorial 
{
	function generar_layout()
	{
		$evt_cuadro = toba_recurso::imagen_proyecto('tutorial/ci-evento-cuadro.png');		
		$evt_form_alta = toba_recurso::imagen_proyecto('tutorial/ci-evento-form-alta.png');
		echo '
			</div>		
			<p>
			Un <strong>Evento</strong> representa la interacción del usuario. Al ser aplicaciones web, esta interacción
			surje en el cliente (navegador o browser) donde el usuario ha realizado acciones que deben ser atendendidas en el lado servidor.
			 En el servidor el lugar para atender esas acciones es la extensión del CI.
			 </p>
			 <p>
 			Una vez que definimos los componentes es hora de extender el CI y definir una subclase vacía.
			Lo primero que vamos a agregar a esta subclase es la atención de eventos del cuadro y del formulario.
			La forma de <em>atrapar</em> un evento es definir un método
			<pre>
			function evt__causante__evento($parametros)
			</pre>
			Donde <em>causante</em> es el id que toma el componente en el CI, y <em>evento</em>
			es el id del evento tal como se definio en el editor.
			</p>
		';
		//----------------------------------------------------------	
			
		echo "
			<h3>Evento Selección del cuadro</h3>		
			<p>
			En el primer caso que vamos a tomar es el del <strong>cuadro</strong>. Cuando
			el usuario selecciona un elemento de la grilla, ese elemento debe ser guardado internamente para 
			luego mostrar sus datos asociados en el formulario.
			</p>
			<div style='float:right;padding: 10px;border: 1px solid gray;background-color:white;'>
			<img src='$evt_cuadro'><br>
			</div>			
		";
		$codigo = '
<?php
	class ci_abm_direcciones extends toba_ci
	{
		protected $actual;
		
		function evt__cuadro__seleccion($direccion)
		{
			$this->actual = $direccion["email"];
		}
		
	}
?>
';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
		
		//----------------------------------------------------------		
		
		echo "
		<h3 style='clear:both;'>Evento Alta del formulario</h3>
		<p>
			El segundo caso de evento lo vamos a tomar del formulario, cuando presionamos el botón
			<em>Agregar</em>, viaja por el POST una nueva dirección de email que el formulario entrega
			con el evento <em>Alta</em>
		</p>
		<div style='float:right;padding: 10px;border: 1px solid gray;background-color:white;'>
			<img src='$evt_form_alta'><br>
		</div>		
		";
		$codigo = '
<?php
//---Dentro de la subclase del CI

		protected $direcciones;
		
		/**
		 * En el alta agrega la direccion al arreglo, indexado por email
		 */
		function evt__form__alta($nueva_dir)
		{
			$email = $nueva_dir["email"];
			$this->direcciones[$email] = $nueva_dir;
		}
?>
';	
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
	}
}

//--------------------------------------------------------------

class pant_configuracion extends pant_tutorial 
{
	function generar_layout()
	{
		$pedido = toba_recurso::imagen_proyecto('tutorial/ci-pedido.png');
		$conf_cuadro = toba_recurso::imagen_proyecto('tutorial/ci-conf-cuadro.png');		
		$conf_form = toba_recurso::imagen_proyecto('tutorial/ci-conf-form.png');		
		echo "
			<p>
			Una vez atendidas las acciones del usuario, la operación se dispone a construir una nueva interface
			a partir de sus componentes. Para ello primero se deben <strong>configurar</strong> los distintos
			componentes que formarán parte de la salida HTML. Para configurar un componente se debe definir un 
			método <em>conf__dependencia</em> donde <em>dependencia</em> es el id del componente en el CI.
			</p>
			<p>En el siguiente gráfico podemos ver donde estamos parados en el pedido de página actual
			</p>
			
			<img style='padding:15px;' src='$pedido'>
		";
		
		//-------------------------------------------------------------
		echo "
			<h3>Configuración del Cuadro</h3>
			<p>
			Ya vimos como el formulario agregaba las direcciones en un arreglo, este arreglo
			es el que necesitará el cuadro para mostrar la grilla:
			</p>
			<div style='float:right;padding: 10px;border: 1px solid gray;background-color:white;'>
				<img src='$conf_cuadro'><br>
			</div>					
		";
		$codigo = '
<?php
//---Dentro de la subclase del CI

		function conf__cuadro(toba_ei_cuadro $cuadro)
		{
			$cuadro->set_datos($this->direcciones);
		}
?>
';			
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";			

		//-------------------------------------------------------------
		echo "
			<h3 style='clear:both'>Configuración del Formulario</h3>
			<p>
			Durante la configuración también vamos a cargar al formulario con datos, pero
			sólo cuando previamente se ha seleccionado algo desde el cuadro (así se edita
			lo que se seleccionó). En caso contrario no se cargarán datos y el formulario
			se graficará vacío.
			</p>
			<div style='float:right;padding: 10px;border: 1px solid gray;background-color:white;'>
				<img src='$conf_form'><br>
			</div>				
		";
		$codigo = '
<?php
//---Dentro de la subclase del CI

		function conf__form(toba_ei_formulario $formulario)
		{
			if (isset($this->actual)) {
				$formulario->set_datos($this->direcciones[$this->actual]);	
			}
		}	
	?>
';			
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	

		echo "
			<h3 style='clear:both'>Otras configuraciones</h3>
			<p>
			Además de componentes, el ci se puede configurar a sí mísmo (definiendo el método <em>conf</em>)
			y a sus pantallas (<em>conf__idpant</em>)
			</p>
		";
	}	
}

//--------------------------------------------------------------

class pant_sesion extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			</p>
			Para cerrar el circuito eventos-configuración es necesario que el ci pueda
			<strong>recordar</strong> la información que va recolectando entre pedidos 
			de página. Esto se logra gracias a las llamadas <strong>variables de sesión</strong>.
			</p>
			<p>
			La forma de indicar al framework que una propiedad sea mantenida en sesión es prefijar su nombre con s__ (de sesión),
			en nuestro ejemplo mantendremos las direcciones y la selección actual en sesión:
			</p>
		";
		$codigo = '
<?php
	class ci_abm_direcciones extends toba_ci
	{
		protected $s__direcciones;
		protected $s__actual;
		....
	}
?>
';			
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";			
		
	}	
}

//--------------------------------------------------------------

class pant_navegacion extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			<p>
			Para terminar con el ejemplo y para que sea un poco más 'real' faltaría
			que al momento de seleccionar una dirección se navegue hacia la segunda pantalla,
			y cuando borre o cancele la edición se navegue hacia la primer pantalla.
			</p>
			<p>
			Usando la api del CI podemos lograr esto cambiando explícitamente de pantalla
			en los eventos que nos interesan:
			</p>
		";
		$codigo = '
<?php
...
	/**
	 * Cuando se selecciona del cuadro, se guarda en sesión la selección
	 * Luego se fuerza la pantalla de edición
	 */
	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__actual = $seleccion["email"];
		$this->set_pantalla("pant_edicion");
	}		
	
	/**
	 * En la baja toma la seleccion actual y la elimina del arreglo de direcciones
	 * Luego se vuelve al listado
	 */
	function evt__form__baja()
	{
		unset($this->s__direcciones[$this->s__actual]);
		$this->set_pantalla("pant_listado");
	}	
	
	/**
	 * Cuando cancela la edición, se saca la selección actual y se vuelve al listado
	 */
	function evt__form__cancelar()
	{
		unset($this->s__actual);
		$this->set_pantalla("pant_listado");
	}
...
?>	
';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";		
		$vinculo = toba::vinculador()->get_url(null, 1000089, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ver Ejemplo Completo</a></p>";
	}	
}


?>