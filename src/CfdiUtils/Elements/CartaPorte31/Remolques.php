<?php

namespace CfdiUtils\Elements\CartaPorte31;

use CfdiUtils\Elements\Common\AbstractElement;

class Remolques extends AbstractElement
{
    public function getElementName(): string
    {
        return 'cartaporte31:Remolques';
    }

    public function addRemolque(array $attributes = []): Remolque
    {
        $subject = new Remolque($attributes);
        $this->addChild($subject);
        return $subject;
    }

    public function multiRemolque(array ...$elementAttributes): self
    {
        foreach ($elementAttributes as $attributes) {
            $this->addRemolque($attributes);
        }
        return $this;
    }
}
