<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'Admin/dataobjects/AdminSection.php';

/**
 * Component to perform a particular administration task
 *
 * Components are the main organizational unit in the Admin package. Each
 * component is composed of a set of AdminPage objects that work together to
 * administer an item.
 *
 * @package   Admin
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminComponent extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * Unique identifier
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * Shortname of this component
	 *
	 * This shortname is used for building Admin page URIs.
	 *
	 * @var string
	 */
	public $shortname;

	/** 
	 * Title of this component
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Optional description of this component
	 *
	 * @var string
	 */
	public $description;

	/** 
	 * Order of display of this component relative to other components in this
	 * component's section
	 *
	 * @var string
	 */
	public $displayorder;

	/**
	 * Whether or not this component is enabled
	 *
	 * If a component is not enabled, it is inaccessible to all users. The
	 * <i>$enabled</i> property overrides the {@link AdminComponent::$show}
	 * property.
	 *
	 * @var boolean
	 */
	public $enabled;

	/**
	 * Whether or not links to this component should be shown in the admin
	 *
	 * This property does not affect the ability of users to load this
	 * component. It only affects whether or not links to this component are
	 * displayed.
	 *
	 * @var boolean
	 */
	public $show;

	// }}}
	// {{{ public function loadFromShortname()

	/**
	 * Loads an admin component by its shortname
	 *
	 * @param string $shortname the shortname of the admin component to load.
	 *
	 * @return boolean true if loading this component was successful and
	 *                  false if a component with the given shortname does not
	 *                  exist.
	 */
	public function loadFromShortname($shortname)
	{
		$this->checkDB();

		$row = null;

		if ($this->table !== null) {
			$sql = sprintf('select * from %s where shortname = %s',
			$this->table,
			$this->db->quote($shortname, 'text'));

			$rs = SwatDB::query($this->db, $sql, null);
			$row = $rs->fetchRow(MDB2_FETCHMODE_ASSOC);
		}

		if ($row === null)
			return false;

		$this->initFromRow($row);
		$this->generatePropertyHashes();
		return true;
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'AdminComponent';
		$this->id_field = 'integer:id';
		$this->registerInternalProperty('section', 'AdminSection');
	}

	// }}}
}

?>
