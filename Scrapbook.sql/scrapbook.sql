--
-- Annex 1 Crop code builder (Genesys codes too)
--
SELECT DISTINCT
	`Code_Annex1_Selection`.`Annex1Crop`,
	`Code_Annex1_Species`.`Crop`,
	`Code_Crops`.`Code`,
	`Code_Crops`.`Label`
FROM
	`Code_Annex1_Selection`
		LEFT JOIN `Code_Annex1_Species`
			ON( `Code_Annex1_Species`.`Crop` = `Code_Annex1_Selection`.`Annex1Crop` )
		LEFT JOIN `Code_Crops`
			ON( `Code_Crops`.`Code` = `Code_Annex1_Selection`.`Crop` )
ORDER BY
	CONVERT( `Code_Crops`.`Code`, UNSIGNED ),
	`Code_Annex1_Species`.`Crop`

--
-- Annex 1 Crop code builder (Genesys codes too)
--
SELECT DISTINCT
	`Code_Annex1_Selection`.`Annex1Crop`,
	`Code_Annex1_Species`.`Crop`,
	`Code_Crops`.`Code`,
	`Code_Crops`.`Label`
FROM
	`Code_Annex1_Selection`
		LEFT JOIN `Code_Annex1_Species`
			ON( `Code_Annex1_Species`.`Crop` = `Code_Annex1_Selection`.`Annex1Crop` )
		LEFT JOIN `Code_Crops`
			ON( `Code_Crops`.`Code` = `Code_Annex1_Selection`.`Crop` )
WHERE
	LENGTH( `Code_Annex1_Selection`.`Annex1Crop` ) > 0
ORDER BY
	`Code_Annex1_Species`.`Crop`

--
-- Table structure for table `Code_Annex1_Groups`
--
DROP TABLE IF EXISTS `Code_Annex1_Crops`;
CREATE TABLE IF NOT EXISTS `Code_Annex1_Crops` (
  `Code` char(3) character set ascii NOT NULL COMMENT 'Annex 1 crop code',
  `Parent` char(3) character set ascii default NULL COMMENT 'Annex 1 group code',
  `Label` tinytext character set ascii NOT NULL COMMENT 'Annex 1 group label',
  `Description` text character set ascii COMMENT 'Annex 1 group description',
  PRIMARY KEY  (`Code`),
  KEY `Parent` (`Parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Annex 1 crops lookup table';

--
-- Annex 1 Crop code builder
--
INSERT INTO
	`Code_Annex1_Crops`
	(
		`Label`,
		`Description`
	)
SELECT DISTINCT
	`Code_Annex1_Selection`.`Annex1Crop`,
	`Code_Annex1_Selection`.`Annex1Crop`
FROM
	`Code_Annex1_Selection`
WHERE
	LENGTH( `Code_Annex1_Selection`.`Annex1Crop` ) > 0
ORDER BY
	`Code_Annex1_Selection`.`Annex1Crop`

SELECT DISTINCT
	`Code_Annex1_Selection`.`Annex1Crop`,
	`Code_Annex1_Selection`.`Group`
FROM
	`Code_Annex1_Selection`

UPDATE
	`Code_Annex1_Crops`
		LEFT JOIN `Code_Annex1_Selection`
			ON( `Code_Annex1_Selection`.`Annex1Crop` = `Code_Annex1_Crops`.`Label` )
SET
	`Code_Annex1_Crops`.`Parent` = `Code_Annex1_Selection`.`Group`
WHERE
	LENGTH( `Code_Annex1_Selection`.`Annex1Crop` ) > 0

UPDATE
	`Code_Annex1_Species`
		LEFT JOIN `Code_Annex1_Crops`
			ON( `Code_Annex1_Crops`.`Label` = `Code_Annex1_Species`.`Crop` )
SET
	`Code_Annex1_Species`.`Code` = `Code_Annex1_Crops`.`Code`
WHERE
	`Code_Annex1_Crops`.`Code` IS NOT NULL
