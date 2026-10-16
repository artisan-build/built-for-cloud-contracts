<?php

declare(strict_types=1);

use ArtisanBuild\BuiltForCloudContracts\Mail\ManagedMail;

it('pins the managed mail protocol and complete field lists', function (): void {
    expect(ManagedMail::CONTRACT_VERSION)->toBe('managed-mail-v1')
        ->and(ManagedMail::PATH)->toBe('/managed-mail/v1/messages')
        ->and(ManagedMail::MAX_RECIPIENTS)->toBe(50)
        ->and(ManagedMail::MAX_MESSAGE_BYTES)->toBe(5_242_880)
        ->and(ManagedMail::PAYLOAD_FIELDS)->toBe(['to', 'cc', 'bcc', 'subject', 'html', 'text', 'attachments'])
        ->and(ManagedMail::RECIPIENT_FIELDS)->toBe(['address', 'name'])
        ->and(ManagedMail::ATTACHMENT_FIELDS)->toBe(['filename', 'content_type', 'content']);
});

it('adds deterministic optional payload field vocabulary without changing required fields', function (): void {
    expect(ManagedMail::REPLY_TO_FIELD)->toBe('reply_to')
        ->and(ManagedMail::EMAIL_KEY_FIELD)->toBe('email_key')
        ->and(ManagedMail::OPTIONAL_PAYLOAD_FIELDS)->toBe(['reply_to', 'email_key'])
        ->and(array_intersect(ManagedMail::PAYLOAD_FIELDS, ManagedMail::OPTIONAL_PAYLOAD_FIELDS))->toBe([])
        ->and(ManagedMail::ALLOWED_PAYLOAD_FIELDS)->toBe([
            'to',
            'cc',
            'bcc',
            'subject',
            'html',
            'text',
            'attachments',
            'reply_to',
            'email_key',
        ]);
});

it('applies the inclusive combined recipient limit', function (): void {
    expect(ManagedMail::MAX_RECIPIENTS)->toBe(50);

    $expectations = [
        -1 => false,
        0 => true,
        49 => true,
        50 => true,
        51 => false,
    ];

    foreach ($expectations as $recipientCount => $expected) {
        expect(ManagedMail::isRecipientCountWithinLimit($recipientCount))->toBe($expected);
    }
});

it('applies the inclusive message size limit in bytes', function (): void {
    expect(ManagedMail::MAX_MESSAGE_BYTES)->toBe(5_242_880);

    $expectations = [
        -1 => false,
        0 => true,
        5_242_879 => true,
        5_242_880 => true,
        5_242_881 => false,
    ];

    foreach ($expectations as $messageBytes => $expected) {
        expect(ManagedMail::isMessageSizeWithinLimit($messageBytes))->toBe($expected);
    }
});

it('allows reply-to to be absent or contain exactly one recipient-shaped object', function (): void {
    expect(ManagedMail::MAX_REPLY_TO_RECIPIENTS)->toBe(1)
        ->and(ManagedMail::RECIPIENT_FIELDS)->toBe(['address', 'name']);

    $expectations = [
        -1 => false,
        0 => true,
        1 => true,
        2 => false,
        50 => false,
    ];

    foreach ($expectations as $recipientCount => $expected) {
        expect(ManagedMail::isReplyToRecipientCountWithinLimit($recipientCount))->toBe($expected);
    }
});

it('validates stable manifest email keys', function (): void {
    expect(ManagedMail::MAX_EMAIL_KEY_BYTES)->toBe(255);

    foreach (['welcome.signup', 'completed-document', str_repeat('a', 255)] as $emailKey) {
        expect(ManagedMail::isValidEmailKey($emailKey))->toBeTrue();
    }

    foreach ([
        '',
        str_repeat('a', 256),
        'Welcome.signup',
        'welcome signup',
        "welcome\r\nsignup",
        'https://example.com/welcome',
        '.welcome',
        'welcome.',
        '-welcome',
        'welcome-',
        'welcome..signup',
        'welcome.-signup',
    ] as $emailKey) {
        expect(ManagedMail::isValidEmailKey($emailKey))->toBeFalse();
    }
});
