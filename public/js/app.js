// Laravel Lab front-end: quizzes, code highlighting, copy buttons, theme toggle.
(function () {
  // Theme toggle (light / dark), remembered in localStorage.
  document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const root = document.documentElement;
      const dark = root.dataset.theme
        ? root.dataset.theme === 'dark'
        : window.matchMedia('(prefers-color-scheme: dark)').matches;
      root.dataset.theme = dark ? 'light' : 'dark';
      try { localStorage.setItem('theme', root.dataset.theme); } catch (e) {}
    });
  });

  // Syntax highlighting (highlight.js from the CDN; plain code if offline).
  const aliases = { blade: 'php-template', dotenv: 'ini', text: 'plaintext', http: 'http' };
  document.querySelectorAll('pre code').forEach((el) => {
    const m = el.className.match(/language-(\S+)/);
    if (m && aliases[m[1]]) el.className = 'language-' + aliases[m[1]];
    if (window.hljs) {
      try { window.hljs.highlightElement(el); } catch (e) {}
    }
  });

  // Copy buttons on code blocks.
  document.querySelectorAll('.prose pre').forEach((pre) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'copy-btn';
    btn.textContent = 'Copy';
    btn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(pre.querySelector('code').innerText);
        btn.textContent = 'Copied!';
      } catch (e) {
        btn.textContent = 'Press ⌘C';
      }
      setTimeout(() => (btn.textContent = 'Copy'), 1500);
    });
    pre.appendChild(btn);
  });

  // Quizzes: instant feedback, one answer per question, "try again" on mistakes.
  document.querySelectorAll('.question').forEach((q) => {
    const answer = Number(q.dataset.answer);
    const why = q.querySelector('.why');
    const options = q.querySelectorAll('.option');
    options.forEach((opt) => {
      opt.addEventListener('click', () => {
        const picked = Number(opt.dataset.index);
        options.forEach((o) => {
          o.disabled = true;
          if (Number(o.dataset.index) === answer) o.classList.add('correct');
        });
        if (picked !== answer) opt.classList.add('wrong');
        why.hidden = false;
        why.textContent = (picked === answer ? '✅ Correct! ' : '❌ Not quite. ') + why.textContent.replace(/^(✅ Correct! |❌ Not quite. )/, '');
        if (picked !== answer) {
          const retry = document.createElement('button');
          retry.type = 'button';
          retry.className = 'link-btn';
          retry.textContent = 'Try again';
          retry.addEventListener('click', () => {
            options.forEach((o) => { o.disabled = false; o.classList.remove('correct', 'wrong'); });
            why.hidden = true;
            retry.remove();
          });
          why.after(retry);
        }
      });
    });
  });
})();
