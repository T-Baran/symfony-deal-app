<?php

namespace App\Factory;

use App\Entity\Deal;
use Zenstruck\Foundry\ModelFactory;

final class DealFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

    }

    protected function getDefaults(): array
    {
        return [
            'description' => self::faker()->text(200),
            'price' => self::faker()->randomNumber(5),
            'priceBefore' => self::faker()->randomNumber(7),
            'score' => self::faker()->randomNumber(2),
            'seller' => self::faker()->text(20),
            'title' => self::faker()->text(100),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Deal::class;
    }
}
