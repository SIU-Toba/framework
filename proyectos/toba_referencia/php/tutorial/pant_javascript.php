<?php 
require_once("tutorial/pant_tutorial.php");

//--------------------------------------------------------------
class pant_introduccion extends pant_tutorial
{
	function generar_layout()
	{
		$api = toba_parser_ayuda::parsear_api_js('index', 'documentaci�n javascript', 'toba_editor');				
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
				En el cap�tulo de componentes se utiliz� la extensi�n PHP
				para personalizar su comportamiento. Dentro de la extensi�n en PHP
				es posible modificar el comportamiento del componente en el cliente utilizando 
				javascript. En este cap�tulo se va trabajar exclusivamente con la parte Javascript
				de los componentes, para esto Toba cuenta con una jerarqu�a de clases
				similar a la que existe en PHP, para profundizar sobre la API est� disponible la $api.
			</p>
			
			<p>
				Es importante tener en cuenta la forma en la cual se extiende un componente en javascript.
				A continuaci�n se muestra un c�digo muy simple que agrega una confirmaci�n en el cliente 
				cuando el usuario clickea Guardar: 
			</p>
		";
		echo mostrar_php($codigo_ej);
		echo "
			<p>
				El c�digo muestra que el m�todo PHP a extender es <strong>extender_objeto_js()</strong>
				dentro del cual es necesario insertar el c�digo Javascript. Este lenguaje no
				soporta clases en la forma convencional de los lenguajes Java o PHP, por lo cual
				no se <em>hereda</em> del componente sino que directamente se lo cambia, esto es 
				por ejemplo si se quiere agregar un m�todo a un objeto <em>mi_componente</em>
				se hace definiendo <em>mi_componente.tal_metodo = function() { var i = 20; ...}</em>.
			</p>
			<p>
				Entonces en la extensi�n PHP, se extiende la <strong>clase</strong> (por ejemplo
				toba_ei_formulario) mientras que en la de Javascript se extiende el <strong>
				objeto</strong> puntual. El nombre de este objeto es desconocido al programador
				(se compone del id del componente) por lo que es necesario pedirselo a la clase PHP
				por eso se hace	<em>{\$this->objeto_js}.metodo = ...</em>.
			</p>
			<p>
				Finalmente cabe recalcar que las extensiones javascript se hacen dentro del mismo
				componente por motivos de orden y modularidad, pero no es necesariamente la �nica forma
				ya que el javascript en definitiva forma parte del HTML resultante de la operaci�n, si
				se mira el c�digo fuente de la p�gina HTML se podr� ver la extensi�n y su entorno.
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
				As� como el evento en PHP significa la interacci�n del usuario con el servidor, en 
				Javascript existe el mismo criterio, s�lo que es la previa de esta interacci�n.
				Un evento antes de viajar al servidor escucha un <em>listener</em> en javascript.
				Por ejemplo si un formulario dispara un evento <em>modificacion</em>, en la extensi�n
				del mismo formulario se puede atrapar el m�todo <em>evt__modificacion</em> y 
				retornar true/false para permitir o no la ejecuci�n del evento (entre otras cosas que se 
				pueden hacer).
			</p>
			<p>
				Un ejemplo real es el siguiente javascript perteneciente al catalogo de items
				del editor, la idea es que cuando el usuario presiona el bot�n <em>Sacar Foto</em>
				se le pregunte en Javascript el nombre que toma la foto y luego se tome la foto
				en el servidor. En this._parametros se guarda el valor del par�metro
				que termina en el servidor (en este caso el m�todo evt__X__sacar_foto(\$nombre_foto))
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
		$codigo = '{$this->objeto_js}.iniciar_viejo = {$this->objeto_js}.iniciar;

{$this->objeto_js}.iniciar = function() {
	//Extensi�n
	this.iniciar_viejo(); //Llamada al original
	//Extensi�n
}';		
		echo "
			<p>
				La opci�n a atrapar eventos predefinidos es redefinir el comportamiento
				de m�todos ya existentes. En general no es una metodolog�a recomendada
				pero a veces es necesaria para casos no contemplados.
			</p>
			<p>
				Al no existir la herencia cl�sica, la redefinici�n del m�todo tiene
				que simularla manualmente, esto es guardar el m�todo viejo y definir el
				nuevo llamando cuando sea necesario al viejo. Vemos un ejemplo:
			</p>
		";
		echo mostrar_php($codigo);
	}	
}


?>