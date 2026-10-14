<?php

declare(strict_types=1);

define('Q1_LOAD_FUNCTIONS_ONLY', true);
require_once __DIR__ . '/solution.php';

$page_param   = $_GET['page'] ?? 1;
$search_param = $_GET['s'] ?? '';

$current_page = q1_normalise_page($page_param);
$search_term  = q1_normalise_search($search_param);

$posts = [
    ['title' => 'Launch notes', 'content' => 'Intro [ad] middle [ad] end'],
    ['title' => 'Editorial workflow', 'content' => 'How editors prepare a story before publishing.'],
    ['title' => 'Mobile performance', 'content' => 'Images, scripts, and layout all affect mobile speed. [ad]'],
    ['title' => 'Search quality', 'content' => 'Useful search pages need clean pagination and empty states.'],
    ['title' => 'Newsletter growth', 'content' => 'Signup forms should avoid duplicate subscribers.'],
    ['title' => 'Admin permissions', 'content' => 'Upload issues often come from roles, quotas, or media settings.'],
    ['title' => 'Release checklist', 'content' => 'Cache, content state, and code deploys all need verification.'],
    ['title' => 'Analytics validation', 'content' => 'Check tracking after changing templates.'],
    ['title' => 'Ad placeholders', 'content' => 'Literal markers such as [ad] become ad containers.'],
    ['title' => 'Security review', 'content' => 'Server-side capability checks matter.'],
    ['title' => 'Second page result', 'content' => 'This appears when testing page two. [ad]'],
];

if ($search_term !== '') {
    $posts = array_values(array_filter(
        $posts,
        static fn (array $post): bool => stripos($post['title'] . ' ' . $post['content'], $search_term) !== false
    ));
}

$per_page    = 10;
$total_pages = max(1, (int) ceil(count($posts) / $per_page));
$page_posts  = array_slice($posts, ($current_page - 1) * $per_page, $per_page);

$verification = [
    'Missing page defaults to page 1' => q1_normalise_page(null) === 1,
    'Missing search defaults to an empty string' => q1_normalise_search(null) === '',
    'Page 2 is accepted for pagination' => q1_normalise_page('2') === 2,
    'No-result searches show the empty state' => $search_term !== 'no-match' || $page_posts === [],
    'Multiple [ad] markers are replaced and numbered' => q1_replace_ad_markers('A [ad] B [ad]') === 'A <div class="ad ad-1" data-ad-position="1"></div> B <div class="ad ad-2" data-ad-position="2"></div>',
];

function demo_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Question 1 Demo</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            line-height: 1.5;
            margin: 2rem auto;
            max-width: 760px;
            padding: 0 1rem;
        }

        form {
            align-items: end;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        label {
            display: grid;
            gap: 0.25rem;
        }

        input,
        button {
            font: inherit;
            padding: 0.5rem 0.65rem;
        }

        article {
            border-top: 1px solid #ddd;
            padding: 1rem 0;
        }

        .ad {
            background: #f2f2f2;
            border: 1px dashed #777;
            color: #444;
            margin: 1rem 0;
            min-height: 1.25rem;
            padding: 0.75rem;
        }

        .checks {
            background: #f7f7f7;
            border: 1px solid #ddd;
            margin: 1.5rem 0;
            padding: 1rem;
        }

        .checks li {
            margin: 0.35rem 0;
        }
    </style>
</head>
<body>
    <h1>
        Page <?php echo demo_escape((string) $current_page); ?>
        of <?php echo demo_escape((string) $total_pages); ?>
        <?php if ($search_term !== '') : ?>
            : Search for <?php echo demo_escape($search_term); ?>
        <?php endif; ?>
    </h1>

    <form method="get">
        <label>
            Search
            <input name="s" value="<?php echo demo_escape($search_term); ?>">
        </label>
        <label>
            Page
            <input name="page" type="number" min="1" value="<?php echo demo_escape((string) $current_page); ?>">
        </label>
        <button type="submit">Test</button>
    </form>

    <section class="checks" aria-labelledby="checks-heading">
        <h2 id="checks-heading">Shared Logic Checks</h2>
        <p>This page calls the same helper functions as <code>solution.php</code>.</p>
        <ul>
            <?php foreach ($verification as $label => $passed) : ?>
                <li><?php echo $passed ? 'PASS' : 'FAIL'; ?>: <?php echo demo_escape($label); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php if ($page_posts === []) : ?>
        <p>No posts found.</p>
    <?php else : ?>
        <?php foreach ($page_posts as $post) : ?>
            <article>
                <h2><?php echo demo_escape($post['title']); ?></h2>
                <div><?php echo q1_replace_ad_markers(demo_escape($post['content'])); ?></div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
