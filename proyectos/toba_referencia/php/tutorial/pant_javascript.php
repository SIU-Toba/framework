<?php 
require_once("tutorial/pant_tutorial.php");

//--------------------------------------------------------------
class pant_introduccion extends pant_tutorial
{
	function generar_layout()
	{
		$api = toba_parser_ayuda::parsear_api_js('index', 'documentación javascript', 'toba_editor');				
		$codigo_ej = '<?php
class ci_X extends toba_ci
{		
	    function extender_objeto_js()
	    {
	    	echo "
	    		{$this->objeto_js}.evt__guardar = function() {
	    			return prompt(\"Desea Guardar?\");
	    		}
	    	";
	    }
}
?>
';		
		echo "
			<p>
				En el capítulo de componentes se utilizó la extensión PHP
				poder personalizar su comportamiento. Dentro de la extensión en PHP
				es posible modificar el comportamiento del componente en el cliente utilizando 
				javascript. En este capítulo se va trabajar exclusivamente con la parte Javascript
				de los componentes, para esto Toba cuenta con una jerarquía de clases
				similar a la que existe en PHP, para profundizar sobre la API está disponible la $api.
			</p>
			
			<p>
				Es importante tener en cuenta la forma en la cual se extiende un componente en javascript.
				A continuación se muestra un código muy simple que agrega una confirmación en el cliente 
				cuando el usuario clickea Guardar: 
			</p>
		";
		echo mostrar_php($codigo_ej);
		echo "
			<p>
				El código muestra que el método PHP a extender es <strong>extender_objeto_js()</strong>
				dentro del cual es necesario insertar el código Javascript. Este lenguaje no
				soporta clases en la forma convencional de los lenguajes Java o PHP, por lo cual
				no se <em>hereda</em> del componente sino que directamente se lo cambia, esto es 
				por ejemplo si se quiere agregar un método a un objeto <em>mi_componente</em>
				se hace definiendo <em>mi_componente.tal_metodo = function() { var i = 20; ...}</em>.
			</p>
			<p>
				Entonces en la extensión PHP, se extiende la <strong>clase</strong> (por ejemplo
				toba_ei_formulario) mientras que en la de Javascript se extiende el <strong>
				objeto</strong> puntual. El nombre de este objeto es desconocido al programador
				(se compone del id del componente) por lo que es necesario pedirselo a la clase PHP
				por eso se hace	<em>{\$this->objeto_js}.metodo = ...</em>.
			</p>
			<p>
				Finalmente cabe recalcar que las extensiones javascript se hacen dentro del mismo
				componente por motivos de orden y modularidad, pero no es necesariamente la única forma
				ya que el javascript en definitiva forma parte del HTML resultante de la operación, si
				se mira el código fuente de la página HTML se podrá ver la extensión y su entorno.
			</p>
		";
	}
}


//--------------------------------------------------------------
class pant_eventos extends pant_tutorial
{
	function generar_layout()
	{
		$codigo = '{$this->objeto_js}.evt__sacar_foto = function() {
	this._parametros = prompt("Nombre de la foto","nombre de la foto");
	if (this._parametros != "" && this._parametros != null) {
		return true;
	}
	return false;
}';
		echo "
			<p>
				Así como el evento en PHP significa la interacción del usuario con el servidor, en 
				Javascript existe el mismo criterio, sólo que es la previa de esta interacción.
				Un evento antes de viajar al servidor escucha un <em>listener</em> en javascript.
				Por ejemplo si un formulario dispara un evento <em>modificacion</em>, en la extensión
				del mismo formulario se puede atrapar el método <em>evt__modificacion</em> y 
				retornar true/false para permitir o no la ejecución del evento (entre otras cosas que se 
				pueden hacer).
			</p>
			<p>
				Un ejemplo real es el siguiente javascript perteneciente al catalogo de items
				del editor, la idea es que cuando el usuario presiona el botón <em>Sacar Foto</em>
				se le pregunte en Javascript el nombre que toma la foto y luego se tome la foto
				en el servidor. En this._parametros se guarda el valor del parámetro
				que termina en el servidor (en este caso método evt__X__sacar_foto(\$nombre_foto))
			</p>
		";
		echo mostrar_php($codigo);
	}	
}


//--------------------------------------------------------------
class pant_metodos extends pant_tutorial
{
	function generar_layout()
	{
		echo "
		
		";
	}	
}


//--------------------------------------------------------------
class pant_formulario extends pant_tutorial
{
	function generar_layout()
	{
		
	}	
}

?>