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
			<span class='caption'>Definici�n en el editor web de una operaci�n con un CI de dos pantallas.
			</span>
			</div>
	
			<p>
			El Controlador de interface $icono  o CI es el componente raiz que necesitamos definir en nuestra
			operaci�n, ya que tiene la capacidad de contener otros componentes, formando las 
			distintas ramas del �rbol de una operaci�n.
			</p>
			<p>
			Para organizar la operaci�n, el CI tiene la capacidad de definir
			 <span style='white-space:nowrap;'>$icono_pant <strong>Pantallas</strong> </span>
			siendo responsable de la l�gica de navegaci�n entre las mismas y de los componentes que utiliza	cada una.
			 La forma m�s usual de navegaci�n entre estas pantallas es usar solapas o tabs horizontales.
			</p>
			
			<div style='padding: 10px;width: 360px;clear:both;'>
			<img src='$tabs'><br>
			<span class='caption'>Ejecuci�n de la operaci�n, las pantallas se ven como solapas horizontales</span>
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
				En el resto de este cap�tulo se trabajar� con un ejemplo simple de 
				un ABM de direcciones de correo (un poco raro... por el bien del ejemplo). Puede verse la operaci�n terminada
				<strong><a href='$vinculo' target='_blank'>aqu�</a></strong>.
			</p>
			<h3>ABM de direcciones de correo</h3>
			<p>
			La idea de la operaci�n es mostrar por un lado el listado de direcciones de correo actual con un <strong>cuadro</strong>
			y por otro poder modificar este listado con un <strong>formulario</strong>. Los datos que se usan no se persisten
			en una base, eso lo vemos m�s adelante.
			</p>
			</p>
			Desde el listado es posible seleccionar una direcci�n. En este caso se cambia a la pantalla de edici�n
			y se presenta un formulario con la opci�n de modificar o dar de baja esa direcci�n.
			Tambi�n esta la posibilidad de dar de alta una nueva direcci�n, navegando por la solapa
			hacia la pantalla de edici�n.
			</p>
			
			<h3>Definici�n de los componentes</h3>
			<div style='float:right;padding: 10px;width: 310px;'>
			<img src='$arbol'><br>
			<span class='caption'>Definici�n en el editor de la operaci�n con un CI de dos pantallas.
			</span>
			</div>					
			El primer paso es definir los distintos componentes que componen la operaci�n:
			<ol>
				<li>Un �tem
				<li>Un CI con dos pantallas
				<li>En la primer pantalla se crea un cuadro
				<li>En la segunda pantalla se crea un formulario
			</ol>
			
			<h3 style='clear:both'>Programaci�n</h3>
			<p>
			Una vez definidos los componentes el resto del cap�tulo vamos a dedicarlo
			a programar el comportamiento de la operaci�n, siempre dentro de la extensi�n
			del CI de la operaci�n.
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
			Un <strong>Evento</strong> representa la interacci�n del usuario. Al ser aplicaciones web, esta interacci�n
			surje en el cliente (navegador o browser) donde el usuario ha realizado acciones que deben ser atendendidas en el lado servidor.
			 En el servidor el lugar para atender esas acciones es la extensi�n del CI.
			 </p>
			 <p>
 			Una vez que definimos los componentes es hora de extender el CI y definir una subclase vac�a.
			Lo primero que vamos a agregar a esta subclase es la atenci�n de eventos del cuadro y del formulario.
			La forma de <em>atrapar</em> un evento es definir un m�todo
			<pre>
			function evt__causante__evento($parametros)
			</pre>
			Donde <em>causante</em> es el id que toma el componente en el CI, y <em>evento</em>
			es el id del evento tal como se definio en el editor.
			</p>
		';
		//----------------------------------------------------------	
			
		echo "
			<h3>Evento Selecci�n del cuadro</h3>		
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
			El segundo caso de evento lo vamos a tomar del formulario, cuando presionamos el bot�n
			<em>Agregar</em>, viaja por el POST una nueva direcci�n de email que el formulario entrega
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
			Una vez atendidas las acciones del usuario, la operaci�n se dispone a construir una nueva interface
			a partir de sus componentes. Para ello primero se deben <strong>configurar</strong> los distintos
			componentes que formar�n parte de la salida HTML. Para configurar un componente se debe definir un 
			m�todo <em>conf__dependencia</em> donde <em>dependencia</em> es el id del componente en el CI.
			</p>
			<p>En el siguiente gr�fico podemos ver donde estamos parados en el pedido de p�gina actual
			</p>
			
			<img style='padding:15px;' src='$pedido'>
		";
		
		//-------------------------------------------------------------
		echo "
			<h3>Configuraci�n del Cuadro</h3>
			<p>
			Ya vimos como el formulario agregaba las direcciones en un arreglo, este arreglo
			es el que necesitar� el cuadro para mostrar la grilla:
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
			<h3 style='clear:both'>Configuraci�n del Formulario</h3>
			<p>
			Durante la configuraci�n tambi�n vamos a cargar al formulario con datos, pero
			s�lo cuando previamente se ha seleccionado algo desde el cuadro (as� se edita
			lo que se seleccion�). En caso contrario no se cargar�n datos y el formulario
			se graficar� vac�o.
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
			Adem�s de componentes, el ci se puede configurar a s� m�smo (definiendo el m�todo <em>conf</em>)
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
			Para cerrar el circuito eventos-configuraci�n es necesario que el ci pueda
			<strong>recordar</strong> la informaci�n que va recolectando entre pedidos 
			de p�gina. Esto se logra gracias a las llamadas <strong>variables de sesi�n</strong>.
			</p>
			<p>
			La forma de indicar al framework que una propiedad sea mantenida en sesi�n es prefijar su nombre con s__ (de sesi�n),
			en nuestro ejemplo mantendremos las direcciones y la selecci�n actual en sesi�n:
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
			Para terminar con el ejemplo y para que sea un poco m�s 'real' faltar�a
			que al momento de seleccionar una direcci�n se navegue hacia la segunda pantalla,
			y cuando borre o cancele la edici�n se navegue hacia la primer pantalla.
			</p>
			<p>
			Usando la api del CI podemos lograr esto cambiando expl�citamente de pantalla
			en los eventos que nos interesan:
			</p>
		";
		$codigo = '
<?php
...
	/**
	 * Cuando se selecciona del cuadro, se guarda en sesi�n la selecci�n
	 * Luego se fuerza la pantalla de edici�n
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
	 * Cuando cancela la edici�n, se saca la selecci�n actual y se vuelve al listado
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