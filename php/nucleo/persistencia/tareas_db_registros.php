
	******  TRABAJO con los DB_REGISTROS  ******

	HILO
	----

		- Rearmar el generados de db_registros
		- Migrar db_registros del comechingones
		- completar funcionalidad
		- ver MT con relacion debil

	PENDIENTE
	---------

		- Definir la nomenclatura: campo/columna (pega feo en 'externa')
		- Validacion de la definicion
		- El manejo de la secuencia esta basado en Postgresql
		- Registrar controlador
		- Control valores UNICOS
		- Las secuencias no estan interviniendo den UPDATES e INSERTS... esta bien?
		
	IDEAS
	-----
	
		- Plantillas para editor de las clases comunes
		- Wrapper de un buffer para realizar procesos
		- El dbr debe poder crear el modelo de datos que representa
		
//___________________________________________________________________________________________________


/*
	*** Definicion db_registros ***

*/
		$def = array(	
				'tabla' => '',
				'columna' => array( 
						array( 	'nombre'=>'',
								'pk'=>1, 
								'no_nulo'=>1, 
								'secuencia'=>'',
								'externa'=>1
							),
						array( 'nombre'=>'' ),
					)
				);

/*

	*** Definicion db_registros_mt ***

	Relaciones 1 -> 1
	Tabla principal = 0;
	Orden = orden array
	alias: 	El comportamiento por defecto respecto de las columnas iguales es comprimirlas (con los IDs tiene sentido)
			Es util para campos duplicados en MT que poseen significados distintos.
			Para utilizar la interface externa solo se usa el alias (al principio y final se traduce al nombre real)
*/
		$def = array(	
				0 => array (
					'nombre' => '',
					'columna' => array( 
						array( 	'nombre'=>'',
								'pk'=>1, 
								'no_nulo'=>1,
								'alias'=>'',
								'secuencia'=>'',
								'externa'=>1,
								'join'=>'',
							),
						array( 'nombre'=>'' ),
					)
				),
			);

//___________________________________________________________________________________________________
<?
		//-- Servicios --
		
		->		set_no_duplicado( array("col_1","col_2") );			//valores unicos

		->		set_order_by( array("col_1","col_2") )				//Indica cual es el orden de 

		->		activar_proceso_carga_externa_dao($dao, $col_parametros, $col_resultado, $sincro_continua=true)

	//------- IN

		->		activar_inner_join()
		->		activar_outer_join()

		->		activar_control_sincro()
		->		desactivar_control_sincro()

		->		activar_transaccion()		
		->		desactivar_transaccion()		

		->		activar_baja_logica($columna, $valor)
		->		activar_modificacion_clave()

		->		activar_memoria_autonoma(id)

		-> 		activar_proceso_carga_externa_sql($sql, $col_parametros=array(), $col_resultado=array(), $sincro_continua=true)
				/* 
					Atencion: las columnas devueltas tiene que ser iguales a las enumeradas en "col_resultado"
					y las columnas del where (%col%) iguales a las enumeradas en "col_parametro"
						Estos dos conjuntos se pueden decidir directamente, tiene sentido acoplar?
							- tal vez como configuracion por defecto
				*/
	
