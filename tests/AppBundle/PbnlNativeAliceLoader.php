<?php

namespace Tests\AppBundle;


use Nelmio\Alice\Loader\NativeLoader;

class PbnlNativeAliceLoader extends NativeLoader
{

    private $seed = 1;

    public function __construct(int $seed)
    {
        $this->seed = $seed;
        parent::__construct();
    }

    public function setSeed(int $seed)
    {
        $this->seed = $seed;
    }

    /**
     * Seed used to generate random data. The seed is passed to the random number generator, so calling the a script
     * twice with the same seed produces the same results.
     *
     * @return int|null
     */
    protected function getSeed()
    {
        return $this->seed;
    }
}