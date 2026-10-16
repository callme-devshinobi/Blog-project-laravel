<x-layout>
  
    <!-- Hero -->
    <section class="page-hero">
      <p class="eyebrow">Get in touch</p>
      <h1>Let's talk.</h1>
      <p>Have a question, a topic suggestion, or a project idea? Drop a message below — I read every email and usually reply within 24 hours.</p>
    </section>
  
    <!-- Contact -->
    <section class="section">
      <div class="contact-grid">
  
        <!-- Info column -->
        <div class="info-stack">
          <div class="info-card">
            <div class="info-icon">✉</div>
            <div>
              <h3>Email</h3>
              <p>For general enquiries, collaborations and feedback.<br>
              <a href="mailto:hello@devnotes.dev">hello@devnotes.dev</a></p>
            </div>
          </div>
          <div class="info-card">
            <div class="info-icon">◈</div>
            <div>
              <h3>Topic suggestions</h3>
              <p>Want a tutorial on something specific? Let me know — the best posts start with reader requests.<br>
              <a href="mailto:ideas@devnotes.dev">ideas@devnotes.dev</a></p>
            </div>
          </div>
          <div class="info-card">
            <div class="info-icon">⚡</div>
            <div>
              <h3>Response time</h3>
              <p>I aim to reply within 24 hours on weekdays. For faster answers, check the FAQ below.</p>
            </div>
          </div>
  
          <div class="social-panel">
            <h3>Find me elsewhere</h3>
            <p>I share snippets, behind-the-scenes and dev tips across these platforms.</p>
            <div class="social-links">
              <a href="#">GitHub</a>
              <a href="#">LinkedIn</a>
              <a href="#">X / Twitter</a>
              <a href="#">YouTube</a>
            </div>
          </div>
        </div>
  
        <!-- Form column -->
        <div class="form-card">
          <h2>Send a message</h2>
          <p>Fields marked <i style="color:#dc2626">*</i> are required.</p>
          <form action="#" method="POST">
            <div class="form-row">
              <div class="field">
                <label for="name">Name <i>*</i></label>
                <input type="text" id="name" name="name" placeholder="Your name" required />
              </div>
              <div class="field">
                <label for="email">Email <i>*</i></label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required />
              </div>
            </div>
            <div class="field">
              <label for="subject">Subject</label>
              <select id="subject" name="subject">
                <option value="general">General enquiry</option>
                <option value="topic">Suggest a topic</option>
                <option value="collab">Collaboration / sponsorship</option>
                <option value="feedback">Feedback on an article</option>
                <option value="bug">Report a code issue</option>
              </select>
            </div>
            <div class="field">
              <label for="message">Message <i>*</i></label>
              <textarea id="message" name="message" placeholder="Hi Dennis, I just read your Laravel tutorial and…" required></textarea>
            </div>
            <div class="form-footer">
              <small>Your details are never shared. Ever.</small>
              <button type="submit" class="btn-send">Send message →</button>
            </div>
          </form>
        </div>
  
      </div>
    </section>
  
    <!-- FAQ -->
    <section class="section">
      <div class="faq">
        <div class="section-title">
          <h2>Frequently asked questions</h2>
          <p>Quick answers before you hit send.</p>
        </div>
        <details class="faq-item">
          <summary>Can I request a tutorial topic?</summary>
          <div class="faq-body">Absolutely — use the "Suggest a topic" subject above. Reader requests shape the DevNotes content calendar, and popular suggestions usually become full series.</div>
        </details>
        <details class="faq-item">
          <summary>Do you accept guest posts or collaborations?</summary>
          <div class="faq-body">Occasionally, yes. If you have a genuinely useful technical article in mind, pitch it via email with an outline. Quality and originality matter more than anything else.</div>
        </details>
        <details class="faq-item">
          <summary>I found a bug in one of your tutorials. What should I do?</summary>
          <div class="faq-body">Please report it! Choose "Report a code issue" in the subject dropdown and include the article link and the code that's failing. Fixes are usually published within a few days.</div>
        </details>
        <details class="faq-item">
          <summary>How can I support DevNotes?</summary>
          <div class="faq-body">Subscribe to the newsletter, share articles that helped you, and leave comments — that's more than enough. 🙌</div>
        </details>
      </div>
    </section>
  
  </x-layout>
  