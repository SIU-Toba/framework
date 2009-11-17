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

    <xsl:output method="html" indent="yes"/>

    <!-- This stylesheet only supports a single service at a time.
             If no service name is specified in this parameter, the first one is used.  -->
    <xsl:param name="service" select="services/service[1]/@name"/>

    <!-- Paths to external resources can be specified here. -->
    <xsl:param name="image-path" select="'../images/'"/>
    <xsl:param name="enable-header" select="'true'"/>
    <xsl:param name="enable-footer" select="'true'"/>

    <!-- Allows some html to be inserted immediately before the body. -->
    <xsl:param name="breadcrumbs" />

    <xsl:template match="/">
        <xsl:apply-templates select="services/service[@name=$service][1]"/>
    </xsl:template>

    <xsl:template match="service">
        <xsl:variable name="service-name">
            <xsl:call-template name="xml-name-to-javascript-name">
                <xsl:with-param name="name" select="@name"/>
            </xsl:call-template>
        </xsl:variable>

        <html>
            <head>
                <title><xsl:value-of select="$service-name"/> service documentation</title>
                <xsl:call-template name="css"/>
            </head>
            <body>
                <!-- insert breadcrumbs -->
                <xsl:value-of select="$breadcrumbs" disable-output-escaping="yes"/>
                <!-- header -->
                <xsl:if test="$enable-header='true'">
                 <div id="header">
                     <nobr>
                        <h1><strong><xsl:value-of select="$service-name"/></strong> service documentation</h1>
                    </nobr>
                 </div>
                </xsl:if>
                <!-- end of header -->
                <div id="body">
                    <xsl:if test="documentation/node()">
                        <div id="intro">
                            <div class="documentation">
                                <!-- MASHUP-65 workaround -->
                                <xsl:choose>
                                    <xsl:when test="not(documentation/*) and starts-with(documentation,'&lt;')"><xsl:value-of select="documentation" disable-output-escaping="yes"/></xsl:when>
                                    <xsl:otherwise><xsl:copy-of select="documentation/node()"/></xsl:otherwise>
                                </xsl:choose>
                            </div>
                        </div>
                    </xsl:if>
                    <div id="middle">
                        <div id="operations">
                            <div class="subtitle"><xsl:value-of select="$service-name"/> operations</div>
                            <xsl:apply-templates select="operations"/>
                        </div>
                        <div class="resources">
                            <xsl:variable name="insecure-endpoint" select="/services/service[starts-with(@address, 'http:')][1]/@address"/>
                            <xsl:variable name="secure-endpoint" select="/services/service[starts-with(@address, 'https:')][1]/@address"/>
                            <xsl:variable name="endpoint">
                                <xsl:choose>
                                    <xsl:when test="$insecure-endpoint">
                                        <xsl:call-template name="getEndpointURL">
                                            <xsl:with-param name="url" select="$insecure-endpoint"/>
                                            <xsl:with-param name="serviceName" select="$service-name"/>
                                        </xsl:call-template>
                                    </xsl:when>
                                    <xsl:when test="$secure-endpoint">
                                        <xsl:call-template name="getEndpointURL">
                                            <xsl:with-param name="url" select="$secure-endpoint"/>
                                            <xsl:with-param name="serviceName" select="$service-name"/>
                                        </xsl:call-template>
                                    </xsl:when>
                                </xsl:choose>
                            </xsl:variable>
                            <div class="subtitle">Additional resources</div>
                            <ul compact="compact">
                                <li>Visit the service's <a id="mashupPageLink">home page</a>.
                                <script type="text/javascript" language="JavaScript">
                                    var mashupPage = document.location.toString();
                                    mashupPage = mashupPage.substring(0, mashupPage.indexOf("?"));
                                    var lastSlash = mashupPage.lastIndexOf("/");
                                    mashupPage = mashupPage.substring(0,lastSlash) + "&amp;mashup=" + mashupPage.substring(lastSlash + 1);
                                    mashupPage = mashupPage.replace("/services/", "/mashup.jsp?author=");
                                    document.getElementById("mashupPageLink").href = mashupPage;
                                </script>
                                </li>
                                <li>Try the service with an on-line
                                    <xsl:if test="$insecure-endpoint">
                                        <xsl:variable name="insecure-endpoint-url">
                                            <xsl:call-template name="getEndpointURL">
                                                <xsl:with-param name="url" select="$insecure-endpoint"/>
                                                <xsl:with-param name="serviceName" select="$service-name"/>
                                            </xsl:call-template>
                                        </xsl:variable>
                                        <a id="insecure-tryit" href="?tryit" alt="HTTP Try-it page">HTTP</a>
                                        <script type="text/javascript" language="JavaScript">
                                            var pageUrl = document.URL;
                                            var pageScheme = pageUrl.substring(0, pageUrl.indexOf(':'));
                                            if (pageScheme == 'https') {
                                                var pageDomain = pageUrl.substring(pageUrl.indexOf('://') + 3, pageUrl.indexOf('/',pageUrl.indexOf('://')+3));
                                                var pageDomainNoPort = pageDomain.indexOf(":") >= 0 ? pageDomain.substring(0, pageDomain.indexOf(":")) : pageDomain;
                                                var altLoc = '<xsl:value-of select="$insecure-endpoint-url"/>';
                                                if (pageDomainNoPort == 'localhost') {
                                                    altLoc = altLoc.replace('<xsl:value-of select="substring-before(concat(substring-before(substring-after($insecure-endpoint-url, '//'),'/'), ':'), ':')"/>', 'localhost');
                                                }
                                                document.getElementById('insecure-tryit').href = altLoc + "?tryit";
                                            }
                                        </script>
                                    </xsl:if>
                                    <xsl:if test="$insecure-endpoint and $secure-endpoint"> or </xsl:if>
                                    <xsl:if test="$secure-endpoint">
                                        <xsl:variable name="secure-endpoint-url">
                                            <xsl:call-template name="getEndpointURL">
                                                <xsl:with-param name="url" select="$secure-endpoint"/>
                                                <xsl:with-param name="serviceName" select="$service-name"/>
                                            </xsl:call-template>
                                        </xsl:variable>
                                        <a id='secure-tryit' href="?tryit" alt="HTTPS Try-it page">HTTPS</a>
                                        <script type="text/javascript" language="JavaScript">
                                            var pageUrl = document.URL;
                                            var pageScheme = pageUrl.substring(0, pageUrl.indexOf(':'));
                                            if (pageScheme == 'http') {
                                                var pageDomain = pageUrl.substring(pageUrl.indexOf('://') + 3, pageUrl.indexOf('/',pageUrl.indexOf('://')+3));
                                                var pageDomainNoPort = pageDomain.indexOf(":") >= 0 ? pageDomain.substring(0, pageDomain.indexOf(":")) : pageDomain;
                                                var altLoc = '<xsl:value-of select="$secure-endpoint-url"/>';
                                                if (pageDomainNoPort == 'localhost') {
                                                    altLoc = altLoc.replace('<xsl:value-of select="substring-before(concat(substring-before(substring-after($secure-endpoint-url, '//'),'/'), ':'), ':')"/>', 'localhost');
                                                }
                                                document.getElementById('secure-tryit').href = altLoc + "?tryit";
                                            }
                                        </script>
                                    </xsl:if>
                                    AJAX client.</li>
                                <li>View the <a alt="Source Code" href="?source&amp;content-type=text/plain">Source Code</a> of the service.</li>
                                <li>View the <a alt="Source Code" href="?stub&amp;content-type=text/plain">Javascript (DOM) stub</a> of the service.</li>
                                <li>View the <a alt="Source Code" href="?stub&amp;lang=e4x&amp;content-type=text/plain">Javascript (E4X) stub</a> of the service.</li>
                                <li>View the <a alt="WSDL 2.0" href="?wsdl2&amp;annotation=true">WSDL 2.0</a> description.</li>
                                <li>View the <a alt="WSDL 1.1" href="?wsdl&amp;annotation=true">WSDL 1.1</a> description.</li>
                                <li>View the <a alt="XML Schema" href="?xsd&amp;annotation=true">XML Schema</a> of the message types.</li>
                             </ul>
                        </div>
                    </div>
                </div>
                <!-- footer -->
                <xsl:if test="$enable-footer='true'">
                  <div id="footer">
                      <p>&#169; 2007-2008 <a href="http://wso2.com/">WSO2 Inc.</a></p>
                  </div>
                </xsl:if>
                <!-- end of footer -->
            </body>
        </html>
    </xsl:template>

    <xsl:template match="operations">
        <table id="operationsTable">
            <xsl:apply-templates />
        </table>
    </xsl:template>

    <xsl:template match="operation">
        <xsl:variable name="name">
            <xsl:call-template name="xml-name-to-javascript-name">
                <xsl:with-param name="name" select="@name"/>
            </xsl:call-template>
        </xsl:variable>
        <tr class="operation">
            <td class="operationName"><xsl:value-of select="$name"/>
                <xsl:text> (</xsl:text>
                <xsl:for-each select="signature/params/param">
                    <xsl:if test="position() != 1">, </xsl:if>
                    <xsl:if test="(position() mod 4) = 0"><br/>&#160;&#160;&#160;</xsl:if>
                    <xsl:value-of select="@name"/>
                </xsl:for-each>
                <xsl:text>)</xsl:text>
            </td>
            <td>
                <xsl:if test="documentation/node()">
                    <div class="documentation">
                        <!-- MASHUP-65 workaround -->
                        <xsl:choose>
                            <xsl:when test="not(documentation/*) and starts-with(documentation,'&lt;')"><xsl:value-of select="documentation" disable-output-escaping="yes"/></xsl:when>
                            <xsl:otherwise><xsl:copy-of select="documentation/node()"/></xsl:otherwise>
                        </xsl:choose>
                    </div>
                </xsl:if>
                <xsl:apply-templates select="signature"/>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="signature">
         <table class="params" cellspacing="0" cellpadding="0">
             <tr>
                 <th colspan="2">
                     Parameters:
                 </th>
             </tr>
             <xsl:for-each select="params/param">
                <tr class="parameters">
                    <td class="parameter"><xsl:value-of select="@name"/></td>
                    <td class="type"><xsl:apply-templates select="@type"/></td>
                </tr>
            </xsl:for-each>
            <xsl:for-each select="returns/param">
                <tr class="parameters">
                    <td class="return">(return value)</td>
                    <td class="type"><xsl:apply-templates select="@type"/></td>
                </tr>
            </xsl:for-each>
         </table>
    </xsl:template>

    <xsl:template match="param/@type">
        <xsl:choose>
            <xsl:when test="../enumeration">
                <span class="enumeration">
                    <xsl:for-each select="../enumeration">
                        <xsl:if test="position() > 1"> | </xsl:if>
                        <xsl:value-of select="@value"/>
                    </xsl:for-each>
                </span>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="."/>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:if test="../@minOccurs &lt; 1 or ../@maxOccurs &gt; 1 or ../@maxOccurs = 'unbounded'"><sub>(<xsl:value-of select="../@minOccurs"/>..<xsl:choose><xsl:when test="../@maxOccurs = 'unbounded'">*</xsl:when><xsl:otherwise><xsl:value-of select="../@maxOccurs"/></xsl:otherwise></xsl:choose>)</sub></xsl:if>
    </xsl:template>

    <xsl:template name="xml-name-to-javascript-name">
        <xsl:param name="name"/>
        <xsl:value-of select="translate($name,'.-','__')"/>
    </xsl:template>

    <xsl:template name="getEndpointURL">
        <xsl:param name="url"/>
        <xsl:param name="serviceName"/>
        <xsl:value-of select="concat(substring-before($url, $serviceName),$serviceName)"/>
    </xsl:template>

    <!-- template for inserting CSS -->
    <xsl:template name="css">
        <!-- css is embedded rather than linked so that the $image-path can be altered dynamically -->
        <style type="text/css">
            body {
                margin: 0px;
                padding: 0px;
                font-family: "Lucida Grande","Lucida Sans","Microsoft Sans Serif","Lucida Sans Unicode",verdana,sans-serif,"trebuchet ms";
                font-size: 10pt;
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

            /* body styles */
            div#body {
                background-image: url(<xsl:value-of select="$image-path"/>header-bg.gif);
                background-position: top left;
                background-repeat: no-repeat;
            }
            div#intro {
                padding-left: 40px;
                padding-top: 10px;
                width: 90%;
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
                padding-top: 3px;
                font-size: 8pt;
            }

            /* middle styles */
            div#middle {
                margin-left: 40px;
                margin-right: 50px;
                margin-right: 20px;
                margin-bottom: 0px;
            }

            div.subtitle {
                padding-top:.5em;
                font-weight:bold;
                font-size:140%;
            }

            /* operation table styles */
            table#operationsTable {
                margin:.5em .5em .5em 1em
            }

            table#operationsTable tr.operation {
                vertical-align:top;
                padding-top:3em
            }
            table#operationsTable td.operationName {
                white-space:nowrap;
                padding-right:1.5em;
                font-weight:bold;
                font-size:10pt;
            }
            table#operationsTable td {
                font-size:10pt;
            }

            table#operationsTable table.params {
                margin: .5em 0em 1.5em 0em
            }
            table#operationsTable table.params th {
                text-align:left;
                font-size:10pt;
            }
            table#operationsTable table.params td.parameter,
            table#operationsTable table.params td.return {
                text-align:right;
                padding-right: 1em;
                padding-left: 2em;
                border-right: 1px solid #F47B20;
            }
            table#operationsTable table.params td.return {
                font-style:italic;
            }
            table#operationsTable table.params td.type {
                padding-left:1em;
                font-style:italic
            }
            table#operationsTable table.params td.type .enumeration {
                font-style:normal
            }
        </style>
    </xsl:template>

</xsl:stylesheet>