<?php

/*=======================================================================================
 *																						*
 *									Offsets.inc.php										*
 *																						*
 *======================================================================================*/
 
/**
 *	Default offsets.
 *
 * This file contains the definitions of the default offsets or tags used by objects in this
 * library, whenever choosing offsets for {@link CPersistentObject persistent} objects, you
 * should first make sure that they are not among those defined in this file.
 *
 *	@package	MyWrapper
 *	@subpackage	Definitions
 *
 *	@author		Milko A. Škofič <m.skofic@cgiar.org>
 *	@version	1.00 29/05/2009
 *				2.00 23/11/2010
 *				3.00 18/02/2012
 */

/*=======================================================================================
 *	DEFAULT IDENTIFICATION TAGS															*
 *======================================================================================*/

/**
 * Local unique identifier offset.
 *
 * This is the tag that represents the object's local unique identifier, this offset should
 * hold a scalar value which uniquely identifies the object within the collection that holds
 * it.
 *
 * This should not be confused with the {@link kTAG_GID global} identifier, which represents
 * the value or values used by the public to refer to that object.
 *
 * This value should be tightly integrated with the database.
 */
define( "kTAG_LID",								'_id' );

/**
 * Global unique identifier offset.
 *
 * This is the tag that represents the object's global unique identifier, this offset should
 * uniquely identify the object among all collections, it represents a string that may only
 * reference that specific object.
 *
 * This should not be confused with the {@link kTAG_LID local} identifier, which represents
 * the key to the object within the local database.
 */
define( "kTAG_GID",								':GID' );

/**
 * Unique identifier offset.
 *
 * This is the tag that represents the object's unique identifier, this offset is used when
 * the {@link kTAG_LID local} identifier is not the actual value that determines the object
 * unique key. This offset should generally be a unique index.
 */
define( "kTAG_UID",								':UID' );

/*=======================================================================================
 *	DEFAULT REFERENCE TAGS																*
 *======================================================================================*/

/**
 * Synonym offset.
 *
 * This is the offset used to indicate a synonym, a synonym is a string that can be used as
 * a substitute to the term, it may be of several kinds: {@link kTYPE_EXACT exact},
 * {@link kTYPE_BROAD broad}, {@link kTYPE_NARROW narrow} and
 * {@link kTYPE_RELATED related}.
 */
define( "kTAG_REFERENCE_SYNONYM",				':SYNONYM' );

/**
 * This is the offset used to indicate a cross-reference, a cross-reference is a reference
 * to another term in the same container, a sort of synonym, except that it is not a string,
 * but a reference to another term object. Cross-references can be of several kinds:
 * {@link kTYPE_EXACT exact}, {@link kTYPE_BROAD broad},
 * {@link kTYPE_NARROW narrow} and {@link kTYPE_RELATED related}.
 */
define( "kTAG_REFERENCE_XREF",					':XREF' );

/**
 * Identifier reference tag.
 *
 * This is the tag is the offset used to indicate an object unique identifier within an
 * object reference.
 */
define( "kTAG_REFERENCE_ID",					'$id' );

/**
 * Container name reference tag.
 *
 * This tag is the offset used to indicate a container within an object reference.
 */
define( "kTAG_REFERENCE_CONTAINER",				'$ref' );

/**
 * Database name reference tag.
 *
 * This tag is the offset used to indicate a database within an object reference.
 */
define( "kTAG_REFERENCE_DATABASE",				'$db' );

/*=======================================================================================
 *	DEFAULT TAGS																		*
 *======================================================================================*/

/**
 * Class tag.
 *
 * This is the offset that should be used to store the object's class name, it will be used
 * to {@link CMongoUnitObject::NewObject() instantiate} objects when loading them from their
 * containers.
 */
define( "kTAG_CLASS",							':CLASS' );

/**
 * Creation time-stammp.
 *
 * This tag is used as the default offset for indicating a creation time-stamp.
 */
