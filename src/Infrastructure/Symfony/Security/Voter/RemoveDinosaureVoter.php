<?php
declare(strict_types=1);
namespace Infrastructure\Symfony\Security\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
final class RemoveDinosaureVoter extends Voter
{
    public const ATTRIBUTES = 'REMOVE_DINO';


    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager){

    }

    protected function supports($attribute, $subject): bool
    {
        return $attribute == self::ATTRIBUTES;
    }

    protected function voteOnAttribute(string $sttribute, mixed $object, TokenInterface $token): bool
    {

        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])){
             return true;
        }

        return false;

    }

}
