<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

Tester\Dumper::$dumpDir = __DIR__ . '/output';
Tester\Environment::setup();

const TempDir = __DIR__ . '/temp';

function getTempDirForTestCase(): string
{
	$tempDir = TempDir . '/' . hash('xxh128', debug_backtrace()[0]['file'] . '/' . getenv(Tester\Environment::VariableThread) ?: 'no-tester');
	@rmdir($tempDir);
	return $tempDir;
}
