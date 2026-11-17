<?php

namespace Infrastructure\Lexik;

use Application\Security\JWTEncoder;

use Domain\Collection\UsersCollection;
use Domain\Model\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Override;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;


final readonly class LexikJWTEncoder implements JWTEncoder {

    public function __construct(private JWTEncoderInterface $JWTManager, private UsersCollection $users){

    }

    #[Override]
    public function encode(User $user):string {

        return $this->JWTManager->encode([
           'sub'=> $user->getId()
        ]);
    }

    #[Override]
    public function decode(string $jwt): User{
        $res = $this->JWTManager->decode($jwt);

        if(!array_key_exists('sub', $res)){
            throw new \RuntimeException('fail to parse jwt');
        }
        $user = $this->users->findById($res['sub']);
        if(null === $user) {
             throw new UserNotFoundException(message: "User not found ");
        }
        return $user;
    }
}
