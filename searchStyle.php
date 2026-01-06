<div id="myWordContainer">Hello World</div>
<style>
    .highlight-color {
        color: red;
        /* Or any other color */
    }
</style>
<script>
    const targetElement = document;
let originalText = targetElement.textContent;

const letterToHighlight = 'l';
const newColor = 'blue';

const regex = new RegExp(letterToHighlight, 'g');
const highlightedText = originalText.replace(regex, `<span style="background: ${newColor};">${letterToHighlight}</span>`);

targetElement.innerHTML = highlightedText;
</script>