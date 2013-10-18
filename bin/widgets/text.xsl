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
                <xsl:apply-templates select="widget_text"/>
            </fieldset>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="widget_text">
        <textarea>
            <xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
            <xsl:attribute name="cols"><xsl:value-of select="@cols"/></xsl:attribute>
            <xsl:attribute name="rows"><xsl:value-of select="@rows"/></xsl:attribute>
            <xsl:if test="@id">
                <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="@class">
                <xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="../value">
                <xsl:value-of select="../value"/>
            </xsl:if>
        </textarea>
    </xsl:template>
</xsl:stylesheet>
