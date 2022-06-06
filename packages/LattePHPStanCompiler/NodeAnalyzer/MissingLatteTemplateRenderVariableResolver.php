<?php

declare(strict_types=1);

namespace Reveal\LattePHPStanCompiler\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Reveal\TemplatePHPStanCompiler\NodeAnalyzer\MethodCallArrayResolver;

/**
 * @api
 */
final class MissingLatteTemplateRenderVariableResolver
{
    /**
     * Variables passed by default to every template
     *
     * @var string[]
     */
    private const DEFAULT_VARIABLE_NAMES = ['basePath', 'user'];

    public function __construct(
        private \Reveal\LattePHPStanCompiler\LatteVariableNamesResolver $latteVariableNamesResolver,
        private MethodCallArrayResolver $methodCallArrayResolver
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromTemplateAndMethodCall(
        MethodCall $methodCall,
        string $templateFilePath,
        Scope $scope
    ): array {
        $templateUsedVariableNames = $this->latteVariableNamesResolver->resolveFromFilePath($templateFilePath);
        $availableVariableNames = $this->methodCallArrayResolver->resolveArrayKeysOnPosition($methodCall, $scope, 1);

        $missingVariableNames = array_diff(
            $templateUsedVariableNames,
            $availableVariableNames,
            self::DEFAULT_VARIABLE_NAMES
        );
        return array_unique($missingVariableNames);
    }
}