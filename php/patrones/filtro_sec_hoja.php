<?

    //$this->info();
	$filtro = $this->cargar_objeto("objeto_filtro",0);
	if($filtro > -1){
		$this->objetos[$filtro]->memorizar_estado();
		echo "<br>";
        $this->objetos[$filtro]->obtener_interface_vertical();
		//$this->objetos[$filtro]->info();
        
        //Creo la SECUENCIA.
        include_once("nucleo/browser/interface/secuencia_hojas.php");
        $secuencia = new secuencia_hojas($this->info["item"],$this);

       	$hoja =	$this->cargar_objeto("objeto_hoja", $secuencia->obtener_indice_hoja() );
		if($hoja > -1){
			//HAgo que la secuencia cargue la informacion de la hoja ACTUAL

			//$this->objetos[$hoja]->info();
			$secuencia->cargar_info_hoja($hoja);

			//$this->objetos[$hoja]->info();
			//Si la HOJA tiene dimensiones asociadas hay que informarlas en el FILTRO.
			if( $dimensiones = $this->objetos[$hoja]->obtener_dimensiones_asociadas() ){
				//ei_arbol($dimensiones,"DIMENSIONES");
				$this->objetos[$filtro]->acoplar_dimensiones($dimensiones);
			}

            //Si el filtro pasa sus controles puedo mostrar los datos
    		if($this->objetos[$filtro]->controlar_estado()===true)
    		{
				//Pido las clausulas WHERE y FROM a la SECUENCIA y al FILTRO
				$where = array_merge( $secuencia->obtener_where(),$this->objetos[$filtro]->obtener_where() );
				$from = array_merge( $secuencia->obtener_from(), $this->objetos[$filtro]->obtener_from() );
				//ei_arbol($where, "WHERE"); ei_arbol($from, "FROM");

				if($this->objetos[$hoja]->cargar_datos($where,$from)===true)
				{
            		echo "<br>";
					$this->objetos[$hoja]->generar_html();
            		echo "<br>";
				}
                else{
					$this->objetos[$hoja]->mostrar_estado();
				}
    		}
            else{
    			ei_arbol($this->observaciones);
       		}
		}else{
			echo ei_mensaje("No se pudo crear la hoja de datos");
		}

		//La propagacion de las dimensiones acopladas depende de la secuencia
		$secuencia->agregar_where_adhoc_etapa( $this->objetos[$filtro]->obtener_where_dim_acopladas() );
		$secuencia->agregar_from_adhoc_etapa( $this->objetos[$filtro]->obtener_from_dim_acopladas() );

        //HAgo que la informacion que poseen los objetos PERSISTA.
        $secuencia->persistir_estado();
        $this->objetos[$filtro]->persistir_estado();

		//STATUS
        //$secuencia->info();
		//$this->objetos[$filtro]->info();
        //dump_SESSION();
	}else{
		echo ei_mensaje("No se pudo crear el filtro");
	}		
?>