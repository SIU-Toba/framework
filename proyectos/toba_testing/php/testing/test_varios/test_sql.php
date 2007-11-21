<?php

class test_sql extends test_toba
{

	function get_descripcion()
	{
		return "Manipulación de sentencias SQL";
	}

	function consultar_sql($sql)
	{
		try {
			toba::db()->consultar($sql);
			$this->pass();
		} catch (toba_error $e) {
			$this->fail($e->getMessage());
		}
	}

	function test_sin_where()
	{
		$where = array();
		$sql = 'SELECT 1 FROM apex_instancia';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);
	}
	
	function test_where_existente()
	{
		$where = array('2=2', '3=3');
		$sql = 'SELECT 1 FROM apex_instancia WHERE 1=1';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);		
	}
	
	function test_varios_where_existentes()
	{
		$where = array('3=3', '4=4');
		$sql = 'SELECT 1 FROM apex_instancia WHERE 1=1 AND 2=2';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);		
	}	

	function test_where_espaciados()
	{
		$where = array('3=3', '4=4');
		$sql = 'SELECT 1 FROM apex_instancia WHERE 1 = 1 AND 2 = 2';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);		
	}	


	//------------------------ CON ORDER --------------------------------
	
	function test_sin_where_con_order()
	{
		$where = array();
		$sql = 'SELECT 1 FROM apex_instancia ORDER BY 1';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);		
	}
		
	
	function test_where_existente_con_order()
	{
		$where = array('2=2', '3=3');
		$sql = 'SELECT 1 FROM apex_instancia WHERE 1=1 ORDER BY 1';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);		
	}	
	
	function test_varios_where_existentes_con_order()
	{
		$where = array('3=3', '4=4');
		$sql = 'SELECT 1,2 FROM apex_instancia WHERE 1=1 AND 2=2 ORDER BY 1,2';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);		
	}		
	
	function test_varios_where_espaciados_con_order()
	{
		$where = array('3=3', '4=4');
		$sql = 'SELECT 1,2 FROM apex_instancia WHERE 1 = 1 AND 2 = 2 ORDER BY 1,2';
		$sql = sql_concatenar_where($sql, $where);
		$this->consultar_sql($sql);		
	}
	
}

?>