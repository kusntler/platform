# Documentation guidelines
- Don't use "we", prefer "you"
- Try to avoid the word "article" cause we're usually writing "guides"
- Third party resources need a link to the official website (for example Twig.js or Vue.js' event system)
    - Do yourself a favor and just link the first occurrence
- Headlines
    - `h1` headline for the title of the guide including a little intro text giving the reader an entrance to the guide. Make sure you're using exactly one `h1` headline in your document.
    - `h2` headlines for each section in your guide including an intro text for the section
    - `h3` headlines are fine, they don't need an intro text. Use them to structure your section
    - Avoid `h4-h6` headlines. If you're needing them, consider creating a dedicated guide for the topic you want to cover
- Code snippets
    - Inside the code fences add a source code comment with the file name and type, so the reader can follow along and get a better understand where you are right now
    - Under the code snippet add a figure which describes what the reader looks at e.g. *Creating a component*
- File- and folder structure
    - Use a code snippet block and use the output of the command "[tree](https://linux.die.net/man/1/tree)" to display a filesystem structure
- Quotes should always use a "blockquote"-tag including a link to the source of the quote.
- Troubleshooting guide
    - Dedicated guide with common error messages and how to fix them. 