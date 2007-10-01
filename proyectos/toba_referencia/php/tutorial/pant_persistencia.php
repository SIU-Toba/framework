<?php
require_once("tutorial/pant_tutorial.php");

class pant_definicion extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			<p>
			En el capítulo anterior (CI) se dejo de lado un aspecto muy importante de una operación:
			la transacción con una base de datos. Las operaciones de Alta, Baja y Modificación
			(ABM) fueron impactando en un arreglo en memoria que se mantiene en sesión, pero en
			cuanto el usuario cierra la aplicación o navega hacia otra operación los cambios
			se pierden.
			</p>
			
			<p>
			Para armar una operación 'real' se debe transaccionar con una fuente de datos, generalmente
			esta fuente es una base de datos relacional así que vamos a concentrarnos en ellas. Existen dos formas 
			principales en que puede <em>transaccionar</em> una operación:
			 <ul>
			 	<li>inmediatamente producidos los eventos, prácticamente en cada pedido de página, o
			 	<li>luego de una confirmación explícita del usuario.
			 </ul>
		";

	}
}

//--------------------------------------------------------

class pant_inmediata extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-inmediato.png');		
		echo "
			<p>
			Si los requisitos funcionales lo permiten, transaccionar inmediatamente cuando se produce el evento
			es la forma más fácil y directa de programar una operación. Simplemente en cada 
			método que escucha un evento se ejecuta un comando SQL, y en las configuraciones se cargan
			los componentes directamente usando consultas SQL:
			</p>
			<div style='float:right;padding: 10px;border: 1px solid gray;background-color:white;'>
				<img src='$img'>
			</div>					
		";
		$codigo = '
<?php
...

	function evt__form__alta($datos)
	{
		$sql = "INSERT INTO direcciones (email, nombre) VALUES 
					(\"$datos[\'email\']\", \"$datos[\'nombre\']\")";
		toba::db()->ejecutar($sql);
	}

	function evt__form__modificacion($datos)
	{
		$sql = "UPDATE direcciones SET nombre = \"$datos[\'nombre\']\" 
					WHERE email=\"$datos[\'email\']\"";
		toba::db()->ejecutar($sql);
	}
	
	function evt__form__baja()
	{
		$sql = "DELETE FROM direcciones WHERE email=\"$datos[\'email\']\"";
		toba::db()->ejecutar($sql);
	}
	
	function conf__form(toba_ei_formulario $form)
	{
		$sql = "SELECT email, nombre FROM direcciones WHERE email=\'{$this->s__actual}\'";
		$datos = toba::db()->consultar_fila($sql);
		$form->set_datos($datos);
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$sql = "SELECT email, nombre FROM direcciones";
		$datos = toba::db()->consultar($sql);
		$cuadro->set_datos($datos);
	}
...
?>
';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
				

	}
}

//--------------------------------------------------------

class pant_marco extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-marco.png');				
		echo "
			<p>
			El único problema con transaccionar contínuamente es que muchas veces es un requisito funcional
			que la edición se maneje como una única transacción, que se cierra cuando el usuario decide presionar 
			<em>Guardar</em>.
			</p>
			<p>
			Este requisito se fundamenta en que si el usuario decide dejar la edición por la mitad, lo editado
			queda en un estado <strong>inconsistente</strong>. Esto depende en gran medida de lo que se está editando,
			por ejemplo si edito la información de una beca no tiene sentido que el usuario sólo llene la primer solapa
			si se cuenta con digamos 8 solapas más para llenar. En una aplicación web no es posible 'detectar' cuando
			el usuario cierra el navegador, cierra la ventana o navega hacia otra página, no es posible (o es muy difícil mejor dicho)
			<em>Deshacer</em> las inconsistencias.
			</p>
			<p>
			La solución que se da a nivel Toba es confiar el manejo de la transacción en unos <strong>componentes
			de persistencia</strong>. Estos serán los encargados de:
				<ol>
					<li>Hacer las consultas para obtener los datos al inicio de la operación/transacción
					<li>Brindar una api para manejar los datos en sesión durante la operación
					<li>Analizar los cambios y sincronizarlo con la base de datos al fin de la operación
				</ol>
			<p>
			Estos tres pasos se pueden ver en la siguiente extensión de un CI:
			</p>
			<div style='width:230px;float:right;padding: 10px;border: 1px solid gray;background-color:white;'>
				<img src='$img'>
				<span class='caption'>La operación forma una única transacción a nivel lógico</span>				
			</div>				
		";
		$codigo = '
