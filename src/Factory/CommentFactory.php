<?php

namespace App\Factory;

use App\Entity\Comment;
use Zenstruck\Foundry\ModelFactory;

final class CommentFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'content' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Comment::class;
    }
}
