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
//--- Si el ejecutar falla (por ej. una restricción de clave foránea), tira una excepcion toba_error_db
?>
';
		echo "
			<p>Cuando se utilizan los componentes de datos (datos_tabla y datos_relacion)
			se hace uso implícito de la base de negocios definida en el editor. A esta base 
			se la denomina <strong>fuente de datos</strong> y puede ser accedida a través de una 
			clase de toba.
			</p>
			<p>Para obtener el objeto que representa la conexión con la base de datos se utiliza el 
			método <em>toba::db(\$fuente)</em>, si no se brinda el nombre de la fuente se utiliza 
			la predeterminada del proyecto. Veamos un ejemplo de una consulta ad-hoc, donde el formato
			del resultado es una matriz filas x columnas (también llamado RecordSet):
			</p>
			".mostrar_php($codigo1)."
			<p>Para los comandos SQL lo que se retorna es el número de registros afectados:</p>
			".mostrar_php($codigo2)."
			<h2>Más info</h2>
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

//Crea un vínculo al item 23421 de este proyecto con un dato como parámetro
$url = toba::vinculador()->get_url(null, 23421, array("moneda" => "dolar"));

//Crea un vínculo al ítem actual
$url = toba::vinculador()->get_url(null, null, $parametros, $opciones);

echo "<a href=\'$url\'>Navegar</a>";
?>
';
		echo "
		<p>
		Existe una clase que permite crear links entre items incluso de distintos proyectos, esta
		clase recibe el nombre de <strong>vinculador</strong>. La utilidad es poder navegar hacia otras 
		operaciones pasando parámetros, o hacia la misma operación accediendo de otra forma.
		</p>
		<p>
		Mostramos algunos ejemplos:
		</p> 
		".mostrar_php($codigo)."
		<p>La interfaz completa de la API está publicada $api</p>
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
//--- Guardar el par $clave=>$valor por lo que resta de la sesión
toba::memoria()->set_dato_aplicacion($clave, $valor);		

//--- Guardar el par $clave=>$valor por lo que resta de la operación
toba::memoria()->set_dato_operacion($clave, $valor);

//-- Para estos tipos de almacenamiento se utiliza este método para consultarlos posteriormente
toba::memoria()->get_dato($clave);

//--- Guardar el par $clave=>$valor para el siguiente pedido de página
toba::memoria()->set_dato_sincronizado($clave, $valor);
//--- Recuperar este valor
toba::memoria()->get_dato_sincronizado($clave);
?>
';
		echo "
			<p>
				Sabemos que las aplicaciones desarrolladas PHP en general no mantienen información
				en memoria entre dos pedidos de página. La forma de recordar información de cada usuario
				es guardarla en un arreglo global de nombre <em>\$_SESSION</em>, garantizando que 
				todo lo que allí se guarda estará disponible a los pedidos siguientes.
			</p>
			<p>
				Otra forma de compartir información entre pedidos es a través de la URL o datos de
				formularios, estos dos conceptos también tienen un arreglo global, <em>\$_GET</em>
				para los datos que provienen de URL y <em>\$_POST</em> para los de formularios. 
				La diferencia principal de estos con respecto a la sesión es que son datos no-seguros 
				(pueden haber sido modificados o leídos por terceros) y que sólo perduran un pedido de página.
			</p>
			<p>
				Lo que propone Toba son mecanismos de almacenamiento de información más sofisticados
				para la aplicación. La idea es dividir los datos según su alcanze, serían tres niveles donde se
				tiene garantizado que la información es segura tanto que no se puede modificar ni leer por 3eros:
			</p>
			<ul>
				<li>Datos <strong>globales a la aplicación</strong>, que por lo general se mantienen durante toda la sesión
				del usuario, como por ejemplo la Universidad a la cual pertenece.
				<li>Datos <strong>globales a una operación</strong>, información que sólo interesa dentro de la operación actual,
				que al cambiar de ítem será descartada.
				<li>Datos que sólo se almacenan para el <strong>siguiente pedido</strong> de página.
			</ul>
			<p>Generalmente en las operaciones el manejo de los datos se hace en forma interna, por ejemplo
			no tenemos que guardar manualmente cual es la solapa actualmente seleccionada sino que el componente es quien
			lo guarda y consulta internamente. La API para almacenar información personalizada tiene razón de ser
			cuando esta información es del propio dominio de la aplicación. Para cubrir estos tres 
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
			Toba cuenta con una clase que va recolectando información
			interna y se almacena en un archivo común de logs del proyecto ubicado en
			<em>\$toba_dir/instalacion/i__X/p__Y/logs</em>, donde X es la instancia e
			Y es el proyecto.
			
			Los programadores también pueden utilizar este log para guardar información
			de debug del sistema. Para esto se consume la clase $api :
			</p>
		";
		echo mostrar_php($codigo);
		$img1 = toba_recurso::imagen_toba('logger.gif', true);
		$img2 = toba_recurso::imagen_proyecto('tutorial/logger.png', true);
		echo "
			<p>
				El archivo de logs generado puede ser analizado con una operación del editor
				creada para ayudar al desarrollo. Este analizador puede ser accedido a través
				del ícono $img1
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
			Para centralizar el manejo de mensajes y permitir su posterior personalización
			Toba brinda la posibilidad de definir los mensajes en el mismo editor web y
			posteriormente instanciarlos y notificarlos usando la API.
		</p>
		<p>Una vez creados los mensajes en el editor es posible recuperarlos en ejecución
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
		
		echo "<h2>Más info</h2>
		 <ul><li>$wiki
			<li><a href='$ejemplo' target='_blank'>Ejemplo</a>
		</ul>
		";
	}
}



?>