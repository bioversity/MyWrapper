<?php

/*=======================================================================================
 *																						*
 *									CFAOInstitute.inc.php								*
 *																						*
 *======================================================================================*/
 
/**
 * {@link CFAOInstitute CFAOInstitute} definitions.
 *
 * This file contains common definitions used by the {@link CFAOInstitute CFAOInstitute}
 * class.
 *
 *	@package	MyWrapper
 *	@subpackage	Entities
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 06/04/2012
 */

/*=======================================================================================
 *	DEFAULT OBJECT TAGS																	*
 *======================================================================================*/

/**
 * Institute type.
 *
 * This value defines the institute entity type.
 */
define( "kENTITY_INST_FAO",						':ENTITY:INST:FAO' );

/*=======================================================================================
 *	DEFAULT OBJECT OFFSETS																*
 *======================================================================================*/

/**
 * ECPGR institute acronym.
 *
 * This value defines the institute ECPGR acronym.
 *
 * Cardinality: single.
 */
define( "kENTITY_INST_FAO_EPACRONYM",			'ECPGR:ACRONYM' );

/**
 * FAO/WIEWS institute types set.
 *
 * This value defines the FAO/WIEWS institute types set.
 *
 * Cardinality: list.
 */
define( "kENTITY_INST_FAO_TYPE",				'FAO:INST:TYPE' );

/**
 * FAO/WIEWS institute latitude.
 *
 * This value defines the FAO/WIEWS institute latitude, it is defined here since it is not
 * provided in standard format.
 *
 * Cardinality: single.
 */
define( "kENTITY_INST_FAO_LAT",					'FAO:INST:LAT' );

/**
 * FAO/WIEWS institute longitude.
 *
 * This value defines the FAO/WIEWS institute longitude, it is defined here since it is not
 * provided in standard format.
 *
 * Cardinality: single.
 */
define( "kENTITY_INST_FAO_LON",					'FAO:INST:LON' );

/**
 * FAO/WIEWS institute altitude.
 *
 * This value defines the FAO/WIEWS institute altitude, it is defined here since it is not
 * provided in standard format.
 *
 * Cardinality: single.
 */
define( "kENTITY_INST_FAO_ALT",					'FAO:INST:ALT' );


/*=======================================================================================
 *	DEFAULT OBJECT TAGS																	*
 *======================================================================================*/

/**
 * FAO/WIEWS institute PGR activity enumeration.
 *
 * This enumeration indicates that the institute manages plant genetic resources.
 */
define( "kENTITY_INST_FAO_ACT_PGR",				':ACTIVITY:PGR' );

/**
 * FAO/WIEWS institute collection management enumeration.
 *
 * This enumeration indicates that the institute manages a germplasm collection.
 */
define( "kENTITY_INST_FAO_ACT_COLL",			':ACTIVITY:COLL' );


/*=======================================================================================
 *	DEFAULT FAO/WIEWS INSTITUTE DOWNLOAD URL											*
 *======================================================================================*/

/**
 * FAO/WIEWS institute download URL.
 *
 * This enumeration indicates the URL used to download the FAO/WIEWS database export file.
 */
define( "kENTITY_INST_FAO_DOWNLOAD",			'http://apps3.fao.org/wiews/export.zip' );

?>
