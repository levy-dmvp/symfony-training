<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Domain\Collection\DinosaursCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\UseCase\CreateDinosaur;
final readonly class DinosaurResolver
{
    public function __construct(
        private DinosaursCollection $dinosaurs,
        private CommandBus $commandBus,
    ) {
    }

    public function dinosaurs(?string $search): array
    {
        return $this->dinosaurs->search($search);
    }

    public function dinosaur(string $id): Dinosaur
    {
        return $this->dinosaurs->find($id)
            ?? throw new DinosaurNotFoundException($id);
    }

    public function createDinosaur(array|\ArrayObject $input): Dinosaur
    {
        $output = $this->commandBus->dispatch(
            new CreateDinosaur\Input(
                $input['name'],
                $input['gender'],
                $input['speciesId'],
                $input['age'],
                $input['eyesColor'],
                new \DateTimeImmutable()
            ),
        );

        return $output->dinosaur;
    }
}