<?php
...

	/**
	 * Momento 1: 
	 * Carga inicial de datos, solo se da en el primer pedido de página
	 */
	function ini__operacion()
	{
		$this->dep("direcciones")->cargar();
	}

	/**
	 * Momento 2: 
	 * Eventos y configuración dialogan con la api 
	 */
	
	function evt__form__alta($datos)
	{
		$this->dep("direcciones")->nueva_fila($datos);
	}

	function evt__form__baja()
	{
		$this->dep("direcciones")->eliminar_fila($this->s__actual);
	}
		
	function evt__form__modificacion($datos)
	{
		$this->dep("direcciones")->modificar_fila($this->s__actual, $datos);	
	}
		
	function conf__form(toba_ei_formulario $form)
	{
		$datos = $this->dep("direcciones")->get_fila($this->s__actual);
		$form->set_datos($datos);
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->dep("direcciones")->get_filas();
		$cuadro->set_datos($datos);
	}

	/**
	 * Momento 3:
	 * Sincronización con la base de datos, solo se da cuando el usuario presiona GUARDAR
	 */	
	function evt__procesar()
	{
		$this->dep("direcciones")->sincronizar();
	}
	
...
?>
';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>
		<p>
			Para poder utilizar estos componentes primero hay que definirlos en el editor, empezaremos
			por definir un <strong>datos_tabla</strong> que es el componente encargado de persistir
			una tabla de la base de datos.
		</p>
		
		";	
	}
}

//--------------------------------------------------------

class pant_def_tablas extends pant_tutorial 
{
	function generar_layout()
	{
		echo mostrar_video('persistencia-tablas');
	}
}

//--------------------------------------------------------

class pant_relaciones extends pant_tutorial 
{
	function generar_layout()
	{
		$vinculo = toba::vinculador()->get_url(null, 2658, array(), array('celda_memoria'=>'ejemplo'));
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-modelo.png');
		echo "
			A medida que se van creando operaciones más complejas, trabajar con tablas aisladas
			empieza a quedar corto. El problema surge de las relaciones entre las tablas, podemos verlo
			con un ejemplo.
			
			La idea es así: Una persona tiene juegos y deportes	asociados, se necesita
			una operación que permita editar datos personales, sus juegos y deportes asociados
			en una transacción que abarque la operación completa. 		
			Las tablas resaltadas son aquellas que necesitan participar de la transacción:
			
			<div style='text-align:center'>
				<img src='$img'>
			</div>
			
			<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ver Operación Terminada</a>
			</p>
				Toba propone definir una relación <em>es padre de</em> entre las tablas.
				En este caso se definiría así:<ul>
					<li>persona <em>es padre de</em> persona_deportes
					<li>persona <em>es padre de</em> persona_juegos
				</ul>
				Esta relación implica que:<ul>
					<li>Si un registro de la tabla padre se elimina, se elimina su hijo.
					<li>Cuando se crea un registro del hijo, se asocia automáticamente con un registro
						de la tabla padre.
				</ul>
			<p>
				Pasemos a ver cómo definir estas relaciones en el editor.
			</p>
		";		
	}
}

//--------------------------------------------------------

class pant_def_relaciones extends pant_tutorial 
{
	function generar_layout()
	{
		echo mostrar_video('persistencia-relacion');
	}
}

//--------------------------------------------------------

