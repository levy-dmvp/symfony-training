<?php

namespace Application\GraphQL;

use Domain\Model\Species;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

final class SpeciesResolverMap extends ResolverMap
{
    protected function map(): array
    {
        return [
            'Species' => [
                'dino_count' => static fn (
                    Species $species,
                ): int => iterator_count($species->getDinosaurs()),
            ],
        ];
    }
}
