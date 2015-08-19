// Class appLauncher
var appLauncher = new function () {
    
    // Metodo que inicializa el lanzador de aplicaciones
    this.init = function (appLauncherDataParam) {
        
        // variable que contiene los datos por defecto del appLauncher
        var appLauncherDataDefault = {
            launcherMaxLineHeight : 100,
        };
        
        // variable que contiene los datos del appLauncher
        var appLauncherData = $.extend({}, appLauncherDataDefault, appLauncherDataParam); // determino los datos del appLauncher
        
        // Variable para activar el scroll vertical
        var scroll = false;
        // determino la cantidad de aplicaciones
        var cant_apps = appLauncherData.data.aplicaciones.length;
        // determino la cantidad de lineas
        var cant_lineas = Math.ceil(cant_apps / 3);
        // variable que contiene el alto del primer contenedor de aplicaciones
        var height_first_set = 0;
        // variable que contiene el alto total del contenedor de aplicaciones
        var height_apps = 0;
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // Armo el HTML del perfil de usuario
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        
        var html_usuario = "";
        html_usuario += "<div id='container_datos_usuario'>";
        html_usuario += "   <div id='datos_usuario_general'>";
        html_usuario += "       <div id='button_datos_usuario'>";
        html_usuario += "           <div id='button_datos_usuario_foto'>";
        html_usuario += "           </div>";
        html_usuario += "       </div>";
        html_usuario += "       <div id='datos_usuario'>";
        html_usuario += "           <div id='perfil_usuario'>";
        html_usuario += "               <div id='perfil_usuario_foto'>";
        html_usuario += "               </div>";
        html_usuario += "               <div id='perfil_usuario_cuenta'>";
        html_usuario += "                   <div id='perfil_usuario_cuenta_nombre'>";
        html_usuario += "                   </div>";
        html_usuario += "                   <div id='perfil_usuario_cuenta_id'>";
        html_usuario += "                   </div>";
        html_usuario += "                   <div id='perfil_usuario_cuenta_botones'>";
        html_usuario += "                       <div id='perfil_usuario_cuenta_perfil'>";
        html_usuario += "                       </div>";
        html_usuario += "                       <div id='perfil_usuario_cuenta_salir'>";
        html_usuario += "                       </div>";
        html_usuario += "                   </div>";
        html_usuario += "               </div>";
        html_usuario += "           </div>";
        html_usuario += "       </div>";
        html_usuario += "   </div>";
        html_usuario += "</div>";
        $(appLauncherData.container).append(html_usuario);
                
        if (appLauncherData.data.usuario_foto != undefined) {
            $(appLauncherData.container + " #button_datos_usuario_foto").html("<img id='perfil_usuario_boton_foto_img' src='" + appLauncherData.data.usuario_foto + "'></i>");
            $(appLauncherData.container + " #perfil_usuario_foto").html("<img id='perfil_usuario_foto_img' src='" + appLauncherData.data.usuario_foto + "'></i> ");
        }
        
        if (appLauncherData.data.usuario_nombre != undefined) {
            $(appLauncherData.container + " #perfil_usuario_cuenta_nombre").html(appLauncherData.data.usuario_nombre);
        }
        
        if (appLauncherData.data.usuario_id != undefined) {
            $(appLauncherData.container + " #button_datos_usuario").append("<div id='perfil_usuario_boton_cuenta_id'>" + appLauncherData.data.usuario_id + "</div>");
            $(appLauncherData.container + " #perfil_usuario_cuenta_id").html(appLauncherData.data.usuario_id);
        }
        
        if (appLauncherData.data.perfil_url != undefined) {
            $(appLauncherData.container + " #perfil_usuario_cuenta_perfil").html("<a id='boton_cuenta' href='"+ appLauncherData.data.perfil_url + "' target='perfil_usuario_" + appLauncherData.data.usuario_id + "'> Mi cuenta  </a>" );
        }
        
        if (appLauncherData.js_salir != undefined) {
            $(appLauncherData.container + " #perfil_usuario_cuenta_salir").html("<a id='boton_salir' href='#'> Salir </a>" );
            $(appLauncherData.container + ' #boton_salir').click(appLauncherData.js_salir);
        }
        
        // Armo la logica del boton de perfil del usuario
        this.setearLogicaBoton(appLauncherData.container + ' #datos_usuario', appLauncherData.container + ' #button_datos_usuario', [appLauncherData.container + ' #app-launcher']);
         
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // Armo el HTML con las aplicaciones
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        
        if (cant_apps > 0) {
            var html_aplicaciones = "";
            html_aplicaciones += "  <div id='container_aplicaciones'>";
            html_aplicaciones += "      <div id='launcher'>";
            html_aplicaciones += "          <div id='button'><i class='fa fa-th fa-2x'></i>";
            html_aplicaciones += "          </div>";
            html_aplicaciones += "          <div id='app-launcher'>";
            html_aplicaciones += "              <div id='app-launcher-container'>";
            html_aplicaciones += "                  <div id='apps'>";
            html_aplicaciones += "                      <ul id='first-set'>";
            html_aplicaciones += "                      </ul>";
            html_aplicaciones += "                  </div>";
            html_aplicaciones += "              </div>";
            html_aplicaciones += "          </div>";
            html_aplicaciones += "      </div>";
            html_aplicaciones += "  </div>";
            $(appLauncherData.container).append(html_aplicaciones);
            
            if (cant_lineas <=3) {
                height_first_set = cant_lineas * appLauncherData.launcherMaxLineHeight;
                height_apps = height_first_set + 57;
            } else {
                height_first_set = 3 * appLauncherData.launcherMaxLineHeight;
                height_apps = height_first_set + 97;

                $(appLauncherData.container + " #apps").append("<a href='#' id='more'>Más</a>");
                $(appLauncherData.container + " #apps").append("<ul id='second-set' class='hide_app_launcher'> </div>");
            }

            // Recorro las aplicaciones y generon los links
            $(appLauncherData.data.aplicaciones).each(function( index, element ) {
                if (element.url != undefined && element.icono_url != undefined && element.etiqueta != undefined && element.title != undefined) {
                    if (index < 9) {
                        $(appLauncherData.container + " #first-set").append("<li> <a class='link_aplicaciones' href='"+ element.url +"' target='aplicacion_"+ index +"' id='aplicacion_"+ index +"'> <div> <img class='fa fa-4x icono_url' src='" + element.icono_url + "' alt='" + element.title + "'> </i> </div> <div> " + element.etiqueta + " </div> </a> </li>");
                    } else {
                        $(appLauncherData.container + " #second-set").append("<li> <a class='link_aplicaciones' href='"+ element.url +"' target='aplicacion_"+ index +"' id='aplicacion_"+ index +"'> <div> <img class='fa fa-4x icono_url' src='" + element.icono_url + "' alt='" + element.title + "'></i> </div> <div> " + element.etiqueta + " </div> </a> </li>");
                    }
                }
            });

            // Setea el maximo alto del contenedor de items
            $(appLauncherData.container + ' #first-set').css({height: height_first_set});
                
            // Setea el maximo alto del contenedor de aplicaciones
            $(appLauncherData.container + ' #apps').css({height: height_apps});

            // Mousewheel event handler to detect whether user has scrolled over the container
            $(appLauncherData.container + ' #apps').bind('mousewheel', function (e) {
                if (e.originalEvent.wheelDelta / 120 > 0) {
                    // Scrolling up
                }
                else {
                    // Scrolling down
                    if (!scroll) {
                        $(appLauncherData.container + " #second-set").show();
                        $(appLauncherData.container + ' #apps').css({height: height_apps}).addClass('overflow');
                        scroll = true;
                        $(this).scrollTop(e.originalEvent.wheelDelta);
                    }
                }
            });

            // Scroll event to detect that scrollbar reached top of the container
            $(appLauncherData.container + ' #apps').scroll(function () {
                var pos = $(this).scrollTop();
                if (pos == 0) {
                    scroll = false;
                    $(appLauncherData.container + ' #apps').css({height: height_apps}).removeClass('overflow');
                    $(appLauncherData.container + " #second-set").hide();
                }
            });

            // Click event handler to show more apps
            $(appLauncherData.container + ' #apps #more').click(function () {
                $(appLauncherData.container + " #second-set").show();
                $(appLauncherData.container + " #apps").animate({scrollTop: $(appLauncherData.container + ' #apps')[0].scrollHeight}).css({height: height_apps}).addClass('overflow');
            }); 

            // Armo la logica del boton de aplicaciones
            this.setearLogicaBoton(appLauncherData.container + ' #app-launcher', appLauncherData.container + ' #button', [appLauncherData.container + ' #datos_usuario']);
        }
        
        // Metodo para ocultar appLauncher cuando se clickea fuera del board
        $(document).click(function () {
            //Hide the launcher if visible
            $(appLauncherData.container + ' #app-launcher').hide();
            $(appLauncherData.container + ' #datos_usuario').hide();
        });

        // Resize event handler to maintain the max-height of the app launcher
        $(window).resize(function () {
            if (cant_apps > 0) {
                $(appLauncherData.container + ' #apps').css({maxHeight: $(window).height() - $(appLauncherData.container + ' #apps').offset().top});
            }
            $(appLauncherData.container + ' #perfil_usuario').css({maxHeight: $(window).height() - $(appLauncherData.container + ' #perfil_usuario').offset().top});
        });
        
    };
    
    this.setearLogicaBoton = function (divBoton, boton, divBotonOcultar) {
        // Prevent hiding on click inside app launcher
        $(divBoton).click(function (event) {
            event.stopPropagation();
        });

        // Click event handler to toggle dropdown
        $(boton).click(function (event) {
            event.stopPropagation();
            $(divBoton).toggle();
            $(divBotonOcultar).each(function( index ) {
                $(divBotonOcultar[index]).hide();
            });
        });

        // Oculto el div inicialmente
        $(divBoton).hide();

    };
    
};
