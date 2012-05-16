SELECT DISTINCT
	`Code_Annex1_Selection`.`Annex1Crop`,
	`Code_Annex1_Species`.`Crop`,
	`Code_Crops`.`Code`
FROM
	`Code_Annex1_Selection`
		LEFT JOIN `Code_Annex1_Species`
			ON( `Code_Annex1_Species`.`Crop` = `Code_Annex1_Selection`.`Annex1Crop` )
		LEFT JOIN `Code_Crops`
			ON( `Code_Crops`.`Code` = `Code_Annex1_Selection`.`Crop` )
ORDER BY
	CONVERT( `Code_Crops`.`Code`, UNSIGNED ),
	`Code_Annex1_Species`.`Crop`
