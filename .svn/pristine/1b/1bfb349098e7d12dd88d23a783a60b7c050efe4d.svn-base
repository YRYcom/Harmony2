<?php

namespace Harmony2\Tools;

/**
 * Created by Yannick LALLEAU.
 * Date: 09/02/2017
 * Time: 15:01
 */
class CliProgressBar
{
	static $oldDisplayValue;
	static $maxLengthDisplay=0;

	public static function display($done, $total, $info = "", $width = 50)
	{
		$perc = round(($done * 100) / $total);
		if($perc != self::$oldDisplayValue) {
			self::$oldDisplayValue = $perc;
			$bar = round(($width * $perc) / 100);
			$display = sprintf("%s%%[%s>%s]%s\r", $perc, str_repeat("=", $bar), str_repeat(" ", $width - $bar), $info);
			if(self::$maxLengthDisplay < strlen($display))
				self::$maxLengthDisplay = strlen($display);
			echo $display;

		}
	}

	public static function reset()
	{
		echo sprintf("%s\r",str_repeat(" ", self::$maxLengthDisplay));
	}
}