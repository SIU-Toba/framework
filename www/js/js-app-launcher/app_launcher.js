// Class appLauncher
var appLauncher = new function () {
    
    // Metodo que inicializa el lanzador de aplicaciones
    this.init = function (appLauncherDataParam) {        
        // variable que contiene los datos por defecto del appLauncher
        var appLauncherDataDefault = {
            launcherMaxLineHeight : 100
        };
                
        var appLauncherData = $.extend({}, appLauncherDataDefault, appLauncherDataParam);   // variable que contiene los datos del appLauncher
        var $base = $(appLauncherData.container);                                           //Cacheo el acceso de JQuery al DOM
        var scroll = false,                                                                 // Variable para activar el scroll vertical
        cant_apps = appLauncherData.data.aplicaciones.length || 0,                          // determino la cantidad de aplicaciones     
        cant_lineas = Math.ceil(cant_apps / 3),                                             // determino la cantidad de lineas   
        height_first_set = 0,                                                               // variable que contiene el alto del primer contenedor de aplicaciones 
        height_apps = 0;                                                                    // variable que contiene el alto total del contenedor de aplicaciones
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // Armo el HTML del perfil de usuario
        //////////////////////////////////////////////////////////////////////////////////////////////////////
                
        var html_usuario = "<div id='container_datos_usuario'>";
            html_usuario += "   <div id='general_datos_usuario'>";
            html_usuario += "       <div id='activador_datos_usuario'>";
            html_usuario += "           <div id='activador_datos_usuario_foto'/>";
            html_usuario += "       </div>";
            html_usuario += "       <div id='datos_usuario'>";
            html_usuario += "           <div id='usuario'>";
            html_usuario += "               <div id='usuario_foto'/>";
            html_usuario += "               <div id='usuario_cuenta'>";
            html_usuario += "                   <div id='usuario_cuenta_nombre'/>";
            html_usuario += "                   <div id='usuario_cuenta_id'/>";    
            html_usuario += "                   <div id='usuario_cuenta_botones'>";
            html_usuario += "                       <div id='usuario_cuenta_perfil'/>";
            html_usuario += "                       <div id='usuario_preferencias'/>";
            html_usuario += "                       <div id='usuario_cuenta_salir'/>";
            html_usuario += "                   </div>";
            html_usuario += "               </div>";
            html_usuario += "           </div>";
            html_usuario += "       </div>";
            html_usuario += "   </div>";
            html_usuario += "</div>";        
        $base.append(html_usuario);                                                         //Agrego el bodoque de HTML
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////        
        //  Agrego la info de acuerdo a su existencia
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        if (appLauncherData.data.usuario_foto != undefined) { 
            $base
                .find("#activador_datos_usuario_foto")
                    .append( $("<img/>", {
                                id :'usuario_boton_foto_img',
                                src:appLauncherData.data.usuario_foto
                                })
                    )
                .end()
                .find("#usuario_foto")
                    .append( $("<img/>", {
                                id :'usuario_foto_img',
                                src:appLauncherData.data.usuario_foto
                    }));
        }
        
        if (appLauncherData.data.usuario_preferencias != undefined) {
            var url_pref = (appLauncherData.data.usuario_preferencias.url != undefined) ? appLauncherData.data.usuario_preferencias.url : "#";
            var label_pref = (appLauncherData.data.usuario_preferencias.label != undefined) ? appLauncherData.data.usuario_preferencias.label : "Preferencias";

            $base
                .find("#usuario_preferencias")
                .append($('<a/>', {
                            id: 'boton_preferencias',
                            href: url_pref,
                            text: label_pref
                        }));
        }

        if (appLauncherData.data.usuario_nombre != undefined) { 
            $base
                .find("#usuario_cuenta_nombre")
                .text(appLauncherData.data.usuario_nombre);
        }
        
        if (appLauncherData.data.usuario_id != undefined) {
            $base
                .find("#activador_datos_usuario")
                .append( $('<div/>', {
                            id: 'usuario_boton_cuenta_id', 
                            text: appLauncherData.data.usuario_id 
                        }));
        }

        if (appLauncherData.data.cuentas != undefined && appLauncherData.data.cuentas.length > 0) {     //Agrego el combo con cuentas validas
            var index, 
                opcion,
                combo = $("<select/>", {"id": "combo_usuario_cuentas", "name": "combo_usuario_cuentas"})
                                .appendTo($base.find("#usuario_cuenta_id"))
                                .on('change', function() {
                                    var nexo = (appLauncherData.urlAppUsrChg.indexOf('?') == -1) ? '?' :  '&';
                                    window.location.href = appLauncherData.urlAppUsrChg + nexo + appLauncherData.usrChangeParam + '=' + $(this).val();
                                });
            //Agrego las distintas cuentas al combo
            for (index in appLauncherData.data.cuentas) {
        		opcion = $("<option/>", {
                                value : appLauncherData.data.cuentas[index].id_base,
                                text : appLauncherData.data.cuentas[index].descripcion
                            });

        		if (appLauncherData.data.cuenta_actual == appLauncherData.data.cuentas[index].id_base) {
        			opcion.attr('selected', '1');
        		}
                combo.append(opcion);
            }   

        } else if (appLauncherData.data.usuario_id != undefined) {
            $base
                .find("#usuario_cuenta_id")
                .text(appLauncherData.data.usuario_id);
        }

        if (appLauncherData.data.perfil_url != undefined) {
            var destino = (appLauncherData.data.usuario_perfil_url_target != undefined) ? appLauncherData.data.usuario_perfil_url_target : 'usuario_' + appLauncherData.data.usuario_id;
            $base
                .find("#usuario_cuenta_perfil")
                .append($("<a/>",{
                            id: 'boton_cuenta',
                            text: 'Mi Cuenta',
                            href: appLauncherData.data.perfil_url,
                            target: destino
                        }));
        }
        
        if (appLauncherData.js_salir != undefined) {
            var leyenda = (appLauncherData.data.leyenda_btn_salir != undefined) ? appLauncherData.data.leyenda_btn_salir : 'Salir';
            $base
                .find("#usuario_cuenta_salir")
                    .append($("<a/>", { 
                                    id: 'boton_salir',
                                    href: '#',
                                    text: leyenda
                                }).on('click', appLauncherData.js_salir)
                    );
        }
        
        // Armo la logica del boton de perfil del usuario
        this.setearLogicaBoton(appLauncherData.container + ' #datos_usuario', appLauncherData.container + ' #activador_datos_usuario', [appLauncherData.container + ' #app-launcher']);
         
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // Armo el HTML con las aplicaciones
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        
        if (cant_apps > 0) {
            var html_aplicaciones = "  <div id='container_aplicaciones'>";
            html_aplicaciones += "      <div id='launcher'>";
            html_aplicaciones += "          <div id='button'><i class='fa fa-th fa-2x'></i>";
            html_aplicaciones += "          </div>";
            html_aplicaciones += "          <div id='app-launcher'>";
            html_aplicaciones += "              <div id='app-launcher-container'>";
            html_aplicaciones += "                  <div id='apps'>";
            html_aplicaciones += "                      <ul id='first-set' />";
            html_aplicaciones += "                  </div>";
            html_aplicaciones += "              </div>";
            html_aplicaciones += "          </div>";
            html_aplicaciones += "      </div>";
            html_aplicaciones += "  </div>"; 
            $base.append(html_aplicaciones);                                                    //Agrego bodoque para Aplicaciones

            if (cant_lineas <=3) {
                height_apps = cant_lineas * appLauncherData.launcherMaxLineHeight + 40;
                height_first_set = height_apps - 5;
            } else {
                height_first_set = 3 * appLauncherData.launcherMaxLineHeight;
                height_apps = height_first_set + 80;
                $base
                    .find("#apps")
                        .append($("<a/>", {
                            id: 'more',
                            text: 'Más',
                            href: '#'
                        }))
                        .append($("<ul/>", {
                            id: 'second-set',
                            class: 'hide_app_launcher'
                        }));
            }

            // Setea el maximo alto del contenedor de items
            $base
                .find("#first-set")
                    .css('height', height_first_set);
                
            // Recorro las aplicaciones y generon los links
            $(appLauncherData.data.aplicaciones)
                .each(function( index, element ) {
                    if (element.url != undefined && element.icono_url != undefined && element.etiqueta != undefined && element.descripcion != undefined) {
                        var set_contenedor,
                        link_app = $("<a/>", {
                                            class: 'link_aplicaciones',
                                            href: element.url,
                                            target: 'aplicacion_' + index,
                                            id: 'aplicacion_' + index,
                                            title: element.descripcion
                                        }),
                        icon_app = $("<img/>", {
                                            class: 'fa fa-4x icono_url',
                                            src: element.icono_url, 
                                            alt: element.descripcion 
                                        });

                        set_contenedor = (index < 9) ? $base.find("#first-set") : $base.find("#second-set");
                        $("<li/>")                        
                            .appendTo(set_contenedor)
                            .append( link_app
                                        .append($("<div/>").append(icon_app))
                                        .append($("<div/>", {
                                                        text: element.etiqueta
                                                    })
                                        )
                            );
                    }
                });

           
            // Setea el maximo alto del contenedor de aplicaciones
            $base
                .find("#apps")
                .css('height', height_apps)
                .on('mousewheel', function (e) {                        // Mousewheel event handler to detect whether user has scrolled over the container
                        if (e.originalEvent.wheelDelta / 120 <= 0) {    // Scrolling down
                            if (!scroll) {
                                scroll = true;
                                $base
                                    .find('#second-set')
                                    .show();
                                $(this)
                                    .css('height', height_apps)
                                    .addClass('overflow')
                                    .scrollTop(e.originalEvent.wheelDelta);
                            }
                        }
                })
                .on('scroll', function() {                                       // Scroll event to detect that scrollbar reached top of the container
                        var $this = $(this),
                            pos = $this.scrollTop();
                        if (pos == 0) {
                            $this
                                .css('height', height_apps)
                                .removeClass('overflow');
                            $base
                                .find('#second-set')
                                .hide();
                        }
                })
                .end()
                .find('#apps #more')
                .on('click', function() {
                        var $this = $(this);
                        $base
                            .find('#second-set')
                            .show();
                        $this
                            .animate({scrollTop: $this[0].scrollHeight})                //TODO: Revisar el $this[0]
                            .css('height', height_apps)
                            .addClass('overflow');

                });
            // Armo la logica del boton de aplicaciones
            this.setearLogicaBoton(appLauncherData.container + ' #app-launcher', appLauncherData.container + ' #button', [appLauncherData.container + ' #datos_usuario']);
        }
        
        // Metodo para ocultar appLauncher cuando se clickea fuera del board
        $(document).on('click', function() {
            $base
                .find('#app-launcher')
                    .hide()
                .end()
                .find('#datos_usuario')
                    .hide();
        });


        // Resize event handler to maintain the max-height of the app launcher
        $(window).on('resize', function(e){
            var alto = $(this).height();                                    //Refiere a $(window)
            $base
                .find('#usuario')
                .css('maxHeight', function() {
                            return alto - $(this).offset().top;             //Refiere a #usuario
                        });

            if (cant_apps > 0) {
                $base
                    .find('#apps')
                    .css('maxHeight', function() {
                            return alto - $(this).offset().top;
                        });
            }
        });
        
    };
    
    this.setearLogicaBoton = function (divBoton, boton, divsBotonOcultar) {
        // Prevent hiding on click inside app launcher
        var $divBoton = $(divBoton);
        $divBoton.on('click', function (event) {
                    event.stopPropagation();
        }).hide();

        // Click event handler to toggle dropdown
        $(boton).on('click', function (event) {
                    event.stopPropagation();
                    $divBoton.toggle();
                    $(divsBotonOcultar).each(function(index) {
                        $(divsBotonOcultar[index]).hide();
                    });
        });
    };
    
};
