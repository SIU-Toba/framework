EXTENSION EN JAVASCRIPT DE LOS OBJETOS FORMULARIO
----------------------------------------------------

INTRODUCCION
-------------

Los objetos ei_fomulario y ei_formulario_ml poseen una clase asociada por donde pasan todo el comportamiento de los objetos en el browser.
Por cada objeto-toba creado se instancia una clase-js responsable del mismo. Desde PHP esta delegación de responsabilidad se ve en estos tres métodos de la rama objeto_ei_formulario
	
		function crear_objeto_js()
		function extender_objeto_js()
		function iniciar_objeto_js()

Este es el orden cronológico de ejecución: en la creación se llama al constructor de la clase pasándole parámetros de la definición, luego hay una ventana de extensión del objeto y finalmente el objeto se inicializa. Por convención no es recomendado extender luego de la inicialización.

Para poder ver andando las extensiones están los casos de test (item de toba /pruebas/testing_automatico_js), corren bien en IE y Moz (salvo por una pasada de las mascaras que la deje para acordarme). En el caso del IE se puede agrandar el frame de más abajo para poder juguetear con el objeto una vez terminado el test (y creer que lo que hace es real). Todos los ejemplos de este documento están sacados de ahí.

También en el proyecto toba_testing (hay que hacer un symlink a $toba_dir/php/acciones/pruebas/testing_automatico) hay un par de ejemplos de un multietapa con un formulario y un formulario_ml con bastantes detalles. 


VALIDACION Y SUBMIT
-------------------

ei_formulario:
-------------

El servicio submit se llama en el proceso de submit del CI (cambio de etapa, disparo de evento o procesamiento). Su función es dejar el objeto en un estado reconocible al homónimo en PHP, esto es:
	- Ejecuta el submit de los efs (para que las mascaras saquen su cosmética por ejemplo)
	- En el caso del formulario enviar el evento correspondiente (alta, baja, modificación, limpiar)
	- En el caso del ML envia las filas que deben tenerse en cuenta

Previo a esto se llama al servicio validar. Por ejemplo si en un formulario se quiere validar sólo si un checkbox está seleccionado se extendería de esta forma en el método PHP extender_objeto_js()

	function extender_objeto_js() 
	{
		echo "
			//Valida sólo si el checkbox está seleccionado
			{$this->objeto_js}.validar = function() {
				if (this.ef('id_del_checkbox').chequeado())
					return this.validacion_defecto();
				else
					return true;
			}
		";
	}

El servicio objeto_ei_formulario.ef(id_ef) retorna la referencia al objeto ef. El método validación_defecto() recorre cada ef validándolo. 

Para engancharse en un evento particular (digamos el agregar) existen métodos para cada uno. Si se quiere validar que un editable sea un número par:

	{$this->objeto_js}.evento_agregar = function() {
		if (this.validar()) {
			var editable = this.ef('el_editable');
			if (editable.valor() % 2 != 0) {
				editable.seleccionar();
				alert('El número debe ser par');
				return false;
			} else {
				return true;
			}
		} else {
			return false
		}
	}
	
Los mismo se podría haber hecho extendiendo el método validar si es algo general al alta y la modificación.
Divagando un poco más se podría	extender el submit para hacer algo más mientras se envía, no hay un método submit_defecto (se podría agregar si merece) pero esto sirve para mostrar como 'heredar' un comportamiento: (Esta no es la herencia de prototipos de javascript pero es muy util para salir del paso).

	{$this->objeto_js}._submit_defecto = {$this->objeto_js}.submit;	
	{$this->objeto_js}.submit = function() {
		alert('pre_proceso');
		if (this._submit_defecto()) {
			alert('post_proceso');
		}
	}

ei_formulario_ml:
----------------

La validación en el caso del ML es similar sólo que ahora las filas entran en juego:

	//Una validacion nueva, si no esta chequeado pasar por alto la validacion de esa fila
	{$this->objeto_js}.validar = function() {
		var filas = this.filas();
		for (fila in filas) {
			if ( this.ef('mi_check').ir_a_fila(filas[fila]).chequeado()) {
				if (! this.validar_fila(fila) )
					return false;
			}
		}
		return true;
	}
	
	

PROCESAMIENTO DE EFS
----------------------

ei_formulario
-------------

El procesamiento de un EF permite armar comportamientos entre varios efs de un formulario (en un futuro cercano de distintos formularios). Un ef se procesa cuando termina su edición (el inicio del formulario es un caso particular de proceso donde pasa de no tener estado a tener uno), para que el formulario 'escuche' este proceso es necesario colgársele antes de del iniciar, nuevamente en el método PHP extender_objeto_js().

