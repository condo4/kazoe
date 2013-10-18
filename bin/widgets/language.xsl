<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" encoding="UTF-8" indent="yes" />
    <xsl:param name="name"/>
    <xsl:param name="lng"/>
    <xsl:param name="lngdef"/>
    
    <xsl:template match="/dataset">
        <xsl:apply-templates select="./field"/>
    </xsl:template>
    
    <xsl:template match="field">
        <xsl:if test="./@name=$name">
            <fieldset>
                <legend><xsl:value-of select='legend' /></legend>
                <xsl:apply-templates select="widget_language"/>
            </fieldset>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="widget_language">
        <select>
            <xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
            <xsl:apply-templates select="option"/>
        </select>
    </xsl:template>
    
    <xsl:template match="option">
        <option>
            <xsl:attribute name="value"><xsl:value-of select="./@id"/></xsl:attribute>
            <xsl:if test="../../value = ./@id">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
             <xsl:value-of select="./desc" />
        </option>
    </xsl:template>
</xsl:stylesheet>
