document.addEventListener('DOMContentLoaded', () => {
    const paragraphContainer = document.querySelector('#paragraphs');
    const addParagraphsButton = document.querySelector('#add-paragraphs');
    const originalParagraphs = Array.from(paragraphContainer.querySelectorAll(':scope > p'));
    let imageParagraphsHidden = false;

    const hasImage = (paragraph) => paragraph.querySelector('img') !== null;

    const shuffle = (items) => {
        const shuffledItems = [...items];

        for (let index = shuffledItems.length - 1; index > 0; index -= 1) {
            const randomIndex = Math.floor(Math.random() * (index + 1));
            [shuffledItems[index], shuffledItems[randomIndex]] = [
                shuffledItems[randomIndex],
                shuffledItems[index],
            ];
        }

        return shuffledItems;
    };

    const updateSeparators = () => {
        paragraphContainer.querySelectorAll(':scope > hr').forEach((separator) => separator.remove());

        const paragraphs = Array.from(paragraphContainer.querySelectorAll(':scope > p'));

        paragraphs.forEach((paragraph, index) => {
            const isEndOfPair = (index + 1) % 2 === 0;
            const anotherParagraphFollows = index < paragraphs.length - 1;

            if (isEndOfPair && anotherParagraphFollows) {
                paragraph.insertAdjacentElement('afterend', document.createElement('hr'));
            }
        });
    };

    const setImageParagraphVisibility = (shouldHide) => {
        imageParagraphsHidden = shouldHide;

        paragraphContainer.querySelectorAll(':scope > p').forEach((paragraph) => {
            if (hasImage(paragraph)) {
                paragraph.hidden = shouldHide;
            }
        });

        toggleImageParagraphsButton.setAttribute('aria-pressed', String(shouldHide));
        toggleImageParagraphsButton.textContent = shouldHide
            ? 'Show image paragraphs'
            : 'Hide image paragraphs';
    };

    const toggleImageParagraphsButton = document.createElement('button');
    toggleImageParagraphsButton.type = 'button';
    toggleImageParagraphsButton.textContent = 'Hide image paragraphs';
    toggleImageParagraphsButton.setAttribute('aria-pressed', 'false');
    addParagraphsButton.insertAdjacentElement('afterend', toggleImageParagraphsButton);

    toggleImageParagraphsButton.addEventListener('click', () => {
        setImageParagraphVisibility(!imageParagraphsHidden);
    });

    addParagraphsButton.addEventListener('click', () => {
        shuffle(originalParagraphs).forEach((paragraph) => {
            const paragraphCopy = paragraph.cloneNode(true);

            if (imageParagraphsHidden && hasImage(paragraphCopy)) {
                paragraphCopy.hidden = true;
            }

            paragraphContainer.append(paragraphCopy);
        });

        updateSeparators();
    });

    updateSeparators();
});
