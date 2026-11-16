<?php

namespace spec\Domain\UseCase\CreateDinosaur;

use Domain\Event\DinosaurIsBorn;
use Domain\EventsRegisterer;
use Domain\Collection\DinosaursCollection;
use Domain\Collection\SpeciesCollection;
use Domain\Exception\SpeciesNotFoundException;
use Domain\Model\Dinosaur;
use Domain\Model\Species;
use Domain\UseCase\CreateDinosaur\Input;
use Domain\UseCase\CreateDinosaur\Output;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Domain\UseCase\CreateDinosaur\Handler;
use Prophecy\Argument;

class HandlerSpec extends ObjectBehavior
{
    private EventsRegisterer $eventsRegisterer;

    function it_is_initializable()
    {
        $this->shouldHaveType(Handler::class);
    }
    function let(
           DinosaursCollection $dinosaurs,
           SpeciesCollection $species,
       ): void {
           $this->eventsRegisterer = new EventsRegisterer();

           $this->beConstructedWith(
               $dinosaurs,
               $species,
               $this->eventsRegisterer,
           );
    }

    function it_creates_a_dinosaur(
          DinosaursCollection $dinosaurs,
          SpeciesCollection $species,
      ): void {
          $dinosaurs->findByName('Rex')->willReturn(null);
          $species->find('1')->willReturn(
              new Species('Tyrannosaurus', ['forest'], 'carnivore'),
          );
          $dinosaurs->add(Argument::type(Dinosaur::class))
              ->shouldBeCalled()
              ->will(function (array $arguments): void {
                  (new \ReflectionProperty($arguments[0], 'id'))
                      ->setValue($arguments[0], 43);
              });

          $this(new Input('Rex', 'male', '1', 12, '#ff0000'))
              ->shouldHaveType(Output::class);

          // l'événement DinosaurIsBorn(42) est enregistré

          $expected = [new DinosaurIsBorn(43)];

          if ($expected != $this->eventsRegisterer->getEvents()) {
              throw new FailureException(
                  'DinosaurIsBorn was not registered',
              );
          }
      }


      function it_rejects_an_unknown_species(
          DinosaursCollection $dinosaurs,
          SpeciesCollection $species,
      ): void {
          $dinosaurs->findByName('Rex')->willReturn(null);
          $species->find('1')->willReturn(null);

          $this
              ->shouldThrow(SpeciesNotFoundException::class)
              ->during('__invoke', [
                  new Input('Rex', 'male', '1', 12, '#ff0000'),
              ]);
      }
}
