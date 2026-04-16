import { useEffect, useMemo, useState } from 'react'
import './App.css'
import ApiSection from './components/ApiSection'

function App() {
  const apiBaseUrl = useMemo(
    () => import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000/api',
    [],
  )
  const [hello, setHello] = useState('')
  const [items, setItems] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const load = async () => {
      try {
        const [helloRes, itemsRes] = await Promise.all([
          fetch(`${apiBaseUrl}/hello`),
          fetch(`${apiBaseUrl}/items`),
        ])

        if (!helloRes.ok || !itemsRes.ok) {
          throw new Error('API request failed')
        }

        const helloData = await helloRes.json()
        const itemsData = await itemsRes.json()

        setHello(helloData.message ?? '')
        setItems(itemsData.data ?? [])
      } catch (fetchError) {
        setError('Laravel APIに接続できませんでした。')
        console.error(fetchError)
      } finally {
        setLoading(false)
      }
    }

    load()
  }, [apiBaseUrl])

  return (
    <main className="container">
      <h1>React Screen (API経由)</h1>
      <p className="endpoint">API: {apiBaseUrl}</p>

      <ApiSection title="Hello" loading={loading} error={error}>
        <p>{hello}</p>
      </ApiSection>

      <ApiSection title="Items" loading={loading} error={error}>
        <ul>
          {items.map((item) => (
            <li key={item.id}>
              {item.id}: {item.name}
            </li>
          ))}
        </ul>
      </ApiSection>
    </main>
  )
}

export default App
