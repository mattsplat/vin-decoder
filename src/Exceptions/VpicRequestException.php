<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Exceptions;

use Throwable;

/**
 * Thrown when a request to the vPIC API fails at the transport level, returns a
 * non-2xx status, or responds with a body that is not decodable JSON.
 */
class VpicRequestException extends VpicException
{
    public function __construct(
        string $message,
        private readonly ?string $endpoint = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * The vPIC endpoint path that was being requested, if known.
     */
    public function endpoint(): ?string
    {
        return $this->endpoint;
    }
}
