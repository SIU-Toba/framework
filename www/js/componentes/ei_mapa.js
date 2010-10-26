	ei_mapa.prototype = new ei();
	ei_mapa.prototype.constructor = ei_mapa;

	/**
	 * Constructor
	 */
	function ei_mapa (id, instancia, input_submit)
	{
		this._id = id;
		this._instancia = instancia;
		this._input_submit = input_submit;
		this._extent = null;
		this._ancho = null;;
		this._alto = null;
		this._url = null;
		this._layers = null;
		this._lista_layers_activos = null;

		this.controlador = null;
		this._evento_implicito = null;
		///this._expandido = false;
		this._mapa = null;
		this._ubicacion_controles = 'standardRight';
		this._toolbar = null;
	}

	/**
	 * Funcion para inicializar el componente
	 */
	ei_mapa.prototype.iniciar= function ()
	{
		var metodo_extension = 'evt__mapa__iniciar';
		//Creo el objeto del mapa
		this._mapa = new msMap(document.getElementById('cuerpo__' + this._instancia));
		this._mapa.setCgi(this._url);
		this._mapa.setWidth(this._ancho);
		this._mapa.setHeight(this._alto);
		this._mapa.setFullExtent(this._extent[0], this._extent[1], this._extent[2]);
		this._mapa.setLayers(this._layers);

		//Creo el objeto que representara al toolbar
		this._toolbar = new msToolbar (this._mapa, this._ubicacion_controles, false, this);
		this._mapa.setToolbar(this._toolbar);

		this._toolbar.addTool(new msTool('Full Extent', 'resetear_posicion', _iconFullExtentButton));
		this._toolbar.addTool(new msTool('Zoom In',  'acercarse', _iconZoominButton));
		this._toolbar.addTool(new msTool('Zoom Out', 'alejarse', _iconZoomoutButton));
		this._toolbar.addTool(new msTool('Pan', 'desplazar', _iconPanButton, true)); 
		this._toolbar.addTool(new msTool('Zoom', 'area', _iconZoomboxButton, true));


		if (existe_funcion(this, metodo_extension)) {
			this[metodo_extension]();
		}
		this._toolbar.redraw();
		this._toolbar.activateButtons();
		
		//Inicializo todo el mapa
		this._mapa.init();																	//Llamo especificamente al inicializador
	}

	//------------------------------------------------------------------------------------------------//
	//											 MANEJO PROPIEDADES										  //
	//------------------------------------------------------------------------------------------------//
	ei_mapa.prototype.set_extent = function (xmin, xmax, ymin, ymax)
	{
		this._extent = [xmin, xmax, ymin, ymax];
	}

	ei_mapa.prototype.set_url = function(url_pedido)
	{
		this._url = url_pedido;
	}

	ei_mapa.set_ubicacion_controles = function (ubicacion)
	{
		this._ubicacion_controles = ubicacion;
	}

	ei_mapa.set_ancho_mapa = function (ancho)
	{
		if (isNaN(ancho)) {
			notificacion.agregar('El ancho para el mapa no es correcto', 'error');
			return false;
		}
		this._ancho = ancho;
	}

	ei_mapa.set_alto_mapa = function (alto)
	{
		if (isNaN(alto)) {
			notificacion.agregar('El alto para el mapa no es correcto', 'error');
			return false;
		}
		this._alto = alto;
	}
	//------------------------------------------------------------------------------------------------//
	//											MANEJO DE EVENTOS											//
	//------------------------------------------------------------------------------------------------//
	ei_mapa.prototype.acercarse = function(e)
	{
		this._mapa.setActionZoomIn();
	}

	ei_mapa.prototype.alejarse = function(e)
	{
		this._mapa.setActionZoomOut();
	}

	ei_mapa.prototype.desplazar = function(e)
	{
		this._mapa.setActionPan();
		this._mapa.dragStart(e);
	}

	ei_mapa.prototype.area = function(e)
	{
		this._mapa.setActionZoombox();
		this._mapa.zoomStart(e);
	}

	ei_mapa.prototype.resetear_posicion = function(e)
	{
		this._mapa.fullExtent();
	}

	ei_mapa.set_evento = function(evento)
	{
		//Esto por ahora no hace nada... deberia setear los eventos en el oculto y enviar el form al servidor
	}

	ei_mapa.prototype.get_punto_click = function (evento)
	{
		var punto = [];
		
		punto['X'] = this._mapa.getClick_X(evento);			//Recupero la posicion en el eje X
		punto['Y'] = this._mapa.getClick_Y(evento);			//Recupero la posicion en el eje Y

		return punto;
	}
	//------------------------------------------------------------------------------------------------//
	//											 MANEJO LAYERS													  //
	//------------------------------------------------------------------------------------------------//
	/**
	 * Permite setear la totalidad de los layers que tiene el mapa
	 */
	ei_mapa.prototype.set_layers = function (layers, cambiar_mapa)
	{
			this._layers = layers;
			if (cambiar_mapa == true) {
				this._mapa.setLayers(layers);
			}
	}

	/**
	 * Setea la lista de layers actualmente activos (util para el control de layers unicamente)
	 */
	ei_mapa.prototype.set_layers_activos = function (layers)
	{
		this._lista_layers_activos = layers;
	}

	/**
	 * Permite seleccionar/deseleccionar un layer en particular y refresca
	 * el mapa de acuerdo a la seleccion
	 */
	ei_mapa.prototype.change_layers = function (obj)
	{
		var layer_actual = obj.value;
		var status = obj.checked;
		if (status) {
			this._lista_layers_activos[layer_actual] = 1;
		} else {
			this._lista_layers_activos[layer_actual] = 0;
		}

		var resultado = [];
		for (var layer  in  this._lista_layers_activos) {
			if (this._lista_layers_activos[layer] == 1) {
				resultado.push(layer);
			}
		}

		this.set_layers(resultado.join(' '), true);
		this.render();
	}

	//----------------------------------------------------------------------------------------------///
	/**
	 * Dispara el renderizado del mapa que hara una llamada para
	 * obtener la nueva imagen
	 */
	ei_mapa.prototype.render = function ()
	{
		this._mapa.redraw();
	}


	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	ei_mapa.prototype.submit = function()
	{
		return true;
	}

	ei_mapa.prototype.puede_submit = function()
	{
		return true;
	}

	toba.confirmar_inclusion('componentes/ei_mapa');
