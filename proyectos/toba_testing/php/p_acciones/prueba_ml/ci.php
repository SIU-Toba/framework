<?php

class ci extends toba_testing_pers_ci
{ 
	protected $datos_ml;
	protected $datos_formulario;
	protected $datos_formulario_abm = array();	
	protected $registro_actual;
	protected $datos_filtro;

    function mantener_estado_sesion() 
    { 
        $propiedades = parent::mantener_estado_sesion(); 
        $propiedades[] = "datos_ml"; 
        $propiedades[] = "datos_formulario";
        $propiedades[] = "datos_formulario_abm";
        $propiedades[] = "registro_actual";		
        $propiedades[] = "datos_filtro";
        return $propiedades; 
    } 	

	function obtener_html_contenido__30()
	{
		ei_arbol($this->datos_ml);
		ei_arbol($this->datos_formulario);
		ei_arbol($this->datos_formulario_abm);
	}	
	
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		$eventos['reiniciar']['etiqueta'] = "Reiniciar";
		$eventos['reiniciar']['imagen'] = "";
		$eventos['reiniciar']['confirmacion'] = "";
		$eventos['reiniciar']['estilo']="";
		$eventos['reiniciar']['tip']="Retorna la operación a su estado inicial";		
		return $eventos;
	}	
	
	function evt__post_cargar_datos_dependencias()
	{
		if (isset($this->dependencias['formulario'])) {
			$eventos = $this->dependencias['formulario']->get_lista_eventos();
			$eventos += eventos::evento_estandar('otro_evento', 'Otro Evento');
			$this->dependencias['formulario']->set_eventos($eventos);
		}
		if (isset($this->dependencias['cuadro_abm'])) {
			//Se le agrega un botón cancelar
			$eventos = $this->dependencias['cuadro_abm']->get_lista_eventos();
			$eventos += eventos::cancelar();
			//Se le agrega un evento seleccion_borrar a cada fila
			$borra = eventos::duplicar(eventos::seleccion(), 'seleccion_borrar');
			$borra['seleccion_borrar']['imagen'] = toba_recurso::imagen_toba('borrar.gif');
			$borra['seleccion_borrar']['confirmacion'] = '¿Está seguro que quiere borrar la fila?';
			$borra['seleccion_borrar']['ayuda'] = 'Borra la fila';
			$eventos += $borra;
			$this->dependencias['cuadro_abm']->set_eventos($eventos);
		}		
	}	
	
	function evt__reiniciar()
	{
		$this->disparar_limpieza_memoria();
	}

	//------------------------------------
	//				ML
	//------------------------------------
	function conf__ml()
	{
		if (isset($this->datos_ml))
			return $this->datos_ml;
		else {
			return array(
				array('oculto' => 123), 
				array('oculto' => 456), 				
			);
		}
	}	

	function evt__ml__modificacion($datos)
	{
		foreach ($datos as $id => $dato) {
			if ($dato[apex_ei_analisis_fila] == 'B')
				unset($datos[$id]);
		}
		$this->datos_ml = $datos;
	}

	//--- Eventos granulares
	function evt__ml__registro_alta($registro, $id)
	{
		$this->datos_ml[$id] = $registro;
	}
	
	function evt__ml__registro_modificacion($registro, $id)
	{
		$this->datos_ml[$id] = $registro;
	}		
	
	function evt__ml__registro_baja($id)
	{
		unset($this->datos_ml[$id]);
	}
	
	//------------------------------------
	//			FORMULARIO
	//------------------------------------
	function evt__formulario__modificacion($datos)
	{
		if ($datos['editable'] == 'asd')
			throw new toba_error('El editable no puede ser \'asd\'.');
		$this->datos_formulario = $datos;
	}

	function conf__formulario(){ 
		if (isset($this->datos_formulario))
			return $this->datos_formulario;
		else
			return array('oculto' => 'oculto!!');
	}	
	
	//------------------------------------
	//		FORMULARIO en ABM
	//------------------------------------
	function conf__formulario_abm()
	{
		$this->dependencias['formulario_abm']->set_colapsable(false);
		if (isset($this->registro_actual)) {
			foreach ($this->datos_formulario_abm as $registro) {
				if ($this->registro_actual == $registro['editable']) {
				   return $registro;
				}
			}
		}
		return null;
	}	
	
	function evt__formulario_abm__alta($registro)
	{
		$this->datos_formulario_abm[$registro['editable']] = $registro;
	}
	
	function evt__formulario_abm__modificacion($registro_mod)
	{
		$clave = $registro_mod['editable'];
		if (isset($this->datos_formulario_abm[$clave]))
			$this->datos_formulario_abm[$clave] = $registro_mod;
		else
			throw new toba_error('EL ABM no contiene un registro en edición');
	}	

	function evt__formulario_abm__cancelar()
	{
		unset($this->registro_actual);
		$this->dependencias['cuadro_abm']->deseleccionar();	
	}	


	function evt__formulario_abm__baja()
	{
		if (isset($this->registro_actual))
			unset($this->datos_formulario_abm[$this->registro_actual]);
		else
			throw new toba_error('EL ABM no contiene un registro en edición');	
	}	
	
	//------------------------------------
	//		CUADRO en ABM
	//------------------------------------
	function conf__cuadro_abm()
	{
		//Filtra los elementos
		$candidatos = array_values($this->datos_formulario_abm);
		if (! isset($this->datos_filtro['editable'])) {
			return $candidatos;
		}
		$cuadro = array();
		foreach ($candidatos as $i => $candidato) {
			if (stripos($candidato['editable'], $this->datos_filtro['editable']) !== false) {	//Esta filtrado
				$cuadro[] = $candidato;
			}
		}
		return $cuadro;
	}

	function evt__cuadro_abm__seleccion($seleccion)
	{
		$this->registro_actual = $seleccion;
	}

	function evt__cuadro_abm__cancelar()
	{
		$this->evt__formulario_abm__cancelar();
	}	
	
	function evt__cuadro_abm__seleccion_borrar($id)
	{
		$this->informar_msg("Se quiere borrar la fila $id", 'info');	
	}
	
	function evt__cuadro_abm__ordenar($param)
	{
		$columna = $param['columna'];
		$sentido = $param['sentido'];
		$this->informar_msg("Se quiere ordenar la columna $columna en orden $sentido", 'info');
	}

	//------------------------------------
	//		FILTRO en ABM
	//------------------------------------
	function conf__filtro_abm()
	{
		$this->dependencias['filtro_abm']->colapsar();
		if (isset($this->datos_filtro))
			return $this->datos_filtro;
		else
			return array();
	}
	
	function evt__filtro_abm__filtrar($datos)
	{
		$this->datos_filtro = $datos;
	}
	
	function evt__filtro_abm__cancelar()
	{
		unset($this->datos_filtro);
	}	
	
	//------------------------------------
	//		JAVASCRIPT
	//------------------------------------	
	function extender_objeto_js()
	{

		echo "
			{$this->objeto_js}.evt__validar_datos = function() {
				if (this._evento.id == 'procesar') {
					notificacion.agregar('No se puede procesar!');
					return false;
				}
				return true;
			}\n";
	}

	//------------------------------------
	//		PROCESO
	//------------------------------------
	function get_info_post_proceso()
	//Mostrar una pantalla cuando se termino el proceso OK.
	{
		return "Mensaje a mostrar despues del procesamiento";	
	}

	
} 

?>
