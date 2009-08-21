/*
*   LibMenu 0.2
*   Copyright (C) 2008-2009 Pablo Revel <revelp at gmail.com>
*
*   The JavaScript code in this page is free software: you can
*   redistribute it and/or modify it under the terms of the GNU
*   General Public License (GNU GPL) as published by the Free Software
*   Foundation, either version 3 of the License, or (at your option)
*   any later version.  The code is distributed WITHOUT ANY WARRANTY;
*   without even the implied warranty of MERCHANTABILITY or FITNESS
*   FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.
*
*   As additional permission under GNU GPL version 3 section 7, you
*   may distribute non-source (e.g., minimized or compacted) forms of
*   that code without the copy of the GNU GPL normally required by
*   section 4, provided you include this license notice and a URL
*   through which recipients can access the Corresponding Source.
*/

function Menu()
{
   var objPrototipo = new Object();

   // clases base para los elementos
   objPrototipo.clsClrOpcion = 'm_co';
   objPrototipo.clsClrSubmenu = 'm_ce';
   objPrototipo.clsClrSubmenuActual = 'm_ca';
   objPrototipo.clsClrActualPadre = 'm_cap';

   // clases hover para los elementos
   objPrototipo.clsHvrOpcion = 'm_ho';
   objPrototipo.clsHvrSubmenu = 'm_he';
   objPrototipo.clsHvrSubmenuActual = 'm_ha';

   // ajustes de estilos
   objPrototipo.esHorizontal = true;
   objPrototipo.ajusteAutomatico = true;
   objPrototipo.pantallaAncho = 1024;
   objPrototipo.pantallaAnchoReservado = 100;
   objPrototipo.menuAncho = objPrototipo.pantallaAncho - objPrototipo.pantallaAnchoReservado;
   objPrototipo.desplzHorizInicial = 0;
   objPrototipo.raizAncho = 200;
   objPrototipo.raizAnchoMaximo = 250;
   objPrototipo.elementoAncho = 200;
   objPrototipo.elementoAnchoMaximo = 300;
   objPrototipo.elementoCantColumnas = 5;
   objPrototipo.ajusteAncho = -4;
   objPrototipo.ajusteDesplzIzq = 1;
   objPrototipo.ajusteDesplzDer = 1;
   objPrototipo.ajusteDesplzVertical = 10;
   objPrototipo.ajusteAlturaSubmenu = 2;
   objPrototipo.zIndexInicial = 100;

   // dinamica del menu
   objPrototipo.ocultarMenu = false;
   objPrototipo.demoraEnOcultarMenu = 750;
   objPrototipo.ocultarPrimos = false;
   objPrototipo.ocultarPrimosBase = undefined;
   objPrototipo.demoraEnOcultarPrimos = 750;
   objPrototipo.selects = new Array();
   objPrototipo.divMenu = undefined;

   function inicializarMenu(p_divMenuId)
   {
      menu.divMenu = document.getElementById(p_divMenuId);
      inicializarEventos(menu.divMenu);
      if (menu.esHorizontal)
      {
         if (menu.ajusteAutomatico)
         {
            determinarTamanios(menu.divMenu);
         }
         ajustarEstilosRaiz(menu.divMenu);
      }
   }

   function inicializarEventos(p_elemento)
   {
      if (p_elemento.nodeName == 'DIV')
      {
         if (tieneClase(p_elemento, 'm_o'))
         {
            //p_elemento.onclick = evtEjecutarOperacion;
            p_elemento.onmouseover = evtResetearOcultar;
            p_elemento.onmouseout = evtOcultarMenu;
            agregarClase(p_elemento, menu.clsClrOpcion);
         }
         else if (tieneClase(p_elemento, 'm_e'))
         {
            p_elemento.onclick = evtMostrarSubmenu;
            p_elemento.onmouseover = evtMostrarSubmenu;
            p_elemento.onmouseout = evtOcultarMenu;
            p_elemento.innerHTML = p_elemento.innerHTML + "&nbsp;" + (tieneClase(p_elemento, 'm_r')? "&darr;": "&rarr;");
            agregarClase(p_elemento, menu.clsClrSubmenu);
         }
         else
         {
            for (var i = 0; i < p_elemento.childNodes.length; i++)
            {
               inicializarEventos(p_elemento.childNodes[i]);
            }
         }
      }
   }

   function determinarTamanios(p_elemento)
   {
      // se determina el ancho de la ventana
      if (window.innerWidth != undefined)
      {
         menu.pantallaAncho = window.innerWidth;
      }
      else if (document.body.clientWidth != undefined)
      {
         menu.pantallaAncho = document.body.clientWidth;
      }
      else
      {
         menu.pantallaAncho = 1024;
      }
      menu.pantallaAncho -= objPrototipo.pantallaAnchoReservado;
      // se ajusta el tamanio de cada elemento
      var cantOpcionesRaiz = 0;
      for (var i = 0; i < p_elemento.childNodes.length; i++)
      {
         var opcion = p_elemento.childNodes[i];
         if (opcion.nodeName == 'DIV')
         {
            if ((opcion.nodeName == 'DIV') && (tieneClase(opcion, 'm_r')) &&
                (tieneClase(opcion, 'm_o') || tieneClase(opcion, 'm_s')))
            {
               cantOpcionesRaiz++;
            }
         }
      }
      var raizAnchoPropuesto = (menu.menuAncho < menu.pantallaAncho? menu.menuAncho: menu.pantallaAncho) / cantOpcionesRaiz;
      menu.raizAncho = (raizAnchoPropuesto < menu.raizAnchoMaximo? raizAnchoPropuesto: menu.raizAnchoMaximo);
      var elementoAnchoPropuesto = menu.pantallaAncho / menu.elementoCantColumnas;
      menu.elementoAncho = (elementoAnchoPropuesto < menu.raizAnchoMaximo? elementoAnchoPropuesto: menu.elementoAnchoMaximo);
   }

   function ajustarEstilosRaiz(p_elemento)
   {
      var alturaMaxima = 0;
      for (var i = 0; i < p_elemento.childNodes.length; i++)
      {
         var opcion = p_elemento.childNodes[i];
         if (opcion.nodeName == 'DIV')
         {
            if ((tieneClase(opcion, 'm_o') || tieneClase(opcion, 'm_s')) &&
                (tieneClase(opcion, 'm_r')))
            {
               opcion.style.width = "" + (menu.raizAncho + menu.ajusteAncho) + "px";
               var alturaActual = opcion.clientHeight;
               if (tieneClase(opcion, 'm_s'))
               {
                  for (var j = 0; j < opcion.childNodes.length; j++)
                  {
                     var hijo = opcion.childNodes[j];
                     if (hijo.nodeName == 'DIV')
                     {
                        if (tieneClase(hijo, 'm_e'))
                        {
                           alturaActual = hijo.clientHeight;
                           hijo.style.width = "" + (menu.raizAncho + menu.ajusteAncho) + "px";
                        }
                        else if (tieneClase(hijo, 'm_n'))
                        {
                           hijo.style.width = "" + (menu.raizAncho) + "px";
                        }
                     }
                  }
               }
               if (alturaMaxima < alturaActual)
               {
                  alturaMaxima = alturaActual;
               }
            }
         }
      }
      var dsplzIzq = objPrototipo.desplzHorizInicial;
      for (var i = 0; i < p_elemento.childNodes.length; i++)
      {
         var opcion = p_elemento.childNodes[i];
         if (opcion.nodeName == 'DIV')
         {
            opcion.style.left = "" + dsplzIzq + "px";
            dsplzIzq += menu.raizAncho;
            opcion.style.height = "" + alturaMaxima + "px";
            if (tieneClase(opcion, 'm_o'))
            {
               opcion.innerHTML = "<table class='m_al_medio' cellspacing='0' cellpadding='0'><tr><td class='m_al_medio'>" +
                                     opcion.innerHTML +
                                  "</td></tr></table>";
            }
            else if (tieneClase(opcion, 'm_s'))
            {
               for (var j = 0; j < opcion.childNodes.length; j++)
               {
                  var hijo = opcion.childNodes[j];
                  if (hijo.nodeName == 'DIV')
                  {
                     hijo.style.left = "0px";
                     if (tieneClase(hijo, 'm_e'))
                     {
                        if (tieneClase(hijo, 'm_r'))
                        {
                           hijo.style.top = "0px";
                        }
                        hijo.style.height = "" + alturaMaxima + "px";
                        hijo.innerHTML = "<table class='m_al_medio' cellspacing='0' cellpadding='0'><tr><td class='m_al_medio'>" +
                                              hijo.innerHTML +
                                         "</td></tr></table>";
                     }
                     else if (tieneClase(hijo, 'm_n'))
                     {
                        hijo.style.top = "" + (alturaMaxima + menu.ajusteAlturaSubmenu) + "px";
                        ajustarEstilosSubmenues(hijo, 'derecha', calcularOffsetLeft(hijo), true, menu.zIndexInicial);
                     }
                  }
               }
            }
         }
      }
   }

   function ajustarEstilosSubmenues(p_elemento, p_direccion, p_posicion, p_esHijoRaiz, p_capa)
   {
      if (p_elemento.nodeName == 'DIV')
      {
         var direccion = p_direccion;
         var posicion = p_posicion;
         p_elemento.style.zIndex = p_capa;
         // se determina el ancho
         if (tieneClase(p_elemento, 'm_n') || tieneClase(p_elemento, 'm_s'))
         {
            p_elemento.style.width = "" + menu.elementoAncho + "px";
         }
         else
         {
            p_elemento.style.width = "" + (menu.elementoAncho + menu.ajusteAncho) + "px";
         }
         // se determina el desplazamiento
         if (tieneClase(p_elemento, 'm_n') && ! p_esHijoRaiz)
         {
            if (p_direccion == 'derecha')
            {
               // se sobrepasa el limite visible derecho de la ventana?
               if (menu.pantallaAncho < (posicion + (menu.elementoAncho * 2)))
               {
                  p_elemento.style.left = "-" + (menu.elementoAncho + menu.ajusteDesplzIzq) + "px";
                  direccion = 'izquierda';
               }
               else
               {
                  p_elemento.style.left = "" + (menu.elementoAncho + menu.ajusteDesplzDer) + "px";
               }
            }
            else // izquierda
            {
               // se sobrepasa el limite visible izquierdo de la ventana?
               if (0 > (posicion - menu.elementoAncho))
               {
                  p_elemento.style.left = "" + (menu.elementoAncho + menu.ajusteDesplzDer) + "px";
                  direccion = 'derecha';
               }
               else
               {
                  p_elemento.style.left = "-" + (menu.elementoAncho + menu.ajusteDesplzIzq) + "px";
               }
            }
            posicion = calcularOffsetLeft(p_elemento);
            p_elemento.style.top = "" + menu.ajusteDesplzVertical + "px";
         }
         // se ajustan los submenues
         if (tieneClase(p_elemento, 'm_s') || tieneClase(p_elemento, 'm_n'))
         {
            var esHijoRaiz = (tieneClase(p_elemento, 'm_n') && p_esHijoRaiz);
            for (var i = 0; i < p_elemento.childNodes.length; i++)
            {
               ajustarEstilosSubmenues(p_elemento.childNodes[i], direccion, posicion, esHijoRaiz, p_capa + 1);
            }
         }
      }
   }

   function tieneClase(p_elemento, p_clase)
   {
      if (p_elemento.className != undefined)
      {
         return (p_elemento.className.indexOf(p_clase) != -1);
      }
      else
      {
         return false;
      }
   }

   function agregarClase(p_elemento, p_clase)
   {
      if (! tieneClase(p_elemento, p_clase))
      {
         p_elemento.className = p_elemento.className + ' ' + p_clase;
      }
   }

   function sacarClase(p_elemento, p_clase)
   {
      if (tieneClase(p_elemento, p_clase))
      {
         p_elemento.className = p_elemento.className.replace(new RegExp(p_clase), '');
      }
   }

   function evtEjecutarOperacion()
   {
      resetearOcultar();
      var elemento = undefined;
      if ((arguments[0] != undefined) && (arguments[0].target != undefined))
      {
         elemento = arguments[0].target;
      }
      else
      {
         elemento = window.event.srcElement;
      }
      // simulaci�n de ejecuci�n de la opci�n
      //alert('evtEjecutarOperacion' + "\n" + elemento.innerHTML);
      for (var i = 0; i < elemento.childNodes.length; i++)
      {
         var hijo = elemento.childNodes[i];
         if (hijo.nodeName == 'A')
         {
            document.location.href = hijo.href;
            break;
         }
      }
   }

   function evtMostrarSubmenu()
   {
      resetearOcultar();
      ocultarSelects();
      enterrarCapas();
      // se determina el elemento que dispar� el evento
      var elemento = undefined;
      if ((arguments[0] != undefined) && (arguments[0].target != undefined))
      {
         elemento = arguments[0].target;
      }
      else
      {
         elemento = window.event.srcElement;
      }
      if (elemento.nodeName == 'TD') // caso especial del menu raiz
      {
         elemento = elemento.parentNode.parentNode.parentNode.parentNode;
      }
      sacarClase(elemento, menu.clsClrSubmenu);
      agregarClase(elemento, menu.clsClrSubmenuActual);

      // se busca el div con las opciones
      var divOpciones = elemento.nextSibling;
      if (divOpciones != null)
      {
         while (divOpciones.nodeName != 'DIV')
         {
            divOpciones = divOpciones.nextSibling;
         }
         // se muestran las opciones
         for (var i = 0; i < divOpciones.childNodes.length; i++)
         {
            var opcion = divOpciones.childNodes[i];
            if (opcion.nodeName == 'DIV')
            {
               // es una opcion hoja
               if (tieneClase(opcion, 'm_o'))
               {
                  opcion.style.display = 'block';
                  agregarClase(opcion, menu.clsClrOpcion);
               }
               // es una opcion submenu
               else if (tieneClase(opcion, 'm_s'))
               {
                  // se busca y se muestra la etiqueta del submenu
                  var j = 0;
                  while ((opcion.childNodes[j].nodeName != 'DIV') ||
                         ! tieneClase(opcion.childNodes[j], 'm_e'))
                  {
                     j++;
                  }
                  opcion.childNodes[j].style.display = 'block';
                  agregarClase(opcion.childNodes[j], menu.clsClrSubmenu);
               }
            }
         }
      }
      // se ocultan los submenues hermanos
      ocultarHermanos(elemento);

      if (tieneClase(elemento, 'm_e') && tieneClase(elemento, menu.clsClrActualPadre))
      {
         sacarClase(elemento, menu.clsClrSubmenu);
         sacarClase(elemento, menu.clsClrActualPadre);
         agregarClase(elemento, menu.clsClrSubmenuActual);
      }

      modificarCapaPadre(elemento);
   }

   function evtOcultarMenu()
   {
      var elemento = undefined;
      if ((arguments[0] != undefined) && (arguments[0].target != undefined))
      {
         elemento = arguments[0].target;
      }
      else
      {
         elemento = window.event.srcElement;
      }
      menu.ocultarMenu = true;
      setTimeout("menu.ocultarMenuDemorado()", menu.demoraEnOcultarMenu);
      desmarcarElemento(elemento);
   }

   function evtResetearOcultar()
   {
      resetearOcultar();
      var elemento = undefined;
      if ((arguments[0] != undefined) && (arguments[0].target != undefined))
      {
         elemento = arguments[0].target;
      }
      else
      {
         elemento = window.event.srcElement;
      }
      if (elemento != undefined)
      {
         marcarElemento(elemento);
         if ((elemento.nodeName == 'DIV') && tieneClase(elemento, 'm_o') && tieneClase(elemento, menu.clsClrActualPadre))
         {
            menu.ocultarPrimos = true;
            menu.ocultarPrimosBase = elemento;
            setTimeout("menu.ocultarPrimosDemorado()", menu.demoraEnOcultarPrimos);
         }
      }
   }

   function resetearOcultar()
   {
      menu.ocultarMenu = false;
      menu.ocultarPrimos = false;
      menu.ocultarPrimosBase = undefined;
   }

   function modificarCapaPadre(p_elemento)
   {
      var abuelo = p_elemento.parentNode.parentNode;
      if ((abuelo != undefined) && (abuelo.nodeName == 'DIV') && ! tieneClase(abuelo, 'm_r'))
      {
         if (tieneClase(abuelo, 'm_n'))
         {
            for (var i = 0; i < abuelo.childNodes.length; i++)
            {
               var hijo = abuelo.childNodes[i];
               if (hijo.nodeName == 'DIV')
               {
                  if (tieneClase(hijo, 'm_o'))
                  {
                     hijo.style.zIndex = menu.zIndexInicial;
                     agregarClase(hijo, menu.clsClrActualPadre);
                  }
                  else if (tieneClase(hijo, 'm_s'))
                  {
                     var j = 0;
                     while ((j < hijo.childNodes.length) &&
                            ((hijo.childNodes[j].nodeName != 'DIV') || ! tieneClase(hijo.childNodes[j], 'm_e')))
                     {
                        j++;
                     }
                     if ((j < hijo.childNodes.length) && (p_elemento != hijo.childNodes[j]))
                     {
                        hijo.childNodes[j].style.zIndex = menu.zIndexInicial;
                        agregarClase(hijo.childNodes[j], menu.clsClrActualPadre);
                     }
                  }
               }
            }
         }
      }
   }

   function ocultarHermanos(p_elemento)
   {
      var abuelo = p_elemento.parentNode.parentNode;
      for (var i = 0; i < abuelo.childNodes.length; i++)
      {
         var tio = abuelo.childNodes[i];
         if ((tio.nodeName == 'DIV') && tieneClase(tio, 'm_s') && (tio != p_elemento.parentNode))
         {
            // se busca la etiqueta
            var j = 0;
            while ((j < tio.childNodes.length) &&
                   ((tio.childNodes[j].nodeName != 'DIV') || ! tieneClase(tio.childNodes[j], 'm_e')))
            {
               j++;
            }
            sacarClase(tio.childNodes[j], menu.clsClrSubmenuActual);
            agregarClase(tio.childNodes[j], menu.clsClrSubmenu);
            if (! tieneClase(tio.childNodes[j], 'm_r') && (tio.childNodes[j] != p_elemento))
            {
               //agregarClase(tio.childNodes[j], menu.clsClrActualPadre);
            }
            else
            {
               sacarClase(tio.childNodes[j], menu.clsClrActualPadre);
            }
            while ((j < tio.childNodes.length) &&
                   ((tio.childNodes[j].nodeName != 'DIV') || ! tieneClase(tio.childNodes[j], 'm_n')))
            {
               j++;
            }
            ocultarOpciones(tio.childNodes[j]);
         }
      }
   }

   function ocultarPrimosDemorado()
   {
      if (menu.ocultarPrimos)
      {
         var elemento = menu.ocultarPrimosBase;
         menu.ocultarPrimos = false;
         menu.ocultarPrimosBase = undefined;
         if ((elemento != undefined) && (elemento.nodeName == 'DIV') && tieneClase(elemento, 'm_o'))
         {
            for (var i = 0; i < elemento.parentNode.childNodes.length; i++)
            {
               var hijo = elemento.parentNode.childNodes[i];
               if (hijo.nodeName == 'DIV')
               {
                  if (tieneClase(hijo, 'm_s') && (hijo != elemento) && ! tieneClase(hijo, 'm_r'))
                  {
                     for (var j = 0; j < hijo.childNodes.length; j++)
                     {
                        var subElemento = hijo.childNodes[j];
                        if (tieneClase(subElemento, 'm_n'))
                        {
                           ocultarOpciones(subElemento);
                        }
                        else if (tieneClase(subElemento, 'm_e'))
                        {
                           sacarClase(subElemento, menu.clsClrSubmenuActual);
                           sacarClase(subElemento, menu.clsClrActualPadre);
                           agregarClase(subElemento, menu.clsClrSubmenu);
                        }
                     }
                  }
                  else if (tieneClase(hijo, 'm_o') && ! tieneClase(hijo, 'm_r'))
                  {
                     sacarClase(hijo, menu.clsClrActualPadre);
                  }
               }
            }
         }
      }
   }

   function ocultarMenuDemorado()
   {
      if (menu.ocultarMenu)
      {
         ocultarOpciones(menu.divMenu);
         mostrarSelects();
         desenterrarCapas();
      }
   }

   function ocultarOpciones(p_elemento)
   {
      if (p_elemento != undefined)
      {
         for (var i = 0; i < p_elemento.childNodes.length; i++)
         {
            var hijo = p_elemento.childNodes[i];
            if (hijo.nodeName == 'DIV')
            {
               // es una etiqueta de submenu
               if (tieneClase(hijo, 'm_e'))
               {
                  if (! tieneClase(hijo, 'm_r'))
                  {
                     hijo.style.display = 'none';
                  }
                  sacarClase(hijo, menu.clsClrSubmenuActual);
                  agregarClase(hijo, menu.clsClrSubmenu);
               }
               // es una opcion hoja
               else if (tieneClase(hijo, 'm_o') && ! tieneClase(hijo, 'm_r'))
               {
                  hijo.style.display = 'none';
               }
               // es un submenu
               else
               {
                  ocultarOpciones(hijo);
               }
            }
            sacarClase(hijo, menu.clsClrActualPadre);
         }
      }
   }

   function enterrarCapas()
   {
      if (window.innerWidth == undefined) //IE
      {
         for (var i = 0; i < document.body.childNodes.length; i++)
         {
            var hijo = document.body.childNodes[i];
            if ((hijo.nodeName == 'DIV') && (hijo.id == 'id_menu'))
            {
               continue;
            }
            if (hijo.style != undefined)
            {
               var hijoCapa = (hijo.style.zIndex != undefined? hijo.style.zIndex: 0);
               hijo.style.zIndex = '' + (hijoCapa - 100);
            }
         }
      }
   }

   function desenterrarCapas()
   {
      if (window.innerWidth == undefined) //IE
      {
         for (var i = 0; i < document.body.childNodes.length; i++)
         {
            var hijo = document.body.childNodes[i];
            if ((hijo.nodeName == 'DIV') && (hijo.id == 'id_menu'))
            {
               continue;
            }
            if (hijo.style != undefined)
            {
               var hijoCapa = (hijo.style.zIndex != undefined? hijo.style.zIndex: -100);
               hijo.style.zIndex = '' + (100 + hijoCapa);
            }
         }
      }
   }

   function mostrarSelects()
   {
      if (selects != undefined)
      {
         for (var i = 0; i < selects.length; i++)
         {
            if (selects[i] != undefined)
            {
               selects[i].style.visibility = 'visible';
            }
         }
      }
   }

   function ocultarSelects()
   {
      selects = document.getElementsByTagName('select');
      for (var i = 0; i < selects.length; i++)
      {
         if (selects[i] != undefined)
         {
            selects[i].style.visibility = 'hidden';
         }
      }
   }

   function marcarElemento(p_elemento)
   {
      if (p_elemento.nodeName == 'DIV')
      {
         if (tieneClase(p_elemento, 'm_e'))
         {
            if (tieneClase(p_elemento, menu.clsClrSubmenuActual))
            {
               agregarClase(p_elemento, menu.clsHvrSubmenuActual);
            }
            else
            {
               agregarClase(p_elemento, menu.clsHvrSubmenu);
            }
         }
         else if (tieneClase(p_elemento, 'm_o'))
         {
            agregarClase(p_elemento, menu.clsHvrOpcion);
         }
      }
   }

   function desmarcarElemento(p_elemento)
   {
      if (p_elemento.nodeName == 'DIV')
      {
         if (tieneClase(p_elemento, 'm_e'))
         {
            if (tieneClase(p_elemento, menu.clsClrSubmenuActual))
            {
               sacarClase(p_elemento, menu.clsHvrSubmenuActual);
            }
            else
            {
               sacarClase(p_elemento, menu.clsHvrSubmenu);
            }
         }
         else if (tieneClase(p_elemento, 'm_o'))
         {
            sacarClase(p_elemento, menu.clsHvrOpcion);
         }
      }
   }

   function calcularOffsetLeft(elemento)
   {
      return calcularOffset(elemento, "offsetLeft");
   }

   function calcularOffsetTop(elemento)
   {
      return calcularOffset(elemento, "offsetTop");
   }

   function calcularOffset(elemento, atributo)
   {
      var offset = 0;
      while (elemento != undefined)
      {
         offset += elemento[atributo];
         elemento = elemento.offsetParent;
      }
      return offset;
   }

   objPrototipo.inicializarMenu = inicializarMenu;
   objPrototipo.ocultarPrimosDemorado = ocultarPrimosDemorado;
   objPrototipo.ocultarMenuDemorado = ocultarMenuDemorado;

   return objPrototipo;
}