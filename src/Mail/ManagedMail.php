<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Mail;

final class ManagedMail
{
    public const string CONTRACT_VERSION = 'managed-mail-v1';

    public const string PATH = '/managed-mail/v1/messages';

    public const int MAX_RECIPIENTS = 50;

    public const int MAX_MESSAGE_BYTES = 5_242_880;

    public const array PAYLOAD_FIELDS = ['to', 'cc', 'bcc', 'subject', 'html', 'text', 'attachments'];

    public const array RECIPIENT_FIELDS = ['address', 'name'];

    public const array ATTACHMENT_FIELDS = ['filename', 'content_type', 'content'];

    public static function isRecipientCountWithinLimit(int $recipientCount): bool
    {
        return $recipientCount >= 0 && $recipientCount <= self::MAX_RECIPIENTS;
    }

    public static function isMessageSizeWithinLimit(int $messageBytes): bool
    {
        return $messageBytes >= 0 && $messageBytes <= self::MAX_MESSAGE_BYTES;
    }
}
