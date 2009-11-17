<?xml version="1.0" encoding="UTF-8"?>
<!--
  Copyright 2008 WSO2, Inc. http://www.wso2.org

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.

  Created by: Jonathan Marsh <jonathan@wso2.com>

-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:import href="tryit.xslt"/>
    
    <xsl:output method="html" indent="yes" omit-xml-declaration="no"/>

    <!-- This stylesheet only supports a single service at a time.
         If no service name is specified in this parameter, the first one is used.  -->
    <xsl:param name="service" select="services/service[1]/@name"/>

    <!-- Choose a gadget host environment -->
    <xsl:param name="host" select="'google'"/>
    <xsl:param name="xslt-location" select="'../xslt/formatxml.xslt'"/>

    <!-- Paths to external resources can be specified here. -->
    <xsl:variable name="image-path" select="'http://mooshup.com/images/'"/>

    <xsl:template match="/">
        <xsl:apply-templates select="services/service[@name=$service][1]"/>
    </xsl:template>
    
    <xsl:template match="service">
        <Module>
            <ModulePrefs title="Try the {$original-service-name} service" width="320" height="240" scrolling="true">
                <Require feature="tabs"/>
            </ModulePrefs>
            <Content type="html">
                <xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text>
                <xsl:call-template name="css"/>
                <script type="text/javascript" src="{$wsrequest-location}"></script>
                <xsl:text>
</xsl:text>
                <!-- Calculate the source of the stub, including whether it's e4x or not -->
                <xsl:variable name="e4x-param">
                    <xsl:if test="$e4x">; e4x=1</xsl:if>
                </xsl:variable>
                <xsl:variable name="src">
                    <xsl:choose>
                        <xsl:when test="$stub-location"><xsl:value-of select="$stub-location"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="$original-service-name"/>?stub</xsl:otherwise>
                    </xsl:choose>
                    <xsl:if test="$e4x">&amp;lang=e4x</xsl:if>
                </xsl:variable>
                <xsl:text>
</xsl:text>
                <script type="text/javascript{$e4x-param}" src="{$src}"></script>
                <xsl:text>
</xsl:text>
                <script type="text/javascript">
<xsl:call-template name="browser-compatibility"/>
        
        _IG_RegisterOnloadHandler(init);
        
        <xsl:variable name="name-of-first-operation"><!-- operation name -->
            <xsl:call-template name="xml-name-to-javascript-name">
                <xsl:with-param name="name" select="operations/operation[1]/@name"/>
            </xsl:call-template>
        </xsl:variable>var tabs = new _IG_Tabs(__MODULE_ID__, "<xsl:value-of select="$name-of-first-operation"/>");
        /*
        *  init: called during onload.  Dynamically resets or restores the page as needed.
        */
        function init() {

            preloadFormatxml();

            <xsl:for-each select="operations/operation">
                <xsl:variable name="name"><!-- operation name -->
                    <xsl:call-template name="xml-name-to-javascript-name">
                        <xsl:with-param name="name" select="@name"/>
                    </xsl:call-template>
                </xsl:variable>tabs.addTab("<xsl:value-of select="$name"/>", "params_<xsl:value-of select="$name"/>");
            </xsl:for-each>
         }
                    
