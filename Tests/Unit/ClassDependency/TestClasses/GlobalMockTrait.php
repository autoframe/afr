<?php

trait GlobalMockTrait
{
    use GlobalMockTraitSub;
    public function color(): string
    {
        return 'blue';
    }

}