class pant_carga extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-carga.png');
		$logger = toba_recurso::imagen_toba('logger.gif', true);
		echo "
			<img style='float:right;padding: 10px;' src='$img'>
			<p>
			La transacción a nivel operación se inicia cuando el componente <em>datos_relacion</em>
			se carga con datos. A partir de allí todo el trabajo de Altas, Bajas y Modificaciones
			debe impactar sobre los componentes <em>datos_tabla</em> de esa relación y no se sincronizará con la base de datos
			hasta el final de la transacción.
			</p>
			<p>
			La relación se carga como un todo, generalmente dando algún valor clave de las tablas raices.
			En una relación una tabla raiz es aquella que no tiene padre, en este caso <em>ref_persona</em>.
			La carga va formando las SQL de las tablas hijas en base a subselects de la tabla padre. 
			También es posible pasarle otros criterios a la carga e incluso armar la consulta manualmente y pasarle directamente 
			los datos a la relación, siendo estos casos menos comunes y no serán vistos en este capítulo.
			</p>
			<p style='clear:both'>
			Para el caso particular del ejemplo, la relación se carga cuando se selecciona una persona
			del cuadro, pasándole la clave de la selección:
			</p>
		";
		
		$codigo = '
<?php
	function evt__cuadro_personas__seleccion($id)
	{
		$this->dep("relacion")->cargar($id);
		$this->set_pantalla("edicion");
	}		
?>
';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";
?>
<p>
Si utilizamos el <?php echo $logger; ?> <strong>visor del logger</strong> durante la carga podemos ver las consultas
que internamente utilizan los componentes para cargar la relación (en este caso con la persona '2').
</p>
<div class='codigo' style="font-size: 80%">
<ul>
<li> componente(1732): toba_datos_relacion: ***   Inicio CARGAR ****************************</li>
<li> componente(1735): toba_datos_tabla: RESET!!</li>
<li> componente(1734): toba_datos_tabla: RESET!!</li>
<li> componente(1733): toba_datos_tabla: RESET!!</li>
<li> AP: toba_ap_tabla_db_s- TABLA: ref_persona - OBJETO: toba_datos_tabla -- <pre>
SQL de carga: 
SELECT
	persona.id, 
	persona.nombre, 
	persona.fecha_nac
FROM
	ref_persona as persona
WHERE
	( persona.id = 2 )</pre></li>
<li> componente(1733): toba_datos_tabla: Carga de datos</li>
<li> AP: ap_persona_deportes- TABLA: ref_persona_deportes - OBJETO: toba_datos_tabla -- <pre>
SQL de carga: 
SELECT
	deportes.id, 
	deportes.persona, 
	deportes.deporte, 
	deportes.dia_semana, 
	deportes.hora_inicio, 
	deportes.hora_fin
FROM
	ref_persona_deportes as deportes
WHERE
	(deportes.persona) IN (
		SELECT
			id
		FROM
			ref_persona as persona
		WHERE
			( persona.id = 2 ) )</pre></li><li> componente(1735): toba_datos_tabla: Carga de datos</li><li> AP: ap_persona_juegos- TABLA: ref_persona_juegos - OBJETO: toba_datos_tabla -- <pre>
SQL de carga: 
SELECT
	juegos.id, 
	juegos.persona, 
	juegos.juego, 
	juegos.dia_semana, 
	juegos.hora_inicio, 
	juegos.hora_fin
FROM
	ref_persona_juegos as juegos
WHERE
	(juegos.persona) IN (
		SELECT
			id
		FROM
			ref_persona as persona
		WHERE
			( persona.id = 2 ) )</pre></li>
<li> componente(1734): toba_datos_tabla: Carga de datos</li>
<li> componente(1732): toba_datos_relacion: ***   Fin CARGAR (OK) *************************
</ul>
</div>
<?php
	}
}

//--------------------------------------------------------

