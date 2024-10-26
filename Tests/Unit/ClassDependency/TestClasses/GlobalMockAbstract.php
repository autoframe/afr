<?php

abstract class GlobalMockAbstract implements GlobalMockInterface
{
    use GlobalMockTrait;

    public function colorb(): string
    {
        return 'pink';
    }
}