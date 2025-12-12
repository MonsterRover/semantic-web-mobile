import React from 'react';
import './SkripsiCard.css';
import { FiDownload, FiCalendar, FiUser, FiTag } from 'react-icons/fi';

const SkripsiCard = ({ skripsi, onDownload }) => {
  const { id, judul, abstrak, penulis, tahun, topik, kata_kunci, matched_topics, is_semantic_match } = skripsi;

  return (
    <div className={`skripsi-card ${is_semantic_match ? 'semantic-match' : ''}`}>
      {is_semantic_match && (
        <div className="semantic-badge">
          <span>Semantic Match</span>
        </div>
      )}

      <h3 className="skripsi-title">{judul}</h3>
      
      <div className="skripsi-meta">
        <div className="meta-item">
          <FiUser />
          <span>{penulis}</span>
        </div>
        <div className="meta-item">
          <FiCalendar />
          <span>{tahun}</span>
        </div>
        {topik && (
          <div className="meta-item">
            <FiTag />
            <span>{topik}</span>
          </div>
        )}
      </div>

      {abstrak && (
        <p className="skripsi-abstrak">
          {abstrak.length > 200 ? `${abstrak.substring(0, 200)}...` : abstrak}
        </p>
      )}

      {kata_kunci && (
        <div className="skripsi-keywords">
          {kata_kunci.split(',').map((keyword, index) => (
            <span key={index} className="keyword-tag">
              {keyword.trim()}
            </span>
          ))}
        </div>
      )}

      {matched_topics && matched_topics.length > 0 && (
        <div className="matched-topics">
          <strong>Matched Topics:</strong>
          {matched_topics.map((topic, index) => (
            <span key={index} className="matched-tag">
              {topic}
            </span>
          ))}
        </div>
      )}

      <div className="skripsi-actions">
        <a 
          href={`/api/skripsi/${id}/download`} 
          className="btn btn-primary"
          target="_blank"
          rel="noopener noreferrer"
        >
          <FiDownload /> Download
        </a>
      </div>
    </div>
  );
};

export default SkripsiCard;
