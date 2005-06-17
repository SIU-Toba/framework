<?php
require_once('admin/editores/editor_ci/dbt_edt.php');

class test_db_tablas_cd extends test_toba
{
	protected $dbt;
	
	function SetUp()
	{
		$this->dbt = new dbt_edt('instancia');
	}

	function cargar_dependencia_global()
	{
		$this->dbt->cargar(array('proyecto'=> 'toba_testing', 'objeto' =>1315));	
	}
	
	function assert_dbt_esta_vacio()
	{
		$this->AssertEqual($this->dbt->elemento('basico')->cantidad_registros(), 0);
		$this->AssertEqual($this->dbt->elemento('basico_dep')->cantidad_registros(), 0);
		$this->AssertEqual($this->dbt->elemento('especifico')->cantidad_registros(), 0);
		$this->AssertEqual($this->dbt->elemento('etapas')->cantidad_registros(), 0);
		$this->AssertEqual($this->dbt->elemento('etapas_dep')->cantidad_registros(), 0);	
	}
	
	function test_dependencia_no_existe()
	{
		$this->dbt->cargar(array('proyecto'=> 'toba_testing', 'objeto' =>131534534));
		$this->assert_dbt_esta_vacio();
	}
	
	function test_dependencia_global_carga()
	{
		$this->cargar_dependencia_global();
		$this->AssertEqual($this->dbt->elemento('basico')->cantidad_registros(), 1);
		$this->AssertEqual($this->dbt->elemento('basico_dep')->cantidad_registros(), 2);
		$this->AssertEqual($this->dbt->elemento('especifico')->cantidad_registros(), 1);
		$this->AssertEqual($this->dbt->elemento('etapas')->cantidad_registros(), 2);
		$this->AssertEqual($this->dbt->elemento('etapas_dep')->cantidad_registros(), 0);
	}
	
	function test_dependencia_global_eliminar_etapas()
	{
		abrir_transaccion();
		$this->cargar_dependencia_global();
		$this->dbt->eliminar_plan();
		$this->assert_dbt_esta_vacio();
		abortar_transaccion();
	}
	
	function test_dependencia_global_agrega_dep_etapa()
	{
		abrir_transaccion();
		$this->cargar_dependencia_global();
		//referencia a la etapa
		$registro['objeto_mt_me_proyecto']="toba_testing";
		$registro['objeto_mt_me']="1315";
		$registro['posicion']="0";
		//referencia a la dependencia asociada al objeto
		$registro['proyecto']="toba_testing";
		$registro['objeto_consumidor']="1315";
		$registro['identificador']="cuadro";
		$registro['objeto_proveedor']="1149";
		$this->dbt->elemento('etapas_dep')->agregar_registro($registro);
		$this->dbt->sincronizar_plan();
		$this->dbt->resetear();
		$this->cargar_dependencia_global();
		$this->AssertEqual($this->dbt->elemento('etapas_dep')->cantidad_registros(), 1);
		abortar_transaccion();
	}	
}
?>