import { useAuth } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import { useApolloClient } from '@apollo/client';

export default function Header() {
  const { user, refetch } = useAuth();
  const navigate = useNavigate();
  const client = useApolloClient();

  const handleLogout = async () => {
    try {
      // Clear the session by making a request to a logout endpoint
      // For now, we'll just clear the Apollo cache and refetch
      await client.clearStore();
      await refetch();
      navigate('/login');
    } catch (error) {
      console.error('Logout error:', error);
      // Force navigation even if logout fails
      navigate('/login');
    }
  };

  return (
    <header style={{ 
      padding: '1rem 2rem', 
      display: 'flex', 
      justifyContent: 'space-between', 
      alignItems: 'center',
      backgroundColor: '#f8f9fa',
      borderBottom: '1px solid #dee2e6'
    }}>
      <div>
        <h2 style={{ margin: 0, color: '#495057' }}>Paradox Key</h2>
      </div>
      <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
        <span style={{ color: '#6c757d' }}>
          Welcome, {user?.username}!
        </span>
        <button 
          onClick={handleLogout}
          style={{
            padding: '0.5rem 1rem',
            backgroundColor: '#dc3545',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
          }}
        >
          Logout
        </button>
      </div>
    </header>
  );
}