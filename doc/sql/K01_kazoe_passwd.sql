/* coding: utf-8
----------------------------------------------
--            kazoe_passwd                  --
----------------------------------------------
*/

CREATE SEQUENCE kazoe_pk_passwd;

CREATE TABLE "kazoe_passwd" (
    id              KAZOE_KEY           NOT NULL    PRIMARY KEY DEFAULT nextval('kazoe_pk_passwd'),
    login           KAZOE_LOGIN         NOT NULL    UNIQUE,
    passwords       KAZOE_PASSWORD      NOT NULL,
    _properties     KAZOE_XML           NOT NULL    DEFAULT '<?xml version="1.0" encoding="UTF-8" ?><properties />',
    _owner          KAZOE_KEY           REFERENCES "kazoe_passwd" (id)
);


/* CREATE DEFAULT USER "root" PASSWORD "kazoe" */
INSERT INTO kazoe_passwd VALUES (0,'root','336df3e8d340009a77f069a48877819a0b5aa51e');
SELECT setval('kazoe_pk_passwd', 1);
