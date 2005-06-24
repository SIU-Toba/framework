<?php
require_once("test_db_registros.php");

class test_db_registros_s extends test_db_registros
{
	function test_carga_registros()
	{
		try{
			$this->cargar_dbr_01_s();
			$this->dbr->cargar_datos();
			$this->AssertEqual($this->dbr->cantidad_registros(), 4);
			$this->descargar_dbr();
		}catch(excepcion_toba $e){
			//esto tiene sentido para que se ejecute el tearDown y se recree la tabla
			echo ei_mensaje($e->getMessage());
		}
	}

	function test_carga_registros_where()
	{
		try{
			$this->cargar_dbr_01_s();
			$where[] = "id IN (0,1,2)";
			$this->dbr->cargar_datos($where);
			$this->AssertEqual($this->dbr->cantidad_registros(), 3);
			$this->descargar_dbr();
		}catch(excepcion_toba $e){
			//esto tiene sentido para que se ejecute el tearDown y se recree la tabla
			echo ei_mensaje($e->getMessage());
		}
	}

	function test_obtencion_datos()
	{
		try{
			$this->cargar_dbr_01_s();
			$where[] = "id IN (0,1,2)";
			$this->dbr->cargar_datos($where);
			$datos = $this->dbr->obtener_registros();
			$this->AssertEqual( count($datos), 3);
			$this->descargar_dbr();
		}catch(excepcion_toba $e){
			//esto tiene sentido para que se ejecute el tearDown y se recree la tabla
			echo ei_mensaje($e->getMessage());
		}
	}

	function test_obtencion_datos_filtro()
	{
		try{
			$this->cargar_dbr_01_s();
			$where[] = "id IN (0,1,2)";
			$this->dbr->cargar_datos($where);
			$condicion["id"] = "0";
			$datos = $this->dbr->obtener_registros($condicion);
			$this->AssertEqual(count($datos), 1);
			$this->descargar_dbr();
		}catch(excepcion_toba $e){
			//esto tiene sentido para que se ejecute el tearDown y se recree la tabla
			echo ei_mensaje($e->getMessage());
		}
	}
}
?>