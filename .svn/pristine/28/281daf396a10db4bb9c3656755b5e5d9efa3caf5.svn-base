<?php

namespace Harmony2;


class CStringDecoupe
{
	protected $iCurrentPos;
	protected $strChaine;

	public function __construct($strChaine)
	{
		$this->iCurrentPos = 0;
		$this->strChaine = $strChaine;
	}
	public function __destruct()
	{
	}

	public function RAZ()
	{
		$this->iCurrentPos = 0;
	}
	public function getPos()
	{
		return $this->iCurrentPos;
	}
	public function Decoupe($strDelimiterStart, $strDelimiterEnd)
	{
		// return CStringDecoupe::QuickDecoupe($this->strChaine, $strDelimiterStart, $strDelimiterEnd, $this->iCurrentPos);
		if( ($pd1 = strpos($this->strChaine, $strDelimiterStart, $this->iCurrentPos)) !== false)
		{
			$pd1 += strlen($strDelimiterStart);
			$pf1 = strpos($this->strChaine, $strDelimiterEnd, $pd1);
			$this->iCurrentPos = $pf1 + strlen($strDelimiterEnd);
			return substr($this->strChaine, $pd1, $pf1 - $pd1);
		}
		else
		{
			return false;
		}
	}
	// alias pour QuickDecoupe
	public static function QD($strChaine, $strDelimiterStart, $strDelimiterEnd, &$iCurrentPos)
	{
		return CStringDecoupe::QuickDecoupe($strChaine, $strDelimiterStart, $strDelimiterEnd, $iCurrentPos);
	}
	public static function QuickDecoupe($strChaine, $strDelimiterStart, $strDelimiterEnd, &$iCurrentPos)
	{
		 if ( strlen( $strChaine ) > $iCurrentPos && ( $pd1 = strpos( $strChaine, $strDelimiterStart, $iCurrentPos ) ) !== FALSE )
		 {
				$pd1 += strlen($strDelimiterStart);
				$pf1 = strpos($strChaine, $strDelimiterEnd, $pd1);
				if($pf1 === false)
					return false;

				$iCurrentPos = $pf1 + strlen($strDelimiterEnd) + 1;
				return substr($strChaine, $pd1, $pf1 - $pd1);
			}
			else
			{
				return false;
			}
	}
	public static function QuickDecoupeME($strChaine, $strDelimiterStart, $strDelimiterEnd, &$iCurrentPos, $count)
	{
		 if( ($pd1 = strpos($strChaine, $strDelimiterStart, $iCurrentPos)) !== false)
		 {
				$pd1 += strlen($strDelimiterStart);

				$use = $pd1;
			 $pf1 = 0;
				while($count > 0)
				{
					$pf1 = strpos($strChaine, $strDelimiterEnd, $use);
					if($pf1 === false)
						return false;

					$use = $pf1 + strlen($strDelimiterEnd);
					$count--;
				}

				$iCurrentPos = $pf1 + strlen($strDelimiterEnd);
				return substr($strChaine, $pd1, $pf1 - $pd1);
			}
			else
			{
				return false;
			}
	}
	public static function RemoveTag($strChaine, $strDelimiterStart, $strDelimiterEnd)
	{
		$val = CStringDecoupe::QDS($strChaine, $strDelimiterStart, $strDelimiterEnd);
		if($val === false)
			return $strChaine;

		return str_replace($strDelimiterStart . $val . $strDelimiterEnd, '', $strChaine);
	}
	public static function IsTag($strTag, $bTerm, $legacy_tag = false)
	{
		$str = strpos($strTag, '<');
		$str_term = strpos($strTag, '/');
		$end = strrpos($strTag, '>');

		if($str === false || $str > 0) {
			return 0;
		}

		if($legacy_tag) {
			if($end === false || $end != strlen($strTag) - 1) {
				return 0;
			}
		}

		if($bTerm === true && $str_term != 1) {
			return -1;
		}

		return 1;
	}
	public static function QDSTag($strChaine, $strDelimiterStart, $strDelimiterEnd, &$offset = 0, $legacy_tag = false, $not_a_tag = false)
	{
		if ( ! $not_a_tag )
		{
			$chk1 = CStringDecoupe::IsTag($strDelimiterStart, false, $legacy_tag);
			$chk2 = CStringDecoupe::IsTag($strDelimiterEnd, true, $legacy_tag);

			if($chk1 == 0) {
				die($strDelimiterStart . ' n\'est pas un tag HTML valide...' . "\n");
				//return false;
			}
			if($chk2 == 0) {
				die($strDelimiterEnd . ' n\'est pas un tag HTML valide...' . "\n");
				//return false;
			}
			else if($chk2 == -1) {
				die($strDelimiterEnd . ' n\'est pas un tag HTML de fin valide...' . "\n");
				//return false;
			}

			$offset = strpos($strChaine, $strDelimiterStart, $offset);
			if($offset === false) {
				return false;
			}
		}

		$safe_tag = $strDelimiterStart;

		if ( ! $not_a_tag )
		{
			if(strpos($strDelimiterStart, ' ') !== false) {
				$safe_tag = CStringDecoupe::QuickDecoupeFromBegin($strDelimiterStart, 0, ' ');
			}
			else {
				$safe_tag = CStringDecoupe::QuickDecoupeFromBegin($strDelimiterStart, 0, '>');
			}

			if ( ! $safe_tag )
			{
				 $safe_tag = $strDelimiterStart;
			}
		}

		$count = 1;
		$offset_end = 0;
		$bContinue = true;
		$string = '';
		while($bContinue !== false)
		{
			$store = $offset;
			$string = CStringDecoupe::QuickDecoupeME($strChaine, $safe_tag, $strDelimiterEnd, $store, $count);
			if($string === false)
				return false;

			$pos_end = strpos($string, $safe_tag, $offset_end);
			if($pos_end === false) {
				$bContinue = false;
			}
			else {
				$count++;
				$offset_end = $pos_end + strlen($safe_tag);
			}
		}
		$offset += strlen($string);

		if($legacy_tag)
			$string = trim(CStringDecoupe::QuickDecoupeToEnd($string, '>'));

		return $string;
	}
	public static function QDS($strChaine, $strDelimiterStart, $strDelimiterEnd)
	{
		return CStringDecoupe::QuickDecoupeSingle($strChaine, $strDelimiterStart, $strDelimiterEnd);
	}
	public static function QuickDecoupeSingle($strChaine, $strDelimiterStart, $strDelimiterEnd)
	{
		$pos = 0;
		return CStringDecoupe::QuickDecoupe($strChaine, $strDelimiterStart, $strDelimiterEnd, $pos);
	}
	public static function QuickDecoupeSingleME($strChaine, $strDelimiterStart, $strDelimiterEnd, $count)
	{
		$pos = 0;
		return CStringDecoupe::QuickDecoupeME($strChaine, $strDelimiterStart, $strDelimiterEnd, $pos, $count);
	}
	public static function QDBegin($strChaine, $startPos, $strDelimiterEnd, &$iCurrentPos = 0)
	{
		return CStringDecoupe::QuickDecoupeFromBegin($strChaine, $startPos, $strDelimiterEnd, $iCurrentPos);
	}
	public static function QuickDecoupeFromBegin($strChaine, $startPos, $strDelimiterEnd, &$iCurrentPos = 0)
	{
		$pd1 = $startPos + $iCurrentPos;

			$pf1 = strpos($strChaine, $strDelimiterEnd, $pd1);
			$iCurrentPos = $pf1 + strlen($strDelimiterEnd);
			return substr($strChaine, $pd1, $pf1 - $pd1);
	}
	public static function QDEnd( $str, $needle, &$offset = 0 )
	{
		return CStringDecoupe::QuickDecoupeToEnd($str, $needle, $offset);
	}
	public static function QuickDecoupeToEnd( $str, $needle, &$offset = 0 )
	{
		if ( ( $pos = strpos( $str, $needle, $offset ) ) !== false )
		{
			$pos	+= strlen( $needle );
				$offset += $pos;

				return substr( $str, $pos );
		}

		return false;
	}
	public static function RemoveSingle($strChaine, $strDelimiterStart, $strDelimiterEnd)
	{
		$inner = CStringDecoupe::QuickDecoupeSingle($strChaine, $strDelimiterStart, $strDelimiterEnd);
		if($inner === false)
			return $strChaine;

		$strChaine = str_replace($strDelimiterStart . $inner . $strDelimiterEnd, '', $strChaine);
		return $strChaine;
	}
	public static function QuickDecoupeTag($strChaine, $tag, $offset)
	{
		$start = '<' . $tag;
		$end = '</' . $tag . '>';

		$reference = strpos($strChaine, $start, $offset);
		$current = $reference + 1;

		$pos_next = strpos($strChaine, $start, $current);
		$pos_fin = strpos($strChaine, $end, $current);

		while($pos_next < $pos_fin && $pos_next !== false && $pos_fin !== false)
		{
			$current = $pos_fin + 1;

			$pos_next = strpos($strChaine, $start, $current);
			$pos_fin = strpos($strChaine, $end, $current);
		}

		if($pos_fin === false)
			return '';

		return substr($strChaine, $reference, $pos_fin - $reference);
	}
}


?>
