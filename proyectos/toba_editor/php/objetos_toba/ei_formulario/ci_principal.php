<?php
require_once('objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_ei_formulario';	

	function ini()
	{
		parent::ini();
		$ef = toba::memoria()->get_parametro('ef');
		//¿Se selecciono un ef desde afuera?
		if (isset($ef)) {
			$this->set_pantalla(2);
			$this->dependencia('efs')->seleccionar_ef($ef);
		}
	}

	function evt__procesar()
	{
		if (! $this->get_dbr_eventos()->hay_evento_maneja_datos()) {
			toba::notificacion()->agregar('El formulario no posee evento que <strong>maneje datos</strong>,
				esto implica que los datos no viajaran del cliente al servidor.<br><br>
				Para que este comportamiento funcione debe generar algún 
				[wiki:Referencia/Eventos#Modelos modelo de eventos] en la solapa
				de Eventos', 'info');
		}
		parent::evt__procesar();		
	}
	
	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************
	//Para no cambiar la visual del editor los que hago es redireccionar
	//la columna posicion_botonera entre el formulario de propiedades
	//basicas y el datos-tabla de base
	//****************************************************************************
	function conf__prop_basicas()
	{
		$datos = $this->get_entidad()->tabla('prop_basicas')->get();
		$datos['posicion_botonera'] = $this->get_entidad()->tabla('base')->get_columna('posicion_botonera');
		return $datos;
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla('base')->set_columna_valor('posicion_botonera', $datos['posicion_botonera']);
		unset($datos['posicion_botonera']);
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	//*******************************************************************
	//** Dialogo con el CI de EFs  **************************************
	//*******************************************************************

	function evt__2__salida()
	{
		$this->dependencia('efs')->limpiar_seleccion();
	}

	function get_dbr_efs()
	{
		return $this->get_entidad()->tabla('efs');
	}


	//*******************************************************************
	//** Dialogo con el CI de EVENTOS  **********************************
	//*******************************************************************
	
	function get_eventos_estandar($modelo)
	{
		return toba_ei_formulario_info::get_lista_eventos_estandar($modelo);
	}

	function evt__3__salida()
	{
		$this->dependencia('eventos')->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}
	
	//*******************************************************************
	//** Tab de Layout                 **********************************
	//*******************************************************************
	
	function conf__form_layout(toba_ei_formulario $form)
	{
		$vinculo = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'ejecutar'));
		$form->ef('template')->set_botonera('Layout');
		$form->ef('template')->set_alto('400px');				
		//$archivo = toba_recurso::url_toba() . '/js/ckeditor/plugins/templates/templates/toba_layout.js';
		$form->ef('template')->set_path_template(array($vinculo));
		 
		$datos = $this->get_entidad()->tabla('prop_basicas')->get();
		if (isset($datos['template']) && trim($datos['template']) != '') {
			$datos['tipo_layout'] = 'L';
		}
		$form->set_datos($datos);
	}
	
	function evt__form_layout__modificacion($datos)
	{
		if (!isset($datos['tipo_layout'])) {
			$datos['template'] = null;
		}
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	//*******************************************************************
	//** Tab de Layout Impresion     **********************************
	//*******************************************************************
	function conf__form_layout_impresion(toba_ei_formulario $form)
	{
		$vinculo = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'ejecutar'));
		$form->ef('template')->set_botonera('Layout');
		$form->ef('template')->set_alto('400px');		
		$form->ef('template')->set_path_template(array($vinculo));		
		$datos = $this->get_entidad()->tabla('prop_basicas')->get();
		unset($datos['template']);
		if (isset($datos['template_impresion']) && trim($datos['template_impresion']) != '') {
			$datos['tipo_layout'] = 'L';
			$datos['template'] = $datos['template_impresion'];
		}
		$form->set_datos($datos);
	}

	function evt__form_layout_impresion__modificacion($datos)
	{
		if (!isset($datos['tipo_layout'])) {
			$datos['template_impresion'] = null;
		} else {
			$datos['template_impresion'] = $datos['template'];
			unset($datos['template']);
		}
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	function get_tipos_layout()
	{
		return array(
			array('clave' => 'L', 'valor' => 'Usando template')
		);
	}

	function servicio__ejecutar()
	{
		//Determina si el ejecutar es por este ci o por el del parent 
 		$imagen = toba::memoria()->get_parametro('imagen');
		if (isset($imagen)) {
			return parent::servicio__ejecutar();		
		}
		$url = toba::proyecto()->get_www('img/fck_templates/') ;
		$salida = "
		CKEDITOR.addTemplates('default', 
		{
			imagesPath: '". toba::escaper()->escapeJs($url['url'])."', \n
			templates: \n
				[ \n
					{ \n
						title: 'Lineal', \n
						image: 'tabla_1_col.gif', \n
						description: 'Un campo debajo del otro, es el layout original' , \n
						html: '{$this->get_template_lineal()}' \n
					},\n
					{ \n
						title: 'Tabla Lineal', \n
						image: 'tabla_1_col.gif', \n
						description: 'Un campo debajo del otro, es el layout original' , \n
						html: '{$this->get_template_columnas(1)}' \n
					},\n
					{ \n
						title: 'Tabla Dos Columnas',  \n
						image: 'tabla_2_col.gif', \n
						description: 'Se arma una tabla tomando los campos en el orden definido, incluyendo dos campos por fila' ,  \n
						html: '{$this->get_template_columnas(2)}' \n
					}, \n
					{ \n
						title: 'Tabla Tres Columnas', \n
						image: 'tabla_3_col.gif',  \n
						description: 'Se arma una tabla tomando los campos en el orden definido, incluyendo tres campos por fila' ,  \n
						html: '{$this->get_template_columnas(3)}' \n
					} \n
				] \n
		}); ";
		
		echo $salida;
	}
	
	protected function get_template_columnas($columnas)
	{
		$salida = '<table>';
		$efs = $this->get_dbr_efs()->get_filas();
		$efs = rs_ordenar_por_columna($efs, 'orden');
		$i = 0;
		$total = count($efs);
		foreach ($efs as $ef) {
			if (! isset($ef['desactivado']) || ! $ef['desactivado']) {
				if ($i % $columnas == 0) {
					$salida .= '<tr>';
				}				
				$salida .= '<td>[ef id='.$ef['identificador'].']</td>';
				$i++;
				if ($i % $columnas == 0) {
					$salida .= '</tr>';
				}				
			}
		}
		$salida .= '</table>';
		return $salida;		
	}
	
	protected function get_template_lineal()
	{
		$salida = '';
		$efs = $this->get_dbr_efs()->get_filas();
		$efs = rs_ordenar_por_columna($efs, 'orden');
		$i = 0;
		$total = count($efs);
		foreach ($efs as $ef) {
			if (!isset($ef['desactivado']) || ! $ef['desactivado']) {
				$salida .= '[ef id='.$ef['identificador'].']';
			}
		}
		return $salida;		
	} 	
	
}
?>