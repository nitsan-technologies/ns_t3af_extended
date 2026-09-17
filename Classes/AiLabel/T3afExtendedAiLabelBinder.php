<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\AiLabel;

use NITSAN\NsT3AF\AiLabel\Service\AiLabelBindHelper;

/**
 * Sample after-save AI Label bind for the summarize-into-content demo.
 *
 * Call after DataHandler save when the live tt_content uid is known. Capture
 * correlation ids are set automatically on {@see \NITSAN\NsT3AF\Api\AiServiceInterface}
 * responses in the same request.
 */
final class T3afExtendedAiLabelBinder
{
    private const SOURCE = 'ns_t3af_extended';

    public function bindContentRecord(int $uid): void
    {
        if ($uid <= 0 || !class_exists(AiLabelBindHelper::class)) {
            return;
        }

        AiLabelBindHelper::bindContentRecord($uid, self::SOURCE);
    }
}
