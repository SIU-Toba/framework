<?php
require_once("tutorial/pant_tutorial.php");

class pant_definicion extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			<p>
			En el capítulo anterior (CI) se evitó un aspecto muy importante de una operación,
			la transacción con una base de datos. Las operaciones de Alta, Baja y Modificación
			(ABM) fueron impactando en un arreglo en memoria que se mantiene en sesión, pero en
			cuanto el usuario cierra la aplicación o navega hacia otra operación los cambios
			se pierden.
			</p>
			
			<p>
			Para armar una operación 'real' se debe transaccionar con una fuente de datos, generalmente
			es una base de datos relacional así que vamos a concentrarnos en ellas. Existen dos formas 
			principales en que puede <em>transaccionar</em> una operación, una inmediatamente producidos los eventos
			y la otra recién luego de una confirmación explícita del usuario.
			</p>
			<br>
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
			método que escucha un evento se ejecuta un comando SQL y en las configuraciones se cargan
			los componentes directamente desde una consulta SQL:
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
				<ul>
					<li>Hacer las consultas para obtener los datos al inicio de la operación/transacción
					<li>Brindar una api para manejar los datos en sesión durante la operación
					<li>Analizar los cambios y sincronizarlo con la base de datos al fin de la operación
				</ul>
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
		echo "

		";
	}
}

//--------------------------------------------------------

class pant_relaciones extends pant_tutorial 
{
	function generar_layout()
	{
		$vinculo = toba::vinculador()->crear_vinculo(null, 2658, array(), array('celda_memoria'=>'ejemplo'));
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-modelo.png');
		echo "
			A medida que se van creando operaciones más complejas, trabajar con tablas aisladas
			empieza a quedar corto. El problema surge de las relaciones entre las tablas. 
			
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
		
	}
}

//--------------------------------------------------------

class pant_carga extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-carga.png');
		$logger = toba_recurso::imagen_toba('admin/logger.gif', true);
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
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-api.png');
		echo "
			<img style='float:right;padding: 10px;' src='$img'>
		";
	}
}

//--------------------------------------------------------

class pant_sincronizacion extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/persistencia-sincronizacion.png');
		echo "
			<img style='float:right;padding: 10px;' src='$img'>
		";
	}
}



?>