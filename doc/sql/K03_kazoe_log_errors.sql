/* coding: utf-8
----------------------------------------------
--           kazoe_errors_log               --
----------------------------------------------
*/

CREATE SEQUENCE kazoe_pk_log_errors;

CREATE TABLE kazoe_log_errors(
    id          KAZOE_KEY       NOT NULL PRIMARY KEY DEFAULT nextval('kazoe_pk_log_errors'),
    datetime    KAZOE_DATETIME  NOT NULL DEFAULT now(),
    level       KAZOE_INTEGER   NOT NULL,
    message     KAZOE_TEXT      NOT NULL,
    file        KAZOE_TEXT      NOT NULL,
    line        KAZOE_TEXT      NOT NULL,
    backtrace   KAZOE_TEXT      NOT NULL,
    occure      KAZOE_INTEGER   NOT NULL DEFAULT 1
);
