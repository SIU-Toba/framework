<?php

/**
 * Menu CSS basado la YUI
 * @package SalidaGrafica
 */
class toba_menu_yui extends toba_menu
{
	private $id=9000;
	private $arbol;	
	
	function plantilla_css()
	{
		return "menu_yui";
	}
	
	//-----------------------------------------------------------
	
	function preparar_arbol()
	{
		$this->arbol .= "\n<div id='menu_principal' class='yuimenubar' style='position:absolute; top:5px;'>\n";
		$this->arbol .= "\t<div class='bd'>\n";
		$this->arbol .= "\t\t<ul>\n";		
		$this->buscar_raiz();
		$this->arbol .= "\n\t\t</ul>\n\t</div>\n</div>";
	}
	
	function get_padres($nodo)
	{
		$inden = str_repeat("\t",$this->prof *3);
		$clase = 'yuimenuitem';
		$this->arbol .= $inden . "<li class='$clase'>";
		
		if (!$this->items[$nodo]['carpeta']) {
			$opciones = array('validar' => false, 'menu' => true);
			$target = '';
			if ($this->item_abre_popup($nodo)) {
				$opciones['celda_memoria'] = 'paralela';
				$target = " target='_blank' ";
			}
			$vinculo = toba::vinculador()->get_url($this->items[$nodo]['proyecto'], $this->items[$nodo]['item'], array(), $opciones);			
			if (! $this->modo_prueba) {
				$this->arbol .= "<a href='$vinculo' title='{$this->items[$nodo]['nombre']}' $target >{$this->items[$nodo]['nombre']}</a>";
			} else {
				$this->arbol .= $this->items[$nodo]['nombre'];
			}
			$this->hay_algun_item = true;
		} else {
			//Es carpeta
			$this->arbol .= $this->items[$nodo]['nombre'] . "\n";
			$this->arbol .= $inden . "\t<div id='".$this->id++."'  class='yuimenu'>\n";
			$this->arbol .= $inden . "\t\t<div class='bd'>\n";
			$this->arbol .= $inden . "\t\t\t<ul>\n";
			$this->recorrer_hijos($nodo);
			$this->arbol .= $inden . "\t\t\t</ul>\n";
			$this->arbol .= $inden . "\t\t</div>\n";
			$this->arbol .= $inden . "\t</div>\n";
		}
		
		$this->arbol .= $inden . "</li>\n";
	}
	
	//-----------------------------------------------------------

	function mostrar()
	{
		$this->preparar_arbol();
		echo $this->arbol;
		if ($this->hay_algun_item) {
			$consumos = array(
								'yui/yahoo/yahoo-min',
								'yui/dom/dom-min',
								'yui/event/event-min',
								'yui/container/container-min',
								'yui/menu/menu-min'
							);
			toba_js::cargar_consumos_globales($consumos);
			echo toba_js::abrir();
			echo '

            // "load" event handler for the window
            YAHOO.example.onWindowLoad = function(p_oEvent) {

                // "click" event handler for each item in the root MenuBar instance
                function onMenuBarItemClick(p_sType, p_aArguments) {
                
                    this.parent.hasFocus = true;
                    var oActiveItem = this.parent.activeItem;
                
                    // Hide any other submenus that might be visible
                
                    if(oActiveItem && oActiveItem != this) {
                        this.parent.clearActiveItem();
                    }
                
                    // Select and focus the current MenuItem instance
                    this.cfg.setProperty("selected", true);
                    this.focus();
                
                    // Show the submenu for this instance
                    var oSubmenu = this.cfg.getProperty("submenu");
                    if(oSubmenu) {
                        if(oSubmenu.cfg.getProperty("visible")) {
                            oSubmenu.hide();
                        }
                        else {
                            oSubmenu.show();                    
                        }
                    }
                }
    
    
                // "mouseover" event handler for each item in the root MenuBar instance
                function onMenuBarItemMouseOver(p_sType, p_aArguments) {
                    var oActiveItem = this.parent.activeItem;
                
                    // Hide any other submenus that might be visible
                    if(oActiveItem && oActiveItem != this) {
                        this.parent.clearActiveItem();
                    }
                
                
                    // Select and focus the current MenuItem instance
                    this.cfg.setProperty("selected", true);
                    this.focus();
    
                    if(this.parent.hasFocus) {        
                        // Show the submenu for this instance
                        var oSubmenu = this.cfg.getProperty("submenu");
                        if(oSubmenu) {
                            if(
                                oSubmenu.cfg.getProperty("visible") && 
                                oSubmenu != oActiveItem.cfg.getProperty("submenu")
                            ) {
                                oSubmenu.hide();
                            }
                            else {
                                oSubmenu.show();                    
                            }
                        }
                    }
                }

                var oMenuBar = new YAHOO.widget.MenuBar("menu_principal", {width:"80%"});
                oMenuBar.render();


                /*
                    Add a "click" and "mouseover" event handler to each item 
                    in the root MenuBar instnace
                */
                var i = oMenuBar.getItemGroups()[0].length - 1,
                    oMenuBarItem;

                do {
                    oMenuBarItem = oMenuBar.getItem(i);
                    if(oMenuBarItem) {
                        oMenuBarItem.clickEvent.subscribe(
                                onMenuBarItemClick,
                                oMenuBarItem,
                                true
                            );
                        oMenuBarItem.mouseOverEvent.subscribe(
                                onMenuBarItemMouseOver,
                                oMenuBarItem,
                                true
                            );
                    }
                }
                while(i--);


                // "click" event handler for the document
                function onDocumentClick(p_oEvent) {
                
                    var oTarget = YAHOO.util.Event.getTarget(p_oEvent);
                    if(
                        oTarget != oMenuBar.element && 
                        !YAHOO.util.Dom.isAncestor(oMenuBar.element, oTarget)
                    ) {
                        oMenuBar.hasFocus = false;
                        if(oMenuBar.activeItem) {
                            var oSubmenu = oMenuBar.activeItem.cfg.getProperty("submenu");
                            if(oSubmenu) {
                                oSubmenu.hide();
                            }
                            oMenuBar.clearActiveItem();
                            oMenuBar.activeItem.blur();
                        }
                    }
                }

                // Add a "click" handler for the document

                YAHOO.util.Event.addListener(
                        document, 
                        "click", 
                        onDocumentClick
                    );

            }
            // Add a "load" handler for the window
            YAHOO.util.Event.addListener(window, "load", YAHOO.example.onWindowLoad);
			';
			echo toba_js::cerrar();
		}
	}
}

?>