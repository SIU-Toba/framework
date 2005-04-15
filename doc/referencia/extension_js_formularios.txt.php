EXTENSION EN JAVASCRIPT DE LOS OBJETOS FORMULARIO
----------------------------------------------------

INTRODUCCION
-------------

Los objetos ei_fomulario y ei_formulario_ml poseen una clase asociada por donde pasan todo el comportamiento de los objetos en el browser.
Por cada objeto-toba creado se instancia una clase-js responsable del mismo. Desde PHP esta delegaci�n de responsabilidad se ve en estos tres m�todos de la rama objeto_ei_formulario
	
		function crear_objeto_js()
		function extender_objeto_js()
		function iniciar_objeto_js()

Este es el orden cronol�gico de ejecuci�n: en la creaci�n se llama al constructor de la clase pas�ndole par�metros de la definici�n, luego hay una ventana de extensi�n del objeto y finalmente el objeto se inicializa. Por convenci�n no es recomendado extender luego de la inicializaci�n.

Para poder ver andando las extensiones est�n los casos de test (item de toba /pruebas/testing_automatico_js), corren bien en IE y Moz (salvo por una pasada de las mascaras que la deje para acordarme). En el caso del IE se puede agrandar el frame de m�s abajo para poder juguetear con el objeto una vez terminado el test (y creer que lo que hace es real). Todos los ejemplos de este documento est�n sacados de ah�.

Tambi�n en el proyecto toba_testing (hay que hacer un symlink a $toba_dir/php/acciones/pruebas/testing_automatico) hay un par de ejemplos de un multietapa con un formulario y un formulario_ml con bastantes detalles. 


VALIDACION Y SUBMIT
-------------------

ei_formulario:
-------------

El servicio submit se llama en el proceso de submit del CI (cambio de etapa, disparo de evento o procesamiento). Su funci�n es dejar el objeto en un estado reconocible al hom�nimo en PHP, esto es:
	- Ejecuta el submit de los efs (para que las mascaras saquen su cosm�tica por ejemplo)
	- En el caso del formulario enviar el evento correspondiente (alta, baja, modificaci�n, limpiar)
	- En el caso del ML envia las filas que deben tenerse en cuenta

Previo a esto se llama al servicio validar. Por ejemplo si en un formulario se quiere validar s�lo si un checkbox est� seleccionado se extender�a de esta forma en el m�todo PHP extender_objeto_js()

	function extender_objeto_js() 
	{
		echo "
			//Valida s�lo si el checkbox est� seleccionado
			{$this->objeto_js}.validar = function() {
				if (this.ef('id_del_checkbox').chequeado())
					return this.validacion_defecto();
				else
					return true;
			}
		";
	}

El servicio objeto_ei_formulario.ef(id_ef) retorna la referencia al objeto ef. El m�todo validaci�n_defecto() recorre cada ef valid�ndolo. 

Para engancharse en un evento particular (digamos el agregar) existen m�todos para cada uno. Si se quiere validar que un editable sea un n�mero par:

	{$this->objeto_js}.evento_agregar = function() {
		if (this.validar()) {
			var editable = this.ef('el_editable');
			if (editable.valor() % 2 != 0) {
				editable.seleccionar();
				alert('El n�mero debe ser par');
				return false;
			} else {
				return true;
			}
		} else {
			return false
		}
	}
	
Los mismo se podr�a haber hecho extendiendo el m�todo validar si es algo general al alta y la modificaci�n.
Divagando un poco m�s se podr�a	extender el submit para hacer algo m�s mientras se env�a, no hay un m�todo submit_defecto (se podr�a agregar si merece) pero esto sirve para mostrar como 'heredar' un comportamiento: (Esta no es la herencia de prototipos de javascript pero es muy util para salir del paso).

	{$this->objeto_js}._submit_defecto = {$this->objeto_js}.submit;	
	{$this->objeto_js}.submit = function() {
		alert('pre_proceso');
		if (this._submit_defecto()) {
			alert('post_proceso');
		}
	}

