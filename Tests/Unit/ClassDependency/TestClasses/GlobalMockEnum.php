<?php
if (PHP_VERSION_ID >= 81000) {
    enum GlobalMockEnum
    {
        case DRAFT;
        case PUBLISHED;
        case ARCHIVED;

        public function color(): string
        {
            return match ($this) {
                GlobalMockEnum::DRAFT => 'grey',
                GlobalMockEnum::PUBLISHED => 'green',
                GlobalMockEnum::ARCHIVED => 'red',
            };
        }
    }
}