<script src="https://unpkg.com/react@18.3.1/umd/react.development.js" integrity="sha384-hD6/rw4ppMLGNu3tX5cjIb+uRZ7UkRJ6BPkLpg4hAu/6onKUg4lLsHAs9EBPT82L" crossorigin="anonymous"></script>
<script src="https://unpkg.com/react-dom@18.3.1/umd/react-dom.development.js" integrity="sha384-u6aeetuaXnQ38mYT8rp6sbXaQe3NL9t+IBXmnYxwkUI2Hw4bsp2Wvmx4yRQF1uAm" crossorigin="anonymous"></script>
<script src="https://unpkg.com/@babel/standalone@7.29.0/babel.min.js" integrity="sha384-m08KidiNqLdpJqLq95G/LEi8Qvjl/xUYll3QILypMoQ65QorJ9Lvtp2RXYGBFj1y" crossorigin="anonymous"></script>
</head>
<body>
<div id="root"></div>

<script type="text/babel" src="cinevo-data.jsx"></script>
<script type="text/babel" src="cinevo-shared.jsx"></script>

<script type="text/babel">
const { useState, useRef, useEffect } = React;
const MIN_CHARS = 20;

function Write() {
  const [film, setFilm] = useState(null);
  const [query, setQuery] = useState("");
  const [showResults, setShowResults] = useState(false);
  const [title, setTitle] = useState("");
  const [body, setBody] = useState("");
  const [savedAt, setSavedAt] = useState(null);
  const [published, setPublished] = useState(false);
  const bodyRef = useRef(null);

  // Auto-grow textarea
  useEffect(() => {
    if (bodyRef.current) {
      bodyRef.current.style.height = "auto";
      bodyRef.current.style.height = Math.max(320, bodyRef.current.scrollHeight) + "px";
    }
  }, [body]);

  // Auto-save
  useEffect(() => {
    if (!film || (!title && !body)) return;
    const t = setTimeout(() => setSavedAt(new Date()), 800);
    return () => clearTimeout(t);
  }, [title, body, film]);

  const results = query
    ? CV_DATA.FILMS.filter((f) =>
        f.title.toLowerCase().includes(query.toLowerCase()) ||
        f.director.toLowerCase().includes(query.toLowerCase())
      ).slice(0, 5)
    : [];

  const canPublish = film && body.trim().length >= MIN_CHARS;

  if (published) {
    return (
      <>
        <Header user={{ name: "Mélanie" }} onNavigate={() => {}} />
        <main className="published">
          <div className="check">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round">
              <path d="m5 12 5 5L20 7" />
            </svg>
          </div>
          <h2>Avis publié.</h2>
          <p>Votre avis sur <em>{film.title}</em> est en ligne. Pas de notification, pas de fanfare — juste un avis de plus, lu par qui voudra le lire.</p>
          <div style={{ display: "flex", gap: 12, justifyContent: "center" }}>
            <button className="btn btn-secondary" onClick={() => { setPublished(false); setTitle(""); setBody(""); setFilm(null); setQuery(""); }}>
              Écrire un autre avis
            </button>
            <a className="btn btn-primary" href="#">Voir mon avis</a>
          </div>
        </main>
        <Footer />
      </>
    );
  }

  return (
    <>
      <a href="#main" className="skip-link">Aller au contenu</a>
      <Header user={{ name: "Mélanie" }} route="write" onNavigate={() => {}} />
      <main id="main" className="write-shell">
        <div className="write-kicker">Écrire un avis</div>
        <h1 className="write-h1">Qu'avez-vous vu, <em>et qu'en avez-vous pensé ?</em></h1>
        <p className="write-help">
          Trois lignes suffisent. Pas de longueur minimale réelle. Pas de note à donner.
          Écrivez comme à un ami qui ne l'a pas encore vu.
        </p>

        {/* Step 1: Pick a film */}
        <div className="film-field">
          <label className="field-label" htmlFor="film-q">Le film</label>
          {film ? (
            <div className="selected-film">
              <div className="sf-mini"><Poster film={film} /></div>
              <div className="sf-info">
                <div className="sf-title">{film.title}</div>
                <div className="sf-meta">{film.year} · {film.director}</div>
              </div>
              <button className="sf-change" onClick={() => { setFilm(null); setQuery(""); }}>Changer</button>
            </div>
          ) : (
            <div className="film-search">
              <input
                id="film-q"
                type="text"
                value={query}
                onChange={(e) => { setQuery(e.target.value); setShowResults(true); }}
                onFocus={() => setShowResults(true)}
                onBlur={() => setTimeout(() => setShowResults(false), 150)}
                placeholder="Chercher un film par titre ou réalisateur…"
                autoFocus
              />
              {showResults && results.length > 0 && (
                <div className="film-results" role="listbox">
                  {results.map((f) => (
                    <div
                      key={f.id}
                      className="film-result"
                      role="option"
                      onMouseDown={() => { setFilm(f); setQuery(""); setShowResults(false); }}
                    >
                      <div className="mini"><Poster film={f} /></div>
                      <div className="info">
                        <div className="ft">{f.title}</div>
                        <div className="fm">{f.year} · {f.director}</div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
              {showResults && query && results.length === 0 && (
                <div className="film-results">
                  <div style={{ padding: 16, fontFamily: "var(--serif)", fontStyle: "italic", color: "var(--ink-2)", fontSize: 14 }}>
                    Aucun film ne correspond à « {query} ».
                  </div>
                </div>
              )}
            </div>
          )}
        </div>

        {/* Step 2: Title (optional) */}
        {film && (
          <>
            <label className="field-label" htmlFor="t-input">Titre de l'avis <span style={{ color: "var(--ink-3)", fontWeight: 400, textTransform: "none", letterSpacing: 0, fontStyle: "italic", fontFamily: "var(--serif)" }}> · facultatif</span></label>
            <input
              id="t-input"
              className="title-input"
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="Une phrase pour résumer, ou rien."
              maxLength={120}
            />

            {/* Step 3: Body */}
            <label className="field-label" htmlFor="b-input">Votre avis</label>
            <textarea
              id="b-input"
              ref={bodyRef}
              className="body-input"
              value={body}
              onChange={(e) => setBody(e.target.value)}
              placeholder="Ce qui vous a marqué. Ce qui vous a déplu. Ce que vous voulez retenir. Vos mots feront l'affaire."
            />
          </>
        )}

        {/* Sticky action bar */}
        <div className="write-bar">
          <div className="status">
            {!film && <span>Choisissez d'abord un film.</span>}
            {film && body.length === 0 && <span>Commencez à écrire, votre brouillon s'enregistre tout seul.</span>}
            {film && body.length > 0 && body.length < MIN_CHARS && (
              <span style={{ color: "var(--accent)" }}>
                Encore {MIN_CHARS - body.length} caractère{MIN_CHARS - body.length > 1 ? "s" : ""} avant de pouvoir publier.
              </span>
            )}
            {film && body.length >= MIN_CHARS && savedAt && (
              <>
                <span className="dot"></span>
                <span>Brouillon enregistré · {savedAt.toLocaleTimeString("fr-FR", { hour: "2-digit", minute: "2-digit" })}</span>
              </>
            )}
          </div>
          <div className="actions">
            <button className="btn btn-secondary btn-sm" disabled={!film}>Enregistrer</button>
            <button className="btn btn-primary btn-sm" disabled={!canPublish} onClick={() => setPublished(true)}>
              Publier l'avis
            </button>
          </div>
        </div>
      </main>
      <Footer />
    </>
  );
}

ReactDOM.createRoot(document.getElementById("root")).render(<Write />);
</script>
</body>