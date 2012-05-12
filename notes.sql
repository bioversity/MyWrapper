--
-- Get all IANA missing language codes
--
SELECT
	`Code_ISO_639_3`.*
FROM
	`Code_ISO_639_3`
		LEFT JOIN `Code_IANA_LanguageRegistry_Languages`
			ON( `Code_IANA_LanguageRegistry_Languages`.`Code`
				= `Code_ISO_639_3`.`Code3` )
WHERE
	`Code_IANA_LanguageRegistry_Languages`.`Code` IS NULL;

--
-- Get all ISO639 missing language codes
--
SELECT
	`Code_IANA_LanguageRegistry_Languages`.*
FROM
	`Code_IANA_LanguageRegistry_Languages`
		LEFT JOIN `Code_ISO_639_3`
			ON( `Code_ISO_639_3`.`Code3`
				= `Code_IANA_LanguageRegistry_Languages`.`Code` )
WHERE
	`Code_ISO_639_3`.`Code3` IS NULL;
