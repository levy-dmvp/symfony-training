<?php

namespace spec\Domain\UseCase\EditDinosaur;

use Domain\Collection\DinosaursCollection;
use Domain\Collection\SpeciesCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\Model\Species;
use Domain\UseCase\EditDinosaur\Handler;
use Domain\UseCase\EditDinosaur\Input;
use Domain\UseCase\EditDinosaur\Output;
use PhpSpec\ObjectBehavior;

class HandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Handler::class);
    }

    function let(
           DinosaursCollection $dinosaurs,
           SpeciesCollection $species,
       ): void {

           $this->beConstructedWith(
               $dinosaurs,
               $species
           );
    }

    function it_edits_a_dinosaur(
          DinosaursCollection $dinosaurs,
          SpeciesCollection $species,
      ): void {
          $trex = new Species('Test', ['land'], 'carnivore');
          $dinosaur = new Dinosaur('Rex', 'male', $trex, 10, '#0f0',  new \DateTimeImmutable('10/02/1980'));
          $dinosaurs->find('42')->willReturn($dinosaur);
          $species->find('1')->willReturn($trex);

          $this(new Input('42', 'Rexy', 'female', '1', 12, '#f00',  new \DateTimeImmutable('10/02/1980')))
              ->shouldBeLike(new Output(
                  new Dinosaur('Rexy', 'female', $trex, 12, '#f00',  new \DateTimeImmutable('10/02/1980')),
              ));
      }

      function it_rejects_an_unknown_dinosaure(
          DinosaursCollection $dinosaurs,
      ): void {
          $dinosaurs->find('42')->willReturn(null);

        $this
            ->shouldThrow(DinosaurNotFoundException::class)
            ->during('__invoke', [
                new Input('42', 'Rexy', 'female', '1', 12, '#f00', new \DateTimeImmutable('10/02/1980')),
            ]);
      }
}
