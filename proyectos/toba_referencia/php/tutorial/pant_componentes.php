<?php 
require_once("tutorial/pant_tutorial.php");

class pant_concepto extends pant_tutorial
{
	function generar_layout()
	{
		echo "
			<p>
			Los componentes son unidades o elementos que cubren distintos aspectos de una operaci�n.
			Construir una operaci�n en base a componentes permite que:
			 <ul>
			 	<li>Un componente pueda reutilizarse en distintas operaciones o en distintas partes de la misma operaci�n.
			 	<li>Los componentes se puedan componer o encastrar entre s�.
			 	<li>Cada uno encapsule alg�n comportamiento complejo, que ya no es necesario programar.
			 	<li>Al estar categorizados seg�n su funci�n, se logre en el sistema una separaci�n en capas de forma transparente.
			 </ul>
			</p>
			<p>
			El comportamiento particular de un componente es determinado por:
		    <ul>
			    <li>La definici�n usando el <strong>editor web</strong>.
			    <li>La extensi�n PHP del componente, utilizando el <strong>editor php</strong> a gusto.
			    <li>La extensi�n Javascript del componente, usando el editor a gusto. 		
			</ul>
			</p>
			
		";
	}	
}

class pant_tipos extends pant_tutorial
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/capas2.png');
		echo "
			Los componentes se categorizan seg�n su funci�n: interface, control o persistencia.
			<img style='padding: 20px;float: right;' src='$img'>
			<h3>Interface</h3>
			Los componentes o elementos de interface son controles gr�ficos o widgets.
			En su aspecto gr�fico se basan en los elementos HTML aunque su comportamiento va m�s all�,
			tomando responsabilidades tanto en el cliente como en el servidor:
			<ul>
				<li>En el servidor recibe un conjunto de datos a partir del control.
				<li>Se grafica utilizando HTML.
				<li>Tiene un comportamiento en el browser (usando javascript).
				<li>Se comunica con su par en el servidor a traves del POST.
				<li>Se analizan los nuevos datos o acciones y se notifican al control.
			</ul>
			
			<h3>Control</h3>
			El componente llamado <strong>controlador de interface</strong>
			oficia como intermediario entre las capas de datos o negocio y la interface, formando la 
			<em>plasticola</em> necesaria para construir una operaci�n con componentes.
			
			<h3>Persistencia</h3>
			Los componentes de persistencia permiten modelar tablas y registros de una base relacional 
			o alg�n otro medio que maneje datos tabulares, brindando servicios transaccionales a las capas superiores. 
			En estos componentes se describen las estructuras y asociaciones de las tablas involucradas en una operaci�n y 
			la forma en que los registros ser�n sincronizados al final de la misma.
		";
	}	
	
}



class pant_creacion extends pant_tutorial
{
	function generar_layout()
	{
		echo mostrar_video('componente-crear');
	}	
	
}

class pant_extension extends pant_tutorial 
{
	function generar_layout()
	{
		$codigo_ej = '
<?php 
	class ci_pago extends toba_ci
	{		
			/**
			 * Atenci�n de un evento "pagar" de un formulario
			 */
		    function evt__form_pago__pagar($datos)
		    {
		    	$this->s__pago = $datos;
		    	$this->set_pantalla("pant_ubicacion");
		    }

		    /**
		     * Redefinici�n de un m�todo
		     */
			function ini()
			{
				$this->valor_defecto = 0;
			}		    
		    
			/**
			 * Redefinici�n de un m�todo para extender el componente en Javascript
			 */
		    function extender_objeto_js()
		    {
		    	echo "
		    		/**
		    		 * Atenci�n del evento procesar de este componente
		    		 */
		    		{$this->objeto_js}.evt__procesar = function() {
		    			return prompt(\"Desea Procesar?\");
		    		}
		    	";
		    }
	}
?>
		';
		
		$ventana = toba::instancia()->get_url_proyecto('toba_editor')."/doc/api/media/ventana.png";
		$api = toba_parser_ayuda::parsear_api('li_Componentes', 'documentaci�n API', 'toba_editor');		
		echo "
			<p>
			El comportamiento de un componente se basa en su definici�n y,
			en distinta medida seg�n el tipo, su extensi�n en c�digo.
			</p>
			<p>
			La extensi�n en c�digo se da a trav�s de la <strong>herencia</strong>, creando una subclase del componente en cuesti�n 
			y seleccionandola durante la definici�n del componente en el editor. Se podr�an definir tres objetivos distintos a la hora de hacer una extensi�n
			de un componente:
			<ul>
				<li><strong>Atender eventos</strong>: El componente notifica <em>sucesos</em>
					y en la extensi�n se escuchan. A esta comunicaci�n se la denomina <em>eventos</em> y sa la ve
					m�s adelante en el tutorial.
				
				
				<li><strong>Redefinir m�todos</strong>: En la $api los m�todos recomendados para 
					extender llevan a su lado un �cono de ventana
					<img src='$ventana'>. 
					Otros m�todos protegidos son extensibles tambi�n, pero si no poseen la ventana 
					no se asegura que en futura versiones del framework ser�n soportados, ya que lo que 
					se est� extendiendo es un m�todo interno.
				
				<li><strong>Extender el componente en Javascript</strong>: Cada componente en PHP tiene su par en Javascript, 
				por lo que en la extensi�n tambi�n es posible variar el comportamiento del componente en el cliente.
			</ul>
			</p>
			<h3>Ejemplo</h3>			
			<p>
			Para tener una idea de como 'luce' una extensi�n, se presenta una extensi�n t�pica de un componente controlador. La idea no es entender en profundidad esta extensi�n sino es para 
			tomar un primer contacto. Como se ve en los comentarios del c�digo, en este caso se consumieron las formas de extensi�n vistas:
			</p>
		";
		echo "<div class='codigo'>";
		highlight_string($codigo_ej);
		echo "</div>";
	}	
}

//--------------------------------------------------------

class pant_masinfo extends pant_tutorial 
{
	function generar_layout()
	{
		$wiki1 = toba_parser_ayuda::parsear_wiki('Referencia/Objetos', 
													'Documentaci�n de Componentes',
													'toba_editor');
		echo "
			<ul>
				<li>$wiki1
			</ul>
		";
	}
}

class pant_video_extension extends pant_tutorial
{
	function generar_layout()
	{
		echo mostrar_video('componente-extender');
	}	
	
}


?>