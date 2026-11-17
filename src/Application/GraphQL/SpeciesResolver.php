<?php
namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Domain\Collection\SpeciesCollection;
use Domain\Exception\SpeciesNotFoundException;
use Domain\Model\Species;
use Domain\UseCase\CreateSpecies;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class SpeciesResolver
{
    public function __construct(
        private SpeciesCollection $species,
        private CommandBus $commandBus,
    ) {
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function species(?string $search): array
    {
        return $this->species->search($search);
    }


    public function createSpecies(array|\ArrayObject $input): Species
    {
        $output = $this->commandBus->dispatch(
            new CreateSpecies\Input(
                $input['name'],
                $input['habitats'],
                $input['feeding'],
            ),
        );

        return $output->species;
    }
}
