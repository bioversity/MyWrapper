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
 * Germplasm.
 *
 * This tag represents the germplasm domain, it is a generalised domain comprising all
 * germplasms.
 */
define( "kDOMAIN_GERMPLASM",		':DOMAIN:100' );				// Germplasm.

/**
 * Geography.
 *
 * This tag represents the geography domain.
 */
define( "kDOMAIN_GEOGRAPHY",		':DOMAIN:200' );				// Geography.

/**
 * Language.
 *
 * This tag represents the geography domain.
 */
define( "kDOMAIN_LANGUAGE",			':DOMAIN:300' );				// Language.

/**
 * Taxonomy.
 *
 * This tag represents the taxonomy domain.
 */
define( "kDOMAIN_TAXONOMY",			':DOMAIN:400' );				// Taxonomy.

/*=======================================================================================
 *	SUBDOMAINS																			*
 *======================================================================================*/

/**
 * Sample.
 *
 * This tag represents the germplasm sample domain.
 */
define( "kDOMAIN_SAMPLE",			':DOMAIN:110' );				// Sample.

/**
 * Accession.
 *
 * This tag represents the accession domain.
 */
define( "kDOMAIN_ACCESSION",		':DOMAIN:120' );				// Accession.

/**
 * Specimen.
 *
 * This tag represents the specimen domain.
 */
define( "kDOMAIN_SPECIMEN",			':DOMAIN:130' );				// Specimen.

/**
 * Land-race.
 *
 * This tag represents the landrace or farmer germplasm domain.
 */
define( "kDOMAIN_LANDRACE",			':DOMAIN:140' );				// Landrace.

/**
 * Population.
 *
 * This tag represents the in-situ population domain.
 */
define( "kDOMAIN_POPULATION",		':DOMAIN:150' );				// Population.

/*=======================================================================================
 *	CATEGORIES																			*
 *======================================================================================*/

/**
 * Passport.
 *
 * This tag represents the passport category, it collects all descriptors comprising
 * passport datasets.
 */
define( "kCATEGORY_PASSPORT",		':CATEGORY:1' );				// Passport.

/**
 * Characterisation.
 *
 * This tag represents the characterisation category, it collects all descriptors comprising
 * characterisation datasets.
 */
define( "kCATEGORY_CHAR",			':CATEGORY:2' );				// Characterisation.

/**
 * Evaluation.
 *
 * This tag represents the evaluation category, it collects all descriptors comprising
 * evaluation datasets.
 */
define( "kCATEGORY_EVAL",			':CATEGORY:3' );				// Evaluation.

/**
 * Administrative.
 *
 * This tag represents the administrative units category, it collects all descriptors
 * comprising administrative units.
 */
define( "kCATEGORY_ADMIN",			':CATEGORY:4' );				// Administrative units.

/**
 * Geographic units.
 *
 * This tag represents the geographic units category, it collects all descriptors
 * comprising geographic units.
 */
define( "kCATEGORY_GEO",			':CATEGORY:5' );				// Geographic units.

/**
 * Epithet.
 *
 * This tag represents the epithet category, it collects all descriptors related to
 * epithets.
 */
define( "kCATEGORY_EPITHET",		':CATEGORY:6' );				// Epithet.

/**
 * Authority.
 *
 * This tag represents the authority category, it collects all descriptors related to
 * authorities.
 */
define( "kCATEGORY_AUTH",			':CATEGORY:7' );				// Authority.


?>
