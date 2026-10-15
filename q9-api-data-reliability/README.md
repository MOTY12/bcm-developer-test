# Question 9: API and Data Reliability

## Assumptions

The newsletter provider accepts an idempotency key or a caller-controlled external reference and can look up a prior request by that value. Without one of those capabilities, a timeout after acceptance cannot be distinguished safely from a request the provider never received.

```php
function submit_signup(string $email, string $idempotencyKey, NewsletterClient $client, SignupStore $store): array
{
    $normalisedEmail = strtolower(trim($email));

    if (filter_var($normalisedEmail, FILTER_VALIDATE_EMAIL) === false) {
        return ['status' => 422, 'message' => 'Enter a valid email address.'];
    }

    $signup = $store->findOrCreatePending(
        idempotencyKey: $idempotencyKey,
        emailHash: hash_hmac('sha256', $normalisedEmail, $_ENV['EMAIL_HASH_KEY']),
        encryptedEmail: $store->encrypt($normalisedEmail)
    );

    if ($signup->isComplete()) {
        return ['status' => 200, 'message' => 'You are already subscribed.'];
    }

    try {
        $response = $client->subscribe(
            email: $normalisedEmail,
            idempotencyKey: $signup->idempotencyKey,
            externalReference: $signup->id
        );
        $store->markComplete($signup->id, $response->subscriberId);

        return ['status' => 201, 'message' => 'Subscription accepted.'];
    } catch (RateLimitException $exception) {
        $store->scheduleRetry($signup->id, $exception->retryAfterSeconds());

        return ['status' => 202, 'message' => 'Subscription is queued.'];
    } catch (TimeoutException) {
        $accepted = $client->findByIdempotencyKey($signup->idempotencyKey);

        if ($accepted !== null) {
            $store->markComplete($signup->id, $accepted->subscriberId);
        } else {
            $store->scheduleRetry($signup->id, 30);
        }

        return ['status' => 202, 'message' => 'Subscription is being confirmed.'];
    }
}
```

## Notes

- The signup store needs a unique constraint on `idempotency_key`. Retries must reuse the same key; generating a new key on every retry defeats idempotency.
- Store the key, internal signup ID, status, retry time, provider subscriber ID, provider error code, and an HMAC of the normalised email for diagnosis/deduplication. Store the email only when needed for delivery and protect it at rest.
- Log the internal signup ID, idempotency key, outcome, retry count, and provider request/correlation ID. Do not log the raw email, full request/response body, access tokens, or other unnecessary personal data.
- A rate limit is queued for delayed retry using the provider's `Retry-After` value; it is not retried immediately.
- For the critical test, make a local mock create the provider subscriber and then throw a timeout. Call `submit_signup` twice with the same key and assert there is one provider subscriber and one completed local signup. Also test an invalid email makes no provider call, and a rate limit creates only one scheduled retry.
