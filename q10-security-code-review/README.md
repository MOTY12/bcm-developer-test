# Question 10: Security Code Review

The claim is unsafe. Hiding a browser button changes only the user interface; a user can still call the endpoint directly, modify a request, or replay it. Authorisation and validation must happen on the server.

## Required Server-Side Checks

- Accept the expected HTTP method and content type only.
- Authenticate the request and verify the WordPress REST nonce for a cookie-authenticated browser request. A nonce helps prevent CSRF but does not replace capability checks.
- Validate and normalise the article ID, then confirm that it resolves to an allowed post type.
- Validate the requested status against an explicit allow-list and allowed transition rules; never pass arbitrary browser input into `post_status`.
- Check the mapped capability for this specific post with `current_user_can('edit_post', $postId)`. For transitions that publish or make content private, require the appropriate publishing/private-post capability too.
- Use a revision or last-modified value when concurrent editing matters, and return a conflict rather than overwriting a newer edit.
- Perform the update through WordPress APIs, check `WP_Error`, and create an audit entry with the actor, post ID, old/new status, and request correlation ID.

## Pseudocode

```php
function update_article_status(WP_REST_Request $request): WP_REST_Response|WP_Error {
    require_post_method($request);
    require_authenticated_user();
    verify_rest_nonce($request);

    $postId = absint($request['article_id']);
    $newStatus = sanitize_key($request['status']);
    $post = get_post($postId);

    if (! $post || $post->post_type !== 'post') {
        return new WP_Error('not_found', 'Article not found.', ['status' => 404]);
    }

    if (! current_user_can('edit_post', $postId)) {
        return new WP_Error('forbidden', 'Not allowed to edit this article.', ['status' => 403]);
    }

    if (! allowed_status_transition($post, $newStatus, get_current_user_id())) {
        return new WP_Error('invalid_status', 'Status change is not allowed.', ['status' => 400]);
    }

    if ($newStatus === 'publish' && ! current_user_can('publish_posts')) {
        return new WP_Error('forbidden', 'Not allowed to publish.', ['status' => 403]);
    }

    assert_expected_revision($request['revision'], $post);
    $result = wp_update_post(['ID' => $postId, 'post_status' => $newStatus], true);

    if (is_wp_error($result)) {
        return $result;
    }

    audit_status_change($postId, $post->post_status, $newStatus, get_current_user_id());

    return new WP_REST_Response(['id' => $postId, 'status' => $newStatus], 200);
}
```

## Rejected Tests

1. A logged-out request, or a logged-in subscriber request, with a valid article ID and `status=publish` must return `401` or `403` and make no change.
2. An editor without permission for a different article submits that article's ID with `status=private`, or any user submits an unsupported status such as `delete-all`; the endpoint must return `403` or `400` and make no change.

## Raising the Issue and Release Bar

I would raise this in the review as a factual security issue: “The button visibility is client-side only, so it does not protect the endpoint from direct requests. This route needs object-level capability checks, CSRF protection for browser sessions, status allow-listing, and negative tests before it can be released.”

Before release, the endpoint must enforce those server-side checks, reject the two negative cases above, handle update failures safely, and have reviewable tests proving that unauthorised users cannot alter any article status.
