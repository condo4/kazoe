CREATE TABLE :{appname}_sections
(
	id          SERIAL        NOT NULL,
	name        kazoe_varname NOT NULL,
	_properties kazoe_xml     NOT NULL DEFAULT ('<?xml version="1.0" encoding="UTF-8" ?><properties />'::text)::kazoe_xml,
	_owner      kazoe_key     NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	FOREIGN KEY (_owner) REFERENCES kazoe_passwd (id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE SET DEFAULT
);

CREATE TABLE :{appname}_sections_titles
(
	id          SERIAL        NOT NULL,
	sectionid   kazoe_key     NOT NULL,
	lang        kazoe_lang    NOT NULL,
	title       kazoe_title    NOT NULL,
	_properties kazoe_xml     NOT NULL DEFAULT ('<?xml version="1.0" encoding="UTF-8" ?><properties />'::text)::kazoe_xml,
	_owner      kazoe_key     NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	FOREIGN KEY (_owner)    REFERENCES kazoe_passwd        (id) ON UPDATE CASCADE ON DELETE SET DEFAULT,
	FOREIGN KEY (sectionid) REFERENCES :{appname}_sections (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE :{appname}
(
	id          SERIAL          NOT NULL,
	type        kazoe_key       NOT NULL DEFAULT 1,
	title       kazoe_title     NOT NULL,
	date_input  kazoe_datetime  DEFAULT now(),
	date_begin  kazoe_date      DEFAULT now(),
	date_expire kazoe_date,
	info        kazoe_text      NOT NULL,
	_properties kazoe_xml       NOT NULL DEFAULT ('<?xml version="1.0" encoding="UTF-8" ?><properties />'::text)::kazoe_xml,
	_owner      kazoe_key       NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	FOREIGN KEY (_owner) REFERENCES kazoe_passwd        (id) ON UPDATE CASCADE ON DELETE SET DEFAULT,
	FOREIGN KEY (type)   REFERENCES :{appname}_sections (id) ON UPDATE CASCADE ON DELETE SET DEFAULT
);
