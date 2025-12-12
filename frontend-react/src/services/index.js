import api from './api';

export const authService = {
  // Login
  async login(email, password, remember = false) {
    const response = await api.post('/login', { email, password, remember });
    return response.data;
  },

  // Logout
  async logout() {
    const response = await api.post('/logout');
    return response.data;
  },

  // Get current user
  async me() {
    const response = await api.get('/me');
    return response.data;
  },

  // Check authentication status
  async check() {
    const response = await api.get('/auth/check');
    return response.data;
  },
};

export const searchService = {
  // Semantic search
  async search(query, filters = {}) {
    const params = new URLSearchParams({
      q: query,
      ...filters,
    });
    const response = await api.get(`/search?${params}`);
    return response.data;
  },

  // Get search suggestions
  async suggestions(query) {
    const response = await api.get(`/search/suggestions?q=${query}`);
    return response.data;
  },
};

export const skripsiService = {
  // Get all skripsi (admin)
  async getAll(filters = {}) {
    const params = new URLSearchParams(filters);
    const response = await api.get(`/admin/skripsi?${params}`);
    return response.data;
  },

  // Get single skripsi
  async getById(id) {
    const response = await api.get(`/skripsi/${id}`);
    return response.data;
  },

  // Upload skripsi
  async upload(formData) {
    const response = await api.post('/skripsi', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Update skripsi
  async update(id, formData) {
    const response = await api.post(`/skripsi/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'X-HTTP-Method-Override': 'PUT',
      },
    });
    return response.data;
  },

  // Delete skripsi
  async delete(id) {
    const response = await api.delete(`/skripsi/${id}`);
    return response.data;
  },

  // Get my uploads
  async myUploads() {
    const response = await api.get('/skripsi/my/uploads');
    return response.data;
  },

  // Download skripsi file
  getDownloadUrl(id) {
    return `/api/skripsi/${id}/download`;
  },
};

export const ontologyService = {
  // Get current ontology
  async getCurrent() {
    const response = await api.get('/ontology/current');
    return response.data;
  },

  // Get all ontologies (admin)
  async getAll() {
    const response = await api.get('/ontology');
    return response.data;
  },

  // Upload ontology
  async upload(formData) {
    const response = await api.post('/ontology/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Set ontology as active
  async setActive(id) {
    const response = await api.post(`/ontology/${id}/activate`);
    return response.data;
  },

  // Delete ontology
  async delete(id) {
    const response = await api.delete(`/ontology/${id}`);
    return response.data;
  },
};

export const userService = {
  // Get all users (admin)
  async getAll() {
    const response = await api.get('/users');
    return response.data;
  },

  // Create user
  async create(userData) {
    const response = await api.post('/users', userData);
    return response.data;
  },

  // Update user
  async update(id, userData) {
    const response = await api.put(`/users/${id}`, userData);
    return response.data;
  },

  // Delete user
  async delete(id) {
    const response = await api.delete(`/users/${id}`);
    return response.data;
  },
};
