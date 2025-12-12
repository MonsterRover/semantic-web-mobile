import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import Navbar from './components/layout/Navbar';

// Pages
import LoginPage from './pages/auth/LoginPage';
import SearchPage from './pages/mahasiswa/SearchPage';
import UploadPage from './pages/kaprodi/UploadPage';
import UsersPage from './pages/admin/UsersPage';

import './styles/theme.css';

// Protected Route Component
const ProtectedRoute = ({ children, allowedRoles }) => {
  const { authenticated, loading, user } = useAuth();

  if (loading) {
    return (
      <div className="loading-state" style={{ minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <div className="spinner"></div>
      </div>
    );
  }

  if (!authenticated) {
    return <Navigate to="/login" replace />;
  }

  if (allowedRoles && !allowedRoles.includes(user?.role)) {
    return <Navigate to="/" replace />;
  }

  return children;
};

// App Routes Component
const AppRoutes = () => {
  return (
    <Routes>
      {/* Public Routes */}
      <Route path="/" element={<><Navbar /><SearchPage /></>} />
      <Route path="/login" element={<LoginPage />} />

      {/* Kaprodi Routes */}
      <Route
        path="/kaprodi/upload"
        element={
          <ProtectedRoute allowedRoles={['kaprodi', 'admin']}>
            <Navbar />
            <UploadPage />
          </ProtectedRoute>
        }
      />

      {/* Admin Routes */}
      <Route
        path="/admin/users"
        element={
          <ProtectedRoute allowedRoles={['admin']}>
            <Navbar />
            <UsersPage />
          </ProtectedRoute>
        }
      />
      <Route
        path="/admin/ontology"
        element={
          <ProtectedRoute allowedRoles={['admin']}>
            <Navbar />
            <div className="container" style={{ padding: '2rem' }}>
              <h1>Ontology Management</h1>
              <p>Upload and manage ontology files here.</p>
            </div>
          </ProtectedRoute>
        }
      />
      <Route
        path="/admin/skripsi"
        element={
          <ProtectedRoute allowedRoles={['admin']}>
            <Navbar />
            <div className="container" style={{ padding: '2rem' }}>
              <h1>Skripsi Management</h1>
              <p>Manage all skripsi data here.</p>
            </div>
          </ProtectedRoute>
        }
      />

      {/* Fallback */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
};

// Main App Component
function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <AppRoutes />
      </AuthProvider>
    </BrowserRouter>
  );
}

export default App;
