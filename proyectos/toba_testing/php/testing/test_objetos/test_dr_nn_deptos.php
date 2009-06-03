<?php
require_once("base_test_datos.php");

class test_dr_nn_deptos extends base_test_datos
{
	/**
	 * @var toba_datos_relacion
	 */
	protected $dr;
	
	function SetUp()
	{
		ejecutar_sql( $this->get_sql_juego_datos() );
		$this->dr = $this->get_dr();
	}

	function TearDown()
	{
		ejecutar_sql( $this->get_sql_eliminar_juego_datos() );
		unset($this->dr);
	}

	function get_descripcion()
	{
		//En 1 departamento trabajan N empleados (un empleado solo puede estar en un departamento), 
		//1 empleado tiene N tareas, 1 Tarea puede estar en N empleados
		//1 tarea puede estar en N departamentos, 1 departamento tiene N tareas
		return "OBJETO datos_relacion (N-N Deptos.)";
	}	

	function get_dr()
	{
		$dr = toba_constructor::get_runtime(array('proyecto' => 'toba_testing', 'componente' => '1625'), 
											'toba_datos_relacion');
		$dr->inicializar();
		return $dr;
	}

	function get_sql_tablas()
	{
		//Los nombres de los campos que son FK son distintos a los de la tabla referenciada con intención
		$sql[] = "CREATE SEQUENCE seq_depto INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;";
		$sql[] = "CREATE TABLE test_depto (
						cod_depto 			int4			DEFAULT nextval('\"seq_depto\"'::text) NOT NULL, 
						nombre				VARCHAR(20) 	NOT NULL, 
						CONSTRAINT test_dpto_pkey PRIMARY KEY(cod_depto)
					);";
		$sql[] = "CREATE SEQUENCE seq_empl INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;";		
		$sql[] = "CREATE TABLE test_empleado (
						cod_empleado		INT4 			DEFAULT nextval('\"seq_empl\"'::text) NOT NULL, 
						depto				INT4			NOT NULL,
					  	nombre				VARCHAR(20) 	NOT NULL, 
					  	CONSTRAINT test_empleado_pkey PRIMARY KEY(cod_empleado),
						FOREIGN KEY (depto) REFERENCES test_depto(cod_depto) ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE
					);";
		$sql[] = "CREATE SEQUENCE seq_tarea INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;";
		$sql[] = "CREATE TABLE test_tarea (
						cod_tarea			INT4 		DEFAULT nextval('\"seq_tarea\"'::text) NOT NULL,
						nombre				VARCHAR(20)	NOT NULL,
					  	CONSTRAINT test_tarea_pkey PRIMARY KEY(cod_tarea)
					);
					";
		$sql[] = "CREATE TABLE test_empleado_tareas (
						empleado			INT4 		NOT NULL, 
						tarea				INT4		NOT NULL,
					  	prioritaria			SMALLINT	DEFAULT 0,
					  	CONSTRAINT test_empleado_tareas_pkey PRIMARY KEY(empleado, tarea),
						FOREIGN KEY (empleado) REFERENCES test_empleado(cod_empleado) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE,
						FOREIGN KEY (tarea) REFERENCES test_tarea(cod_tarea) ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE
					);
					";
		$sql[] = "CREATE TABLE test_depto_tareas (
						tarea				INT4		NOT NULL,
						depto				INT4		NOT NULL,
					  	CONSTRAINT test_depto_tareas_pkey PRIMARY KEY(tarea, depto),
						FOREIGN KEY (tarea) REFERENCES test_tarea(cod_tarea) ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE,
						FOREIGN KEY (depto) REFERENCES test_depto(cod_depto) ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE						
					);
					";
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_depto_tareas;";
		$sql[] = "DROP TABLE test_empleado_tareas;";
		$sql[] = "DROP TABLE test_tarea;";
		$sql[] = "DROP TABLE test_empleado;";
		$sql[] = "DROP TABLE test_depto";
		$sql[] = "DROP SEQUENCE seq_empl;";		
		$sql[] = "DROP SEQUENCE seq_depto;";
		$sql[] = "DROP SEQUENCE seq_tarea;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "SELECT setval('seq_depto', 800, false);";
		$sql[] = "SELECT setval('seq_empl', 80, false);";
		$sql[] = "SELECT setval('seq_tarea', 8, false);";
		//Deptos.
		$sql[] = "INSERT INTO test_depto (cod_depto, nombre) VALUES (100, 'Transporte');";
		$sql[] = "INSERT INTO test_depto (cod_depto, nombre) VALUES (200, 'Prensa');";
		$sql[] = "INSERT INTO test_depto (cod_depto, nombre) VALUES (300, 'OTRO');";		
		
		//Empleados de Transporte
		$sql[] = "INSERT INTO test_empleado (cod_empleado, nombre, depto) VALUES (10, 'Gonzalez', 100);";
		$sql[] = "INSERT INTO test_empleado (cod_empleado, nombre, depto) VALUES (11, 'Alvarez', 100);";
		
		//Empleados de Prensa
		$sql[] = "INSERT INTO test_empleado (cod_empleado, nombre, depto) VALUES (20, 'Gimenez', 200);";
		//Emp[eado de Prensa que se quiere EXCLUIR por X motivo
		$sql[] = "INSERT INTO test_empleado (cod_empleado, nombre, depto) VALUES (21, 'García EXCLUIDO', 200);";		
		
		//Empleados de OTRO
		$sql[] = "INSERT INTO test_empleado (cod_empleado, nombre, depto) VALUES (30, 'OTRO', 300);";
		
		//Tareas
		$sql[] = "INSERT INTO test_tarea (cod_tarea, nombre) VALUES (1, 'Limpieza Patio');";
		$sql[] = "INSERT INTO test_tarea (cod_tarea, nombre) VALUES (2, 'Revisión Camiones');";
		$sql[] = "INSERT INTO test_tarea (cod_tarea, nombre) VALUES (3, 'Vigilancia');";
		$sql[] = "INSERT INTO test_tarea (cod_tarea, nombre) VALUES (4, 'Atención al cliente');";
		
		//Tareas de los empleados
		$sql[] = "INSERT INTO test_empleado_tareas (empleado, tarea) VALUES (10, 1);";
		$sql[] = "INSERT INTO test_empleado_tareas (empleado, tarea) VALUES (10, 2);";
		$sql[] = "INSERT INTO test_empleado_tareas (empleado, tarea) VALUES (11, 3);";
		$sql[] = "INSERT INTO test_empleado_tareas (empleado, tarea) VALUES (30, 3);";
		
		//Tareas de los deptos
		$sql[] = "INSERT INTO test_depto_tareas (depto, tarea) VALUES (100, 3);";
		$sql[] = "INSERT INTO test_depto_tareas (depto, tarea) VALUES (200, 1);";
		$sql[] = "INSERT INTO test_depto_tareas (depto, tarea) VALUES (200, 2);";
		$sql[] = "INSERT INTO test_depto_tareas (depto, tarea) VALUES (300, 3);";
		
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_empleado_tareas;";
		$sql[] = "DELETE FROM test_depto_tareas;";
		$sql[] = "DELETE FROM test_tarea;";
		$sql[] = "DELETE FROM test_empleado;";
		$sql[] = "DELETE FROM test_depto;";
		return $sql;
	}

	function get_fila_test($tabla, $concepto)
	//Registros para insertar en las tablas
	{
		$datos = array();
		//- Registros validos
		$datos['depto']['comercial'] = array('nombre' => 'Comercial');
		$datos['depto']['atencion'] = array('nombre' => 'Atención al cliente');
		
		$datos['empleado']['perez'] = array('nombre' => 'Juan Perez');
		$datos['empleado']['gomez'] = array('nombre' => 'Ernesto Gomez');
		$datos['empleado']['juarez'] = array('nombre' => 'Enrique Juarez');

		$datos['tarea']['backup'] = array('nombre' => "Backups");
		$datos['tarea']['electrica'] = array('nombre' => "Electrica");

		//- Registros invalidos
		//El depto no existe
		//$datos['empleado']['pereyra'] = array('cod_empleado'=>21, 'depto' => 9999, 'nombre' => 'Enrique Juarez');		
		
		if(isset($datos[$tabla][$concepto])){
			return 	$datos[$tabla][$concepto];
		}else{
			throw new toba_error("Se solicito una FILA inexistente");
		}
	}

	function cargar()
	{
		//Carga el dpto de transporte y de prensa, y tambien alguno nuevo si hay
		$wheres['depto'] = "depto.cod_depto=100 OR depto.cod_depto=200 OR cod_depto >= 800";
		$wheres['empleado'] = "empleado.cod_empleado != 21";		
		$carga = $this->dr->persistidor()->cargar_con_wheres($wheres);
		$this->AssertTrue($carga);		
	}
	
	/**
	 * @todo falta testear las tareas
	 */
	function test_carga_por_clave()
	{
		$this->cargar();
				
		//Se identifican las filas cargadas
		$transporte = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 100));
		$prensa = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 200));
		$gonzales = $this->dr->tabla('empleado')->get_id_fila_condicion(array('cod_empleado' => 10));
		$alvarez = $this->dr->tabla('empleado')->get_id_fila_condicion(array('cod_empleado' => 11));
		$gimenez = $this->dr->tabla('empleado')->get_id_fila_condicion(array('cod_empleado' => 20));
		$tarea_limpieza = $this->dr->tabla('tarea')->get_id_fila_condicion(array('cod_tarea' => 1));
		$tarea_camiones = $this->dr->tabla('tarea')->get_id_fila_condicion(array('cod_tarea' => 2));
		$tarea_vigilancia = $this->dr->tabla('tarea')->get_id_fila_condicion(array('cod_tarea' => 3));
		$tarea_atencion = $this->dr->tabla('tarea')->get_id_fila_condicion(array('cod_tarea' => 3));
		
		//Aserciones globales, cargo todos los registros?
		$this->AssertEqual($this->dr->tabla('depto')->get_cantidad_filas(), 2);
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 3);
		$this->AssertEqual($this->dr->tabla('empleado_tareas')->get_cantidad_filas(), 3);
		$this->AssertEqual($this->dr->tabla('tarea')->get_cantidad_filas(), 4);
		$this->AssertEqual($this->dr->tabla('depto_tareas')->get_cantidad_filas(), 3);
		
		//Preguntas particulares Del area de transporte
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 2);
		$this->AssertEqual($this->dr->tabla('depto_tareas')->get_cantidad_filas(), 1);
		
		//Preguntas sobre el area de prensa
		$this->dr->tabla('depto')->set_cursor(current($prensa));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 1);
		$this->AssertEqual($this->dr->tabla('depto_tareas')->get_cantidad_filas(), 2);		

		//Preguntas del empleado Gonzales
		$this->dr->tabla('empleado')->set_cursor(current($gonzales));
		$this->AssertEqual($this->dr->tabla('empleado_tareas')->get_cantidad_filas(), 2);
		//Preguntas del empleado Alvarez
		$this->dr->tabla('empleado')->set_cursor(current($alvarez));
		$this->AssertEqual($this->dr->tabla('empleado_tareas')->get_cantidad_filas(), 1);
		//Preguntas del empleado Gimenez
		$this->dr->tabla('empleado')->set_cursor(current($gimenez));
		$this->AssertEqual($this->dr->tabla('empleado_tareas')->get_cantidad_filas(), 0);
		
		
		$this->control_cambios_dr(	array(	"depto" => array("db", "db"),
										"empleado" => array("db", "db", "db"),
										"empleado_tareas" => array("db", "db", "db"),
										"tarea" =>	array("db","db","db","db"),
										"depto_tareas" => array("db", "db", "db")));
	}	
	
	//---------------------------------------------------------------------------
	//--------------------      ABM EN MEMORIA  ---------------------------------
	//---------------------------------------------------------------------------

	function aserciones_alta()
	{
		$this->dr->resetear_cursores();	//Para que no incida en las columnas
				
		$dep_c = $this->dr->tabla('depto')->get_id_fila_condicion( $this->get_fila_test('depto', 'comercial') );
		$dep_a = $this->dr->tabla('depto')->get_id_fila_condicion( $this->get_fila_test('depto', 'atencion')  );

		$perez = $this->dr->tabla('empleado')->get_id_fila_condicion( $this->get_fila_test('empleado', 'perez') );
		$gomez = $this->dr->tabla('empleado')->get_id_fila_condicion( $this->get_fila_test('empleado', 'gomez') );		
		$juarez = $this->dr->tabla('empleado')->get_id_fila_condicion( $this->get_fila_test('empleado', 'juarez') );

		$electrica = $this->dr->tabla('tarea')->get_id_fila_condicion($this->get_fila_test('tarea', 'electrica'));
		$backup = $this->dr->tabla('tarea')->get_id_fila_condicion($this->get_fila_test('tarea', 'backup'));

		//ASERCIONES
		$this->dr->tabla('depto')->set_cursor(current($dep_c));
		$this->assertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 2);
		$this->dr->tabla('depto')->set_cursor(current($dep_a));
		$this->assertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 1);
		
		$this->dr->tabla('empleado')->set_cursor(current($perez));
		$this->assertEqual($this->dr->tabla('empleado_tareas')->get_cantidad_filas(), 2);
		$this->dr->tabla('empleado')->set_cursor(current($juarez));
		$this->assertEqual($this->dr->tabla('empleado_tareas')->get_cantidad_filas(), 1);
		$this->dr->tabla('empleado')->set_cursor(current($gomez));
		$this->assertEqual($this->dr->tabla('empleado_tareas')->get_cantidad_filas(), 1);		
	}
	
	/**
	 * Se prueba insertar registros desde cero, verificando las asociaciones padre-hijo entre ellos
	 */	
	function test_memoria_alta()
	{
		//Tareas
		$electrica = $this->dr->tabla('tarea')->nueva_fila( $this->get_fila_test('tarea', 'electrica'));
		$backup = $this->dr->tabla('tarea')->nueva_fila( $this->get_fila_test('tarea', 'backup'));		
		
		//Empleados del departamento comercial
		$dep_c = $this->dr->tabla('depto')->nueva_fila( $this->get_fila_test('depto', 'comercial') );
		$this->dr->tabla('depto')->set_cursor($dep_c);
		$perez = $this->dr->tabla('empleado')->nueva_fila(  $this->get_fila_test('empleado', 'perez'));
		$gomez = $this->dr->tabla('empleado')->nueva_fila(  $this->get_fila_test('empleado', 'gomez'));
		
		//Empleados del departamento de atención
		$dep_a = $this->dr->tabla('depto')->nueva_fila( $this->get_fila_test('depto', 'atencion') );
		$this->dr->tabla('depto')->set_cursor($dep_a);
		$juarez = $this->dr->tabla('empleado')->nueva_fila(  $this->get_fila_test('empleado', 'juarez'));
		
		//Tareas de perez
		$this->dr->tabla('empleado')->set_cursor($perez);
		$this->dr->tabla('tarea')->set_cursor($backup);
		$this->dr->tabla('empleado_tareas')->nueva_fila();
		$this->dr->tabla('tarea')->set_cursor($electrica);
		$this->dr->tabla('empleado_tareas')->nueva_fila();
		
		//Tareas de juarez
		$this->dr->tabla('empleado')->set_cursor($gomez);
		$this->dr->tabla('tarea')->set_cursor($backup);
		$this->dr->tabla('empleado_tareas')->nueva_fila();		

		//Tareas de gomez
		$this->dr->tabla('empleado')->set_cursor($juarez);
		$this->dr->tabla('tarea')->set_cursor($electrica);
		$this->dr->tabla('empleado_tareas')->nueva_fila();
		
		//---ASERCIONES
		$this->aserciones_alta();
		//---ASERCIONES
				
		$this->control_cambios_dr(	array(	"depto" => array("i", "i"),
											"empleado" => array("i", "i", "i"),
											"empleado_tareas" => array("i", "i", "i", "i")));
		//$this->dr->dump_esquema();
		
		//SINCRONIZACION
		try {
			$this->dr->sincronizar();
			$this->pass();
		} catch (toba_error  $e) {
			$this->fail($e->getMessage());
		}
		$this->control_cambios_dr(	array(	"depto" => array("db", "db"),
											"empleado" => array("db", "db", "db"),
											"empleado_tareas" => array("db", "db", "db", "db")));

		//Recarga desde la base verificando que estan los mismos datos
		$wheres['depto'] = "depto.nombre IN ('Comercial', 'Atención al cliente')";
		$wheres['tarea'] = "tarea.nombre IN ('Backups', 'Electrica')";
		$this->dr->persistidor()->cargar_con_wheres($wheres);
		$this->aserciones_alta();
		//$this->dr->dump_esquema();
	}
	
	function test_memoria_borrado_cascada()
	{
		$this->cargar();

		$transporte = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 100));

		//Se pone foco al depto
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		//Se elimina el depto
		$this->dr->tabla('depto')->eliminar_fila(current($transporte));

		//Ahora el area de transporte tiene que tener un empleado menos
		$this->AssertEqual($this->dr->tabla('depto')->get_cantidad_filas(), 1);
		
		$this->control_cambios_dr(	array(	"depto" => array("d", "db"),
											"empleado" => array("d", "d", "db"),
											"empleado_tareas" => array("d", "d", "d")));
	}
	
	/**
	 * Prueba la capacidad de actualización de los mapeos de filas en la relacion_entre_tablas
	 * Se modifica el padre de una fila modificando la clave en la fila hija
	 */
	function test_memoria_carga_modificacion_padre_por_clave()
	{
		$this->cargar();
		
		$gonzales = $this->dr->tabla('empleado')->get_id_fila_condicion(array('cod_empleado' => 10));
		$transporte = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 100));
		$prensa = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 200));
				
		//El area de transporte tiene que tener dos empleados
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 2);
		
		//Y el area de prensa solo uno
		$this->dr->tabla('depto')->set_cursor(current($prensa));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 1);		
		
		//Se modifica el departamento del empleado gonzalez
		$datos_gonzales = $this->dr->tabla('empleado')->get_fila(current($gonzales));
		$datos_gonzales['depto'] = 200;
		$this->dr->tabla('empleado')->modificar_fila(current($gonzales), $datos_gonzales);

		//Ahora el area de transporte tiene que tener un empleado menos
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 1);
		
		//Y el area de prensa uno más
		$this->dr->tabla('depto')->set_cursor(current($prensa));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 2);
	}


	/**
	 * Prueba la capacidad de actualización de los mapeos de filas en la relacion_entre_tablas
	 * Se modifica el padre de una fila, indicando explicitamente a traves del API del datos_tabla
	 */
	function test_memoria_carga_modificacion_padre_explicito()
	{
		$this->cargar();
		
		//Carga el dpto de transporte y de prensa con todos sus empleados/tareas
		$gonzales = $this->dr->tabla('empleado')->get_id_fila_condicion(array('cod_empleado' => 10));
		$transporte = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 100));
		$prensa = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 200));
				
		//El area de transporte tiene que tener dos empleados
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 2);
		
		//Y el area de prensa solo uno
		$this->dr->tabla('depto')->set_cursor(current($prensa));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 1);
		
		//Se modifica el departamento del empleado gonzalez usando la API
		$datos_gonzales = $this->dr->tabla('empleado')->get_fila(current($gonzales));
		$this->dr->tabla('empleado')->cambiar_padre_fila(current($gonzales), array('depto' =>  current($prensa)));

		//Ahora el area de transporte tiene que tener un empleado menos
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 1);
		
		//Y el area de prensa uno más
		$this->dr->tabla('depto')->set_cursor(current($prensa));
		$this->AssertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 2);
	}
	
	
	/**
	 * Se da de alta y baja una asociacion entre dpto y tareas
	 *
	 */
	function test_memoria_alta_y_baja_mismo_registro()
	{
		$this->cargar();
		$transporte = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 100));
		$tarea_vigilancia = $this->dr->tabla('tarea')->get_id_fila_condicion(array('cod_tarea' => 3));
		
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		$this->dr->tabla('tarea')->set_cursor(current($tarea_vigilancia));
		
		$this->assertEqual($this->dr->tabla('depto_tareas')->get_cantidad_filas(), 1);
		//Se elimina la fila
		$tarea_transporte = $this->dr->tabla('depto_tareas')->get_id_filas();
		$this->dr->tabla('depto_tareas')->eliminar_fila(current($tarea_transporte));
		//Se verifica la eliminación
		$this->assertEqual($this->dr->tabla('depto_tareas')->get_cantidad_filas(), 0);
		//Se inserta la misma fila
		$this->dr->tabla('depto_tareas')->nueva_fila();
		//Vuelve a existir la fila en memoria 
		$this->assertEqual($this->dr->tabla('depto_tareas')->get_cantidad_filas(), 1);		
		
		//SINCRONIZACION
		try {
			$this->dr->sincronizar();
			$this->pass();
		} catch (toba_error $e) {
			$this->fail($e->getMessage());
		}
		$this->cargar();
	}
	
	/**
	 * Carga en memoria, borra todo y vuelve a cargarlo
	 */
	function test_memoria_borrado_total()
	{
		$this->assertTrue($this->dr->cargar());
		$this->dr->eliminar_todo();
		$this->assertFalse($this->dr->cargar());
	}
	
	
	/**
	 * Realiza modificaciones varias a la carga y sincroniza
	 * Prueba Altas, bajas y modificaciones:
	 * 	-Da de alta un nuevo departamento
	 *  -Le asigna los empleados de otro depto (el de transporte)
	 *  -Borra el depto de transporte (y sus tareas asignadas tambien)
	 */
	function test_memoria_modificaciones_y_sincronizacion()
	{
		$this->cargar();
		//$this->dr->dump_esquema("INICIAL");
		//Al ser necesario que update se ejecuta antes que delete se deben retrazar las constraints
		$this->dr->persistidor()->retrasar_constraints();
		
		//Depto de transporte
		$transporte = $this->dr->tabla('depto')->get_id_fila_condicion(array('cod_depto' => 100));
		
		//Nuevo Depto.
		$dep_c = $this->dr->tabla('depto')->nueva_fila( $this->get_fila_test('depto', 'comercial') );
		
		//Se transfieren los empleados de prensa a comercial
		$this->dr->tabla('depto')->set_cursor(current($transporte));
		$emple_trans = $this->dr->tabla('empleado')->get_id_filas();
		foreach ($emple_trans as $empleado) {
			$this->dr->tabla('empleado')->cambiar_padre_fila($empleado, array('depto' => $dep_c));
		}
		
		//Borra del depto.
		$this->dr->tabla('depto')->eliminar_fila(current($transporte));

		$this->dr->sincronizar();
		$this->cargar();
		
		//--ASERCIONES
		$dep_c = $this->dr->tabla('depto')->get_id_fila_condicion($this->get_fila_test('depto', 'comercial'));
		$tarea_vigilancia = $this->dr->tabla('tarea')->get_id_fila_condicion(array('cod_tarea' => 3));
		
		$this->assertEqual($this->dr->tabla('depto')->get_cantidad_filas(), 2);
		$this->dr->tabla('depto')->set_cursor(current($dep_c));
		$this->assertEqual($this->dr->tabla('empleado')->get_cantidad_filas(), 2);
		
		//La tarea de vigilancia no debe estar más asociada con un deptro
		$this->dr->tabla('tarea')->set_cursor(current($tarea_vigilancia));
		$this->assertEqual($this->dr->tabla('depto_tareas')->get_cantidad_filas(), 0);
		
		//$this->dr->dump_esquema("CAMBIO");		
	}
}
?>
