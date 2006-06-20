<?
	$abms = $this->cargar_objeto("objeto_mt_abms",0);
	$this->objetos[$abms]->procesar();

	// Si viene de que apretaron el boton Agregar
	if ($param = $this->hilo->obtener_parametro("alta") ) {

		//--> Modo ALTA


		// Si cuando fue al ABM a agregar se arrepintio, puede volver al listado
		$html = "<a href='".$this->vinculador->generar_solicitud(null,null,array("reiniciar_ciclo"=>1))."'>";
		$html .=  recurso::imagen_apl("volver.gif",true,null,null,"Volver al listado");
		$html .= "</a>";
		ei_separador($html);

  	    $this->objetos[$abms]->obtener_html();

	}else {

		$etapa = $this->objetos[$abms]->obtener_etapa();
		//Si el abm no esta en modo MODIFICACION, muestro el cuadro
		if(($etapa != "SM")) {

		//--> Modo SELECCION

		  $cuadro = $this->cargar_objeto("objeto_cuadro",0);   
		  $this->objetos[$cuadro]->cargar_datos();   
		  $url = $this->vinculador->generar_solicitud(null,null,array("alta"=>1));

		  $html = "&nbsp;&nbsp;<a href='".$url."'>";
		  $html .=  recurso::imagen_apl("Agregar.gif",true,null,null,"Agregar nuevo");
		  $html .= "</a>&nbsp;&nbsp;";

		  ei_separador($html);
			enter();
		  $this->objetos[$cuadro]->obtener_html();  
	
		}
		else  {

		//--> Modo MODIFICACION

		// Si cuando fue al ABM a agregar se arrepintio, puede volver al listado
		$html = "<a href='".$this->vinculador->generar_solicitud(null,null,array("reiniciar_ciclo"=>1))."'>";
		$html .=  recurso::imagen_apl("volver.gif",true,null,null,"Volver al listado");
		$html .= "</a>";
		ei_separador($html);

		  $this->objetos[$abms]->obtener_html();
		}
	}
?>
