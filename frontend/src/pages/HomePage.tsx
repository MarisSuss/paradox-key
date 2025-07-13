import { useAuth } from '../context/AuthContext';
import Header from '../components/Header';

export default function HomePage() {
  const { user } = useAuth();

  return (
    <div>
      <Header />
      <div style={{ padding: '2rem' }}>
        <h1>Welcome, {user?.username || 'friend'}!</h1>
        <div style={{ 
          marginTop: '2rem', 
          padding: '1rem', 
          backgroundColor: '#f5f5f5', 
          borderRadius: '8px' 
        }}>
          <h3>Your Profile:</h3>
          <p><strong>Email:</strong> {user?.email}</p>
          <p><strong>Username:</strong> {user?.username}</p>
          <p><strong>ID:</strong> {user?.id}</p>
        </div>
      </div>
    </div>
  );
}