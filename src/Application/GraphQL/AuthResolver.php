<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Application\Security\JWTEncoder;
use Domain\Collection\UsersCollection;
use Application\Security\User as SecurityUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
final readonly class AuthResolver
{
    public function __construct(
        private UsersCollection $users,
        private CommandBus $commandBus,
        private JWTEncoder $jwtEncoder,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(string $email, string $password): string
    {

        $existingUser = $this->users->findOneByEmail($email);

        if (null === $existingUser) {
            throw new \RuntimeException('wrong password or email');
        }

        $securityUser = new SecurityUser($existingUser);


        if(!$this->passwordHasher->isPasswordValid($securityUser, $password)){
             throw new \RuntimeException('wrong password or email');
        }
        //TODO: check password

        $jwt = $this->jwtEncoder->encode($existingUser);

        return $jwt;
    }

}
