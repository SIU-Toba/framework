<?php

	class estado_error
	//Maneja un registro del estado erroneo
	{
		var $marcador_actual;
		var $excep_prox;
		var $excepciones;

		function estado_error()
		{
			$this->excep_prox = 0;
			$this->marcador_actual = $this->excep_prox;
		}
		
		function info()
		{
			$temp["marcador actual"] = $this->marcador_actual;
			$temp["prox"] = $this->excep_prox;
			$temp["excepciones"] = $this->excepciones;
			ei_arbol($temp);
		}	
		
		function agregar_marca($marcador)
		//Agrega un marcador candidato
		{
			$this->marcador_actual = $marcador;
		}
		
		function registrar_excepcion($excepcion){
			$excepcion = $this->excepciones[$this->marcador_actual];
			$this->excep_prox++ ;
			$this->marcador_actual = $this->excep_prox;
		}

		function consultar_marca($marca){
			
		}
	
	}
	//-----------------------------------------------------------------


?>