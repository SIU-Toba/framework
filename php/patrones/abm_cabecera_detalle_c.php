<?
//------------- Codigo de control de ETAPA ("cabecera" y "detalle") ----------------

	if( $this->hilo->recuperar_dato("control_etapa") !== null ){
		//Viene de de un OBJETO
		if( $this->hilo->obtener_parametro("reiniciar_ciclo") !== null ){
			//Se presiono el LINK que reinicia el CICLO
			$etapa = "cabecera";
		}else{
			$etapa = "detalle";
		}
	}else{
		//Entrada INCIAL
		$etapa = "cabecera";
	}
	$this->hilo->persistir_dato("control_etapa",1);	
//---------------------------------------------------------------------------------------	
	
	//Creo el ABM cabecera

	$abm_cabecera = $this->cargar_objeto("objeto_mt_abms",0);
	if($abm_cabecera > -1){

		$this->objetos[$abm_cabecera]->procesar(null,true);
		//Si el ABM proceso una BAJA, vuelvo a la etapa INICIAL
		if($this->objetos[$abm_cabecera]->obtener_etapa()=="PM-D"){
			$etapa = "cabecera";
		}
		elseif($this->objetos[$abm_cabecera]->obtener_etapa()=="SA"){
			$etapa = "cabecera";
		}
		if($etapa=="cabecera")
		{
			$cuadro_cabecera = $this->cargar_objeto("objeto_cuadro",0);
			if($cuadro_cabecera > -1){
				$this->objetos[$cuadro_cabecera]->cargar_datos();
				enter();
				$this->objetos[$cuadro_cabecera]->obtener_html();
				$this->objetos[$abm_cabecera]->procesar_etapa_SA();
				$this->objetos[$abm_cabecera]->limpiar_interface();
				$this->objetos[$abm_cabecera]->obtener_html();
			}else{
				echo ei_mensaje("No fue posible instanciar la LISTA CABECERA");
			}
		}elseif( $etapa=="detalle" )
		{
			$abm_detalle = $this->cargar_objeto("objeto_mt_abms",1);
			if($abm_detalle > -1){
				$cuadro_detalle = $this->cargar_objeto("objeto_cuadro",1);
				if($cuadro_detalle > -1){

					$html = "<a href='".$this->vinculador->generar_solicitud(null,null,array("reiniciar_ciclo"=>1))."'>";
					$html .=  recurso::imagen_apl("volver.gif",true,null,null,"Volver");
					$html .= "</a>";

					ei_separador($html);

					$this->objetos[$abm_cabecera]->obtener_html();

					//Cargo en el DETALLE el campo oculto que posee la clave de la CABECERA
					//Los EF a cargar en el DETALLE tienen que llamarse como las claves de la CABECERA
					$clave_cabecera = $this->objetos[$abm_cabecera]->obtener_clave();
					//ei_arbol( $clave_cabecera );
					$this->objetos[$abm_detalle]->cargar_estado_ef(	$clave_cabecera );
					
					ei_separador("DETALLE");
					$this->objetos[$abm_detalle]->procesar();

					//Como la lista tiene que trabajar solo con los registros de la cabecera,
					//Tengo que restringir el SELECT

					foreach($clave_cabecera as $columna => $valor){
						$where[] = " ( $columna = '$valor') ";
					}
					$this->objetos[$cuadro_detalle]->cargar_datos($where);

					$this->objetos[$cuadro_detalle]->obtener_html();
					$this->objetos[$abm_detalle]->obtener_html();
					//$this->objetos[$abm_detalle]->info_estado_ef();

				}else{
					echo ei_mensaje("No fue posible instanciar la LISTA DETALLE");
				}
			}else{
				echo ei_mensaje("No fue posible instanciar el ABM DETALLE");
			}
		}else{
			echo ei_mensaje("Nunca deberia entrar aca!");
		}


	}else{
		echo ei_mensaje("No fue posible instanciar el ABM CABECERA");
	}
	enter();
	//$this->info();
	//$this->objetos[$abms]->info_estado();		
	//$this->vinculador->info();
	//$this->hilo->dump_memoria();

//---------------------------------------------------------------------------------------	
?>
