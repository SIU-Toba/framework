<?php
require_once("tutorial/pant_tutorial.php");

class pant_definicion extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			<p>
			En el cap�tulo anterior (CI) se evit� un aspecto muy importante de una operaci�n,
			la transacci�n con una base de datos. Las operaciones de Alta, Baja y Modificaci�n
			(ABM) fueron impactando en un arreglo en memoria que se mantiene en sesi�n, pero en
			cuanto el usuario cierra la aplicaci�n o navega hacia otra operaci�n los cambios
			se pierden.
			</p>
			
			<p>
			Para armar una operaci�n 'real' se debe transaccionar con una fuente de datos, generalmente
			es una base de datos relacional as� que vamos a concentrarnos en ellas. Existen dos formas 
			principales en que puede <em>transaccionar</em> una operaci�n, una inmediatamente producidos los eventos
			y la otra reci�n luego de una confirmaci�n expl�cita del usuario.
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
			es la forma m�s f�cil y directa de programar una operaci�n. Simplemente en cada 
			m�todo que escucha un evento se ejecuta un comando SQL y en las configuraciones se cargan
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
			El �nico problema con transaccionar cont�nuamente es que muchas veces es un requisito funcional
			que la edici�n se maneje como una �nica transacci�n, que se cierra cuando el usuario decide presionar 
			<em>Guardar</em>.
			</p>
			<p>
			Este requisito se fundamenta en que si el usuario decide dejar la edici�n por la mitad, lo editado
			queda en un estado <strong>inconsistente</strong>. Esto depende en gran medida de lo que se est� editando,
			por ejemplo si edito la informaci�n de una beca no tiene sentido que el usuario s�lo llene la primer solapa
			si se cuenta con digamos 8 solapas m�s para llenar. En una aplicaci�n web no es posible 'detectar' cuando
			el usuario cierra el navegador, cierra la ventana o navega hacia otra p�gina, no es posible (o es muy dif�cil mejor dicho)
			<em>Deshacer</em> las inconsistencias.
			</p>
			<p>
			La soluci�n que se da a nivel Toba es confiar el manejo de la transacci�n en unos <strong>componentes
			de persistencia</strong>. Estos ser�n los encargados de:
				<ul>
					<li>Hacer las consultas para obtener los datos al inicio de la operaci�n/transacci�n
					<li>Brindar una api para manejar los datos en sesi�n durante la operaci�n
					<li>Analizar los cambios y sincronizarlo con la base de datos al fin de la operaci�n
				</ul>
			<p>
			Estos tres pasos se pueden ver en la siguiente extensi�n de un CI:
			</p>
			<div style='width:230px;float:right;padding: 10px;border: 1px solid gray;background-color:white;'>
				<img src='$img'>
				<span class='caption'>La operaci�n forma una �nica transacci�n a nivel l�gico</span>				
			</div>				
		";
		$codigo = '
<?php
...

	/**
	 * Momento 1: 
	 * Carga inicial de datos, solo se da en el primer pedido de p�gina
	 */
	function ini__operacion()
	{
		$this->dep("direcciones")->cargar();
	}

	/**
	 * Momento 2: 
	 * Eventos y configuraci�n dialogan con la api 
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
	 * Sincronizaci�n con la base de datos, solo se da cuando el usuario presiona GUARDAR
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
			A medida que se van creando operaciones m�s complejas, trabajar con tablas aisladas
			empieza a quedar corto. El problema surge de las relaciones entre las tablas. 
			
			La idea es as�: Una persona tiene juegos y deportes	asociados, se necesita
			una operaci�n que permita editar datos personales, sus juegos y deportes asociados
			en una transacci�n que abarque la operaci�n completa. 		
			Las tablas resaltadas son aquellas que necesitan participar de la transacci�n:
			
			<div style='text-align:center'>
				<img src='$img'>
			</div>
			
			<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ver Operaci�n Terminada</a>
			</p>
				Toba propone definir una relaci�n <em>es padre de</em> entre las tablas.
				En este caso se definir�a as�:<ul>
					<li>persona <em>es padre de</em> persona_deportes
					<li>persona <em>es padre de</em> persona_juegos
				</ul>
				Esta relaci�n implica que:<ul>
					<li>Si un registro de la tabla padre se elimina, se elimina su hijo.
					<li>Cuando se crea un registro del hijo, se asocia autom�ticamente con un registro
						de la tabla padre.
				</ul>
			<p>
				Pasemos a ver c�mo definir estas relaciones en el editor.
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
			La transacci�n a nivel operaci�n se inicia cuando el componente <em>datos_relacion</em>
			se carga con datos. A partir de all� todo el trabajo de Altas, Bajas y Modificaciones
			debe impactar sobre los componentes <em>datos_tabla</em> de esa relaci�n y no se sincronizar� con la base de datos
			hasta el final de la transacci�n.
			</p>
			<p>
			La relaci�n se carga como un todo, generalmente dando alg�n valor clave de las tablas raices.
			En una relaci�n una tabla raiz es aquella que no tiene padre, en este caso <em>ref_persona</em>.
			La carga va formando las SQL de las tablas hijas en base a subselects de la tabla padre. 
			Tambi�n es posible pasarle otros criterios a la carga e incluso armar la consulta manualmente y pasarle directamente 
			los datos a la relaci�n, siendo estos casos menos comunes y no ser�n vistos en este cap�tulo.
			</p>
			<p style='clear:both'>
			Para el caso particular del ejemplo, la relaci�n se carga cuando se selecciona una persona
			del cuadro, pas�ndole la clave de la selecci�n:
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
que internamente utilizan los componentes para cargar la relaci�n (en este caso con la persona '2').
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