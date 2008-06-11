<?php 
require_once("tutorial/pant_tutorial.php");

class pant_fuente extends pant_tutorial
{
	function generar_layout()
	{
		$wiki = toba_parser_ayuda::parsear_wiki('Referencia/FuenteDatos', 
													'Fuentes de Datos',
													'toba_editor');		
		$api = toba_parser_ayuda::parsear_api('Fuentes/toba_db',
												 'toba_db', 'toba_editor');

		$codigo1 = '<?php
$sql = "SELECT id, nombre FROM tabla..."
$rs = toba::db()->consultar($sql);
if (! empty($rs)) {
    foreach ($rs as $fila) {
        echo "{$fila[\'id\']} es {$fila[\'nombre\']} ";
    } 
} else {
    echo "No hay datos!"
}
//--- Si la consulta falla (por ej. no existe la tabla), tira una excepcion toba_error_db
?>
';	
		$codigo2 = '<?php
$sql = "UPDATE tabla SET nombre = id";

toba::db()->abrir_transaccion();
$cant = toba::db()->ejecutar($sql);
....
toba::db()->cerrar_transaccion();

echo "Se modificaron $cant registros";
//--- Si el ejecutar falla (por ej. una restricci�n de clave for�nea), tira una excepcion toba_error_db
?>
';
		echo "
			<p>Cuando se utilizan los componentes de datos (datos_tabla y datos_relacion)
			se hace uso impl�cito de la base de negocios definida en el editor. A esta base 
			se la denomina <strong>fuente de datos</strong> y puede ser accedida a trav�s de una 
			clase de toba.
			</p>
			<p>Para obtener el objeto que representa la conexi�n con la base de datos se utiliza el 
			m�todo <em>toba::db(\$fuente)</em>, si no se brinda el nombre de la fuente se utiliza 
			la predeterminada del proyecto. Veamos un ejemplo de una consulta ad-hoc, donde el formato
			del resultado es una matriz filas x columnas (tambi�n llamado RecordSet):
			</p>
			".mostrar_php($codigo1)."
			<p>Para los comandos SQL lo que se retorna es el n�mero de registros afectados:</p>
			".mostrar_php($codigo2)."
			<h2>M�s info</h2>
			<ul><li>$wiki
			<li>$api</ul>
		";
	}
}

//--------------------------------------------------------------

class pant_vinculacion extends pant_tutorial
{
	function generar_layout()
	{
		$api = toba_parser_ayuda::parsear_api('Centrales/toba_vinculador', 'toba_vinculador', 'toba_editor');
		$codigo = '<?php
//Forma general
$url = toba::vinculador()->get_url($proyecto, $item, $parametros, $opciones);

//Crea un v�nculo al item 23421 de este proyecto con un dato como par�metro
$url = toba::vinculador()->get_url(null, 23421, array("moneda" => "dolar"));

//Crea un v�nculo al �tem actual
$url = toba::vinculador()->get_url(null, null, $parametros, $opciones);

echo "<a href=\'$url\'>Navegar</a>";
?>
';
		echo "
		<p>
		Existe una clase que permite crear links entre items incluso de distintos proyectos, esta
		clase recibe el nombre de <strong>vinculador</strong>. La utilidad es poder navegar hacia otras 
		operaciones pasando par�metros, o hacia la misma operaci�n accediendo de otra forma.
		</p>
		<p>
		Mostramos algunos ejemplos:
		</p> 
		".mostrar_php($codigo)."
		<p>La interfaz completa de la API est� publicada $api</p>
		";
	}	
}

//--------------------------------------------------------------

class pant_memoria extends pant_tutorial
{
	function generar_layout()
	{
		$api = toba_parser_ayuda::parsear_api('Centrales/toba_memoria', 'toba_memoria', 'toba_editor');
		$codigo = '<?php
//--- Guardar el par $clave=>$valor por lo que resta de la sesi�n
toba::memoria()->set_dato_aplicacion($clave, $valor);		

//--- Guardar el par $clave=>$valor por lo que resta de la operaci�n
toba::memoria()->set_dato_operacion($clave, $valor);

//-- Para estos tipos de almacenamiento se utiliza este m�todo para consultarlos posteriormente
toba::memoria()->get_dato($clave);

//--- Guardar el par $clave=>$valor para el siguiente pedido de p�gina
toba::memoria()->set_dato_sincronizado($clave, $valor);
//--- Recuperar este valor
toba::memoria()->get_dato_sincronizado($clave);
?>
';
		echo "
			<p>
				Sabemos que las aplicaciones desarrolladas PHP en general no mantienen informaci�n
				en memoria entre dos pedidos de p�gina. La forma de recordar informaci�n de cada usuario
				es guardarla en un arreglo global de nombre <em>\$_SESSION</em>, garantizando que 
				todo lo que all� se guarda estar� disponible a los pedidos siguientes.
			</p>
			<p>
				Otra forma de compartir informaci�n entre pedidos es a trav�s de la URL o datos de
				formularios, estos dos conceptos tambi�n tienen un arreglo global, <em>\$_GET</em>
				para los datos que provienen de URL y <em>\$_POST</em> para los de formularios. 
				La diferencia principal de estos con respecto a la sesi�n es que son datos no-seguros 
				(pueden haber sido modificados o le�dos por terceros) y que s�lo perduran un pedido de p�gina.
			</p>
			<p>
				Lo que propone Toba son mecanismos de almacenamiento de informaci�n m�s sofisticados
				para la aplicaci�n. La idea es dividir los datos seg�n su alcanze, ser�an tres niveles donde se
				tiene garantizado que la informaci�n es segura tanto que no se puede modificar ni leer por 3eros:
			</p>
			<ul>
				<li>Datos <strong>globales a la aplicaci�n</strong>, que por lo general se mantienen durante toda la sesi�n
				del usuario, como por ejemplo la Universidad a la cual pertenece.
				<li>Datos <strong>globales a una operaci�n</strong>, informaci�n que s�lo interesa dentro de la operaci�n actual,
				que al cambiar de �tem ser� descartada.
				<li>Datos que s�lo se almacenan para el <strong>siguiente pedido</strong> de p�gina.
			</ul>
			<p>Generalmente en las operaciones el manejo de los datos se hace en forma interna, por ejemplo
			no tenemos que guardar manualmente cual es la solapa actualmente seleccionada sino que el componente es quien
			lo guarda y consulta internamente. La API para almacenar informaci�n personalizada tiene raz�n de ser
			cuando esta informaci�n es del propio dominio de la aplicaci�n. Para cubrir estos tres 
			niveles la clase $api brinda estas primitivas:
			</p>
			
		";	
		echo mostrar_php($codigo);
	}	
}

