<?php declare(strict_types = 1);

namespace PrestaShop\CodingStandards\ErrorFormatter;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\TableErrorFormatter;
use PHPStan\Command\Output;
use PHPStan\File\RelativePathHelper;

class GithubErrorFormatter extends TableErrorFormatter
{

	private RelativePathHelper $relativePathHelper;

	public function __construct(
		RelativePathHelper $relativePathHelper,
		bool $showTipsOfTheDay
	)
	{
		parent::__construct($relativePathHelper, $showTipsOfTheDay);
		$this->relativePathHelper = $relativePathHelper;
	}

	public function formatErrors(AnalysisResult $analysisResult, Output $output): int
	{
		parent::formatErrors($analysisResult, $output);

		foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
			$metas = [
				'file' => $this->relativePathHelper->getRelativePath($fileSpecificError->getFile()),
				'line' => $fileSpecificError->getLine(),
				'col' => 0,
			];
			array_walk($metas, static function (&$value, string $key): void {
				$value = sprintf('%s=%s', $key, (string) $value);
			});

			$severity = $fileSpecificError->canBeIgnored() ? 'warning' : 'error';
			$message = $fileSpecificError->getMessage();

			$line = sprintf('::%s %s::%s', $severity, implode(',', $metas), $message);

			$output->writeRaw($line);
			$output->writeLineFormatted('');
		}

		return $analysisResult->hasErrors() ? 1 : 0;
	}

	private function formatForGitHubActionAnnotation(string $message): string
	{
		return str_replace(["\n", "\r"], ['%0A', '%0D'], $message);
	}
}