define( "kTAG_CREATED",							':CREATED' );

/**
 * Last modification time-stammp.
 *
 * This tag is used as the default offset for indicating a last modification time-stamp.
 */
define( "kTAG_MODIFIED",						':MODIFIED' );

/**
 * Version tag.
 *
 * This tag is an offset that should be used to represent the object's version, the version
 * is a value that should change each time the object is saved: it can be used to check
 * whether an object was modified since the last time it was read.
 *
 * By default it is an integer incremented each time the object is saved.
 */
define( "kTAG_VERSION",							':VERS' );

/**
 * Type.
 *
 * This tag is used as the default offset for indicating an attribute's data type, in
 * general it is used in a structure in conjunction with the {@link kTAG_DATA data} offset
 * to indicate the data type of the item.
 */
define( "kTAG_TYPE",							':TYPE' );

/**
 * Pattern.
 *
 * This tag is used to describe a pattern, in general this may be applied to terms that are
 * of the {@link kTYPE_STRING string} type which are restricted by a pattern.
 */
define( "kTAG_PATTERN",							':PATTERN' );

/**
 * Kind.
 *
 * This tag is used as the default offset for indicating a kind attribute. A kind is
 * similar to the {@link kTAG_TYPE kTAG_TYPE} attribute, except that in the latter case it
 * qualifies specifically the {@link kTAG_DATA kTAG_DATA} elements, in this case it
 * discriminates the elements of a list.
 */
define( "kTAG_KIND",							':KIND' );

/**
 * Domain.
 *
 * This tag is used as the default offset for indicating a domain attribute. A domain
 * represents what kind of object the current object represents, it should indicate the
 * nature of the instance it represents.
 */
define( "kTAG_DOMAIN",							':DOMAIN' );

/**
 * Category.
 *
 * This tag is used as the default offset for indicating a category attribute. A category
 * represents an area to which the current instance belongs to, it should indicate the main
 * quality of the instance in regards to other instances.
 */
define( "kTAG_CATEGORY",						':CATEGORY' );

/**
 * Cardinality.
 *
 * This tag is used as the default offset for indicating the cardinality of a data
 * attribute, it can take the following values:
 *
 * <ul>
 *	<li><i>{@link kCARD_0_1 kCARD_0_1}</i>: Zero or one, the data is either a scalar or it
 *		may not be present.
 *	<li><i>{@link kCARD_1 kCARD_1}</i>: One, the data is a required scalar.
 *	<li><i>{@link kCARD_ANY kCARD_ANY}</i>: Any, the data may not be present or it may have
 *		many elements; in general this indicates that the data element must be an array.
 * </ul>
 */
define( "kTAG_CARDINALITY",						':CARD' );

/**
 * Unit.
 *
 * This tag is used as the default offset for indicating a unit attribute. A unit is a
 * measurement unit such as centimeters, in general this offset will hold a reference to
 * an object that defines the unit.
 */
define( "kTAG_UNIT",							':UNIT' );

/**
 * Source.
 *
 * This tag is used as the default offset for indicating a source. A source indicates from
 * where an object comes from, it is usually expressed as an URL.
 */
define( "kTAG_SOURCE",							':SOURCE' );

/**
 * Data.
 *
 * This tag is used as the default offset for indicating an attribute's data or content, in
 * general this tag is used in conjunction with the {@link kTAG_TYPE type} or
 * {@link kTAG_KIND kind} offsets when storing lists of items.
 */
define( "kTAG_DATA",							':DATA' );

/**
 * Code offset.
 *
 * This tag is used as the default offset for indicating an attribute's code or acronym.
 */
define( "kTAG_CODE",							':CODE' );

/**
 * Enumeration offset.
 *
 * This tag is used as the default offset for indicating an attribute containing an
 * enumeration code or acronym.
 */
define( "kTAG_ENUM",							':ENUM' );

