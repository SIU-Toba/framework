<?php 
require_once("tutorial/pant_tutorial.php");

class pant_concepto extends pant_tutorial
{
	function generar_layout()
	{
		echo "
			<p>
			Los componentes son unidades o elementos que cubren distintos aspectos de una operación.
			Construir una operación en base a componentes permite que:
			 <ul>
			 	<li>Un componente pueda reutilizarse en distintas operaciones o en distintas partes de la misma operación.
			 	<li>Los componentes se puedan componer o encastrar entre sí.
			 	<li>Cada uno encapsule algún comportamiento complejo, que ya no es necesario programar.
			 	<li>Al estar categorizados según su función, se logre en el sistema una separación en capas de forma transparente.
			 </ul>
			</p>
			<p>
			El comportamiento particular de un componente es determinado por:
		    <ul>
			    <li>La definición usando el <strong>editor web</strong>.
			    <li>La extensión PHP del componente, utilizando el <strong>editor php</strong> a gusto.
			    <li>La extensión Javascript del componente, usando el editor a gusto. 		
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
			Los componentes se categorizan según su función: interface, control o persistencia.
			<img style='padding: 20px;float: right;' src='$img'>
			<h3>Interface</h3>
			Los componentes o elementos de interface son controles gráficos o widgets.
			En su aspecto gráfico se basan en los elementos HTML aunque su comportamiento va más allá,
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
			<em>plasticola</em> necesaria para construir una operación con componentes.
			
			<h3>Persistencia</h3>
			Los componentes de persistencia permiten modelar tablas y registros de una base relacional 
			o algún otro medio que maneje datos tabulares, brindando servicios transaccionales a las capas superiores. 
			En estos componentes se describen las estructuras y asociaciones de las tablas involucradas en una operación y 
			la forma en que los registros serán sincronizados al final de la misma.
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
			 * Atención de un evento "pagar" de un formulario
			 */
		    function evt__form_pago__pagar($datos)
		    {
		    	$this->s__pago = $datos;
		    	$this->set_pantalla("pant_ubicacion");
		    }

		    /**
		     * Redefinición de un método
		     */
			function ini()
			{
				$this->valor_defecto = 0;
			}		    
		    
			/**
			 * Redefinición de un método para extender el componente en Javascript
			 */
		    function extender_objeto_js()
		    {
		    	echo "
		    		/**
		    		 * Atención del evento procesar de este componente
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
		$api = toba_parser_ayuda::parsear_api('li_Componentes', 'documentación API', 'toba_editor');		
		echo "
			<p>
			El comportamiento de un componente se basa en su definición y,
			en distinta medida según el tipo, su extensión en código.
			</p>
			<p>
			La extensión en código se da a través de la <strong>herencia</strong>, creando una subclase del componente en cuestión 
			y seleccionandola durante la definición del componente en el editor. Se podrían definir tres objetivos distintos a la hora de hacer una extensión
			de un componente:
			<ul>
				<li><strong>Atender eventos</strong>: El componente notifica <em>sucesos</em>
					y en la extensión se escuchan. A esta comunicación se la denomina <em>eventos</em> y sa la ve
					más adelante en el tutorial.
				
				
				<li><strong>Redefinir métodos</strong>: En la $api los métodos recomendados para 
					extender llevan a su lado un ícono de ventana
					<img src='$ventana'>. 
					Otros métodos protegidos son extensibles también, pero si no poseen la ventana 
					no se asegura que en futura versiones del framework serán soportados, ya que lo que 
					se está extendiendo es un método interno.
				
				<li><strong>Extender el componente en Javascript</strong>: Cada componente en PHP tiene su par en Javascript, 
				por lo que en la extensión también es posible variar el comportamiento del componente en el cliente.
			</ul>
			</p>
			<h3>Ejemplo</h3>			
			<p>
			Para tener una idea de como 'luce' una extensión, se presenta una extensión típica de un componente controlador. La idea no es entender en profundidad esta extensión sino es para 
			tomar un primer contacto. Como se ve en los comentarios del código, en este caso se consumieron las formas de extensión vistas:
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
													'Documentación de Componentes',
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