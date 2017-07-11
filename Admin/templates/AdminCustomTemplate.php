<?php

/**
 * @package   Admin
 * @copyright 2017 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminCustomTemplate extends SiteAbstractTemplate
{
	// {{{ public function display()

	public function display(SiteLayoutData $data)
	{
		echo $data->content;
	}

	// }}}
}

?>
