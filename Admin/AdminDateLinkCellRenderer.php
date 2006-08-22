<?php

require_once 'Admin/AdminTitleLinkCellRenderer.php';
require_once 'Swat/SwatDateCellRenderer.php';

/**
 * Hybrid swat date/admin title link cell renderer for dates
 *
 * @package   veseys2
 * @copyright 2006 silverorange
 */
class AdminDateLinkCellRenderer extends AdminTitleLinkCellRenderer 
{
	// {{{ public properties

	/**
	 * Date to render
	 *
	 * This may either be a Date object, or may be an ISO-formatted date string
	 * that can be passed into the SwatDate constructor.
	 *
	 * @var string|SwatDate|Date
	 */
	public $date = null;

	/**
	 * Format
	 *
	 * Either a {@link SwatDate} format mask, or class constant. Class
	 * constants are preferable for sites that require translation.
	 *
	 * @var mixed
	 */
	public $format = SwatDate::DF_DATE_TIME;

	/**
	 * The time zone to render the date in
	 *
	 * The time zone may be specified either as a time zone identifier valid
	 * for PEAR::Date_TimeZone or as a Date_TimeZone object. If the render
	 * time zone is null, no time zone conversion is performed.
	 *
	 * @var string|Date_TimeZone 
	 */
	public $display_time_zone = null;

	// }}}
	// {{{ protected function getText()

	protected function getText()
	{
		$date_renderer = new SwatDateCellRenderer();
		$date_renderer->date = $this->date;
		$date_renderer->format = $this->format;
		$date_rendeer->display_time_zone = $this->display_time_zone;

		ob_start();
		$date_renderer->render();
		return ob_get_clean();
	}

	// }}}
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return parent::getText();
	}

	// }}}
}

?>