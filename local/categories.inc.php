<?php

/*=======================================================================================
 *																						*
 *									categories.inc.php									*
 *																						*
 *======================================================================================*/
 
/**
 *	Categories.
 *
 *	This file contains common domains and categories used by all classes.
 *
 *	@package	MyWrapper
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 08/05/2012
 */

/*=======================================================================================
 *	NAMESPACES																			*
 *======================================================================================*/

/**
 * Domains.
 *
 * This tag represents the default domains namespace.
 */
define( "kDEF_DOMAIN",				':DOMAIN' );					// Domain.

/**
 * Categories.
 *
 * This tag represents the default categories namespace.
 */
define( "kDEF_CATEGORY",			':CATEGORY' );					// Category.

/*=======================================================================================
 *	DOMAINS																				*
 *======================================================================================*/

/**
 * Geography.
 *
 * This tag represents the geography domain.
 */
define( "kDOMAIN_GEOGRAPHY",		':DOMAIN:GEOGRAPHY' );			// Geography.

/**
 * Language.
 *
 * This tag represents the geography domain.
 */
define( "kDOMAIN_LANGUAGE",			':DOMAIN:LANGUAGE' );			// Language.

/**
 * Germplasm.
 *
 * This tag represents the germplasm domain, it is a generalised domain comprising all
 * germplasms.
 */
define( "kDOMAIN_GERMPLASM",		':DOMAIN:GERMPLASM' );			// Germplasm.

/*=======================================================================================
 *	SUBDOMAINS																			*
 *======================================================================================*/

/**
 * Sample.
 *
 * This tag represents the germplasm sample domain.
 */
define( "kDOMAIN_SAMPLE",			':DOMAIN:GERMPLASM:SAMPLE' );		// Sample.

/**
 * Accession.
 *
 * This tag represents the accession domain.
 */
define( "kDOMAIN_ACCESSION",		':DOMAIN:GERMPLASM:ACCESSION' );	// Accession.

/**
 * Specimen.
 *
 * This tag represents the specimen domain.
 */
define( "kDOMAIN_SPECIMEN",			':DOMAIN:GERMPLASM:SPECIMEN' );		// Specimen.

/**
 * Land-race.
 *
 * This tag represents the landrace or farmer germplasm domain.
 */
define( "kDOMAIN_LANDRACE",			':DOMAIN:GERMPLASM:LANDRACE' );		// Landrace.

/**
 * Population.
 *
 * This tag represents the in-situ population domain.
 */
define( "kDOMAIN_POPULATION",		':DOMAIN:GERMPLASM:POPULATION' );	// Population.

/*=======================================================================================
 *	CATEGORIES																			*
 *======================================================================================*/

/**
 * Passport.
 *
 * This tag represents the passport category, it collects all descriptors comprising
 * passport datasets.
 */
define( "kCATEGORY_PASSPORT",		':CATEGORY:PASSPORT' );			// Passport.

/**
 * Characterisation.
 *
 * This tag represents the characterisation category, it collects all descriptors comprising
 * characterisation datasets.
 */
define( "kCATEGORY_CHAR",			':CATEGORY:CHAR' );				// Characterisation.

/**
 * Evaluation.
 *
 * This tag represents the evaluation category, it collects all descriptors comprising
 * evaluation datasets.
 */
define( "kCATEGORY_EVAL",			':CATEGORY:EVAL' );				// Evaluation.

/**
 * Administrative.
 *
 * This tag represents the administrative units category, it collects all descriptors
 * comprising administrative units.
 */
define( "kCATEGORY_ADMIN",			':CATEGORY:ADMIN' );			// Administrative units.

/**
 * Geographic units.
 *
 * This tag represents the geographic units category, it collects all descriptors
 * comprising geographic units.
 */
define( "kCATEGORY_GEO",			':CATEGORY:GEOG' );				// Geographic units.


?>
