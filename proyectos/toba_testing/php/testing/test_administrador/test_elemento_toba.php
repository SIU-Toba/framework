<?php

class test_elemento_toba extends test_toba
{

	function generar_todo()
	{
		return array(
			'constructor' => 1,
			'basicos' => 1,
			'eventos' => 2,
			'nivel_comentarios' => 3
		);	
	}
	
	function get_descripcion()
	{
		return "Elementos Toba";
	}	

	//-----------------------------------------------------
	//---------------ANALISIS DE EVENTOS-------------------
	//-----------------------------------------------------	
	
	function asertar_eventos($elemento, $predefinidos, $invalidos, $desconocidos, $sospechosos)
	{
		foreach ($invalidos as $evento) {
			$this->assertTrue($elemento->es_evento($evento), "$evento no es evento");
			$this->assertFalse($elemento->es_evento_valido($evento), "$evento es valido");		
			$this->assertFalse($elemento->es_evento_predefinido($evento), "$evento es predefinido");		
		}
		foreach ($predefinidos as $evento) {
			$this->assertTrue($elemento->es_evento($evento), "$evento no es evento");
			$this->assertTrue($elemento->es_evento_predefinido($evento), "$evento no es predefinido");		
			$this->assertTrue($elemento->es_evento_valido($evento), "$evento no es valido");
			$this->assertFalse($elemento->es_evento_sospechoso($evento), "$evento es sospechoso");
		}
		foreach ($desconocidos as $evento) {
			$this->assertTrue($elemento->es_evento($evento), "$evento no es evento");
			$this->assertFalse($elemento->es_evento_predefinido($evento), "$evento es predefinido");		
			$this->assertTrue($elemento->es_evento_valido($evento), "$evento no es valido");	
			$this->assertFalse($elemento->es_evento_sospechoso($evento), "$evento es sospechoso");
		}
		foreach ($sospechosos as $evento) {
			$this->assertTrue($elemento->es_evento($evento), "$evento no es evento");
			$this->assertFalse($elemento->es_evento_predefinido($evento), "$evento es predefinido");		
			$this->assertTrue($elemento->es_evento_valido($evento), "$evento no es valido");
			$this->assertTrue($elemento->es_evento_sospechoso($evento), "$evento no es sospechoso");	
		}		
	}
/*
	ATENCION: despues de la migracion de objetos INFO, no existe una instancia del INFO
				sin hacer referencia a un componente puntual. Este test esta comoentado por eso.

	function test_eventos_ci_simple()
	{
		$predefinidos= array();
		$invalidos = array('evt_bla', 'evtotro');
		$desconocidos = array('evt__mirar');
		$sospechosos = array('evt___otro');
		
		$et_ci = new elemento_toba_ci();
		$this->asertar_eventos($et_ci, $predefinidos, $invalidos, $desconocidos, $sospechosos);
	}
*/	
	
	function test_eventos_ci_con_dependencias()
	{
		//Un formulario como dependencia que no tiene el 'baja' entre los predefinidos
		$predefinidos= 
				array('evt__formulario__carga', 'evt__formulario__alta', 'evt__formulario__modificacion', 'evt__formulario__cancelar',
					'evt__cuadro__carga', 'evt__cuadro__seleccion',
					'evt__filtro__filtrar', 'evt__filtro__cancelar');
		$desconocidos = array('evt__formulario__observar', 'evt__formulario__baja', 'evt__cuadro__baja');
		$sospechosos = array('evt__formulario___otro', 'evt__formulario_alta', 'evt__filtro_cantar');
		$et_ci = toba_constructor::get_info( array('proyecto'=>'toba_testing', 'componente'=>1323) );
		$this->asertar_eventos($et_ci, $predefinidos, array(), $desconocidos, $sospechosos);		
	}	
	
	
	//--------------------------------------------------------------------------------
	//---------------CUERPO DE LA SUBCLASE EN BASE AL ELEMENTO-TOBA-------------------
	//--------------------------------------------------------------------------------	