//--------------------------------------------------------------

class pant_logger extends pant_tutorial
{
	function generar_layout()
	{
		$api = toba_parser_ayuda::parsear_api('Debug/toba_logger', 'toba_logger', 'toba_editor');				
		$codigo = '<?php
//--- Guardar un mensaje de debug
toba::logger()->debug($mensaje);

//--- Guardar un error
toba::logger()->error($mensaje);

//--- Guardar el valor de una variable
toba::logger()->var_dump($variable);

//--- Guardar una traza completa de llamadas
toba::logger()->trace();
?>
';
		echo "
			<p>
			Toba cuenta con una clase que va recolectando informaci�n
			interna y se almacena en un archivo com�n de logs del proyecto ubicado en
			<em>\$toba_dir/instalacion/i__X/p__Y/logs</em>, donde X es la instancia e
			Y es el proyecto.
			
			Los programadores tambi�n pueden utilizar este log para guardar informaci�n
			de debug del sistema. Para esto se consume la clase $api :
			</p>
		";
		echo mostrar_php($codigo);
		$img1 = toba_recurso::imagen_toba('logger.gif', true);
		$img2 = toba_recurso::imagen_proyecto('tutorial/logger.png', true);
		echo "
			<p>
				El archivo de logs generado puede ser analizado con una operaci�n del editor
				creada para ayudar al desarrollo. Este analizador puede ser accedido a trav�s
				del �cono $img1
			</p>
			$img2
		";
	}	
}

//--------------------------------------------------------------

class pant_mensajes extends pant_tutorial
{
	function generar_layout()
	{
		$api1 = toba_parser_ayuda::parsear_api('Fuentes/toba_mensajes', 'toba_mensajes', 'toba_editor');		
		$api2 = toba_parser_ayuda::parsear_api('SalidaGrafica/toba_notificacion', 'toba_notificacion', 'toba_editor');		
		$img = toba_recurso::imagen_proyecto('tutorial/notificacion.png', true);
		$wiki = toba_parser_ayuda::parsear_wiki('Referencia/Mensajes', 
													'Mensajes y Notificaciones',
													'toba_editor');
		$ejemplo = 	toba::vinculador()->get_url(null, 1000204, array(), array('celda_memoria'=>'ejemplo'));
		
		$codigo1 = '<?php
//Suponiendo que el mensaje ingresado es: \'Esta es la %1% instancia de un mensaje global de Toba. Fecha de hoy: %2%.\'
$mensaje = toba::mensajes()->get("indice", array("primera", date("d/M/Y"));
echo $mensaje;
//La salida es: \'Esta es la primera instancia de un mensaje global de Toba. Fecha de hoy: 01/02/2007.\'
?>
';
		$codigo2 = '<?php
toba::notificacion()->agregar($mensaje);
toba::notificacion()->agregar($mensaje, "info");
?>
';		
		echo "<p>
			Para centralizar el manejo de mensajes y permitir su posterior personalizaci�n
			Toba brinda la posibilidad de definir los mensajes en el mismo editor web y
			posteriormente instanciarlos y notificarlos usando la API.
		</p>
		<p>Una vez creados los mensajes en el editor es posible recuperarlos en ejecuci�n
		usando la clase $api1:
		</p>
		";
		echo mostrar_php($codigo1);
		echo "<p>
			En lugar de mostrar el mensaje con un simple <em>echo</em> es posible notificarlo
			utilizando la clase $api2: 
		</p>";
		echo mostrar_php($codigo2);
		echo $img;
		
		echo "<h2>M�s info</h2>
		 <ul><li>$wiki
			<li><a href='$ejemplo' target='_blank'>Ejemplo</a>
		</ul>
		";
	}
}



?>