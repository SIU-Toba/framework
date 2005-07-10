
	DOCUMENTACION !!

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

	INTERFACE db_registros
	----------------------

*	public function validar_definicion()
	public function info($mostrar_datos=false)
	public function info_definicion()
	public function get_definicion()															EX obtener_definicion()
	public function get_tope_max_registros()
	public function get_tope_min_registros()
	public function get_estructura_control()

	public function cargar_datos($where=null, $from=null)
	public function cargar_datos_clave($id)
	public function resetear()

	public function get_registros($condiciones=null, $usar_id_registro=false)					EX obtener_registros
	public function get_id_registro_condicion($condiciones=null)								EX obtener_id_registro_condicion
	public function get_registro($id)															EX obtener_registro
	public function get_registro_valor($id, $columna)											EX get_registro_valor
	public function get_cantidad_registros()													EX cantidad_registros
	public function existe_registro($id)
	public function get_cantidad_registros_a_sincronizar()
	public function get_id_registros_a_sincronizar()

	public function agregar_registro($registro)
	public function modificar_registro($registro, $id)
	public function eliminar_registro($id=null)
	public function eliminar_registros()

	public function set_registro_valor($id_registro, $columna, $valor)							EX establecer_registro_valor
	public function set_valor_columna($columna, $valor)											EX establecer_valor_columna
	public function set($registro)
	public function get()
	public function procesar_registros($registros)
*	public function validar_registros()

	public function sincronizar()

	protected function evt__pre_sincronizacion()
	protected function evt__post_sincronizacion()
	protected function evt__pre_insert($id)
	protected function evt__post_insert($id)
	protected function evt__pre_update($id)
	protected function evt__post_update($id)
	protected function evt__pre_delete($id)
	protected function evt__post_delete($id)


	*** modificadores del comportamiento ***

	public function set_tope_max_registros($cantidad)
	public function set_tope_min_registros($cantidad)
*	public function set_no_duplicado( array("col_1",col_2") )
*	public function set_order_by( array("col_1","col_2") )	
	public function activar_transaccion()		
	public function desactivar_transaccion()		
*	public function activar_control_sincro()
*	public function desactivar_control_sincro()
!t	public function activar_proceso_carga_externa_sql($sql, $col_parametros, $col_resultado, $sincro_continua=true)
*	public function activar_proceso_carga_externa_dao($dao, $col_parametros, $col_resultado, $sincro_continua=true)
*	public function activar_memoria_autonoma($id)

	db_registros_s
	--------------

!t	public function activar_baja_logica($columna, $valor)
!t	public function activar_modificacion_clave()

	db_registros_mt
	---------------

	public function activar_inner_join()
*	public function activar_outer_join()

//___________________________________________________________________________________________________
//___________________________________________________________________________________________________

		- Cambio de nomenclatura en la interface de la clase

	obtener_registro_valor     		get_registro_valor
	obtener_registros             	get_registros
	obtener_registro              	get_registro
	obtener_definicion            	get_definicion															
	obtener_id_registro_condicion 	get_id_registro_condicion
	cantidad_registros            	get_cantidad_registros
	establecer_registro_valor     	set_registro_valor
	establecer_valor_columna      	set_valor_columna

?>