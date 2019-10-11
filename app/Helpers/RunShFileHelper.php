<?php
namespace App\Helpers;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RunShFileHelper
{
	public static function run($file){
		$process = new Process($file);
		$process->run();

		if (!$process->isSuccessful()) {
		    throw new ProcessFailedException($process);
		}

		return $process->getOutput();
	}

	public static function updateTheme(){
		return self::run("run.sh");
	}


}