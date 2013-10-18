<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" />
<!--
/**
 * Project: KaZoe
 * File name: event.xsl
 * Description: Style sheet
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
			<xsl:if test="expired != 1">
				<xsl:attribute name='expired'><xsl:text>yes</xsl:text></xsl:attribute>
			</xsl:if>
			<head>
				<right>
					<xsl:value-of select="concat(substring(date_input,9,2),'/',substring(date_input,6,2),'/',substring(date_input,1,4))"/>
				</right>
				<left>
					<xsl:value-of select="title"/>
					<xsl:value-of select="@expired"/>
				</left>
			</head>
			<content>
				<xsl:value-of disable-output-escaping="yes" select="info"/>
			</content>
			<foot>
				<right>
					<xsl:value-of select="section"/>
				</right>
				<left>
					<xsl:attribute name="href">
						<xsl:text>?query=send_mail&amp;key=</xsl:text>
						<xsl:value-of select="contact"/>
					</xsl:attribute>
					<xsl:value-of select="name"/>
					<xsl:if test="functions != ''">
						<xsl:text> (</xsl:text>
						<xsl:value-of select="functions"/>
						<xsl:text>)</xsl:text>
					</xsl:if>
				</left>
			</foot>
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
								<action query='mod' text='ButtonModify'>
									<xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
								</action>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
				</actions>
			</xsl:if>
		</table_zone>
	</xsl:template>
</xsl:stylesheet>



