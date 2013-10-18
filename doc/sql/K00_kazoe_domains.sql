/* coding: utf-8 */

CREATE DOMAIN KAZOE_KEY         AS integer;
CREATE DOMAIN KAZOE_VARNAME     AS character varying(32)    CONSTRAINT KAZOE_VARNAME_check  CHECK (((VALUE)::text ~ '^[A-Za-z0-9]*$'::text));
CREATE DOMAIN KAZOE_NAME        AS character varying(128);
CREATE DOMAIN KAZOE_LOGIN       AS character varying(128)   CONSTRAINT KAZOE_LOGIN_check    CHECK (((VALUE)::text ~ '^[A-Za-z0-9]*$'::text));
CREATE DOMAIN KAZOE_PASSWORD    AS character varying(128);
CREATE DOMAIN KAZOE_XML         AS text;
CREATE DOMAIN KAZOE_EMAIL       AS character varying(512)   CONSTRAINT KAZOE_EMAIL_check    CHECK (((VALUE)::text ~ '^[\\w\\.\\-]+@[\\w\\.\\-]+$'::text));
CREATE DOMAIN KAZOE_ADDRESS     AS text;
CREATE DOMAIN KAZOE_PHONE       AS character varying(16)    CONSTRAINT KAZOE_PHONE_check    CHECK (((((VALUE)::text ~ '[0-9]{10}'::text) OR ((VALUE)::text ~ '\\+[0-9]+'::text)) OR ((VALUE)::text ~ '[0]{2}[0-9]+'::text)));
CREATE DOMAIN KAZOE_TEXT        AS text;
CREATE DOMAIN KAZOE_TITLE       AS character varying(256);
CREATE DOMAIN KAZOE_DATETIME    AS timestamp without time zone;
CREATE DOMAIN KAZOE_DATE        AS date;
CREATE DOMAIN KAZOE_PICTURE     AS character varying(32);
CREATE DOMAIN KAZOE_LANG        AS character varying(4);
CREATE DOMAIN KAZOE_URL         AS character varying(512)   CONSTRAINT KAZOE_URL_check      CHECK (((VALUE)::text ~ '^[^\\s]+$'::text));
CREATE DOMAIN KAZOE_INTEGER     AS integer;
CREATE DOMAIN KAZOE_LNGCODE     AS character(5);
CREATE DOMAIN KAZOE_INET        AS inet;
CREATE DOMAIN KAZOE_METADATA    AS text;

