<?php

require_once 'Admin/AdminApplication.php';
require_once 'Admin/dataobjects/AdminComponent.php';
require_once 'Admin/dataobjects/AdminComponentWrapper.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/exceptions/AdminNoAccessException.php';
require_once 'Site/SiteObject.php';

/**
 * Page request
 *
 * @package   Admin
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminPageRequest extends SiteObject
{
	// {{{ protected properties

	protected $source;
	protected $component;
	protected $subcomponent;
	protected $title;
	protected $app;
	protected $class_prefix;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new page request and resolves the component for the request
	 *
	 * @param AdminApplication $app the admin application creating the page
	 *                               request.
	 * @param string $source the source of the page request.
	 */
	public function __construct(AdminApplication $app, $source)
	{
		$this->source = $source;
		$this->app = $app;

		if ($this->source == '')
			$this->source = $this->app->getFrontSource();

		$allow_reset_password =
			(boolean)$app->config->admin->allow_reset_password;

		if ($this->app->session->isLoggedIn()) {
			$source_exp = explode('/', $this->source);

			if (count($source_exp) == 1) {
				$this->component = $this->source;
				$this->subcomponent = $this->app->getDefaultSubComponent();
			} elseif (count($source_exp) == 2) {
				list($this->component, $this->subcomponent) = $source_exp;
			} else {
				throw new AdminNotFoundException(sprintf(Admin::_(
					"Invalid source '%s'."),
					$this->source));
			}

			if ($this->component == 'AdminSite') {
				$admin_titles = $this->getAdminSiteTitles();

				if (isset($admin_titles[$this->subcomponent])) {
					$this->title = $admin_titles[$this->subcomponent];
				} else {
					throw new AdminNotFoundException(
						sprintf(
							Admin::_("Component not found for source '%s'."),
							$this->source
						)
					);
				}

			} else {
				$component = new AdminComponent();
				$component->setDatabase($this->app->db);
				if (!$component->loadFromShortname($this->component)) {
					throw new AdminNotFoundException(sprintf(Admin::_(
						"Component not found for source '%s'."),
						$this->source));
				} else {
					$this->title = $component->title;
				}

				if (!$this->app->session->user->hasAccess($component)) {
					$user = $this->app->session->user;
					throw new AdminNoAccessException(sprintf(Admin::_(
						"Access to the requested component is forbidden for ".
						"user '%s'."), $user->id), 0, $user);
				}
			}
		} else {
			switch ($this->source) {
			case 'AdminSite/ChangePassword':
				$this->subcomponent = 'ChangePassword';
				break;

			case 'AdminSite/ForgotPassword':
				if (!$allow_reset_password) {
					throw new AdminNotFoundException(sprintf(Admin::_(
						"Component not found for source '%s'."),
						$this->source));
				}

				$this->subcomponent = 'ForgotPassword';
				break;

			case 'AdminSite/ResetPassword':
				$this->subcomponent = 'ResetPassword';
				break;

			default:
				$this->subcomponent = 'Login';
				break;
			}

			$admin_titles = $this->getAdminSiteTitles();
			$this->title = $admin_titles[$this->subcomponent];
			$this->component = 'AdminSite';
		}
	}

	// }}}
	// {{{ public function getFilename()

	/**
	 * Finds the PHP file containing the class definition of the current
	 * sub-component
	 */
	public function getFilename()
	{
		$classfile = $this->component.'/'.$this->subcomponent.'.php';
		$file = null;
		$path = $this->app->getDefaultComponentIncludePath();

		if (file_exists($path.'/'.$classfile)) {
			$file = $path.'/'.$classfile;
		} else {
			$include_paths = explode(':', ini_get('include_path'));
			$component_include_paths = $this->app->getComponentIncludePaths();

			foreach ($component_include_paths as $prefix => $path) {
				foreach ($include_paths as $include_path) {
					if (file_exists($include_path.'/'.$path.'/'.$classfile)) {
						$file = $path.'/'.$classfile;
						$this->class_prefix = $prefix;
						break 2;
					}
				}
			}
		}

		return $file;
	}

	// }}}
	// {{{ public function getClassName()

	public function getClassName()
	{
		$class_name = ($this->class_prefix === null) ?
			$this->component.$this->subcomponent :
			$this->class_prefix.$this->component.$this->subcomponent;

		return $class_name;
	}

	// }}}
	// {{{ public function getTitle()

	public function getTitle()
	{
		return $this->title;
	}

	// }}}
	// {{{ public function getComponent()

	public function getComponent()
	{
		return $this->component;
	}

	// }}}
	// {{{ public function getSubComponent()

	public function getSubComponent()
	{
		return $this->subcomponent;
	}

	// }}}
	// {{{ public function getSource()

	public function getSource()
	{
		return $this->source;
	}

	// }}}
	// {{{ protected function getAdminSiteTitles()

	public function getAdminSiteTitles()
	{
		return array(
			'Profile'        => Admin::_('Edit User Profile'),
			'Logout'         => Admin::_('Logout'),
			'Login'          => Admin::_('Login'),
			'Exception'      => Admin::_('Exception'),
			'Front'          => Admin::_('Index'),
			'ChangePassword' => Admin::_('Change Password'),
			'ResetPassword'  => Admin::_('Update Password'),
			'ForgotPassword' => Admin::_('Reset Forgotten Password'),
			'MenuViewServer' => '',
		);
	}

	// }}}
}

?>