/**
 * Namespace offset.
 *
 * This tag is used as the default offset for indicating a namespace term reference.
 */
define( "kTAG_NAMESPACE",						':NS' );

/**
 * Term offset.
 *
 * This tag is used as the default offset for indicating a graph node term.
 */
define( "kTAG_TERM",							':TERM' );

/**
 * Tag offset.
 *
 * This tag is used as the default offset for indicating a tag.
 */
define( "kTAG_TAG",								':TAG' );

/**
 * Graph node offset.
 *
 * This tag is used as the default offset for indicating a graph node.
 */
define( "kTAG_NODE",							':NODE' );

/**
 * Graph edge offset.
 *
 * This tag is used as the default offset for indicating a graph edge node.
 */
define( "kTAG_EDGE",							':EDGE' );

/**
 * Subject offset.
 *
 * This tag is used as the default offset for indicating a subject term or node.
 */
define( "kTAG_SUBJECT",							':SUBJECT' );

/**
 * Predicate offset.
 *
 * This tag is used as the default offset for indicating a predicate term or node.
 */
define( "kTAG_PREDICATE",						':PREDICATE' );

/**
 * Object offset.
 *
 * This tag is used as the default offset for indicating an object term or node.
 */
define( "kTAG_OBJECT",							':OBJECT' );

/**
 * Path offset.
 *
 * This tag is used as the default offset for indicating a path.
 */
define( "kTAG_PATH",							':PATH' );

/**
 * Title offset.
 *
 * This tag is used as the default offset for indicating a title.
 */
define( "kTAG_TITLE",							':TITLE' );

/**
 * Name offset.
 *
 * This tag is used as the default offset for indicating an attribute's name.
 */
define( "kTAG_NAME",							':NAME' );

/**
 * Description.
 *
 * This tag is used as the default offset for indicating an attribute's description.
 */
define( "kTAG_DESCRIPTION",						':DESCR' );

/**
 * Definition.
 *
 * This tag is used as the default offset for indicating an attribute's definition.
 */
define( "kTAG_DEFINITION",						':DEF' );

/**
 * Examples.
 *
 * This tag is used as the default offset for indicating an attribute containing a list of
 * examples.
 */
define( "kTAG_EXAMPLES",						':EXAMPLE' );

/**
 * Language.
 *
 * This tag is used as the default offset for indicating the language of an attribute, it
 * should be the 2 character ISO 639 language code.
 */
define( "kTAG_LANGUAGE",						':LANGUAGE' );

/**
 * Entity.
 *
 * This tag is used as the namespace and default offset for entities.
 */
define( "kTAG_ENTITY",							':ENTITY' );

/**
 * Status.
 *
 * This tag is used as the default offset for indicating an attribute's status or state, it
 * will generally be an array of tags defining the various states associated with the
 * object.
 */
define( "kTAG_STATUS",							':STATUS' );

/**
 * Role.
 *
 * This tag is used as the default offset for indicating a role or function, this is
 * generally associated with the capabilities or permissions of users.
 */
define( "kTAG_ROLE",							':ROLE' );

/**
 * Annotation.
 *
 * This tag is used as the default offset for indicating a list of annotations, in general
 * it will contain a list of key/value pairs.
 */
define( "kTAG_ANNOTATION",						':ANNOTATION' );

/**
 * Dataset.
 *
 * This tag is used as the default offset for indicating a dataset.
 */
define( "kTAG_DATASET",							':DATASET' );

