<?
require_once("nucleo/browser/clases/objeto_esquema.php");						//Ancestro de todos los OE

class objeto_esquema_db extends objeto_esquema
/*
	@@acceso: publico
	@@desc: Permite representar planeamientos en el eje del tiempo
*/
{
	var $sql;
	var $datos;
	    	
	function objeto_esquema($id)
/*
	@@acceso: publico
	@@desc: Constructor de la clase
*/
	{
		parent::objeto_esquema($id);
	}

//---------------------------------------------------------------

	function obtener_dot()
	{
		if(isset($this->datos))
		{
			//ei_arbol($this->datos);
			$auto_dot = "";
			for($a=0;$a<count($this->datos);$a++){
				$auto_dot .= $this->datos[$a][0] ." -> ". $this->datos[$a][1] . "\n";
			}
			$dot = ereg_replace("%%%",$auto_dot,$this->info_esquema['dot']);
		}else{
			$dot = "graph J { 'No hay datos' }";
		}
		//echo $dot;
		return $dot;
	}
//---------------------------------------------------------------

    function cargar_datos($where=null,$from=null,$memorizar=true)
/*
    @@acceso: publico
    @@desc: Carga los datos del cuadro desde la base
    @@param: array | sentencias WHERE a acoplar
    @@param: array | Sentencias FROM a acoplar
    @@param: boolean | Desactivar la paginacion
    @@retorno: boolean | Estado resultante de la operacion
*/
    {
        //Generacion directa de clausulas WHERE a traves de vinculos al objeto
        //ATENCION: se confia en un paso correcto de parametros
        if(isset($this->canal_recibidos))
        {
            //La entrada por el CANAL  fuerza el estado SM, mas alla de la memoria
            $clave = explode(apex_qs_separador,$this->canal_recibidos);     
            $columnas = explode(",",$this->info_cuadro['asociacion_columnas']);
            for($a=0;$a<count($columnas);$a++){
                $where_vinculo[]= "{$columnas[$a]} = '{$clave[$a]}'";
            }
            if(is_array($where)){
                $where = array_merge($where,$where_vinculo);
            }else{
                $where = $where_vinculo;
            }
        }

        global $db,$ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
    
        //Setear MEMORIA
        if(isset($where)){
            $this->memoria["where"] = $where;
            $this->memoria["from"] = $from;
        }else{
            if(isset($this->memoria["where"])){
                //Recuperar MEMORIA
                $where = $this->memoria["where"];
                $from = $this->memoria["from"];
            }
        }
		//
		if(!$memorizar){
	   	     $this->borrar_memoria();
		}

        //Concateno el WHERE y el FROM pasado por el consumidor
        $sql = sql_agregar_clausulas_where( stripslashes($this->info_esquema['sql']),$where);
        $sql = sql_agregar_tablas_from($sql,$from);     
        $this->sql = $sql;
        $rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
        if(!$rs){//SQL mal formado
            $this->observar("error","OBJETO ESQUEMA [cargar_datos] - No se genero un recordset [SQL] $sql - [ERROR] " . 
                            $db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
            return false;
        }
        if($rs->EOF){//NO existe el registro
            $this->observar("info","OBJETO ESQUEMA [cargar_datos] - No hay registros");
            return false;
        }
        $this->datos = $rs->getArray();
        //ei_arbol($this->datos,"DATOS");
        return true;
    }
//--------------------------------------------------------------------------

}
?>
