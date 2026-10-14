# Question 1: WordPress PHP

## Errors and Effects

- `WP_Query(...)` must be instantiated with `new WP_Query(...)`; without `new`, PHP tries to call an undefined function.
- WordPress pagination uses the `paged` query argument, not `page`, for post archives/search result pagination. Using `page` means page 2 will not reliably load the second set of posts.
- `$_GET['s']` is read without an existence check, causing notices when the search parameter is missing.
- `$_GET` values are used without unslashing, sanitising, or type coercion. That can produce bad query values and unsafe output.
- `$pages` is undefined. The total page count should come from `$query->max_num_pages`.
- The `<h1>` tag is never closed.
- `esc_html(the_title())` is incorrect because `the_title()` echoes and returns void. Use `get_the_title()` when a value is needed.
- `preg_replace_callback('#[ad]#', ...)` does not match a literal `[ad]`; square brackets form a character class in regex. It would match any single `a` or `d`.
- The callback echoes instead of returning the replacement string, and it references undefined `$m`.
- The original loop does not handle zero results and does not call `wp_reset_postdata()`.

## Corrected Version

See [solution.php](/Users/newuser/Documents/GitHub/bcm-developer-test/q1-php-wordpress/solution.php).

The reusable logic is at the top of [solution.php](/Users/newuser/Documents/GitHub/bcm-developer-test/q1-php-wordpress/solution.php). The local demo loads those functions from `solution.php`, so the demo exercises the same page/search/ad-marker handling used by the submitted answer.

## Assumptions About Ads

Each literal `[ad]` marker is an editor-placed placeholder. Every occurrence should become one empty `<div>` with `ad`, `ad-N`, and `data-ad-position` attributes. The live ad script can then replace or fill those containers. Markers are numbered from `1` again for each post.

## Checks

- Missing parameters: with no `page` and no `s`, `$current_page` defaults to `1`, `$search_term` defaults to an empty string, and the heading omits the search phrase.
- Page 2: `?page=2&s=example` becomes `paged => 2`, so WordPress requests the second page of ten matching posts.
- No search results: if `$query->have_posts()` is false, the template prints `No posts found.` and still has a valid heading.
- Multiple `[ad]` markers: `Intro [ad] middle [ad] end` becomes two ad containers, numbered `ad-1`/`ad-2` with matching `data-ad-position` values.

## Verification

```bash
php -l q1-php-wordpress/solution.php
php -l q1-php-wordpress/demo.php
```

## Interviewer Demo

The production answer is `solution.php`, which is intended for a WordPress theme. To test the same behaviours without setting up WordPress, run this local demo:

```bash
php -S 127.0.0.1:8080 -t q1-php-wordpress
```

Then open:

- `http://127.0.0.1:8080/demo.php`
- `http://127.0.0.1:8080/demo.php?page=2`
- `http://127.0.0.1:8080/demo.php?s=no-match`
- `http://127.0.0.1:8080/demo.php?s=Launch`