/**
 * References tag.
 *
 * This is the tag that represents the list of references of an object, it is an array of
 * object references in which each element may either be the reference itself or the
 * following structure:
 *
 * <ul>
 *	<li><i>{@link kTAG_KIND kTAG_KIND}</i>: Relation predicate, it can either be an object
 *		reference or a string.
 *	<li><i>{@link kTAG_DATA kTAG_DATA}</i>: Relation object, it will be a reference to an
 *		object in which the following elements may appear:
 *	 <ul>
 *		<li><i>{@link kTAG_REFERENCE_ID kTAG_REFERENCE_ID}</i>: The unique identifier of the
 *			referenced object.
 *		<li><i>{@link kTAG_REFERENCE_CONTAINER kTAG_REFERENCE_CONTAINER}</i>: The
 *			{@link CContainer container} name.
 *		<li><i>{@link kTAG_REFERENCE_DATABASE kTAG_REFERENCE_DATABASE}</i>: The database
 *			name.
 *		<li><i>{@link kTAG_CLASS kTAG_CLASS}</i>: The object class name.
 *	 </ul>
 * </ul>
 */
define( "kTAG_REFS",							':REFS' );

/**
 * Generic count tag.
 *
 * This is the tag that represents a generic count.
 */
define( "kTAG_COUNT",							':COUNT' );

/**
 * References count tag.
 *
 * This is the tag that represents the references count of an object, it is an integer
 * representing the number of times the object was referenced. It is used in general to
 * count the number of term data instances.
 */
define( "kTAG_REF_COUNT",						':REF-COUNT' );

/**
 * Tags.
 *
 * This tag represents a list of attribute tags, it is generally used to collect the list of
 * tags used in an object.
 */
define( "kTAG_TAGS",							':TAGS' );

/**
 * Data tags.
 *
 * This tag represents a list of data tags, it is generally used to collect the list of
 * data tags that reference the current object.
 */
define( "kTAG_DTAGS",							':DTAGS' );

/**
 * Edge terms path.
 *
 * This tag represents a graph edge node by using its related terms as a path in the form of
 * a string containing the <i>SUBJECT/PREDICATE/OBJECT</i> path constituted by the term
 * identifier elements.
 */
define( "kTAG_EDGE_TERM",						':TEDGE' );

/**
 * Edge nodes path.
 *
 * This tag represents a graph edge node by using its related nodes and predicate term as a
 * path in the form of a string containing the <i>SUBJECT/PREDICATE/OBJECT</i> path in which
 * the subject and object elements are represented by the respective node identifiers, and
 * the predicate element is represented by the edge term identifier.
 */
define( "kTAG_EDGE_NODE",						':NEDGE' );

/**
 * Default tag.
 *
 * This is the tag that represents the default entry related to the current one. There may be
 * cases in which an object is interchangeable with many others, in enumerations, for
 * instance: in this case we can use this tag to point to the default or used instance.
 */
define( "kTAG_DEFAULT",							':DEFAULT' );

/**
 * Preferred tag.
 *
 * This is the tag that represents the preferred entry related to the current one. There may
 * be cases in which an object may be obsolete, but still in use, this tag refers to the
 * object that should be used in place of the current one. This tag  expects the value of
 * the {@link kTAG_LID native} identifier of the preferred object here.
 */
define( "kTAG_PREFERRED",						':PREFERRED' );

/**
 * Valid tag.
 *
 * This is the tag that represents the valid entry related to the current one. There may be
 * cases in which it is not an option to delete objects, so we create a new one and the old
 * one will point to the new one. This tag represents that property and it expects the value
 * of the {@link kTAG_LID native} identifier of the new object here.
 */
define( "kTAG_VALID",							':VALID' );

/**
 * Provided tag.
 *
 * This tags provided elements as opposed to {@link kTAG_GENERATED generated} elements.
 */
define( "kTAG_PROVIDED",						':PROVIDED' );

/**
 * Generated tag.
 *
 * This tags generated elements as opposed to {@link kTAG_PROVIDED provided} elements.
 */
define( "kTAG_GENERATED",						':GENERATED' );

/**
 * Incoming tag.
 *
 * This is the tag that represents the incoming direction, it can be used for tagging items
 * that point to the current object.
 */
define( "kTAG_IN",								':IN' );

/**
 * Outgoing tag.
 *
 * This is the tag that represents the outgoing direction, it can be used for tagging items
 * that the current object points to.
 */
