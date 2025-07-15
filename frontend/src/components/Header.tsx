import { useAuth } from '../context/AuthContext';
import { useNavigate, Link } from 'react-router-dom';
import { useApolloClient, useMutation } from '@apollo/client';
import { LOGOUT_MUTATION } from '../graphql/logout';

export default function Header() {
  const { user, refetch } = useAuth();
  const navigate = useNavigate();
  const client = useApolloClient();
  const [logout] = useMutation(LOGOUT_MUTATION);

  const handleLogout = async () => {
    try {
      // Call the logout mutation to destroy session on server
      await logout();
      
      // Clear Apollo cache
      await client.clearStore();
      
      // Refetch user data (should return null now)
      await refetch();
      
      // Navigate to login
      navigate('/login');
    } catch (error) {
      console.error('Logout error:', error);
      // Force navigation even if logout fails
      await client.clearStore();
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
        <Link to="/" style={{ textDecoration: 'none', color: '#495057' }}>
          <h2 style={{ margin: 0 }}>Paradox Key</h2>
        </Link>
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