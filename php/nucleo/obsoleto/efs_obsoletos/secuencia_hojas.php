<?php

class secuencia_hojas 
{
//El objetivo de este elemento es manejar la navegacion entre HOJAS
//Mantener el estado consta de dos partes: 1) Mantener las claves de querystring
//seleccionadas y 2) Mantener los WHERE generados por las dimensiones acopladas al FILTRO
//por las hojas anteriores.
//PENDIENTE: Esta clase no deberia hacer un manejo un poco mas 

    var $id;
    var $solicitud;
    var $etapas;
    var $memoria;
    var $parametro_recibido;
    
    function secuencia_hojas($id, &$solicitud)
    {
        $this->solicitud =& $solicitud;
        $this->id = "secuencia_" . $id;
        $this->etapas = count($this->solicitud->indice_objetos["objeto_hoja"]);
        if($this->etapas < 2){ 
            echo ei_mensaje("La secuencia requiere un minimo de 2 HOJAS");
        }
        //-----------  DECIDO en que ETAPA estoy  ----------------
        $this->memoria = $this->solicitud->hilo->recuperar_dato($this->id);
        if(!isset($this->memoria["etapa_actual"])){
            //INICIO del CICLO
            $this->memoria["etapa_actual"]=0;
        }else{
			//Busco la clave de GET de la ultima hoja. 
			//OJO, la etapa actual muestra la anterior porque todavia no se actualizo...
            $id_get = $this->memoria["hoja"][$this->memoria["etapa_actual"]]["clave_get"];
            //Esta seteada la clave GET de la ultima hoja?
            if(isset($_GET[$id_get])){
				// (SI) ------> se avanzo un ETAPA...
	            $this->parametro_recibido = $_GET[$id_get];//Recupero parametros
				//Aumento el contador de etapas.
                if($this->memoria["etapa_actual"] < ($this->etapas -1)) 
					$this->memoria["etapa_actual"]++;
            }else{
   	        	// (NO) -------> CICLO MUERTO (ej: post del filtro)
       	  	}
        }
    }
    //----------------------------------------------------------------------------------

    function obtener_indice_hoja()
    //Devuelve el INDICE correspondiente a la etapa actual.
    {
        return $this->memoria["etapa_actual"];
    }
    //----------------------------------------------------------------------------------    

	function cargar_info_hoja($hoja)
	//Obtiene la informacion de la hoja actual
	{
		//Tengo informacion sobre esta etapa? -> Puede iterar otro elemento del ITEM
		//En la misma etapa de este secuenciador.
		if(!isset($this->memoria["hoja"][$this->memoria["etapa_actual"]])){
			$this->memoria["hoja"][$this->memoria["etapa_actual"]] 
				= $this->solicitud->objetos[$hoja]->obtener_info_secuenciador();
		}
		//Si estoy en el medio de un HILO, a esta hoja se le acopla un WHERE
        if($this->memoria["etapa_actual"]!=0){
            if($this->parametro_recibido!=""){
				$columna = $this->memoria["hoja"][$this->memoria["etapa_actual"]]["columna_entrada"];
				$this->memoria["hoja"][$this->memoria["etapa_actual"]]["where_hilo"]
					 = " (". $columna . " = '" . $this->parametro_recibido ."') ";
			}
		}
	}
    //----------------------------------------------------------------------------------

    function agregar_where_adhoc_etapa($where)
    //El objeto secuencia tambien puede propagar clausulas where AD-HOC
	//Es utilizado para propagar las dimensiones asociadas del FILTRO declaradas por la HOJA
    {
		if(is_array($where)){
			if(!isset($this->memoria["hoja"][$this->memoria["etapa_actual"]]["where_adhoc"])){
				$this->memoria["hoja"][$this->memoria["etapa_actual"]]["where_adhoc"] = $where;
			}
		}
    }
    //----------------------------------------------------------------------------------

    function agregar_from_adhoc_etapa($from)
    //El objeto secuencia tambien puede propagar clausulas FROM AD-HOC
	//Es utilizado para propagar las dimensiones asociadas del FILTRO declaradas por la HOJA
    {
		if(is_array($from)){
			if(!isset($this->memoria["hoja"][$this->memoria["etapa_actual"]]["from_adhoc"])){
				$this->memoria["hoja"][$this->memoria["etapa_actual"]]["from_adhoc"] = $from;
			}
		}
    }
    //----------------------------------------------------------------------------------
	
    function  obtener_where()
    //Devuelve el WHERE correspondiente a esta ETAPA
    {
		if($this->memoria["etapa_actual"]!=0){
			$where = array();
			//Itero la informacion acumulado hasta la esta actual.
			for($a=0; $a<=$this->memoria["etapa_actual"]; $a++)
			{
				if(isset($this->memoria["hoja"][$a]["where_adhoc"])){
					$where = array_merge( $where, $this->memoria["hoja"][$a]["where_adhoc"] );
				}
				if(isset($this->memoria["hoja"][$a]["where_hilo"])){
					$where = array_merge( $where, $this->memoria["hoja"][$a]["where_hilo"] );
				}
			}
			if(count($where)>0) return $where;
		}
    }
    //----------------------------------------------------------------------------------    

    function  obtener_from()
    //Devuelve el WHERE correspondiente a esta etapa.
    {
		if($this->memoria["etapa_actual"]!=0){
			$form = array();
			//Itero la informacion acumulado hasta la esta actual.
			for($a=0; $a<=$this->memoria["etapa_actual"]; $a++)
			{
				if(isset($this->memoria["hoja"][$a]["from_adhoc"])){
					$from = array_merge( $from, $this->memoria["hoja"][$a]["from_adhoc"] );
				}
			}
			if(count($from)>0) return $from;
		}
    }
    //----------------------------------------------------------------------------------    

    function persistir_estado()
    //Guardo la memoria
    {
        $this->solicitud->hilo->persistir_dato($this->id,$this->memoria);
    }
    //----------------------------------------------------------------------------------

    function info()
    {
        $temp["id"] = $this->id;
        $temp["etapas"] = $this->etapas;
        $temp["memoria"] = $this->memoria;
        $temp["parametro_recibido"] = $this->parametro_recibido;
        ei_arbol($temp,"SECUENCIADOR de HOJAS");
    }
    //----------------------------------------------------------------------------------
}
?>