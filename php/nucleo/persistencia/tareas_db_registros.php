
	HILO
	----

		- Refactorizar definicion MT
		- refac. constructor
		- refac. nombres interface servicios
		- Rearmar el generados de db_registros
		- Migrar db_registros del comechingones
		- completar funcionalidad y tests
		- ver MT con relacion debil
		
	IDEAS
	-----
	
		- Plantillas para editor de las clases comunes
		- Wrapper de un buffer para realizar procesos
		- El dbr debe poder crear el modelo de datos que representa
		
//___________________________________________________________________________________________________

	Modificaciones CONSTRUCTOR

	__construct($id, $definicion, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=true)
	__construct($definicion, $fuente, $max_registros=0, $min_registros=0)

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
	
*/
		$def = array(	
				0 => array (
					'nombre' => '',
					'alias' => '',
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
		
		->		activar_transaccion()		
		->		desactivar_transaccion()		

		//->		set_max_registros($cantidad)						//tope superior de registros
		//->		set_min_registros($cantidad)						//tope minimo de registros
				
		->		set_no_duplicado( array("col_1","col_2") );			//valores unicos

		->		set_order_by( array("col_1","col_2") )				//Indica cual es el orden de 

		->		activar_memoria_autonoma(id)

		-> 		activar_proceso_carga_externa_sql($sql, $col_parametros=array(), $col_resultado=array(), $sincro_continua=true)
				/* 
					Atencion: las columnas devueltas tiene que ser iguales a las enumeradas en "col_resultado"
					y las columnas del where (%col%) iguales a las enumeradas en "col_parametro"
						Estos dos conjuntos se pueden decidir directamente, tiene sentido acoplar?
							- tal vez como configuracion por defecto
				*/
		->		activar_proceso_carga_externa_dao($dao, $col_parametros, $col_resultado, $sincro_continua=true)

		->		activar_baja_logica($columna, $valor)

		->		activar_control_sincro()
		->		desactivar_control_sincro()

		->		activar_modificacion_clave()
	
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
?>