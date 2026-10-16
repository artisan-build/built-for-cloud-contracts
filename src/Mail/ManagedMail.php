<?php

declare(strict_types=1);

namespace ArtisanBuild\BuiltForCloudContracts\Mail;

final class ManagedMail
{
    public const string CONTRACT_VERSION = 'managed-mail-v1';

    public const string PATH = '/managed-mail/v1/messages';

    public const int MAX_RECIPIENTS = 50;

    public const int MAX_MESSAGE_BYTES = 5_242_880;

    public const int MAX_REPLY_TO_RECIPIENTS = 1;

    public const int MAX_EMAIL_KEY_BYTES = 255;

    public const array PAYLOAD_FIELDS = ['to', 'cc', 'bcc', 'subject', 'html', 'text', 'attachments'];

    public const string REPLY_TO_FIELD = 'reply_to';

    public const string EMAIL_KEY_FIELD = 'email_key';

    public const array OPTIONAL_PAYLOAD_FIELDS = [self::REPLY_TO_FIELD, self::EMAIL_KEY_FIELD];

    public const array ALLOWED_PAYLOAD_FIELDS = [...self::PAYLOAD_FIELDS, ...self::OPTIONAL_PAYLOAD_FIELDS];

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

    public static function isReplyToRecipientCountWithinLimit(int $recipientCount): bool
    {
        return $recipientCount >= 0 && $recipientCount <= self::MAX_REPLY_TO_RECIPIENTS;
    }

    public static function isValidEmailKey(string $emailKey): bool
    {
        return $emailKey !== ''
            && strlen($emailKey) <= self::MAX_EMAIL_KEY_BYTES
            && preg_match('/\A[a-z0-9]+(?:[._-][a-z0-9]+)*\z/', $emailKey) === 1;
    }
}
