(function () {
  const assetPath = 'assets/chatbot.png';
  const apiUrl = 'chatbot_ollama.php';
  const quickReplies = [
    { label: 'How do I order?', template: 'How do I place a print order?' },
    { label: 'Pricing', template: 'How much do printing and lamination cost?' },
    { label: 'Operating hours', template: 'What are your shop hours?' },
    { label: 'Where are you?', template: 'Where is the shop located?' },
    { label: 'Talk to staff', template: 'How can I reach a real person?' }
  ];

  function init() {
    if (document.getElementById('iskobot-launcher')) return;

    injectStyles();
    const launcher = createLauncher();
    const panel = createPanel();

    document.body.appendChild(launcher);
    document.body.appendChild(panel);

    wireUp(panel, launcher);
    greet(panel);
  }

  function injectStyles() {
    const style = document.createElement('style');
    style.id = 'iskobot-styles';
    style.textContent = `
      .iskobot-launcher {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1200;
        border: none;
        border-radius: 999px;
        background: linear-gradient(135deg, #8a1111, #5d0a0a);
        color: #fff;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        box-shadow: 0 20px 45px rgba(0,0,0,0.25);
        cursor: pointer;
        font-family: inherit;
      }
      .iskobot-launcher img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        background: #fff;
        padding: 4px;
      }
      .iskobot-panel {
        position: fixed;
        bottom: 100px;
        right: 24px;
        width: 320px;
        max-width: calc(100vw - 32px);
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.25);
        z-index: 1199;
        display: flex;
        flex-direction: column;
        transform: translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s ease, transform .25s ease;
        overflow: hidden;
      }
      .iskobot-panel.active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
      }
      .iskobot-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: linear-gradient(135deg, #750d0d, #a31212);
        color: #fff;
      }
      .iskobot-agent {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .iskobot-agent img {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #fff;
        padding: 4px;
        object-fit: cover;
      }
      .iskobot-close {
        background: rgba(255,255,255,0.15);
        border: none;
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
      }
      .iskobot-messages {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 280px;
        overflow-y: auto;
      }
      .iskobot-typing {
        padding: 0 16px 12px;
        font-size: 12px;
        color: #8a8a8a;
        display: none;
      }
      .iskobot-typing.active {
        display: block;
      }
      .iskobot-message {
        padding: 10px 14px;
        border-radius: 14px;
        font-size: 14px;
        line-height: 1.4;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        animation: iskobot-pop .2s ease;
      }
      .iskobot-message.bot {
        background: #fff5f5;
        color: #5d0a0a;
        align-self: flex-start;
      }
      .iskobot-message.user {
        background: #750d0d;
        color: #fff;
        align-self: flex-end;
      }
      .iskobot-quick {
        padding: 0 16px 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
      }
      .iskobot-quick button {
        border: 1px solid rgba(117,13,13,0.2);
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        background: #fff;
        color: #5d0a0a;
        cursor: pointer;
      }
      .iskobot-form {
        display: flex;
        gap: 8px;
        padding: 16px;
        border-top: 1px solid #f0f0f0;
      }
      .iskobot-form input {
        flex: 1;
        border-radius: 999px;
        border: 1px solid #ddd;
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
      }
      .iskobot-form button {
        border: none;
        border-radius: 999px;
        background: #750d0d;
        color: #fff;
        padding: 10px 18px;
        font-weight: 600;
        cursor: pointer;
      }
      @media (max-width: 600px) {
        .iskobot-launcher {
          bottom: 16px;
          right: 16px;
        }
        .iskobot-panel {
          bottom: 88px;
          right: 16px;
          width: calc(100vw - 32px);
        }
      }
      @keyframes iskobot-pop {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
      }
    `;
    document.head.appendChild(style);
  }

  function createLauncher() {
    const button = document.createElement('button');
    button.id = 'iskobot-launcher';
    button.type = 'button';
    button.className = 'iskobot-launcher';
    button.innerHTML = `<img src="${assetPath}" alt="Chatbot avatar" /><span>Need help?</span>`;
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-controls', 'iskobot-panel');
    return button;
  }

  function createPanel() {
    const panel = document.createElement('section');
    panel.id = 'iskobot-panel';
    panel.className = 'iskobot-panel';
    panel.setAttribute('aria-live', 'polite');
    panel.innerHTML = `
      <header class="iskobot-header">
        <div class="iskobot-agent">
          <img src="${assetPath}" alt="IskoBot avatar" />
          <div>
            <strong>IskoBot</strong><br>
            <small>Online now</small>
          </div>
        </div>
        <button class="iskobot-close" type="button" aria-label="Close chat">&times;</button>
      </header>
      <div class="iskobot-messages" id="iskobot-messages" role="log"></div>
      <div class="iskobot-typing" id="iskobot-typing" aria-live="polite">IskoBot is thinking…</div>
      <div class="iskobot-quick" role="group" aria-label="Quick replies"></div>
      <form class="iskobot-form" id="iskobot-form" autocomplete="off">
        <input type="text" id="iskobot-input" name="message" placeholder="Ask something…" required />
        <button type="submit">Send</button>
      </form>
    `;
    return panel;
  }

  function wireUp(panel, launcher) {
    const closeBtn = panel.querySelector('.iskobot-close');
    const form = panel.querySelector('#iskobot-form');
    const input = panel.querySelector('#iskobot-input');
    const messages = panel.querySelector('#iskobot-messages');
    const quickContainer = panel.querySelector('.iskobot-quick');
    const typingIndicator = panel.querySelector('#iskobot-typing');

    launcher.addEventListener('click', () => togglePanel(panel, launcher));
    closeBtn.addEventListener('click', () => togglePanel(panel, launcher, false));

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const text = input.value.trim();
      if (!text) return;
      appendMessage(messages, 'user', text);
      input.value = '';
      respond(messages, typingIndicator, text);
    });

    quickReplies.forEach(({ label, template }) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = label;
      btn.addEventListener('click', () => {
        if (!template) return;
        input.value = template;
        input.focus();
      });
      quickContainer.appendChild(btn);
    });
  }

  function togglePanel(panel, launcher, forceState) {
    const shouldOpen = typeof forceState === 'boolean' ? forceState : !panel.classList.contains('active');
    panel.classList.toggle('active', shouldOpen);
    launcher.setAttribute('aria-expanded', String(shouldOpen));
  }

  function greet(panel) {
    const messages = panel.querySelector('#iskobot-messages');
    appendMessage(
      messages,
      'bot',
      'Hi! I’m IskoBot. Ask me anything about printing, binding, lamination, photos, photocopy, or tarpaulin services.',
      { allowRichText: true }
    );
  }

  function appendMessage(container, author, text, options = {}) {
    const bubble = document.createElement('div');
    bubble.className = `iskobot-message ${author}`;
    if (options.allowRichText) {
      bubble.innerHTML = formatBotMessage(text);
    } else {
      bubble.textContent = text;
    }
    container.appendChild(bubble);
    container.scrollTop = container.scrollHeight;
    return bubble;
  }

  function formatBotMessage(text) {
    const safe = (text || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
    const paragraphs = safe
      .split(/\n\s*\n/)
      .map(part => part.trim().replace(/\n/g, '<br>'))
      .filter(Boolean);
    if (!paragraphs.length) return '<p>…</p>';
    return paragraphs.map(part => `<p>${part}</p>`).join('');
  }

  async function respond(container, typingIndicator, userText) {
    const placeholder = appendMessage(container, 'bot', '…');
    setTyping(typingIndicator, true);
    try {
      const context = window.__iskobotContext || '';
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userText, context })
      });

      if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}`);
      }

      const data = await response.json();
      const reply = data.reply || 'Walang sagot si IskoBot ngayon. Please message support@iskoprint.com.';
      placeholder.innerHTML = formatBotMessage(reply);
    } catch (error) {
      console.error('IskoBot error:', error);
      placeholder.textContent = 'May problema sa pag-connect kay Ollama. Siguraduhing running ang AI server at subukan muli.';
    } finally {
      setTyping(typingIndicator, false);
    }
  }

  function setTyping(indicator, isActive) {
    if (!indicator) return;
    indicator.classList.toggle('active', isActive);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

