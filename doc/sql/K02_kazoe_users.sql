/* coding: utf-8
----------------------------------------------
--            kazoe_users                   --
----------------------------------------------
*/

CREATE SEQUENCE kazoe_pk_users;

CREATE TABLE "kazoe_users" (
    id              KAZOE_KEY           NOT NULL    PRIMARY KEY DEFAULT nextval('kazoe_pk_users'),
    name            KAZOE_NAME          NOT NULL,
    firstname       KAZOE_NAME          NOT NULL,
    email           KAZOE_EMAIL         NOT NULL,
    address         KAZOE_ADDRESS,
    phone           KAZOE_PHONE,
    mobile          KAZOE_PHONE,
    fax             KAZOE_PHONE,
    functions       KAZOE_NAME,
    _passwd         KAZOE_LOGIN         NOT NULL    REFERENCES "kazoe_passwd" (login),
    _properties     KAZOE_XML           NOT NULL    DEFAULT '<?xml version="1.0" encoding="UTF-8" ?><properties />',
    _owner          KAZOE_KEY           NOT NULL    REFERENCES "kazoe_passwd" (id)
);

/* CREATE DEFAULT USER "root" PASSWORD "kazoe" */
INSERT INTO kazoe_users VALUES (0,'AdminName','AdminFirstname','admin@localhost',NULL,NULL,NULL,NULL,NULL,'root','<?xml version="1.0" encoding="UTF-8" ?><properties />',0);
SELECT setval('kazoe_pk_users', 1);
