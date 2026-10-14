# Questions 5 and 6

## Question 5: HTML corrections

Actual HTML5 validity errors in the supplied code:

- The `html` element is closed before the `body`, so `head` and `body` are not in the required document structure.
- `with=300` is not the image `width` attribute.
- `height=auto` is invalid because the `height` attribute must be a positive integer when present.
- The closing tags for `code` and `b` overlap. Elements must close in reverse nesting order.

Accessibility and style improvements, rather than validity errors:

- Added `lang="en"` so assistive technology can use the correct language rules.
- Added meaningful alternative text to the local image.
- Used `title` on `abbr` to expose the expansion of `lbrt`; `name` was not an appropriate attribute for that purpose.
- Replaced the vague button label with `Add paragraphs`.
- Used `main` and `article` to expose the page's primary content structure.

HTML-only discoverability and interpretation improvements:

- A specific `title` describes the document in browser tabs and search results.
- A `meta name="description"` gives search engines a concise page summary.
- Semantic `main`, `article`, and a single `h1` describe the main topic and content boundary.
- `lang="en"` gives browsers and search tools the document language.

## Question 6: JavaScript behaviour

`app.js` preserves the three initial paragraphs as clone sources. Clicking `Add paragraphs` adds one clone of each paragraph in a shuffled order. Before separators are rebuilt, every existing `hr` is removed. The script then counts all direct paragraph children, including hidden ones, and inserts an `hr` after each completed pair only when another paragraph follows.

The second button is created by JavaScript. It toggles the `hidden` state of every paragraph containing an image. The script keeps that state in `imageParagraphsHidden`, so new image-containing copies remain hidden while image paragraphs are hidden.

## Validation

I ran the following from the repository root:

```bash
tidy -qe q5-q6-html-javascript/index.html
```

Result: exit code `0` with no output, meaning no HTML errors were reported. I also ran `node --check q5-q6-html-javascript/app.js`, which exited with code `0`, and confirmed that `test.jpeg` is a local JPEG file.

`tidy` is an HTML validator supplied by many local development environments; if it is unavailable, use the W3C Nu HTML Checker against `index.html`.

## Manual checks

1. Open `q5-q6-html-javascript/index.html` in a browser.
2. On load, confirm the order is paragraph 1, paragraph 2, separator, paragraph 3.
3. Click `Add paragraphs` twice. There should be 9 paragraphs and separators after paragraphs 2, 4, 6, and 8.
4. Click `Hide image paragraphs`, then click `Add paragraphs`. Both existing and new paragraphs containing the image remain hidden.
5. Click `Show image paragraphs` and confirm all image paragraphs return.

The DOM order is always the reading and keyboard order. No CSS or JavaScript visual reordering is used.

The interactive manual checks are documented but not marked as completed here because the local browser was unavailable while the computer was locked.