class pant_api extends pant_tutorial 
{
	function generar_layout()
	{
		$vinculo = toba::vinculador()->get_url(null, 2658, array(), array('celda_memoria'=>'ejemplo'));		
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-api.png');
		$api = toba_parser_ayuda::parsear_api('Componentes/Persistencia/toba_datos_tabla#sec-method-summary',
												 'documentación del datos_tabla', 'toba_editor');
		echo "
			<img style='float:right;padding: 10px;' src='$img'>
			<p>
			Una vez cargada la relación es posible consumir el API para manipular los registros en memoria.
			Las operaciones clásicas sobre la tabla son:</p>
			<ul>
				<li>get_fila(\$clave)
				<li>nueva_fila(\$registro)
				<li>modificar_fila(\$clave, \$registro)
				<li>eliminar_fila(\$clave)
			</ul>
			<p>
			Cuando se conoce de antemano que una tabla en la transacción puede tener un único registro se dispone
			de dos métodos:</p>
			<ul>
				<li>set(\$registro)
				<li>get()
			</ul>
				
			<p>
			Muchas de estas primitivas hacen referencia a una <strong>\$clave</strong>, vale notar
			que este valor no es la clave real del registro en la base sino un valor interno
			que maneja el datos_tabla. Por ejemplo al crear una nueva_fila se le brinda una clave para 
			futuras referencias siendo que aún en la base aún no existe esta fila.
			Existe más información sobre las primitivas en la $api.
			</p>
			
			<p style='clear:both'>
			Volviendo al ejemplo, podemos ver el código que trabaja sobre los
			datos básicos y los deportes de una persona,
			tomado directamente de la <a href='$vinculo' target='_blank'>operación</a>:
			</p>
		";
		$codigo ='
<?php
...
	//-------------------------------------------------------------------
	//--- Pantalla "persona"
	// Se sabe de antemano que los datos de una persona es un único registro por lo 
	// que se trabaja con la api get/set	
	//-------------------------------------------------------------------

	function conf__form_persona()
	{
	  return $this->get_relacion()->tabla("persona")->get();
	}

	function evt__form_persona__modificacion($registro)
	{
		$this->get_relacion()->tabla("persona")->set($registro);
	}
	
	//-------------------------------------------------------------------
	//--- Pantalla "deportes"
	//-------------------------------------------------------------------

	//-- Cuadro --

	/**
	 * El cuadro de deportes contiene todos los registros de deportes disponibles
	 */
	function conf__cuadro_deportes()	
	{
		return $this->get_relacion()->tabla("deportes")->get_filas();	
	}

	function evt__cuadro_deportes__seleccion($seleccion) {	
		$this->s__deporte = $seleccion;
	}

	//-- Formulario --
		
	/**
	 *	Se carga al formulario con el deporte actualmente seleccionado
	 */
	function conf__form_deportes()
	{
		if(isset($this->s__deporte)) {	
			return $this->get_relacion()->tabla("deportes")->get_fila($this->s__deporte);	
		}
	}

	/**
	 *	Se modifica el registro y se limpia el formulario
	 */
	function evt__form_deportes__modificacion($registro)
	{
		if(isset($this->s__deporte)){
			$this->get_relacion()->tabla("deportes")->modificar_fila($this->s__deporte, $registro);	
			$this->evt__form_deportes__cancelar();	
		}
	}

	/**
	 *	Se borra el registro y se limpia el formulario
	 */	
	function evt__form_deportes__baja()
	{
		if(isset($this->s__deporte)){
			$this->get_relacion()->tabla("deportes")->eliminar_fila( $this->s__deporte );	
			$this->evt__form_deportes__cancelar();	
		}
	}

	/**
	 *	Se crea una nueva fila
	 */		
	function evt__form_deportes__alta($registro)
	{
		$this->get_relacion()->tabla("deportes")->nueva_fila($registro);
	}
	
	function evt__form_deportes__cancelar()
	{
		unset($this->s__deporte);
	}
...	
?>
';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";		
	}
}

//--------------------------------------------------------

