<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" />
	<xsl:variable name="query" select="php:functionString('GetKEnv','QUERY')" />

	<xsl:template match="/data">
		<data>
			<xsl:apply-templates select="item"/>
		</data>
	</xsl:template>

	<xsl:template match="item">
		<table_zone>
			<head>
				<left>
					<xsl:value-of select="php:functionString('GetKText','SendTitle')"/>
				</left>
				<right>
					<xsl:value-of select="name"/>
					<xsl:if test="functions != ''">
						<xsl:text> (</xsl:text>
						<xsl:value-of select="functions"/>
						<xsl:text>)</xsl:text>
					</xsl:if>
				</right>
			</head>
			<content>
				<form method="post" enctype="multipart/form-data">
					<fieldset>
						<legend>
							<xsl:value-of select="php:functionString('GetKText','Subject')"/>
						</legend>
						<input type="text" maxlength="512" name="subject" size="80" />
					</fieldset>
					<fieldset>
						<legend>
							<xsl:value-of select="php:functionString('GetKText','Name')"/>
						</legend>
						<input type="text" maxlength="512" name="name" size="80" />
					</fieldset>
					<fieldset>
						<legend>
							<xsl:value-of select="php:functionString('GetKText','Email')"/>
						</legend>
						<input type="text" maxlength="512" name="email" size="80" />
					</fieldset>
					<fieldset>
						<legend>
							<xsl:value-of select="php:functionString('GetKText','Message')"/>
						</legend>
						<textarea name="message" cols="92" rows="25"></textarea>
					</fieldset>
					<fieldset>
						<legend>
							<xsl:value-of select="php:functionString('GetKText','Security')"/>
						</legend>
						<table>
							<tr>
								<td>
									<img src="kazoe/lib/crypt/cryptographp.php?cfg=0&amp;" id="cryptogramme" />
								</td>
								<td>
									<a title="" style="cursor:pointer" onclick="javascript:document.images.cryptogramme.src='lib/crypt/cryptographp.php?cfg=0&amp;&amp;'+Math.round(Math.random(0)*1000)+1">
										<img src="kazoe/lib/crypt/images/reload.png" />
									</a>
								</td>
							</tr>
						</table>
						<xsl:value-of select="php:functionString('GetKText','SecurityMsg')"/>
						<input size="20" name="cryptogramme" type="text" />
					</fieldset>
					<fieldset>
						<legend>
							<xsl:value-of select="php:functionString('GetKText','Send')"/>
						</legend>
						<input type="hidden" name="id">
							<xsl:attribute name="value">
								<xsl:value-of select="php:functionString('GetKEnv','KEY')"/>
							</xsl:attribute>
						</input>
						<input type="hidden" name="query" value="submit_mail" />
						<input type="submit" name="Submit" value="Valider" />
					</fieldset>
				</form>
			</content>
		</table_zone>
	</xsl:template>
</xsl:stylesheet>



