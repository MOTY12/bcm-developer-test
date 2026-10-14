<?php
/**
 * Question 1 corrected WordPress theme snippet.
 *
 * Assumptions:
 * - Each literal "[ad]" marker should be replaced with one empty ad container.
 * - Containers are numbered per post so the advertising JavaScript can target them.
 * - Normal WordPress post content HTML is trusted only after WordPress' content filters
 *   and kses allow-list have run.
 */

if (! function_exists('q1_normalise_page')) {
    function q1_normalise_page(mixed $value): int
    {
        if (! is_scalar($value)) {
            return 1;
        }

        return max(1, abs((int) $value));
    }
}

if (! function_exists('q1_normalise_search')) {
    function q1_normalise_search(mixed $value): string
    {
        if (! is_scalar($value)) {
            return '';
        }

        return trim(strip_tags((string) $value));
    }
}

if (! function_exists('q1_replace_ad_markers')) {
    function q1_replace_ad_markers(string $content): string
    {
        $ad_index = 0;

        return preg_replace_callback(
            '/\[ad\]/',
            static function () use (&$ad_index): string {
                ++$ad_index;

                return sprintf(
                    '<div class="ad ad-%1$d" data-ad-position="%1$d"></div>',
                    $ad_index
                );
            },
            $content
        );
    }
}

if (defined('Q1_LOAD_FUNCTIONS_ONLY') && Q1_LOAD_FUNCTIONS_ONLY) {
    return;
}


$page_param   = $_GET['page'] ?? 1;
$search_param = $_GET['s'] ?? '';

$current_page = q1_normalise_page(is_scalar($page_param) ? wp_unslash((string) $page_param) : $page_param);
$search_term  = sanitize_text_field(q1_normalise_search(is_scalar($search_param) ? wp_unslash((string) $search_param) : $search_param));

$query = new WP_Query([
    'posts_per_page' => 10,
    'paged'          => $current_page,
    's'              => $search_term,
]);

$total_pages = max(1, (int) $query->max_num_pages);
?>

<h1>
    Page <?php echo esc_html((string) $current_page); ?> of <?php echo esc_html((string) $total_pages); ?>
    <?php if ($search_term !== '') : ?>
        : Search for <?php echo esc_html($search_term); ?>
    <?php endif; ?>
</h1>

<?php if ($query->have_posts()) : ?>
    <?php
    while ($query->have_posts()) :
        $query->the_post();

        $raw_content = q1_replace_ad_markers(get_the_content());
        $content = apply_filters('the_content', $raw_content);
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h2><?php echo esc_html(get_the_title()); ?></h2>
            <?php echo wp_kses_post($content); ?>
        </article>
    <?php endwhile; ?>

    <?php wp_reset_postdata(); ?>
<?php else : ?>
    <p>No posts found.</p>
<?php endif; ?>