define( "kTAG_OUT",								':OUT' );

/**
 * Manager offset.
 *
 * This tag is used as the default offset for indicating the manager of the current object.
 */
define( "kTAG_MANAGER",							':MANAGER' );

/*=======================================================================================
 *	DEFAULT PROPERTY OFFSETS															*
 *======================================================================================*/

/**
 * Password offset.
 *
 * This is the tag that represents a password; the value is a string.
 */
define( "kOFFSET_PASSWORD",						':PASS' );

/**
 * Mail offset.
 *
 * This is the tag that represents a mailing address, the value may either be a string or
 * an array.
 */
define( "kOFFSET_MAIL",							':MAIL' );

/**
 * E-mail offset.
 *
 * This is the tag that represents an e-mail, the value may either be a string or an array.
 */
define( "kOFFSET_EMAIL",						':EMAIL' );

/**
 * Telephone offset.
 *
 * This is the tag that represents a telephone number, the value may either be a string or
 * an array.
 */
define( "kOFFSET_PHONE",						':PHONE' );

/**
 * Telefax offset.
 *
 * This is the tag that represents a telefax number, the value may either be a string or
 * an array.
 */
define( "kOFFSET_FAX",							':FAX' );

/**
 * URL.
 *
 * This is the tag that represents an URL, link or web address.
 */
define( "kOFFSET_URL",							':URL' );

/**
 * Acronym.
 *
 * This is the tag that represents a list of acronyms.
 */
define( "kOFFSET_ACRONYM",						':ACRONYM' );

/**
 * Version.
 *
 * This is the tag that represents the version, it should not be confused with the
 * {@link kTAG_VERSION kTAG_VERSION} which is automatically managed in the class library:
 * this offset represents the actual version.
 */
define( "kOFFSET_VERSION",						':VERSION' );

/**
 * Namespace offset.
 *
 * This tag is used as the default offset for indicating a namespace name or acronym.
 */
define( "kOFFSET_NAMESPACE",					':NAMESPACE' );

/**
 * Image offset.
 *
 * This tag is used as the default offset for indicating an images list.
 */
define( "kOFFSET_IMAGE",						':IMAGE' );

/**
 * File offset.
 *
 * This tag is used as the default offset for indicating a file reference.
 */
define( "kOFFSET_FILE",							':FILE' );

/**
 * Files list offset.
 *
 * This tag is used as the default offset for indicating a list of files.
 */
define( "kOFFSET_FILES",						':FILES' );

/**
 * Columns list offset.
 *
 * This tag is used as the default offset for indicating a list of columns.
 */
define( "kOFFSET_COLS",							':COLS' );

/*=======================================================================================
 *	DEFAULT MAIL PROPERTY OFFSETS														*
 *======================================================================================*/

/**
 * Place offset.
 *
 * This is the tag that represents a place or named location.
 */
define( "kOFFSET_PLACE",						':PLACE' );

/**
 * Care of offset.
 *
 * This is the tag that represents a care of address reference.
 */
define( "kOFFSET_CARE",							':CARE' );

/**
 * Street offset.
 *
 * This is the tag that represents a place or named location.
 */
define( "kOFFSET_STREET",						':STREET' );

/**
 * ZIP offset.
 *
 * This is the tag that represents a ZIP code.
 */
define( "kOFFSET_ZIP_CODE",						':ZIP' );

/**
 * City offset.
 *
 * This is the tag that represents a city name.
 */
define( "kOFFSET_CITY",							':CITY' );

/**
 * Province offset.
 *
 * This is the tag that represents a province name or code.
 */
define( "kOFFSET_PROVINCE",						':PROV' );

/**
 * Country offset.
 *
 * This is the tag that represents an ISO3166 3 character country code.
 */
define( "kOFFSET_COUNTRY",						':COUNTRY' );

/**
 * Full data offset.
 *
 * This is the tag that represents the full data as a string.
 */
