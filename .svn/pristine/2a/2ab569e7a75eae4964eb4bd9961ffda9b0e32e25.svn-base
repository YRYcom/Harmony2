<?php
/**
 * Created by Yannick LALLEAU.
 * Date: 01/02/2017
 * Time: 09:27
 */

namespace Harmony2;


class RunParallelTasks
{

	private $pids;

	public function __construct()
	{
		$this->pids = [];
	}

	public function run($task)
	{
		$out = [];
		exec($task . " > /dev/null & echo $!", $out);
		$pid = 0;
		if (isset($out[0]))
			$pid = (int)$out[0];
		if ($pid == 0)
			return 0;
		$this->pids[] = $pid;
		return $pid;
	}

	public function wait()
	{
		foreach ($this->pids as $key => $pid) {
			$out = [];
			//echo "ps -ef | grep -v grep|grep " . $pid . " | awk '{ print $2 }'\n";
			exec("ps -ef | grep -v grep|grep php|grep ' " . $pid . " ' | awk '{ print $2 }'", $out);
			$pid = 0;
			if (isset($out[0]))
				$pid = (int)$out[0];
			if ($pid == 0)
				unset($this->pids[$key]);
			else
				return true;
		}
		return false;
	}

	public function waitUntilEnd()
	{
		while ($this->wait()) {
			sleep(1);
		}
		return true;
	}

}