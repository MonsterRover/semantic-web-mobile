import React, { useState } from 'react';
import { skripsiService } from '../../services';
import { FiUpload, FiFile, FiCheck } from 'react-icons/fi';
import './UploadPage.css';

const UploadPage = () => {
  const [formData, setFormData] = useState({
    judul: '',
    abstrak: '',
    kata_kunci: '',
    topik: '',
    tahun: new Date().getFullYear(),
    penulis: '',
    pembimbing: '',
  });
  const [file, setFile] = useState(null);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState('');

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleFileChange = (e) => {
    const selectedFile = e.target.files[0];
    if (selectedFile) {
      // Validate file type
      const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
      if (!validTypes.includes(selectedFile.type)) {
        setError('File must be PDF or DOCX');
        return;
      }
      // Validate file size (max 10MB)
      if (selectedFile.size > 10 * 1024 * 1024) {
        setError('File size must be less than 10MB');
        return;
      }
      setFile(selectedFile);
      setError('');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!file) {
      setError('Please select a file');
      return;
    }

    setLoading(true);
    setError('');
    setSuccess(false);

    try {
      const data = new FormData();
      Object.keys(formData).forEach(key => {
        data.append(key, formData[key]);
      });
      data.append('file', file);

      await skripsiService.upload(data);
      
      setSuccess(true);
      // Reset form
      setFormData({
        judul: '',
        abstrak: '',
        kata_kunci: '',
        topik: '',
        tahun: new Date().getFullYear(),
        penulis: '',
        pembimbing: '',
      });
      setFile(null);
      
      // Reset file input
      const fileInput = document.getElementById('file');
      if (fileInput) fileInput.value = '';
    } catch (err) {
      setError(err.response?.data?.message || 'Upload failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="upload-page">
      <div className="container">
        <div className="page-header">
          <h1><FiUpload /> Upload Skripsi</h1>
          <p>Upload data skripsi beserta file PDF/DOCX</p>
        </div>

        {success && (
          <div className="success-message">
            <FiCheck /> Skripsi berhasil diupload!
          </div>
        )}

        {error && (
          <div className="error-message">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="upload-form">
          <div className="form-grid">
            <div className="form-group">
              <label htmlFor="judul">Judul Skripsi *</label>
              <input
                type="text"
                id="judul"
                name="judul"
                className="input"
                value={formData.judul}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="penulis">Penulis *</label>
              <input
                type="text"
                id="penulis"
                name="penulis"
                className="input"
                value={formData.penulis}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="pembimbing">Pembimbing</label>
              <input
                type="text"
                id="pembimbing"
                name="pembimbing"
                className="input"
                value={formData.pembimbing}
                onChange={handleChange}
              />
            </div>

            <div className="form-group">
              <label htmlFor="tahun">Tahun *</label>
              <input
                type="number"
                id="tahun"
                name="tahun"
                className="input"
                value={formData.tahun}
                onChange={handleChange}
                min="1900"
                max={new Date().getFullYear() + 1}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="topik">Topik</label>
              <input
                type="text"
                id="topik"
                name="topik"
                className="input"
                placeholder="e.g., Machine Learning, Web Development"
                value={formData.topik}
                onChange={handleChange}
              />
            </div>

            <div className="form-group">
              <label htmlFor="kata_kunci">Kata Kunci</label>
              <input
                type="text"
                id="kata_kunci"
                name="kata_kunci"
                className="input"
                placeholder="Pisahkan dengan koma"
                value={formData.kata_kunci}
                onChange={handleChange}
              />
            </div>
          </div>

          <div className="form-group">
            <label htmlFor="abstrak">Abstrak</label>
            <textarea
              id="abstrak"
              name="abstrak"
              className="input"
              rows="5"
              value={formData.abstrak}
              onChange={handleChange}
            ></textarea>
          </div>

          <div className="form-group">
            <label htmlFor="file">File Skripsi (PDF/DOCX) * (Max 10MB)</label>
            <div className="file-input-wrapper">
              <input
                type="file"
                id="file"
                accept=".pdf,.doc,.docx"
                onChange={handleFileChange}
                required
              />
              {file && (
                <div className="file-info">
                  <FiFile />
                  <span>{file.name}</span>
                  <span className="file-size">
                    ({(file.size / 1024 / 1024).toFixed(2)} MB)
                  </span>
                </div>
              )}
            </div>
          </div>

          <button 
            type="submit" 
            className="btn btn-primary btn-block"
            disabled={loading}
          >
            {loading ? (
              <>
                <span className="spinner"></span> Uploading...
              </>
            ) : (
              <>
                <FiUpload /> Upload Skripsi
              </>
            )}
          </button>
        </form>
      </div>
    </div>
  );
};

export default UploadPage;