define( "kOFFSET_FULL",							':FULL' );

/*=======================================================================================
 *	DEFAULT PREDICATES																	*
 *======================================================================================*/

/**
 * IS-A.
 *
 * This is the tag that defines the IS-A predicate.
 *
 * This predicate is equivalent to a subclass, it can be used to relate a term to the
 * default category to which it belongs within the current ontology.
 */
define( "kPRED_IS_A",							':IS-A' );

/**
 * PART-OF.
 *
 * This is the tag that defines the PART-OF predicate.
 *
 * This predicate indicates that the subject is part of the object.
 */
define( "kPRED_PART_OF",						':PART-OF' );

/**
 * COMPONENT-OF.
 *
 * This is the tag that defines the COMPONENT-OF predicate.
 *
 * This predicate indicates that the subject is a component of the object.
 */
define( "kPRED_COMPONENT_OF",					':COMPONENT-OF' );

/**
 * SCALE-OF.
 *
 * This is the tag that defines the SCALE-OF predicate.
 *
 * This predicate is used to relate a term that can be used to annotate data with its method
 * term or trait term.
 */
define( "kPRED_SCALE_OF",						':SCALE-OF' );

/**
 * METHOD-OF.
 *
 * This is the tag that defines the METHOD-OF predicate.
 *
 * This predicate is used to relate a term that defines a measurement method to the trait
 * term.
 */
define( "kPRED_METHOD_OF",						':METHOD-OF' );

/**
 * ENUM-OF.
 *
 * This is the tag that defines the ENUM-OF predicate.
 *
 * This predicate is used to relate {@link kTAG_ENUM enumeration} terms, this edge type
 * relates enumeration terms in a hierarchy.
 */
define( "kPRED_ENUM_OF",						':ENUM-OF' );

/*=======================================================================================
 *	DEFAULT CARDINALITIES																*
 *======================================================================================*/

/**
 * Zero or one.
 *
 * This is the tag that defines a cardinality of zero or one.
 */
define( "kCARD_0_1",							':01' );

/**
 * One.
 *
 * This is the tag that defines a cardinality of exactly one.
 */
define( "kCARD_1",								':1' );

/**
 * Any.
 *
 * This is the tag that defines a cardinality of any kind.
 */
define( "kCARD_ANY",							':ANY' );

/*=======================================================================================
 *	DEFAULT TAXON ATTRIBUTES															*
 *======================================================================================*/

/**
 * Rank.
 *
 * This is the tag that defines a taxon rank.
 */
define( "kTAXON_RANK",							':RANK' );

/**
 * Epithet.
 *
 * This is the tag that defines a taxon epithet.
 */
define( "kTAXON_EPITHET",						':EPITH' );

/**
 * Authority.
 *
 * This is the tag that defines a taxon authority.
 */
define( "kTAXON_AUTHORITY",						':AUTH' );

/**
 * Taxon.
 *
 * This is the tag that defines a full taxon epithet.
 */
define( "kTAXON_NAME",							':TAXON' );

/*=======================================================================================
 *	DEFAULT IMAGE ATTRIBUTES															*
 *======================================================================================*/

/**
 * Thumbnail flag.
 *
 * A flag is the image of a flag or an icon symbol representing an object, the thumbnail
 * flag is a small sized version of this image.
 */
define( "kIMAGE_THUMB_FLAG",					':IMG-THMB-FLAG' );

/**
 * Medium flag.
 *
 * A flag is the image of a flag or an icon symbol representing an object, the medium
 * flag is a medium sized version of this image.
 */
define( "kIMAGE_MED_FLAG",						':IMG-MED-FLAG' );

/**
 * Vector flag.
 *
 * A flag is the image of a flag or an icon symbol representing an object, the vector
 * flag is a vector version of this image which can be resized at will.
 */
define( "kIMAGE_VECT_FLAG",						':IMG-VECT-FLAG' );


?>
