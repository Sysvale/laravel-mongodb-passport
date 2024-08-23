<?php

namespace Sysvale\Mongodb\Passport\Guards;

use Laravel\Passport\Guards\TokenGuard as PassportTokenGuard;
use Sysvale\Mongodb\Passport\Client;

use League\OAuth2\Server\ResourceServer;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\ClientRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Laravel\Passport\PassportUserProvider;

class TokenGuard
{
    protected PassportTokenGuard $token_guard;

    /**
     * Create a new token guard instance.
     *
     * @param  \League\OAuth2\Server\ResourceServer  $server
     * @param  \Laravel\Passport\PassportUserProvider  $provider
     * @param  \Laravel\Passport\TokenRepository  $tokens
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @param  \Illuminate\Contracts\Encryption\Encrypter  $encrypter
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(
        ResourceServer $server,
        PassportUserProvider $provider,
        TokenRepository $tokens,
        ClientRepository $clients,
        Encrypter $encrypter,
        Request $request
    ) {
        $this->token_guard = resolve(
            PassportTokenGuard::class,
            [
                'server' => $server,
                'provider' => $provider,
                'tokens' => $tokens,
                'clients' => $clients,
                'encrypter' => $encrypter,
                'request' => $request
            ]
        );
    }

    /**
     * Set the client for the current request.
     *
     * @param  Sysvale\Mongodb\Passport\Client  $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    public function __call($name, $args)
    {
        return $this->token_guard->$name(...$args);
    }
}
