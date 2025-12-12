import React, { useState } from 'react';
import { searchService } from '../../services';
import SkripsiCard from '../../components/common/SkripsiCard';
import { FiSearch, FiFilter } from 'react-icons/fi';
import './SearchPage.css';

const SearchPage = () => {
  const [query, setQuery] = useState('');
  const [topik, setTopik] = useState('');
  const [tahun, setTahun] = useState('');
  const [results, setResults] = useState(null);
  const [loading, setLoading] = useState(false);
  const [showFilters, setShowFilters] = useState(false);

  const handleSearch = async (e) => {
    e.preventDefault();
    
    if (!query.trim()) {
      return;
    }

    setLoading(true);
    try {
      const data = await searchService.search(query, {
        topik: topik || undefined,
        tahun: tahun || undefined,
      });
      setResults(data);
    } catch (error) {
      console.error('Search error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="search-page">
      <div className="search-hero">
        <div className="container">
          <h1 className="hero-title">Pencarian Semantik Judul Skripsi</h1>
          <p className="hero-subtitle">
            Temukan skripsi dengan pencarian berbasis ontologi dan semantic web
          </p>

          <form onSubmit={handleSearch} className="search-form">
            <div className="search-input-group">
              <FiSearch className="search-icon" />
              <input
                type="text"
                className="search-input"
                placeholder="Cari judul skripsi... (contoh: machine learning, web development)"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
              />
              <button type="submit" className="btn btn-primary" disabled={loading}>
                {loading ? 'Searching...' : 'Search'}
              </button>
            </div>

            <button
              type="button"
              className="filter-toggle"
              onClick={() => setShowFilters(!showFilters)}
            >
              <FiFilter /> {showFilters ? 'Hide' : 'Show'} Filters
            </button>

            {showFilters && (
              <div className="filters-panel">
                <div className="filter-group">
                  <label>Topik</label>
                  <input
                    type="text"
                    className="input"
                    placeholder="Filter by topic"
                    value={topik}
                    onChange={(e) => setTopik(e.target.value)}
                  />
                </div>
                <div className="filter-group">
                  <label>Tahun</label>
                  <input
                    type="number"
                    className="input"
                    placeholder="Filter by year"
                    value={tahun}
                    onChange={(e) => setTahun(e.target.value)}
                    min="1900"
                    max={new Date().getFullYear() + 1}
                  />
                </div>
              </div>
            )}
          </form>
        </div>
      </div>

      <div className="container">
        {results && (
          <div className="search-results">
            <div className="results-header">
              <h2>Search Results</h2>
              <div className="results-info">
                <p>
                  Found <strong>{results.total_results}</strong> results for "<strong>{results.keyword}</strong>"
                </p>
                {results.related_topics && results.related_topics.length > 0 && (
                  <div className="related-topics">
                    <strong>Related Topics:</strong>
                    {results.related_topics.map((topic, index) => (
                      <span key={index} className="topic-tag">
                        {topic}
                      </span>
                    ))}
                  </div>
                )}
              </div>
            </div>

            {results.results.length === 0 ? (
              <div className="no-results">
                <p>No results found. Try different keywords or remove filters.</p>
              </div>
            ) : (
              <div className="results-grid">
                {results.results.map((skripsi) => (
                  <SkripsiCard key={skripsi.id} skripsi={skripsi} />
                ))}
              </div>
            )}
          </div>
        )}

        {!results && !loading && (
          <div className="search-placeholder">
            <FiSearch className="placeholder-icon" />
            <h3>Start Your Search</h3>
            <p>Enter keywords to find thesis titles using semantic search</p>
            <div className="search-examples">
              <p>Try searching for:</p>
              <div className="example-tags">
                <span className="example-tag" onClick={() => setQuery('machine learning')}>
                  machine learning
                </span>
                <span className="example-tag" onClick={() => setQuery('web development')}>
                  web development
                </span>
                <span className="example-tag" onClick={() => setQuery('mobile app')}>
                  mobile app
                </span>
              </div>
            </div>
          </div>
        )}

        {loading && (
          <div className="loading-state">
            <div className="spinner"></div>
            <p>Searching with semantic analysis...</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default SearchPage;
