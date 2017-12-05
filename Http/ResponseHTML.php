<?php
namespace Harmony2\Http;

use Harmony2\DebugBar;
use Harmony2\Template;

class ResponseHTML implements Response
{
	private $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	/**
	 * @return Template
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @param bool $return
	 * @return string|true
	 */
	public function send( $return = false)
	{

		$this->template->parse();

		if ($return == true) {
			$body = ob_get_clean();
			return $body;
		}

		DebugBar::parse();
		return true;
	}


}