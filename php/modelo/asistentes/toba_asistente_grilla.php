<?php

class toba_asistente_grilla extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->molde, $this->molde_abms, $this->molde_abms_fila));
		$this->ci->set_titulo($this->molde['nombre']);
		$this->ci->agregar_pantalla(1, 'Pantalla');
		$this->crear_extension();
		//- Creo dependencias -----------------------------------
		$form = $this->ci->agregar_dep('toba_ei_formulario_ml', 'formulario');
		$this->ci->asociar_pantalla_dep(1, $form);
		$this->generar_formulario_ml($form);
		$tabla = $this->ci->agregar_dep('toba_datos_tabla', 'datos');
		$this->generar_datos_tabla($tabla, $this->molde_abms['tabla'], $this->molde_abms_fila);
		//- Eventos del CI ---------------------------------------
		$evento = $this->ci->agregar_evento('guardar');
		$evento->maneja_datos();
		$evento->en_botonera();
		$evento->set_imagen('guardar.gif');
		$this->ci->asociar_pantalla_evento(1, $evento);
	}
	
	function crear_extension()
	{
		$clase = $this->molde['prefijo_clases'] . 'ci';
		$this->ci->extender($clase , $clase . '.php');
		$metodo = new toba_codigo_metodo_php('ini__operacion');
		$metodo->set_contenido("\$this->dep('datos')->cargar();");
		$this->ci->php()->agregar($metodo);
		$metodo = new toba_codigo_metodo_php('evt__guardar');
		$metodo->set_contenido( array(	"\$this->dep('datos')->sincronizar();",
										"\$this->dep('datos')->resetear();",
										"\$this->dep('datos')->cargar();"));
		$this->ci->php()->agregar($metodo);
		$metodo = new toba_codigo_metodo_php('evt__formulario__modificacion',array('$datos'));
		$metodo->set_contenido("\$this->dep('datos')->procesar_filas(\$datos);");
		$this->ci->php()->agregar($metodo);
		$metodo = new toba_codigo_metodo_php('conf__formulario',array('$componente'));
		$metodo->set_contenido("\$componente->set_datos(\$this->dep('datos')->get_filas());");
		$this->ci->php()->agregar($metodo);
	}

	function generar_formulario_ml($form)
	{
		$form->set_nombre($this->molde['nombre'] . ' - Form');
		$form->set_analisis_cambios('LINEA');
		$form->agregar_filas_js();
		$this->generar_efs($form, $this->molde_abms_fila);
		$evento = $form->agregar_evento('modificacion');
		$evento->maneja_datos();
		$evento->implicito();
	}
}
?>