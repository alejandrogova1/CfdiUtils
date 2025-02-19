<?php

namespace CfdiUtilsTests\Validate\FakeObjects;

use CfdiUtils\Nodes\NodeInterface;
use CfdiUtils\Validate\Asserts;
use CfdiUtils\Validate\Contracts\ValidatorInterface;

class ImplementationValidatorInterface implements ValidatorInterface
{
    /** @var string */
    public $version = '3.3';

    /** @var bool */
    public $onValidateSetMustStop = false;

    /** @var bool */
    public $enterValidateMethod = false;

    /** @var Asserts|null */
    public $assertsToImport;

    public function validate(NodeInterface $comprobante, Asserts $asserts): void
    {
        if ($this->assertsToImport instanceof Asserts) {
            $asserts->import($this->assertsToImport);
        }
        $this->enterValidateMethod = true;
        $asserts->mustStop($this->onValidateSetMustStop);
    }

    public function canValidateCfdiVersion(string $version): bool
    {
        return $version === $this->version;
    }
}
