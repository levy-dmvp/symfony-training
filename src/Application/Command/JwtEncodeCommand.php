<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Security\JWTEncoder;
use Infrastructure\Doctrine\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:jwt:encode', )]
final class JwtEncodeCommand extends Command {


    protected function configure(): void
    {
        $this
        ->addArgument('email', InputArgument::REQUIRED, 'Which mail ?')
        ;
    }

    public function __construct(private readonly JWTEncoder $jwtEncoder, private UserRepository $userRepository){
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputEmail  = $input->getArgument('email');
        $user = $this->userRepository->findOneByEmail( $inputEmail);
        $jwt = $this->jwtEncoder->encode($user);
        $decodedUser = $this->jwtEncoder->decode($jwt);

        dd($decodedUser);
        return Command::SUCCESS;
    }

}