	function test_generacion_ci_con_dependencias()
	{
		$nombre_clase = 'mi_ci';
		$clase = new toba_clase_php($nombre_clase, '', 'toba_ci', 'nucleo/browser/clases/toba_ci.php');
		$clase->set_objeto('toba_testing', '1323');
		$codigo = $clase->generar_clase($this->generar_todo());
//		highlight_string("<?php\n $codigo \n");
		eval($codigo);
		
		//Pruebas 
		$mi_ci = new ReflectionClass($nombre_clase);		
		//-- Asegura que se haya heredado el constructor
		$this->AssertEqual($mi_ci->getConstructor()->getDeclaringClass(), $mi_ci);	
		//-- El mantener_estado_sesion debe estar heredado
		$this->AssertEqual($mi_ci->getMethod('mantener_estado_sesion')->getDeclaringClass(), $mi_ci);

		//-- Los listeners de carga, alta, modificacion y cancelar del formulario	
		$this->AssertEqual($mi_ci->getMethod('evt__formulario__carga')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__formulario__alta')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__formulario__modificacion')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__formulario__cancelar')->getDeclaringClass(), $mi_ci);
		//-- No debio generar el listener de baja porque no esta en la definición
		try { 
			$mi_ci->getMethod('evt__formulario__baja');
			$this->fail();
		}
		catch (Exception $e) { 
			$this->pass();
		};
		
		//-- Los listeners de carga y seleccion del cuadro
		$this->AssertEqual($mi_ci->getMethod('evt__cuadro__carga')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__cuadro__seleccion')->getDeclaringClass(), $mi_ci);
		
		//--Los listeners de carga, modificacion y seleccion del ML sin analisis
		$this->AssertEqual($mi_ci->getMethod('evt__ml_sin_analisis__carga')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__ml_sin_analisis__modificacion')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__ml_sin_analisis__seleccion')->getDeclaringClass(), $mi_ci);
		
		//--Los listeners de carga, modificacion y seleccion del ML con analisis por eventos
		$this->AssertEqual($mi_ci->getMethod('evt__ml_eventos__carga')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__ml_eventos__registro_alta')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__ml_eventos__registro_baja')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__ml_eventos__registro_modificacion')->getDeclaringClass(), $mi_ci);		
		
		//--Listeners de carga, filtrar y cancelar del filtro		
		$this->AssertEqual($mi_ci->getMethod('evt__filtro__carga')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__filtro__cancelar')->getDeclaringClass(), $mi_ci);
		$this->AssertEqual($mi_ci->getMethod('evt__filtro__filtrar')->getDeclaringClass(), $mi_ci);
	}	
	
	function test_generacion_ei_formulario()
	{
		$nombre_clase = 'mi_ei_formulario';
		$clase = new toba_clase_php($nombre_clase, '', 'toba_ei_formulario', 'nucleo/browser/clases/toba_ei_formulario.php');
		$clase->set_objeto('toba_testing', '1324');
		$codigo = $clase->generar_clase($this->generar_todo());
//		highlight_string("<?php\n $codigo \n");
		eval($codigo);
		//Pruebas 
		$clase = new ReflectionClass($nombre_clase);		
		//-- Asegura que se haya heredado el constructor
		$this->AssertEqual($clase->getConstructor()->getDeclaringClass(), $clase);	
	}	

	function test_generacion_ei_cuadro()
	{
		$nombre_clase = 'mi_ei_cuadro';
		$clase = new toba_clase_php($nombre_clase, '', 'toba_ei_cuadro', 'nucleo/browser/clases/toba_ei_cuadro.php');
		$clase->set_objeto('toba_testing', '1326');
		$codigo = $clase->generar_clase($this->generar_todo());
//		highlight_string("<?php\n $codigo \n");
		eval($codigo);
		$clase = new ReflectionClass($nombre_clase);		
		//-- Asegura que se haya heredado el constructor
		$this->AssertEqual($clase->getConstructor()->getDeclaringClass(), $clase);	
	}	

	function test_generacion_ei_filtro()
	{
		$nombre_clase = 'mi_ei_filtro';
		$clase = new toba_clase_php($nombre_clase, '', 'toba_ei_filtro', 'nucleo/browser/clases/toba_ei_filtro.php');
		$clase->set_objeto('toba_testing', '1330');
		$codigo = $clase->generar_clase($this->generar_todo());
//		highlight_string("<?php\n $codigo \n");
		eval($codigo);
		$clase = new ReflectionClass($nombre_clase);		
		//-- Asegura que se haya heredado el constructor
		$this->AssertEqual($clase->getConstructor()->getDeclaringClass(), $clase);
		$this->AssertEqual($clase->getMethod('mantener_estado_sesion')->getDeclaringClass(), $clase);		
	}	
	
	
}

?>