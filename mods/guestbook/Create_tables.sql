CREATE TABLE :{appname}
(
	id          SERIAL         NOT NULL,
	name        kazoe_name     NOT NULL,
	email       kazoe_email,
	date_input  kazoe_datetime NOT NULL DEFAULT now(),
	lang        kazoe_lngcode  NOT NULL,
	comment     kazoe_text     NOT NULL,
	_properties kazoe_xml      NOT NULL DEFAULT ('<?xml version="1.0" encoding="UTF-8" ?><properties />'::text)::kazoe_xml,
	PRIMARY KEY (id)
);
