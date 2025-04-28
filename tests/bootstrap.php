<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/constants.php';

Tester\Dumper::$dumpDir = OutputDir;
Tester\Environment::setup();

function getTempDirForTestCase(): string
{
	$tempDir = TempDir . '/' . hash('xxh128', debug_backtrace()[0]['file'] . '/' . getenv(Tester\Environment::VariableThread) ?: 'no-tester');
	@rmdir($tempDir);
	return $tempDir;
}
