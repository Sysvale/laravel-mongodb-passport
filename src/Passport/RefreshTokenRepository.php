<?php

namespace Sysvale\Mongodb\Passport;

use Laravel\Passport\RefreshTokenRepository as PassportRefreshTokenRepository;

class RefreshTokenRepository extends PassportRefreshTokenRepository
{
    /**
     * Stores the given token instance.
     *
     * @param  \Sysvale\Mongodb\Passport\RefreshToken  $token
     * @return void
     */
    public function save($token)
    {
        $token->save();
    }
}
