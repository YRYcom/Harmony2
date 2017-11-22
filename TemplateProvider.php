<?php
namespace Harmony2;

use Harmony2\Http\Request;

interface TemplateProvider {
	public function compose(Template $template, Request $request);
}