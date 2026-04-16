export default function ApiSection({ title, loading, error, children }) {
  return (
    <section className="card">
      <h2>{title}</h2>
      {loading && <p>Loading...</p>}
      {error && <p className="error">{error}</p>}
      {!loading && !error && children}
    </section>
  )
}
