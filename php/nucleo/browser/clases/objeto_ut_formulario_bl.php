<?
require_once("objeto_ut_formulario.php");	//Ancestro de todos los OE

class objeto_ut_formulario_bl extends objeto_ut_formulario
/*
 	@@acceso: actividad
	@@desc: Esta clase contruye la Interface Grafica de un registro de una tabla
*/
{
	function objeto_ut_formulario_bl($id)
/*
 	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::objeto_ut_formulario($id);
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------  PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_db($clave)
/*
 	@@acceso: interno
	@@desc: Busca un registro de la base y lo carga en los EF.
*/
	{
		if(!isset($clave)) return false;
		//Busco las columnas que tengo que recuperar
		foreach ($this->lista_ef as $ef){	//Tengo que recorrer todos los EF...
			$dato = $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja DATO COMPUESTO
				for($x=0;$x<count($dato);$x++){
					$sql_col[] = $dato[$x];
				}
			}else{					//El EF maneja un DATO SIMPLE
				$sql_col[] = $dato;
			}
		}
		//Armo la porcion de SQL que corresponde al WHERE
		$clave_ok = $this->formatear_clave($clave);
		foreach($clave_ok as $columna => $valor){
			$sql_where[] = "( $columna = '$valor')";
		}
                //Validacion, no 
                $sql_where[] = $this->control_registro_activo();
		$sql = 	" SELECT " . implode(", ",$sql_col) . 
				" FROM " . $this->info_ut_formulario["tabla"] .
				" WHERE " . implode(" AND ",$sql_where) .";";
		//Busco el registro en la base
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$rs){//SQL mal formado
			$this->observar("error","[recuperar_registro_db] - No se genero un recordset [SQL] $sql - [ERROR] " . 
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
		}
		if($rs->EOF){//NO existe el registro
			return false;
		}
		$datos_db = current($rs->getArray());//Siempre va a ser un solo registro
		//ei_arbol($datos_db,"DATOS DB");
		//Seteo los EF con el valor recuperado
		foreach ($this->lista_ef as $ef){	//Tengo que recorrer todos los EF...
			$dato = $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja DATO COMPUESTO
				$temp = array();
				for($x=0;$x<count($dato);$x++){
					$temp[$dato[$x]]=$datos_db[$dato[$x]];
				}
			}else{					//El EF maneja un DATO SIMPLE
				$temp = $datos_db[$dato];
			}
			$this->elemento_formulario[$ef]->cargar_estado($temp);
		}
		return true;
	}
	//-------------------------------------------------------------------------------
	
        function control_registro_activo()
        {
                return $this->info_ut_formulario['campo_bl'] . " IS NULL";
        }

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------------  Generacion de SQL   ------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_sql_delete()
/*
 	@@acceso: interno
	@@desc: ELIMINA el registro en la BASE
*/
	{
		global $db;
		//Grabo el contenido de la interface en la base
		//Armo la porcion de SQL que corresponde al WHERE
		foreach( $this->obtener_clave() as $columna => $valor){
			$sql_where[] = "( $columna = '$valor')";
		}
		$sql = 	" UPDATE " . $this->info_ut_formulario["tabla"] . 
                        " SET " . $this->info_ut_formulario['campo_bl'] . " = '" .
                        $this->clausula_eliminar_registro() 
                        . "' WHERE " . implode(" AND ",$sql_where) .";";
		return array($sql);
	}
	//-------------------------------------------------------------------------------

        function clausula_eliminar_registro()
        {
                return date("Y-m-d");
        }
	//-------------------------------------------------------------------------------
        
}
?>