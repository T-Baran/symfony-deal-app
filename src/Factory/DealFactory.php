<?php

namespace App\Factory;

use App\Entity\Deal;
use App\Factory\UserFactory;
use App\Repository\DealRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Deal>
 *
 * @method        Deal|Proxy create(array|callable $attributes = [])
 * @method static Deal|Proxy createOne(array $attributes = [])
 * @method static Deal|Proxy find(object|array|mixed $criteria)
 * @method static Deal|Proxy findOrCreate(array $attributes)
 * @method static Deal|Proxy first(string $sortedField = 'id')
 * @method static Deal|Proxy last(string $sortedField = 'id')
 * @method static Deal|Proxy random(array $attributes = [])
 * @method static Deal|Proxy randomOrCreate(array $attributes = [])
 * @method static DealRepository|RepositoryProxy repository()
 * @method static Deal[]|Proxy[] all()
 * @method static Deal[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Deal[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Deal[]|Proxy[] findBy(array $attributes)
 * @method static Deal[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Deal[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class DealFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     * @throws \Exception
     */
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

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Deal $deal): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Deal::class;
    }
}
