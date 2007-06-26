<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/dataobjects/AdminComponent.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminComponents
 *
 * @package   Admin
 * @copyright 2005-2007 silverorange
 */
class AdminAdminComponentEdit extends AdminDBEdit
{
	// {{{ private properties

	/**
	 * @var AdminComponent
	 */
	private $edit_component;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initComponent();

		$this->ui->loadFromXML(dirname(__FILE__).'/edit.xml');

		$section_flydown = $this->ui->getWidget('section');
		$section_flydown->addOptionsByArray(SwatDB::getOptionArray(
			$this->app->db, 'AdminSection', 'title', 'id', 'displayorder'));

		$group_list = $this->ui->getWidget('groups');
		$group_list_options = SwatDB::getOptionArray($this->app->db,
			'AdminGroup', 'title', 'id', 'title');

		$group_list->addOptionsByArray($group_list_options);
	}

	// }}}
	// {{{ private function initComponent()

	private function initComponent()
	{
		$this->edit_component = new AdminComponent();
		$this->edit_component->setDatabase($this->app->db);

		if ($this->id !== null){
			if (!$this->edit_component->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf(Admin::_('Component with id "%s" not found.'),
						$this->id));
			}
		}
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		$shortname = $this->ui->getWidget('shortname');

		$sql = sprintf('select count(shortname) from AdminComponent
			where shortname = %s and id %s %s',
			$this->app->db->quote($shortname->value, 'text'),
			SwatDB::equalityOperator($this->id, true),
			$this->app->db->quote($this->id, 'integer'));

		$count = SwatDB::queryOne($this->app->db, $sql);

		if ($count > 0) {
			$message = new SwatMessage(
				Admin::_('Shortname already exists and must be unique.'),
				SwatMessage::ERROR);

			$shortname->addMessage($message);
		}
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array(
			'title',
			'shortname',
			'section',
			'show',
			'enabled',
			'description',
		));

		$this->edit_component->title       = $values['title'];
		$this->edit_component->shortname   = $values['shortname'];
		$this->edit_component->section     = $values['section'];
		$this->edit_component->show        = $values['show'];
		$this->edit_component->enabled     = $values['enabled'];
		$this->edit_component->description = $values['description'];

		$this->edit_component->save();

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'AdminComponentAdminGroupBinding',
			'component', $this->id, 'groupnum', $group_list->values,
			'AdminGroup', 'id');

		$message = new SwatMessage(sprintf(
			Admin::_('Component “%s” has been saved.'),
			$this->edit_component->title));

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->edit_component));

		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db,
			'AdminComponentAdminGroupBinding', 'groupnum', 'component',
			$this->id);
	}

	// }}}
}

?>
