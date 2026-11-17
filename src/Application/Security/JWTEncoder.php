<?php

declare(strict_types=1);

namespace Application\Security;

use Domain\Model\User;

interface JWTEncoder
{
    public function encode(User $user):string;
    public function decode(string $jwt):User;
}
