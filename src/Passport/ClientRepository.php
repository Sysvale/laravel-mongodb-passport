<?php

namespace Sysvale\Mongodb\Passport;

use Laravel\Passport\ClientRepository as PassportClientRepository;

class ClientRepository extends PassportClientRepository
{
    /**
     * Update the given client.
     *
     * @param  \Sysvale\Mongodb\Passport\Client  $client
     * @param  string  $name
     * @param  string  $redirect
     * @return \Sysvale\Mongodb\Passport\Client
     */
    public function update($client, $name, $redirect)
    {
        $client->forceFill([
            'name' => $name, 'redirect' => $redirect,
        ])->save();

        return $client;
    }

    /**
     * Regenerate the client secret.
     *
     * @param  \Sysvale\Mongodb\Passport\Client  $client
     * @return \Sysvale\Mongodb\Passport\Client
     */
    public function regenerateSecret($client)
    {
        $client->forceFill([
            'secret' => Str::random(40),
        ])->save();

        return $client;
    }

    /**
     * Delete the given client.
     *
     * @param  \Sysvale\Mongodb\Passport\Client  $client
     * @return void
     */
    public function delete($client)
    {
        $client->tokens()->update(['revoked' => true]);

        $client->forceFill(['revoked' => true])->save();
    }
}
