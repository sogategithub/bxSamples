CREATE TABLE `ch_testdispatcher` (
    `UF_CREATED` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `UF_ACTIVE` int(1) NOT NULL DEFAULT '0',
    `UF_ACTIVE_TO` date NULL,
    `UF_USER` int(11) NOT NULL,
    `UF_COMMENT` longtext COLLATE utf8_unicode_ci NOT NULL,
    `UF_RIGHTS` int(2) CHECK (`UF_RIGHTS` BETWEEN 1 and 12),
    FOREIGN KEY (`UF_USER`)  REFERENCES b_user (ID) ON DELETE CASCADE,
    PRIMARY KEY (`UF_USER`)
    );

CREATE TABLE `ch_testobjects` (
    `ID` INT(11) NOT NULL AUTO_INCREMENT,
    `UF_CREATED` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `UF_NAME` VARCHAR(255) NOT NULL ,
    `UF_ADDRESS` TEXT NOT NULL ,
    `UF_COMMENT` LONGTEXT NOT NULL ,
    PRIMARY KEY (`ID`)
    );

CREATE TABLE `ch_testdisob` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `UF_USER` int(11) NOT NULL,
    `UF_OBJECT` int(11) NOT NULL,
    FOREIGN KEY (`UF_USER`)  REFERENCES b_user (ID) ON DELETE CASCADE,
    FOREIGN KEY (`UF_OBJECT`)  REFERENCES ch_testobjects (ID) ON DELETE CASCADE,
    PRIMARY KEY (`ID`)
    );