Por ejemplo si se quiere que un checkbox deshabilite un editable se hace de esta forma:

	{this->objeto_js}.procesar_id_del_checkbox = function () {
		if (this.ef('id_del_checkbox').chequeado())
			this.ef('id_del_editable').activar();
		else
			this.ef('id_del_editable').desactivar();
	}

Hay que notar el nombre de la función: "procesar_" seguido por el id del ef. Automáticamente el formulario 'escucha' cualquier cambio al checkbox y lo procesa en caso de que suceda. 

Otro escenario es que un combo dispare el submit del objeto, para que ande sólo resta que los CI sean clases para que los objetos puedan disparar un submit (disparar también un onSubmit porque el browser no lo manda si se hace a mano). De todas formas el fomulario sería así:

	//Extension del procesamiento del ef el_combo
	//El parametro opcional es_inicial sirve para distinguir el disparo inicial durante la carga
	{this->objeto_js}.procesar_el_combo = function (es_inicial) {
			if (! es_inicial && this.ef('combo').valor() != apex_ef_no_seteado) {
				this.set_evento('M', '{$this->submit_modificar}');
				this.submit();
			}
	}

ei_formulario_ml
----------------

El multilínea tiene como shortcut el procesamiento llamado 'totalizacion' que consiste en sumarizar el valor de cada fila posicionándose en un ef determinado desde el administrador (flag 'total' en el ABM de los ef del ML). Si se quisiera simular la totalización usando la extensión (no es necesario pero es fácil de ver como se hace): 

	{this->objeto_js}.agregar_procesamiento('mi_moneda');
	
Para colgar procesamientos a otras columnas hay que definir nuevas funciones, nuevamente deben empezar con "procesar_":

	//Cuando se procesa el checkbox sumarizar los importes cuya fila está chequeada (es más facil verlo)
	{this->objeto_js}.procesar_mi_check = function () {
		var total = 0;	
		var filas = this.filas();
		for (fila in filas)	{
			var mi_moneda = this.ef('mi_moneda').ir_a_fila(filas[fila]);
			if (this.ef('mi_check').ir_a_fila(filas[fila]).chequeado()) {
				mi_moneda.activar();
				valor = mi_moneda.valor();
				valor = (valor=='' || isNaN(valor)) ? 0 : valor;	//Por si viene vacio o cualquier otra cosa
				total += valor
			}
			else {
				mi_moneda.desactivar();
			}
		}
		total = Math.round(total * 100)/100;
		this.cambiar_total('mi_moneda', total);
		return total;
	}

Este procesamiento particular finaliza cambiando el total de la columna 'mi_moneda'.


REFRESCO GRAFICO
-----------------

Tanto en el fomulario y el formulario_ml existen situaciones en donde por ejemplo por extensión se cambia el valor de un ef. El procesamiento de ese ef sólo se produce en el onblur, onclick u onchange y estos eventos no los dispara el browser cuando se modifican por script. Esto se puede tomar como punto a favor para no hacer un refresco gráfico continuo si por ejemplo se le suma 15 a todos los efs de un ML de 50 líneas y se dispara un refresco por cada uno... puede tardar mucho (1). Otro motivo es evitar entrar en deadlock (el combo cambia el editable, este cambia el combo y así siguiendo...), actualmente el orden visual determina la prioridad.

En el caso del formulario existe un sólo refresco que es el de procesamiento {$this->objeto_js}.refrescar_procesamientos() que recalcula el procesamiento de los efs.

Para el ML existen algunos más:
		-refrescar_procesamientos: Recorre todas las columnas que tienen procesamientos y las recalcula
		-refrescar_numeracion_filas: Recorre todas las filas y las vuelve a numerara comenzando desde uno
		-refrescar_deshacer: Actualiza el botón deshacer
		-refrescar_seleccion: Resalta la línea seleccionada 
		-refrescar_foco: Toma la fila seleccionada y le pone foco al primer ef que se la banque.
		-refrescar_eventos_procesamiento: Toma una fila y le refresca los listeners de procesamiento


FIN
-----------

¿Hay cosas que quedan afuera de esta forma de extender?
Estaría bueno que cualquier extensión se vaya subiendo a los test, baja bastante la presión arterial!!.


(1) Ahora también puede ser pesadito, falta hacer pruebas más formales, pero acá en el orden de los cientos de filas empieza a ponerse pesado el refresco.
