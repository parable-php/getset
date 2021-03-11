<?php declare(strict_types=1);

namespace Parable\GetSet;

use Parable\GetSet\Resource\LocalResourceInterface;
use Throwable;

class InputStreamCollection extends BaseCollection implements LocalResourceInterface
{
    protected const INPUT_SOURCE = 'php://input';

    public function __construct()
    {
        $body_content = @file_get_contents(static::INPUT_SOURCE);

        if ($body_content === false) {
            throw new GetSetException(sprintf(
                "Could not read from input source '%s'.",
                static::INPUT_SOURCE
            ));
        }

        if (!empty($body_content)) {
            $this->extractAndSetData($body_content);
        }
    }

    protected function extractAndSetData(string $data)
    {
        try {
            $data_parsed = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $t) {
            parse_str(trim($data), $data_parsed);
        }

        if (is_array($data_parsed) && !empty($data_parsed)) {
            $this->setAll($data_parsed);
        }
    }
}
