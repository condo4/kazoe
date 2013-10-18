<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<!--
/**
 * Project: KaZoe
 * File name: section.xsl
 * Description: Section list transformation sheet for displaying
 *
 * @author Fabien Proriol Copyright (C) 2009.
 *
 * @see The GNU Public License (GPL)
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
 -->
    <xsl:output method="xml" encoding="UTF-8" indent="yes" />

	<xsl:variable name="query" select="php:functionString('GetKEnv','QUERY')" /> 
    
    <xsl:template match="/data">
        <data>
            <navi><xsl:attribute name="page"><xsl:value-of select="php:functionString('GetKEnv','PAGE')"/></xsl:attribute><xsl:attribute name="nbpage"><xsl:value-of select="php:functionString('GetKEnv','PAGEMAX')"/></xsl:attribute></navi>
            <xsl:apply-templates select="item"/>
            <navi><xsl:attribute name="page"><xsl:value-of select="php:functionString('GetKEnv','PAGE')"/></xsl:attribute><xsl:attribute name="nbpage"><xsl:value-of select="php:functionString('GetKEnv','PAGEMAX')"/></xsl:attribute></navi>
        </data>
    </xsl:template>

    <xsl:template match="item">
        <table_zone>
            <head>
                <left>
                    <xsl:value-of select="name"/>
                </left>
            </head>
            <content>
                <xsl:value-of disable-output-escaping="yes" select="title"/>
            </content>

            <xsl:if test="_permitions/text()='True'">
                <actions>
                    <xsl:if test="__DEL__">
                        <xsl:choose>
                            <xsl:when test="$query='del'">
                                <action query="submit_del" text='ButtonDeleteC'>
                                    <xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
                                </action>
                            </xsl:when>
                            <xsl:otherwise>
                                <action query="del" text='ButtonDelete'>
                                    <xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
                                </action>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:if>
                    <xsl:if test="__MOD__">
                        <xsl:choose>
                            <xsl:when test="$query='mod'">
                                <action query="submit_mod" text='ButtonModify'>
                                    <xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
                                </action>
                            </xsl:when>
                            <xsl:otherwise>
                                <action query="mod" text='ButtonModify'>
                                    <xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
                                </action>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:if>
                    <xsl:if test="__MODTRANSLATION__">
						<action query="modtranslation" text='ButtonModifyTr'>
							<xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
						</action>
                    </xsl:if>
                </actions>
            </xsl:if>

        </table_zone>
    </xsl:template>
</xsl:stylesheet>



