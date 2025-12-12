import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { FiSearch, FiUser, FiLogOut, FiUpload, FiUsers, FiDatabase } from 'react-icons/fi';
import './Navbar.css';

const Navbar = () => {
  const { user, authenticated, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    const result = await logout();
    if (result.success) {
      navigate('/');
    }
  };

  return (
    <nav className="navbar">
      <div className="container navbar-container">
        <Link to="/" className="navbar-brand">
          <FiSearch className="brand-icon" />
          <span>Semantic Web Skripsi</span>
        </Link>

        <div className="navbar-menu">
          {!authenticated ? (
            <>
              <Link to="/" className="nav-link">
                <FiSearch /> Pencarian
              </Link>
              <Link to="/login" className="btn btn-primary">
                Login
              </Link>
            </>
          ) : (
            <>
              <Link to="/" className="nav-link">
                <FiSearch /> Pencarian
              </Link>

              {user?.role === 'kaprodi' && (
                <Link to="/kaprodi/upload" className="nav-link">
                  <FiUpload /> Upload Skripsi
                </Link>
              )}

              {user?.role === 'admin' && (
                <>
                  <Link to="/admin/users" className="nav-link">
                    <FiUsers /> Users
                  </Link>
                  <Link to="/admin/ontology" className="nav-link">
                    <FiDatabase /> Ontology
                  </Link>
                  <Link to="/admin/skripsi" className="nav-link">
                    <FiUpload /> Skripsi
                  </Link>
                </>
              )}

              <div className="user-menu">
                <div className="user-info">
                  <FiUser />
                  <span>{user?.name}</span>
                  <span className="user-role">{user?.role}</span>
                </div>
                <button onClick={handleLogout} className="btn btn-outline">
                  <FiLogOut /> Logout
                </button>
              </div>
            </>
          )}
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