//___________________________________________________________________________________________________

		//-- PERSONAS --

		$def = array(	
				'tabla' => 'anx_personas',
				'columna' = array( 
						array( 'nombre'=>'persona',
								'pk'=>1, 
								'secuencia'=>'sq_anx_personas',
								'no_nulo'=>1 
							),
						array( 'nombre'=>'fisica_o_juridica'),
						array( 'nombre'=>'razon_social' ,
								'no_nulo'=> 1 
							),
						array( 'nombre'=>'nombre_fantasia'),
						array( 'nombre'=>'sexo'),
						array( 'nombre'=>'nacionalidad'),
						array( 'nombre'=>'email'),
						array( 'nombre'=>'pais'),
						array( 'nombre'=>'pais_nombre',
								'externa'=>1 
							)
					)
				);

		//Tabla principal
		$definicion['tabla'][0]['nombre']='anx_domicilios';
		$definicion['tabla'][0]['alias']='dom';
		$definicion['tabla'][0]['columna'][0]['nombre']='domicilio';
		$definicion['tabla'][0]['columna'][0]['pk']=1;
		$definicion['tabla'][0]['columna'][0]['secuencia']='sq_anx_domicilios';
		$definicion['tabla'][0]['columna'][1]['nombre']='calle';
		$definicion['tabla'][0]['columna'][2]['nombre']='numero';
		$definicion['tabla'][0]['columna'][3]['nombre']='piso';
		$definicion['tabla'][0]['columna'][4]['nombre']='departamento';
		$definicion['tabla'][0]['columna'][5]['nombre']='unidad';
		$definicion['tabla'][0]['columna'][6]['nombre']='fax';
		$definicion['tabla'][0]['columna'][6]['alias']='faxx';
		$definicion['tabla'][0]['columna'][7]['nombre']='telefono';
		$definicion['tabla'][0]['columna'][8]['nombre']='localidad';
		$definicion['tabla'][0]['columna'][9]['nombre']='dpto_partido';
		$definicion['tabla'][0]['columna'][10]['nombre']='provincia';
		$definicion['tabla'][0]['columna'][11]['nombre']='pais';
		//Tabla secundaria
		$definicion['tabla'][1]['nombre']='anx_personas_domicilios';
		$definicion['tabla'][1]['alias']='per';
		$definicion['tabla'][1]['columna'][0]['nombre']='domicilio';
		$definicion['tabla'][1]['columna'][0]['clave']= 1;
		$definicion['tabla'][1]['columna'][0]['join']= 'domicilio';
		$definicion['tabla'][1]['columna'][1]['nombre']='persona';
		$definicion['tabla'][1]['columna'][1]['clave']= 1;
		$definicion['tabla'][1]['columna'][2]['nombre']='rol';
		$definicion['tabla'][1]['columna'][2]['clave']= 1;

//___________________________________________________________________________________________________


	INTERFACE db_registros
	----------------------

	public function info($mostrar_datos=false)
	public function info_definicion()
	public function get_definicion()															obtener_definicion()
	public function validar_definicion()
	public function get_tope_max_registros()
	public function get_tope_min_registros()
	public function get_estructura_control()

	public function cargar_datos($where=null, $from=null)
	public function cargar_datos_clave($id)
	public function resetear()

	public function obtener_registros($condiciones=null, $usar_id_registro=false)				obtener_registros
	public function obtener_id_registro_condicion($condiciones=null)							obtener_id_registro_condicion
	public function obtener_registro($id)														obtener_registro
	public function obtener_registro_valor($id, $columna)										obtener_registro_valor
	public function cantidad_registros()														cantidad_registros
	public function existe_registro($id)

	public function agregar_registro($registro)
	public function modificar_registro($registro, $id)
	public function eliminar_registro($id=null)
	public function eliminar_registros()

	public function establecer_registro_valor($id, $columna, $valor)							establecer_registro_valor
	public function establecer_valor_columna($columna, $valor)									establecer_valor_columna
	public function set($registro)
	public function get()
	public function procesar_registros($registros)
	public function validar_registro($registro, $id=null)

	public function sincronizar()

		protected function evt__pre_sincronizacion()
		protected function evt__post_sincronizacion()
		protected function evt__pre_insert($id)
		protected function evt__post_insert($id)
		protected function evt__pre_update($id)
		protected function evt__post_update($id)
		protected function evt__pre_delete($id)
		protected function evt__post_delete($id)


	FLAGS que modifican el comportamiento

	db_registros
	------------
	
	public function activar_transaccion()		
	public function desactivar_transaccion()		
	public function activar_control_sincro()
	public function desactivar_control_sincro()
	public function activar_proceso_carga_externa_sql($sql, $col_parametros, $col_resultado, $sincro_continua=true)
	public function activar_proceso_carga_externa_dao($dao, $col_parametros, $col_resultado, $sincro_continua=true)
	public function activar_memoria_autonoma($id)

	db_registros_s
	--------------

	public function activar_baja_logica($columna, $valor)
	public function activar_modificacion_clave()

	db_registros_mt
	---------------

	function activar_inner_join()
	function activar_outer_join()
	
?>