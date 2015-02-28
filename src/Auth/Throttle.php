<?php
namespace Lavender\Auth;

class Throttle
{

    /**
     * Increments the count for the given identity by one and
     * also returns the current value for that identity.
     *
     * @param mixed $identity The login identity.
     *
     * @return int How many times that same identity was used.
     */
    public function throttleIdentity($identity)
    {
        $identity = $this->parseIdentity($identity);

        // Increments and also retuns the current count
        return $this->countThrottle($identity);
    }

    /**
     * Tells if the given identity has reached the throttle_limit.
     *
     * @param mixed $identity The login identity.
     *
     * @return bool True if the identity has reached the throttle_limit.
     */
    public function isThrottled($identity)
    {
        $identity = $this->parseIdentity($identity);

        // Retuns the current count
        $count = $this->countThrottle($identity, 0);

        return $count >= config('store.throttle_limit', 10);
    }

    /**
     * Parse the given identity in order to return a string with
     * the relevant fields. I.E: if the attacker tries to use a
     * bunch of different passwords, the identity will still be the
     * same.
     *
     * @param mixed $identity
     *
     * @return string $identityString.
     */
    protected function parseIdentity($identity)
    {
        // If is an array, remove password, remember and then
        // transforms it into a string.
        if(is_array($identity)){
            unset($identity['password']);
            unset($identity['remember']);
            $identity = serialize($identity);
        }

        return $identity;
    }

    /**
     * Increments the count for the given string by one stores
     * it into cache and returns the current value for that
     * identity.
     *
     * @param string $identityString
     * @param int $increments Amount that is going to be added to the throttling attempts for the given identity.
     *
     * @return int How many times that same string was used.
     */
    protected function countThrottle($identityString, $increments = 1)
    {
        $count = app('cache')->get('login_throttling:' . md5($identityString), 0);

        $count = $count + $increments;

        $ttl = config('store.throttle_time_period');

        app('cache')->put('login_throttling:' . md5($identityString), $count, $ttl);

        return $count;
    }
}
