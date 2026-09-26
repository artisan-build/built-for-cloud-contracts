<?php

declare(strict_types=1);

use ArtisanBuild\BuiltForCloudContracts\Mail\ManagedMail;

it('pins the managed mail protocol and complete field lists', function (): void {
    expect(ManagedMail::CONTRACT_VERSION)->toBe('managed-mail-v1')
        ->and(ManagedMail::PATH)->toBe('/managed-mail/v1/messages')
        ->and(ManagedMail::PAYLOAD_FIELDS)->toBe(['to', 'cc', 'bcc', 'subject', 'html', 'text', 'attachments'])
        ->and(ManagedMail::RECIPIENT_FIELDS)->toBe(['address', 'name'])
        ->and(ManagedMail::ATTACHMENT_FIELDS)->toBe(['filename', 'content_type', 'content']);
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
