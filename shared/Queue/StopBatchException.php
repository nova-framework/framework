<?php

namespace Shared\Queue;

use Exception;


class StopBatchException extends Exception
{
    // Cleanly handle stopping a batch without resorting to killing the process
}

