<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\E2E;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('e2e')]
#[Group('integrator')]
final class IntegratorApiTest extends E2ETestCase
{
    #[Test]
    public function list_accounts_returns_a_collection_for_the_api_key_owner(): void
    {
        [$idToken, $apiKey] = $this->loginAsDataUser();

        $resp = $this->integratorApi()->listAccounts($idToken, $apiKey);

        self::assertNotNull($resp);
        $accounts = $resp->getAccounts();
        self::assertIsArray($accounts);
        // The current account itself should be in the list when this principal is the owner;
        // for a non-owner the call returns an empty array but should not error.
    }
}
