
	HILO
	----
	
		- Refactorizar definicion MT
		- refac. constructor
		- Migrar db_registros
		- refac. interface servicios
		- completar test
		- completar funcionalidad
		- ver MT con relacion debil

	Modificaciones definicion db_registros

	__construct($id, $definicion, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=true)

	1) ID
	5) memoria
	
		pasan a ser un metodo: 
		
->		activar_memoria_autonoma(id)
		
	4) transaccion
		
		pasa a ser un metodo: 
			
->		activar_transaccion()		
->		desactivar_transaccion()		
		
	__construct($definicion, $fuente, $max_registros=0, $min_registros=0)
	
	Primitivas que controlan los registros
	
->		set_max_registros()									//tope superior de registros
->		set_min_registros()									//tope minimo de registros
		
->		set_no_duplicado( array("col_1","col_2") );			//valores unicos

->		set_order_by( array("col_1","col_2") )				//Indica cual es el orden de 

<?
		//-- Definicion db_registros --

		$definicion['tabla']='anx_personas';
		$definicion['columna'][0]['nombre']='persona';
		$definicion['columna'][0]['clave']=1;
		$definicion['columna'][0]['secuencia']='sq_anx_personas';
		$definicion['columna'][0]['no_nulo']=1;
		$definicion['columna'][1]['nombre']='fisica_o_juridica';
		$definicion['columna'][1]['alias']='f_o_j';
		$definicion['columna'][2]['nombre']='razon_social';
		$definicion['columna'][2]['no_nulo']=1;
		$definicion['columna'][3]['nombre']='nombre_fantasia';
		$definicion['columna'][4]['nombre']='sexo';
		$definicion['columna'][5]['nombre']='nacionalidad';
		$definicion['columna'][6]['nombre']='email';
		$definicion['columna'][7]['nombre']='pais';
		$definicion['columna'][7]['externa']=1;

//___________________________________________________________________________________________________
		//-- DEFINICION del columnas externas (REVEER) --

		// Parametro de la definicion o metodo?

		//Carga de las columnas externas
		//Atencion: las columnas devueltas tiene que ser iguales a las enumeradas en "col"
		//			y las columnas del where iguales a las enumeradas en "llave"
		$definicion['carga_externa'][0]['sincro_continua'] = true; //Hay que dispararla despues de un ALTA o MODIFIACION?
		$definicion['carga_externa'][0]['sql'] = "
					SELECT h.codigo || ' - ' || h.nombre 	as elemento_nombre,
					       p.elemento 					as elemento_padre,
					       p.codigo || ' - ' || p.nombre 		as elemento_padre_nombre
					FROM sau_np_elementos h,
					sau_np_elementos p
					WHERE h.elemento_padre = p.elemento
					AND h.elemento = %elemento%;";
		$definicion['carga_externa'][0]['col_parametros'][0] = "elemento";
		$definicion['carga_externa'][0]['col_resultado'][0] = "elemento_nombre";
		$definicion['carga_externa'][0]['col_resultado'][1] = "elemento_padre";
		$definicion['carga_externa'][0]['col_resultado'][2] = "elemento_padre_nombre";

		

//___________________________________________________________________________________________________
		//-- Definicion db_registros_mt --
/*
	Hacer que esto se parezca con lo que va a venir de la definicion de la DB?
*/
		
		$definicion['tabla'][0]='anx_domicilios';
		$definicion['tabla_alias'][0]='dom';
		$definicion['anx_domicilios']['columna'][0]['nombre']='domicilio';
		$definicion['anx_domicilios']['columna'][0]['clave']=1;
		$definicion['anx_domicilios']['columna'][0]['secuencia']='sq_anx_domicilios';
		$definicion['anx_domicilios']['columna'][1]['nombre']='calle';
		$definicion['anx_domicilios']['columna'][2]['nombre']='numero';
		$definicion['anx_domicilios']['columna'][3]['nombre']='piso';
		$definicion['anx_domicilios']['columna'][4]['nombre']='departamento';
		$definicion['anx_domicilios']['columna'][5]['nombre']='unidad';
		$definicion['anx_domicilios']['columna'][6]['nombre']='fax';
		$definicion['anx_domicilios']['columna'][7]['nombre']='telefono';
		$definicion['anx_domicilios']['columna'][8]['nombre']='localidad';
		$definicion['anx_domicilios']['columna'][9]['nombre']='dpto_partido';
		$definicion['anx_domicilios']['columna'][10]['nombre']='provincia';
		$definicion['anx_domicilios']['columna'][11]['nombre']='pais';
		//Tabla secundaria
		$definicion['tabla'][1]='anx_personas_domicilios';
		$definicion['tabla_alias'][1]='per';
		$definicion['anx_personas_domicilios']['columna'][0]['nombre']='domicilio';
		$definicion['anx_personas_domicilios']['columna'][0]['clave']= 1;
		$definicion['anx_personas_domicilios']['columna'][1]['nombre']='persona';
		$definicion['anx_personas_domicilios']['columna'][1]['clave']= 1;
		$definicion['anx_personas_domicilios']['columna'][2]['nombre']='rol';
		$definicion['anx_personas_domicilios']['columna'][2]['clave']= 1;

		$definicion['relacion']['anx_personas_domicilios'][0]['pk'] = 'domicilio';
		$definicion['relacion']['anx_personas_domicilios'][0]['fk'] = 'domicilio';

//___________________________________________________________________________________________________

?>