ei_formulario_ml:
----------------

La validaci�n en el caso del ML es similar s�lo que ahora las filas entran en juego:

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

El procesamiento de un EF permite armar comportamientos entre varios efs de un formulario (en un futuro cercano de distintos formularios). Un ef se procesa cuando termina su edici�n (el inicio del formulario es un caso particular de proceso donde pasa de no tener estado a tener uno), para que el formulario 'escuche' este proceso es necesario colg�rsele antes de del iniciar, nuevamente en el m�todo PHP extender_objeto_js().

Por ejemplo si se quiere que un checkbox deshabilite un editable se hace de esta forma:

	{this->objeto_js}.procesar_id_del_checkbox = function () {
		if (this.ef('id_del_checkbox').chequeado())
			this.ef('id_del_editable').activar();
		else
			this.ef('id_del_editable').desactivar();
	}

Hay que notar el nombre de la funci�n: "procesar_" seguido por el id del ef. Autom�ticamente el formulario 'escucha' cualquier cambio al checkbox y lo procesa en caso de que suceda. 

Otro escenario es que un combo dispare el submit del objeto, para que ande s�lo resta que los CI sean clases para que los objetos puedan disparar un submit (disparar tambi�n un onSubmit porque el browser no lo manda si se hace a mano). De todas formas el fomulario ser�a as�:

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

El multil�nea tiene como shortcut el procesamiento llamado 'totalizacion' que consiste en sumarizar el valor de cada fila posicion�ndose en un ef determinado desde el administrador (flag 'total' en el ABM de los ef del ML). Si se quisiera simular la totalizaci�n usando la extensi�n (no es necesario pero es f�cil de ver como se hace): 

	{this->objeto_js}.agregar_procesamiento('mi_moneda');
	
Para colgar procesamientos a otras columnas hay que definir nuevas funciones, nuevamente deben empezar con "procesar_":

	//Cuando se procesa el checkbox sumarizar los importes cuya fila est� chequeada (es m�s facil verlo)
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

Tanto en el fomulario y el formulario_ml existen situaciones en donde por ejemplo por extensi�n se cambia el valor de un ef. El procesamiento de ese ef s�lo se produce en el onblur, onclick u onchange y estos eventos no los dispara el browser cuando se modifican por script. Esto se puede tomar como punto a favor para no hacer un refresco gr�fico continuo si por ejemplo se le suma 15 a todos los efs de un ML de 50 l�neas y se dispara un refresco por cada uno... puede tardar mucho (1). Otro motivo es evitar entrar en deadlock (el combo cambia el editable, este cambia el combo y as� siguiendo...), actualmente el orden visual determina la prioridad.

En el caso del formulario existe un s�lo refresco que es el de procesamiento {$this->objeto_js}.refrescar_procesamientos() que recalcula el procesamiento de los efs.

Para el ML existen algunos m�s:
		-refrescar_procesamientos: Recorre todas las columnas que tienen procesamientos y las recalcula
		-refrescar_numeracion_filas: Recorre todas las filas y las vuelve a numerara comenzando desde uno
		-refrescar_deshacer: Actualiza el bot�n deshacer
		-refrescar_seleccion: Resalta la l�nea seleccionada 
		-refrescar_foco: Toma la fila seleccionada y le pone foco al primer ef que se la banque.
		-refrescar_eventos_procesamiento: Toma una fila y le refresca los listeners de procesamiento


FIN
-----------

�Hay cosas que quedan afuera de esta forma de extender?
Estar�a bueno que cualquier extensi�n se vaya subiendo a los test, baja bastante la presi�n arterial!!.


(1) Ahora tambi�n puede ser pesadito, falta hacer pruebas m�s formales, pero ac� en el orden de los cientos de filas empieza a ponerse pesado el refresco.