class pant_sincronizacion extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-sincronizacion.png');
		$logger = toba_recurso::imagen_toba('logger.gif', true);		
		echo "
			<img style='float:right;padding: 10px;' src='$img'>
			<p>
			Finalmente la transacción finaliza sincronizando con el medio de persistencia, 
			en este caso la base de datos. Durante este proceso se analizan todos los cambios
			que se produjeron desde la carga y se arma un plan de sincronización formando
			los comandos SQL necesarios.
			
			</p>
		";
		$codigo = '
<?php
...
	/**
	 * Cuando presiona GUARDAR se sincroniza con la base, se resetea la relacion y se cambia de pantalla
	 */
	function evt__procesar()
	{
		$this->get_relacion()->sincronizar();
		$this->get_relacion()->resetear();
		$this->set_pantalla("seleccion");
	}	

	/**
	 * Cuando presiona ELIMINAR se eliminan todos los registros de todas las tablas y 
	 * se sincroniza con la base (implicito), luego se cambia de pantalla
	 */
	function evt__eliminar()
	{
		$this->get_relacion()->eliminar();
		$this->set_pantalla("seleccion");
	}

	/**
	 * Cuando se presiona CANCELAR se descartan todos los cambio y se cambia de pantalla
	 */
	function evt__cancelar()
	{
		$this->get_relacion()->resetear();
		$this->set_pantalla("seleccion");
	}
...
?>
		';
		echo "<div class='codigo' style='clear:both'>";
		highlight_string($codigo);
		echo "</div>";	
?>
<p>
Si utilizamos el <?php echo $logger; ?> <strong>visor del logger</strong> durante la sincronización podemos ver los comandos
que generan los componentes para sincronizar la relación (en este caso se modifica la persona, un deporte y se agrego uno nuevo).
</p>

<div class='codigo' style="font-size: 80%">
<ul>
<li > ************ ABRIR transaccion (toba_trunk@localhost) ****************</li>
<li > AP: ap_persona_deportes- TABLA: ref_persona_deportes - OBJETO: toba_datos_tabla -- <pre>

registro: 1 - INSERT INTO ref_persona_deportes ( persona, deporte, dia_semana, hora_inicio, hora_fin ) 
 VALUES (2, 3, 0, 10, 11);</pre></li><li > AP: toba_ap_tabla_db_s- TABLA: ref_persona - OBJETO: toba_datos_tabla -- <pre>
registro: 0 - UPDATE ref_persona SET nombre = 'Jose Enrique', fecha_nac = '2000-05-08' WHERE ( id = 2 );</pre></li><li > AP: ap_persona_deportes- TABLA: ref_persona_deportes - OBJETO: toba_datos_tabla -- <pre>
registro: 0 - UPDATE ref_persona_deportes SET persona = 2, deporte = 6, dia_semana = 4, hora_inicio = 17, hora_fin = 22 WHERE ( id = 2 );</pre>
</li>
<li > ************ CERRAR transaccion (toba_trunk@localhost) ****************</li>
</ul>
</div>
<?php
	}
}

//--------------------------------------------------------

class pant_masinfo extends pant_tutorial 
{
	function generar_layout()
	{
		$wiki1 = toba_parser_ayuda::parsear_wiki('Referencia/Objetos/Persistencia', 
													'Introducción a los componentes de persistencia',
													'toba_editor');
		$wiki2 = toba_parser_ayuda::parsear_wiki('Referencia/Objetos/datos_tabla', 
													'Documentación del datos_tabla',
													'toba_editor');
		$wiki3 = toba_parser_ayuda::parsear_wiki('Referencia/Objetos/datos_relacion', 
													'Documentación del datos_relacion',
													'toba_editor');
		$api1 = toba_parser_ayuda::parsear_api('Componentes/Persistencia/toba_datos_tabla',
												 'Primitivas del datos_tabla', 'toba_editor');
		$api2 = toba_parser_ayuda::parsear_api('Componentes/Persistencia/toba_datos_relacion',
												 'Primitivas del datos_relacion', 'toba_editor');
												 
		echo "
			<ul>
				<li>$wiki1
				<li>$wiki2
				<li>$wiki3
				<li>$api1
				<li>$api2
			</ul>
		";
	}
}


?>