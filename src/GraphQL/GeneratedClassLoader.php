<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use Nette;
use ReflectionClass;
use Stringable;


final class GeneratedClassLoader
{

	/**
	 * @param callable(string): (string|Stringable) $factory
	 * @throws Exceptions\CannotGenerateCachedClassException
	 */
	public function generateAndLoadCachedClass(
		string $tempDir,
		string $className,
		callable $factory,
	): void
	{
		$classFilePath = "{$tempDir}/{$className}.php";

		if ((@include $classFilePath) !== false) {
			return;
		}

		if (
			is_dir($tempDir) === false
			&& @mkdir($tempDir, 0777, recursive: true) === false
			&& is_dir($tempDir) === false
		) {
			throw new Exceptions\CannotGenerateCachedClassException(
				"Target directory '{$tempDir}' can't be created",
			);
		}

		$lockFile = "{$tempDir}/{$className}.lock";
		$lockFileHandle = @fopen($lockFile, 'c+');

		if ($lockFileHandle === false || @flock($lockFileHandle, LOCK_EX) === false) {
			throw new Exceptions\CannotGenerateCachedClassException(
				"Cannot acquire lock at '{$lockFile}'",
			);
		}

		if (
			is_file($classFilePath)
			&& (@include $classFilePath) === false // @phpstan-ignore identical.alwaysTrue (File could have been created elsewhere meanwhile)
		) {
			throw new Exceptions\CannotGenerateCachedClassException(
				"Cannot include '{$classFilePath}'",
			);
		}

		file_put_contents(
			$classFilePath,
			(string) $factory($className),
		);

		if ((@include $classFilePath) === false) { // @phpstan-ignore identical.alwaysTrue (File could have been created elsewhere meanwhile)
			throw new Exceptions\CannotGenerateCachedClassException(
				"Cannot include '{$classFilePath}'",
			);
		}

		flock($lockFileHandle, LOCK_UN); // @phpstan-ignore deadCode.unreachable
	}

}
