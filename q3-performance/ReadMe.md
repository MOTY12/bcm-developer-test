# Question 3: Improving a Slow WordPress Article Page

## Answer

I would first use Lighthouse or PageSpeed Insights to check what is making the article page slow, especially on mobile. I would consider these three improvements:

### 1. Optimize images
Large images can make pages slow to load on mobile. I would check image sizes and the page's Largest Contentful Paint (LCP). I would compress images, use modern formats such as WebP, and lazy-load images that are further down the page.

### 2. Reduce unnecessary JavaScript and CSS
Too many scripts and stylesheets from themes and plugins can slow down a page. I would check the browser's Network tab to identify large files and unnecessary requests. I would remove unused assets where safe and defer non-critical scripts where appropriate.

### 3. Improve caching
Slow server responses and database queries can affect page loading. I would measure the Time to First Byte (TTFB) and check whether page caching is working properly. I would consider caching public article pages and optimizing slow queries, while ensuring signed-in users receive the correct content.

## How I Would Check the Improvement

I would start with image optimization. First, I would record the page's loading performance on mobile, then optimize the images and repeat the test under the same conditions.

I would also check that ads still display, analytics events are recorded, and signed-in users can access their content. If the change causes problems or does not improve performance, I would investigate and roll it back if necessary.