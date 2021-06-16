<?php

namespace Ria\Bundle\CoreBundle\Component\Pipeline;

use Closure;

interface PipelineInterface
{
    /**
     * Set the traveler object being sent on the pipeline.
     *
     * @param  mixed  $traveler
     * @return $this
     */
    public function send(mixed $traveler): self;

    /**
     * Set the stops of the pipeline.
     *
     * @param  array  $stops
     * @return $this
     */
    public function through(array $stops): self;

    /**
     * Set the method to call on the stops.
     *
     * @param  string  $method
     * @return $this
     */
    public function via(string $method): self;

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination);
}