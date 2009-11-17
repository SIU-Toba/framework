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

    <xsl:import href="template-html.xslt"/>

    <xsl:output method="xml" indent="yes" omit-xml-declaration="no"/>

    <!-- Toggle between DOM and E4X treatment of XML objects. -->
    <xsl:param name="e4x" select="false()"/>

    <!-- This stylesheet only supports a single service at a time.
         For gadgets, this must be an HTTP endpoint.  -->
    <xsl:param name="service" select="services/service[1]/@name"/>
    <xsl:variable name="address" select="services/service[1]/@address"/>

    <!-- Paths to external resources can be specified here. -->
    <xsl:param name="wsrequest-location" select="concat(substring-before($address,'/services'), '/js/wso2/WSRequest.js')"/>
    <xsl:param name="stub-location"/>

    <xsl:variable name="service-name">
        <xsl:call-template name="service-name-to-javascript-name">
            <xsl:with-param name="name" select="$service"/>
        </xsl:call-template>
    </xsl:variable>

<xsl:template match="/">
    <xsl:apply-templates select="services/service[@name=$service][1]"/>
</xsl:template>

<xsl:template match="service">
    <Module>
        <xsl:comment>
      ~ Copyright 2005-2008 WSO2, Inc. http://www.wso2.org
      ~
      ~ Licensed under the Apache License, Version 2.0 (the "License");
      ~ you may not use this file except in compliance with the License.
      ~ You may obtain a copy of the License at
      ~
      ~ http://www.apache.org/licenses/LICENSE-2.0
      ~
      ~ Unless required by applicable law or agreed to in writing, software
      ~ distributed under the License is distributed on an "AS IS" BASIS,
      ~ WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
      ~ See the License for the specific language governing permissions and
      ~ limitations under the License.
    </xsl:comment>
        <xsl:text>&#10;&#10;</xsl:text>
        <ModulePrefs title="{$service} - powered by the WSO2 Mashup Server" width="320" height="240" scrolling="true"/>
        <Content type="html">
            <xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text>
<style type="text/css">
    #body {font: 75%/1.5 "Lucida Grande","Lucida Sans","Microsoft Sans Serif", "Lucida Sans Unicode",verdana,sans-serif,"trebuchet ms"; color: #111; }
    #result-console {border:1px solid black; padding:1em;}
    #error-console {color:red; font-weight:bold}
    #footer {text-align:center; font-size:75%}
</style>
            <script type="text/javascript" src="{$wsrequest-location}"><xsl:text> </xsl:text></script>
            <!-- Calculate the source of the stub, including whether it's e4x or not -->
            <xsl:variable name="e4x-param">
                <xsl:if test="$e4x">; e4x=1</xsl:if>
            </xsl:variable>
            <xsl:variable name="src">
                <xsl:choose>
                    <xsl:when test="$stub-location"><xsl:value-of select="$stub-location"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="substring-before(@address,concat('.', @endpoint))"/>?stub</xsl:otherwise>
                </xsl:choose>
                <xsl:if test="$e4x">&amp;lang=e4x</xsl:if>
            </xsl:variable>
            <xsl:text>&#10;</xsl:text>
            <script type="text/javascript{$e4x-param}" src="{$src}"><xsl:text> </xsl:text></script>
            <xsl:text>&#10;</xsl:text>
            <xsl:call-template name="localscript"/>
            <div id="body">
                <h1><xsl:value-of select="$service"/></h1>
                <div id="result-console">
                    <xsl:comment> This div will contain the text returned as response to the service call. </xsl:comment>
                </div>
                <div id="error-console">
                    <xsl:comment> This div will contain a description of any errors encountered. </xsl:comment>
                </div>
                <div id="footer">
                    <div>Powered by <a href="http://wso2.org/projects/mashup" target="_top">WSO2 Mashup Server</a>.</div>
                    <div>Access all the operations of the <xsl:value-of select="$service"/> service through the <a href="{substring-before($src,'?')}?tryit" target="_top">Try-it page</a>.</div>
                </div>
            </div>
        <xsl:text disable-output-escaping="yes">]]&gt;</xsl:text>
        </Content>
    </Module>
</xsl:template>

<xsl:template name="localscript">
<script type="text/javascript" language="javascript">
    var browser = WSRequest.util._getBrowser();
    _IG_RegisterOnloadHandler(init);

    // Demonstrates calling an operation of the '<xsl:value-of select="$service"/>' Mashup
    function init() {
        <xsl:apply-templates select="operations/operation[1]"/>
    }

    // Sample invocations (unused) for other operations.
    function samples() {
        <xsl:apply-templates select="operations/operation[position() > 1]"/>
    }

    // Handles and error by displaying the reason in a dialog
    function showPayload(payload) {
        log ("result-console", payload);
    }

    // Handles and error by displaying the reason in a dialog
    function handleError(error) {
        log ("error-console", "Fault: " + error.reason + "\n\n" + error.detail);
    }

    function log(consoleName, data) {
        var console = document.getElementById(consoleName);
        <xsl:if test="$e4x">if (typeof(data) == "xml") data = data.toXMLString();
        </xsl:if>if (browser == "ie" || browser == "ie7") console.innerText = data;
        else console.textContent = data;
    }
</script>
</xsl:template>

</xsl:stylesheet>