<xsl:call-template name="do-operation-functions"/>
<xsl:call-template name="logging-functions"/>
<xsl:call-template name="serialization-functions"/>
<xsl:call-template name="form-behavior-functions"/>
                </script>
                <div id="body">
                    <!--<xsl:if test="documentation/node()">
                        <div class="documentation">
                        <xsl:copy-of select="documentation/node()"/>
                        </div>
                        </xsl:if>-->
                    <!--<div id="endpoint">
                        <div id="endpoint-collapsed">
                        <div class="content">Using endpoint <span id="endpoint-name"></span></div>
                        <div class="content" style="text-align:right; margin-right:2em; font-size:8pt;">(<a href="#" onclick="toggleconfig('endpoint-collapsed', 'endpoint-expanded')">expand</a> to change...)</div>
                        <div class="bottom">
                        <span class="right-corner"><a href="#"><img src="{$image-path}expand.gif" onclick="toggleconfig('endpoint-collapsed', 'endpoint-expanded')" title="Show endpoint options"/></a></span>
                        </div>
                        </div>
                        <div id="endpoint-expanded" style="display:none">
                        <div class="content">Choose endpoint:
                        <ul>
                        <li>Select an endpoint: <select id="endpointSelect" onchange="selectEndpoint()" style="border: 1px solid #CCCCCC;">
                        <xsl:for-each select="/services/service[@name=$service]">
                        <xsl:sort select="@type = 'SOAP12'" order="descending"/>
                        <xsl:sort select="@type = 'SOAP11'" order="descending"/>
                        <xsl:sort select="@address" order="ascending"/>
                        <option value="{@endpoint}"><xsl:value-of select="@endpoint"/></option>
                        </xsl:for-each>
                        </select>
                        </li>
                        <li>Change the address for the selected endpoint:
                        <input type="text" id="address" value="{@address}" onchange="addressChange()" style="border: 1px solid #CCCCCC;"/>
                        <span id="xssWarning">Warning! Access to a service in a different domain may be prohibited by security features in your browser.</span>
                        </li>
                        <li id="alternate-bullet">Try an alternate <a id="alternate-tryit" href="https://">https</a> endpoint.</li>
                        </ul>
                        </div>
                        <div class="bottom">
                        <span class="right-corner"><a href="#"><img src="{$image-path}collapse.gif" onclick="toggleconfig('endpoint-expanded', 'endpoint-collapsed')" title="Show endpoint options"/></a></span>
                        </div>
                        </div>
                        </div>
                    -->
                    <!--<xsl:if test="operations/operation/binding-details[policy/@type = 'UTOverTransport']">
                        <div id="credentials">
                        This service requires credentials:
                        <div>username: <input type="text" id="username" size="20"></input> password: <input type="password" id="password" size="20"></input></div>
                        </div>
                        </xsl:if>-->
                    <div id="middle">
                        <xsl:call-template name="parameter-view"/>
                    </div>
                    <!-- footer -->
                    <xsl:if test="$enable-footer='true'">
                        <div id="footer">
                            <p>© 2007-2008 <a href="http://wso2.com/">WSO2 Inc.</a></p>
                        </div>
                    </xsl:if>
                </div>
                <xsl:text disable-output-escaping="yes">]]&gt;</xsl:text>
            </Content>
        </Module>
    </xsl:template>
    
    <xsl:template name="parameter-view">
         <xsl:for-each select="operations/operation">
            <xsl:variable name="name">
                <xsl:call-template name="xml-name-to-javascript-name">
                    <xsl:with-param name="name" select="@name"/>
                </xsl:call-template>
            </xsl:variable>
            <div class="params" id="params_{$name}">
                <table class="ops">
                    <tr>
                        <td colspan="2">
                            <xsl:if test="documentation/node()">
                                <div class="operationDocumentation">
                                    <xsl:copy-of select="documentation/node()"/>
                                </div>
                                </xsl:if>
                            <div id="resturl_{$name}" class="operationDocumentation"> </div>
                        </td>
                    </tr>
                    <xsl:for-each select="signature/params/param">
                        <tr>
                            <xsl:choose>
                                <!-- this parameter represents expandable parameters -->
                                <xsl:when test="@token = '#any'">
                                    <td class="label"><div>(additional parameters)</div></td>
                                    <td class="param">
                                        <input type="text" id="input_{$name}_additionalParameters" class="emptyfield" value="xs:anyType" onkeyup="showRestTemplate()" onfocus="prepareInput(event)" onblur="restoreInput(event,'(xs:anyType)')" />
                                        <!-- TODO expandable fields of additional parameters -->
                                    </td>
                                </xsl:when>

                                <!-- this parameter represents a boolean (checkbox) -->
                                <xsl:when test="@type = 'boolean'">
                                    <td class="label">
                                        <xsl:value-of select="@name"/>
                                        <xsl:if test="@minOccurs &lt; 1 or @maxOccurs &gt; 1 or @maxOccurs = 'unbounded'"><sub>(<xsl:value-of select="@minOccurs"/>..<xsl:choose><xsl:when test="@maxOccurs = 'unbounded'">*</xsl:when><xsl:otherwise><xsl:value-of select="@maxOccurs"/></xsl:otherwise></xsl:choose>)</sub></xsl:if></td>
                                    <td class="param">
                                        <div id="arrayparams_{$name}_{@name}">
                                            <!-- first child is a hidden template for cloning additional array items -->
                                            <div style="display:none">
                                                <input type="checkbox" id="input_{$name}_{@name}_" title="An [xs:boolean] value representing {@name}" onchange="showRestTemplate()"/><span class="typeannotation"> (xs:boolean)</span>
                                            </div>
                                            <div>
                                                <input type="checkbox" id="input_{$name}_{@name}_0" title="An [xs:boolean] value representing {@name}" onchange="showRestTemplate()"/><span class="typeannotation"> (xs:boolean)</span>
                                            </div>
                                        </div>
                                        <xsl:if test="@maxOccurs &gt; 1 or @maxOccurs = 'unbounded'">
                                            <input type="button" value="Add {@name}" onclick="addArrayItem(event)"></input>
                                            <input type="button" value="Remove {@name}" onclick="removeArrayItem(event)" disabled="disabled"></input>
                                        </xsl:if>
                                    </td>
                                </xsl:when>

                                <!-- this parameter represents a QName (separate namespace and QName fields) -->
                                <xsl:when test="@type = 'QName'">
                                    <td class="label">
                                        <xsl:value-of select="@name"/>
                                        <xsl:if test="@minOccurs &lt; 1 or @maxOccurs &gt; 1 or @maxOccurs = 'unbounded'"><sub>(<xsl:value-of select="@minOccurs"/>..<xsl:choose><xsl:when test="@maxOccurs = 'unbounded'">*</xsl:when><xsl:otherwise><xsl:value-of select="@maxOccurs"/></xsl:otherwise></xsl:choose>)</sub></xsl:if></td>
                                    <td class="param">
                                        <div id="arrayparams_{$name}_{@name}">
                                            <!-- first child is a hidden template for cloning additional array items -->
                                            <div style="display:none">
                                                <textarea id="input_{$name}_{@name}_ns_" class="emptyfield" onfocus="prepareInput(event)" onkeyup="showRestTemplate()" onblur="restoreInput(event,'(namespace URI)')" title="The [namespace URI] value corresponding to the xs:QName">(namespace URI)</textarea>
                                                <xsl:text> </xsl:text>
                                                <textarea id="input_{$name}_{@name}_" class="emptyfield" onfocus="prepareInput(event)" onkeyup="showRestTemplate()" onblur="restoreInput(event,'(xs:QName)')" title="An [xs:QName] value representing prefix and localName of {@name}">(xs:QName)</textarea>
                                            </div>
                                            <div>
                                                <textarea id="input_{$name}_{@name}_ns_0" class="emptyfield" onfocus="prepareInput(event)" onkeyup="showRestTemplate()" onblur="restoreInput(event,'(namespace URI)')" title="The [namespace URI] value corresponding to the xs:QName">(namespace URI)</textarea>
                                                <xsl:text> </xsl:text>
                                                <textarea id="input_{$name}_{@name}_0" class="emptyfield" onfocus="prepareInput(event)" onkeyup="showRestTemplate()" onblur="restoreInput(event,'(xs:QName)')" title="An [xs:QName] value representing prefix and localName of {@name}">(xs:QName)</textarea>
                                            </div>
                                        </div>
                                        <xsl:if test="@maxOccurs &gt; 1 or @maxOccurs = 'unbounded'">
                                            <input type="button" value="Add {@name}" onclick="addArrayItem(event)"></input>
                                            <input type="button" value="Remove {@name}" onclick="removeArrayItem(event)" disabled="disabled"></input>
                                        </xsl:if>
                                    </td>
                                </xsl:when>

                                <!-- this parameter represents an enumeration (<select>) -->
                                <xsl:when test="enumeration">
                                    <td class="label">
                                        <xsl:value-of select="@name"/>
                                    </td>
                                    <td class="param">
                                        <select id="input_{$name}_{@name}_0"  onchange="showRestTemplate()">
                                            <xsl:for-each select="enumeration">
                                                <option value="{@value}"><xsl:value-of select="@value"/></option>
                                            </xsl:for-each>
                                        </select>
                                    </td>
                                </xsl:when>

                                <!-- this parameter represents a type exposed as a <textarea> -->
                                <xsl:otherwise>
                                    <xsl:variable name="prefix">
                                        <xsl:if test="@type-namespace = 'http://www.w3.org/2001/XMLSchema'">xs:</xsl:if>
                                    </xsl:variable>
                                    <xsl:variable name="restriction">
                                        <xsl:if test="@restriction-of">
                                            <xsl:if test="@restriction-namespace = 'http://www.w3.org/2001/XMLSchema'">xs:</xsl:if>
                                            <xsl:value-of select="@restriction-of"/>
                                            <xsl:text> restriction</xsl:text>                                            
                                        </xsl:if>
                                    </xsl:variable>
                                    <td class="label"><div>
                                        <xsl:value-of select="@name"/>
                                        <xsl:if test="@minOccurs &lt; 1 or @maxOccurs &gt; 1 or @maxOccurs = 'unbounded'"><sub>(<xsl:value-of select="@minOccurs"/>..<xsl:choose><xsl:when test="@maxOccurs = 'unbounded'">*</xsl:when><xsl:otherwise><xsl:value-of select="@maxOccurs"/></xsl:otherwise></xsl:choose>)</sub></xsl:if></div></td>
                                    <td class="param">
                                        <div id="arrayparams_{$name}_{@name}">
                                            <!-- first child is a hidden template for cloning additional array items -->
                                            <div style="display:none">
                                                <textarea id="input_{$name}_{@name}_" class="emptyfield" onfocus="prepareInput(event)" onkeyup="showRestTemplate()" onblur="restoreInput(event,'({$prefix}{@type}{$restriction})')" title="A [{$prefix}{@type}{$restriction}] value representing the {@name}">(<xsl:value-of select="$prefix"/><xsl:value-of select="@type"/><xsl:value-of select="$restriction"/>)</textarea>
                                                <img src="{$image-path}expand.gif" class="cornerExpand" onclick="expand(event)" title="Increase typing space"/>
                                            </div>
                                            <div>
                                                <textarea id="input_{$name}_{@name}_0" class="emptyfield" onfocus="prepareInput(event)" onkeyup="showRestTemplate()" onblur="restoreInput(event,'({$prefix}{@type}{$restriction})')" title="A [{$prefix}{@type}{$restriction}] value representing the {@name}">(<xsl:value-of select="$prefix"/><xsl:value-of select="@type"/><xsl:value-of select="$restriction"/>)</textarea>
                                                <img src="{$image-path}expand.gif" class="cornerExpand" onclick="expand(event)" title="Increase typing space"/>
                                            </div>
                                        </div>
                                        <xsl:if test="@maxOccurs &gt; 1 or @maxOccurs = 'unbounded'">
                                            <input type="button" value="Add {@name}" onclick="addArrayItem(event)"></input>
                                            <input type="button" value="Remove {@name}" onclick="removeArrayItem(event)" disabled="disabled"></input>
                                        </xsl:if>
                                    </td>
                                </xsl:otherwise>
                            </xsl:choose>
                        </tr>
                    </xsl:for-each>
                    <tr>
                         <td style="padding-top: 1em">
                             <input type="button" id="button_{$name}" value="{$name} >>" onclick="do_{$name}()" title="Invoke the {$name} operation"></input>
                         </td>
                         <td style="padding-top: 1em">
                             <div id="console_{$name}" class="output">

                             </div>
                         </td>
                     </tr>
                 </table>
            </div>
        </xsl:for-each>
    </xsl:template>    

    <!-- template for inserting CSS -->
    <xsl:template name="css">
        <!-- css is embedded rather than linked so that the $image-path can be altered dynamically -->
        <style type="text/css">
            #body {
                margin: 0px;
                padding: 0px;
                font-family: "Lucida Grande","Lucida Sans","Microsoft Sans Serif","Lucida Sans Unicode",verdana,sans-serif,"trebuchet ms";
                font-size: 8pt;
            }

            p { }
            td { }
            a:link { }
            a:visited { }
            a:hover { }
            a:active { }

            a img {
                border: 0px;
            }

            /* header styles */
            div#header {
                height: 70px;
                background-image: url(<xsl:value-of select="$image-path"/>gradient-rule-wide.gif);
                background-repeat: no-repeat;
                background-color: #7e8e35;
                background-position: bottom left;
                color:white;
            }
            div#header h1 {
                margin: 0px 0px 0px 0px;
                padding: 20px 0px 0px 40px;
                font-size: 18pt;
                font-weight: normal;
            }
            a#mashupPageLink {
                font-weight:bold;
            }
            a#mashupPageLink:link { color:white }
            a#mashupPageLink:visited { color:white }
            a#mashupPageLink:hover { color: #f47a20}
            a#mashupPageLink:active { color: #f47a20}


            /* body styles */
            div.documentation {
                padding-left: 40px;
                padding-top: 10px;
                padding-bottom: 20px;
                width: 90%;
            }
            /* end point styles */
            div#endpoint {
                margin: 0em 40px 0em 40px;
                padding-top:1em;
            }
            /* end point collapsed styles */
            div#endpoint div#endpoint-collapsed {
                border-bottom: solid 1px #999;
                border-right: solid 1px #999;
                width: 400px;
            }
            div#endpoint div#endpoint-expanded {
                border-bottom: solid 1px #999;
                border-right: solid 1px #999;
                width: 450px;
            }

            span#endpoint-name {
                font-weight:bold;
            }

            div#endpoint-collapsed div.bottom .right-corner a img,
            div#endpoint-expanded div.bottom .right-corner a img {
                border: 0px;
                vertical-align: bottom;
            }
            div#endpoint-collapsed div.bottom,
            div#endpoint-expanded div.bottom {
                height: 16px;
                text-align:right;
                padding: 0px;
                margin: 0px;
            }
            div#endpoint-collapsed div.bottom .right-corner,
            div#endpoint-expanded div.bottom .right-corner {
                width: 16px;
                border: 0px;
                margin-bottom: 0px;
                position:relative;
                top:6px;
                left:6px;
            }
            div#endpoint-expanded #address {
                width:28em
            }
            div#endpoint-expanded #xssWarning {
                color:red;
                font-weight:bold;
                font-size: 8pt;
                display:none;
            }
            /* credential styles */
            div#credentials {
                margin: 0em 40px 0em 40px;
                padding-top:1em;
            }
            div#credentials div {
                margin-left: 3em;
            }
            
            /* middle styles */
            div#middle {
                margin-left: 35px;
                margin-top: 15px;
                margin-right: 50px;
                margin-right: 20px;
                margin-bottom: 0px;
            }
            /* tabs styles */
            div#middle table#middle-content {
                padding: 0px;
                margin: 0px;
                border-collapse: collapse;
                width: 93%;
            }
            div#middle table#middle-content tr td {
                padding: 0px;
                vertical-align:top;
            }
            div#middle table#middle-content tr td.left-tabs {
                background-image: url(<xsl:value-of select="$image-path"/>left-tabs-bg.gif);
                background-position: top right;
                background-repeat: repeat-y;
                background-attachment: scroll;
                width: 5%;
                vertical-align: top;
            }
            div#middle table#middle-content tr td.bottom-left {
                background-image: url(<xsl:value-of select="$image-path"/>bottom-left.gif);
                background-position: top right;
                background-repeat: no-repeat;
                background-attachment: scroll;
                height: 16px;
                vertical-align: top;
            }
            div#middle table#middle-content tr td.bottom {
                background-image: url(<xsl:value-of select="$image-path"/>bottom.gif);
                background-position: top left;
                background-repeat: repeat-x;
                background-attachment: scroll;
                height: 16px;
                text-align: right;
                vertical-align: top;
            }

            div#middle table#operations {
                padding: 0px;
                margin: 0px;
                border-collapse: collapse;
            }
            div#middle table#operations tr td {
                vertical-align: top;
            }
            div#middle table#operations tr.operation-top td.operation-left {
                background-position: left top;
                background-repeat: no-repeat;
                background-attachment: scroll;
                height: 1px;
            }
            div#middle table#operations tr.operation-top td.operation-right {
                width: 26px;
                background-image: url(<xsl:value-of select="$image-path"/>operation-top-right.gif);
                background-position: left top;
                background-repeat: repeat-x;
                background-attachment: scroll;
                height: 1px;
            }

            div#middle table#operations tr.operation-selected td.operation-left {
                background-image: url(<xsl:value-of select="$image-path"/>operation-selected-bg.gif);
                background-position: left bottom;
                background-repeat: no-repeat;
                background-attachment: scroll;
                padding-bottom: 10px;
                padding-left: 15px;
                margin: 0px;
                padding-top: 5px;
            }
            div#middle table#operations tr.operation-selected td.operation-left a {
                color: #666;
                font-weight: bold;
                text-decoration: none;
                cursor: text;
                font-size: 10pt;
            }
            div#middle table#operations tr.operation-selected td.operation-right {
                width: 26px;
                background-image: url(<xsl:value-of select="$image-path"/>operation-selected-bg-right.gif);
                background-position: left bottom;
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-color: #fff;
            }

            div#middle table#operations tr.operation td.operation-left {
                background-image: url(<xsl:value-of select="$image-path"/>operations-bg.gif);
                background-position: left bottom;
                background-repeat: no-repeat;
                background-attachment: scroll;
                padding-bottom: 10px;
                padding-left: 15px;
                margin: 0px;
                padding-top: 5px;
                font-size: 10pt;
            }
            div#middle table#operations tr.operation td.operation-left a{
                color: #000;
                font-size: 10pt;
                font-weight: bold;
                text-decoration: none;
            }
            div#middle table#operations tr.operation td.operation-left a:hover{
                font-size: 10pt;
                text-decoration: underline;
            }
            div#middle table#operations tr.operation td.operation-left a:visited {
                font-size: 10pt;
                color: #894f7b;
            }
            div#middle table#operations tr.operation td.operation-right {
                width: 26px;
                background-image: url(<xsl:value-of select="$image-path"/>operations-bg-right.gif);
                background-position: left bottom;
                background-repeat: no-repeat;
                background-attachment: scroll;
            }

            div#middle table#content-table {
                padding: 0px;
                margin: 0px;
                border-collapse: collapse;
            }
            div#middle table#content-table tr td.content {
                padding: 0px 10px 10px 10px;
            }
            div#middle table#content-table tr td.content-top {
                background-image: url(<xsl:value-of select="$image-path"/>content-top.gif);
                background-position: left top;
                background-repeat: repeat-x;
                background-attachment: scroll;
                height: 21px;
            }

            div#middle table#content-table tr td.content-top-right {
                background-image: url(<xsl:value-of select="$image-path"/>content-top-right.gif);
                background-position: left top;
                background-repeat: no-repeat;
                background-attachment: scroll;
                height: 21px;
                width: 12px;
            }

            /* footer styles */
            div#footer {
                margin-top: 30px;
                clear: both;
                height: 40px;
                text-align:center;
                color: white;
                font-weight:bold;
                background-color: #7e8e35;
                background-image: url(<xsl:value-of select="$image-path"/>gradient-rule-wide.gif);
                background-repeat: no-repeat;
                padding-left: 40px;
                padding-top: 16px;
                font-size: 8pt;
            }

            /* parameter form styles */
            table#content-table div.params {
                display:none;
            }
            table.ops .operationDocumentation {
                margin-bottom: 1em;
            }
            table.ops td {
                padding: 0px 5px;
                font-size: 10pt;
                margin:0px;
            }
            table.ops td.label {
                text-align: right;
                vertical-align:top
            }
            table.ops td.label div {
                margin-right:1em;
                margin-top:3px;
            }
            table.ops td.param {
                width:90%;
            }
            table.ops textarea.nonemptyfield {
                height: 1.7em;
                overflow-x:hidden;
                overflow-y:auto;
                margin:0px;
                border: 1px solid #CCCCCC;
                width: 15em;
            }
            table.ops textarea.emptyfield {
                height: 1.7em;
                color:#CCC;
                overflow-x:hidden;
                overflow-y:auto;
                margin:0px;
                border: 1px solid #CCCCCC;
                width: 15em;
            }
            table.ops .typeannotation {
                color:#CCC;
            }
            table.ops .output {
                font-family: monospace;
                font-size:10pt;
                padding-top: 10px;
                padding-left: 10px;
            }
            table.ops .cornerExpand, table.ops .cornerCollapse {
                position:relative;
                top: 8px;
                left: -8px;
                cursor:pointer
            }
            table.ops .showDetail {
                display:block;
                margin-top:1em;
                font-family: "Lucida Grande","Lucida Sans","Microsoft Sans Serif", "Lucida Sans Unicode",verdana,sans-serif,"trebuchet ms"
            }
            table.ops .faultDetail {
                display:none;
                margin-top:1em;
            }

            /* styles for pretty-printed XML
                .fx-block (block of XML - element, multi-line text)
                .fx-elnm (element name)
                .fx-atnm (attribute name)
                .fx-att (attribute value)
                .fx-text (text content)
                .fx-cmk (comment markup)
                .fx-com (comment text)
                .fx-ns (namespace name)
                .fx-nsval (namespace value)
            */
            .fx-block {
                font-family: "Lucida Grande","Lucida Sans","Microsoft Sans Serif", "Lucida Sans Unicode",verdana,sans-serif,"trebuchet ms";
                font-size:13px;
                color:#555;
                line-height:140%;
                margin-left:1em;
                text-indent:-1em;
                margin-right:1em;
            }
            .fx-elnm { color:#005; }
            .fx-atnm { color:#500; }
            .fx-att { color:black }
            .fx-att a:link { color:black; text-decoration: none}
            .fx-att a:hover { color:black; text-decoration:underline}
            .fx-att a:active { color:black; text-decoration:underline}
            .fx-att a:visited { color:black; text-decoration:none }
            .fx-text { color:black; }
            pre.fx-text { margin-left:-1em; text-indent:0em; line-height:15px; }
            .fx-cmk {
                margin-left:1em;
                text-indent:-1em;
                margin-right:1em;
                color:#050;
            }
            .fx-com { color:#050;}
            .fx-ns { color:#505}
            .fx-nsval {color:#505}
            .fx-nsval a:link { color:#505; text-decoration: none}
            .fx-nsval a:hover { color:#505; text-decoration:underline}
            .fx-nsval a:active { color:#505; text-decoration:underline}
            .fx-nsval a:visited { color:#505; text-decoration:none}
        </style>
    </xsl:template>
</xsl:stylesheet>


