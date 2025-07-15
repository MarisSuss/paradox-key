import { Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import LoginPage from './pages/LoginPage';
import HomePage from './pages/HomePage';
import GamePage from './pages/GamePage';
import LoadingSpinner from './components/LoadingSpinner';

function App() {
  const location = useLocation();
  const { user, loading } = useAuth();

  if (loading) {
    return <LoadingSpinner size="large" message="Loading your session..." />;
  }

  return (
    <Routes>
      <Route path="/login" element={user ? <Navigate to="/" replace /> : <LoginPage />} />
      <Route
        path="/"
        element={user ? <HomePage /> : <Navigate to="/login" state={{ from: location }} replace />}
      />
      <Route
        path="/game"
        element={user ? <GamePage /> : <Navigate to="/login" state={{ from: location }} replace />}
      />
    </Routes>
  );
}

export default App;