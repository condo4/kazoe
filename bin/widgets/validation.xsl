<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" encoding="UTF-8" indent="yes" />
    <xsl:param name="name"/>
    <xsl:param name="lng"/>
    <xsl:param name="lngdef"/>
    <xsl:param name="mode"/>
    <xsl:param name="return"/>
    
    <xsl:template match="/dataset">
        <fieldset>
            <legend>Valider</legend>
            <input type='hidden' name='query'>
                <xsl:attribute name="value">
                    <xsl:value-of select="$return"/>
                </xsl:attribute>
            </input>
            <input type='submit' name='Submit' value='Valider' />
        </fieldset>
    </xsl:template>
</xsl:stylesheet>
