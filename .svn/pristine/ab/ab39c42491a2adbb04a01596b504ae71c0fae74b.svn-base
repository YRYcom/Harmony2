<?php
namespace Harmony2\Http;

use Harmony2\Template;

class Response404 implements Response {
    public function send($return = false) {
        Header::status(404);
        $template = new Template();
        $template->set_filename('default/404.tpl');
        $template->parse();
				if ($return == true) {
					return ob_get_clean();
				}
				return true;
    }
}